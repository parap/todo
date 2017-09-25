(function () {
    'use strict';

    angular.
            module('statistic').
            component('statistic', {
                templateUrl: 'app/statistic/statistic.template.html',
                controller: function StatisticController($scope, $http, 
                AuthenticationService, $location) {

                    highlightButton('2');

                    var date = new Date();
                    $scope.weekLength = (0 === date.getDay() ? 7 : date.getDay());
                    $scope.monthLength = date.getDate();
                    $scope.date = date;

                    $scope.fetch = function () {
                        $http.get("statistic?email=" + AuthenticationService.GetUsername())
                                .then(function (response) {
                                    
                                    if ('string' === typeof(response.data)) {
                                        $location.path('/login');
                                        return;
                                    }
                                    
                                    $scope.items = response.data;
                                });
                    };

                    $scope.fetch();
                }
            });
}());