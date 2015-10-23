/* Requires Crypto.js */
(function(){
  'use strict';
  angular.module('app')
    .factory('AuthSrv', AuthSrv)
   // .factory('AuthInterceptor', AuthInterceptor);

  AuthSrv.$inject = ['$http', 'StorageUtils', 'Config', '$q', '$rootScope','ToastPlugin', 'CommSrv', '$state', 'Utils'];
  function AuthSrv($http, StorageUtils, Config, $q, $rootScope,ToastPlugin, CommSrv, $state, Utils){
    var service = {
      login: login,
      logout: logout,
      isLogged: isLogged,
	  getSession: getSession,
	  checkSession: checkSession,
	  setInstallationId: setInstallationId,
	  setLogoutSession: setLogoutSession,
	  sendAuthenticatedRequest: sendAuthenticatedRequest,
	  sendAuthenticatedUploadImage: sendAuthenticatedUploadImage,
	  hookOn: hookOn
    };
	var hooks = {};
    return service;

	function login(credentials) {
		var loginfn = $rootScope.isOnline ? onlineLogin : offlineLogin;
		console.log($rootScope.isOnline);
		
		////
		var deviceType = (navigator.userAgent.match(/iPad/i))  == "iPad" ? "iPad" : (navigator.userAgent.match(/iPhone/i))  == "iPhone" ? "iPhone" : (navigator.userAgent.match(/Android/i)) == "Android" ? "Android" : (navigator.userAgent.match(/BlackBerry/i)) == "BlackBerry" ? "BlackBerry" : "null";
		if (deviceType == "null") loginfn = onlineLogin;
		////
		return loginfn(credentials).then(function(user){
			
			
			//if (user && user.logged) {
			if (user && user.logged) {
				saveSession(user).then(function(a){
					// Fire hooks on logins
					if (typeof hooks['login'] != 'undefined') {
						var hks = hooks['login'];
						for (var i = 0; i < hks.length; i++) hks[i]();
					}
					//setTimeout(function(){$rootScope.$broadcast('login');},100);
					return a;
				});
			}
			return user;
		});
	}
	
	function checkSession() {
		if ($rootScope.isOnline) {
			return CommSrv.sendRequest('/mobileapi/checksession',{session_id:getSession().sessionId}).then(function(response){ return response }, function(error){
				// Unauthorized. Re login.
				console.log(error);
				if (error.status == 401) {
					var session = getSession();
					session.logged = false;
					return saveSession(session).then(function(){
						//alert('Invalid session. Please re-login');
						$state.go('login');
						throw 'Invalid session';
					});
				}
			});
		} else {
			return Utils.async(function(){})
		}
	}
	
	function getInstallationId() {
		return StorageUtils.get('installation_id');
	}
	
	function setInstallationId(installation_id) {
		return StorageUtils.set('installation_id', installation_id);
	}
	
	
    function onlineLogin(credentials){
	return getInstallationId().then(function(installation_id) {
		if (!installation_id) installation_id = "";
		return CommSrv.sendRequest('/login/mobile_login',{email: credentials.login, keypass: credentials.password, installation_id: installation_id})
		.then(function(res){
			var data = res.data;
			var status = data.st;
			if (status == "1") {
				//Logged in, now save hashed password and user info into local storage
				var user = {};
				user.username = data.u;
				user.sessionId = data.sessionid;
				user.logged = true;
				user.lastLoggedOnline = new Date().getTime();
				user.lastLoggedOffine = null;
				var salt = (new Date()).valueOf().toString();
				StorageUtils.set('profile-'+credentials.login,{'credentials':{
					login: credentials.login,
					password: CryptoJS.SHA3(salt+credentials.password).toString(),
					salt: salt
				},'user':user});
				return user;
			} else {
				// Use throw if we do not want to trigger exception
				throw 'Invalid username or password';
			}
			/*
			if ((user.username != credentials.login) ||	 (user.password != credentials.password)) {
				alert("Wrong username or password");
				user.logged = false;
			} else {
				
			}
			return UserSrv.set(user).then(function(){
			  return user;
			});*/
		  },(function(e,f,g){
		  alert('Error connecting to server');
			console.log("ERROR",e,f,g);}
			
			
		));
	})
    }
	
	function sendAuthenticatedUploadImage(url, fileURL, data){
		var deferred = $q.defer();
		if (isLogged()) {
			if (typeof data == "undefined") data = {};
			data.session_id = getSession().sessionId;
			
			var win = function (r) {
				console.log("Code = " + r.responseCode);
				console.log("Response = " + r.response);
				console.log("Sent = " + r.bytesSent);
				deferred.resolve(r);
			}

			var fail = function (error) {
				console.log("An error has occurred:",error);
				console.log("upload error source " + error.source);
				console.log("upload error target " + error.target);
				deferred.reject(error);
			}

			var options = new FileUploadOptions();
			options.fileKey = "file";
			options.fileName = fileURL.substr(fileURL.lastIndexOf('/') + 1);
			options.mimeType = "image/jpeg";

			/*var params = {};
			params.session_id = ;
			params.value2 = "param";*/
			// FILE UPLOAD IS NAMED FILE. SUPPORT THAT IN journaldataentryadd, with the data entry no, the descripotion
			options.params = data;

			var ft = new FileTransfer();
			var c = 0;
			ft.onprogress = function(progressEvent) {
				if (progressEvent.lengthComputable) {
				  console.log(progressEvent.loaded / progressEvent.total);
				} else {
				  console.log(c++);
				}
			};
			ft.upload(fileURL, encodeURI(CommSrv.getUrl()+url), win, fail, options);
			
			
			
			
			
			/*
			return CommSrv.sendRequest(url,data).then(function(response){ return response }, function(error){
				
				// Unauthorized. Re login.
				if (error.status == 401) {
					var session = getSession();
					session.logged = false;
					return saveSession(session).then(function(){
						alert('Invalid session. Please re-login');
						$state.go('login');
						throw 'Invalid session';
					});
				}
			});*/
		} else setTimeout(function(){deferred.reject('Not logged in');}, 1000);
		return deferred.promise;
	}
	
	function sendAuthenticatedRequest(url, data) {
		if (isLogged()) {
			if (typeof data == "undefined") data = {};
			data.session_id = getSession().sessionId;
			return CommSrv.sendRequest(url,data).then(function(response){ return response }, function(error){
				
				// Unauthorized. Re login.
				if (error.status == 401) {
				//setLogoutSession();
				alert('Invalid session. Please re-login');
					return internalLogout().then(function(){
						throw 'Invalid session';
					});
					//return setLogoutSession()
				}
			});
		} else throw 'Not logged in';
	}
	
	function offlineLogin(credentials){
		return StorageUtils.get('profile-'+credentials.login).then(function(stg){
			if (!stg) {
				//Storage does not exist for this login
				if (credentials.login == "demo" && credentials.password == "demo") {
					var user = {"id":"1","name":"Demo User","username":"demo","password":"demo","profile":"demo profile here","logged":true};
					user.lastLoggedOffine = new Date();
					return user;
					/*return UserSrv.set(user).then(function(){
						return user;
					});*/
				} else alert("Storage does not exist for"+credentials.login);
				
				return {};
			} else {
				//Exists, lets check
				var user = stg.user;
				
				if ((credentials.login != stg.credentials.login) || (CryptoJS.SHA3(stg.credentials.salt+credentials.password).toString() != stg.credentials.password)) {
					// Wrong password
					alert("Invalid username or password");
					user.logged = false;
				} else {
					user.logged = true;
					user.lastLoggedOffine = new Date();
				}
				return user;
			}
		});
	}
	
	function setLogoutSession() {
		var user = getSession();
		user.logged = false;
		return saveSession(user);
	}
	
	
	function internalLogout() {
		var p = setLogoutSession();
		$rootScope.$broadcast('logout');
		runHook('logout');
		$state.go('login', {logout: true});
		return p;
	}
	

    function logout(){
	var user = getSession();
	if ($rootScope.isOnline) {
		if (user.logged) {
			return CommSrv.sendRequest('/mobileapi/logout', {'session_id':user.sessionId}).then(function(){
				internalLogout();
			});
		} else {
			internalLogout();
		}
	} else {
		// Offline. Just set logged to false.
		return Utils.async(function(){
			if (user.logged) {
				/*user.logged = false;
				saveSession(user);
				$rootScope.$broadcast('logout');
				runHook('logout');
				$state.go('login', {logout: true});*/
				internalLogout();
			}
		});
	}
      
    }
	
	function saveSession(user) {
		$rootScope.session = user;
		return StorageUtils.set('session', user);
	}
	
	function getSession() {
		//If app is running
		if ($rootScope.session && (typeof $rootScope.session.logged != 'undefined')) return $rootScope.session;
		
		//If app was not running
		var stored = StorageUtils.getSync('session');
		if (!stored || typeof stored.logged == 'undefined') {
			// No session was present! Probably first time run.
			return false;
		}
		return stored;
	}

    function isLogged(){
		var session = getSession();
		return session ? session.logged : session
    }
	
	// Better way compared to event broadcasting and listening
	function hookOn(hookID, cb){
		if (typeof hooks[hookID] == "undefined") {
			hooks[hookID] = [];
		}
		hooks[hookID].push(cb);
	}
	
	function runHook(hookID, data) {
		if ((typeof hooks != 'undefined') && (typeof hooks[hookID] != 'undefined')) for (var i = 0; i < hooks[hookID].length; i++) if (typeof hooks[hookID][i] == 'function') hooks[hookID][i](data);
	}
	
  }
/*
  AuthInterceptor.$inject = ['$q', '$location', '$log'];
  function AuthInterceptor($q, $location, $log){
    var service = {
      request: onRequest,
      response: onResponse,
      responseError: onResponseError
    };
    return service;

    function onRequest(config){
      // add headers here if you want...
      return config;
    }

    function onResponse(response){
      return response;
    }

    function onResponseError(response){
      $log.warn('request error', response);
      if(response.status === 401 || response.status === 403){
        // user is not authenticated
        $location.path('/login');
      }
      return $q.reject(response);
    }
  }*/
})();
