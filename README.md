# Symfony7-template

Commands:

- `composer install`
- `php bin/console l:j:generate-keypair`
- `php bin/console doctrine:database:create --if-not-exists`
- `php bin/console doctrine:schema:drop --force`
- `php bin/console d:s:u --force --complete`
- `php bin/console cache:clear`


Files ( complete / copy )
```
# .env
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
```

```
# config/packages/lexik_jwt_authentication.yaml
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%' # required for token creation
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%' # required for token verification
    pass_phrase: '%env(JWT_PASSPHRASE)%' # required for token creation
    token_ttl: 3600 # in seconds, default is 3600
```
