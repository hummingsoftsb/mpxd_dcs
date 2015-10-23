(function(){
  'use strict';
  angular.module('app')
    .config(configure);

  function configure($stateProvider){
    $stateProvider
      .state('loading', {
      url: '/loading',
      templateUrl: 'js/layout/loading.html',
      controller: 'LoadingCtrl'
    })
      .state('app', {
      url: '/app',
      abstract: true,
      templateUrl: 'js/layout/layout.html',
      controller: 'MenuCtrl',
	  
      data: {
        restrictAccess: ['logged'] // this property will be herited to child views
      }
    }).state('app.tabs', {
      url: '/tabs',
	  //template: '<p>Hello, world!</p>',
      abstract: true,
      views: {
        'mainContent': {
          templateUrl: 'js/layout/tabs.html',
          controller: 'TabsCtrl'
        }
      }
    });
     /* .state('app.tabs', {
      url: '/tabs',
	  template: '<p>Hello, world!</p>'
      //abstract: true,
      /*views: {
        'menuContent': {
          templateUrl: 'js/layout/tabs.html',
          controller: 'TabsCtrl'
        }
      }*
    });*/
  }
})();
