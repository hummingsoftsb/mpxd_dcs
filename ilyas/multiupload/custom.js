
function checkAndSendAllImages() {
	if (checkImageUpload()) {
		sendAll();
	}
	return false;
}

function checkImageUpload(){
	var ok = true;
	$.each($('.description-textarea'), function(a){
		if ($(this).val() == "") ok = false;
	});
	
	if (!ok) { 
		alert("Image descriptions are compulsory.");
	}
	
	return ok;
}

/*jslint unparam: true, regexp: true */
/*global window, $ */

function disableBeforeSend() {
	$('.description-textarea').attr('disabled','disabled');
	$('#addimage button').attr('disabled','disabled');
	$('.closebutton').attr('disabled','disabled');
	$('#MyModal .close').attr('disabled','disabled');
	window.onbeforeunload = function(e){
		e = e || window.event;
		if  (e) {
			e.returnValue = 'Upload in progress, are you sure?';
		}
		return 'Upload in progress';
	}
}

function enableAfterSend() {
	$('.description-textarea').removeAttr('disabled');
	$('#addimage button').removeAttr('disabled');
	$('.closebutton').removeAttr('disabled');
	$('#MyModal .close').removeAttr('disabled');
	window.onbeforeunload = null;
}

function generateHidden() {
	$('.description-textarea').each(function(idx, i){
		var $t = $(this);
		var $hidden = $('<input type="hidden">');
		$hidden.addClass('temporary-hidden-textarea')
		.val($t.val())
		.attr('name',$t.attr('name'));
		$(this).parent().append($hidden);
	});
}

function removeHidden() {
	$('.temporary-hidden-textarea').remove();
}

function resolve(pending) {
	if (typeof pending.files.error != 'undefined') {
		//var d = $.Deferred();
		//setTimeout(d.resolve,0);
		return pending.abort().promise();
	}
	return pending.submit().promise();
}

// YOU NEED TO CHECK ERRORS, AND THEN AUTO-REFRESHER
function sendAll() {
	shouldRefresh = false;
	var deferred = $.Deferred();
	disableBeforeSend();
	generateHidden();
	pendingFiles.reduce(function(promise, pending){
		return promise.then(function(){
			return resolve(pending);
		}, function(e){
			return resolve(pending);
		});
	}, deferred.promise()).then(function(){
		console.log('Finished successfully');
		pendingFiles = [];
		removeHidden();
		enableAfterSend();
	}, function(e){ 
		console.log('Finished errornously');
		pendingFiles = [];
		removeHidden();
		enableAfterSend();
	});
	setTimeout(deferred.resolve, 0);
}

var shouldRefresh = false;
var pendingFiles = [];
$(function () {
    'use strict';
        var uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });
    $('#addimage').fileupload({
        url: uploadUrl,
        dataType: 'json',
        autoUpload: false,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 10000000,
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: true,
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: false,
		fail: function(e, data) {
			if (e.isDefaultPrevented()) {
                    return false;
                }
				$.each(pendingFiles, function(idx, i) {
					if (i == data) pendingFiles.splice(idx,1);
				});
                var that = $(this).data('blueimp-fileupload') ||
                        $(this).data('fileupload'),
                    template,
                    deferred;
                if (data.context) {
                    data.context.each(function (index) {
                        if (data.errorThrown !== 'abort') {
                            var file = data.files[index];
                            file.error = file.error || data.errorThrown ||
                                data.i18n('unknownError');
                            deferred = that._addFinishedDeferreds();
                            that._transition($(this)).done(
                                function () {
                                    var node = $(this);
                                    template = that._renderDownload([file])
                                        .replaceAll(node);
                                    that._forceReflow(template);
                                    that._transition(template).done(
                                        function () {
                                            data.context = $(this);
                                            that._trigger('failed', e, data);
                                            that._trigger('finished', e, data);
                                            deferred.resolve();
                                        }
                                    );
                                }
                            );
                        } else {
                            deferred = that._addFinishedDeferreds();
                            that._transition($(this)).done(
                                function () {
                                    $(this).remove();
                                    that._trigger('failed', e, data);
                                    that._trigger('finished', e, data);
                                    deferred.resolve();
                                }
                            );
                        }
                    });
                } else if (data.errorThrown !== 'abort') {
                    data.context = that._renderUpload(data.files)[
                        that.options.prependFiles ? 'prependTo' : 'appendTo'
                    ](that.options.filesContainer)
                        .data('data', data);
                    that._forceReflow(data.context);
                    deferred = that._addFinishedDeferreds();
                    that._transition(data.context).done(
                        function () {
                            data.context = $(this);
                            that._trigger('failed', e, data);
                            that._trigger('finished', e, data);
                            deferred.resolve();
                        }
                    );
                } else {
                    that._trigger('failed', e, data);
                    that._trigger('finished', e, data);
                    that._addFinishedDeferreds().resolve();
                }
		}
    }).on('fileuploadadd', function (e, data) {
		pendingFiles.push(data);
        data.context = $('<div/>').appendTo('#files');
        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                    .append($('<span/>').text(file.name));
            if (!index) {
                node
                    .append('<br>')
                    .append(uploadButton.clone(true).data(data));
            }
            node.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                //.prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
        $.each(data.result.files, function (index, file) {
            if (file.url) {
				shouldRefresh = true;
                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
                $(data.context.children()[index])
                    .wrap(link);
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }
        });
    }).on('fileuploadfinished',function(e,data) {
		data.context.find('.remove').off().on('click', function(e){
			e.preventDefault();
			$(this).parents('tr').remove();
		});
	}).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
		
	$('.closebutton, #MyModal .close').on('click',function(){
		if (shouldRefresh) location.reload();
	})
});