(function () {
    'use strict';

    var login = function ($scope, $location, AuthenticationService) {
        var vm = $scope;

        (function initController() {
            // reset login status
            AuthenticationService.ClearCredentials();
        })();

        vm.login = function () {

            vm.dataLoading = true;

            AuthenticationService.Login(vm.email, vm.password, function (response) {
                if (response.success) {
                    console.log('login successful');
                    AuthenticationService.SetCredentials(vm.email, vm.password);
                    $location.path('/');
                } else {
                    console.log('failure! ' + response.message);
                    vm.dataLoading = false;
                }
            });
        };
    };

    angular
            .module('login')
            .component('login', {
                $inject: ['$scope', '$location', 'AuthenticationService'],
                templateUrl: 'app/auth/login.template.html',
                controller: login
            });

})();