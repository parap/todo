'use strict';

angular.
        module('todoList').
        component('archive', {
            templateUrl: 'app/archive/archive.template.html',
            controller: function ArchiveController($scope, $http) {

                $scope.todos = [
                    {
                        name: 'For some reason server failed to load data, please reload page',
                        done: false
                    }
                ];
                
                $scope.day = 0;
                $scope.date = new Date();
                
                $scope.fetch = function () {
                    $http.get("/fetch-archived?day=" + $scope.day)
                        .then(function (response) {
                            $scope.todos = response.data;

                            for (var i = 0; i < $scope.todos.length; i++) {
                                $scope.todos[i].done = ("1" === $scope.todos[i].done );
                            }
                        });
                }

                $scope.archive = function (item) {
                    $scope.todos.splice(this.$index, 1);
                    $http.post("/index.php?route=archive", {"id": item.id});
                };

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