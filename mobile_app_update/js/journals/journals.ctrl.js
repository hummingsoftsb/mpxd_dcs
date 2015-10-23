(function(){
  'use strict';
  angular.module('app')
    .controller('JournalsCtrl', JournalsCtrl);

  function JournalsCtrl($scope, $window, $ionicModal, $ionicPopover, $ionicActionSheet, $log, JournalSrv, $ionicScrollDelegate, AuthSrv, $state){
    var vm = {};
    $scope.vm = vm;
	$scope.isLoaded = false;	
	vm.data_listed = [];
	vm.processed_data = [];
	vm.settings = {};
	//vm.settings.max_items_to_load = 10;
	
	vm.pageSize = 10;
	vm.currentPage = 1;
	vm.totalPage = Math.ceil(vm.data_listed.length / vm.pageSize);
	vm.pageChangeHandler = function(){
		 $ionicScrollDelegate.resize();
	}
	
	
	/* 
	Function to filter project & journals. It will search all project & journal names which contains the
	query keyword. Might be expensive, can be optimized later.
	*/
	
	vm.filterAll = function() {
	
		var query = (typeof $scope.search == "undefined") ? "" : $scope.search.toLowerCase();
		//var max = vm.settings.max_items_to_load;
		vm.data_listed = [];
		
		if (query == "") {
			vm.data_listed = vm.processed_data;
		} else {
			// Loop project
			for (var i = 0; (i < vm.processed_data.length); i++) {
			
				// Copy to keep integrity of vm.processed_data
				var project = angular.copy(vm.processed_data[i]);
				var list_journals = [];
				
				// Loop journal
				for (var j = 0; j < project.journals.length; j++) {
					var journal = project.journals[j];
					if (journal.journal_name.toLowerCase().indexOf(query) != -1) list_journals.push(journal);
				}
				
				// If no matching journal name, but project name has a match, show all journals
				if ((list_journals.length == 0) && (project.project_name.toLowerCase().indexOf(query) != -1)) {
					vm.data_listed.push(project);
				}
				
				// If there is matching journal name, only the matched journals are shown.
				else if (list_journals.length > 0) {
					project.journals = list_journals;
					vm.data_listed.push(project);
				}
			}
		}
		vm.totalPage = Math.ceil(vm.data_listed.length / vm.pageSize);
		//console.log(vm.data_listed)
	}
	
	vm.resetSearch = function() { $scope.search = ""; vm.filterAll(); }
	vm.data_listed = [];
	vm.refreshData = function() {
		JournalSrv.getProcessedProjects().then(function(j) {
			vm.processed_data = j;
			$scope.isLoaded = true;
			vm.filterAll();
			//console.log(vm.processed_data);
		});
	}
	
	$scope.init = function() {
		vm.refreshData();
	}
	
	$scope.$on('sync', function(evt, data){
		if (data.lastSync.st == "complete-success") {
			vm.refreshData();
		}
	});
	
	// Currently this is ran twice, and there will be two hooks. Dont know why.
	JournalSrv.hookOn('published', function(){
		vm.refreshData();
	});
	
	vm.go = function(project,journal) {
		if (journal.data_entries.length > 1) {
			$state.go('app.tabs.journaldetail', {projectId:project.project_no, journalId:journal.journal_no});
		} else {
			JournalSrv.getEntryInitialized(project.project_no, journal.journal_no, journal.data_entries[0]).then(function(initialized){
				if (!initialized) {
					if (confirm('Assign data attributes to the current week?')) {
						JournalSrv.setEntryInitialized(project.project_no, journal.journal_no, journal.data_entries[0]);
						$state.go('app.tabs.dataentry', {projectId: project.project_no, journalId: journal.journal_no, entryId: journal.data_entries[0], force: true});
					}
				} else {
					$state.go('app.tabs.dataentry', {projectId: project.project_no, journalId: journal.journal_no, entryId: journal.data_entries[0], force: true});
				}
			});
			
			
		}
	}
	
	// Generate a link to either go directly to entry (if only a single entry is available) or choose week if more than an entry is available.
	$scope.generateLink = function(project, journal) {
		
		if (journal.data_entries.length > 1) {
			// List weeks first
			return $state.href('app.tabs.journaldetail', {projectId:project.project_no, journalId:journal.journal_no});
		}
		
		else {
			// Direct to entry
			//JournalSrv.getEntryState(project.project_no, journal.journal_no, journal.data_entries[0]).then(function(a){console.log(a)});
			return $state.href('app.tabs.dataentry', {projectId: project.project_no, journalId: journal.journal_no, entryId: journal.data_entries[0]});
		}
	}
	
	AuthSrv.hookOn('login', function(){ vm.refreshData();});
	/*$scope.$on('login', function(evt) {
		// Login, refresh data.
		vm.refreshData();
	});*/
	
    $scope.init();
  }
})();
