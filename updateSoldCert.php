<?php
//create meta boxes
add_meta_box('wpgft_soldCert_status', __('Status'), 'wpgft_soldCertStatus_meta_box', 'wpgft', 'normal', 'core');

?>
<div class="wrap">

<form method="post" action="?page=edit_issued_cert">    
	<h2><?php echo __('Edit Cert Button'); ?> <?php } ?></h2>
    
	<div id="poststuff" class="metabox-holder">
    <?php
		do_meta_boxes('wpgftsold', 'normal','low');
	?>
	</div>

<input type="submit" value="<?php echo __('Edit Cert Button','wpgft-plugin'); } ?>" name="wpgft_edit_Soldcert" class="button-primary">
</form>

</div>
