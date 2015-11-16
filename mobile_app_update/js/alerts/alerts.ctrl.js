(function(){
  'use strict';
  angular.module('app')
    .controller('AlertsCtrl', AlertsCtrl)
    .controller('RemindsCtrl', RemindsCtrl);

  function AlertsCtrl($scope, $window, $ionicModal, $ionicPopover, $ionicActionSheet, $log, AlertSrv, AuthSrv, $ionicScrollDelegate, JournalSrv, $q, $state, DataSrv){
    var vm = {};
    $scope.vm = vm;
	vm.alerts = [];
	vm.pageSize = 10;
	vm.currentPage = 1;
	$scope.isLoaded = false;
	vm.totalpage = 0;
	
	$scope.init = function() {
	//console.log('alert init');
		vm.refreshData();
	}

	vm.pageChangeHandler = function(){
		$ionicScrollDelegate.resize();
	};
	
	vm.refreshData = function() {
		var alertPromise = AlertSrv.getAllAlerts();
		var journalPromise = JournalSrv.getAllDataEntries();
		
		// Gets all listed alerts and assign them links using $state while referring to project, journal, and entry IDs
		$q.all([alertPromise, journalPromise]).then(function(values){
			vm.alerts = angular.copy(values[0]);
			var dataEntries = values[1];
			angular.forEach(vm.alerts, function(val, key){
				
				var lo = dataEntries[val.data_entry_no];
				vm.alerts[key].linkObj = dataEntries[val.data_entry_no];
				if (typeof lo != 'undefined') {
					vm.alerts[key].link = $state.href('app.tabs.dataentry', {projectId: lo.projectId, journalId:lo.journalId, entryId: lo.entryId});
				} else {
					vm.alerts[key].link = '#';
				}
			});
			console.log(vm.alerts);
			vm.totalpage = Math.ceil(vm.alerts.length / vm.pageSize);
			$scope.isLoaded = true;
		});
	}
	/*
	$scope.$on('sync', function(evt, data){
		console.log("SYNC DETECTED FOR ALERT",data.lastSync.st)
		if (data.lastSync.st == "complete-success") {
			vm.refreshData();
		}
	});*/
	
	DataSrv.hookOn('sync', function(data){
		if (data.lastSync.st == "complete-success") {
			vm.refreshData();
		}
	});
	
	AuthSrv.hookOn('login', function(){console.log('woot! got callback from my hooks');vm.refreshData();});
	
	$scope.init();
	
  }
  
  
  function RemindsCtrl($scope, $window, $ionicModal, $ionicPopover, $ionicActionSheet, $log, AlertSrv, AuthSrv, $ionicScrollDelegate, JournalSrv, $q, $state, DataSrv){
    var vm = {};
    $scope.vm = vm;
	
	vm.reminders = [];
	vm.pageSize = 10;
	vm.currentPage = 1;
	$scope.isLoaded = false;
	vm.totalpage = 0;
	
	$scope.init = function() {
		//console.log('remind alert');
		vm.refreshData();
	}

	vm.pageChangeHandler = function(){
		$ionicScrollDelegate.resize();
	};
	
	vm.refreshData = function() {
		var remindPromise = AlertSrv.getAllReminders();
		var journalPromise = JournalSrv.getAllDataEntries();
		
		// Gets all listed alerts and assign them links using $state while referring to project, journal, and entry IDs
		$q.all([remindPromise, journalPromise]).then(function(values){
			vm.reminders = angular.copy(values[0]);
			var dataEntries = values[1];
			angular.forEach(vm.reminders, function(val, key){
				var lo = dataEntries[val.data_entry_no];
				vm.reminders[key].linkObj = dataEntries[val.data_entry_no];
				if (typeof lo != 'undefined') {
					vm.reminders[key].link = $state.href('app.tabs.dataentry', {projectId: lo.projectId, journalId:lo.journalId, entryId: lo.entryId});
				} else {
					vm.reminders[key].link = '#';
				}
				
			});
			//console.log(vm.reminders);
			vm.totalpage = Math.ceil(vm.reminders.length / vm.pageSize);
			$scope.isLoaded = true;
		});
	}
	/*
	vm.refreshData = function() {
		AlertSrv.getAllReminders().then(function(r){
		vm.reminders = r;
		vm.totalpage = Math.ceil(vm.reminders.length / vm.pageSize);
		$scope.isLoaded = true;
		});
	}*/
	
	/*$scope.$on('sync', function(evt, data){
		console.log("SYNC DETECTED FOR REMINDER",data.lastSync.st)
		
	});
	*/
	
	DataSrv.hookOn('sync', function(data){
		if (data.lastSync.st == "complete-success") {
			vm.refreshData();
		}
	});
	AuthSrv.hookOn('login', function(){console.log('woot! got callback from my hooks');vm.refreshData();});
	
	/*console.log('Reminder login setup!');
	$scope.$on('login', function(evt) {
		console.log('Reminder login!');
		// Login, refresh data.
		vm.refreshData();
	});*/
	
	$scope.init();
	
  }
})();
