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
                    
                    // FIXME: $translate.use() shouldn't be undefined!
                    $rootScope.language = $scope.language = "undefined" === typeof($translate.use()) ? $rootScope.baseLanguage : $translate.use();
                    
                    $scope.changeLanguage = function (langKey) {
                        $translate.use(langKey);
                        $rootScope.language = $scope.language = langKey;
                    };
                }
            });
}());