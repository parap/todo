'use strict';

var login = {
    $inject: ['$scope', '$location', 'AuthenticationService'],
    templateUrl: 'app/auth/login.template.html',
    controller: function login($scope, $location, AuthenticationService) {
        var vm = this;
        vm.login = login;

        (function initController() {
            // reset login status
            AuthenticationService.ClearCredentials();
        })();

        function login() {
            vm.dataLoading = true;
            AuthenticationService.Login(vm.username, vm.password, function (response) {
                if (response.success) {
                    AuthenticationService.SetCredentials(vm.username, vm.password);
                    $location.path('/');
                } else {
                    alert(response.message);
                    vm.dataLoading = false;
                }
            });
        }
        ;
    }
}
;


angular
//        .module('todoApp')
        .module('login')
        .component('login', login);
