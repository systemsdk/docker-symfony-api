# API Key Management
This document describes how to use the API key functionality for application-to-application authentication using non-expiring API tokens.

## Overview
This environment includes a built-in API key system that allows you to manage API tokens with an unlimited lifetime and assign them the necessary roles. This is primarily used for seamless integration with external applications and third-party services.

The current implementation allows you to:
* Create an unlimited number of API tokens.
* Define custom roles and user groups.
* Assign API keys to specific user groups to strictly control access levels.

## Configuration
The application allows you to store API key tokens in the database in one of two formats: **Encrypted** or **Plain text** (Unencrypted).

You can configure this behavior in your `.env` file using the following parameters:

```env
###> Api-key options ###
API_KEY_TOKEN_OPEN_SSL_ENCRYPT=1
API_KEY_TOKEN_HASH_ALGO=sha256
###< Api-key options ###

###> Open ssl options ###
OPEN_SSL_ALGORITHM=aes-128-gcm
OPEN_SSL_KEY=systemsdk
###< Open ssl options ###
```

> ⚠️ Important Warning: Modifying any of these values after API keys have been generated will invalidate all existing tokens in the database. You will need to generate new ones.

Parameter Details:

* `API_KEY_TOKEN_OPEN_SSL_ENCRYPT`: Set to `1` to enable OpenSSL encryption for tokens in the database, or `0` to store them as plain text.
* `API_KEY_TOKEN_HASH_ALGO`: Specifies the hashing algorithm (e.g., md5, sha1, sha256, sha512). This is used when encryption is enabled.
* `OPEN_SSL_ALGORITHM`: The cipher method used for encryption (the default is `aes-128-gcm`).
* `OPEN_SSL_KEY`: The secret passphrase used for encryption. You must set a strong, unique value for this key in your production environment and store it securely.

## Architecture & Use Case Example
Consider a scenario where you need to expose two separate API endpoints for two different external applications: Application1 and Application2. To maintain strict security, Application1 must not be able to access the endpoint designated for Application2 and vice versa.

To support this level of granular access control, the system relies on the following database tables:

* `api_key`

   Stores the unique API tokens (`token`) and their descriptions. You can generate a new API token using the command: `./bin/console api-key:create`.
  
   **Tip:** All necessary default roles and user groups will be created automatically on the first run if they are missing.

* `role`

   Stores the unique `role` string (which acts as the primary key) and a detailed `description` of its permissions. You can extend the list of roles within the `App\Role\Domain\Enum\Role.php` class. To synchronize (create/delete) these roles with the database, use the command: `./bin/console user:create-roles`.

* `user_group`

   Stores user groups, consisting of a mandatory `name` and an associated `role` (allowing for flexible logical grouping without strict permissions). You can create additional groups using the command: `./bin/console user:create-group`.

* `api_key_has_user_group`

   A pivot table that establishes a many-to-many relationship. It uses a composite primary key linking the UUIDs (`api_key_id` and `user_group_id`) of API tokens and their assigned user groups. You can assign one or multiple groups (comma-separated) directly when creating a new token via the `api-key:create` command.

> Routing Note: All endpoints utilizing this API key functionality must be configured under the `^/api` URL prefix.

> Security Note: Following Symfony security standards, all roles defined in `App\Role\Domain\Enum\Role.php` must begin with the `ROLE_` prefix. For more details, refer to the [Symfony Security Documentation](https://symfony.com/doc/current/security.html#roles).

### Implementation Steps
Follow these steps to implement the scenario described above:

1. Create the API endpoint for Application1

   Configure the route and restrict access using the specific role for Application1.
   ```php
   #[Route(
       path: '/application1/endpoint1',
       name: 'api_to_api_application1_endpoint1',
       requirements: [
           'role' => new EnumRequirement(Role::class),
       ],
       methods: [Request::METHOD_GET],
   )]
   #[IsGranted(Role::APPLICATION1->value)]
   ```

2. Create the API endpoint for Application2

   Similarly, configure the route and restrict access using the specific role for Application2.
   ```php
   #[Route(
       path: '/application2/endpoint1',
       name: 'api_to_api_application2_endpoint1',
       requirements: [
           'role' => new EnumRequirement(Role::class),
       ],
       methods: [Request::METHOD_GET],
   )]
   #[IsGranted(Role::APPLICATION2->value)]
   ```

3. Define the Roles
   
   Extend the role list and add descriptions inside the `App\Role\Domain\Enum\Role.php` class (e.g., add `ROLE_APPLICATION1` and `ROLE_APPLICATION2`).

4. Synchronize Roles

   Run the following console command to sync your new roles with the database:
   ```bash
   ./bin/console user:create-roles
   ```

5. Create User Groups

   Run this command to create a distinct user group for each of the new roles:
   ```bash
   ./bin/console user:create-group
   ``` 

6. Generate API Keys

   Run the key generation command to create an API token for each application, assigning the appropriate user group to each token:
   ```bash
   ./bin/console api-key:create
   ``` 

7. Test the Authentication

   You can verify the created API key tokens by sending a request with the following header: `Authorization:ApiKey your_token_here`.

That's it! You can now use both tokens. The token assigned to Application1 will successfully authenticate against its specific endpoint but will receive an "Access Denied" response if it attempts to access the endpoint designated for Application2.

> ⚠️ Important Note: The system includes a built-in internal role called `ROLE_API`. All generated API keys are implicitly assigned this `ROLE_API` by default. This means that if an endpoint is protected strictly by `#[IsGranted(Role::API->value)]`, all existing valid API keys will be able to access it, regardless of which specific user groups are assigned to them.
