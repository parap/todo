'use strict';

var app = angular.module("todoApp", [
    "ngRoute", "todoList", "statistic"
]);

app.config(function($routeProvider) {
    $routeProvider
    .when("/statistic", {
        template: "<statistic></statistic>"
//        controller: "StatisticController"
    })
    .when("/test", {
        template: "it is test, test-test-test"
    }).otherwise({
        template: "<todo-list></todo-list>"
    });
});