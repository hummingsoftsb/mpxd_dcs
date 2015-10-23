(function(){
  'use strict';
  angular.module('app')
    .config(configure);

  function configure($stateProvider){
    $stateProvider
      .state('app.tabs.journals', {
	 url: '/journals',
	 views: {
		'main-tab': {
			templateUrl: 'js/journals/journals.html',
			controller: 'JournalsCtrl'
		}
	 }
	 }).state('app.tabs.journaldetail', {
	 url: '/journals/:projectId/:journalId',
	 views: {
		'main-tab': {
			templateUrl: 'js/journals/journal.detail.html',
			controller: 'JournalDetailCtrl'
		}
	 }
	 }).state('app.tabs.dataentry', {
	 url: '/journals/:projectId/:journalId/:entryId/:force',
	 views: {
		'main-tab': {
			templateUrl: 'js/journals/journal.dataentry.html',
			controller: 'JournalDataEntryCtrl'
		}
	 }
	 });
	
	
  }
})();

 