/**
 * WC Simple Waiting List
 * http://imakeplugins.com
 *
 * Licensed under the GPLv2+ license.
 */

window.WCSimpleWaitingList = window.WCSimpleWaitingList || {};

( function( window, document, $, plugin ) {
	let $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window = $( window );
		$c.body = $( document.body );
	};

	plugin.bindEvents = function() {

		$( '#register_user' ).on( 'click', function() {
			var product_id = $( this ).data( 'product-id' );
			var user_email = $( '#emailaddr' ).val();

			if ( $.trim( user_email ).length == 0 ) {
				user_email = $( this ).data( 'user-email' );
			}

			if ( $.trim( user_email ).length == 0 || $.trim( product_id ).length == 0 ) {
				alert( 'An Error Has Occur!' );
				return false;
			}

			if ( ! validateEmail( user_email ) ) {
				alert( 'Invalid Email Address' );
				return false;
			}

			$( '#emailaddr' ).hide();
			$( '#register_user' ).hide();

			var post_data = {
				action: 'wc_simple_waiting_list_register_user',
				product_id: product_id,
				user_email: user_email,
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_public_vars.nonce
			};

			$.post( wc_simple_waiting_list_public_vars.ajaxurl, post_data, function( response ) {
				if ( response == 'success' ) {
					$( '#deregister_user' ).show();
				} else {
					$( '#emailaddr' ).show();
					$( '#register_user' ).show();
					alert( wc_simple_waiting_list_public_vars.error_message );
				}
			});
			return false;
		});

		$( '#deregister_user' ).on( 'click', function() {
			var product_id = $( this ).data( 'product-id' );
			var user_email = $( '#emailaddr' ).val();

			if ( $.trim( user_email ).length == 0 ) {
				user_email = $( this ).data( 'user-email' );
			}

			if ( $.trim( user_email ).length == 0 || $.trim( product_id ).length == 0 ) {
				alert( 'An Error Has Occur!' );
				return false;
			}

			$( '#deregister_user' ).hide();

			var post_data = {
				action: 'wc_simple_waiting_list_user_deregister_user',
				product_id: product_id,
				user_email: user_email,
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_public_vars.nonce
			};

			$.post( wc_simple_waiting_list_public_vars.ajaxurl, post_data, function( response ) {
				if ( response == 'success' ) {
					$( '#register_user' ).show();
					$( '#emailaddr' ).show();;
				} else {
					$( '#deregister_user' ).show();
					alert( wc_simple_waiting_list_public_vars.error_message );
				}
			});
			return false;
		});
	};

	function validateEmail( sEmail ) {
		var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if ( filter.test( sEmail ) ) {
			return true;
		} else {
			return false;
		}
	}

	$( plugin.init );
}( window, document, require( 'jquery' ), window.WCSimpleWaitingList ) );
