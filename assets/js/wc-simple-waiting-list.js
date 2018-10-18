/**
 * WC Simple Waiting List - v1.0.7 - 2018-10-18
 * http://imakeplugins.com
 *
 * Copyright (c) 2018;
 * Licensed GPLv2+
 */

(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
(function (global){
'use strict';

/**
 * WC Simple Waiting List
 * http://imakeplugins.com
 *
 * Licensed under the GPLv2+ license.
 */

window.WCSimpleWaitingList = window.WCSimpleWaitingList || {};

(function (window, document, $, plugin) {
	var $c = {};

	plugin.init = function () {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function () {
		$c.window = $(window);
		$c.body = $(document.body);
	};

	plugin.bindEvents = function () {
		$('.wcswl-export-reminders').live('click', function () {
			var postdata = {
				action: 'wc_simple_waiting_list_export_csv',
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_vars.nonce
			};

			$.post(wc_simple_waiting_list_vars.ajaxurl, postdata, function (response) {
				if ('fail' == response) {
					alert(wc_simple_waiting_list_vars.error_message);
				} else {
					$('#Results').after('<a href="' + response + '">Download Report here ' + response + ' </a><br/>');
				}
			});
			return false;
		});
	};

	$(plugin.init);
})(window, document, (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null), window.WCSimpleWaitingList);

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1]);
