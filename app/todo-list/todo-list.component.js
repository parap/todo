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
                    // 
// TEST CODE BEGINS
//                    $scope.today = function () {
//                        $scope.dt = new Date();
//                    };
//                    $scope.today();
//
//                    $scope.clear = function () {
//                        $scope.dt = null;
//                        $scope.datepickerPopupTemplateUrl = 'my/template'
//                    };
//
//                    
//                    $scope.inlineOptions = {
//                        customClass: getDayClass,
//                        minDate: new Date(),
//                        showWeeks: true,
//                        datepickerPopupTemplateUrl: 'my/template'
//                    };
//
//                    $scope.dateOptions = {
//                        dateDisabled: disabled,
//                        formatYear: 'yy',
//                        maxDate: new Date(2020, 5, 22),
//                        minDate: new Date(),
//                        startingDay: 1,
//                        datepickerPopupTemplateUrl: 'my/template'
//                    };
//                    
//                    $scope.attrs = {
//                        datepickerPopupTemplateUrl: 'my/template'
//                    }
//
//                    // Disable weekend selection
//                    function disabled(data) {
//                        var date = data.date,
//                                mode = data.mode;
//                        return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
//                    }
//
//                    $scope.toggleMin = function () {
//                        $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
//                        $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
//                    };
//
//                    $scope.toggleMin();
//
//                    $scope.open1 = function () {
//                        $scope.popup1.opened = true;
//                    };
//                    
//                    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
//                    $scope.format = $scope.formats[0];
//                    $scope.altInputFormats = ['M!/d!/yyyy'];
//                    $scope.datepickerPopupTemplateUrl = {'attrs': 'my/template'};
//
//
//                    $scope.popup1 = {
//                        opened: false
//                    };
//
//                    function getDayClass(data) {
//                        var date = data.date,
//                                mode = data.mode;
//                        if (mode === 'day') {
//                            var dayToCheck = new Date(date).setHours(0, 0, 0, 0);
//
//                            for (var i = 0; i < $scope.events.length; i++) {
//                                var currentDay = new Date($scope.events[i].date).setHours(0, 0, 0, 0);
//
//                                if (dayToCheck === currentDay) {
//                                    return $scope.events[i].status;
//                                }
//                            }
//                        }
//
//                        return '';
//                    }
// TEST CODE ENDS

  
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    

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