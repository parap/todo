(function () {
    'use strict';

    angular.
            module('menu').
            component('menu', {
                templateUrl: 'app/menu/menu.template.html',
                controller: function MenuController($scope, $rootScope,
                        AuthenticationService, $translate) {

                    $rootScope.$on('$locationChangeStart', function () {
                        $rootScope.logged = AuthenticationService.GetUsername();
                        $scope.logged = $rootScope.logged;
                    });

                    $rootScope.logged = AuthenticationService.GetUsername();
                    $scope.logged = $rootScope.logged;
                    
                    $scope.language = "undefined" === typeof($translate.use()) ? "ru" : $translate.use();
                    
                    $scope.changeLanguage = function (langKey) {
                        $translate.use(langKey);
                        $scope.language = langKey;
                    };
                }
            });
}());