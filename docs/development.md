# Development
This document contains basic information and recommendation for development.

## General
* Follow the [PSR-1 guide](https://www.php-fig.org/psr/psr-1/), [PSR-12 guide](https://www.php-fig.org/psr/psr-12/), [Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html).
* Try to follow [DDD](https://en.wikipedia.org/wiki/Domain-driven_design) approach.
* Try to keep class names informative but not too long.
* Follow Symfony conventions and [best practices](https://symfony.com/doc/current/best_practices/index.html).
* Separate application logic from presentation and data-persistence layers.
* Use namespaces to group all related classes into separate folders.
* Put stuff in the cache when its easy enough to invalidate.
* Use [messenger](https://symfony.com/doc/current/components/messenger.html) to delegate when you don't need to wait for data to return.
* Write documentation for all things outside of standard MVC functions.
* Write application, integration and unit tests for all new features (in that order of priority).
* All functionality needs to be "mockable", so that you can test every part of the app without 3rd party dependencies.
* Use strict_types, type hinting and return type hinting.
* Use PHPStorm IDE as currently it is most powerful IDE for PHP development on today's market.

Within this application the base workflow is following:

`Controller/Command/EventSubscriber/MessageHandler(Transport layer) <--> Resource/Service(Application layer) <--> Repository/Service(Infrastructure layer) <--> Entity/Message/Service(Domain layer)`

#### Exceptions
* All Exceptions that should terminate the current request (and return an error message to the user) should be handled
using Symfony [best practice](https://symfony.com/doc/current/controller/error_pages.html#use-kernel-exception-event).
* All Exceptions that should be handled in the controller, or just logged for debugging, should be wrapped in a
try catch block (catchable Exceptions).
* Use custom Exceptions for all catchable scenarios, and try to use standard Exceptions for fatal Exceptions.
* Use custom Exceptions to log.

#### Entities
Entities should only be data-persistence layers, i.e. defines relationships, attributes, helper methods
but does not fetch collections of data. Entities are located on the Domain layer (according to DDD approach) and shouldn't
know anything about other layers (Application/Infrastructure) or framework. In this application we made some "exception"
for such components like Doctrine/Swagger/Serializer/Validator (for the first time) and you can find such
dependencies inside Entities.

Within this application we are using uuid v1 for the primary key inside Entities. Also we have id field as
binary type ([details](https://uuid.ramsey.dev/en/stable/database.html#using-as-a-primary-key)). If you need to convert
id into binary ordered time or from bin ordered time into a string inside query, please use MySql 8 internal functions [UUID_TO_BIN](https://dev.mysql.com/doc/refman/8.0/en/miscellaneous-functions.html#function_uuid-to-bin) and [BIN_TO_UUID](https://dev.mysql.com/doc/refman/8.0/en/miscellaneous-functions.html#function_bin-to-uuid).
For instance `... WHERE id = UUID_TO_BIN(:id, 1)`, or when you need to convert uuid binary ordered time into string representative `... WHERE BIN_TO_UUID(id, 1) = :id`.
The second argument determines if the byte order should be swapped, therefore when using uuid_binary you should pass 0 and when using uuid_binary_ordered_time you should pass 1.

#### Repositories
Repositories need to be responsible for parameter handling and query builder callbacks/joins. Should be located on
infrastructure layer. Parameter handling can help with generic REST queries.

#### Resources
Resource services are services between your controller/command and repository. Should be located on application layer.
Within this service it is possible to control how to `mutate` repository data for application needs.
Resource services are basically the application foundation and it can control your request and response as you like.
We have provided 2 examples how to build resource services: 1)resource with all-in-one actions (create/update/delete/etc, see example src/ApiKey/Application/Resource/ApiKeyResource.php) 
2)resource with single responsibility (f.e. count, see example src/ApiKey/Application/Resource/ApiKeyCountResource.php).

#### Controllers
Should be located on Transport layer. Keep controllers clean of application logic. They should ideally just inject
resources/services - either through the constructor (if used more than once) or in the controller method itself.
We have provided 2 examples how to build controllers: 1)controller with all-in-one actions (create/update/delete/etc, see example src/ApiKey/Transport/Controller/Api/v1/ApiKey/ApiKeyController.php)
2)controller with single responsibility (f.e. count, see example src/ApiKey/Transport/Controller/Api/v2/ApiKey/ApiKeyCountController.php)

#### Events
Events are handled by event listeners. Please follow instruction [here](https://symfony.com/doc/current/event_dispatcher.html).

#### Serializers
Use [Serializer component](https://symfony.com/doc/current/components/serializer.html) to transform data into JSON.

#### Services
Isolate 3rd party dependencies into Service classes for simple refactoring/extension.


## PHP code quality
You can control code quality of your PHP project using already integrated code quality tools. Before creating merge request you can run on your local PC code quality tools and get the report with issues that you can fix.
Also code quality tools integrated inside CI environment and after creating merge request you can check if you have some issues inside your code. Please find the list of code quality tools that we recommend to use while PHP backend development.

### PHP coding standard
This tool is an essential development tool that ensures your code remains coding standard.

PHP coding standard is available for dev/test environment using next local shell command:
```bash
make ecs
```

If you want to fix all possible issues in auto mode(some issues can be fixed only manually) just use next local shell command:
```bash
make ecs-fix
```

### PHP code sniffer
This tool is an essential development tool that ensures your code remains clean and consistent.

PHP Code Sniffer is available for dev/test environment using next local shell command:
```bash
make phpcs
```

If you are using [PhpStorm](https://www.jetbrains.com/phpstorm/) you can configure PHP Code Sniffer using recommendation
[here](https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html).

### PHPStan static analysis tool
PHPStan focuses on finding errors in your code without actually running it. It catches whole classes of bugs even before you write tests for the code.
It moves PHP closer to compiled languages in the sense that the correctness of each line of the code can be checked before you run the actual line.

PHPStan static analysis tool is available for dev/test environment using next local shell command:
```bash
make phpstan
```

### Phpinsights PHP quality checks
PHP Insights was carefully crafted to simplify the analysis of your code directly from your terminal, and is the perfect starting point to analyze the code quality of your PHP projects.

Phpinsights is available for dev/test environment using next local shell command:
```bash
make phpinsights
```

### PHP mess detector
This tool takes a given PHP source code base and look for several potential problems within that source. These problems can be things like:
* Possible bugs
* Suboptimal code
* Overcomplicated expressions
* Unused parameters, methods, properties

PHP mess detector is available for dev/test environment using next local shell command:
```bash
make phpmd
```

### PHP copy/paste detector
This tool is a copy/paste detector for PHP code.

PHP copy/paste detector is available for dev/test environment using next local shell commands:
```bash
make phpcpd
make phpcpd-html-report
```

### Composer tools
To normalize or validate your composer.json you can use next local shell commands:
```bash
make composer-normalize
make composer-validate
```

If you need to find unused packages by scanning your code you can use next local shell commands:
```bash
make composer-unused
```

In order to check the defined dependencies against your code you can use next local shell commands:
```bash
make composer-require-checker
```

### Metrics
This environment contains [PhpMetrics](https://github.com/phpmetrics/phpmetrics) to make some code analysis.
Use next local shell command in order to run it:
```bash
make phpmetrics
```
Note: You need run tests before this local shell command.

After execution above local shell command please open `reports/phpmetrics/index.html` with your browser.

### Rector
Rector instantly upgrades and refactors the PHP code of your application. It can help you in 2 major areas:
- Instant upgrades
- Automated refactoring

Rector now supports upgrades of your code from PHP 5.3 to 8.3 or upgrades your code for new framework version. This tool supports major open-source projects like Symfony, PHPUnit, Nette, Laravel, CakePHP and Doctrine.
You can find live demo [here](https://symfonycasts.com/screencast/symfony6-upgrade/rector) or more info [here](https://packagist.org/packages/rector/rector).

Rector is available for test/dev environment. If you need to run this tool, please use next local shell command in order to enter inside symfony container shell and then run rector:
```bash
make ssh
```
```bash
vendor/bin/rector process src/your_folder_with_code_for_refactoring
```
Note: You can process rector without specifying folder, in such case it will process src and tests folder.

### Qodana (trial)
Qodana is a smart code quality platform by JetBrains. This powerful static analysis engine enables development teams to automate code reviews, build quality gates, and enforce code quality guidelines enterprise-wide – all within their JetBrains ecosystems.
The platform can be integrated into any CI/CD pipeline and can analyze code (currently there are some issues with CI - https://youtrack.jetbrains.com/issue/QD-7379).

If you are using IDE PHPStorm, you can use it via menu `Tools` -> `Qodana` -> `Try Code Analysis with Qodana` -> `Try Locally` -> `Run`.
You can find some video [here](https://blog.jetbrains.com/qodana/2023/09/code-quality-under-pressure-supporting-developers-with-qodana-integration-in-intellij-based-ides/) or more info [here](https://www.jetbrains.com/help/qodana/getting-started.html).

## Database changes
Doctrine migrations it is functionality for versioning your database schema and easily deploying changes to it.
Migration files contain all necessary database changes to get application running with its database structure.
In order to migrate changes to your database please use next command in symfony container shell:
```bash
./bin/console doctrine:migrations:migrate
```
Note: Also you can use make command (`make migrate`) in your local shell and it will make necessary changes to main database and test database.

Please use next workflow for migrations:

1. Make changes (create/edit/delete) to entities in `/src/Entity/` folder
2. Run `diff` command to create new migration file
3. Run `migrate` command to make actual changes to your database
4. Run `validate` command to validate your mappings and actual database structure

Above commands you can run in symfony container shell using next: `./bin/console doctrine:migrations:<command>`.

Using above workflow allow you make database changes on your application.
Also you do not need to make any migrations files by hand (Doctrine will handle it).
Please always check generated migration files to make sure that those doesn't contain anything that you really don't want.

## IDE
Short list of most popular IDE for PHP development:

* [PhpStorm](https://www.jetbrains.com/phpstorm/)
* [Zend Studio](https://www.zend.com/products/zend-studio)
* [Eclipse PDT](https://www.eclipse.org/pdt/)
* [NetBeans](https://netbeans.org/)
* [Sublime Text](https://www.sublimetext.com/)
