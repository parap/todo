'use strict';

function highlightButton(number)
{
    document.getElementById('menu-button1').className = "btn";
    document.getElementById('menu-button2').className = "btn";
    document.getElementById('menu-button3').className = "btn";
    document.getElementById('menu-button' + number).className = "btn btn-primary";
}

var app = angular.module("todoApp", [
    "ngRoute", "ngCookies", "todoList", "statistic", "archive", "login", 
//    "AuthenticationService"
]);

app.config(config);

app.run(run);

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
            .otherwise({
                template: "<todo-list></todo-list>"
            });
}
;

run.$inject = ['$rootScope', '$location', '$cookieStore', '$http'];
function run($rootScope, $location, $cookieStore, $http) {
    // keep user logged in after page refresh
    $rootScope.globals = $cookieStore.get('globals') || {};
    if ($rootScope.globals.currentUser) {
        $http.defaults.headers.common['Authorization'] = 'Basic ' + $rootScope.globals.currentUser.authdata; // jshint ignore:line
    }

    $rootScope.$on('$locationChangeStart', function (event, next, current) {
        
        var allowedPage = $location.path() === '/login' || $location.path() === '/register';
        var loggedIn = $rootScope.globals.currentUser;
        
        if (!allowedPage && !loggedIn) {
            $location.path('/login');
        }
    });
}