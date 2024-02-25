<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

$_SERVER['APP_ENV'] = 'test';

passthru(sprintf('php bin/console doctrine:database:create --if-not-exists --env=test'));
passthru(sprintf('php bin/console doctrine:schema:drop --force --env=test'));
passthru(sprintf('php bin/console d:s:u --force --complete --env=test'));
passthru(sprintf('php bin/console cache:clear --env=test'));

