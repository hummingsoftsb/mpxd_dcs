(function(){
  'use strict';
  angular.module('app')
    .config(configure);

  function configure($stateProvider){
    $stateProvider
      .state('app.tabs.settings', {
	 url: '/settings',
	 views: {
		'main-tab': {
			templateUrl: 'js/settings/settings.html',
			controller: 'SettingsCtrl'
		}
	 }
	 }).state('app.tabs.about', {
	 url: '/about',
	 views: {
		'main-tab': {
			templateUrl: 'js/settings/about.html',
			controller: 'AboutCtrl'
		}
	 }
	 });
	
	
  }
})();

 