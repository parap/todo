(function () {
    'use strict';

    angular
            .module('registerx')
            .component('registerx', {
                $inject: ['$scope', '$http', '$location'],
                templateUrl: 'app/auth/register.template.html',
                controller: registerx
            });

    var registerx = function ($scope, $http, $location) {

        var vm = $scope;

        vm.registerx = registerx;
        $scope.count = 5;

        alert('outside');

        vm.pingz = function () {
            vm.count = vm.count - 5;
            alert('ping!');
        };


        vm.registerx = function () {
            alert('register');
            vm.dataLoading = true;
            $http.post('/user/register', vm.user)
                    .then(function (response) {
                        if (response.success) {
                            alert('Registration successful');
                            $location.path('/login');
                        } else {
                            alert(response.message);
                            vm.dataLoading = false;
                        }
                    });
        }
    };

})();