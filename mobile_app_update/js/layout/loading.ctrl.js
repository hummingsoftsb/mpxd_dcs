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
		if(AuthSrv.isLogged()){
			AuthSrv.checkSession().then(function(){
				PushSrv.initialize();
				$state.go('app.tabs.journals');
			});
			} else {
			    $state.go('login');
			}
		
      }, 300);
    }
  }
})();
