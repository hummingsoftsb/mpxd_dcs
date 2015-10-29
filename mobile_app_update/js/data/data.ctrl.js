(function(){
  'use strict';
  angular.module('app')
    .controller('DataCtrl', DataCtrl)

  function DataCtrl($rootScope, $scope, $state, AuthSrv, $window, $q, ToastPlugin, DataSrv){
    var vm = {};
    $scope.vm = vm;
	
  }
  
})();
