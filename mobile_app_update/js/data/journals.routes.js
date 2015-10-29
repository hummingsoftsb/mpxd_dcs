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
	 });
	
	
  }
})();

 