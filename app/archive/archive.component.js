(function () {
    'use strict';

    angular.
            module('todoList').
            component('archive', {
                templateUrl: 'app/archive/archive.template.html',
                controller: function ArchiveController($scope, $http, 
                AuthenticationService, $filter, $location) {

                    highlightButton('3');
                    
                    var email = AuthenticationService.GetUsername();                    

                    $scope.todos = [
                        {
                            name: 'For some reason server failed to load data, please reload page',
                            done: false
                        }
                    ];

                    $scope.day = 0;

                    $scope.fetch = function () {
                        $http.get("fetch-archived?day=" + $scope.day + "&email=" + email)
                                .then(function (response) {
                                    
                                    if ('string' === typeof(response.data)) {
                                        $location.path('/login');
                                        return;
                                    }
                                    
                                    $scope.todos = response.data;

                                    for (var i = 0; i < $scope.todos.length; i++) {
                                        $scope.todos[i].done = ("1" === $scope.todos[i].done);
                                    }
                                });
                    };
                    
                    $scope.remove = function (item) {
                        if (confirm($filter('translate')('IRREVERSIBLE'))) {
                            $scope.todos.splice(this.$index, 1);
                            $http.post("index.php?route=remove", {"id": item.id, "email": email});
                        }
                    };

                    $scope.unarchive = function (item) {
                        $scope.todos.splice(this.$index, 1);
                        $http.post("index.php?route=unarchive", {"id": item.id, "email": email});
                    };

                    $scope.fetch();
                }
            });
}());