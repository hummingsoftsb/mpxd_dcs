(function(){
  'use strict';
  angular.module('app')
	.controller('SettingsCtrl', SettingsCtrl)
	.controller('AboutCtrl', function(){});
	
  function SettingsCtrl($rootScope, $scope, $state, AuthSrv, $window, $q, ToastPlugin, DataSrv, $ionicLoading, StorageUtils, UpdateSrv, $ionicScrollDelegate) {
	var vm = {};
	$scope.vm = vm;
	vm.user = ""; 
	vm.lastSync = "";
	vm.user = AuthSrv.getSession().username;
	vm.connection = $rootScope.isOnline;
	vm.updating = true;
	vm.version = 'Loading...';
	vm.fullVersion = '';
	vm.versionNumber = 'Loading...';
	
	vm.passcode = '1234';
	vm.serverStatus = false;
	vm.serverURL = Config.backendUrl;
	vm.updateServerURL = UpdateSrv.getUpdateUrl();
	vm.developerEnabled = false;
	vm.refreshScroll = function(){setTimeout(function(){$ionicScrollDelegate.resize();}, 50);}
	
	vm.changeDeveloperMode = function($event){
		// This function is after it is clicked - in other words, true is false and false is true.
		//
		
		console.log('is it',vm.developerEnabled)
		
		if (!vm.developerEnabled) {
			$event.preventDefault();
			
			setTimeout(function(){
				var ok = (prompt('Developer Passcode', '') == vm.passcode);
				if (ok) {
					vm.developerEnabled = true;
					$rootScope.$digest();
					vm.refreshScroll();
				}
			},1);
		} else {
			vm.developerEnabled = false;
			vm.refreshScroll();
		}	
	}
	
	vm.pull = function() {
		/*$ionicLoading.show({
			template: '<ion-spinner icon="android"></ion-spinner><p>Synchronizing</p>'
		});*/
		DataSrv.synchronize().then(function(){
			//$ionicLoading.hide();
		});
	}
	
	vm.refreshData = function() {
		if ($rootScope.isOnline) {
			var manifestPromise = StorageUtils.get('manifest','',true);
			var serverPromise = AuthSrv.ping();
			vm.serverStatus = false;
			vm.serverStatusText = 'Checking...';
			manifestPromise.then(function(c){
				if ((c.versionNumber) && (c.version)) {
					vm.versionNumber = c.versionNumber;
					vm.fullVersion = c.version;
					vm.version = c.version.substr(0,20)+'...';
					vm.updating = false;
				}
			});
			
			serverPromise.then(function(r){
				if ((typeof r != 'undefined') && (typeof r.data != 'undefined')) {
					vm.serverStatus = (r.data.st == 1);
					vm.serverStatusText = vm.serverStatus ? 'Online': 'Offline'
				}
			}, function(e){
				console.log('Unable to ping server!');
				vm.serverStatus = false;
				vm.serverStatusText = vm.serverStatus ? 'Online': 'Offline'
			});
		} else {
		}
		
		vm.user = AuthSrv.getSession().username;
		DataSrv.getLastSync().then(function(lastSync){ vm.lastSync = lastSync.displayTime });
	}
	
	vm.showBuildInfo = function() {
		alert(vm.fullVersion);
	}
	
	DataSrv.hookOn('sync', function(a) {
		vm.lastSync = a.lastSync.displayTime;
	});
	
	AuthSrv.hookOn('backendUrl', function() {
		vm.serverURL = Config.backendUrl;
	});
	
	
	vm.update = function() {
		/*var savedUrl = StorageUtils.getSync('saved-update-url');
		//alert(savedUrl);
		var url = prompt('Update URL',(typeof savedUrl == 'undefined' || savedUrl == '') ? 'http://192.168.1.69:8100/' : savedUrl);
		if ((typeof url == 'undefined') || (url == null) || (url == '')) return;
		UpdateSrv.setUpdateUrl(url);*/
		var upd = UpdateSrv.updateAppNow().then(function(){
		}, function(){}, function(n){
			//StorageUtils.set('saved-update-url', url);
			vm.updating = n.updating;
		})
	}
	
	vm.setUpdateUrl = function() {
		var url = prompt('Update URL',vm.updateServerURL);
		if (url == null) return;
		vm.updateServerURL = url;
		UpdateSrv.setUpdateUrl(url);
		StorageUtils.set('saved-update-url', url);
	}
	
	
	$rootScope.$on('$cordovaNetwork:online', function(event, networkState){
		vm.connection = true;
		vm.refreshData();
	});
	
	$rootScope.$on('$cordovaNetwork:offline', function(event, networkState){
		vm.connection = false;
		vm.serverStatus = false;
		vm.serverStatusText = 'You are offline';
	});
	
	AuthSrv.hookOn('login', function(){vm.refreshData();});
	
	
	vm.refreshData();
  }
})();