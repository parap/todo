(function () {
    'use strict';

    angular.
            module('todoList').
            component('todoList', {
                templateUrl: 'app/todo-list/todo-list.template.html',
                controller: function ListController($scope, $http, AuthenticationService, 
                $location, $rootScope) {

                    highlightButton('1');

                    var params, email = AuthenticationService.GetUsername();

                    $scope.todos = [
                        {
                            name: 'For some reason server failed to load data, please reload page',
                            done: false
                        }
                    ];
                    
                    $scope.language = $rootScope.language;

                    
                    
// TEST CODE BEGINS
                    $scope.inlineOptions = {
                    };

                    $scope.dateOptions = {
                        formatYear: 'yy',
                        maxDate: new Date(2020, 5, 22),
                        minDate: new Date(),
                        showWeeks: false,
                        showButtonBar: false,
                        startingDay: 1
                    };
                    
                    $scope.toggleMin = function () {
                        $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
                        $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
                    };
                    
                    $scope.toggleMin();

                    $scope.format = ['dd-MMMM-yyyy'];
                    $scope.altInputFormats = ['M!/d!/yyyy'];
                    
                    $scope.popup = {};
                    $scope.open = function (index) {
                        $scope.popup[index] = {};
                        $scope.popup[index].opened = true;
                    };
                    
                    $scope.change = function (id, todo_at, index) {
                        var params = {'id': id, 'date': todo_at, 'email': email};
                        $http.post("index.php?route=set-date", params)
                                .then(function (response) {
                                    $scope.todos[index].time_left = $scope.convertTimeLeft(response.data);
                                });
                    };
                    
                    $scope.convertTimeLeft = function(timeLeft) {
                        if (timeLeft < 0) return 8;
                        if (timeLeft > 8) return 0;
                        return (8 - timeLeft);
                    }

                    $scope.day = 0;
                    $scope.date = new Date();

                    $scope.updateDate = function () {
                        var date = new Date();
                        date.setDate(date.getDate() + $scope.day);
                        $scope.date = date;
                    };

                    $scope.fetch = function () {
                        $http.get("fetch?day=" + $scope.day + "&email=" + email)
                                .then(function (response) {
                                    if ('string' === typeof(response.data)) {
                                        $location.path('/login');
                                        return;
                                    }

                                    $scope.todos = response.data;

                                    for (var i = 0; i < $scope.todos.length; i++) {
                                        $scope.todos[i].done = ("1" === $scope.todos[i].done);
                                    }
                                });
                    };

                    $scope.archive = function (item) {
                        $scope.todos.splice(this.$index, 1);
                        params = {"id": item.id, "email": email};
                        $http.post("index.php?route=archive", params);
                    };

                    $scope.add = function (text, type) {
                        $scope.todos.push({name: text, done: false, type: type});
                        $scope.newTodo = '';
                        $scope.newDaily = '';
                        params = {"name": text, "type": type, "email": email};

                        $http.post("index.php?route=create", params)
                                .then(function (response) {
                                    $scope.fetch();
                                });
                    };

                    $scope.switch = function (item) {
                        params = {"done": item.done, "id": item.id,
                            "type": item.type, "day": $scope.day, "email": email};
                        $http.post("index.php?route=complete", params)
                                .then(function (response) {
                                    item.delay = response.data;
                                });
                    };

                    $scope.update = function (item) {
                        params = {"name": item.name, "id": item.id,
                            "type": item.type, "email": email};
                        $http.post("index.php?route=update", params);
                    };

                    $scope.increaseDay = function () {
                        $scope.day++;
                        $scope.fetch();
                        $scope.updateDate();
                    };

                    $scope.decreaseDay = function () {
                        $scope.day--;
                        $scope.fetch();
                        $scope.updateDate();
                    };

                    $scope.today = function () {
                        $scope.day = 0;
                        $scope.fetch();
                        $scope.updateDate();
                    };

                    $scope.fetch();
                }
            });
}());