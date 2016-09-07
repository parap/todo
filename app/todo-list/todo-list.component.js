
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

                $scope.remove = function (item) {
                    $scope.todos.splice(this.$index, 1);
                    $http.post("/index.php?route=remove", {"id": item.id});
                };

                $scope.add = function (text) {
                    $scope.todos.push({name: text, done: false});
                    $scope.newTodo = '';

                    $http.post("/index.php?route=create", {"name": text});

                };

                $scope.switch = function (item) {
                    $http.post("/index.php?route=complete", {"done": item.done, "id": item.id});
                }

                $scope.update = function (todo) {
                    // AJAX request to update item
                }
            }
        });