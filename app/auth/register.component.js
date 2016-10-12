(function () {
    'use strict';

    var register = function ($scope, $http, $location, $timeout) {

        var vm = $scope;
        vm.register = register;

        vm.register = function () {
            vm.dataLoading = true;
            $http.post('/user/register', vm.user)
                    .success(function (response) {
                        if (response.success) {
//                            alert('Registration successful');
                            $timeout(function() {
                                $location.path('/login')
                            });
                        } else {
//                            alert(response.message);
                            vm.dataLoading = false;
                        }
                    });
        };
    };

    angular
            .module('register')
            .component('register', {
                $inject: ['$scope', '$http', '$location', '$timeout'],
                templateUrl: 'app/auth/register.template.html',
                controller: register
            });
})();