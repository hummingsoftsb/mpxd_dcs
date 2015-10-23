(function(){
  'use strict';
  angular.module('app')
    .controller('MenuCtrl', MenuCtrl);

  function MenuCtrl($rootScope, $scope, $state, AuthSrv, DataSrv){
    var vm = {};
    $scope.vm = vm;
	$scope.syncing = false;
    vm.logout = logout;
	vm.exit = exit; 
	vm.lastSync = "";
	vm.alertCount = 0;
	vm.remindCount = 0;
	vm.sync = function() {
		DataSrv.synchronize().catch(function(e){
			console.log('Catched sync from menu.ctrl!',e);
			$scope.syncing = false;
		});
	}
	
	vm.refreshData = function(){
		DataSrv.getData().then(function(data){ 
			console.log('from defsdgsdgs',data);
			vm.lastSync = data.lastSync.displayTime;
			vm.alertCount = data.data.alerts.length;
			vm.remindCount = data.data.reminders.length;
		}, function(e){
			$scope.syncing = false;
		});
	}
	
	$scope.$on('sync',function(evt,a){
		console.log('got sync!',evt,a);
		if ((a.lastSync.st == 'complete-success') || (a.lastSync.st == 'complete-latest')) {
			vm.lastSync = a.lastSync.displayTime;
			vm.alertCount = a.data.alerts.length;
			vm.remindCount = a.data.reminders.length;
		}
	});
	
	$scope.$on('sync-start', function() {
		$scope.syncing = true;
	});
	$scope.$on('sync-end', function() {
		$scope.syncing = false;
	});
	
	AuthSrv.hookOn('login', function(){vm.refreshData();});
	
	
	
	function exit() {/*
	navigator.notification.confirm(
		'Confirm exit?', function(b){if (b==2){navigator.app.exitApp()}},'Exit','Cancel,OK'
    )*/
		navigator.app.exitApp();
	}

    function logout(){
      AuthSrv.logout()
    };
	
	
	//if ($rootScope.isOnline) setTimeout(DataSrv.synchronize, 1500);
	vm.refreshData();
  }
})();
