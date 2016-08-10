
'use strict';

angular.
    module('todoList').
    component('todoList', {
        templateUrl: 'app/todo-list/todo-list.template.html',
        controller: function ListController($scope) {
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

            $scope.remove = function (todo) {
                $scope.todos.splice(this.$index, 1);
            }
        }
    });