(function () {
    'use strict';

    var logout = function (AuthenticationService, $location) {
        AuthenticationService.ClearCredentials();
        $location.path('/login');
    };

    angular
            .module('logout')
            .component('logout', {
                $inject: ['AuthenticationService', '$location'],
                controller: logout
            });

})();