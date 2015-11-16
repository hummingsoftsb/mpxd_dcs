(function(){
  'use strict';
  angular.module('app')
    .controller('LoginCtrl', LoginCtrl);

  function LoginCtrl($rootScope, $scope, $state, AuthSrv, $window, $q, ToastPlugin, DataSrv, PushSrv, UpdateSrv){
    var vm = {};
    $scope.vm = vm;

    vm.error = null;
    vm.loding = false;
    vm.credentials = {login: 'zul@hummingsoft.com.my', password: 'demo'};
    vm.login = login;
	vm.serverAddress = Config.backendUrl;
	vm.isExitAvailable = ((typeof navigator.app != 'undefined') && (typeof navigator.app.exitApp == 'function'));
	
	vm.exitApp = function(){
		if (vm.isExitAvailable) navigator.app.exitApp();
	}
	
	vm.changeServer = function() {
		Config.backendUrl = vm.serverAddress;
	}
	
    function login(credentials){
      vm.error = null;
      vm.loading = true;
	  
	  //var loginfn = $window.isOnline ? AuthSrv.login : AuthSrv.offlineLogin;
	  
      AuthSrv.login(credentials).then(function(user){
		if(!AuthSrv.isLogged()) {
			 vm.loading = false;
			 return;
		}
		PushSrv.initialize();
		/*
		PushSrv.setInstallationId();
		PushSrv.listenTo('logout', function(pn){
			//alert('Im logging out from PN',JSON.stringify(pn));
			AuthSrv.logout();
		});
		$scope.$on('logout', function(){PushSrv.unlistenTo('logout');})
		*/
		//$rootScope.session = user;
		//console.log(DataSrv);
		//DataSrv.getData();
		;;;
		setTimeout(function(){if ($rootScope.isOnline) {
			DataSrv.synchronize().then(function(){
				UpdateSrv.promptForUpdate();
			});
		}}, 1);
        $state.go('app.tabs.journals');
        vm.credentials.password = '';
        vm.error = null;
        vm.loading = false;
      }, function(error){
		alert(error);
		vm.error = error;
        vm.credentials.password = '';;
        //vm.error = error.data && error.data.message ? error.data.message : error.statusText;
		//var cleanError = vm.error;
		//if (vm.error.indexOf('Invalid username or password')) cleanError = 'Invalid username or password';
		//alert(cleanError);
        vm.loading = false;
      });
    };
	/*
	function offlineLogin(credentials) {
		AuthSrv.offlineLogin(credentials).then(function(user){
			if(!user.logged) {
				vm.loading = false;
				return;
			}
			$state.go('app.tabs.twitts');
			vm.credentials.password = '';
			vm.error = null;
			vm.loading = false;
			}, function(error){
			vm.credentials.password = '';
			vm.error = error.data && error.data.message ? error.data.message : error.statusText;
			vm.loading = false;
		});
	}*/
  }
})();
