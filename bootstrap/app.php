<?php

use DI\Container;
use Dotenv\Dotenv;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Validation\Validator;
use Respect\Validation\Validator as v;
use Slim\App;
use Slim\Views\Twig;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

$neededExtensions = [];

if (!areExtensionsEnabled($neededExtensions))
    exit(0);

define('BASE_PATH', __DIR__ . '/../');
Dotenv::create(BASE_PATH)->load();
$config = include(BASE_PATH . '/config/config.php');
define('APP_ENV', $config['app_env']);
define('APP_URL', $config['app_url']);

$app = new App([
    'settings' => [
        'displayErrorDetails' => $config['app_debug'] === 'true' ? true : false,
        'app' => [
            'name' => $config['app_name'],
            'url' => $config['app_url'],
            'env' => $config['app_env'],
        ],
        'db' => [
            'driver' => 'mysql',
            'host' => $config['db_host'],
            'database' => $config['db_database'],
            'username' => $config['db_username'],
            'password' => $config['db_password'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]
    ],
]);

$container = $app->getContainer();

// setup illuminate (Model generator)
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['validator'] = function ($container) {
    return new Validator;
};

// add Illuminate package
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// add views to the application
$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);

    $view->addExtension(new Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$container['HomeController'] = function ($container) {
    return new HomeController($container);
};
$container['UserController'] = function ($container) {
    return new UserController($container);
};

// setup custom rules
v::with('App\\Validation\\Rules\\');

require __DIR__ . '/../app/routes.php';






