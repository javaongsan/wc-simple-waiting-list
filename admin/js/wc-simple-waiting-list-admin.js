(function( $ ) {
	'use strict';

	$(".exportreminders").live('click',function()	{
		var post_data = {
		action: 'wc_simple_waiting_list_export_csv',
		wc_simple_waiting_list_nonce: wc_simple_waiting_list_admin_vars.nonce
		};

		$.post(wc_simple_waiting_list_admin_vars.ajaxurl, post_data, function(response) {
			if(response == 'fail') {
				alert(wc_simple_waiting_list_admin_vars.error_message);
			} else {
				$('#Results').after('<a href="' + response + '">Download Report here '  + response +  ' </a><br/>');
			}
		});
	  	return false;
	});


})( jQuery );
