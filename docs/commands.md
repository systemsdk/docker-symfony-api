# Commands
This document describes the primary commands available in your local terminal to interact with the environment and the internal Symfony container.

## Local Shell (Makefile)
This environment includes a `Makefile` designed to simplify common tasks and workflows. To execute any of the commands listed below, use the following syntax in your local terminal: `make {command_name}`.

Here is the complete list of available commands:
```bash
make help                               # Show available commands and their descriptions

make build                              # Build the development environment
make build-test                         # Build the test or CI environment
make build-staging                      # Build the staging environment
make build-prod                         # Build the production environment

make start                              # Start the development environment
make start-test                         # Start the test or CI environment
make start-staging                      # Start the staging environment
make start-prod                         # Start the production environment

make stop                               # Stop the development environment containers (without removing them)
make stop-test                          # Stop the test or CI environment containers (without removing them)
make stop-staging                       # Stop the staging environment containers (without removing them)
make stop-prod                          # Stop the production environment containers (without removing them)

make down                               # Stop and remove the development environment containers and networks
make down-test                          # Stop and remove the test or CI environment containers and networks
make down-staging                       # Stop and remove the staging environment containers and networks
make down-prod                          # Stop and remove the production environment containers and networks

make restart                            # Restart the development environment
make restart-test                       # Restart the test or CI environment
make restart-staging                    # Restart the staging environment
make restart-prod                       # Restart the production environment

make env-staging                        # Create cached config file .env.local.php (for staging)
make env-prod                           # Create cached config file .env.local.php (for production)

make generate-jwt-keys                  # Generate RSA keys for JWT authentication

make ssh                                # Access the bash shell inside the symfony container
make ssh-root                           # Access the bash shell as root inside the symfony container
make fish                               # Access the fish shell inside the symfony container (https://www.youtube.com/watch?v=C2a7jJTh3kU)
make ssh-nginx                          # Access the bash shell inside the nginx container
make ssh-supervisord                    # Access the bash shell inside the supervisord container (cron jobs, etc.)
make ssh-mysql                          # Access the bash shell inside the mysql container
make ssh-rabbitmq                       # Access the bash shell inside the rabbitmq container
make ssh-elasticsearch                  # Access the bash shell inside the elasticsearch container
make ssh-kibana                         # Access the bash shell inside the kibana container

make exec                               # Execute a command (defined in cmd="...") as www-data user inside the symfony container
make exec-bash                          # Execute multiple commands (defined in cmd="...") as www-data user inside the symfony container
make exec-by-root                       # Execute a command (defined in cmd="...") as root user inside the symfony container

make report-prepare                     # Create the /reports/coverage folder (used for test reports)
make report-clean                       # Remove all generated reports in the /reports/ folder

make wait-for-db                        # Check MySQL database availability (useful for CI/CD, e.g. /.circleci)
make wait-for-elastic                   # Check Elasticsearch availability (useful for CI/CD, e.g. /.circleci folder)

make composer-install-no-dev            # Install Composer dependencies (excluding dev packages)
make composer-install                   # Install all Composer dependencies
make composer-update                    # Update Composer dependencies
make composer-audit                     # Check installed packages for security vulnerabilities

make info                               # Show the current PHP and Symfony versions

make logs                               # View logs from the symfony container (use ctrl+c to exit)
make logs-nginx                         # View logs from the nginx container (use ctrl+c to exit)
make logs-supervisord                   # View logs from the supervisord container (use ctrl+c to exit)
make logs-mysql                         # View logs from the mysql container (use ctrl+c to exit)
make logs-rabbitmq                      # View logs from the rabbitmq container (use ctrl+c to exit)
make logs-elasticsearch                 # View logs from the elasticsearch container (use ctrl+c to exit)
make logs-kibana                        # View logs from the kibana container (use ctrl+c to exit)
make logs-redis                         # View logs from the redis container (use ctrl+c to exit)

make drop-migrate                       # Drop databases and run all migrations for main/test databases
make migrate                            # Run all migrations for main/test databases
make migrate-no-test                    # Run all migrations for the main database only
make migrate-cron-jobs                  # Create cron job tasks (cleanup logs, failed messenger messages)

make fixtures                           # Run fixtures for the test database, without --append option (tables are dropped and recreated)

make create-roles-groups                # Initialize default user roles and groups

make messenger-setup-transports         # Initialize transports for the Symfony Messenger component

make elastic-create-or-update-template  # Create or update Elasticsearch templates

make phpunit                            # Run the PHPUnit test suite
make report-code-coverage               # Update code coverage report on Coveralls.io (requires COVERALLS_REPO_TOKEN, should be set on CI side)

make ecs                                # Run Easy Coding Standard (ECS) checks
make ecs-fix                            # Run Easy Coding Standard to automatically fix issues
make phpcs                              # Run PHP CodeSniffer checks
make phpmetrics                         # Generate a PhpMetrics static analysis report
make phpcpd                             # Run PHP Copy/Paste Detector
make phpcpd-html-report                 # Generate an HTML report for PHP Copy/Paste Detector
make phpmd                              # Run PHP Mess Detector
make phpstan                            # Run PHPStan static analysis
make phpinsights                        # Run PHP Insights analysis

make composer-normalize                 # Normalize the composer.json file structure
make composer-validate                  # Validate the composer.json file syntax
make composer-require-checker           # Check defined dependencies against actual code usage
make composer-unused                    # Detect unused Composer packages by scanning namespaces
```

## Symfony Container Shell
Inside the Symfony container, you have access to both native Symfony commands and custom application commands.

To access the Symfony container shell, run the following command in your local terminal:
```bash
make ssh
```
Once inside the container, you can display the full list of available commands by running:
```bash
./bin/console
```
#### Custom Application Commands
1. User Management
   ```bash
   ./bin/console user:management           # Manage users and user groups
   ./bin/console user:create               # Create a new user
   ./bin/console user:create-group         # Create a new user group
   ./bin/console user:create-roles         # Initialize user roles
   ./bin/console user:create-roles-groups  # Initialize default user roles and groups
   ./bin/console user:edit                 # Edit an existing user
   ./bin/console user:edit-group           # Edit an existing user group
   ./bin/console user:list                 # List all current users
   ./bin/console user:list-groups          # List all current user groups
   ./bin/console user:remove               # Remove a user
   ./bin/console user:remove-group         # Remove a user group
   ```
2. API Key Management
   ```bash
   ./bin/console api-key:management      # Manage API keys
   ./bin/console api-key:change-token    # Change an API key token string
   ./bin/console api-key:create          # Create a new API key
   ./bin/console api-key:edit            # Edit an existing API key
   ./bin/console api-key:list            # List all API keys
   ./bin/console api-key:remove          # Remove an API key
   ```
3. System Utilities & Maintenance
   ```bash
   ./bin/console check-dependencies                    # Check vendor dependencies for available updates
   ./bin/console db:wait                               # Wait for database availability (up to 1 minute)
   ./bin/console elastic:wait                          # Wait for Elasticsearch availability (up to 2 minutes)
   ./bin/console utils:create-date-dimension-entities  # Create 'DateDimension' entities
   ./bin/console messenger:setup-transports            # Initialize transports for the Symfony Messenger component
   ./bin/console logs:cleanup                          # Clean up logs (log_login, log_request) in the database (runs via cron daily at 00:00)
   ./bin/console messenger:messages-cleanup            # Clean up messenger_messages table (runs via cron daily at 00:00)
   ./bin/console elastic:create-or-update-template     # Create or update index templates in Elasticsearch
   ```
