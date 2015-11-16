(function(){
  'use strict';
  angular.module('app')
	.controller('TestCtrl', TestCtrl);
	
  function TestCtrl($rootScope, $scope, $state, AuthSrv, $window, $q, ToastPlugin, DataSrv, $ionicLoading, StorageUtils, UpdateSrv) {
	var vm = {};
	$scope.vm = vm;
	var test = kludjstest;
	vm.testGroups = [];
	vm.atestGroups = {};
	vm.testData = {};
	
	vm.username = 'integrationtestuser@hummingsoft.com.my';
	vm.password = 'demo';
	vm.journalDataEntryName = 'Integration Test Journal Data';
	vm.journalImageEntryName = 'Integration Test Journal Image';
	vm.journalImageDescription = 'Integration Test Image Description';
	
	vm.storageNameInitialize = vm.username+'-initialized-entries';
	vm.storageNameUpload = vm.username+'-to-upload-entries';
	vm.storageNamePublish = vm.username+'-published-entries';
	
	vm.storageNameProfile = 'profile-'+vm.username;
	vm.storageNameProfileData = 'profile-data-'+vm.username;
	
	vm.storedSession = null;
	
	vm.isRunning = false;
	
	window.myscope = $scope;
	
	
	function isDefined(target, path) {
		if (typeof target != 'object' || target == null) {
			return false;
		}

		var parts = path.split('.');

		while(parts.length) {
			var branch = parts.shift();
			if (!(branch in target)) {
				return false;
			}

			target = target[branch];
		}

		return true;
	}
	
	
	
	function runTest(testGroup) {
		if (typeof vm.atestGroups[testGroup] != 'undefined') {			
			console.log('Running',testGroup, vm.atestGroups[testGroup]);
			var tg = vm.atestGroups[testGroup];
			for (var i = 0; i < tg.tests.length; i++) {
				tg.tests[i].func('');
			}
		}
	}
	
	
	
	
	
	function setupTests() {
		vm.atestGroups = {};
		vm.testGroups = [];
		
		test(function(e, test, data) {
			if (e == 'begin') vm.isRunning = true;
			if (e == 'end') vm.isRunning = false;
			
			switch (e) {
				case 'begin':
				case 'end':
				case 'pass':
				case 'fail':
				case 'except':
					//console.log(e, test, vm.allTests[test],'asdasdadasdasd');
					
					var testGroup = vm.allTests[test].testGroup;
				break;
				case 'default':
				break;
			}
			
			if (isDefined(data, 'message')) var msg = data.message;
			switch (e) {
				case 'begin':
					//console.log('Test started: ' + test);
					vm.atestGroups[testGroup].results[test] = {
						//passed: true,
						//loading: true,
						status: 'running',
						test: test
					}
					break;
				case 'end':
					console.log('Test finished: ' + test);
					break;
				case 'pass':
					console.log('Assertion passed: ' + test);
					vm.atestGroups[testGroup].results[test] = {
						status: 'passed',
						test: test
					};
					vm.atestGroups[testGroup].passed++;
					break;
				case 'fail':
					console.log('Assertion failed: ' + test + ':' + msg);
					vm.atestGroups[testGroup].results[test] = {
						status: 'failed',
						test: test
					}
					
					
					/*vm.atestGroups[testGroup].results.push({
						passed: false,
						test: test
					})*/
					break;
				case 'except':
					console.log('Unhandled exception: ' + test + ':' + msg);
					vm.atestGroups[testGroup].results[test] = {
						status: 'failed',
						test: test
					}
					break;
			}
		});
		
		var installTest = function(){
			test('Setup environment', function(next){
				vm.storedSession = StorageUtils.getSync('session');
			
				AuthSrv.sendAuthenticatedRequest('/mobileapi/setup_test_environment').then(function(a){
					
					var promises = [];
					
					promises.push(StorageUtils.set(vm.storageNameInitialize, []));
					promises.push(StorageUtils.set(vm.storageNameUpload, []));
					promises.push(StorageUtils.set(vm.storageNamePublish, []));
					
					promises.push(StorageUtils.remove(vm.storageNameProfile));
					promises.push(StorageUtils.remove(vm.storageNameProfileData));
					
					return $q.all(promises).then(function(){
						var condition = (
							isDefined(a, 'data.status') &&
							a.data.status == true
						);
						ok(condition);
						next();
					}, function(e){ok(false); next();});
					
					
				
				}, function(e){
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		
		var uninstallTest = function(){	
			test('Cleanup environment', function(next){
				StorageUtils.set('session', vm.storedSession);
				$rootScope.session = vm.storedSession;
				AuthSrv.sendAuthenticatedRequest('/mobileapi/unsetup_test_environment').then(function(a){
					var condition = typeof a != undefined;
					
					StorageUtils.remove(vm.storageNameInitialize);
					StorageUtils.remove(vm.storageNameUpload);
					StorageUtils.remove(vm.storageNamePublish);
					
					StorageUtils.remove(vm.storageNameProfile);
					StorageUtils.remove(vm.storageNameProfileData);
					
					ok(condition);
					next();
					/*AuthSrv.logout(true).then(function(){
						
						
						
					});*/
					
					
					
				}, function(e){
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		
		var pingTest = function() {
			test('Ping', function(next){
				AuthSrv.ping().then(function(s){
					var condition = (
						isDefined(s, 'data.ping') &&
						s.data.ping == 'pong'
					);
					ok(condition);
					next();
				}, function(e){
					ok(false, {message: e});
					next();
				});
			}, true);
		}
		
		var loginTest = function() {test('Login', function(next) {
				AuthSrv.login({login:vm.username, password:vm.password}, true).then(function(a){
					var condition = (
						isDefined(a, 'username') &&
						isDefined(a, 'sessionId') &&
						isDefined(a, 'logged') &&
						a.logged == true &&
						a.sessionId != ''
					);
					vm.testData['username'] = a.username;
					setTimeout(function(){ok(condition);
					//$state.go('app.tabs.test');
					next();}, 5);
				}).catch(function(e){
					'Invalid username or password';
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		var logoutTest = function(){
			test("Logout", function(next) {
				AuthSrv.logout(true).then(function() {
					console.log('Logged out!!!');//
					return AuthSrv.login({login:vm.username, password:vm.password}, true).then(function(){
						console.log('LOGGED IN AGAIN', $rootScope.session);
						setTimeout(function(){ok(true); console.log('HERER1 NExST');next();}, 10);
					});
				}, function(e) {
					console.log('HERER2 NExST');
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		var pullTest = function(){
		test('Pull', function(next) {
			console.log('Running puylll yo!');
			DataSrv.pull().then(function(a){
				if (!isDefined(a, 'data')) { ok(false, {message: 'Server error'}); next(); return false;}
				var data = a.data;
				
				var condition = (
					isDefined(data, 'alerts') &&
					isDefined(data, 'lookups') &&
					isDefined(data, 'projects') &&
					isDefined(data, 'reminders') &&
					isDefined(data, 'uoms') && 
					Object.keys(data.projects).length == 1
				);
				
				if (condition) {
					var project = data.projects[Object.keys(data.projects)[0]];
					condition = project && isDefined(project, 'journals') && isDefined(project, 'project_name') && isDefined(project, 'project_no');
					if (condition) {
						var journals = project['journals'];
						angular.forEach(journals, function(v,k){
							if (!condition) return false;
							condition = (
								isDefined(v, 'data_attributes') &&
								isDefined(v, 'data_entries') &&
								isDefined(v, 'dependency') &&
								isDefined(v, 'journal_name') &&
								isDefined(v, 'journal_no')
							)
							if (condition) {
								angular.forEach(v.data_entries, function(v2, k2){
									if (!condition) return false;
									condition = (
										isDefined(v2, 'count') &&
										isDefined(v2, 'data_attributes') &&
										isDefined(v2, 'data_entry_images') &&
										isDefined(v2, 'data_entry_no') &&
										isDefined(v2, 'dependency') &&
										isDefined(v2, 'frequency_detail_name') &&
										isDefined(v2, 'frequency_period') &&
										isDefined(v2, 'is_image') &&
										isDefined(v2, 'reject_notes')
									);
								})
							}
						});
					}
					
				}
				
				setTimeout(function(){ok(condition); $rootScope.$digest();next();}, 1000);
			}, function(e){
				ok(false, {message: e});
				next();
			});
		}, true);
		}
		
		var initializeTest = function(){
			test('Initialize', function(next){
				DataSrv.getData().then(function(d){
					var condition = (
						isDefined(d, 'data') &&
						isDefined(d.data, 'projects')
					);
					if (condition){
						var projects = [], journals = [], entries = [], storage = [];
						
						angular.forEach(d.data.projects, function(projv, projk){
							projects.push(projv);
							angular.forEach(projv.journals, function(jourv, jourk){
								journals.push(jourv);
								angular.forEach(jourv.data_entries, function(entryv, entryk){
									entries.push(entryv);
									storage.push('project-'+projk+'-'+jourk+'-'+entryk);
								});
							});
						})
						
						condition = (
							condition && 
							projects.length > 0 &&
							journals.length > 0 &&
							entries.length > 0 &&
							storage.length > 0
						);
						
						//var initializedStorage = storage;
						
						if (condition) {
							StorageUtils.set(vm.storageNameInitialize, storage).then(function(){
								DataSrv.push().then(function(b){
									var promises = [];
									for (var i = 0; i < storage.length; i++) {
										promises.push(StorageUtils.remove(storage[i]));
									}
									return $q.all(promises, function(values){}).then(function(){
										return DataSrv.pull();
									});
								}, function(e){
									ok(false, {message: e});
									next();
								}).then(function(d){
									for (var i = 0; i < storage.length; i++) {
										var s = storage[i];
										var st = s.split('-');
										var pid = st[1];
										var jid = st[2];
										var eid = st[3];
										
										condition = (
											condition &&
											isDefined(d, 'data.projects.'+pid+'.journals.'+jid+'.data_entries.'+eid) &&
											isDefined(d, 'data.projects.'+pid+'.journals.'+jid+'.data_entries.'+eid) &&
											d.data.projects[pid].journals[jid].data_entries[eid].count > 0 || d.data.projects[pid].journals[jid].data_entries[eid].is_image == '1'
										);
										
										if (!condition) {ok(false, {message: 'Error in data'}); next(); return false; }
										
									}
									
									ok(condition);
									next();
									
								})
								
							});
						} else {
							ok(false);
							next();
						}
						
					} else {
						ok(condition);
						next();
					}
				}, function(e){
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		
		var uploadDataTest = function(){
			test('Upload data', function(next){
				DataSrv.getData().then(function(d){
					var condition = (
						isDefined(d, 'data') &&
						isDefined(d.data, 'projects')
					);
					if (condition){
						var projects = [], journals = [], entries = [], storage = [];
						
						angular.forEach(d.data.projects, function(projv, projk){
							projects.push(projv);
							angular.forEach(projv.journals, function(jourv, jourk){
								journals.push(jourv);
								angular.forEach(jourv.data_entries, function(entryv, entryk){
									entries.push(entryv);
									storage.push('project-'+projk+'-'+jourk+'-'+entryk);
								});
							});
						})
						
						condition = (
							condition && 
							projects.length > 0 &&
							journals.length > 0 &&
							entries.length > 0 &&
							storage.length > 0
						);
						
						if (condition) {
							var attbs = [];
							var fullEntryName = '';
							var eee = {}; 
							
							for (var i = 0; i < entries.length; i++) {
								if ((entries[i].is_image == '0') && (entries[i].count == 4)) {
									var data_entry_no = entries[i].data_entry_no;
									for (var j = 0; j < entries[i].data_attributes.length; j++) {
										var attb = entries[i].data_attributes[j];
										var data_attb_id = attb.data_attb_id;
										var attb_name = 'entry_'+data_entry_no+'_'+data_attb_id;
										
										
										if (attb.data_attb_type_id == '4') { eee[attb_name] = "Yes"; }
										if (attb.data_attb_type_id == '3') { eee[attb_name] = attb.end_value; }
										if (attb.data_attb_type_id == '2') { 
											var lookup = d.data.lookups[attb.data_set_id];
											eee[attb_name] = lookup[lookup.length-1].lk_value; 
										}
										if (attb.data_attb_type_id == '1') { eee[attb_name] = attb.end_value; }
										attbs.push(eee);
									}
									
									for (var k = 0; k < storage.length; k++) {
										if (storage[k].indexOf(data_entry_no) != -1) {
											fullEntryName = storage[k];
										}
									}
									
								}
							}
							
							var entryStorageData = {
								entries: eee,
								images: [],
								aImages: {},
								deletedImages: [],
								timestamp: new Date().getTime()
							}
							
							if (fullEntryName == '') {ok(false, {message: 'Invalid journal data somehow'}); next(); return false; };
							StorageUtils.set(fullEntryName, entryStorageData);
							
							
							StorageUtils.set(vm.storageNameUpload, [fullEntryName]).then(function(){
								DataSrv.push().then(function(b){
									var promises = [];
									for (var i = 0; i < storage.length; i++) {
										promises.push(StorageUtils.remove(storage[i]));
									}
									return $q.all(promises, function(values){}).then(function(){
										return DataSrv.pull();
									});
								}, function(e){
									ok(false, {message: e});
									next();
								}).then(function(d){
									for (var i = 0; i < storage.length; i++) {
										var s = storage[i];
										var st = s.split('-');
										var pid = st[1];
										var jid = st[2];
										var eid = st[3];
										
										condition = (
											condition &&
											isDefined(d, 'data.projects.'+pid+'.journals.'+jid+'.data_entries.'+eid+'.data_attributes') &&
											d.data.projects[pid].journals[jid].data_entries[eid].data_attributes.length > 0
										);
										
										if (!condition) {ok(false, {message: 'Error in data'}); next(); return false; }
										
										var new_attbs = d.data.projects[pid].journals[jid].data_entries[eid].data_attributes;
										/*angular.forEach(eee, function(v, k) {
											var temp = k.split('_');
											attbid = temp[2];
											
										});*/
										for (var i = 0; i < new_attbs.length; i++) {
											var new_attb = new_attbs[i];
											var attb_id = new_attb.data_attb_id;
											condition = (
												condition && 
												isDefined(eee, 'entry_'+eid+'_'+attb_id) &&
												new_attb.actual_value == eee['entry_'+eid+'_'+attb_id]
											);
											if (!condition) {ok(false, {message: 'Wrong data from server'}); next(); return false; }
										}
									}
									
									ok(condition);
									next();
									
								})
							});
						} else {
							ok(false);
							next();
						}
						
					} else {
						ok(condition);
						next();
					}
				}, function(e){
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		
		
		var uploadImageTest = function(){
			test('Upload image', function(next){
				DataSrv.getData().then(function(d){
					var condition = (
						isDefined(d, 'data') &&
						isDefined(d.data, 'projects')
					);
					if (condition){
						var projects = [], journals = [], entries = [], storage = [];
						
						angular.forEach(d.data.projects, function(projv, projk){
							projects.push(projv);
							angular.forEach(projv.journals, function(jourv, jourk){
								journals.push(jourv);
								angular.forEach(jourv.data_entries, function(entryv, entryk){
									entries.push(entryv);
									storage.push('project-'+projk+'-'+jourk+'-'+entryk);
								});
							});
						})
						
						condition = (
							condition && 
							projects.length > 0 &&
							journals.length > 0 &&
							entries.length > 0 &&
							storage.length > 0
						);
						
						if (condition) {
							var attbs = [];
							var fullEntryName = '';
							var eee = {}; 
							
							for (var i = 0; i < entries.length; i++) {
								if (entries[i].is_image == '1') {
									var data_entry_no = entries[i].data_entry_no;
									
									for (var k = 0; k < storage.length; k++) {
										if (storage[k].indexOf(data_entry_no) != -1) {
											fullEntryName = storage[k];
										}
									}
								}
							}
							
							if (fullEntryName == '') {ok(false, {message: 'Invalid journal data somehow'}); next(); return false; }
							var deferred = $q.defer();
							if ((typeof cordova == 'undefined') || (typeof resolveLocalFileSystemURL == 'undefined')) {ok(false, {message: 'Not on mobile app'}); next(); return false; }
							
							resolveLocalFileSystemURL(cordova.file.applicationDirectory+'www/img/dontremovethis.jpg', function(a){deferred.resolve(a);}, function(e){ deferred.reject(e); })
							
							
							var afterSet = deferred.promise.then(function(fileEntry){
								var src = fileEntry.nativeURL;
								var entryStorageData = {
									entries: [],
									images: [
										{
											"src": src,
											"description": vm.journalImageDescription,
											"comment": "",
											"internal": true,
											"pict_seq_no": 1
										}
									],
									aImages: {},
									deletedImages: [],
									timestamp: new Date().getTime()
								}
								
								return StorageUtils.set(fullEntryName, entryStorageData);
							}, function(e){ throw e; });
							
							
							afterSet.then(function(){StorageUtils.set(vm.storageNameUpload, [fullEntryName]).then(function(){
								DataSrv.push().then(function(b){
									return StorageUtils.remove(fullEntryName).then(function(){
										return DataSrv.pull();
									});
								}, function(e){
									ok(false, {message: e});
									next();
								}).then(function(d){
								
									var st = fullEntryName.split('-');
									var pid = st[1];
									var jid = st[2];
									var eid = st[3];
									
									condition = (
										condition &&
										isDefined(d, 'data.projects.'+pid+'.journals.'+jid+'.data_entries.'+eid+'.data_attributes') &&
										d.data.projects[pid].journals[jid].data_entries[eid].data_entry_images.length > 0
									);
									console.log('KIKIKIK',pid,jid,eid,d.data.projects[pid].journals[jid].data_entries[eid]);
									if (!condition) {ok(false, {message: 'Error in data 1'}); next(); return false; }
									
									var data_entry_images = d.data.projects[pid].journals[jid].data_entries[eid].data_entry_images[0];
									
									condition = (
										condition &&
										isDefined(data_entry_images, 'data_entry_pict_no') &&
										isDefined(data_entry_images, 'data_entry_no') &&
										isDefined(data_entry_images, 'pict_seq_no') &&
										isDefined(data_entry_images, 'pict_file_name') &&
										isDefined(data_entry_images, 'pict_file_path') &&
										isDefined(data_entry_images, 'pict_definition') &&
										isDefined(data_entry_images, 'pict_user_id') &&
										isDefined(data_entry_images, 'pict_validate_comment') &&
										data_entry_images.pict_definition == vm.journalImageDescription
									);
									
									if (!condition) {ok(false, {message: 'Error in data'}); next(); return false; }
									
									
									ok(condition);
									next();
									
								})
							});
							});
						} else {
							ok(false);
							next();
						}
						
					} else {
						ok(condition);
						next();
					}
				}, function(e){
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		
		
		
		var publishTest = function(){
			test('Publish', function(next){
				DataSrv.getData().then(function(d){
					var condition = (
						isDefined(d, 'data') &&
						isDefined(d.data, 'projects')
					);
					if (condition){
						var projects = [], journals = [], entries = [], storage = [];
						
						angular.forEach(d.data.projects, function(projv, projk){
							projects.push(projv);
							angular.forEach(projv.journals, function(jourv, jourk){
								journals.push(jourv);
								angular.forEach(jourv.data_entries, function(entryv, entryk){
									entries.push(entryv);
									storage.push('project-'+projk+'-'+jourk+'-'+entryk);
								});
							});
						})
						
						condition = (
							condition && 
							projects.length > 0 &&
							journals.length > 0 &&
							entries.length > 0 &&
							storage.length > 0
						);
						
						if (condition) {
							StorageUtils.set(vm.storageNamePublish, storage).then(function(){
								DataSrv.push().then(function(b){
									return DataSrv.pull()
								}, function(e){
									ok(false, {message: e});
									next();
								}).then(function(d){
									//console.log('DDDDD',d);
									condition = (
										condition &&
										isDefined(d, 'data.projects') &&
										d.data.projects.length == 0
									)
									ok(condition);
									next();
								})
								
							});
						} else {
							ok(false);
							next();
						}
						
					} else {
						ok(condition);
						next();
					}
				}, function(e){
					ok(false, {message: e});
					next();
				})
			}, true);
		}
		
		
		
		
		vm.allTests = {
			'Setup environment': {testGroup: 'Initialize Test'},
			'Ping': {testGroup: 'Initialize Test'},
			'Logout': {testGroup: 'Authentication'},
			'Login': {testGroup: 'Authentication'},
			'Pull': {testGroup: 'Synchronize'},
			'Initialize': {testGroup: 'Synchronize'},
			'Upload data': {testGroup: 'Synchronize'},
			'Upload image': {testGroup: 'Synchronize'},
			'Publish': {testGroup: 'Synchronize'},
			'Cleanup environment': {testGroup: 'Finalize Test'}
		}
	
		vm.atestGroups['Initialize Test'] = {
			groupName: 'Initialize Test',
			tests: [
				{name: 'Ping', func:pingTest},
				{name: 'Setup environment', func:installTest}
			]
		}
	
		vm.atestGroups['Authentication'] = {
			groupName: 'Authentication',
			tests: [
				{name: 'Login', func:loginTest},
				{name: 'Logout', func:logoutTest}
			]
		}
		
		vm.atestGroups['Synchronize'] = {
			groupName: 'Synchronize',
			tests: [
				{name: 'Pull', func:pullTest},
				{name: 'Initialize', func:initializeTest},
				{name: 'Upload data', func:uploadDataTest},
				{name: 'Upload image', func:uploadImageTest},
				{name: 'Publish', func:publishTest}
			]
		}
		
		vm.atestGroups['Finalize Test'] = {
			groupName: 'Finalize Test',
			tests: [
				{name: 'Cleanup environment', func:uninstallTest}
			]
		}
		
		
		angular.forEach(vm.atestGroups, function(v, k){
			angular.forEach(vm.atestGroups[k], function(v2, k2) {
				angular.forEach(vm.atestGroups[k]['tests'], function(v3, k3){
					vm.atestGroups[k]['results'] = {};
					vm.atestGroups[k]['passed'] = 0;
					vm.atestGroups[k]['results'][v3.name] = {
						status: '',
						test: v3.name
					}
				});
			});
		});
		
		vm.testGroups.push(vm.atestGroups['Initialize Test']);
		vm.testGroups.push(vm.atestGroups['Authentication']);
		vm.testGroups.push(vm.atestGroups['Synchronize']);
		vm.testGroups.push(vm.atestGroups['Finalize Test']);
		
	}
	
	vm.runAll = function(){
		setupTests();
		//Store session, remove logout
		for (var i = 0; i < vm.testGroups.length; i++) {
			runTest(vm.testGroups[i].groupName);
		}/*
		
		//Restore session
		runTest('Initialize Test');
		runTest('Authentication');
		runTest('Synchronize');*/
	}
	
	setupTests();
	
	
	// Utils
	
	
	/*
	function isEven(x) {
		return x % 2 == 0;
	}

	kludjstest('Testing isEven', function() {
		ok(isEven(0), 'Zero is even');
		ok(isEven(1) == false, 'One is odd');
		ok(isEven(12), 'Twelve is even');
	});*/
	//('lol',function(){
	
	
	
  }
})();