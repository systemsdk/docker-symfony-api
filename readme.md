# PHP symfony environment with JSON REST API example
Docker environment (based on official php and mysql docker hub repositories) required to run Symfony with JSON REST API example.

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Actions Status](https://github.com/dimadeush/docker-symfony-api/workflows/Symfony%20Rest%20API/badge.svg)](https://github.com/dimadeush/docker-symfony-api/actions)
[![CircleCI](https://circleci.com/gh/dimadeush/docker-symfony-api.svg?style=svg)](https://circleci.com/gh/dimadeush/docker-symfony-api)
[![Coverage Status](https://coveralls.io/repos/github/dimadeush/docker-symfony-api/badge.svg)](https://coveralls.io/github/dimadeush/docker-symfony-api)

[Source code](https://github.com/dimadeush/docker-symfony-api.git)

## Requirements
* Docker version 18.06 or later
* Docker compose version 1.22 or later
* An editor or IDE
* MySQL Workbench

Note: OS recommendation - Linux Ubuntu based.

## Components
1. Nginx 1.19
2. PHP 7.4 fpm
3. MySQL 8
4. Symfony 4 LTS
5. RabbitMQ 3
6. Elasticsearch 7
7. Kibana 7

## Setting up PROD environment
1.Clone this repository from GitHub.

2.Edit docker-compose-prod.yml and set necessary user/password for MySQL and RabbitMQ.

Note: Delete var/mysql-data folder if it is exist.

3.Edit env.prod and set necessary user/password for MySQL and RabbitMQ.

4.Elasticsearch is pre-configured with the following privileged bootstrap user:
```bash
user: elastic
password: ********
```

5.Build, start and install the docker images from your terminal:
```bash
make build-prod
make start-prod
make generate-jwt-keys
```

6.Make sure that you have installed migrations / created roles and groups / messenger transports / elastic template:
```bash
make migrate-no-test
make create-roles-groups
make migrate-cron-jobs
make messenger-setup-transports
make elastic-create-or-update-template
```

## Setting up STAGING environment
1.Clone this repository from GitHub.

Note: Delete var/mysql-data folder if it is exist.

2.Elasticsearch is pre-configured with the following privileged bootstrap user:
```bash
user: elastic
password: ********
```

3.Build, start and install the docker images from your terminal:
```bash
make build-staging
make start-staging
make generate-jwt-keys
```

4.Make sure that you have installed migrations / created roles and groups / messenger transports / elastic template:
```bash
make migrate-no-test
make create-roles-groups
make migrate-cron-jobs
make messenger-setup-transports
make elastic-create-or-update-template
```

## Setting up DEV environment
1.Clone this repository from GitHub.

2.Set another APP_SECRET for application in .env.prod and .env.staging files.

Note 1: You can get unique secret key for example [here](http://nux.net/secret).

Note 2: Do not use .env.local.php on dev and test environment (delete it if exist).

Note 3: Delete var/mysql-data folder if it is exist.

3.Add domain to local 'hosts' file:
```bash
127.0.0.1    localhost
```

4.Configure `/docker/dev/xdebug.ini` (optional):

- In case you need debug only requests with IDE KEY: PHPSTORM from frontend in your browser:
```bash
xdebug.remote_autostart = 0
```
Install locally in Firefox extension "Xdebug helper" and set in settings IDE KEY: PHPSTORM

- In case you need debug any request to an api (by default):
```bash
xdebug.remote_autostart = 1
```

5.Elasticsearch is pre-configured with the following privileged bootstrap user(you can use it in order to enter in Kibana):
```bash
user: elastic
password: changeme
```

Note: For prod/staging environment another password should be used.

6.Build, start and install the docker images from your terminal:
```bash
make build
make start
make composer-install
make generate-jwt-keys
```

7.Make sure that you have installed migrations / created roles and groups / messenger transports / elastic template:
```bash
make migrate
make create-roles-groups
make migrate-cron-jobs
make messenger-setup-transports
make elastic-create-or-update-template
```

8.In order to use this application, please open in your browser next urls: 
- [http://localhost/api/doc](http://localhost/api/doc)
- [http://localhost:5601 (Kibana)](http://localhost:5601)

## How to enable paid features for Elasticsearch
Switch the value of Elasticsearch's `xpack.license.self_generated.type` option from `basic` to `trial` (`/docker/elasticsearch/config/elasticsearch.yml`).

## Getting shell to container
After application will start (`make start`) and in order to get shell access inside symfony container you can run following command:
```bash
make ssh
```
Note 1: Please use next make commands in order to enter in other containers: `make ssh-nginx`, `make ssh-supervisord`, `make ssh-mysql`, `make ssh-rabbitmq`.

Note 2: Please use `exit` command in order to return from container's shell to local shell.

## Building containers
In case you edited Dockerfile or other environment configuration you'll need to build containers again using next commands:
```bash
make stop
make build
make start
```
Note 1: Please use next command if you need to build staging environment `make build-staging` instead `make build`.

Note 2: Please use next command if you need to build prod environment `make build-prod` instead `make build`.

## Start and stop environment
Please use next make commands in order to start and stop environment:
```bash
make start
make stop
```
Note 1: For staging environment need to be used next make commands: `make start-staging`, `make stop-staging`.

Note 2: For prod environment need to be used next make commands: `make start-prod`, `make stop-prod`.

## Additional main command available
```bash
make build
make build-test
make build-staging
make build-prod

make start
make start-test
make start-staging
make start-prod

make stop
make stop-test
make stop-staging
make stop-prod

make restart
make restart-test
make restart-staging
make restart-prod

make env-staging
make env-prod

make generate-jwt-keys

make ssh
make ssh-nginx
make ssh-supervisord
make ssh-mysql
make ssh-rabbitmq
make ssh-elasticsearch
make ssh-kibana

make composer-install-no-dev
make composer-install
make composer-update

make info

make logs
make logs-nginx
make logs-supervisord
make logs-mysql
make logs-rabbitmq
make logs-elasticsearch
make logs-kibana

make drop-migrate
make migrate
make migrate-no-test
make migrate-cron-jobs

make fixtures

make create-roles-groups

make messenger-setup-transports

make elastic-create-or-update-template

make phpunit
make report-code-coverage

make phpcs
make ecs
make ecs-fix
make phpmetrics
make phpcpd
make phpmd
make phpstan
make phpinsights

etc....
```
Notes: Please see more commands in Makefile

## Architecture & packages
* [Symfony 4](https://symfony.com)
* [doctrine-migrations-bundle](https://github.com/doctrine/DoctrineMigrationsBundle)
* [doctrine-fixtures-bundle](https://github.com/doctrine/DoctrineFixturesBundle)
* [command-scheduler-bundle](https://github.com/j-guyon/CommandSchedulerBundle)
* [phpunit](https://github.com/sebastianbergmann/phpunit)
* [phpunit-bridge](https://github.com/symfony/phpunit-bridge)
* [browser-kit](https://github.com/symfony/browser-kit)
* [css-selector](https://github.com/symfony/css-selector)
* [security-checker](https://github.com/sensiolabs/security-checker)
* [messenger](https://symfony.com/doc/current/messenger.html)
* [serializer-pack](https://packagist.org/packages/symfony/serializer-pack)
* [composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin)
* [ergebnis/composer-normalize](https://github.com/ergebnis/composer-normalize)
* [symfony/requirements-checker](https://github.com/symfony/requirements-checker)
* [security-advisories](https://github.com/Roave/SecurityAdvisories)
* [jwt-authentication-bundle](https://packagist.org/packages/lexik/jwt-authentication-bundle)
* [automapper-plus-bundle](https://packagist.org/packages/mark-gerarts/automapper-plus-bundle)
* [symfony-console-form](https://packagist.org/packages/matthiasnoback/symfony-console-form)
* [api-doc-bundle](https://packagist.org/packages/nelmio/api-doc-bundle)
* [cors-bundle](https://packagist.org/packages/nelmio/cors-bundle)
* [device-detector](https://packagist.org/packages/matomo/device-detector)
* [uuid-doctrine](https://packagist.org/packages/ramsey/uuid-doctrine)
* [doctrine-extensions-bundle](https://packagist.org/packages/stof/doctrine-extensions-bundle)
* [php-coveralls](https://github.com/php-coveralls/php-coveralls)
* [easy-coding-standard](https://github.com/Symplify/EasyCodingStandard)
* [PhpMetrics](https://github.com/phpmetrics/PhpMetrics)
* [phpcpd](https://packagist.org/packages/sebastian/phpcpd)
* [phpmd](https://packagist.org/packages/phpmd/phpmd)
* [phpstan](https://packagist.org/packages/phpstan/phpstan)
* [phpinsights](https://packagist.org/packages/nunomaduro/phpinsights)
* [beberlei/doctrineextensions](https://github.com/beberlei/DoctrineExtensions)
* [elasticsearch](https://github.com/elastic/elasticsearch-php)

## External links / resources
* [Symfony Flex REST API](https://github.com/tarlepp/symfony-flex-backend.git): code in "src/" folder forked from Symfony Flex REST API.

## Guidelines
* [Commands](docs/commands.md)
* [Development](docs/development.md)
* [Testing](docs/testing.md)
* [IDE PhpStorm configuration](docs/phpstorm.md)
* [Xdebug configuration](docs/xdebug.md)
* [Swagger](docs/swagger.md)
* [Postman](docs/postman.md)
* [Messenger component](docs/messenger.md)

## Working on your project
1. For new feature development, fork `develop` branch into a new branch with one of the two patterns:
    * `feature/{ticketNo}`
2. Commit often, and write descriptive commit messages, so its easier to follow steps taken when reviewing.
3. Push this branch to the repo and create pull request into `develop` to get feedback, with the format `feature/{ticketNo}` - "Short descriptive title of Jira task".
4. Iterate as needed.
5. Make sure that "All checks have passed" on CircleCI(or another one in case you are not using CircleCI) and status is green.
6. When PR is approved, it will be squashed & merged, into `develop` and later merged into `release/{No}` for deployment.
