(function(){
  'use strict';
  angular.module('app')
    .factory('Utils', Utils);

  function Utils($timeout, $q, $sce, $log, $http, $cordovaFile, $jrCrop){
    var service = {
      createUuid: createUuid,   // ()                             generate an identifier like 'de7a545d-0045-454a-81de-deb9d74e74a7'
      isEmail: isEmail,         // (str)                          check if str has email format
      isUrl: isUrl,             // (str)                          check if str has url format
      startsWith: startsWith,   // (str, prefix)                  check if str starts with prefix
      endsWith: endsWith,       // (str, suffix)                  check if str ends with suffix
      randInt: randInt,         // (min, max)                     generate a random Int between min & max
      toDate: toDate,           // (date)                         format input (timestamp, iso string, JS Date, moment Date) to a JS Date
      isDate: isDate,           // (date)                         check if date is a Date, get timestamp, iso string, JS Date or moment Date
      getDeep: getDeep,         // (obj, path)                    allow to get deep value in object
      async: async,             // (fn)                           transform synchronous function in asynchronous function
      debounce: debounce,       // (key, callback, _debounceTime) debounce a value based on given key
      trustHtml: trustHtml,     // (html)                         angular trust html (to display unsafe html)
      extendDeep: extendDeep,   // (dest, args...)                extends dest with values in args objets (like angular.extends but recursivly)
      extendsWith: extendsWith, // (dest, src)                    add src values to dest where key is undefined (do not override existing values)
      sort: sort,                // (arr, params)                  sort Array arr according to params (order: elt attribute name, desc: true/false)
      checkConnection: checkConnection,                // ()                  Checks internet connectivity
	  effectiveDeviceWidth: effectiveDeviceWidth,
	  formRequest: formRequest,   // Gives a form-encoded request instead of JSON one
	  formatSyncTime: formatSyncTime,
	  moveFile: moveFile,
	  removeFile: removeFile,
	  base64toBlob: base64toBlob,
	  saveBlobToFile: saveBlobToFile,
	  cropImage: cropImage,
	  compressImage: compressImage
    };

    function createUuid(){
      function S4(){ return (((1+Math.random())*0x10000)|0).toString(16).substring(1); }
      return (S4() + S4() + '-' + S4() + '-4' + S4().substr(0,3) + '-' + S4() + '-' + S4() + S4() + S4()).toLowerCase();
    }

    function isEmail(str){
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(str);
    }

    function isUrl(str){
      return (/^(https?):\/\/((?:[a-z0-9.-]|%[0-9A-F]{2}){3,})(?::(\d+))?((?:\/(?:[a-z0-9-._~!$&'()*+,;=:@]|%[0-9A-F]{2})*)*)(?:\?((?:[a-z0-9-._~!$&'()*+,;=:\/?@]|%[0-9A-F]{2})*))?(?:#((?:[a-z0-9-._~!$&'()*+,;=:\/?@]|%[0-9A-F]{2})*))?$/i).test(str);
    }

    function startsWith(str, prefix){
      return str.indexOf(prefix) === 0;
    }

    function endsWith(str, suffix){
      return str.indexOf(suffix, str.length - suffix.length) !== -1;
    }

    function randInt(min, max){
      return Math.floor(Math.random()*(max - min + 1)) - min;
    }

    function toDate(date){
      if(typeof date === 'number'){ return new Date(date); } // timestamp
      if(typeof date === 'string'){ return new Date(date); } // iso string
      if(date instanceof Date){ return date; } // JavaScript Date
      if(date && typeof date.toDate === 'function' && date.toDate() instanceof Date){ return date.toDate(); } // moment Date
    }

    function isDate(date){
      var d = toDate(date);
      return d instanceof Date && d.toString() !== 'Invalid Date';
    }

    function async(fn){
      var defer = $q.defer();
      $timeout(function(){
        defer.resolve(fn());
      }, 0);
      return defer.promise;
    }

    function trustHtml(html){
      return $sce.trustAsHtml(html);
    }

    var debounces = [];
    function debounce(key, callback, _debounceTime){
      $timeout.cancel(debounces[key]);
      debounces[key] = $timeout(function(){
        callback();
      }, _debounceTime || 1000);
    }

    // like angular.merge() (but for previous angular versions)
    function extendDeep(dest){
      angular.forEach(arguments, function(arg){
        if(arg !== dest){
          angular.forEach(arg, function(value, key){
            if(dest[key] && typeof dest[key] === 'object'){
              extendDeep(dest[key], value);
            } else {
              dest[key] = angular.copy(value);
            }
          });
        }
      });
      return dest;
    }

    function extendsWith(dest, src){
      for(var i in src){
        if(typeof src[i] === 'object'){
          if(dest[i] === undefined || dest[i] === null){
            dest[i] = angular.copy(src[i]);
          } else if(typeof dest[i] === 'object'){
            extendsWith(dest[i], src[i]);
          }
        } else if(typeof src[i] === 'function'){
          // nothing
        } else if(dest[i] === undefined || dest[i] === null){
          dest[i] = src[i];
        }
      }
    }



    function sort(arr, params){
      if(Array.isArray(arr) && arr.length > 0 && params && params.order){
        var firstElt = null;
        for(var i in arr){
          firstElt = _getDeep(arr[i], params.order.split('.'));
          if(typeof firstElt !== 'undefined'){ break; }
        }
        if(typeof firstElt === 'boolean')      { _boolSort(arr, params); }
        else if(typeof firstElt === 'number')  { _intSort(arr, params);  }
        else if(typeof firstElt === 'string')  { _strSort(arr, params);  }
        else {
          $log.warn('Unable to find suitable sort for type <'+(typeof firstElt)+'>', firstElt);
        }
      }
    }

    function _strSort(arr, params){
      arr.sort(function(a, b){
        var aStr = _getDeep(a, params.order.split('.'), '').toLowerCase();
        var bStr = _getDeep(b, params.order.split('.'), '').toLowerCase();
        if(aStr > bStr)       { return 1 * (params.desc ? -1 : 1);   }
        else if(aStr < bStr)  { return -1 * (params.desc ? -1 : 1);  }
        else                  { return 0;                     }
      });
    }
    function _intSort(arr, params){
      arr.sort(function(a, b){
        var aInt = _getDeep(a, params.order.split('.'), 0);
        var bInt = _getDeep(b, params.order.split('.'), 0);
        return (aInt - bInt) * (params.desc ? -1 : 1);
      });
    }
    function _boolSort(arr, params){
      arr.sort(function(a, b){
        var aBool = _getDeep(a, params.order.split('.'), 0);
        var bBool = _getDeep(b, params.order.split('.'), 0);
        return (aBool === bBool ? 0 : (aBool ? -1 : 1)) * (params.desc ? -1 : 1);
      });
    }

    function getDeep(obj, path, _defaultValue){
      return _getDeep(obj, path.split('.'), _defaultValue);
    }

    function _getDeep(obj, attrs, _defaultValue){
      if(Array.isArray(attrs) && attrs.length > 0){
        if(typeof obj === 'object'){
          var attr = attrs.shift();
          return _getDeep(obj[attr], attrs, _defaultValue);
        } else {
          return _defaultValue;
        }
      } else {
        return typeof obj === 'undefined' ? _defaultValue : obj;
      }
    }
	
	function formRequest(url, data) {
		return $http({
		method: 'POST',
		url: url,
		headers: {'Content-Type': 'application/x-www-form-urlencoded'},
		transformRequest: function(obj) {
			var str = [];
			for(var p in obj)
			str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
			return str.join("&");
		},
		data: data
		})
	}
	
	function formatSyncTime(date) {
		moment.locale('en');
		return moment(date).format('DD MMM YYYY hh:mm a');
	}
	
	
	function checkConnection() {
		var networkState = navigator.network.connection.type;
		var states = {};
		states[Connection.UNKNOWN]  = 'Unknown connection';
		states[Connection.ETHERNET] = 'Ethernet connection';
		states[Connection.WIFI]     = 'WiFi connection';
		states[Connection.CELL_2G]  = 'Cell 2G connection';
		states[Connection.CELL_3G]  = 'Cell 3G connection';
		states[Connection.CELL_4G]  = 'Cell 4G connection';
		states[Connection.NONE]     = 'No network connection';
		//console.log('Connection : ' + Connection);
		//console.log('Connection type: ' + states[networkState]);
		return networkState;
	}
	
	
	function effectiveDeviceWidth() {
		var deviceWidth = window.orientation == 0 ? window.screen.width : window.screen.height;
		// iOS returns available pixels, Android returns pixels / pixel ratio
		// http://www.quirksmode.org/blog/archives/2012/07/more_about_devi.html
		if (navigator.userAgent.indexOf('Android') >= 0 && window.devicePixelRatio) {
			deviceWidth = deviceWidth / window.devicePixelRatio;
		}
		return deviceWidth;
	}
	
	function splitFilePath(p) {
		var splt = p.split('/');
		var fileName = splt[splt.length-1];
		var dirName = splt[splt.length-2];
		var path = splt.slice(0, splt.length-2).join('/');
		return {
			'filename' : fileName,
			'dirname' : dirName,
			'path': path
		};
	}
	
		/*
		var srcPath = '';
		var imageDir = 'images';
		
		filename = dest.slice(dest.lastIndexOf('/')+1)
		
		var destPath = cordova.file.externalApplicationStorageDirectory
		*/
	/*
		var copy = function(fileEntry) {
			 window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSys) { 
				fileSys.root.getDirectory("photos", {create: true, exclusive: false}, function(dir) { 
						fileEntry.copyTo(dir, fileEntry.name, onCopySuccess, fail); 
					}, fail); 
			}, fail); 
		}
		
		var onCopySuccess = function(entry) {
			console.log('Copied image to',entry.fullPath);
		}
		
		var fail = function(e) {
			console.log('Error copying file with error:',e);
		}
		
		window.resolveLocalFileSystemURL(src, copy, fail); */   
	function moveFile(src, dest, newName) {
		var srcObj = splitFilePath(src);
		var destObj = splitFilePath(dest);
		
		var srcPathDir = srcObj.path+'/'+srcObj.dirname;
		var destPathDir = destObj.path+'/'+destObj.dirname;
		//if (typeof newName != 'undefined') destObj.filename = newName;
		if (typeof newName == 'undefined') newName = destObj.filename;
		console.log('srcObj',srcObj);
		console.log('destObj',destObj);
		console.log('checking',destObj.path, destObj.dirname);
	
		return $cordovaFile.checkDir(destObj.path+'/', destObj.dirname).then(function(s){
			// Dir exists
			//console.log(s);
		}, function(e){
			// Dir does not exist, create
			if (e.code == 1) {
				//console.log('Creating dir', destObj.path, destObj.dirname);
				return $cordovaFile.createDir(destObj.path+'/', destObj.dirname);
			}
		}).then(function(){
			return $cordovaFile.checkFile(srcPathDir+'/', srcObj.filename);
			
		}).then(function(s){
			//console.log('Moving '+ srcPathDir,srcObj.filename);
			//console.log('To '+ destPathDir, srcObj.filename);
			return $cordovaFile.moveFile(srcPathDir, srcObj.filename, destPathDir, newName);
		})
		.then(function(s) {
			console.log('Success?',s);
			return s.nativeURL;
		}, function(e){
			console.log('Error!',e);
			if (e.code == 1) {
				console.log('Source file not found!');
			}
		})
	}
	
	
	// Delete file
	function removeFile(fullPath) {
		var fileObj = splitFilePath(fullPath);
		return $cordovaFile.removeFile(fileObj.path+'/'+fileObj.dirname+'/', fileObj.filename);
	}
	
	
	// Save blob (binary form of stuff) to filesystem
	function saveBlobToFile(fullPath, blob, replace) {
		var fileObj = splitFilePath(fullPath);
		//console.log('hoohohohoo',fileObj.path+'/'+fileObj.dirname+'/',fileObj.filename)
		return $cordovaFile.writeFile(fileObj.path+'/'+fileObj.dirname+'/',fileObj.filename, blob, replace);
	}
	
	function base64toBlob(base64Data, contentType) {
		contentType = contentType || '';
		var sliceSize = 1024;
		var byteCharacters = atob(base64Data);
		var bytesLength = byteCharacters.length;
		var slicesCount = Math.ceil(bytesLength / sliceSize);
		var byteArrays = new Array(slicesCount);

		for (var sliceIndex = 0; sliceIndex < slicesCount; ++sliceIndex) {
			var begin = sliceIndex * sliceSize;
			var end = Math.min(begin + sliceSize, bytesLength);

			var bytes = new Array(end - begin);
			for (var offset = begin, i = 0 ; offset < end; ++i, ++offset) {
				bytes[i] = byteCharacters[offset].charCodeAt(0);
			}
			byteArrays[sliceIndex] = new Uint8Array(bytes);
		}
		return new Blob(byteArrays, { type: contentType });
	}
	
	function cropImage(fpath) {
		var width = effectiveDeviceWidth();
		var height = width*0.75;
		return $jrCrop.crop({url:fpath, width:width, height:height}).then(function(a){
			var blob = base64toBlob(a.toDataURL().split(',')[1],'image/png');
			var starttime = Date.now();
			return saveBlobToFile(fpath,blob,true).then(function(b){
				//console.log('Ended writing'/*,b,Date.now()-starttime*/); 
				//console.log(blob);
			}, function(e){
				console.log('Error writing from crop',e);
			});
		})
	}
	
	// CompressImage will output the compressed image to a new file, and move it back to replace the original image
	function compressImage(fpath) {
		var deferred = $q.defer();
		ImageResizer.resize({uri: fpath, folderName:'temp', quality:90, width:800, height:600}, function(path){
			deferred.resolve(path);
		})
		return deferred.promise.then(function(path){
			//Replace back the original image.
			//console.log('I did it!',path,fpath);
			console.log('Compressed file. Now moving from',path,fpath);

			return moveFile(path, fpath);
		});
	}
    

    return service;
  }
})();
