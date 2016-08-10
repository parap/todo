'use strict';

// Define the `phonecatApp` module
var todoApp = angular.module('todoApp', []);

// Define the `PhoneListController` controller on the `phonecatApp` module
todoApp.controller('listController', function listController($scope) {
    $scope.todos = [
        {
            name: 'Nexus S',
            done: false
        }, {
            name: 'Motorola XOOM™ with Wi-Fi',
            done: true
        }, {
            name: 'MOTOROLA XOOM™',
            done: false
        }
    ];
});