<?php

return [
    'app_name' => getenv('APP_NAME'),
    'app_env' => getenv('APP_ENV'),
    'app_url' => getenv('APP_URL'),
    'app_debug' => getenv('APP_DEBUG'),

    'db_connection' => getenv('DB_CONNECTION'),
    'db_host' => getenv('DB_HOST'),
    'db_port' => getenv('DB_PORT'),
    'db_database' => getenv('DB_DATABASE'),
    'db_username' => getenv('DB_USERNAME'),
    'db_password' => getenv('DB_PASSWORD'),
];