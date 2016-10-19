(function () {
    'use strict';

    angular.
            module('menu').
            component('menu', {
                templateUrl: 'app/menu/menu.template.html',
                controller: function MenuController($scope, $rootScope, AuthenticationService) {
                    
                    $rootScope.$on('$locationChangeStart', function () {
                        $rootScope.logged = AuthenticationService.GetUsername();
                        $scope.logged = $rootScope.logged;
                    });
                    
                    $rootScope.logged = AuthenticationService.GetUsername();
                    $scope.logged = $rootScope.logged;
                }
            });
}());