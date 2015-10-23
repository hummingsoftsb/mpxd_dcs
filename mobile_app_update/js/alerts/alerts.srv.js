(function(){
  'use strict';
  angular.module('app')
    .factory('AlertSrv', AlertSrv)

  AlertSrv.$inject = ['$http', 'Config', 'Utils', 'DataSrv', 'AuthSrv'];
  function AlertSrv($http, Config, Utils, DataSrv, AuthSrv){
	var service = {
		getAllReminders: getAllReminders,
		getAllAlerts: getAllAlerts,
		//hookOnLogin: hookOnLogin
    };
    return service;
	
	function getAllReminders() {
		return DataSrv.getData().then(function(r){
			//console.log('Here is reminders',r);
			return r.data['reminders'];
		});
	}
	
	function getAllAlerts() {
		return DataSrv.getData().then(function(r){
			//console.log('Here is alerts',r);
			return r.data['alerts'];
		});
	}
	/*
	function hookOnLogin(f) {
		AuthSrv.hookOn('login',f);
	}*/
	
 }
})();
