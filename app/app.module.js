'use strict';

function highlightButton(number)
{
    document.getElementById('menu-button1').className = "btn";
    document.getElementById('menu-button2').className = "btn";
    document.getElementById('menu-button3').className = "btn";
    document.getElementById('menu-button' + number).className = "btn btn-primary";
}
    

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