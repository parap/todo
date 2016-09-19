'use strict';

var app = angular.module("todoApp", [
    "ngRoute", "todoList", "statistic"
]);

app.config(function($routeProvider) {
    $routeProvider
    .when("/statistic", {
        template: "<statistic></statistic>"
    })
    .when("/test", {
        template: "<br/>it is future Archive page"
    }).otherwise({
        template: "<todo-list></todo-list>"
    });
});