(function(){
  'use strict';
  angular.module('app')
    .config(configure);

  function configure($stateProvider){
    $stateProvider
      .state('app.tabs.reminds', {
	 url: '/reminds',
	 views: {
		'main-tab': {
			templateUrl: 'js/alerts/reminds.html',
			controller: 'RemindsCtrl'
		}
	 }
	 }).state('app.tabs.alerts', {
	 url: '/alerts',
	 views: {
		'main-tab': {
			templateUrl: 'js/alerts/alerts.html',
			controller: 'AlertsCtrl'
		}
	 }
	 });
	
	
  }
})();

 