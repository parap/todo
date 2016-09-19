
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
                
                $scope.day = 0;
                $scope.date = new Date();
                
                $scope.updateDate = function () {
                    var date = new Date();
                    date.setDate(date.getDate() + $scope.day);
                    $scope.date = date;
                }

                $scope.fetch = function () {
                    $http.get("/fetch?day=" + $scope.day)
                        .then(function (response) {
                            $scope.todos = response.data;

                            for (var i = 0; i < $scope.todos.length; i++) {
                                $scope.todos[i].done = ("1" === $scope.todos[i].done );
                            }
                        });
                }

                $scope.remove = function (item) {
                    $scope.todos.splice(this.$index, 1);
                    $http.post("/index.php?route=remove", {"id": item.id});
                };

                $scope.archive = function (item) {
                    $scope.todos.splice(this.$index, 1);
                    $http.post("/index.php?route=archive", {"id": item.id});
                };

                $scope.add = function (text, type) {
                    $scope.todos.push({name: text, done: false, type: type});
                    $scope.newTodo = '';
                    $scope.newDaily = '';

                    $http.post("/index.php?route=create", {"name": text, "type": type})
                            .then(function(response) {
                                $scope.fetch();
                    });
                };

                $scope.switch = function (item) {
                    $http.post("/index.php?route=complete", {"done": item.done, "id": item.id, "type": item.type, "day": $scope.day})
                            .then(function (response) {
                                item.delay = response.data;
                            });
                }

                $scope.update = function (item) {
                    $http.post("/index.php?route=update", {"name": item.name, "id": item.id, "type": item.type});
                }
                
                $scope.increaseDay = function () {
                    $scope.day++;
                    $scope.fetch();
                    $scope.updateDate();
                }
                
                $scope.decreaseDay = function () {
                    $scope.day--;
                    $scope.fetch();
                    $scope.updateDate();
                }

                $scope.today = function () {
                    $scope.day = 0;
                    $scope.fetch();
                    $scope.updateDate();
                }
                
                $scope.fetch();
            }
        });