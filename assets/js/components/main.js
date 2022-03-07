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
		$( '.wcswl-export-reminders' ).on( 'click', function()	{
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

        $( '#wc-simple-waiting-list-feature-request-submit' ).on( 'click', function()  {
            var data = {
                msg: $( '#wc-simple-waiting-list-feature-request-msg' ).val(),
                email: $( this ).data( 'email' ),
                plugin: $( this ).data( 'plugin' ),
                blog: $( this ).data( 'blog' ),
                name: $( this ).data( 'name' )
            };
            var postdata = {
                data: data,
                action: 'wc_simple_waiting_list_feature_request',
                wc_simple_waiting_list_nonce: wc_simple_waiting_list_vars.nonce
            };

            $( this ).parents( '.one' ).animate({
                top: '-500px'
            }, 500 );

            $( this ).parents( '.one' ).siblings( '.two' ).animate({
                top: '0px'
            }, 500 );

            $.post( wc_simple_waiting_list_vars.ajaxurl, postdata, function( response ) {
                if ( 'fail' == response ) {
                    alert( wc_simple_waiting_list_vars.error_message );
                }
            });
            $( '#wc-simple-waiting-list-feature-request-msg' ).val( '' );
            return false;
        });

        $( '.wc-simple-waiting-list-review' ).on( 'click', function()  {
            var postdata = {
                action: 'wc_simple_waiting_list_update_review',
                wc_simple_waiting_list_nonce: wc_simple_waiting_list_vars.nonce
            };

            $( this ).parents( '.one' ).animate({
                top: '-500px'
            }, 500 );

            $( this ).parents( '.one' ).siblings( '.two' ).animate({
                top: '0px'
            }, 500 );

            $( document.body ).scrollTop( $( '#anchorId' ).offset().top );

            $.post( wc_simple_waiting_list_vars.ajaxurl, postdata, function( response ) {
                if ( 'fail' == response ) {
                    alert( wc_simple_waiting_list_vars.error_message );
                }
            });
            return false;
        });

        $( '.two .close' ).on( 'click', function() {
            $( this ).parent().animate({
                top: '-500px'
            }, 500 );

            $( this ).parent().siblings( '.one' ).animate({
                top: '0px'
            }, 500 );
            return false;
        });
	};

	$( plugin.init );
}( window, document, require( 'jquery' ), window.WCSimpleWaitingList ) );
