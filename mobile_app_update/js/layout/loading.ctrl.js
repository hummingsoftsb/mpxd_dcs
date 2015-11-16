(function(){
  'use strict';
  angular.module('app')
    .controller('LoadingCtrl', LoadingCtrl);

  function LoadingCtrl($scope, $q, $timeout, $state, AuthSrv, PushSrv){
    var vm = {};
    $scope.vm = vm;

    $scope.$on('$ionicView.enter', function(viewInfo){
      redirect();
    });

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
					$state.go('login');
				}
			});
		} else {
			$state.go('login');
		}
		
		
		
      }, 300);
    }
  }
})();
