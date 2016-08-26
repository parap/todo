<?php

use Classes\Db;
use Classes\Routing;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/vendor/autoload.php';

new Db($user, $password, $host, $db);

$routing = new Routing($routes);
$controller = $routing->getControllerName();
$action = $routing->getActionName();
$request = Request::createFromGlobals();
echo (new $controller)->$action($request);
