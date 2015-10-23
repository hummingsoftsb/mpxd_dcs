/* Requires Crypto.js */
(function(){
  'use strict';
  angular.module('app')
    .factory('UpdateSrv', UpdateSrv)
   // .factory('AuthInterceptor', AuthInterceptor);

  UpdateSrv.$inject = ['$http', 'Config', '$q', '$rootScope','ToastPlugin', '$state', 'Utils', '$ionicLoading'];
  function UpdateSrv($http, Config, $q, $rootScope,ToastPlugin, $state, Utils, $ionicLoading){
    var service = {
		check: check,
		download: download,
		update: update,
		promptForUpdate: promptForUpdate,
		updateAppNow: updateAppNow,
		getUpdateUrl: getUpdateUrl
    };
	
	var updateUrl = Config.backendUrl+'/mobile_app_update/';
	var isCordova = typeof cordova != 'undefined';
	
	
	var fs = new CordovaPromiseFS({
		persistent: isCordova, // Chrome should use temporary storage.
		Promise: Promise
	});

	var loader = new CordovaAppLoader({
		fs: fs,
		localRoot: 'app',
		serverRoot: updateUrl,//Config.updateUrl,
		mode: 'mirror',
		cacheBuster: true
	});
	
	window.loader = loader;
	
	
	return service;
	
	function getUpdateUrl(){
		return updateUrl;
	}
	
	function check() {
		return loader.check();
	}
	
	function download() {
		return loader.download();
	}
	
	function update() {
		return loader.update();
	}
	
	function promptForUpdate() {
		check().then(function(c){
			if (c){
				if (confirm('App update is available. Update now?')) {
					updateAppNow();
				}
			} else {
				console.log('No update for prompt!');
			}
		});
	}
	
	function updateAppNow() {
		var deferred = $q.defer();
		
		if (!$rootScope.isOnline) {
			alert('You are offline');
			setTimeout(function(){deferred.resolve();}, 1);
			return deferred.promise;
		}
		
		setTimeout(function(){
			console.log('Checking updates..');
			//vm.updating = true;
			deferred.notify({updating: true});
			$ionicLoading.show();
			//console.log('UPDATE SRV',UpdateSrv);
			check().then(function(c){
				if (c) {
					console.log('Got update! Downloading..');
					ToastPlugin.showLongTop('Downloading updates');
					download().then(function(d){
						$ionicLoading.hide();
						console.log('Finished downloading, will update now',d);
						deferred.notify({updating: false});
						deferred.resolve();
						update();
					}, function(e){alert('Unable to connect to server');console.log('Erred during update!'); $ionicLoading.hide();
						deferred.notify({updating: false});
						deferred.resolve();
					})
				} else {
					console.log('No updates');
					ToastPlugin.showLongTop('No updates available');
					$ionicLoading.hide();
					//vm.updating = false;
					deferred.notify({updating: false});
					deferred.resolve();
				}
			}, function(e){
				alert('Unable to connect to server');;
				console.log('Erred during update!'); 
				$ionicLoading.hide();
				deferred.notify({updating: false});
				deferred.resolve();
				});
			}, 1);
		return deferred.promise;
	};
  }
})();
