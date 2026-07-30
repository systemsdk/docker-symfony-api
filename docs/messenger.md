# Symfony Messenger & Asynchronous Processing
This document outlines the architectural guidelines and operational procedures for asynchronous task execution using the [Symfony Messenger](https://symfony.com/doc/current/messenger.html) component and RabbitMQ.

## Architectural Role
Symfony Messenger is the backbone of our asynchronous processing and event-driven architecture. It provides message buses to decouple application layers and route background tasks (e.g., sending emails, external API calls, processing heavy reports) to message queues.

Before implementing new asynchronous logic, ensure you are familiar with the core [Messenger component concepts](https://symfony.com/doc/current/components/messenger.html).

## Message Broker (RabbitMQ)
This environment utilizes [RabbitMQ](https://hub.docker.com/_/rabbitmq) as the primary message broker. It implements the Advanced Message Queuing Protocol (AMQP) to guarantee reliable message delivery and failover capabilities.

### Management UI (Admin Panel)
For manual management and monitoring, use the full-featured web interface. It allows you to view all existing queues, exchanges and bindings, as well as monitor message flow in real-time.

* URL: [http://localhost:15672](http://localhost:15672)
* Default Credentials: `guest` / `guest` (These can be modified in your `.env` configuration file).

### HTTP API & Documentation
RabbitMQ exposes a REST HTTP API for programmatic access to broker metrics and management.

* Local API Reference: When the container is running, the API documentation and help are accessible directly at [http://localhost:15672/api/](http://localhost:15672/api/).
* External API Reference: You can also refer to the [official RabbitMQ Management HTTP API reference](https://rawcdn.githack.com/rabbitmq/rabbitmq-server/v3.11.5/deps/rabbitmq_management/priv/www/api/index.html).

## Messages and Handlers
In accordance with our Domain-Driven Design (DDD) principles, asynchronous communication must strictly follow these rules:

* Messages (Domain Layer): Messages are exclusively used for asynchronous operations. They must be strictly typed, immutable and as small as possible. A smaller message payload ensures faster broker routing and maximizes processing speed. Never serialize full Doctrine Entities into a message. Always pass scalar values or Entity IDs (UUIDs). This keeps the payload lightweight and prevents data staleness or `EntityManager` detachment issues.
    * Example: `App\Tool\Domain\Message\TestMessage`
* Handlers (Transport Layer): They act as adapters between the message broker and our core application. Their sole responsibility is to receive the asynchronous message, unpack the payload (e.g., extract the UUID) and delegate the execution to the corresponding Application Service. A Handler must never contain business logic or manipulate Domain entities directly.
    * Example: `App\Tool\Transport\MessageHandler\TestHandler`

For basic implementation details, refer to the official documentation:
* [Creating a Message](https://symfony.com/doc/current/messenger.html#creating-a-message-handler)
* [Creating a Message Handler](https://symfony.com/doc/current/messenger.html#creating-a-message-handler)

### Namespace Structure
Because we follow a strictly modular Domain-Driven Design (DDD) approach, we do not use Symfony's standard global `App\Message\...` or `App\MessageHandler\...` directories.

Instead, messages and handlers reside within their specific modules and layers:
* Messages: `App\<Module>\Domain\Message\...`
* Handlers: `App\<Module>\Transport\MessageHandler\...`

You can find working examples inside the `Tool` module: `App\Tool\Domain\Message\...` and `App\Tool\Transport\MessageHandler\...`.

### Internal Messages
When you need to send an asynchronous message to process an internal task, instantiate your message class and pass it to our infrastructure service: `App\General\Infrastructure\Service\MessageService::sendMessage()`

**Routing & Priorities:**
Routing is determined by marker interfaces (mapped in `messenger.yaml`).
* For high-priority tasks (queue `messages_high`), implement: `App\General\Domain\Message\Interfaces\MessageHighInterface`.
    * Example: `App\Tool\Domain\Message\TestMessage.php`
* For low-priority tasks (queue `messages_low`), implement: `App\General\Domain\Message\Interfaces\MessageLowInterface`.

> **⚡** Supervisor Note: Please pay attention to the transport processing order inside the supervisord configuration file (`docker/general/supervisord.conf`, under `program:messenger-consume`). The system is configured to process `async_priority_high` first, followed by `async_priority_low`.

**Internal Message Handler:**

To process the internal message, create a handler within the Transport layer.
* Example: `App\Tool\Transport\MessageHandler\TestHandler.php`

When extending or creating new handlers, never forget our strict DDD layer execution flow (see `development.md`).

### External Messages & Integration
Sometimes the application needs to consume messages originating from external applications. For example, we might receive a raw JSON payload:
```json
{
    "service": "some-service",
    "action": "entity-edited",
    "id": "3"
}
```

Our application routes these external payloads through a dedicated RabbitMQ queue named `messages_external` (see the `messenger.yaml` configuration).

**External Message Processing**

Because external messages arrive as raw text/JSON, they require a two-step process in the Transport layer:

* Serializer: A custom serializer decodes the raw payload into a strictly typed Application Message object.
    * Example: `App\Tool\Transport\Serializer\ExternalMessageSerializer.php`
* Handler: The handler receives the deserialized object and delegates the work.
    * Example: `App\Tool\Transport\MessageHandler\TestHandler.php`

When extending or creating new handlers, never forget our strict DDD layer execution flow (see `development.md`).

## Transports Initialization
Before dispatching or consuming messages, you must initialize the configured transports. This process creates the required routing infrastructure in RabbitMQ and sets up the database table for failed messages (Dead Letter Queue).

To set up the transports, execute the following command:
```bash
make messenger-setup-transports
```

Note: After executing this command, all necessary RabbitMQ queues, exchanges and bindings will be created, along with the MySQL table for failed messages.

## Consuming Messages
Messages dispatched to asynchronous transports are not executed immediately. They are queued in RabbitMQ and processed by background worker processes.

Currently, workers are managed automatically by Supervisor across all environments (including local setups for `dev`, `test`, `staging` and `prod`). If you notice that messages are sitting in the queue and not being consumed, verify the worker status by executing the following command in your local shell:
```bash
make logs-supervisord
```

> 🏗️ DevOps Note for Production:
> While bundling Supervisor inside the application container is a pragmatic and necessary solution for all our local environments (including local `staging` and `production` setups), any actual remote deployments to live servers should be refactored by the DevOps team. Remote infrastructure must follow the strict containerization best practice of "one process per container" (e.g., separating PHP-FPM and Messenger workers into completely distinct containers/pods).

## Retries & Failures
If an exception is thrown while a handler is processing a message, the message is automatically returned to the transport to be retried. By default, our configuration attempts 3 retries with an increasing delay before routing the message to the failure transport.

This environment doesn't have a default failure retry strategy and failed messages will not be deleted after the 3rd retry by default.

We utilize a custom retry strategy to manage this behavior, located in the Infrastructure layer:
`App\General\Infrastructure\Messenger\Strategy\FailedRetry.php`

### Configuration (.env)
You can customize the manual retry behavior and retention policy using the following environment variables:

```dotenv
# Allow unlimited manual retries for "failed" messages (bin/console messenger:failed:retry).
# 1 = Unlimited manual retries allowed. 0 = Only 1 manual retry allowed.
MESSENGER_FAILED_IS_RETRYABLE=1

# Time in milliseconds to wait before a failed message can be manually retried again.
# (Only applicable if MESSENGER_FAILED_IS_RETRYABLE=1)
MESSENGER_FAILED_RETRY_WAITING_TIME=10000

# Number of days to keep failed messages in the messenger_messages database table before deletion.
MESSENGER_MESSAGES_HISTORY_DAYS=7
```

### The Dead Letter Queue (DLQ) & Cleanup
After the initial automated retries fail, messages are permanently moved to the `messenger_messages` MySQL table.

* Manual Retry: You can investigate and retry these messages manually unlimited times via the console:
```bash
bin/console messenger:failed:retry
```

Note: A scheduled cron job (`bin/console messenger:messages-cleanup`) automatically purges messages older than the configured `MESSENGER_MESSAGES_HISTORY_DAYS`. By default, you have a 7-day window to resolve and retry failed messages before they are permanently deleted.

For deeper insights into the retry mechanisms, refer to the [official Symfony documentation](https://symfony.com/doc/current/messenger.html#retries-failures).
