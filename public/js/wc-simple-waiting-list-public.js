(function( $ ) {
	'use strict';

	$(function() {

		$("#save").live('click',function(){
			var product_id = $(this).data('product-id');
			var user_email = $("#emailaddr").val();

			if ($.trim(user_email).length == 0)
				user_email = $(this).data('user-email');

			if ($.trim(user_email).length == 0 || $.trim(product_id).length == 0)
			{
				alert('An Error Has Occur!');
				return false;
			}

			if (!validateEmail(user_email)) {
				alert('Invalid Email Address');
				return false;
			}

			$("#emailaddr").hide()
			$("#save").hide();

			var post_data = {
				action: 'wc_simple_waiting_list_user_add',
				product_id: product_id,
				user_email: user_email,
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_vars.nonce
			};

			$.post(wc_simple_waiting_list_vars.ajaxurl, post_data, function(response) {
				if(response == 'success') {
					$("#remove").show()
				} else {
					$("#emailaddr").show()
					$("#save").show();
					alert(wc_simple_waiting_list_vars.error_message);
				}
			});
			return false;
		});

		$("#remove").live('click',function(){
			var product_id = $(this).data('product-id');
			var user_email = $("#emailaddr").val();

			if ($.trim(user_email).length == 0)
				user_email = $(this).data('user-email');

			if ($.trim(user_email).length == 0 || $.trim(product_id).length == 0)
			{
				alert('An Error Has Occur!');
				return false;
			}
			$("#remove").hide()

			var post_data = {
				action: 'wc_simple_waiting_list_user_del',
				product_id: product_id,
				user_email: user_email,
				wc_simple_waiting_list_nonce: wc_simple_waiting_list_vars.nonce
			};

			$.post(wc_simple_waiting_list_vars.ajaxurl, post_data, function(response) {
				if(response == 'success') {
						$("#save").show()
						$("#emailaddr").show();
				} else {
					$("#remove").show()
					alert(wc_simple_waiting_list_vars.error_message);
				}
			});
			return false;
		});
	});

	function validateEmail(sEmail) {
		var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if (filter.test(sEmail)) {
			return true;
		}
		else {
			return false;
		}
	}
})( jQuery );
