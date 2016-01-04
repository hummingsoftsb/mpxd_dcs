(function(){
  'use strict';
  angular.module('app')
    .controller('LoadingCtrl', LoadingCtrl);

  function LoadingCtrl($scope, $q, $timeout, $state, AuthSrv, PushSrv, ToastPlugin, StorageUtils, $rootScope){
    var vm = {};
    $scope.vm = vm;
//
    $scope.$on('$ionicView.enter', function(viewInfo){
      redirect();
    });
	
	
	var last_url = StorageUtils.getSync('last-server-ip');
	if ((typeof last_url != 'undefined') && (last_url != null) && (last_url != '')) Config.backendUrl = last_url;
	$rootScope.isCheckedBackend = true;

    function redirect(){
      $timeout(function(){
	  // First, check if logged
		if(AuthSrv.isLogged()) {
			// If logged & online, check online logged
			AuthSrv.isOnlineLogged().then(function(y){
				if(y){
					AuthSrv.checkSession().then(function(){
						PushSrv.initialize();
						$state.go('app.tabs.journals');
					});
				} else {
					//Check if online, then use old address? or use old address from back
					if (AuthSrv.isLogged() && $rootScope.isOnline) {
						ToastPlugin.showLongTop('Unable to connect to server');
					} else {
					}
					$state.go('login')
					
				}
			});
		} else {
			$state.go('login');
		}
		
		
		
      }, 300);
    }
  }
})();
