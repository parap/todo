(function () {
    'use strict';

    var register = function ($scope, $http, $location, $timeout, AuthenticationService) {

        var vm = $scope;
        vm.register = register;

        vm.register = function () {
            vm.dataLoading = true;
            $http.post('user/register', vm.user)
                    .success(function (response) {
                        if (response.success) {
                            console.log('Registration successful');
                            $timeout(function () {
// TODO: extract to separate functionality this code duplicated from login.component.js .
// begin
                                AuthenticationService.Login(vm.user.email, vm.user.password, function (response) {
                                    if (response.success) {
                                        console.log('login successful');
                                        AuthenticationService.SetCredentials(vm.user.email, vm.user.password);
                                        $location.path('/');
                                    } else {
                                        console.log('failure! ' + response.message);
                                        vm.dataLoading = false;
                                    }
                                });
// end
                            });
                        } else {
                            console.log(response.message);
                            vm.dataLoading = false;
                        }
                    });
        };
    };

    angular
            .module('register')
            .component('register', {
                $inject: ['$scope', '$http', '$location', '$timeout', 'AuthenticationService'],
                templateUrl: 'app/auth/register.template.html',
                controller: register
            });
})();