'use strict';
var app = angular.module('minibank', ['angularSpinner'], function($interpolateProvider, $qProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

app.config(['usSpinnerConfigProvider', function (usSpinnerConfigProvider) {
    usSpinnerConfigProvider.setDefaults({color: '#7952b3', lines: 10, length: 0, width: 14, radius: 20});
}]);

app.factory('service', function($http) {
	var prefix = "/minibank/";

	return {
		getClients: function() {
			return $http.get(prefix + 'getClients');
		},

		addClient: function(client_name) {
			return $http.post(prefix + 'addClient', {'client_name': client_name});
		},

		getAccounts: function(client_id) {
			return $http.get(prefix + 'getAccounts/' + client_id);
		},

		createAccount: function(client_id) {
			return $http.post(prefix + 'createAccount', {'client_id': client_id});
		},

		getEvents: function(account_id) {
			return $http.get(prefix + 'getEvents/' + account_id);
		},

		depositMoney: function(account_id, amount) {
			return $http.post(prefix + 'depositMoney', {'account_id': account_id, 'amount': amount});
		},

		withdrawMoney: function(account_id, amount) {
			return $http.post(prefix + 'withdrawMoney', {'account_id': account_id, 'amount': amount});
		},

		replay: function(account_id, event_limit) {
			return $http.get(prefix + 'replay/' + account_id + "/" + event_limit);
		}
	}
});

app.filter('capitalize', function() {
    return function(input) {
      	return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

app.filter('eventType', function() {
    return function(input) {
    	if(input == 0)
    		return "Account Created";
    	else if(input == 1)
    		return "Money Deposited";
    	else if(input == 2)
    		return "Money Withdrew";
    }
});

app.filter('eventData', function() {
    return function(input) {
    	if(input.type == 1)
    		return input.data;
    	else if(input.type == 2)
    		return "-" + input.data;
    }
});

app.directive("floatingNumberOnly", function() {
    return {
        require: 'ngModel',
        link: function(scope, ele, attr, ctrl) {
            ctrl.$parsers.push(function(inputValue) {
                var pattern = new RegExp("(^[0-9]{1,9})+(\.[0-9]{1,4})?$", "g");
                
                if (inputValue == '')
                    return '';
        
                var dotPattern = /^[.]*$/;

                if (dotPattern.test(inputValue)) {
                    ctrl.$setViewValue('');
                    ctrl.$render();
                    return '';
                }

                var newInput = inputValue.replace(/[^0-9.]/g, '');

                if (newInput != inputValue) {
                    ctrl.$setViewValue(newInput);
                    ctrl.$render();
                }

                var result;
                var dotCount;
                var newInputLength = newInput.length;
                
                if (result = (pattern.test(newInput))) {
                    dotCount = newInput.split(".").length - 1;
                    
                    if (dotCount == 0 && newInputLength > 9) { 
                        newInput = newInput.slice(0, newInputLength - 1);
                        ctrl.$setViewValue(newInput);
                        ctrl.$render();
                    }
                } else {              
                    dotCount = newInput.split(".").length - 1;
                  
                    if (newInputLength > 0 && dotCount > 1)
                        newInput = newInput.slice(0, newInputLength - 1);

                    if ((newInput.slice(newInput.indexOf(".") + 1).length) > 4)
                        newInput = newInput.slice(0, newInputLength - 1);
                    
                    ctrl.$setViewValue(newInput);
                    ctrl.$render();
                }

                return newInput;
            });
        }
    };
});

app.controller('controller', function($scope, service, $timeout) {
	$scope.showSpinnerClients = false;
	$scope.showSpinnerAccounts = false;
	$scope.showSpinnerReplay = false;

	$scope.showNoClients = false;
	$scope.showNoAccounts = false;

	$scope.spinnerButtonReplay = {radius: 8, lines: 7, width: 7, left: "81%"};
	$scope.spinnerBalanceReplay = {radius: 8, lines: 7, width: 7, left: "26%"};

	$scope.getClients = function() {
		$scope.showSpinnerClients = true;

		$timeout( function(){
	        service.getClients().then(function(response) {
	            $scope.clients = response.data;
	            $scope.showSpinnerClients = false;

	            if($scope.clients.length == 0)
	            	$scope.showNoClients = true;
	           	else 
	           		$scope.showNoClients = false;
	        })
	    }, 200 );
	}

	$scope.addClient = function() {
        service.addClient($scope.client_name).then(function(response) {
        	$('#add-client').modal('hide');
        	$scope.clients.push({id: parseInt(response.data), name: $scope.client_name});
        	$scope.client_name = undefined;
        	$scope.showNoClients = false;
        })
	}

	$scope.selectClient = function(client_id) {
		if(client_id != $scope.selected_client_id) {
			$scope.selected_client_id = client_id;
			$scope.selected_account_id = undefined;
			$scope.getAccounts(client_id);
			$scope.events = [];
		}
	}

	$scope.selectAccount = function(account_id) {
		if(account_id != $scope.selected_account_id) {
			$scope.selected_account_id = account_id;
			$scope.getEvents(account_id);
			var index_account = _.findIndex($scope.accounts, function(o) { return o.id == account_id; });
			$scope.selected_account_index = index_account;
		}
	}

	$scope.getAccounts = function(client_id) {
		$scope.showNoAccounts = false;
		$scope.accounts = undefined;
		$scope.showSpinnerAccounts = true;

		$timeout( function(){
	        service.getAccounts(client_id).then(function(response) {
	            $scope.accounts = response.data.accounts;
	            var event_counts = response.data.event_counts;
	            $scope.showSpinnerAccounts = false;

	            if($scope.accounts.length == 0)
	            	$scope.showNoAccounts = true;
	            else
	            	$scope.showNoAccounts = false;

	            var i = 0;
	            _.forEach($scope.accounts, function(account) {
				  	account.events_applied = event_counts[i];
				  	i++;
				});
	        })
	    }, 200 );
	}

	$scope.createAccount = function(client_id) {
        service.createAccount(client_id).then(function(response) {
        	$('#create-account').modal('hide');
        	$scope.accounts.push({id: parseInt(response.data), balance: 0, events_applied: 1});
        	$scope.showNoAccounts = false;
        })
	}

	$scope.getEvents = function(account_id) {
		$scope.events = undefined;
		$scope.showSpinnerEvents = true;

		$timeout( function(){
	        service.getEvents(account_id).then(function(response) {
	            $scope.events = response.data;
	            $scope.showSpinnerEvents = false;

	            var index_account = _.findIndex($scope.accounts, function(o) { return o.id == account_id; });
	            var index_event = $scope.accounts[index_account].events_applied - 1;

	            $scope.selected_event_id = $scope.events[index_event].id;
	        })
	    }, 200 );
	}

	$scope.depositMoney = function(account_id, amount) {
		$timeout( function(){
	        service.depositMoney(account_id, amount).then(function(response) {
	        	$('#deposit').modal('hide');
	        	$scope.deposit_amount = undefined;

	            var new_event = response.data.new_event;
	            $scope.events.push(new_event);

	            var new_balance = response.data.new_balance;

	            var index = _.findIndex($scope.accounts, function(o) { return o.id == new_event.aggregate_id; });
	            $scope.accounts[index].balance = new_balance;
	            $scope.accounts[index].events_applied = $scope.events.length;

	            var last_index = $scope.events.length - 1;
	            $scope.selected_event_id = $scope.events[last_index].id;
	        })
	    }, 200 );
	}

	$scope.withdrawMoney = function(account_id, amount) {
		$timeout( function(){
	        service.withdrawMoney(account_id, amount).then(function(response) {
	        	if(typeof response.data == "string")
	        		$scope.exception_message = response.data;
	        	else {
	        		$('#withdraw').modal('hide');
		        	$scope.withdraw_amount = undefined;
		        	$scope.exception_message = undefined;

		            var new_event = response.data.new_event;
		            $scope.events.push(new_event);

		            var new_balance = response.data.new_balance;

		            var index = _.findIndex($scope.accounts, function(o) { return o.id == new_event.aggregate_id; });
		            $scope.accounts[index].balance = new_balance;
		            $scope.accounts[index].events_applied = $scope.events.length;

		            var last_index = $scope.events.length - 1;
		            $scope.selected_event_id = $scope.events[last_index].id;
	        	}
	        })
	    }, 200 );
	}

	$('#deposit').on('hidden.bs.modal', function (e) {
	  	$scope.deposit_amount = undefined;
	});

	$('#withdraw').on('hidden.bs.modal', function (e) {
	  	$scope.withdraw_amount = undefined;
	  	$scope.exception_message = undefined;
	});

	$scope.replay = function(account_id, event_limit) {
		$scope.showSpinnerReplay = true;
		$scope.clicked_index = event_limit - 1;

		$timeout( function(){
	        service.replay(account_id, event_limit).then(function(response) {
	            var new_balance = response.data;
	            
	            var index_account = _.findIndex($scope.accounts, function(o) { return o.id == account_id; });
	            $scope.accounts[index_account].balance = new_balance;
	            $scope.accounts[index_account].events_applied = event_limit;

	            var index_event = $scope.accounts[index_account].events_applied - 1;
	            $scope.selected_event_id = $scope.events[index_event].id;

	            $scope.showSpinnerReplay = false;
	            $scope.clicked_index = undefined;
	        })
	    }, 200 );
	}
});