var Config = (function(){
  'use strict';
  var cfg = {
    appVersion: '~',
    debug: true, // to toggle features between dev & prod
    verbose: true, // should log in console more infos
    track: false, // should send tracking events to a server
    storage: true, // should save data to browser storage
    storagePrefix: 'app-', // prefix all stoarge entries with this prefix
    emailSupport: 'ilyas@rauvetech.com',
    backendUrl: 'http://uoa.hummingsoft.com.my:9090', // 'http://myserver.com/api/v1',
	//updateUrl: 'http://192.168.0.105:8100/',
    /*parse: {
      applicationId: '',
      restApiKey: ''
    },//
    /*gcm: {
      // create project here : https://console.developers.google.com/
      senderID: '78337432198', // Google project number
      apiServerKey: 'AIzaSyC4VSUUx-7VXkburp8uxqqkYSTJKfLPbi8' // used only to send notifications
    },*/
	apns: {
		appId: 'QgdLLD7HW1T62aepQvW08dz5giIZe8UmQnZS9MZ2',
		clientKey: 'CCY75Ud4rH1VsTI7tIWZEsaFp6OMT3F03D7c388W'
	}
  };
  return cfg;
})();
