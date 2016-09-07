
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

                $scope.add = function (text, type) {
                    $scope.todos.push({name: text, done: false, type: type});
                    $scope.newTodo = '';

                    $http.post("/index.php?route=create", {"name": text, "type": type});

                };

                $scope.switch = function (item) {
                    $http.post("/index.php?route=complete", {"done": item.done, "id": item.id});
                }

                $scope.update = function (item) {
                    $http.post("/index.php?route=update", {"name": item.name, "id": item.id});
                }
            }
        });