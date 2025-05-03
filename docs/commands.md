# Commands
This document describing commands that can be used in local shell or inside symfony container shell.

## Local shell (Makefile)
This environment comes with "Makefile" and it allows to simplify using some functionality.
In order to use command listed bellow just use next syntax in your local shell: `make {command name}`.
Next commands available for this environment:
```bash
make help                               # Shows available commands with description

make build                              # Build dev environment
make build-test                         # Build test or continuous integration environment
make build-staging                      # Build staging environment
make build-prod                         # Build prod environment

make start                              # Start dev environment
make start-test                         # Start test or continuous integration environment
make start-staging                      # Start staging environment
make start-prod                         # Start prod environment

make stop                               # Stop dev environment containers
make stop-test                          # Stop test or continuous integration environment containers
make stop-staging                       # Stop staging environment containers
make stop-prod                          # Stop prod environment containers

make down                               # Stop and remove dev environment containers, networks
make down-test                          # Stop and remove test or continuous integration environment containers, networks
make down-staging                       # Stop and remove staging environment containers, networks
make down-prod                          # Stop and remove prod environment containers, networks

make restart                            # Stop and start dev environment
make restart-test                       # Stop and start test or continuous integration environment
make restart-staging                    # Stop and start staging environment
make restart-prod                       # Stop and start prod environment

make env-staging                        # Creates cached config file .env.local.php (usually for staging environment)
make env-prod                           # Creates cached config file .env.local.php (usually for prod environment)

make generate-jwt-keys                  # Generates RSA keys for JWT

make ssh                                # Get bash inside symfony docker container
make ssh-root                           # Get bash as root user inside symfony docker container
make fish                               # Get fish shell inside symfony docker container (https://www.youtube.com/watch?v=C2a7jJTh3kU)
make ssh-nginx                          # Get bash inside nginx docker container
make ssh-supervisord                    # Get bash inside supervisord docker container (cron jobs running there, etc...)
make ssh-mysql                          # Get bash inside mysql docker container
make ssh-rabbitmq                       # Get bash inside rabbitmq docker container
make ssh-elasticsearch                  # Get bash inside elasticsearch docker container
make ssh-kibana                         # Get bash inside kibana docker container

make exec                               # Executes some command, under the www-data user, defined in cmd="..." variable inside symfony container shell
make exec-bash                          # Executes several commands, under the www-data user, defined in cmd="..." variable inside symfony container shell
make exec-by-root                       # Executes some command, under the root user, defined in cmd="..." variable inside symfony container shell

make report-prepare                     # Creates /reports/coverage folder, will be used for report after running tests
make report-clean                       # Removes all reports in /reports/ folder

make wait-for-db                        # Checks MySQL database availability, using for CI (f.e. /.circleci folder)
make wait-for-elastic                   # Checks Elastic availability, using for CI (f.e. /.circleci folder)

make composer-install-no-dev            # Installs composer no-dev dependencies
make composer-install                   # Installs composer dependencies
make composer-update                    # Updates composer dependencies
make composer-audit                     # Checks for security vulnerability advisories for installed packages

make info                               # Shows Php and Symfony version

make logs                               # Shows logs from the symfony container. Use ctrl+c in order to exit
make logs-nginx                         # Shows logs from the nginx container. Use ctrl+c in order to exit
make logs-supervisord                   # Shows logs from the supervisord container. Use ctrl+c in order to exit
make logs-mysql                         # Shows logs from the mysql container. Use ctrl+c in order to exit
make logs-rabbitmq                      # Shows logs from the rabbitmq container. Use ctrl+c in order to exit
make logs-elasticsearch                 # Shows logs from the elasticsearch container. Use ctrl+c in order to exit
make logs-kibana                        # Shows logs from the kibana container. Use ctrl+c in order to exit

make drop-migrate                       # Drops databases and runs all migrations for the main/test databases
make migrate                            # Runs all migrations for the main/test databases
make migrate-no-test                    # Runs all migrations for the main database
make migrate-cron-jobs                  # Creates cron job tasks (cleanup logs, failed old messenger messages)

make fixtures                           # Runs all fixtures for test database without --append option (tables will be dropped and recreated)

make create-roles-groups                # Creates roles and groups

make messenger-setup-transports         # Initializes transports for Symfony Messenger bundle

make elastic-create-or-update-template  # Creates or updates elastic templates

make phpunit                            # Runs PhpUnit tests
make report-code-coverage               # Updates code coverage report on https://coveralls.io (COVERALLS_REPO_TOKEN should be set on CI side)

make ecs                                # Runs Easy Coding Standard tool
make ecs-fix                            # Runs Easy Coding Standard tool to fix issues
make phpcs                              # Runs PHP CodeSniffer
make phpmetrics                         # Generates PhpMetrics static analysis report
make phpcpd                             # Runs php copy/paste detector
make phpcpd-html-report                 # Generates phpcpd html report
make phpmd                              # Runs php mess detector
make phpstan                            # Runs PhpStan static analysis tool
make phpinsights                        # Runs Php Insights analysis tool

make composer-normalize                 # Normalizes composer.json file content
make composer-validate                  # Validates composer.json file content
make composer-require-checker           # Checks the defined dependencies against your code
make composer-unused                    # Shows unused packages by scanning and comparing package namespaces against your code
```

## Symfony container shell
Inside symfony container shell available "native" symfony commands with their description and, in additional, custom commands.
In order to enter inside symfony container shell please use next command on your local shell:
```bash
make ssh
```
After above command you will be inside symfony container and for display list of available commands please use next command:
```bash
./bin/console
```
#### Custom commands in symfony container shell
1.Help with user management:
```bash
./bin/console user:management           # Manage your users/user groups
./bin/console user:create               # Create user
./bin/console user:create-group         # Create user group
./bin/console user:create-roles         # Initialize user group roles
./bin/console user:create-roles-groups  # Initialize user groups and roles
./bin/console user:edit                 # Edit user
./bin/console user:edit-group           # Edit user group
./bin/console user:list                 # List current users
./bin/console user:list-groups          # List current user groups
./bin/console user:remove               # Remove user
./bin/console user:remove-group         # Remove user group
```
2.Help with api-key management:
```bash
./bin/console api-key:management      # Manage your API keys
./bin/console api-key:change-token    # Change API key token
./bin/console api-key:create          # Create API key
./bin/console api-key:edit            # Edit API key
./bin/console api-key:list            # List API keys
./bin/console api-key:remove          # Remove API key
```
3.Help with other things:
```bash
./bin/console check-dependencies                    # Check which vendor dependencies has updates
./bin/console db:wait                               # Waits for database availability (1 mins max)
./bin/console elastic:wait                          # Waits for elastic availability (2 mins max)
./bin/console utils:create-date-dimension-entities  # Create 'DateDimension' entities
./bin/console messenger:setup-transports            # Initializes transports for Symfony Messenger bundle
./bin/console logs:cleanup                          # Command to cleanup logs(log_login, log_request) in the database (runs by cron every day at 00-00)
./bin/console messenger:messages-cleanup            # Command to cleanup messenger_messages table (runs by cron every day at 00-00)
./bin/console elastic:create-or-update-template     # Command in order to create/update index template in Elastic
```
