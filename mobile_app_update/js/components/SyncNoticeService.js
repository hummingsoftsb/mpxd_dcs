/*
 |--------------------------------------------------------------------------
 | SyncNoticeService
 |--------------------------------------------------------------------------
 |
 | Global Loader Factory, use for fancy effect when saving, loading, deleting
 |
 */
var SyncNoticeService = function () {};

/**
 * the HTML that will be created and appended to the document as the effect
 *
 * @param loaderText
 * @param loaderMode
 * @returns {string}
 */
SyncNoticeService.prototype.effect = function () {

    return '<div style="position:absolute; bottom:0px; z-index:10; width:100%; padding: 0 10px;" id="sync_notice_container"><div style="position:relative">\
	<div style="position:absolute; top:0; right:10px;">\
	<button class="button button-icon icon ion-ios-close-empty" onclick="vm.dismissDialog()" style="color:#fff"></button>\
	</div>\
	<div style="background: rgba(0,0,0,0.85); color:#fff; padding:2px 16px;">\
\
	<h4 style="color:#fff;"><i class="icon ion-android-alert" style="font-size:17px; color:#fff;"></i> Synchronize pending</h4>\
		<p>Device is not sync with server.</p>\
	<button class="button button-small button-clear button-light" ng-click="vm.startSynchronize()" style="padding-left:0; padding-bottom:10px;">\
		SYNC NOW\
	</button>\
	<button class="button button-small button-clear button-light" ng-click="vm.dismissDialog()" style="color: #888; margin-left:10px; padding-bottom:10px;">\
		DISMISS\
	</button>\
	</div>\
	</div></div>';

};

/**
 * triggers to show the effect
 *
 * @param message
 * @param mode
 * @returns {*|void}
 */
SyncNoticeService.prototype.show = function (message,mode) {

    this.remove(); // make sure to remove the previous effect


    angular.element(document.getElementsByTagName('body')).append(this.effect());
    return this;

};

/**
 * triggers to hide the effect slowly
 *
 * @returns {*}
 */
SyncNoticeService.prototype.hide = function () {

    angular.element("#globalEffect").removeClass('animated fadeInDown').fadeOut(3000,function(){ this.remove(); });
    return this;

};

/**
 * removes the appended fancy effect immediately
 *
 * @returns {*}
 */
SyncNoticeService.prototype.remove = function () {

    angular.element("#globalEffect").remove();
    return this;

};

app.service('SyncNoticeService',SyncNoticeService);