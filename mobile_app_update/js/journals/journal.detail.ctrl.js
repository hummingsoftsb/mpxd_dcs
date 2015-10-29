(function(){
  'use strict';
  angular.module('app')
    .controller('JournalDetailCtrl', JournalDetailCtrl);

  function JournalDetailCtrl($scope, $window, $ionicModal, $ionicPopover, $ionicActionSheet, $log, JournalSrv, $ionicScrollDelegate, AuthSrv, $stateParams){
    var projectId = $stateParams.projectId;
    var journalId = $stateParams.journalId;
	var vm = {};
	$scope.vm = vm;
	vm.dataEntries = [];
	JournalSrv.getJournalData(projectId, journalId).then(function(d){
		angular.forEach(d['data_entries'], function(val, key) {
			var r = angular.copy(val);
			r.projectId = projectId;
			r.journalId = journalId;
			vm.dataEntries.push(r);
		});
		
	});
  }	
})();
