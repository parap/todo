'use strict';

var app = angular.module("todoApp", [
    "ngRoute", "todoList", "statistic", "archive"
]);

app.config(function($routeProvider) {
    $routeProvider
    .when("/statistic", {
        template: "<statistic></statistic>"
    })
    .when("/archive", {
        template: "<archive></archive>"
    }).otherwise({
        template: "<todo-list></todo-list>"
    });
});