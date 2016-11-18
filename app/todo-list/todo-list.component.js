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
// calendar part begins                    
                    $scope.inlineOptions = {
                        minDate: new Date(2014, 1, 1)
                    };

                    $scope.dateOptions = {
                        formatYear: 'yy',
                        maxDate: new Date(2020, 5, 22),
                        showWeeks: false,
                        showButtonBar: false,
                        startingDay: 1
                    };
                    
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
                                    $scope.todos[index].time_left = $scope.convertTimeLeft(response.data.time_left, response.data.done);
                                });
                    };

                    $scope.convertTimeLeft = function(timeLeft, done) {
                        if (done === true) return 0;
                        if (done === "1") return 0;
                        if (timeLeft < 0) return 8;
                        if (timeLeft > 8) return 0;
                        if (timeLeft === '0') return ''; // color blue things to do today
                        return (8 - timeLeft);
                    }
// calendar part ends
// add periodic form begins
                    $scope.addPeriod = false;
                    $scope.date = new Date();
                    $scope.days = [];
                    $scope.dailyCheck = true;
                    $scope.currentType = 1;
                    for(var i=1;i<32;i++) $scope.days[i] = i;
                    $scope.selectDay = function(item) {
                        $scope.deselectWeekEl(item);
                        $scope.deselectMonthEl(item);
                        $scope.currentType = 1;
                    };
                    
                    $scope.selectWeek = function(item) {
                        $scope.deselectDayEl(item);
                        $scope.deselectMonthEl(item);
                        $scope.currentType = 2;
                    };
                    
                    $scope.selectMonth = function(item) {
                        $scope.deselectDayEl(item);
                        $scope.deselectWeekEl(item);
                        $scope.currentType = 3;
                    };
                    
                    $scope.deselectWeekEl = function (item) {
                        for (var i=1; i<8; i++) {
                            $scope['weeklyCheck' + i] = false;
                            if(typeof(item) === 'undefined') continue;
                            item.weekly[i-1] = (item.type === '2' && item.numbers.indexOf(i-1) > -1);
                        }

                    };
                    $scope.deselectDayEl = function (item) {
                        $scope.dailyCheck = false;
                        if(typeof(item) === 'undefined') return;
                        item.daily = false;
                    };
                    $scope.deselectMonthEl = function (item) {
                        $scope.monthlyCheck = "";
                        if(typeof(item) === 'undefined') return;
                        item.monthly = "";
                    };
// add periodic form ends                    
                    
                    $scope.day = 0;

                    $scope.updateDate = function () {
                        var date = new Date();
                        date.setDate(date.getDate() + $scope.day);
                        $scope.date = date;
                    };
                    
                    $scope.initEditItemForm = function (item) {
                        item.daily = (item.type === '1');
                        item.weekly = [];
                        for (var i = 0; i < 7; i++) {
                            item.weekly[i] = (item.type === '2' && item.numbers.indexOf(i) > -1);
                        }
                        
                        item.monthly = (item.type === '3' ? item.numbers : false);
                    }
                    
                    $scope.fetch = function () {
                        $http.get("fetch?day=" + $scope.day + "&email=" + email)
                                .then(function (response) {
                                    if ('string' === typeof(response.data)) {
                                        AuthenticationService.ClearCredentials();
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
                        var params = {"id": item.id, "email": email};
                        $http.post("index.php?route=archive", params);
                    };

                    $scope.add = function (text, type) {
                        
                        var dParams = new Object;
                        // type = 0 - regular, 1 -daily, 2 - weekly, 3 - monthly
                        if (type > 1) {
                            dParams.day = $scope.dailyCheck;
                            dParams.month = $scope.monthlyCheck;
                            var week = [];
                            for(var i=1;i<8;i++) week[i] = $scope['weeklyCheck' + i];
                            dParams.week = week;
                        }
                        
                        $scope.todos.push({name: text, done: false, type: type, params: dParams});
                        $scope.newTodo = '';
                        $scope.newDaily = '';
                        params = {"name": text, "type": type, "email": email, params: dParams};

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
                                    item.time_left = $scope.convertTimeLeft(item.time_left, item.done);
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
                    
                    //FIXME: use pluralization instead of this code
                    $scope.russianDays = function (days) {
                        if("1" === days) {
                            return "DAYS_PASSED_1";
                        }

                        if("2" === days || "3" === days || "4" === days) {
                            return "DAYS_PASSED_2";
                        }
                        
                        return "DAYS_PASSED_3";
                    }

                    $scope.fetch();
                }
            });
}());