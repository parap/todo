
'use strict';

angular.
        module('statistic').
        component('statistic', {
            templateUrl: 'app/statistic/statistic.template.html',
            controller: function StatisticController($scope, $http) {
                var x = new Date();
                $scope.weekLength = x.getDay();
                $scope.monthLength = x.getDate();
                
                $scope.fetch = function () {
                    $http.get("/statistic")
                            .then(function (response) {
                                $scope.items = response.data;
                            });
                }
                
                $scope.fetch();
            }
        });