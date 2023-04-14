# ArthemConfigFileBundle

## Motivations

In Symfony configuration, you can inject file content as parameter using `env(file:FOO)`

But some Symfony bundles expect you to provide file path whereas your infrastructure can only provide env variables.
In some Docker deployments, it is really complicated to mount files and it's easier to deal with env.

This bundle generates files in the cache with your env var written in them, allowing you to write templates.

Let's see a Docker case!

```yaml
# docker-compose.yaml
services:
  symfony-app:
    environments:
    - JWT_PRIVATE_KEY
```

The default env var of docker compose can look like:
```dotenv
# .env
JWT_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCN/VTNk1Sdr9mg
...
tDMMHT4ch+pSmyyndMSeu+I+pjF5+cour3FrmmzZFzKivzj0EXbcb+LPUQKBgHWA
WRv4Q33WAa3BFKqCZOKhfFY=
-----END PRIVATE KEY-----
"
```

You have a `jwt_provider` bundle that requires a `private_key_file` (yes, a file path!)
```yaml
# config/packages/jwt_provider.yaml
    jwt_provider:
        private_key_file: "I don't have file, just a JWT_PRIVATE_KEY env var"
```

This is where the bundle helps you!

## Installation

```bash
composer require arthem/config-file-bundle
```

## Usage

### Inlined configuration

You can directly write the expected content of your file with the following syntax:

```yaml
param_value: |
    &file:<filename.ext>
    <content of your file>
```

example:

```yaml
# config/packages/jwt_provider.yaml
    jwt_provider:
        private_key_file: |
            &file:jwt_private_key.pem
            %env(JWT_PRIVATE_KEY)%
```

this will:

- Generate the file `jwt_private_key-%hash%.pem` in the project cache directory. (`%hash%` refers to the resolved content of the file)
- Compile your configuration into:

```yaml
# config/packages/jwt_provider.yaml
    jwt_provider:
        private_key_file: 'cache/dir/jwt_private_key-c131d7b5c6bd917a83e0cbea6296bf95.pem'
```

### Provisioned configuration

The other way to generate files is to define them in the bundle extension config:

```yaml
# config/packages/arthem_config_file.yaml
arthem_config_file:
    files:
        service_account:
            extension: json
            content: |
                {
                  "type": "service_account",
                  "project_id": "my-project",
                  "private_key_id": "%env(json_string:SERVICE_ACCOUNT_ID)%",
                  "private_key": "%env(json_string:SERVICE_ACCOUNT_PRIVATE_KEY)%"
                }
        jwt_private_key:
            extension: pem
            content: '%env(JWT_SECRET_KEY)%'
        jwt_public_key:
            extension: pem
            content: '%env(JWT_PUBLIC_KEY)%'
```

Then inject files in your configuration:

```yaml
# config/packages/jwt_provider.yaml
jwt_provider:
    private_key_file: '%arthem_config_file.file.jwt_public_key%'
```

## File content

### Escaping values

This bundle also provides its JSON [env var processor](https://symfony.com/doc/current/configuration/env_var_processors.html)

```json
{
  "private_key_id": "%env(json_string:SERVICE_ACCOUNT_PRIVATE_KEY)%"
}
```

This processor escapes JSON strings which can be useful for multiline env vars.
