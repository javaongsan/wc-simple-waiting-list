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
		$( '.wcswl-export-reminders' ).live( 'click', function()	{
			var postdata = {
			action: 'wc_simple_waiting_list_export_csv',
			wc_simple_waiting_list_nonce: wc_simple_waiting_list_vars.nonce
			};

			$.post( wc_simple_waiting_list_vars.ajaxurl, postdata, function( response ) {
				if ( 'fail' == response ) {
					alert( wc_simple_waiting_list_vars.error_message );
				} else {
					$( '#Results' ).after( '<a href="' + response + '">Download Report here '  + response +  ' </a><br/>' );
				}
			});
			return false;
		});
	};

	$( plugin.init );
}( window, document, require( 'jquery' ), window.WCSimpleWaitingList ) );
