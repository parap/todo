
'use strict';

angular.
    module('todoList').
    component('todoList', {
        templateUrl: 'app/todo-list/todo-list.template.html',
        controller: function ListController($scope, $http) {
            $scope.todos = [
                {
                    name: 'For some reason server failed to load data, please reload page',
                    done: false
                }, {
                    name: 'Sample two',
                    done: true
                }, {
                    name: 'Sample three',
                    done: false
                }
            ];

            $http.get("today.php")
                .then(function(response) {
                    $scope.todos = response.data;
                });

            $scope.remove = function (todo) {
                $scope.todos.splice(this.$index, 1);
            };

            $scope.add = function(todo) {
                $scope.todos.push({name: todo, done: false});
                $scope.newTodo = '';
            };
        }
    });