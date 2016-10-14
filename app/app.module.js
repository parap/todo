//(function(){

'use strict';

var highlightButton = function (number)
{
    document.getElementById('menu-button1').className = "btn";
    document.getElementById('menu-button2').className = "btn";
    document.getElementById('menu-button3').className = "btn";
    document.getElementById('menu-button' + number).className = "btn btn-primary";
};

angular.module("todoApp", [
    "ngRoute", "ngCookies", "todoList", "statistic", "archive", "login", "register", "menu"
])
        .config(config)
        .run(run);

config.$inject = ['$routeProvider'];
function config($routeProvider) {
    $routeProvider
            .when("/statistic", {
                template: "<statistic></statistic>"
            })
            .when("/archive", {
                template: "<archive></archive>"
            })
            .when('/login', {
                template: "<login></login>"
            })
            .when('/register', {
                template: "<register></register>"
            })
            .otherwise({
                template: "<todo-list></todo-list>"
            });
}


run.$inject = ['$rootScope', '$location', '$cookieStore', '$http'];
function run($rootScope, $location, $cookieStore, $http) {
    // keep user logged in after page refresh
    $rootScope.globals = $cookieStore.get('globals') || {};
    if ($rootScope.globals.currentUser) {
        $http.defaults.headers.common['Authorization'] = 'Basic ' + // jshint ignore:line
                $rootScope.globals.currentUser.authdata;
    }

    $rootScope.$on('$locationChangeStart', function (event, next, current) {

        var allowedPage = ($location.path() === '/login' ||
                $location.path() === '/register');
        var loggedIn = $rootScope.globals.currentUser;

        if (!allowedPage && !loggedIn) {
            $location.path('/login');
        }
    });
}

//}());