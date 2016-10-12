(function () {
    'use strict';

    angular.
            module('todoList').
            component('archive', {
                templateUrl: 'app/archive/archive.template.html',
                controller: function ArchiveController($scope, $http) {

                    highlightButton('3');

                    $scope.todos = [
                        {
                            name: 'For some reason server failed to load data, please reload page',
                            done: false
                        }
                    ];

                    $scope.day = 0;

                    $scope.fetch = function () {
                        $http.get("/fetch-archived?day=" + $scope.day)
                                .then(function (response) {
                                    $scope.todos = response.data;

                                    for (var i = 0; i < $scope.todos.length; i++) {
                                        $scope.todos[i].done = ("1" === $scope.todos[i].done);
                                    }
                                });
                    };

                    $scope.remove = function (item) {
                        if (confirm('It is irreversible action. Please confirm you are going to delete the item forever')) {
                            $scope.todos.splice(this.$index, 1);
                            $http.post("/index.php?route=remove", {"id": item.id});
                        }
                    };

                    $scope.unarchive = function (item) {
                        $scope.todos.splice(this.$index, 1);
                        $http.post("/index.php?route=unarchive", {"id": item.id});
                    };

                    $scope.fetch();
                }
            });
}());