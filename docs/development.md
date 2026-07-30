# Development Guidelines
This document outlines the fundamental principles, coding standards and best practices for developing within this project.

## General Principles
* Follow the [PSR-1](https://www.php-fig.org/psr/psr-1/) and [PSR-12](https://www.php-fig.org/psr/psr-12/) standards, along with the official Symfony [Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html).
* Adopt the [Domain-Driven Design (DDD)](https://en.wikipedia.org/wiki/Domain-driven_design) approach to structure complex business logic.
* Keep class names descriptive, informative and concise.
* Adhere strictly to Symfony conventions and [best practices](https://symfony.com/doc/current/best_practices/index.html).
* Maintain a clear separation of concerns by isolating application logic from the presentation and data-persistence layers.
* Use namespaces logically to group related classes into coherent directories.
* Leverage caching for expensive operations, provided that the cache invalidation strategy is straightforward and reliable.
* Use the [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html) component for asynchronous task delegation when an immediate response is not required.
* Document all custom architectural decisions, complex logic and functionality that falls outside of standard MVC patterns.
* Write application, integration and unit tests for all new features (prioritizing in that exact order).
* Design all functionality to be easily mockable. This ensures every part of the application can be tested in isolation without relying on third-party dependencies.
* Enforce strict typing (`declare(strict_types=1);`) and consistently use both parameter and return type hints.
* We highly recommend using **PhpStorm** as your primary IDE, as it provides the most robust toolset for modern PHP development.

For this application the base workflow is following:

`Controller/Command/EventSubscriber/MessageHandler(Transport layer) <--> Resource/Service(Application layer) <--> Repository/Service(Infrastructure layer) <--> Entity/Message/Service(Domain layer)`

#### Controllers
Should be located on the Transport layer. Keep controllers clean of application logic. They should ideally just inject
resources/services - either through the constructor (if used more than once) or in the controller method itself.

We have provided 2 examples how to build controllers:

1)controller with all-in-one actions (create/update/delete/etc, see example src/ApiKey/Transport/Controller/Api/v1/ApiKey/ApiKeyController.php)

2)controller with single responsibility (f.e. count, see example src/ApiKey/Transport/Controller/Api/v2/ApiKey/ApiKeyCountController.php)

#### Events
Events are handled by event listeners. Please follow instruction [here](https://symfony.com/doc/current/event_dispatcher.html).

#### Serializers
Use [Serializer component](https://symfony.com/doc/current/components/serializer.html) to transform data into JSON.

#### Services
Isolate 3rd party dependencies into Service classes for simple refactoring/extension.

#### Resources
Resource services are services between controller/command and repository. Should be located on application layer.
Inside this service it is possible to control how to `mutate` repository data for application needs.
Resource services are basically the application foundation and it can control your request and response as you like.

We have provided 2 examples how to build resource services:

1)resource with all-in-one actions (create/update/delete/etc, see example src/ApiKey/Application/Resource/ApiKeyResource.php)

2)resource with single responsibility (f.e. count, see example src/ApiKey/Application/Resource/ApiKeyCountResource.php).

#### Repositories
Repositories need to be responsible for parameter handling and query builder callbacks/joins. Should be located on infrastructure layer. Parameter handling can help with generic REST queries.

#### Entities
Entities should only be data-persistence layers, i.e. defines relationships, attributes, helper methods but does not fetch collections of data.
Entities are located on the Domain layer (according to DDD approach) and shouldn't know anything about other layers (Application/Infrastructure) or framework.
In this application we made some "exception" for such components like Doctrine/Swagger/Serializer/Validator (for the first time) and you can find such dependencies inside Entities.

Inside this application we are using uuid v1 for the primary key inside Entities. Also we have id field as binary type ([details](https://uuid.ramsey.dev/en/stable/database.html#using-as-a-primary-key)).
If you need to convert id into binary ordered time or from bin ordered time into a string inside query, please use MySql 8 internal functions [UUID_TO_BIN](https://dev.mysql.com/doc/refman/8.0/en/miscellaneous-functions.html#function_uuid-to-bin) and [BIN_TO_UUID](https://dev.mysql.com/doc/refman/8.0/en/miscellaneous-functions.html#function_bin-to-uuid).
For instance `... WHERE id = UUID_TO_BIN(:id, 1)`, or when you need to convert uuid binary ordered time into string representative `... WHERE BIN_TO_UUID(id, 1) = :id`.
The second argument determines if the byte order should be swapped. Therefore, when using uuid_binary, you should pass 0. When using uuid_binary_ordered_time you should pass 1.

#### Exceptions
* All Exceptions that should terminate the current request (and return an error message to the user) should be handled using Symfony [best practice](https://symfony.com/doc/current/controller/error_pages.html#use-kernel-exception-event).
* All Exceptions that should be handled in the controller, or just logged for debugging, should be wrapped in a try catch block (catchable Exceptions).
* Use custom Exceptions for all catchable scenarios, and try to use standard Exceptions for fatal Exceptions.
* Use custom Exceptions to log.

## Database Migrations
We use [Doctrine Migrations](https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html) to version our database schema and ensure safe, repeatable deployments. Migration files contain all the necessary database changes to keep the application and its database structure perfectly synchronized.

* **The Strict Workflow:** Never modify the database schema manually. Always follow this procedure:
    * **Modify Entities:** Make your changes (create/edit/delete) to the Entity classes within their respective Domain layers (e.g., `src/User/Domain/Entity/`).
    * **Generate Diff:** Run the `diff` command. Doctrine will automatically compare your mapping files with the current database structure and generate a new migration class.
    * **Manual Review:** *Always* check the generated migration file by hand. Ensure it only contains the changes you intended and isn't attempting to execute destructive drops or unintended alterations.
    * **Migrate:** Run the `migrate` command to apply the actual changes to your database.
    * **Validate:** Run the `validate` command to guarantee your Doctrine mappings and the actual database structure are perfectly aligned.

* **CLI Commands (Symfony Container Shell)**
  ```bash
  ./bin/console doctrine:migrations:diff
  ./bin/console doctrine:migrations:migrate
  ./bin/console doctrine:schema:validate
  ```

* **Make Commands (Local Shell)**
    
    To simplify the process for `doctrine:migrations:migrate` and ensure your environments stay in sync, use the provided Makefile commands in your local shell:
    ```bash
    make migrate
    ```
  > 💡 Note: This helper command will automatically apply the necessary migrations (`doctrine:migrations:migrate`) to both your main database and your test database, saving you from running commands twice.

## IDE
To maintain high code quality and adhere to the project's architectural standards, we recommend using a professional development IDEs:

* [PhpStorm](https://www.jetbrains.com/phpstorm/)
* [Eclipse PDT](https://www.eclipse.org/pdt/)
* [NetBeans](https://netbeans.org/)
* [Sublime Text](https://www.sublimetext.com/)
