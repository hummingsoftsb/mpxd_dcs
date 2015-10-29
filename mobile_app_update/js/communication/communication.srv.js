(function(){
  'use strict';
  angular.module('app')
    .factory('CommSrv', CommSrv)

  CommSrv.$inject = ['$http', 'Config', 'Utils'];
  function CommSrv($http, Config, Utils){
	var service = {
		sendRequest: sendRequest,
		getUrl: getUrl
    };
    return service;
	
	function sendRequest(url, data) {
		var furl = Config.backendUrl+url;
		return Utils.formRequest(furl,data);
	}
	
	function getUrl() {
		return Config.backendUrl;
	}
 }
})();
