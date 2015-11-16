(function(){
  'use strict';
  angular.module('app')
    .factory('PushSrv', PushSrv)

  PushSrv.$inject = ['Config', '$q', '$rootScope','ToastPlugin', '$state', 'AuthSrv', 'DataSrv'];
  function PushSrv(Config, $q, $rootScope, ToastPlugin, $state, AuthSrv, DataSrv){
    
	var listeners = {};
	var initialized = false;
	var service = {
		setInstallationId: setInstallationId,
		listenTo: listenTo,
		unlistenTo: unlistenTo,
		initialize: initialize
    };
    return service;
	

	function setInstallationId() {
		if (typeof ParsePushPlugin == "undefined") return;
		ParsePushPlugin.register({},
		function() {
			console.log('Successfully registered device!');
		}, function(e) {
			console.log('error registering device: ' + e);
		});
		
		ParsePushPlugin.getInstallationId(function(id) {
			if ((!$rootScope.isOnline) || (!AuthSrv.isLogged())) return false;
			AuthSrv.isOnlineLogged().then(function(isLogged){
				console.log($rootScope.isOnline, isLogged);
				if ($rootScope.isOnline && isLogged) {
				
					return AuthSrv.sendAuthenticatedRequest('/mobileapi/set_installation_id', {
						installation_id: id,
						session_id: AuthSrv.getSession().sessionId
					}).then(function(){
						console.log('Set install id',id);
						ParsePushPlugin.subscribe('Everyone', function(msg) {
							console.log('Channel Set!');
						}, function(e) {
							console.log('Error in setting channel');
						});
						
						
						ParsePushPlugin.on('openPN', function(pn){
							//you can do things like navigating to a different view here
							//alert('Open PN alert!!!!');
							//ToastPlugin.showLongTop(pn.alert);
							/*$cordovaToast.show(pn.alert+' from opening a PN!', 'long', 'top').then(function(success) {
								console.log("The toast was shown");
							}, function (error) {
								console.log("The toast was not shown due to " + error);
							});*/
							//console.log('Yo, I get this when the user clicks open a notification from the tray');
						});
						
						ParsePushPlugin.on('receivePN', function(pn){
							//alert('Receive PN alert!!!!');
							if ((typeof pn.data != 'undefined') && (typeof pn.data.pnID != 'undefined')) {
								// Call listener callback
								if (typeof listeners[pn.data.pnID] != 'undefined') {
									console.log('Calling back',pn.data.pnID);
									if (listeners[pn.data.pnID].callback) listeners[pn.data.pnID].callback(pn);
								} else {
									console.log('Listener not found for pnID',pn.data.pnID);
								}
							}
							
							if ((typeof pn.data != 'undefined') && (typeof pn.data.sync != 'undefined')) {
								setTimeout(function(){console.log('Sync from PN!', JSON.stringify(pn.data)); DataSrv.synchronize();}, 10);
							}
							console.log('Received PN: ',JSON.stringify(pn));
							// If silentforeground, dont display it.
							if ((typeof pn.data != 'undefined') && (typeof pn.data.silentforeground != 'undefined') && (!pn.data.silentforeground)) ToastPlugin.showLongTop(pn.alert);
						});
						
						
					})
				}
			})
		
			
			
			//
			/*
			console.log('Installation ID',JSON.stringify(id));
			$http({
				method: 'POST',
				url: 'http://192.168.1.52/mobileapi/set_installation_id?id='+id,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				transformRequest: function(obj) {
					var str = [];
					for(var p in obj)
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
					return str.join("&");
				},
				data: {
					installation_id: id,
					session_id: getSession().sessionId
				}
			}).then(function(res){
				console.log('registerede!',res.data);
			},function(e){console.log('Error installation ID to Server!',e)});
		}, function(e) {
			alert('error Installation ID from Parse!');
		}) ;
		
		/*
		//alert('Setting subscriptions');
		
		ParsePushPlugin.getInstallationId(function(id) {
			console.log('Installation ID',JSON.stringify(id));
		}, function(e) {
			alert('error Installation ID');
		});*/
		
		
		}, function(e){console.log("Caught error when setting up installation ID",e);})
	}
	
	
	
	function initialize() {
		if (initialized) return false;
		setInstallationId();
		
		listenTo('logout', function(pn){
			AuthSrv.setLogoutSession();
			AuthSrv.logout();
			alert('You have been logged out from this device');
		});
		
		AuthSrv.hookOn('logout', function(){
			// Should unregister parse here
			unlistenTo('logout'); 
			initialized = false;
		});
		
		initialized = true;
	}
	
	function listenTo(pnID, cb) {
		if (typeof ParsePushPlugin != "undefined") {
			listeners[pnID] = {};
			listeners[pnID].callback = cb;
		} else {
			
		}
	}
	
	function unlistenTo(pnID) {
		listeners[pnID] = {};
	}
	
	
  }
})();
