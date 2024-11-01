jQuery(document).ready( function () {
// this changes the purchase log item status
		 jQuery('.selector').change(function(){	
				cert_id = jQuery(this).attr('title');
				cert_status = jQuery(this).val();
				post_values = "cert_id="+cert_id+"&cert_status="+cert_status;
				jQuery.post( 'index.php?wpgft_admin_action=cert_edit_status', post_values, function(returned_data) { });
		 });
});