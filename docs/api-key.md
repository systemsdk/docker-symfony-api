# API key

This document describing how you can use api key functionality for the application to application authentication using api key token with unlimited lifetime.

## Basics

This environment comes with api-key functionality that allows you to manage api key tokens with unlimited lifetime and assign necessary roles. It is necessary in general when you need to integrate this application with other applications.

Existing functionality allow you to create unlimited number of tokens, create roles, create user groups, assign api key for selected user group/groups.

## Configuration

This environment allows you to save api key tokens inside db in two ways:
 - encrypted
 - native

Please use next configuration params inside .env file for defining mentioned above things:
```bash
API_KEY_TOKEN_OPEN_SSL_ENCRYPT=1
API_KEY_TOKEN_HASH_ALGO=sha256

OPEN_SSL_ALGORITHM=aes-128-gcm
OPEN_SSL_KEY=systemsdk
```

## Details and examples

Let's imagine you need to create 2 api endpoints that will be available for external application "A" and "B". External application "A" should not have access for the endpoint that designed for the external application "B".

We have next tables for such functionality: `api_key`, `role`. `user_group`, `api_key_has_user_group`.

`api_key` - table with tokens list and their description. You can create new api key token using console command `./bin/console api-key:create`. All necessary roles, user groups will be created automatically if missing for the first run.

`role` - table with roles and their description. You can extend list inside class `App\Role\Domain\Enum\Role.php`. You can sync(create/delete) them using console command `./bin/console user:create-roles`.

`user_group` - table with user groups and their role, group name. You can create additional groups using console command `./bin/console user:create-group`.

`api_key_has_user_group` - table with api key tokens and assigned user groups. You are able to assign user group/groups while creating api key token using above console command (please use comma for assigning multiple user groups).

Note 1: All endpoints for this functionality should be implemented under URL `^/api`.

Note 2: All roles inside `App\Role\Domain\Enum\Role.php` should start with `ROLE_...` [docs](https://symfony.com/doc/current/security.html#roles).

### Steps:

1.Create 1 api endpoint for the external application "A":

```bash
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

2.Create 1 api endpoint for the external application "B":

```bash
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

3.Extend roles list with their description inside `App\Role\Domain\Enum\Role.php`.

4.Run console command in order to sync roles with database `./bin/console user:create-roles`.

5.Run console command in order to create user group for the new role `./bin/console user:create-group`.

6.Run console command in order to create api keys for every application `./bin/console api-key:create`.

7.You can check created api key token using next header param: `Authorization:ApiKey your_token_here`.

That's it. You can use both tokens. Token for the external application "A" will not have access to the endpoint for external application "B".

Note: Please note that existing functionality has internal role `ROLE_API`. All api keys has "assigned" role `ROLE_API` by default. It means that if endpoint has `#[IsGranted(Role::API->value)]`, all existing api keys will be able to access it even without assigned user group.
