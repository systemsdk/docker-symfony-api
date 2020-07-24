# Commands
This document describing commands that can be used in local shell or inside symfony container shell.

## Local shell (Makefile)
This environment comes with "Makefile" and it allow to simplify using some functionality.
In order to use command listed bellow just use next syntax in your local shell: `make {command name}`.
Next commands available for this environment:
```bash
make build                              # Build dev environment
make build-test                         # Build test or continuous integration environment
make build-staging                      # Build staging environment
make build-prod                         # Build prod environment

make start                              # Start dev environment
make start-test                         # Start test or continuous integration environment
make start-staging                      # Start staging environment
make start-prod                         # Start prod environment

make stop                               # Stop dev environment
make stop-test                          # Stop test or continuous integration environment
make stop-staging                       # Stop staging environment
make stop-prod                          # Stop prod environment

make restart                            # Stop and start dev environment
make restart-test                       # Stop and start test or continuous integration environment
make restart-staging                    # Stop and start staging environment
make restart-prod                       # Stop and start prod environment

make env-staging                        # Create cached config file .env.local.php (usually for staging environment)
make env-prod                           # Create cached config file .env.local.php (usually for prod environment)

generate-jwt-keys                       # Generates JWT auth keys

make ssh                                # Enter symfony container shell
make ssh-nginx                          # Enter nginx container shell
make ssh-supervisord                    # Enter supervisord container shell (cron jobs running there, etc...)
make ssh-mysql                          # Enter mysql container shell
make ssh-rabbitmq                       # Enter rabbitmq container shell
make ssh-elasticsearch                  # Enter elasticsearch container shell
make ssh-kibana                         # Enter kibana container shell

make exec                               # Exucute some command defined in cmd="..." variable inside symfony container shell
make exec-bash                          # Execute several commands defined in cmd="..." variable inside symfony container shell

make report-prepare                     # Create /reports/coverage folder, will be used for report after running tests
make report-clean                       # Delete all reports in /reports/ folder

make wait-for-db                        # Checking MySQL database availability, using for CI (f.e. /.circleci folder)
make wait-for-elastic                   # Checking Elastic availability, using for CI (f.e. /.circleci folder)

make composer-install-no-dev            # Installing composer dependencies for prod environment (without dev tools)
make composer-install                   # Installing composer dependencies for dev environment
make composer-update                    # Update composer dependencies

make info                               # Display information about symfony version and php version

make logs                               # Display logs for symfony container. Use ctrl+c in order to exit
make logs-nginx                         # Display logs for nginx container. Use ctrl+c in order to exit
make logs-supervisord                   # Display logs for supervisord container. Use ctrl+c in order to exit
make logs-mysql                         # Display logs for mysql container. Use ctrl+c in order to exit
make logs-rabbitmq                      # Display logs for rabbitmq container. Use ctrl+c in order to exit
make logs-elasticsearch                 # Display logs for elasticsearch container. Use ctrl+c in order to exit
make logs-kibana                        # Display logs for kibana container. Use ctrl+c in order to exit

make drop-migrate                       # Drop databases (main and for tests) and run all migrations
make migrate                            # Run all migrations for databases (main and for tests)
make migrate-no-test                    # Run all migrations for main database
make migrate-cron-jobs                  # Create cron job tasks (cleanup logs)

make fixtures                           # Run all fixtures for test database without --append option (tables will be dropped and recreated)

make create-roles-groups                # Initialize user groups and roles

make messenger-setup-transports         # Initialize transports for Messenger component

make elastic-create-or-update-template  # Create/update index template in Elastic

make phpunit                            # Run all tests
make report-code-coverage               # Update code coverage report on https://coveralls.io (COVERALLS_REPO_TOKEN should be set on CI side)

make phpcs                              # Run PHP CodeSniffer
make ecs                                # Run The Easiest Way to Use Any Coding Standard
make ecs-fix                            # Run The Easiest Way to Use Any Coding Standard to fix issues
phpmetrics                              # Generates PhpMetrics static analysis
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
./bin/console messenger:setup-transports            # Initialize transports for Messenger component
./bin/console logs:cleanup                          # Command to cleanup logs(log_login, log_request) in the database
./bin/console elastic:create-or-update-template     # Command in order to create/update index template in Elastic
```
