(function(){
  'use strict';
  angular.module('app')
    .factory('DataSrv', DataSrv)

  DataSrv.$inject = ['$http', 'StorageUtils', 'Config', '$rootScope', 'AuthSrv', '$ionicLoading', 'Utils', 'ToastPlugin', '$q', '$ionicPopup'];
  function DataSrv($http, StorageUtils, Config, $rootScope, AuthSrv, $ionicLoading, Utils, ToastPlugin, $q, $ionicPopup){
	var service = {
		getData: getData,
		synchronize: synchronize,
		pull: _pull,
		push: _push,
		getLastSync: getLastSync,
		hookOn: hookOn
    };
	var hooks = {};
	
	var lastSync = "";
	//var syncing = false;
	var promises = [];
	var getData_busy_promise = null;
	var synchronize_busy_promise = null;
	
	//
    return service;
	
	
	function getData() {
		if (getData_busy_promise != null) return getData_busy_promise;
		var user = AuthSrv.getSession();
		if (!user || !user.username) {
			// User does not exist
			var testd = $q.defer();
			setTimeout(function(){ testd.reject('User does not exist'); },0);
			return testd.promise;
			//return $q.when().then(function(){throw new Error('User does not exist')});
		} else if (!user.logged) {
			// User is not logged in yet.
			var testd = $q.defer();
			setTimeout(function(){ testd.reject('Not logged in'); },0);
			return testd.promise;
			//return $q.when().then(function(){throw new Error('Not logged in');});
		}
		
		getData_busy_promise = StorageUtils.get('profile-data-'+user.username).then(function(profile){
			getData_busy_promise = null;
			if (profile) {
				// Check if online and user last sync is more than 1 hour
				/*if ($rootScope.isOnline) {
					var lastSync = profile[user.username].lastSync;
					var now = new Date().getTime();
					if (((lastSync - now)/1000) > 3600) {
						return synchronize().then(function(r){
							return r;
						});
					}
				}*/
				return profile;
			}
			else {
				// First time sync this data.
				//var test = synchronize(true);
				//console.log('test',test);
				//console.log('HERasdasdERE',StorageUtils.getSync('initialized'));
				
				if (typeof StorageUtils.getSync('initialized') == 'undefined') { console.log('HERERE');return _pull(); }
				
				return synchronize(true).then(function(r){
					console.log('Just finished sync!',r);
					return r;
				});
				//throw new Error('Profile does not exist');
			}
		});
		return getData_busy_promise;
	}
	
	function getSyncTimeNow(status, md5) {
		var now = new Date();
		if (typeof md5 == "undefined") md5 = "";
		if (status == "never") {
			return {
				st: "Never",
				time: null,
				displayTime: "Never",
				md5checksum: md5
			}
		}
		return {
			st:status,
			time:now,
			displayTime: Utils.formatSyncTime(now),
			md5checksum: md5
		}
	}
	
	
	function initProfileData(username) {
		var prof = StorageUtils.getSync('profile-data-'+username);
		if (!prof) {
			// First time run
			prof = {};
			prof.lastSync = getSyncTimeNow("never");
			prof.lastSync.md5checksum = "";
			prof.data = {};
		}
		
		return StorageUtils.set('profile-data-'+username, prof).then(function(){return prof});
	}
	
	function getProfileData(username) {
		var prof = StorageUtils.getSync('profile-data-'+username);
		if (!prof || (typeof prof == "undefined" )) {
			// First time, should initialize
			return initProfileData(username);
		}
		return Utils.async(function(){return prof});
	}
	
	function setProfileData(username, data) {
		var prof = StorageUtils.getSync('profile-data-'+username);
		if (!prof) {
			return initProfileData(username).then(function(newprof){
				prof = data;
				return StorageUtils.set('profile-data-'+username, prof).then(function(){return prof});
			});
		} else {
			prof = data;
			return StorageUtils.set('profile-data-'+username, prof).then(function(){return prof});
		}
	}
	
	function _pull() {
		console.log("PULLING");
		if (!$rootScope.isOnline) throw new Error('Not connected');
		return getLastSync().then(function(lastSync) {
			console.log("WOOT LAST SYNC",lastSync);
			var md5checksum = lastSync['md5checksum'];
			//console.log(lastSync);
			return AuthSrv.sendAuthenticatedRequest('/mobileapi/data',{md5checksum:md5checksum}).then(function(res){
				if ((typeof res == 'undefined') || (typeof res.data == 'undefined')) {
					throw 'Error contacting server';
				}
				console.log('pull completed');
				runHook('pull');
				var data = res.data;
				if (data.st != 1) {
					// Should I throw error?
				} else {
					var user = AuthSrv.getSession();
					
					if (data.versionWas != "latest") {
						var profileData = {
							lastSync: getSyncTimeNow('complete-success', data.md5checksum),
							versionWas: 'not-latest',
							data: data
						}
						return setProfileData(user.username, profileData).then(function(r){
							$rootScope.$broadcast('sync',profileData);
							$rootScope.$broadcast('sync-end');
							runHook('sync',profileData);
							console.log('returning profileData', profileData);
							return profileData;
						});
					} else {
						return getData().then(function(d){
								var profileData = {
									lastSync: getSyncTimeNow('complete-latest', d.lastSync.md5checksum),
									versionWas: 'latest',
									data: d.data
								}
								return setProfileData(user.username, profileData).then(function(r){	
									$rootScope.$broadcast('sync',profileData);
									$rootScope.$broadcast('sync-end');
									runHook('sync',profileData);
									return profileData;
								});
								
							});
					}
				
				/*
					if (data.versionWas == 'latest') {
						// Latest, no need to update
						return getData().then(function(d){
						d.versionWas = 'latest';
						return d;
						});
					} else {
						// Update
						return {
							versionWas: 'not-latest',
							data: data
						}
					}*/
				}
				//console.log('got from data!', res);
				//$rootScope.$emit('sync:pull_complete', res);
			});
		})
	}
	
	
	// HAVE NOT COUNTED FOR EDGE CASES
	function _push() {
		if (!$rootScope.isOnline) throw new Error('Not connected');
		var username = AuthSrv.getSession().username;
		var initializedStorageName = username+'-initialized-entries';
		var uploadedStorageName = username+'-to-upload-entries';
		var publishedStorageName = username+'-published-entries';
		var queries = [StorageUtils.get(initializedStorageName), StorageUtils.get(uploadedStorageName), StorageUtils.get(publishedStorageName)];
		var pushPromise = $q.defer();
		$q.all(queries).then(function(values){
			var inits = (typeof values[0] == 'undefined') ? [] : values[0];
			var uploads = (typeof values[1] == 'undefined') ? [] : values[1];
			var publishs = (typeof values[2] == 'undefined') ? [] : values[2];
			
			var initialized = [];
			var uploaded = [];
			var published = [];
			
			// Either no data, or uninitialized.
			/*if ((inits.length == 0) && (uploads.length == 0) && (publishs.length == 0)) { 
				return [];
			}*/
			
			var totalActions = 0;
			var erredDuringUpload = false;
			
			// CALCULAATE ALL THE TO-s. AFTER FINISH, LOOP THROUGH ALL AND DELETE ACCORDINGLY. AND THEN DO THE PROGRESS THING
			
			var imagesToUpload = {};
			var imagesDescriptionToUpdate = {};
			var imagesToDelete = {};
			
			var imagesUploaded = {};
			var imagesDescriptionUpdated = {};
			var imagesDeleted = {};
			
			var journals = {};
			
			angular.forEach(inits, function(v, k) {
				if (typeof journals[v] == 'undefined') journals[v] = {}; 
				journals[v]['init'] = true;
				totalActions++;
			});
			
			angular.forEach(uploads, function(v, k) {
				if (typeof journals[v] == 'undefined') journals[v] = {};
				journals[v]['upload'] = true;
				totalActions++;
			});
			
			angular.forEach(publishs, function(v, k) {
				if (typeof journals[v] == 'undefined') journals[v] = {};
				journals[v]['publish'] = true;
				totalActions++;
			});
			
			// Get all the datas for the journals
			var js = Object.keys(journals);
			var journalDatas = [];
			var promises = [];
			for (var i = 0; i < js.length; i++) promises.push(StorageUtils.get(js[i]));
			promises.push(getData());
			
			var finishAll = $q.all(promises).then(function(values){
				
				// Not a very good practice here, but saved some codes. Values is not all just journals, the last index is a getData().
				var allData = values[values.length-1];
				
				for (var i = 0; i < values.length-1; i++) {
					journalDatas.push({jid:js[i], data:values[i]});
				}
				//journalDatas = values;
				var arr = [];
				for (var i = 0; i < js.length; i++) {
					journals[js[i]]['data'] = values[i];
					arr.push({
						journal: journals[js[i]],
						jid: js[i]
					});
					
					// Undefined if the journal does not have any data or images yet, ie: just initialized.
					if (typeof values[i] == 'undefined') continue;
					totalActions += values[i].deletedImages.length;
					totalActions += values[i].images.length;
					console.log('uploads',journals[js[i]]['data']);
				}
				
				pushPromise.notify({
					'action': 'push-start',
					'value': totalActions
				});
				
				return arr.reduce(function(promise,item){
					return promise.then(function(){
						var tempdefer = $q.when();
						var journal = item.journal;
						var jid = item.jid;
						var ids = jid.split('-');
						var project_no = ids[ids.length-3];
						var journal_no = ids[ids.length-2];
						var data_entry_no = ids[ids.length-1];
						var journal_original_data = allData['data']['projects'][project_no]['journals'][journal_no];
						
						
						
						if (journal.init) {
							//Init journal
							//console.log('initing', data_entry_no);
							tempdefer = AuthSrv.sendAuthenticatedRequest('/mobileapi/init_data_entry', {id: data_entry_no}).then(function(s){
								pushPromise.notify({'action': 'push-progress', 'value': 1, 'message':'Uploading '+journal_original_data.journal_name});
								console.log("THIS IS",item);
								if ((typeof s == 'undefined') || (s.status != 200)) { console.log('Error initializing entry (server error)',item.jid,s); throw 'Error contacting server'; return; }
								if ((typeof s.data.st != 'undefined') && (s.data.st == 1)) {
									initialized.push(jid);
									
								} else { console.log('Error initializing entry (maybe already initialized)',item.jid,s); if (s.data.st == 2) initialized.push(jid); } 
							}, function(e){console.log('Error initializing entry',item.jid,e); erredDuringUpload = true;});
						}
						if (journal.upload) {
							console.log('uploading');
							tempdefer = tempdefer.then(function(){
								// Upload data entry
								var data = [];
								var anotherdefer = $q.when();
								if (typeof journal.data == 'undefined') { console.log('Upload cancel, no data in data entry'); } else {
									angular.forEach(journal.data.entries, function(v,k){
										var attbId = k.split('_');
										attbId = attbId[attbId.length-1];
										data.push({attb: attbId, value: v})
									})
									anotherdefer = AuthSrv.sendAuthenticatedRequest('/mobileapi/upload', {id: data_entry_no, attbs:JSON.stringify(data)}).then(function(s){
										pushPromise.notify({'action': 'push-progress', 'value': 1, 'message':'Uploading '+journal_original_data.journal_name});
										if ((typeof s == 'undefined') || (s.status != 200)) { console.log('Error uploading entry (server error)',item.jid,s); throw 'Error contacting server'; return; }
										if ((typeof s.data.st != 'undefined') && (s.data.st == 1)) {
											uploaded.push(jid);
										} else { console.log('Error uploading entry (maybe already published)',item.jid,s); if (s.data.st == 2) uploaded.push(jid); } 
									}, function(e){console.log('Error uploading entry',item.jid,e); erredDuringUpload = true;});
								}
								// Upload image entry
								return anotherdefer.then(function(){
									if ((typeof journal.data == 'undefined') || (typeof journal.data.images == 'undefined')) return $q.when();
									var images = journal.data.images;
									
									return images.reduce(function(p,it){
										return p.then(function(){
										if ((it.internal) && (typeof it.data_entry_pict_no == 'undefined')) {
											var fileURL = it.src;
											var description = it.description;
											console.log('uploading image',it);
											// AFTER THIS SHOULD 1) DELETE IMAGE RECORD IN JOURNAL DATA, 2) DELETE PHYSICAL IMAGE, 3) PUBLISH 4) DO THAT PROGRESS THING 5) FIX THAT SYNC THING
											// Fix it with 1) Auto delete all entry records after each successful push. 2) Only save the images where there is change!!!
											
											return AuthSrv.sendAuthenticatedUploadImage('/mobileapi/upload_image', fileURL, {dataentryno1: data_entry_no, description:description, ismobile: 1}).then(function(s){ 
												//throw "TESTERROR";
												pushPromise.notify({'action': 'push-progress', 'value': 1, 'message':'Uploading '+journal_original_data.journal_name});
												if ((typeof s == 'undefined') || (typeof s.data == 'undefined')) { console.log('Unable to upload image',fileURL); return; }
												if (s.data.st == 0) { console.log('Error at server when uploading image',fileURL); throw 'Error contacting server'; return }
												if (typeof imagesUploaded[jid] == 'undefined') imagesUploaded[jid] = [];
												imagesUploaded[jid].push(fileURL);
												//if (uploaded.indexOf(item.jid) == -1) uploaded.push(item.jid);
											}, function(e){console.log('Rejected from uploading image (Maybe image is non-existant?)',e); erredDuringUpload = true;});
											
										} else if (typeof it.data_entry_pict_no != 'undefined') {
											// Update description here
											var description = it.description;
											return AuthSrv.sendAuthenticatedRequest('/mobileapi/update_image_description', {id: it.data_entry_pict_no, description:description, pict_seq_no: it.pict_seq_no}).then(function(s){
												pushPromise.notify({'action': 'push-progress', 'value': 1, 'message':'Uploading '+journal_original_data.journal_name});
												if ((typeof s == 'undefined') || (s.status != 200)) { console.log('Error updating description (server error)',item.jid,s); throw 'Error contacting server'; return; }
												if ((typeof s.data.st != 'undefined') && (s.data.st == 1)) {
													//if (uploaded.indexOf(item.jid) == -1) uploaded.push(item.jid);
													if (typeof imagesDescriptionUpdated[jid] == 'undefined') imagesDescriptionUpdated[jid] = [];
													imagesDescriptionUpdated[jid].push(it.data_entry_pict_no);
												} else { console.log('Error updating description (maybe already published)',item.jid,s) } 
											}, function(e){console.log('Error updating description',item.jid,e); erredDuringUpload = true;});
										} 
										else { console.log('Alert! special case!!!'); return $q.when(); }
										});
									}, $q.when())
									
								// Delete images
								}).then(function(){
									if ((typeof journal.data == 'undefined') || (typeof journal.data.deletedImages == 'undefined')) return $q.when();
									var deleted = journal.data.deletedImages;
									
									return deleted.reduce(function(p,it){
										return p.then(function(){
											return AuthSrv.sendAuthenticatedRequest('/journaldataentryadd/deleteimage',{
												id: it,
												dataid: data_entry_no
											}).then(function(a){
												if (typeof a == 'undefined') {	
													//alert('Cannot delete file on server');
													//after.reject();
													console.log ('Cannot delete file on server');
													throw 'Error contacting server';
												}
												pushPromise.notify({'action': 'push-progress', 'value': 1, 'message':'Uploading '+journal_original_data.journal_name});
												
												//console.log(a.data.st);
												if (a.data.st == 1) {
													//success = true;
													console.log('Deleted image');
													if (typeof imagesDeleted[jid] == 'undefined') imagesDeleted[jid] = [];
													imagesDeleted[jid].push(it);
												} else {
													console.log('Error deleting image', a);
												}
												//after.resolve();
											}, function(a) {
												//alert('Error deleting image');
												erredDuringUpload = true;
												console.log('Error deleting image', a);
												//////after.resolve();
											})
										})
										
									},$q.when())
									//alert('deleting');
									//console.log(images);
									/*
									*/;
								}, function(e){console.log('Upload error bro?',e); throw e;});
								
								
							});
						}
						
						if (journal.publish) {
							tempdefer = tempdefer.then(function(){
								console.log('publisheeeing');
								return AuthSrv.sendAuthenticatedRequest('/mobileapi/publish', {
									id: data_entry_no,
									is_mobile: 1
								}).then(function(){
									pushPromise.notify({'action': 'push-progress', 'value': 1, 'message':'Uploading '+journal_original_data.journal_name});
									published.push(jid);
								}, function(e){ console.log('Error during publish',e); erredDuringUpload = true;});
							})
						}
						
						return tempdefer
						//console.log('Sending request',journal);
						//return $q.when()
					});
					//return AuthSrv.sendAuthenticatedRequest('/mobileapi/update_data_entry', item);
				}, $q.when());
				
				/*
				angular.forEach(journals, function(v, k) {
				if (v.init) {
					//console.log('Init!',);
					$.journals
					
				}
				});*/
			});
			
			finishAll.then(function(){
				// Finished. Time to clean up internal datas
				console.log('wooot!');
				if (!erredDuringUpload) {
				//console.log(inits, uploads, publishs);
				//console.log(initialized, uploaded, published);
				console.log(imagesUploaded, imagesDescriptionUpdated, imagesDeleted);
				
				
				var doneStuff = [];
				var promises = [];
				
				
				
				// Delete all journal datas, images.
				console.log('Before',angular.copy(journalDatas));
				
				for (var i = 0; i < journalDatas.length; i++) {
					var jid = journalDatas[i].jid;
					var data = journalDatas[i].data;
					
					// If data is uploaded, remove data entries
					if (uploads.indexOf(jid) != -1) { data.entries = {}; }
					
					// If image is uploaded, remove stuff NOT DONE!!!!
					if (typeof imagesUploaded[jid] != 'undefined') {
						//data.aimages = {};
						var j = data.images.length;
						while (j--) {
						//for (var j = 0; j < data.images.length; j++) {
							if (imagesUploaded[jid].indexOf(data.images[j].src) != -1) {  data.images.splice(j,1); };
						}
						
						/*for (var j = 0; j < imagesUploaded[jid].length; j++) {
							imagesUploaded[jid][j].indexOf(data)
						}*/
					}
					
					
					// If image deletion
					if (typeof imagesDeleted[jid] != 'undefined') {
						var j = data.images.length;
						while (j--) {
							var pict_no = data.images[j].data_entry_pict_no;
							if (imagesDeleted[jid].indexOf(pict_no) != -1) { data.images.splice(j,1); delete data.aImages[pict_no]; data.deletedImages.splice(data.deletedImages.indexOf(pict_no),1) };
						}
					}
					
					// If image description
					if (typeof imagesDescriptionUpdated[jid] != 'undefined') {
						var j = data.images.length;
						while (j--) {
							var pict_no = data.images[j].data_entry_pict_no;
							console.log('Dete',pict_no);
							if (imagesDescriptionUpdated[jid].indexOf(pict_no) != -1) { data.images.splice(j,1); delete data.aImages[pict_no] };
						}
					}
					
					journalDatas[i].data = data;
					promises.push(StorageUtils.set(jid, data));
				}
				
				$q.all(promises).then
				console.log('After',journalDatas);
				
				// THE SORTING IS IMPORTANT, I THINK
				for (var i = 0; i < initialized.length; i++) {
					if (doneStuff.indexOf(initialized[i]) == -1) doneStuff.push(initialized[i])
					inits.splice(inits.indexOf(initialized[i]), 1);
				}
				
				for (var i = 0; i < uploaded.length; i++) { 
					if (doneStuff.indexOf(uploaded[i]) == -1) doneStuff.push(uploaded[i]) 
					uploads.splice(uploads.indexOf(uploaded[i]), 1);
					//console.log(journalDatas);
				}
				
				for (var i = 0; i < published.length; i++) { 
					if (doneStuff.indexOf(published[i]) == -1) doneStuff.push(published[i])
					publishs.splice(publishs.indexOf(published[i]), 1); 
				}
				
				promises = promises.concat([StorageUtils.set(initializedStorageName, inits), StorageUtils.set(uploadedStorageName,uploads), StorageUtils.set(publishedStorageName,publishs)])
				
				
				//var promises = 
				
				
				console.log(inits, uploads, publishs);
				
				$q.all(promises).then(function(){
					pushPromise.notify({'action': 'push-end'});
					pushPromise.resolve();
				});
				
				} else {
					console.log('Erred during upload! Aborting saves');
					pushPromise.notify({'action': 'push-end'});
					pushPromise.resolve();
					return;
				}
			}, function(e){
				console.log('In FINISHALL ERROERDEDDE!');
				pushPromise.reject(e);
			})
			
			
			
			
			
			//console.log(journals);
		});
		
		return pushPromise.promise;
		/*
		return;
		
		
		
		return StorageUtils.get(uploadedStorageName).then(function(d){
			if ((typeof d == 'undefined') || (typeof d['dirty'] == 'undefined')) return false;
			var promises = [];
			angular.forEach(d['dirty'], function(val, key) {
				//console.log(val,key);
				if (val) {
					promises.push(StorageUtils.get(key));
				}
			});
			
			$q.all(promises).then(function(values){
				console.log('Going to push all these',values);
				// Data entries
				var data_entries = values.map(function(r){return r.entries});
				data_entries.reduce(function(promise,item){
					console.log('Sending request',item);
					return promise.then(function(){return AuthSrv.sendAuthenticatedRequest('/mobileapi/update_data_entry', item);});
				}, $q.when());
				
			});
		});*/
	}
	
	
	
	function getLastSync() {
		var user = AuthSrv.getSession();
		var oldprof = StorageUtils.getSync('profile-data-'+user.username);
		if (oldprof && oldprof.lastSync) {
			return Utils.async(function(){return oldprof.lastSync});
			//return Utils.async(function(){ return oldprof.lastSync; });//getData().then(function(r){return r.lastSync});
		} else {
			return Utils.async(function(){return getSyncTimeNow('never')});
			
			//return 
		}
	}
	/*
	function showLoaderSynchronize() {		
		$ionicLoading.show({
			template: '<ion-spinner icon="android"></ion-spinner><p>Synchronizing</p>'
		});
	}*/
	
	
	function synchronize() {
	// PULL BEFORE AND AFTER SYNCHRONIZE, DO THE DEFERRED NOTIFY, DRAW PROGRESS PAGE
	
		//Busy, return promise
		if (synchronize_busy_promise != null) return synchronize_busy_promise.promise;
		
		/*if (syncing) {
			// BUSY!
			var newpromise = $q.defer();
			promises.push(newpromise);
			return newpromise.promise;
		}*/
		if (!$rootScope.isOnline) {
			var t = $q.defer();
			setTimeout(function(){alert('Not connected');$t.reject('Not connected');}, 1);
			return t.promise;
		}
		
		//if ((typeof showLoader == 'boolean') && showLoader) showLoaderSynchronize();
		
		//syncing = true;
		console.log('syncing!');
		//ToastPlugin.showLongTop('Checking for update');
		$rootScope.$broadcast('sync-start');
		var user = AuthSrv.getSession();
		var progressDeferred, totalProgress = 1, updateProgress = 0, popupObject;
		synchronize_busy_promise = $q.defer();
		//try {
		
		_push().then(
			
			function(){
				synchronize_busy_promise.notify({action:'pull-start'})
			},
			function(e){ synchronize_busy_promise.reject(e); synchronize_busy_promise = null; throw e; },
			function(n){
				synchronize_busy_promise.notify(n);
			})
			.then(function(){
			return _pull().then(function(res) {
			
			if ((typeof res == 'undefined') || (typeof res.data == 'undefined')) {
				// Some error occured.
				throw "Error contacting server";
			}
			synchronize_busy_promise.notify({action:'pull-end'})
			var username = user.username;
			var data = res.data;
			
			return res;
			//console.log(res);
			// Version was not latest, need to write to data. Block user for a while.
			
			
			
		/*
			var currentProfileData = StorageUtils.getSync('profile-data-');
			if (!currentProfileData) currentProfileData = {};
			currentProfileData[user.username] = res;
			
			// SAVE AND GET LAST SYNC!
			var now = new Date();
			currentProfileData[user.username].lastSync = getSyncTimeNow("complete-success");
			console.log("Finished sync!",res);
			return StorageUtils.set('profile-data-', currentProfileData).then(function(){ 
				moment.locale('en');
				$rootScope.$broadcast('sync',currentProfileData[user.username].lastSync);
				$ionicLoading.hide();
				return res;
				
			});*/
		}, function(error) {
			if (synchronize_busy_promise != null) synchronize_busy_promise.reject(error);
			//console.log("ERROR RUNNED HERE TOO",error);
			//syncing = false;
			//console.log('Sync ended with error!');
			//$rootScope.$broadcast('sync-end');
			//$ionicLoading.hide();
			//synchronize_busy_promise.reject(error);
			//synchronize_busy_promise = null;
			//console.log("Rejecting PROMISE");
			//throw error
			//promises.pop().reject(error);
			
		})
		})
		
		//} catch (e) {console.log('Catching',e);}
		/*.then(function(){
			_push();
		});*/
		
		synchronize_busy_promise.promise.then(function(){},function(e){ 
			if (synchronize_busy_promise != null) {
				synchronize_busy_promise.reject(e);
				synchronize_busy_promise = null; 
			}
			
			//if (typeof popupObject != 'undefined') popupObject.close();
			
			progressDeferred.promise.then(function(stuff){
				if (e == 'Error contacting server') {
					$rootScope.popupMessage = 'Disconnected from server';
					setTimeout(function(){ stuff.popup.close(); }, 2000)
				} else { stuff.popup.close(); }
			});
			console.log("ERROR RUNNED HERE",e);
			},function(n){
				if ((typeof progressDeferred == 'undefined')) {
					//var promise = runPopup();
					progressDeferred = $q.defer();
					runPopup().then(function(p){ progressDeferred.resolve(p); });
				} 
				
				//console.log('GOT NOTIEFED',n);
				progressDeferred.promise.then(function(stuff){
					var progress = stuff.progress;
					var popup = stuff.popup;
					popupObject = popup;
					console.log('After popup',n);
					var action = n.action;
					switch(action) {
						case 'push-start':
							$rootScope.popupMessage = "";
							totalProgress += n.value;
							break;
						
						case 'push-progress':
							updateProgress += n.value;
							if (typeof n.message != 'undefined') $rootScope.popupMessage = n.message;
							break;
							
						case 'push-end':
							break;
						
						case 'pull-start':
							$rootScope.popupMessage = 'Downloading data';
							break;
							
						case 'pull-end':
							updateProgress += 1;
							$rootScope.popupMessage = 'Completed';
							break;
					}
					var percent = (totalProgress == 0) ? 0: (updateProgress/totalProgress)*100;
					if ((percent >= 100) || (n.action == 'pull-end')) { 
						setTimeout(function(){
							popup.close();
							synchronize_busy_promise.resolve();
							synchronize_busy_promise = null;
						}, 1000);
					}
					progress.update(percent);
					console.log(updateProgress,totalProgress,percent,popup);
				})
			
			
			
		});
		//console.log('returning promise', synchronize_busy_promise.promise);
		return synchronize_busy_promise.promise;
	}
	
	function runPopup() {
		$rootScope.popupMessage = "";
		var popup = $ionicPopup.show({
			 template: '<div id="loading_popup" style="height:160px; text-align:center;"></div><p style="text-align:center">&nbsp;{{popupMessage}}&nbsp;</p>',
			 title: 'Synchronizing',
			 subTitle: '',
			 cssClass: 'no-opacity',
			 buttons: [
			   //{ text: 'Cancel' },
			   /*{
				 text: '<b>Save</b>',
				 type: 'button-positive',
				 onTap: function(e) {
				   if (!$scope.vm.editDescription) {
					 //don't allow the user to close unless he enters wifi password
					 e.preventDefault();
				   } else {
					 return $scope.vm.editDescription;
				   }
				 }
			   },*/
			 ]
		   });
		var progressDeferred = $q.defer();
		var pop = function () {
			var n, id, progress;
			
			progress = new CircularProgress({
				radius: 70,
				strokeStyle: '#81CE97',
				lineCap: 'round',
				lineJoin: 'round',
				lineWidth: 8,
				shadowBlur: 0,
				shadowColor: 'yellow',
				text: {
				  font: 'bold 15px arial',
				  fillStyle: '#81CE97',
				  shadowBlur: 0
				},
				initial: {
				  strokeStyle: '#E5E5E5',
				  lineCap: 'round',
				  lineJoin: 'round',
				  lineWidth: 8,
				  shadowBlur: 0,
				  shadowColor: 'black'
				}
			});

			document.getElementById('loading_popup').appendChild(progress.el);
			progressDeferred.resolve({
				progress: progress,
				popup: popup
			})
		  /*n = 0;
		  id = setInterval(function () {
			if (n == 100) clearInterval(id);
			progress.update(n++);
		  }, 30);*/
		}
		
		/*progressDeferred.promise.then(function(p){
			callback(p);
		});*/
		
		setTimeout(pop,300);
		return progressDeferred.promise;
	}
	
	
	
	function runHook(hookID, data) {
		if ((typeof hooks != 'undefined') && (typeof hooks[hookID] != 'undefined')) for (var i = 0; i < hooks[hookID].length; i++) if (typeof hooks[hookID][i] == 'function') hooks[hookID][i](data);
	}
	
	function hookOn(hookID, cb){
		if (typeof hooks[hookID] == "undefined") {
			hooks[hookID] = [];
		}
		hooks[hookID].push(cb);
	}
  }

 
})();
