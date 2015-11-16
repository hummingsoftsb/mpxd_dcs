(function(){
  'use strict';
  angular.module('app')
    .controller('JournalDataEntryCtrl', JournalDataEntryCtrl);

  function JournalDataEntryCtrl($scope, $window, $ionicModal, $ionicPopup, $ionicPosition, $ionicPopover, $ionicActionSheet, $log, JournalSrv, $ionicScrollDelegate, AuthSrv, $stateParams, $q, Utils, $cordovaFile, $ionicLoading, ToastPlugin, CommSrv, DataSrv, $rootScope){
    var projectId = $stateParams.projectId;
    var journalId = $stateParams.journalId;
    var entryId = $stateParams.entryId;
	var forced = ((typeof $stateParams.force != 'undefined') && ($stateParams.force == 'true'))
	var vm = {};
	$scope.vm = vm;
	$scope.pageTitle = "";
    $scope.showReorder = false;
	$scope.isDirty = false;
	$rootScope.isJournalDirty = $scope.isDirty;
	$ionicLoading.show();
	
	
	//console.log(this.arguments);
	//console.log('window',imagePicker);[attb.generated_name]
	window.myscope = $scope;
	$scope.images = [];
	/*$scope.increase = function(v) {
		console.log(vm[v]);
		vm[v] = vm[v] + 1;
	}*/
	
	

    $scope.toggleReorder = function () { 
		$scope.showReorder = !$scope.showReorder;
		if (!$scope.showReorder) { 
			// Save the settings.
			for (var i = 0; i < $scope.images.length; i++) {
				$scope.images[i].pict_seq_no = i+1;
			}
			$scope.saveData();
		}
		setTimeout(function(){$ionicScrollDelegate.resize()}, 50);
		//$ionicScrollDelegate.scrollTop();
	};
	
	$scope.resortImages = function() {
		$scope.images.sort(function(a,b){
			return parseInt(a.pict_seq_no) - parseInt(b.pict_seq_no);
		});
	}

    $scope.onReorder = function (fromIndex, toIndex) {
        var moved = $scope.images.splice(fromIndex, 1);
        $scope.images.splice(toIndex, 0, moved[0]);
		$scope.setDirty();
    };
	
	//$scope.not_implemented = function(){window.alert('Sorry not implemented yet')};
	$scope.back = function(){history.back(1)}
	$scope.setDirty = function(){$scope.isDirty = true; $rootScope.isJournalDirty = $scope.isDirty;}
	$scope.setClean = function(){$scope.isDirty = false; $rootScope.isJournalDirty = $scope.isDirty;}
	
	
	$scope.editDescriptionAndScroll = function(idx) {
		var position = $ionicPosition.position(angular.element(document.getElementById('image-'+idx)));
		$ionicScrollDelegate.scrollTo(position.left, position.top+70, true);
		return $scope.edit(idx, true);
	}
	
	$scope.isImageToDisplayAvailable = function() {
		for (var i = 0; i < $scope.images.length; i++) {
			if (!$scope.images[i].deleted) return true;
		}
		return false;
	}
	
	/*
	$scope.changeImageSort = function(a){ 
		//console.log(a.pict_seq_no, 'changto', a.changeTo);
		var from = a.pict_seq_no-1;
		var to = a.changeTo-1;
		var temp = $scope.images.splice(from,1)[0];
		$scope.images.splice(to,0,temp);
		
		for (var i = 0; i < $scope.images.length; i++) {
			$scope.images[i].pict_seq_no = i+1;
		}
		
		
		//window.alert('LOLOL');console.log('SORT',a);
	}*/
	
	
	$scope.publish = function() {
		if (confirm('Confirm publish?')) {
			
			
			$scope.saveData().then(function(){
				
				var data = {}
				data.entries = {};
				for (var i = 0; i < vm.entryNames.length; i++) {
					data.entries[vm.entryNames[i]] = vm[vm.entryNames[i]];
				}
				
				var isOk = true;
				
				angular.forEach(data.entries, function(v, k){
					var ids = k.split('_');
					var attbId = ids[ids.length-1];
					var value = v;
					var attb = vm.adataAttbs[attbId];
					var variance = value - parseFloat(attb.prev_actual_value);
					
					// If is already approved, skip.
					if ((typeof attb.approved != 'undefined') && (attb.approved)) return;
				
					if (variance > parseFloat(attb.frequency_max_value)) {
						vm.adataAttbs[attbId].variance = variance;
						vm.adataAttbs[attbId].varient = true;
						vm.adataAttbs[attbId].approved = false;
						isOk = false;
					}
				})
				//console.log(data.entries);
				if (!isOk) {
					alert('Exceeded weekly max data. Please approve and retry.');
					return;
				}
				
				JournalSrv.setEntryPublished(projectId, journalId, entryId).then(function(){
					$rootScope.$broadcast('published');
					history.back();
				});
				
			})
		}
	}
	
	function saveIt(link) {
		
		var newName = 'DCS-img'+new Date().getTime()+'-'+(Math.random()*10000).toFixed('0')+'.jpg';
		console.log('Saving new file as',newName);
		console.log('Src',link);
		
		var destFolder = cordova.file.externalApplicationStorageDirectory;
		// Most likely iOS
		if (destFolder == null) destFolder = cordova.file.dataDirectory;
		destFolder = destFolder+'images/';
		console.log('Dest',destFolder);
		return Utils.moveFile(link, destFolder,newName).then(function(s){
			return s;
		}, function(e){console.log('ERRRRRR',e);})
	}
	
	$scope.capture = function() {
		if (typeof navigator.camera != 'undefined') {
			navigator.camera.getPicture(function(link){
				saveIt(link).then(function(l){
					//$scope.$apply(function(){
					
					Utils.cropImage(l).then(function(r){
						return Utils.compressImage(l);
					}).then(function(){
						$scope.images.push({
						src: l,
						description: '',
						comment: '',
						internal: true,
						pict_seq_no: $scope.images.length+1
						});
						
					}).then(function() {
						var d = $q.defer();
						setTimeout(function(){
							$scope.editDescriptionAndScroll($scope.images.length-1).then(d.resolve, d.reject);
						},500);
						return d.promise;
					}).then(function(){
						return $scope.saveData();
					});
					//});
				/*
				$scope.$apply(function(){$scope.images.push({
					src: link,
					description: ''
				});*/
				});
			}, function(e) {
			}, {
				targetHeight: 1200,
				targetWidth: 1600,
				quality: 50
			})
		} else {
			alert('Camera is not available');
		}
	}
	
	JournalSrv.getAllDataEntries();
	
	$scope.edit = function(idx, forceNotSave) {
		var desc = $scope.images[idx].description;
		vm.editDescription = desc;
		forceNotSave = (typeof forceNotSave != 'undefined') && forceNotSave;
		var promise = $ionicPopup.show({
		 template: '<input id="edit_description" type="text" ng-model="vm.editDescription">',
		 title: 'Enter Description',
		 subTitle: '',
		 scope: $scope,
		 buttons: [
		   {
			 text: '<b>Save</b>',
			 type: 'button-positive',
			 onTap: function(e) {
			   if (!$scope.vm.editDescription) {
				 //don't allow the user to close unless he enters wifi password
				e.preventDefault();
				alert('Picture description is mandatory');
			   } else {
				 return $scope.vm.editDescription;
			   }
			 }
		   },{ 
			text: 'Cancel',
			onTap: function(e){
				if (desc != '') $scope.vm.editDescription = desc;
				if ($scope.vm.editDescription == '') { e.preventDefault(); alert('Picture description is mandatory'); }
			}}
		 ]
	   });
	   setTimeout(function(){angular.element(document.getElementById('edit_description'))[0].focus()},300)
	   
	   promise.then(function(){
		var newDesc = $scope.vm.editDescription;
		$scope.images[idx].description = (newDesc == null) ? desc : newDesc;
		if (newDesc != desc) { if (!forceNotSave) return $scope.saveData(); }
	   });
	   
	   return promise;
		//var newDesc = prompt('Description', desc);
		
	}
	
	$scope.deleteImg = function(idx) {
		if (confirm('Confirm delete')) {
			var filePath = $scope.images[idx].src;
			var success = false;
			var after = $q.defer();	
			// Only delete internal images
			if ($scope.images[idx].internal) {
				Utils.removeFile(filePath).then(function(s){
					console.log('File removed?');
					success = true;
					$scope.images.splice(idx,1);
					after.resolve();
				}, function(e){
					console.log('Error when deleting file',e);
					success = true;
					$scope.images.splice(idx,1);
					after.resolve();
				});
			} else {
				// Delete through web for external images
				//$ionicLoading.show();
				if (typeof vm.deletedImages == 'undefined') vm.deletedImages = [];
				if (vm.deletedImages.indexOf($scope.images[idx].data_entry_pict_no) == -1) vm.deletedImages.push($scope.images[idx].data_entry_pict_no);
				success = true;
				$scope.images[idx].deleted = true;
				after.resolve();
			}
			after.promise.then(function(){
				if (success) {
				if (!$scope.images[idx].internal) { 
					$scope.images[idx].deleted = true;
				}
				$scope.resortImages();
				$scope.saveData().then(function(){
					//$scope.images.splice(idx,1);
					setTimeout(function(){$ionicScrollDelegate.resize();},100);
				});
				}
				//$ionicLoading.hide();
			}, function(){
				//$ionicLoading.hide();
			})
			
			
			/*window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSystem){
			fileSystem.root.getFile(filePath, {create:false}, function(fileEntry){
				fileEntry.remove(function(file){
					$scope.images.splice(idx,1);
					console.log("File removed!");
				},function(){
					console.log("error deleting the file " + error.code);
					});
				},function(){
					console.log("file does not exist");
				});
			},function(evt){
				console.log(evt.target.error.code);
		});*/
	}
	};
	
	$scope.initForm = function(f){
		$scope.journalForm = f;
		//window.wow = $scope;
	}
	
	$scope.saveData = function() {
		if ($scope.showReorder) { $scope.toggleReorder(); }
		return JournalSrv.getEntryState(projectId, journalId, entryId).then(function(saved){
			//console.log('SAVED',saved);
			if (!$scope.journalForm.$valid) {
				alert('Please rectify the highlighted data');
				return false;
			}
			
			for (var i = 0; i < $scope.images.length; i++) {
				if ($scope.images[i].description == "") {
					alert('Picture description is mandatory');
					return false;
				}
			}
			var data = {}
			data.entries = {};
			for (var i = 0; i < vm.entryNames.length; i++) {
				data.entries[vm.entryNames[i]] = vm[vm.entryNames[i]];
			}
			data.images = angular.copy($scope.images);
			data.aImages = {};
			
			data.deletedImages = ((typeof saved == 'undefined') || (typeof saved.deletedImages == 'undefined')) ? [] : saved.deletedImages;
			//data.deletedImages = data.deletedImages.concat( (typeof vm.deletedImages == 'undefined') ? [] : vm.deletedImages );
			if (typeof vm.deletedImages != 'undefined') {
				for (var i = 0; i < vm.deletedImages.length; i++) {
					if (data.deletedImages.indexOf(vm.deletedImages[i]) == -1) data.deletedImages.push(vm.deletedImages[i]);
				}
			}
			
			var i = $scope.images.length;
			while (i--) {
				if (typeof $scope.images[i].data_entry_pict_no == 'undefined') continue;
				var img = $scope.images[i];
				var original = vm.dataEntry.adata_entry_images[img.data_entry_pict_no];
				// If it is the same as original (no change), dont save it.
				//console.log('original',original,'img',img);
				if (((typeof original != 'undefined') && (original.pict_definition == img.description)) && (!img.deleted) && (parseInt(original.pict_seq_no) == img.pict_seq_no)) {console.log('not saving'); data.images.splice(i,1); continue;}	
				//if ($scope.images[i].description == 
				data.aImages[$scope.images[i].data_entry_pict_no] = $scope.images[i];
			}
			/*for (var i = 0; i < $scope.images.length; i++) {
				
			}*/
			console.log('Saving',data);
			return JournalSrv.saveEntryState(projectId, journalId, entryId, data).then(function(){
				ToastPlugin.showLongTop('Saved');
				$scope.setClean();
			});
		});
	}
	
	
	
	//if (typeof cordova == 'undefined') $scope.images = [{		'src':'/img/blur.jpg',		'description':'Description like what'	},{		'src':'/img/test.jpg',		'description':'Description like what'	}];
	$scope.getImagesFromGallery = function() {
		window.imagePicker.getPictures(
			function(results) {
				//$scope.$apply(function(){
				var promises = [];
				for (var i = 0; i < results.length; i++) {
					console.log('Image URI: ' + results[i]);
					promises.push(saveIt(results[i]))
					/**/
					//console.log($scope.images);
				}
				// editDescriptionAndScroll
				
				var savedIndexes = [];
				
				$q.all(promises).then(
					function(values) {
					var anotherPromises = [];
					
					var afterCompress = values.reduce(function(promise,item){
						var finalPromise = promise.then(function(){
							var fpath = item;
							var cropPromise = Utils.cropImage(fpath).then(function(){
								var compressPromise = Utils.compressImage(fpath);
								//console.log('Compress',compressPromise);
								return compressPromise;
							}).then(function(result){
								$scope.images.push({
									src: result,
									description: '',
									comment: '',
									internal: true,
									pict_seq_no: $scope.images.length+1
								});
								savedIndexes.push($scope.images.length-1);
							});
							//console.log('cropPromise',cropPromise);
							return cropPromise;
						});
						//console.log('FinalPromise',finalPromise);
						return finalPromise;
					}, $q.when());
					
					
					//console.log('Got afterCOmpress',afterCompress);
					return afterCompress.then(function(){
						setTimeout(function(){
							return savedIndexes.reduce(function(promise,item){
								return promise.then(function(){
									return $scope.editDescriptionAndScroll(item);
								});
							}, $q.when()).then(function(){
								return $scope.saveData();
							});
						}, 500)
					});
					
					
					/*
					for (var i = 0; i < values.length; i++) {
						(function(fpath){
							//var p = jrCrop.crop({url:fpath, width:400, height:300}).then(function(a){
							//var blob = Utils.base64toBlob(a.toDataURL().split(',')[1],'image/png');
							//var starttime = Date.now();
							var p = Utils.cropImage(fpath).then(function(){
								return Utils.compressImage(fpath);
							});
						
						anotherPromises.push(p);
						})(values[i]);
					}
					
					$q.all(anotherPromises).then(function(anotherValues){
						for (var i = 0; i < anotherValues.length; i++) {
							$scope.images.push({
								src: anotherValues[i],
								description: '',
								comment: '',
								internal: true,
								pict_seq_no: $scope.images.length+1
							});
						}
						$scope.saveData();
					})*/
					})
				//});
				//$ionicScrollDelegate.resize();
			}, function (error) {
				console.log('Error: ' + error);
			}, {
				maximumImagesCount: 10,
				width: 800
			}
		);
	}
	
	var start = new Date().getTime();
	//console.log('Starting to get them..',start);
	var pJD = JournalSrv.getJournalData(projectId, journalId);
	var pUOM = JournalSrv.getUOMs();
	var pLookup = JournalSrv.getLookups();
	var pInitialized = JournalSrv.getEntryInitialized(projectId, journalId, entryId);
	var loadDeffered = $q.defer();

	$q.all([pJD,pUOM,pLookup,pInitialized]).then(function(results){
		//console.log('results',results);
		//console.log('Finished!',new Date().getTime() - start);
		
		var d = results[0];
		var uoms = results[1];
		var lookups = results[2];
		var initialized = results[3];
		vm.lookups = lookups;
		vm.entryNames = [];
		vm._tempChecked = {};
		window.vm = vm;
		var dataEntry = d['data_entries'][entryId];
		vm.dataEntry = dataEntry;
		if (!initialized) {
			if (forced || window.confirm('Assign data attributes to the current week?')) {
				dataEntry.data_attributes = d['data_attributes'];
				
				// Sorting using display_seq_no
				dataEntry.data_attributes.sort(function(a,b){
					return parseInt(a.display_seq_no) - parseInt(b.display_seq_no);
				});
			} else {
				history.back(-1);
			}
		} else {
			// If it is initialized locally
			if (dataEntry.count == 0) {
				dataEntry.data_attributes = d['data_attributes'];
				
				// Sorting using display_seq_no
				dataEntry.data_attributes.sort(function(a,b){
					return parseInt(a.display_seq_no) - parseInt(b.display_seq_no);
				});
			}
		}
		
		
		vm.checkMyDependency = function(a,b) {
		//console.log(a);
			var id = a.split('_')[2];
			if (typeof vm.dependTo[id] == 'undefined') {return false;}
			//console.log('returning',!vm._tempChecked[id],'for',id);
			return !vm._tempChecked[id];
		}
		vm.checkDisabled = function(){
			//console.log("EHEHE",a);
			//var dID = a.split('_')[2];
			//console.log(dID);
			vm._tempChecked = {};
			//console.log("Initiating");
			
			angular.forEach(vm.dependTo, function(val, key) {
				vm._tempChecked[key] = true;
				for (var i = 0; i < val.length; i++) {
					// YOU STOPPED HERE!
					vm._tempChecked[val[i]] = vm.recursive(val[i]/*, vm[vm.entryName+'_'+val[i]]*/);
					//console.log('checked',val[i],vm._tempChecked[val[i]]);
					if (!vm._tempChecked[val[i]]) vm._tempChecked[key] = false;
				}
				//vm.recursive(key, vm[vm.entryName+'_'+key]);
			});
			//console.log("FINISHED",vm._tempChecked);
		}
		
		vm.recursive = function(id) {
			if (typeof vm._tempChecked[id] != 'undefined') { /*console.log('Already checked',id);*/ return vm._tempChecked[id]; } 
			
			var value = vm[vm.entryName+'_'+id];
			var result = (vm.adataAttbs[id].exactMax == value);
			//console.log("Checking",id,value,vm.adataAttbs[id].exactMax,result, vm._tempChecked);
			/*if (!result) {
				//vm._tempChecked[id] = false;
				return false;
			}*/
			if (typeof vm.dependTo[id] == 'undefined') {
				// No further dependency
				//vm._tempChecked[id] = result;
				return result;
			} else {
				// Further dependency
				
				for (var i = 0; i < vm.dependTo[id].length; i++) {
					return result && vm.recursive(vm.dependTo[id][i]/*, vm[vm.entryName+'_'+vm.dependTo[id][i]]*/);
				}
			}
		}
		
		vm.dependency = d['dependency'];
		// Not sure why dependency is "0" or empty array, but this is a temporary fix to standardize dependency
		if (vm.dependency == 0) vm.dependency = {};
		if ((vm.dependency == null) || (typeof vm.dependency.length != 'undefined')) vm.dependency = {};
		vm.dependTo = {};
		angular.forEach(vm.dependency, function(val, key) {
			vm.dependTo[key] = val.split('|');
		});
		
		$scope.isImage = (dataEntry.is_image == '1');
		$scope.pageTitle = d.journal_name;
		vm.entryName = "entry_"+entryId;
		vm.data_entry_no = entryId;
		vm.adataAttbs = {};
		vm.dataAttbs = dataEntry.data_attributes;
		vm.rejectNotes = dataEntry.reject_notes;
		console.log('reject!',vm.rejectNotes);
		angular.forEach(vm.dataAttbs, function(val, key) {
			vm.dataAttbs[key].generated_name = vm.entryName+"_"+vm.dataAttbs[key].data_attb_id;
			if (typeof val.actual_value == 'undefined') {
				// No actual value, must be a new attribute
				vm[vm.dataAttbs[key].generated_name] = parseFloat(val.start_value);
			} else {
				vm[vm.dataAttbs[key].generated_name] = isNaN(parseFloat(val.actual_value)) ? val.actual_value : parseFloat(val.actual_value);
			}
			var generated_name = vm.dataAttbs[key].generated_name;
			//vm.entries[generated_name] = vm[vm.dataAttbs[key].generated_name];
			vm.entryNames.push(generated_name);
			
			vm.adataAttbs[vm.dataAttbs[key].data_attb_id] = val;
			
			if (val.data_attb_type_id == 3) {
				// Incremental text box
				
				// Set minimum value
				if (val.prev_actual_value == null) {
					vm.dataAttbs[key].exactMin = parseFloat(val.start_value);
				} else {				
					vm.dataAttbs[key].exactMin = parseFloat(val.start_value);
				}
				
				// Set maximum value
				var tempMax = parseFloat(val.frequency_max_value) + parseFloat(val.actual_value);
				// This code should be enabled if you want to limit with max frequency too: vm.dataAttbs[key].exactMax = Math.min(tempMax, parseFloat(val.end_value));
				vm.dataAttbs[key].exactMax = parseFloat(val.end_value);
				
				// Set actual value
				if (val.actual_value == null) {
					vm.dataAttbs[key].exactActual = parseFloat(val.start_value)
				} else {
					vm.dataAttbs[key].exactActual = parseFloat(val.actual_value)
				}
 			}
			else if (val.data_attb_type_id == 4) {
				// Radio
				//console.log('RADIO', vm[vm.dataAttbs[key].generated_name])
				vm.dataAttbs[key].isLocked = (val.actual_value == "Yes");
			}
			else if (val.data_attb_type_id == 2) {
				// Select
				vm.dataAttbs[key].lookups = vm.lookups[val.data_set_id];
				
			}
			else if (val.data_attb_type_id == 1) {
				// Textbox
				
				// Set minimum value
				if (val.prev_actual_value == null) {
					vm.dataAttbs[key].exactMin = parseFloat(val.start_value);
				} else {				
					vm.dataAttbs[key].exactMin = parseFloat(val.start_value);
				}
				
				// Set maximum value
				var tempMax = parseFloat(val.frequency_max_value) + parseFloat(val.actual_value);
				// This code should be enabled if you want to limit with max frequency too: vm.dataAttbs[key].exactMax = Math.min(tempMax, parseFloat(val.end_value));
				vm.dataAttbs[key].exactMax = parseFloat(val.end_value);
 			}
		});
		
		
		console.log('Finished rendering',new Date().getTime() - start);
		console.log(vm);
		loadDeffered.resolve();
	});
	loadDeffered.promise.then(function(){
		return $q.all([JournalSrv.getEntryState(projectId, journalId, entryId), DataSrv.getLastSync()])
	}).then(function(values){
		var r = values[0];
		var lastSync = values[1];
		
		vm.dataEntry.adata_entry_images = {};
		for (var i = 0; i < vm.dataEntry.data_entry_images.length; i++) {
			var j = vm.dataEntry.data_entry_images[i];
			vm.dataEntry.adata_entry_images[j.data_entry_pict_no] = j;
		}
		if (r) {
			// Image from internal entry!
			
			
			var syncTime = new Date(lastSync.time).getTime();
			var saveTime = r.timestamp;
			if (typeof saveTime == 'undefined') saveTime = 0;
			
			var serverLatest = (syncTime > saveTime);
			/*console.log('ServerLatest',serverLatest);
			console.log('SyncTime',syncTime);
			console.log('SaveTime',saveTime);*/
			angular.forEach(r.entries, function(val,key){
				vm[key] = val;
			});
			for (var i = 0; i < r.images.length; i++) {
				// If external image, check out the saved ones -- whether deleted or not
				if (typeof r.images[i].data_entry_pict_no != 'undefined') {
					var aImage = r.aImages[r.images[i].data_entry_pict_no];
					//console.log("EXIST!",aImage);
					//if (aImage.deleted) continue;
				}
				// If server is latest and the image does not exist anymore, it is probably deleted!
				if ((serverLatest) && (typeof vm.dataEntry.adata_entry_images[r.images[i].data_entry_pict_no] == 'undefined')) { continue; }
					
				$scope.images.push(r.images[i]);
				
				//$scope.images = $scope.images.concat(r.images);
			}
			//console.log('loaded!',r);
			/*vm.dataEntry.data_entry_images.sort(function(a,b){
				return parseInt(a.pict_seq_no) - parseInt(b.pict_seq_no);
			});*/
		
		}
		
		// Image from external
		for (var i = 0; i < vm.dataEntry.data_entry_images.length; i++) {
		var d = vm.dataEntry.data_entry_images[i];
		// If we have a internal one, dont add this too.
		if ((typeof r != 'undefined') && (typeof r.aImages != 'undefined') && (typeof r.aImages[d.data_entry_pict_no] != 'undefined')) continue;
		$scope.images.push({
			description: d.pict_definition,
			src: CommSrv.getUrl()+'/'+d.pict_file_path+d.pict_file_name,
			comment: ((d.pict_validate_comment == null) ? "" : d.pict_validate_comment),
			internal: false,
			deleted: false,
			data_entry_pict_no: d.data_entry_pict_no,
			pict_seq_no: d.pict_seq_no
		});
		}
		
		$scope.resortImages();

		$ionicLoading.hide();
		$ionicScrollDelegate.resize();
		vm.checkDisabled();
		
	});
  }	
})();
