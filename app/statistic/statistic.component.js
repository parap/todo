
'use strict';

angular.
        module('statistic').
        component('statistic', {
            templateUrl: 'app/statistic/statistic.template.html',
            controller: function StatisticController($scope, $http) {
                var date = new Date();
                $scope.weekLength = date.getDay();
                $scope.monthLength = date.getDate();
                $scope.date = date;
                
                $scope.fetch = function () {
                    $http.get("/statistic")
                            .then(function (response) {
                                $scope.items = response.data;
                            });
                }
                
                $scope.fetch();
            }
        });