# Messenger
This document describing how you can use [symfony/messenger](https://symfony.com/doc/current/messenger.html) bundle.

## Basics

### Description
Symfony's Messenger is much powerful bundle, that supports by the symfony core team, and provides a message bus and some routing capabilities to send messages within your application and through transports such as message queues. Before using it, read the [Messenger component](https://symfony.com/doc/current/components/messenger.html) documentation to get familiar with its concepts.

### RabbitMQ
This environment is using [RabbitMQ](https://hub.docker.com/_/rabbitmq) message broker software. RabbitMQ is open source message broker software (sometimes called message-oriented middleware) that implements the Advanced Message Queuing Protocol (AMQP). The RabbitMQ server is written in the Erlang programming language and is built on the Open Telecom Platform framework for clustering and failover. Client libraries to interface with the broker are available for all major programming languages.

### Admin panel
You can use your browser in order to manage/view messages. Just open next url in your browser: [http://localhost:15672](http://localhost:15672). Default login - `guest` and password - `guest` (you are able to change it inside `.env` configuration file).

### Consuming Messages
Once your messages have been routed, it will be consumed. In case any issue just make sure that program:messenger is working in supervisord. You can use make command `make logs-supervisord` for these needs.

### Message and Handler
Before you can send a message, you must create it first. In order to do something when your message is dispatched, you need to create a message handler. Please follow docs in order to implement it:

* [Message](https://symfony.com/doc/current/messenger.html#creating-a-message-handler)
* [Handler](https://symfony.com/doc/current/messenger.html#creating-a-message-handler)

### RabbitMQ Management HTTP API
When activated, the management plugin provides an HTTP API at http://server-name:15672/api/ by default. Browse to that location for more information on the API. For convenience the same API reference is available from GitHub:
* [RabbitMQ Management HTTP API](https://rawcdn.githack.com/rabbitmq/rabbitmq-server/v3.11.5/deps/rabbitmq_management/priv/www/api/index.html)


# Using Messenger

### Installation
Before using bundle, make sure that you have installed transports using next command inside your local shell:

```bash
make messenger-setup-transports
```

Note: After executing above command, necessary RabbitMQ queues and MySQL table for the failed messages should be created.

### Namespace structure
As we are using [DDD](https://en.wikipedia.org/wiki/Domain-driven_design) approach, we don't have standard `App\Message\...` and `App\MessageHandler\...` namespaces. While using DDD, we have `App\...\Domain\Message\...` and `App\...\Transport\MessageHandler\...` namespaces.

Please find existing examples inside `App\Tool\Domain\Message\...` and `App\Tool\Transport\MessageHandler\...`.

### External messages
Sometimes you need to have possibility to integrate application with external applications. For these needs, as example, we can use json message example:

```json
{
    "service": "someservice",
    "external_id": "3"
}
```

This application for these needs has RabbitMQ queue `external` (see `messenger.yaml` configuration file).

### External message handler
For processing above external message we have custom serializer `App\Tool\Transport\Serializer\ExternalMessageSerializer.php` and handler `App\Tool\Transport\MessageHandler\ExternalHandler.php`. Please extend it for your needs and don't forget about DDD layer flow `Transport -> Application -> Infrastructure -> Domain` (see [development.md](development.md)).

### Internal messages
Sometimes you need to have possibility to send async message for process something. For these needs, as example, you can use `App\Tool\Domain\Message\TestMessage.php` and Infrastructure service `App\General\Infrastructure\Service\MessageService.php` (just create an instance of message and use method `sendMessage`).

`App\Tool\Domain\Message\TestMessage.php` responsible for sending message to the `messages_high` queue (see routing interface configuration mapping inside `messenger.yaml`).

If you need to use another queue for the message (f.e. `messages_low`), just use another interface `App\General\Domain\Message\Interfaces\MessageLowInterface` for the `App\Tool\Domain\Message\TestMessage.php` class.

Note: Please pay your attention to the transports order processing(first async_priority_high, then async_priority_low) inside supervisord configuration file `docker\general\supervisord.conf` and program `program:messenger-consume`.

### Internal message handler
For processing above external message we have handler `App\Tool\Transport\MessageHandler\TestHandler.php`. Please extend it for your needs and don't forget about DDD layer flow `Transport -> Application -> Infrastructure -> Domain` (see [development.md](development.md)).

### Retries & Failures
If an exception is thrown while consuming a message from a transport it will automatically be re-sent to the transport to be tried again. By default, a message will be retried 3 times before being discarded or sent to the failure transport. Each retry will also be delayed, in case the failure was due to a temporary issue. All of this is configurable for each transport (see `messenger.yaml`).

This environment doesn't have default failure retry strategy and failed message will not be deleted after 3-rd retry by default. You can find existing retry strategy inside `App\General\Infrastructure\Messenger\Strategy\FailedRetry.php`.

You are able to configure some options for failure transport inside .env file:

```dotenv
# Send "failed" messages for unlimited retry (messenger:failed:retry). Possible values: 0|1. In case 1 - failed messages will be sent for unlimited retry. In case 0 - only 1 retry is possible.
MESSENGER_FAILED_IS_RETRYABLE=1
# Time in miliseconds before retry for "failed" messages (messenger:failed:retry). Available in case MESSENGER_FAILED_IS_RETRYABLE=1.
MESSENGER_FAILED_RETRY_WAITING_TIME=10000
# How many days we should have failed messages inside messenger_messages table
MESSENGER_MESSAGES_HISTORY_DAYS=7
```

All failed messages after 3-rd failed retry will be places into the `messenger_messages` MySQL table. You are able to retry it manually using console command `bin/console messenger:failed:retry` unlimited times. Please pay your attention that by default all failed messages inside `messenger_messages` older 7 days (configured as `MESSENGER_MESSAGES_HISTORY_DAYS`) will be deleted by cron job (`bin/console messenger:messages-cleanup`). So it means that you have by default 7 days for retry some failed messages manually or all such old messages will be deleted.

More details for configuring retries and failures you can find [here](https://symfony.com/doc/current/messenger.html#retries-failures).
