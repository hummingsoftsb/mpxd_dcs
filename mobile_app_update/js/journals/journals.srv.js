(function(){
  'use strict';
  angular.module('app')
    .factory('JournalSrv', JournalSrv);

  // This is a dummy service to use in demo...
  JournalSrv.$inject = ['$http', '$q', '$timeout', 'Utils', 'Config', '_', 'StorageUtils', 'DataSrv', 'AuthSrv'];
  function JournalSrv($http, $q, $timeout, Utils, Config, _, StorageUtils, DataSrv, AuthSrv){
    var cachedTwitts = undefined;
    var service = {
      getAllProjects: getAllProjects,
	  getAllDataEntries: getAllDataEntries,
	  getProcessedProjects: getProcessedProjects,
	  getJournalData: getJournalData,
	  getLookups: getLookups,
	  getUOMs: getUOMs,
	  saveEntryState: saveEntryState,
	  getEntryState: getEntryState,
	  setEntryInitialized: setEntryInitialized,
	  getEntryInitialized: getEntryInitialized,
	  setEntryPublished: setEntryPublished,
	  getEntryPublished: getEntryPublished,
	  hookOn: hookOn
    };
	var hooks = {};
	
	DataSrv.hookOn('pull', function(){
		//Clean up journal data after pull. Remove images stuff
	});
	
    return service;
	
	

    function getAllProjects(){
		return DataSrv.getData().then(function(r) {
			console.log('projects',r.data);
			return r.data['projects'];
		}, function(e){
			console.log('Caught error when trying to getData() from Journals SRV',e);
		})/*
		return StorageUtils.set('testdata',data["demo"]["data"]).then(function(){
			return StorageUtils.get('testdata')
		});*/
    }
	
	
	function getAllDataEntries() {
		return getAllProjects().then(function(p) {
			//console.log(p);
			var result = {};
			angular.forEach(p, function(pval, pkey) {
				var pId = pkey;
				angular.forEach(pval.journals, function(jval, jkey) {
					var jId = jkey;
					angular.forEach(jval.data_entries, function(dval, dkey) {
						var dId = dkey;
						result[dId] = {
							projectId : pId,
							journalId : jId,
							entryId : dId
						}
					});
				});
			});
			//console.log('de',result);
			return result;
		});
	}
	
	function getLookups() {
		return DataSrv.getData().then(function(r) {
			return r.data['lookups'];
		})
	}
	
	function getUOMs() {
		return DataSrv.getData().then(function(r) {
			return r.data['uoms'];
		})
	}
	
	function getJournalData(projectId, journalId) {
		return getAllProjects().then(function(p) {
			return p[projectId]['journals'][journalId];
		});
	}
	
	function saveEntryState(projectId, journalId, entryId, data) {
		var entryName = 'project-'+projectId+'-'+journalId+'-'+entryId;
		var username = AuthSrv.getSession().username;
		var uploadedStorageName = username+'-to-upload-entries';
		if (typeof data == 'undefined') data = {};
		data.timestamp = new Date().getTime();
		return StorageUtils.set(entryName, data).then(function(){
			return StorageUtils.get(uploadedStorageName).then(function(data){
				if (typeof data == 'undefined') data = [];
				//if (typeof data['dirty'] == 'undefined') data['dirty'] = {};
				//data['dirty'][entryName] = true;
				if (data.indexOf(entryName) == -1) { 
					data.push(entryName);
					return StorageUtils.set(uploadedStorageName, data);
				}
			});
		});
	}
	
	function getEntryState(projectId, journalId, entryId) {
		return StorageUtils.get('project-'+projectId+'-'+journalId+'-'+entryId);
	}
	
	
	function getProcessedProjects() {
		return $q.all([getAllProjects(),getAllEntryPublished()]).then(function(values){
			var r = values[0];
			var published = values[1];
			var result = [];
			angular.forEach(r, function(projval, projkey) {
				var project = angular.copy(projval);
				var journals = [];
				angular.forEach(project.journals, function(jourval, jourkey) {
					var journal = {
						journal_no: jourval.journal_no,
						journal_name: jourval.journal_name
					};
					var data_entries = [];
					angular.forEach(jourval.data_entries, function(dataval, datakey) {
						var data_entry = dataval;
						var is_image = (data_entry.is_image == "1");
						var initialized = (data_entry.count != 0);
						var week = data_entry.frequency_period;
						var entryName = 'project-'+projkey+'-'+jourkey+'-'+datakey;
						//console.log('yes!',published,entryName);
						if ((typeof published != 'undefined') && (published.indexOf(entryName) != -1)) { return false;}
						data_entries.push({
							data_entry_no: data_entry.data_entry_no,
							is_image: is_image,
							initialized: initialized,
							week: 'W'+week
						});
						
						
					});
					
					// Should check for this logic if something seems wrong about data entry in journals
					// journal.data_entries = data_entries;
					
					if (typeof data_entries[0] == 'undefined') return false;
					journal.is_image = data_entries[0].is_image;
					journal.initialized = data_entries[0].initialized;
					journal.week = data_entries.map(function(a){ return a.week }).join(', ');
					journal.data_entries = data_entries.map(function(a){ return a.data_entry_no });
					journals.push(journal);
				});
				if (journals.length == 0) return false;
				project.journals = journals;
				result.push(project);
			});
			return result;
		});
	}
	
	function setEntryInitialized(projectId, journalId, entryId) {
		var entryName = 'project-'+projectId+'-'+journalId+'-'+entryId;
		var username = AuthSrv.getSession().username;
		var initializedStorageName = username+'-initialized-entries';
		
		
		StorageUtils.get(initializedStorageName).then(function(inited){
			if (typeof inited == 'undefined') {
				return StorageUtils.set(initializedStorageName, [entryName]);
			} else {
				if (inited.indexOf(entryName) != -1) return true;
				return StorageUtils.set(initializedStorageName, inited.concat([entryName]));
			}
		});
	}
	
	function getEntryInitialized(projectId, journalId, entryId) {
		var username = AuthSrv.getSession().username;
		var initializedStorageName = username+'-initialized-entries';
		var entryName = 'project-'+projectId+'-'+journalId+'-'+entryId;
		var journalPromise = getJournalData(projectId, journalId);
		
		var initializedPromise = StorageUtils.get(initializedStorageName);
		return $q.all([journalPromise, initializedPromise]).then(function(values){
			var journal = values[0];
			var inited = values[1];
			if (journal.data_entries[entryId].count > 0) {console.log('here1'); return true;}
			if ((typeof inited == 'undefined') || (inited.indexOf(entryName) == -1)) {
				return false;
			} else {
				//console.log('here2',inited.indexOf(entryName));
				return true;
			}
			return false;
		});
	}
	
	function setEntryPublished(projectId, journalId, entryId){
		var entryName = 'project-'+projectId+'-'+journalId+'-'+entryId;
		var username = AuthSrv.getSession().username;
		var publishedStorageName = username+'-published-entries';
		
		return StorageUtils.get(publishedStorageName).then(function(pubbed){
			if (typeof pubbed == 'undefined') {
				return StorageUtils.set(publishedStorageName, [entryName]).then(function(){runHook('published')});
			} else {
				if (pubbed.indexOf(entryName) != -1) { runHook('published'); return true };
				return StorageUtils.set(publishedStorageName, pubbed.concat([entryName])).then(function(){runHook('published')});
			}
		});
	}
	
	function getEntryPublished(projectId, journalId, entryId) {
		var entryName = 'project-'+projectId+'-'+journalId+'-'+entryId;
		var username = AuthSrv.getSession().username;
		var publishedStorageName = username+'-published-entries';
		return StorageUtils.get(publishedStorageName);
	}
	function getAllEntryPublished() {
		var username = AuthSrv.getSession().username;
		var publishedStorageName = username+'-published-entries';
		return StorageUtils.get(publishedStorageName);
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
