# PHP Symfony environment with a JSON REST API
A scalable Docker-based environment (based on official PHP and MySQL Docker Hub repositories) required to run Symfony with a JSON REST APIs.

[![Actions Status](https://github.com/systemsdk/docker-symfony-api/workflows/Symfony%20Rest%20API/badge.svg)](https://github.com/systemsdk/docker-symfony-api/actions)
[![CircleCI](https://circleci.com/gh/systemsdk/docker-symfony-api.svg?style=svg)](https://circleci.com/gh/systemsdk/docker-symfony-api)
[![Coverage Status](https://coveralls.io/repos/github/systemsdk/docker-symfony-api/badge.svg)](https://coveralls.io/github/systemsdk/docker-symfony-api)
[![Latest Stable Version](https://poser.pugx.org/systemsdk/docker-symfony-api/v)](https://packagist.org/packages/systemsdk/docker-symfony-api)
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

[Source code](https://github.com/systemsdk/docker-symfony-api.git)


> This repository provides a basic (lite) DDD foundation and the standard core logic. If you are looking for a cleaner, pragmatic DDD architecture, a superior environment with robust core logic, and flawless documentation, we highly recommend our commercial product [Enterprise API](https://www.systemsdk.com/em_case_study/enterprise-api/).

## Requirements
* Docker Engine version 23.0 or later
* Docker Compose version 2.0 or later
* An editor or IDE
* MySQL Workbench

Note: We recommend using a Linux Ubuntu-based OS for the best experience.

## Components
**Core Stack**
* **PHP 8.5 FPM** - Main application runtime.
* **Symfony 8** - High-performance PHP framework for the REST API.
* **Nginx 1.31** - Web server and reverse proxy.

**Data & Caching**
* **MySQL 9** - Primary relational database.
* **Redis 8** - In-memory data structure store and cache.

**Search & Asynchronous Processing**
* **Elasticsearch 7** - Distributed search and analytics engine.
* **RabbitMQ 4** - Robust message broker for background jobs.

**Monitoring & Development Tools**
* **Kibana 7** - Data visualization dashboard for Elasticsearch.
* **Mailpit** - Email testing tool (available in the development environment only).

## Setting up Docker Engine & Docker Compose
To install Docker Engine and Docker Compose, please follow the official [Docker Engine Installation Guide](https://docs.docker.com/engine/install/).

**For Linux Users:**
After installation, run the following command to manage Docker as a non-root user (this allows you to run Docker without `sudo`):
```bash
sudo usermod -aG docker $USER
```

Note: You must log out and log back in for this change to take effect.

**For macOS Users:**
If you are using Docker Desktop for macOS 12.2 or later, we highly recommend enabling [virtiofs](https://www.docker.com/blog/speed-boost-achievement-unlocked-on-docker-desktop-4-6-for-mac/) for a significant performance boost.

Note: Enabled by default since Docker Desktop v4.22.

## Setting up the DEV environment
1. You can clone this repository from GitHub or install via composer.

   If you have installed composer and want to install environment via composer you can use next cmd command:
   ```bash
   composer create-project systemsdk/docker-symfony-api api-example-app
   ```
 
2. Set a unique `APP_SECRET` for the application in `.env.prod` and `.env.staging` files.
   * **Secret Key**: You can generate a secure key by running: `openssl rand -hex 16` (do not use third-party websites for generating this value).
   * **Environment Files**: Do not use `.env.local.php` on dev and test environments (delete it if it exists).
   * **Custom Configs**: If you want to change default web port/xdebug configurations, you can create an `.env.local` file and override parameters there (see `.env` file).
   * **Database**: Delete the `var/mysql-data` folder if it exists before starting.
 
3. Verify that your local `hosts` file contains the default mapping for `localhost` (this is usually set by default in all operating systems):
   ```text
   127.0.0.1    localhost
   ```

   Note: The file is located at `/etc/hosts` on Linux/macOS and `C:\Windows\System32\drivers\etc\hosts` on Windows.

4. Configure Xdebug (Optional)

   Depending on your operating system, you can customize Xdebug behavior by editing either `/docker/dev/xdebug-main.ini` (Linux/Windows) or `/docker/dev/xdebug-osx.ini` (macOS).

    * To debug every request (Default):
      
      This is the default setting. It will intercept and debug all incoming API requests.
      ```ini
      xdebug.start_with_request = yes
      ```

    * To debug only specific requests (On-Demand):
      
      If you prefer to trigger the debugger manually only when making requests from a browser frontend, change the configuration to:
      ```ini
      xdebug.start_with_request = no
      ```
      
      Tip: Install the "Xdebug helper" extension for Chrome or Firefox and set the IDE Key to `PHPSTORM` in the extension settings.

5. Elasticsearch & Kibana Credentials

   Elasticsearch is pre-configured with a privileged bootstrap user. You can use these credentials to log into the Kibana dashboard:
   * **Username:** `elastic`
   * **Password:** `changeme`

   Security Note: You must use a strong, unique password for staging and production environments. Never use these default credentials outside of local development.

6. Build and Initialize the Environment
   
   Run the following commands in your terminal to build the Docker images, start the containers, install PHP dependencies and generate the JWT authentication keys:
   ```bash
   make build
   make start
   make composer-install
   make generate-jwt-keys
   ```

7. Apply Migrations and Configurations

   Execute the following commands to set up the database structure, initialize default user roles and groups, configure cron jobs, prepare message broker transports and apply Elasticsearch templates:
   ```bash
   make migrate
   make create-roles-groups
   make migrate-cron-jobs
   make messenger-setup-transports
   make elastic-create-or-update-template
   ```

8. Access Application Services

   Once the environment is successfully running, you can access the various services in your browser using the following URLs:
   * **API Documentation:** [http://localhost/api/doc](http://localhost/api/doc)
   * **RabbitMQ Management:** [http://localhost:15672](http://localhost:15672)
   * **Kibana Dashboard:** [http://localhost:5601](http://localhost:5601) (Use the credentials from Step 6)
   * **Mailpit (Debug Email):** [http://localhost:8025](http://localhost:8025)

## Setting up the STAGING environment locally
Important: This section describes how to set up the staging environment locally for debugging and verification purposes only. A real STAGING environment must be deployed on a dedicated server and should be as close to the PRODUCTION environment as possible.

Note: These steps assume you have already completed steps 1 through 3 of the "Setting up the DEV environment" section above.

1. Database Clean-up
   
   Delete the `var/mysql-data` folder if it exists before starting.

2. Elasticsearch & Kibana Credentials

   Elasticsearch is pre-configured with a privileged bootstrap user. You can use these credentials to log into the Kibana dashboard:
    * **Username:** `elastic`
    * **Password:** `changeme`

   Security Note: You must use a strong, unique password for the real staging environment. Never use these default credentials outside of local development.

3. Build and Initialize the Environment
   
   Run the following commands in your terminal to build the staging Docker images, start the containers and generate the JWT authentication keys:
   ```bash
   make build-staging
   make start-staging
   make generate-jwt-keys
   ```
   Note: With `opcache.validate_timestamps=0` ([php.ini](docker/staging/php.ini)) enabled for performance, any manual file changes or code updates require a PHP-FPM restart/reload to take effect.

4. Apply Migrations and Configurations
   
   Execute the following commands to set up the database structure, initialize default user roles and groups, configure cron jobs, prepare message broker transports and apply Elasticsearch templates:
   ```bash
   make migrate-no-test
   make create-roles-groups
   make migrate-cron-jobs
   make messenger-setup-transports
   make elastic-create-or-update-template
   ```

## Setting up the PROD environment locally
Important: This section describes how to set up the production environment locally for debugging and verification purposes only. A real PROD environment must be deployed on a dedicated server.

Note: These steps assume you have already completed steps 1 through 3 of the "Setting up the DEV environment" section above.

1. Database and RabbitMQ Clean-up

   Delete the `var/mysql-data`, `var/rabbitmq` and `var/elasticsearch-data/nodes` folders if they exist before starting.

2. Edit the `.env.prod` file and set a secure password for MySQL, as well as a username and password for RabbitMQ.

3. Elasticsearch & Kibana Credentials

   Elasticsearch is pre-configured with a privileged bootstrap user. You can use these credentials to log into the Kibana dashboard:
    * **Username:** `elastic`
    * **Password:** `changeme`

   Security Note: You must use a strong, unique password for the real production environment. Never use these default credentials outside of local development.

4. Build and Initialize the Environment

   Run the following commands in your terminal to build the production Docker images, start the containers and generate the JWT authentication keys:
   ```bash
   make build-prod
   make start-prod
   make generate-jwt-keys
   ```
   Note: With `opcache.validate_timestamps=0` ([php.ini](docker/prod/php.ini)) enabled for performance, any manual file changes or code updates require a PHP-FPM restart/reload to take effect.

5. Apply Migrations and Configurations

   Execute the following commands to set up the database structure, initialize default user roles and groups, configure cron jobs, prepare message broker transports and apply Elasticsearch templates:
   ```bash
   make migrate-no-test
   make create-roles-groups
   make migrate-cron-jobs
   make messenger-setup-transports
   make elastic-create-or-update-template
   ```

## Enabling Paid Features for Elasticsearch
To unlock Elasticsearch's paid features (for a 30-day trial), you need to modify its configuration file:

1. Open `/docker/elasticsearch/config/elasticsearch.yml`.
2. Change the value of the `xpack.license.self_generated.type` option from `basic` to `trial`.
3. Restart the environment to apply the changes.

## Accessing Container Shells
Once the application is running (via `make start`), you can easily access the command line inside your containers.

To open a shell inside the main **Symfony** container, run:
```bash
make ssh
```

You can also access the other services using the following commands:
```bash
make ssh-nginx
make ssh-supervisord
make ssh-mysql
make ssh-rabbitmq
```

Tip: Type `exit` and press Enter to leave the container's shell and return to your local terminal.

## Rebuilding Containers
If you modify any `Dockerfile` or environment configurations, you will need to rebuild the containers using the following commands:
```bash
make down
make build
make start
```

Note: Use environment-specific commands if you need to rebuild the test, staging, or production environments. For a complete list of available commands, run `make help`.

## Starting and Stopping Containers
Use the following commands to start or stop the development environment:
```bash
make start
make stop
```

If you are working with the staging or production environments, use their respective commands:
* Staging: `make start-staging` or `make stop-staging`.
* Production: `make start-prod` or `make stop-prod`.

## Stopping and Removing Containers
To completely stop and remove all environment containers and networks, use the following command:
```bash
make down
```

Note: Use environment-specific commands if you need to tear down the test, staging, or production environments. For a complete list of available commands, run `make help`.

## Available Makefile Commands
Here is a reference list of the primary commands available for managing the environment, databases, logs and testing:
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

make down
make down-test
make down-staging
make down-prod

make restart
make restart-test
make restart-staging
make restart-prod

make env-staging
make env-prod

make generate-jwt-keys

make ssh
make ssh-root
make fish
make ssh-nginx
make ssh-supervisord
make ssh-mysql
make ssh-rabbitmq
make ssh-elasticsearch
make ssh-kibana

make composer-install-no-dev
make composer-install
make composer-update
make composer-audit

make info
make help

make logs
make logs-nginx
make logs-supervisord
make logs-mysql
make logs-rabbitmq
make logs-elasticsearch
make logs-kibana
make logs-redis

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
make phpcpd-html-report
make phpmd
make phpstan
make phpinsights

# ... and many more
```
Note: For a complete list of all available commands, please inspect the `Makefile` directly or run `make help`.

## Architecture & packages
**Core Framework & Utilities**
* [Symfony](https://symfony.com)
* [messenger](https://symfony.com/doc/current/messenger.html)
* [api-doc-bundle](https://packagist.org/packages/nelmio/api-doc-bundle)
* [cors-bundle](https://packagist.org/packages/nelmio/cors-bundle)
* [command-scheduler-bundle](https://packagist.org/packages/dukecity/command-scheduler-bundle)
* [automapper-plus-bundle](https://packagist.org/packages/mark-gerarts/automapper-plus-bundle)
* [symfony-console-form](https://packagist.org/packages/matthiasnoback/symfony-console-form)
* [easy-log-bundle](https://packagist.org/packages/systemsdk/easy-log-bundle)
* [device-detector](https://packagist.org/packages/matomo/device-detector)

**Database & Search**
* [doctrine-migrations-bundle](https://github.com/doctrine/DoctrineMigrationsBundle)
* [doctrine-fixtures-bundle](https://github.com/doctrine/DoctrineFixturesBundle)
* [uuid-doctrine](https://packagist.org/packages/ramsey/uuid-doctrine)
* [doctrine-extensions](https://packagist.org/packages/gedmo/doctrine-extensions)
* [beberlei/doctrineextensions](https://github.com/beberlei/DoctrineExtensions)
* [elasticsearch](https://github.com/elastic/elasticsearch-php)

**Authentication & Security**
* [jwt-authentication-bundle](https://packagist.org/packages/lexik/jwt-authentication-bundle)
* [security-checker](https://github.com/fabpot/local-php-security-checker)
* [security-advisories](https://github.com/Roave/SecurityAdvisories)

**Testing, QA & Static Analysis**
* [phpunit](https://github.com/sebastianbergmann/phpunit)
* [phpunit-bridge](https://github.com/symfony/phpunit-bridge)
* [dama/doctrine-test-bundle](https://packagist.org/packages/dama/doctrine-test-bundle)
* [browser-kit](https://github.com/symfony/browser-kit)
* [css-selector](https://github.com/symfony/css-selector)
* [php-coveralls](https://github.com/php-coveralls/php-coveralls)
* [phpstan](https://packagist.org/packages/phpstan/phpstan)
* [phpmd](https://packagist.org/packages/phpmd/phpmd)
* [phpcpd](https://packagist.org/packages/systemsdk/phpcpd)
* [PhpMetrics](https://github.com/phpmetrics/PhpMetrics)
* [phpinsights](https://packagist.org/packages/nunomaduro/phpinsights)
* [easy-coding-standard (ECS)](https://github.com/Symplify/EasyCodingStandard)
* [rector](https://packagist.org/packages/rector/rector)

**Composer & Infrastructure Tools**
* [composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin)
* [composer-normalize](https://github.com/ergebnis/composer-normalize)
* [composer-unused](https://packagist.org/packages/icanhazstring/composer-unused)
* [composer-require-checker](https://packagist.org/packages/maglnet/composer-require-checker)
* [requirements-checker](https://github.com/symfony/requirements-checker)

## Guidelines
* [Commands](docs/commands.md)
* [Development](docs/development.md)
* [IDE PhpStorm Configuration](docs/phpstorm.md)
* [Xdebug Configuration](docs/xdebug.md)
* [Swagger](docs/swagger.md)
* [Postman](docs/postman.md)
* [Redis GUI](docs/rdm.md)
* [Messenger Component](docs/messenger.md)
* [Code Quality Tools](docs/code-quality.md)
* [Testing](docs/testing.md)
* [API Key](docs/api-key.md)

## Development Workflow
1. **Branching:** Create a new branch from `develop` using one of the following patterns:
    * `feature/{ticketNo}`
    * `bugfix/{ticketNo}`
2. **Commits:** Commit frequently and write clear, descriptive commit messages to facilitate the review process.
3. **Pull Request:** Push your branch to the repository and open a Pull Request (PR) against the `develop` branch. Use the following naming convention for your PR: `feature/{ticketNo} - Short descriptive title of the Jira task`.
4. **Review:** Address any feedback from reviewers and iterate as needed.
5. **CI/CD Checks:** Ensure that all continuous integration checks (e.g., CircleCI) pass successfully and the build status is green.
6. **Merge:** Once approved, your PR will be squashed and merged into `develop`. It will later be merged into a `release/{version}` branch for deployment.

Note: For a detailed visual guide on this branching model, please refer to the [Git Flow Cheatsheet](https://danielkummer.github.io/git-flow-cheatsheet).

## License
[The MIT License (MIT)](LICENSE)
