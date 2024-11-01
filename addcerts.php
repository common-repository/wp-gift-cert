<?php
//create meta boxes
global $current_button;
$current_button = get_current_button($_GET['id']);

add_meta_box('wpgft_certBtn_amount', __('Amount'), 'wpgft_certBtn_amount_meta_box', 'wpgft', 'normal', 'core');
add_meta_box('wpgft_certBtn_description', __('Description'), 'wpgft_certBtn_description_meta_box', 'wpgft', 'normal', 'core');
add_meta_box('wpgft_certBtn_company', __('Company'), 'wpgft_certBtn_company_meta_box', 'wpgft', 'normal', 'core');
add_meta_box('wpgft_certBtn_coinfo', __('Company Info'), 'wpgft_certBtn_coinfo_meta_box', 'wpgft', 'normal', 'core');

add_meta_box('wpgft_certBtn_button', __('Button'), 'wpgft_certBtn_button_meta_box', 'wpgft', 'normal', 'core');
?>
<div class="wrap">

<form method="post" action="?page=certificate_management">    
	<h2><?php if(isset($_GET['id'])) { echo __('Edit Cert Button'); } else { echo __('Add Cert Button'); ?> <?php } ?></h2>
    <em>Leave Company Blank to use default company (set on Settings page)</em>
	<div id="poststuff" class="metabox-holder">
    <?php
		do_meta_boxes('wpgft', 'normal','low');
	?>
	</div>

<input type="submit" value="<?php if(isset($_GET['id'])) { echo __('Edit Cert Button','wpgft-plugin'); } else { echo __('Add Cert Button','wpgft-plugin'); } ?>" name="wpgft_add_cert" class="button-primary">
</form>

</div>
