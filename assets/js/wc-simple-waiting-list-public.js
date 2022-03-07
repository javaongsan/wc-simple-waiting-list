/**
 * WC Simple Waiting List - v1.0.11 - 2019-05-03
 * http://imakeplugins.com
 *
 * Copyright (c) 2019;
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

		$('#register_user').on('click', function () {
			var product_id = $(this).data('product-id');
			var user_email = $('#emailaddr').val();

			if ($.trim(user_email).length == 0) {
				user_email = $(this).data('user-email');
			}

			if ($.trim(user_email).length == 0 || $.trim(product_id).length == 0) {
				alert('An Error Has Occur!');
				return false;
			}

			if (!validateEmail(user_email)) {
				alert('Invalid Email Address');
				return false;
			}

			$('#emailaddr').hide();
			$('#register_user').hide();

			var post_data = {
				action: 'wc_simple_waiting_list_register_user',
				product_id: product_id,
				user_email: user_email,
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_public_vars.nonce
			};

			$.post(wc_simple_waiting_list_public_vars.ajaxurl, post_data, function (response) {
				if (response == 'success') {
					$('#deregister_user').show();
				} else {
					$('#emailaddr').show();
					$('#register_user').show();
					alert(wc_simple_waiting_list_public_vars.error_message);
				}
			});
			return false;
		});

		$('#deregister_user').on('click', function () {
			var product_id = $(this).data('product-id');
			var user_email = $('#emailaddr').val();

			if ($.trim(user_email).length == 0) {
				user_email = $(this).data('user-email');
			}

			if ($.trim(user_email).length == 0 || $.trim(product_id).length == 0) {
				alert('An Error Has Occur!');
				return false;
			}

			$('#deregister_user').hide();

			var post_data = {
				action: 'wc_simple_waiting_list_user_deregister_user',
				product_id: product_id,
				user_email: user_email,
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_public_vars.nonce
			};

			$.post(wc_simple_waiting_list_public_vars.ajaxurl, post_data, function (response) {
				if (response == 'success') {
					$('#register_user').show();
					$('#emailaddr').show();;
				} else {
					$('#deregister_user').show();
					alert(wc_simple_waiting_list_public_vars.error_message);
				}
			});
			return false;
		});
	};

	function validateEmail(sEmail) {
		var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if (filter.test(sEmail)) {
			return true;
		} else {
			return false;
		}
	}

	$(plugin.init);
})(window, document, (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null), window.WCSimpleWaitingList);

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1]);
