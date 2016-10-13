<?php

use Classes\Db;
use Classes\Routing;
use Classes\RequestM;

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();

new Db($user, $password, $host, $db);

$request = RequestM::createFromGlobals();
$routing = new Routing($request, $routes);
$controller = $routing->getControllerName();
$action = $routing->getActionName();
echo (new $controller)->$action($request);
