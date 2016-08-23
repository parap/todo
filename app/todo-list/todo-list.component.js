
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
                }
            ];

                $http.get("/fetch")
                        .then(function (response) {
                            $scope.todos = response.data;

                            for (var i = 0; i < $scope.todos.length; i++) {
                                $scope.todos[i].done = ($scope.todos[i].done === "1");
                            }
                        });

            $scope.remove = function (todo) {
                $scope.todos.splice(this.$index, 1);
                // AJAX request to remove
            };

            $scope.add = function(todo) {
                $scope.todos.push({name: todo, done: false});
                $scope.newTodo = '';
                // AJAX request to add
            };
            
            $scope.switch = function(todo) {
                //AJAX request to complete/uncomplete
            }
        }
    });