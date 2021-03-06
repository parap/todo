<?php

$routes = [
    'fetch'           => 'IndexController::fetch',
    'create'          => 'IndexController::create',
    'update'          => 'IndexController::update',
    'complete'        => 'IndexController::complete',
    'complete-next'   => 'IndexController::completeNext',
    'uncomplete-last' => 'IndexController::uncompleteLast',
    'remove'          => 'IndexController::remove',
    'archive'         => 'IndexController::archive',
    'unarchive'       => 'IndexController::unarchive',
    'fetch-archived'  => 'IndexController::fetchArchived',
    'set-date'        => 'IndexController::setDate',
    'statistic'       => 'StatisticController::index',
    'user/register'   => 'UserController::register',
    'user/login'      => 'UserController::login',
    ''                => 'IndexController::render',
];

$freeRoutes = ['user/register', 'user/login', ''];

$user     = 'user';
$password = 'password';
$host     = 'localhost';
$db       = 'db';
