<?php

use Classes\Db;
use Classes\Routing;

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/vendor/autoload.php';

new Db($user, $password, $host, $db);

$routing = new Routing($routes);
$controller = $routing->getControllerName();
$action = $routing->getActionName();
echo (new $controller)->$action();
