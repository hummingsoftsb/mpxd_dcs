(function(){
  'use strict';
  angular.module('app', ['ionic', 'ngCordova', 'LocalForageModule', 'angularUtils.directives.dirPagination', 'jrCrop', 'templatescache'])
    .config(configure)
    .run(runBlock);

  configure.$inject = ['$urlRouterProvider', '$provide', '$httpProvider'];
  function configure($urlRouterProvider, $provide, $httpProvider){
    // ParseUtilsProvider.initialize(Config.parse.applicationId, Config.parse.restApiKey);

    $urlRouterProvider.otherwise('/loading');

    // improve angular logger
    $provide.decorator('$log', ['$delegate', 'customLogger', function($delegate, customLogger){
      return customLogger($delegate);
    }]);

    // configure $http requests according to authentication
    //$httpProvider.interceptors.push('AuthInterceptor');
  }

  function runBlock($rootScope, $state, $log, AuthSrv, PushPlugin, ToastPlugin, Config, $cordovaNetwork, $cordovaPush, $window, PushSrv){
    checkRouteRights();
	//console.log('Setting up push notification');
    setupPushNotifications();
	//console.log('finished');
	
	
	checkConnectivity();
	
	document.addEventListener('deviceready', function(){
	
		/*document.addEventListener('backbutton', function(e){
			console.log($state.current.name);
			
			if ($state.current.name != 'login') return;
			console.log('It is login!');
			e.preventDefault();
			if (confirm('Exit?'))
			{
				console.log('Yes exit');
				if (navigator.app) navigator.app.exitApp();
			}
			else
			{
				console.log('No exit');
			}
		}, false);*/

	});
	
	var exitNext = false;
	
	$rootScope.$on('$stateChangeStart', 
      function(event, toState, toParams, fromState, fromParams){
		var from = fromState.name;
		var to = toState.name;
		var loggedIn = AuthSrv.getSession().logged;
		console.log('FROM:'+from,'TO:'+to,exitNext);
		//console.log('toparams: ',toParams);//
		//console.log('fromparams: ',fromParams);
		if ((from == '') && (to == 'loading')) {
			// First load, allow.
		} else if ((from == '') && (to != 'loading')) {
			// Skipped loading! Should I initialize push?
			PushSrv.initialize();
		} else if (to == 'loading') {
			// Anywhere to loading must be 'back to exit'
			event.preventDefault();
			askExit();
		} else if ((to == 'login') && (typeof fromParams.logout != 'undefined') && (!fromParams.logout) && !exitNext) {
			// Anywhere except loading to login is also exit except for logout!
			event.preventDefault();
			askExit();
		} else if ((from == 'app.tabs.dataentry') && (to == 'login') && $rootScope.isJournalDirty) {
			if (!loggedIn) {
				if (confirm('You have been logged out, please re-login. Discard changes?')) {
					exitNext = false;
					//event.preventDefault();
					//$state.go('login');
				} else {
					// So that after saving this journal, will go back to login screen.
					exitNext = true;
					event.preventDefault();
				}
			} else {
				// Not logged out, probably clicked on logout himself while in data entry
				if (!askJournalExit()) event.preventDefault();
			}
		}
		else if ((from == 'app.tabs.dataentry') && $rootScope.isJournalDirty && !exitNext) {
			if (!askJournalExit()) event.preventDefault();
		} /*else if ((from == 'app.tabs.dataentry') && exitNext) {
			event.preventDefault();
			$state.go('login');
		}*/
		
			
        
          // transitionTo() promise will be rejected with 
          // a 'transition prevented' error
	})
	
	function askExit() {
		if (confirm('Exit?'))
		{
			console.log('Yes exit');
			if (navigator.app) navigator.app.exitApp();
		}
		else
		{
			console.log('No exit');
		}
	}
	
	function askJournalExit() {
		return confirm('Discard changes?')
	}
	
    ////////////////
	
	
	function checkConnectivity() {
	
	////
		var deviceType = (navigator.userAgent.match(/iPad/i))  == 'iPad' ? 'iPad' : (navigator.userAgent.match(/iPhone/i))  == 'iPhone' ? 'iPhone' : (navigator.userAgent.match(/Android/i)) == 'Android' ? 'Android' : (navigator.userAgent.match(/BlackBerry/i)) == 'BlackBerry' ? 'BlackBerry' : 'null';
		if (deviceType == 'null') $rootScope.isOnline = true;
		////
	
		document.addEventListener('deviceready', function () {
		var type = $cordovaNetwork.getNetwork()
		var isOnline = $cordovaNetwork.isOnline()
		//var isOffline = $cordovaNetwork.isOffline()
		console.log('Setting rootscope online');
		$rootScope.isOnline = isOnline;
		if (isOnline) console.log('Online using',type);
		// listen for Online event
		$rootScope.$on('$cordovaNetwork:online', function(event, networkState){
			var onlineState = networkState;
			$rootScope.isOnline = true;
			ToastPlugin.showLongTop('Connection established');
			console.log('Online!');
		})

		// listen for Offline event
		$rootScope.$on('$cordovaNetwork:offline', function(event, networkState){
			var offlineState = networkState;
			$rootScope.isOnline = false;
			ToastPlugin.showLongTop('Connection lost');
			console.log('Offline!');
		})
	  }, false);
	}

    function checkRouteRights(){
      $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams){
        if(toState && toState.data && Array.isArray(toState.data.restrictAccess)){
          var restricted = toState.data.restrictAccess;
          var logged = AuthSrv.isLogged();
          if(logged && restricted.indexOf('notLogged') > -1){
            event.preventDefault();
            $log.log('IllegalAccess', 'State <'+toState.name+'> is restricted to non logged users !');
            $state.go('login');
          } else if(!logged && restricted.indexOf('logged') > -1){
            event.preventDefault();
            $log.log('IllegalAccess', 'State <'+toState.name+'> is restricted to logged users !');
            $state.go('login');
          }
        }
      });
    }

    function setupPushNotifications(){
		
    }
  }
})();

window.BOOTSTRAP_OK = true;
