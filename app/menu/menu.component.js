(function () {
    'use strict';

    angular.
            module('menu').
            component('menu', {
                templateUrl: 'app/menu/menu.template.html',
                controller: function MenuController($scope, AuthenticationService) {
                    $scope.logged = AuthenticationService.GetUsername();
                }
            });
}());