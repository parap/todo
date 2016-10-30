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
    "ngRoute", "ngCookies", "todoList", "statistic", "archive", 
    "login", "register", "menu", "logout", "pascalprecht.translate"
])
        .config(configRoute)
        .config(configTranslate)
        .run(run);

configTranslate.$inject = ['$translateProvider'];
function configTranslate($translateProvider) {
    $translateProvider.translations('en', {
    MAIN: 'Main',
    STATISTIC: 'Statistic',
    ARCHIVE: 'Archive',
    LOGOUT: 'Logout',
    LOGIN: 'Logout'
  })
  .translations('ru', {
    MAIN: 'Главная',
    STATISTIC: 'Статистика',
    ARCHIVE: 'Архив',
    LOGOUT: 'Выйти',
    LOGIN: 'Войти'
  });
  
  $translateProvider.preferredLanguage('ru');
}

configRoute.$inject = ['$routeProvider'];
function configRoute($routeProvider) {
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
            .when('/logout', {
                template: "<logout></logout>"
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