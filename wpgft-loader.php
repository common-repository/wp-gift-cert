<?php
		global $wpgft_db_version;
		global $wpgft_current_version;
		
		$wpgft_db_version = "1.1";
		$wpgft_current_version = "1.1";
		
		//Register the hook to set everything up on Activation
		register_activation_hook(WP_PLUGIN_DIR . '/wp-gift-cert/wpgft.php', 'wpgft_install');
		
		add_action('plugins_loaded', 'wpgft_update_check');
		
		// Action hook to register our option settings
		add_action( 'admin_init', 'wpgft_register_settings');
		
		//Create Custom Plugin Settings Menu
		add_action('admin_menu', 'wpgft_create_menu');
		
	/** 
	*  Function:   wpgft_install 
	*
	*  Description: 
	*  Sets up the plugin data when installed & activated
	*
	*
	*/ 	
	function wpgft_install() {
		global $wpdb;
		global $wpgft_db_version;
		global $wpgft_current_version;
		//Defines the Custom Table Name
		$table_name = $wpdb->prefix . "wpgft_data";
		
		//Table Structure Version
		$wpgft_db_version = "1.1";
		$wpgft_current_version = "1.0";
		
		//Verify the Database Doesn't Already Exist
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//Create the Options in the Options Table
			$wpgft_options_arr = array(
				"current_ver"		=> $wpgft_current_version,
				"paypal_id"			=> 'Your Paypal ID',
				"paypal_url" 		=> "https://www.sandbox.paypal.com/cgi-bin/webscr",
				"currency"			=> "USD",
				"admin_email" 		=> "info@domain.com",
				"require_address" 	=> "yes",
				"disable_css"		=> "no",
				"company"			=> "Your Company Name",
				"company_info"		=> "",
				"custom_return"		=> "",
				"email_message" 	=> 'Thank You for purchasing a Gift Certificate from us. If you have any questions or problems please contact us at (865)555-1212 or via our <a href="http://www.suburbanmedia.net/get-a-quote">Contact Page</a>.
<br /><br />
You must be able to receive HTML formatted messages to print this certificate. Make sure you allow images as well. This certificate cannot be redeemed for cash.',
				"return_page"		=> 'This is the return page content displayed when a user is redirected to your site from PayPal. If you enter a Custom Return page in the box above, this box is pretty much just worthless.'
				);
			
			$wpgft_buttons_arr = array();
			
			//Update the options in the database
			update_option('wpgft_options', $wpgft_options_arr);
			update_option('wpgft_buttons', $wpgft_buttons_arr);
			
			//Build the Query to Create the DatabaseTest
			$sql = "CREATE TABLE {$wpdb->wpgft_data} (
				`ID` INT(32) NOT NULL auto_increment,
				`cert_num` varchar(55),
				`recipient` varchar(55),
				`cert_amount` varchar(55),
				`sold_to` varchar(55),
				`sold_to_email` varchar(55),
				`sold_to_phone` varchar(55),
				`sold_to_address` varchar(220),
				`sale_date` varchar(55),
				`status` varchar(55),
				`secret` varchar(220),
				`button_id` varchar(100),
				PRIMARY KEY  (ID)
				);";
				
			include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			//execute the query creating the table
			dbDelta($sql);
			
			//save the table structure version number
			add_option("wpgft_db_version", $wpgft_db_version);
		} else {
			$wpgft_options_arr = get_option("wpgft_options");
			$wpgft_installed_ver = $wpgft_options_arr['current_ver'];
			$installed_db_version = get_option("wpgft_db_version");
			
			//Check to see if the installed version is different than the current version running, if so add any new options
			if($wpgft_current_version != $wpgft_installed_ver) {
				$wpgft_options_arr['currency'] = 'USD';
				$wpgft_options_arr['require_address'] = 'yes';
				$wpgft_options_arr['disable_css'] = 'no';
				$wpgft_options_arr['current_ver'] = $wpgft_current_version;
				$wpgft_options_arr['custom_return'] = '';
				update_option('wpgft_options', $wpgft_options_arr);
			}
			
			if($installed_db_version <= $wpgft_db_version) {
				
				$sql = "CREATE TABLE {$wpdb->wpgft_data} (
				`ID` INT(32) NOT NULL auto_increment,
				`cert_num` varchar(55),
				`recipient` varchar(55),
				`cert_amount` varchar(55),
				`sold_to` varchar(55),
				`sold_to_email` varchar(55),
				`sold_to_phone` varchar(55),
				`sold_to_address` varchar(220),
				`sale_date` varchar(55),
				`status` varchar(55),
				`secret` varchar(220),
				`button_id` varchar(100),
				PRIMARY KEY  (ID)
				);";
				
				include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				update_option('wpgft_db_version', $wpgft_db_version);
				
				include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				//execute the query creating the table
				dbDelta($sql);
			}
		}
		
	}
	
	//Checks to see if an upgrade is needed, if so calls the install function
	function wpgft_update_check(){
		global $wpgft_db_version;
		global $wpgft_current_version;
		
		if (get_option('wpgft_db_version') < $wpgft_db_version) {
			wpgft_install();
		}
	}
	
	// Calls to create the admin menus
	function wpgft_create_menu() {
		//Create the new top level menu
		add_menu_page('WP Gift Cert', 'WP Gift Cert', 'edit_others_pages', 'wpgft-settings', 'wpgft_settings_page' );
		add_submenu_page('wpgft-settings', 'Settings', 'Settings', 'edit_others_pages', 'wpgft-settings', 'wpgft_settings_page' );
		//Create the First SubMenu
		add_submenu_page('wpgft-settings', 'Button Management', 'Create and Edit', 'edit_others_pages', 'certificate_management', 'wpgft_manage_certs' );
		add_submenu_page('wpgft-settings', 'Sold Certicates', 'Sold Certificates', 'edit_others_pages', 'manage_sold', 'wpgft_manage_soldCerts' );
	}
	
	//Setup the settings page on the admin menu
	function wpgft_settings_page() {
		$wpgft_options_arr = get_option('wpgft_options');
		?>
		
		<div class="wrap">
			<h2><?php _e('WP Gift Cert Options', 'wpgft-plugin') ?> <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="H7G7STCJR3HU6">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
</h2>
			

			<form method="post" action="options.php">
				<?php settings_fields( 'wpgft-settings-group' ); ?>
				<h3>PayPal Options</h3>
				<table class="form-table">
				
					<tr valign="top">
					<th scope="row"><?php _e('Paypal Email', 'wpgft-plugin'); ?></th>
					<td> <input type="text" name="wpgft_options[paypal_id]" value="<?php echo $wpgft_options_arr['paypal_id']; ?>" /></td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('Paypal URL', 'wpgft-plugin');	?></th>
					<td> <input type="text" name="wpgft_options[paypal_url]" value="<?php echo $wpgft_options_arr['paypal_url']; ?>"/></td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('Require Address', 'wpgft-plugin');	?></th>
					<td>
						<select name="wpgft_options[require_address]">
							<option value="Yes" <?php if($wpgft_options_arr['require_address'] == "Yes") echo "selected"; ?>>Yes</option>
							<option value="No" <?php if($wpgft_options_arr['require_address'] == "No") echo "selected"; ?>>No</option>
						</select></td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('Currency', 'wpgft-plugin');	?></th>
					<td>
						<select name="wpgft_options[currency]">
							<option value="AUD" <?php if($wpgft_options_arr['currency'] == "AUD") echo "selected"; ?>>AUD</option>
							<option value="CAD" <?php if($wpgft_options_arr['currency'] == "CAD") echo "selected"; ?>>CAD</option>
							<option value="EUR" <?php if($wpgft_options_arr['currency'] == "EUR") echo "selected"; ?>>EUR</option>
							<option value="GBP" <?php if($wpgft_options_arr['currency'] == "GBP") echo "selected"; ?>>GBP</option>
							<option value="NZD" <?php if($wpgft_options_arr['currency'] == "NZD") echo "selected"; ?>>NZD</option>
							<option value="USD" <?php if($wpgft_options_arr['currency'] == "USD" || $wpgft_options_arr['currency'] == "") echo "selected"; ?>>USD</option>
						</select></td>
					</tr>
					<th scope="row"><?php _e('Custom Return Page') ?></th>
					<td> 
						<input type="text" name="wpgft_options[custom_return]" style="width:300px;" value="<?php echo $wpgft_options_arr['custom_return']; ?>"/>
						<br />
						<span class="description">If blank will use the return page content below</span>
					</td>
					</tr>
					<tr>
					<th scope="row"><?php _e('Return Page Content', 'wpgft-plugin'); ?></th>
					<td> <textarea cols="50" rows="15" name="wpgft_options[return_page]" ><?php echo $wpgft_options_arr['return_page']; ?></textarea><br />
						<span class="description"> Text displayed when the user returns from PayPal</span>
					</td>
					</tr>
				</table>
				<h3>Certificate Display Options</h3>
				<table class="form-table">
									
					<tr valign="top">
					<th scope="row"><?php _e('Company Name', 'wpgft-plugin'); ?></th>
					<td> <input type="text" name="wpgft_options[company]" value="<?php echo $wpgft_options_arr['company']; ?>"/><br />
						<span class="description">Displayed on Gift Certificate</span></td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('Company Info', 'wpgft-plugin'); ?></th>
					<td> <textarea name="wpgft_options[company_info]" ><?php echo $wpgft_options_arr['company_info']; ?></textarea><br />
						<span class="description">Displays below Company Name on Gift Cert</span></td>
					</tr>
				</table>
				<h3>Certificate Email Options</h3>
				<table class="form-table">
					<tr valign="top">
					<th scope="row"><?php _e('Admin E-mail', 'wpgft-plugin'); ?></th>
					<td> <input type="text" name="wpgft_options[admin_email]" value="<?php echo $wpgft_options_arr['admin_email']; ?>"/><br />
						<span class="description">"From:" Email used to Issue Certs</span>
					</td>
					</tr>
					<tr valign="top">
					<th scope="row"><?php _e('E-mail Message', 'wpgft-plugin'); ?></th>
					<td> <textarea cols="50" rows="15" name="wpgft_options[email_message]" ><?php echo $wpgft_options_arr['email_message']; ?></textarea><br />
						<span class="description">Message Sent to user after payment is made (displays above the certificate)</span>
					</td>
					</tr>
				</table>
				<h3>Style Options</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Disable Included CSS', 'wpgft-plugin'); ?></th>
						<td>
						<select name="wpgft_options[disable_css]">
							<option value="yes" <?php if($wpgft_options_arr['disable_css'] == "yes") echo "selected"; ?>>Yes</option>
							<option value="no" <?php if($wpgft_options_arr['disable_css'] == "no") echo "selected"; ?>>No</option>
						</select></td>
					</tr>
				</table>
				<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wpgft-plugin'); ?>" />
			</form>
		</div>
		<?php
	}
	
	//Manage the creation of Certificate buttons
	function wpgft_manage_certs() {
		include('wpgft-actions.php');
		if(empty($_GET['insert'])) {
			if(empty($_GET['add'])) {
				include('editcerts.php');
			} else {
				include('addcerts.php');
			}
		}
	}
	
	//Manage the sold certificates
	function wpgft_manage_soldCerts() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wpgft_data";
		$cert_id = $_GET['cert_id'];
		include('wpgft-actions.php');
		
		//If you requested the e-mail be resent, then resend it.
		if($_GET['resend'] == 'true') {
			$payment_data = $wpdb->get_row($wpdb->prepare("SELECT * from $table_name WHERE ID = $cert_id"), ARRAY_A);
			send_cert_email($payment_data);
		} 
		include('showsoldcerts.php');
		
	}
	
	//Define Edit Boxes
	function wpgft_certBtn_amount_meta_box() {
		global $current_button;
		$button_id == -1;
		$wpgft_buttons_arr = get_option('$wpgft_buttons');
		
		if(isset($_GET['id'])) {
			$button_id = $_GET['id']; ?>
			<input type="hidden" name="wpgft_id" value="<?php echo $button_id?>" />
		<?php
		}
		//if($_GET['id'] == 0) $button_id = 0;
		//if($button_id >= 0) $current_button = $wpgft_buttons_arr[$button_id];
		?>
		<input type="text" name="wpgft_certBtn_amount" value="<?php echo $current_button['amount'];?>" class="regular-text" />
		
		<?php
	}
	function wpgft_certBtn_description_meta_box() {
		global $current_button;
		?>
		<input type="text" name="wpgft_certBtn_description" value="<?php echo $current_button['description']?>" class="regular-text" />
		<?php
	}
	
	function wpgft_certBtn_company_meta_box() {
		global $current_button;
		?>
		<input type="text" name="wpgft_certBtn_company" value="<?php echo $current_button['company']?>" class="regular-text" />
		<?php
	}
	
	function wpgft_certBtn_coinfo_meta_box() {
		global $current_button;
		?>
		<textarea type="text" name="wpgft_certBtn_coinfo"><?php echo $current_button['coinfo']?></textarea>
		<?php
	}
	
	function wpgft_certBtn_button_meta_box() {
		global $current_button;
		$button_option_arr = array(
			"Blue Button"	=> "bluebtn1",
			"Orange Button"	=> "orangebtn1"
		);
		?>
		<select name="wpgft_certBtn_button">
		<?php
			foreach($button_option_arr as $key => $button_option) {
				$selected = '';
				if($button_option == $current_button['button']) $selected = ' selected="selected" ';
				echo '<option'.$selected.' value="'.$button_option.'">'.$key.'</option>';
			}
			?>
		</select>
		<?php
	}
	
	
	//Setup the function to show the buttons based on Short Codes.
	function showButton($atts, $content = null) {
		global $wpgft_content_url;
		extract(shortcode_atts(array(
		"id" => FALSE,
		"button_only" => FALSE,), $atts));
		$current_button = get_current_button($id);
		$button_background = $wpgft_content_url ."/plugins/wp-gift-cert/images/".$current_button['button'];
		
		//Make Sure The ID Fed Us actually relates to a record
		if($current_button) {
			if ($current_button['amount'] != 0 && $current_button['amount'] != "") {
				$box_disabled = 'readonly="true"'; 
				$box_class='class="disabled-box"'; 
			}
			
			$formCode = '<form method="post" action="#" id="wpgft-btn-form-' . $id . '" class="wpgft-btn-form">';
			$formCode .= '<input type="hidden" value="true" name="wpgft_order" />';
			$formCode .= '<input type="hidden" value="'. $id .'" name="button_id" />';
			if ($button_only == TRUE) {
				$formCode .= '<input type="hidden" value="'.$current_button['amount'].'" name="amount" />';
			} else {
				$formCode .= '<dl id="wpgft-btn-list-'.$id.'" class="wpgft-table" >';
				$formCode .= '<dt>Amount</dt>';
				$formCode .= '<dd><input type="text"'. $box_disabled . ' ' . $box_class .'name="amount" value="'.$current_button['amount'] .'" /></dd>';
				
				if($current_button['company'] != "") {
					$formCode .= '<dt>Company</dt>';
					$formCode .= '<dd>' . $current_button['company'] . '</dd>';
				}
				
				$formCode .= '<dt>Description</dt>';
				$formCode .= '<dd>' . $current_button['description'] . '</dd>';
				$formCode .= '</dl>';
			}
			$formCode .= '<input class="'. $current_button['button'] .'" type="submit" value="Purchase Cert" name="submit">';
			$nonceID = 'wpgft_nonce_check'.$id;
			
			if( function_exists('wp_nonce_field') ) $formCode .= wp_nonce_field($nonceID);
			$formCode .= '</form>';
			return $formCode;
		}
	}
	add_shortcode('wpgft', 'showButton');

	//Intercept to check for submitted data via one of the forms or from PayPal
	function wpgft_eval_PostData() {
		if(isset($_POST['wpgft_order']) || isset($_POST['wpgft_purch'])) {
			$nonceID = 'wpgft_nonce_check'.$_POST['button_id'];
			check_admin_referer($nonceID);
			include("wpgft_processOrder.php");
		}
		if(isset($_GET['ipn_request']) == 'true') {
			include("wpgft_ipnHandler.php");
		}
		if(isset($_GET['verifyGft'])) {
			include("wpgft_verify.php");
		}
		if(isset($_GET['paypal_return'])) {
			include("wpgft_return.php");
		}
	}
	add_action('init', wpgft_eval_PostData);

function wpgft_register_settings() {
	//register our Array of settings
	register_setting( 'wpgft-settings-group', 'wpgft_options' );
}

/** 
*  Function:   get_current_button 
*
*  Description: 
*  Takes an integer ID and returns the associated button array from the DB
*
*  @int
*
*  @return array
*
*/ 
function get_current_button($id) {
		$wpgft_buttons_arr = get_option('wpgft_buttons');
		
		$current_button = $wpgft_buttons_arr[$id];
		return $current_button;
}

function send_cert_email($payment_data) {
		$wpgft_options = get_option("wpgft_options");
		$current_button = get_current_button($payment_data['button_id']);
		if($current_button['company'] != "") {
			$company = $current_button['company'];
			$companyinfo = nl2br($current_button['coinfo']);
		} else {
			$company = $wpgft_options['company'];
			$companyinfo = nl2br($wpgft_options['company_info']);
		}
		$urltoEncode = get_option('siteurl') . "/?verifyGft=true&" . "cert=".$payment_data['cert_num']."&amount=".$payment_data['cert_amount']."&data=".$payment_data['secret'];
		$url = urlencode($urltoEncode);
		$imgCode = '<img src="http://chart.apis.google.com/chart?chs=150x150&cht=qr&chld=|1&chl='.$url.'" alt="QR Code" />';
		$wordAmount = convert_number($payment_data['cert_amount']) . ' and <sup>' . substr($payment_data['cert_amount'], -2) . '/100</sup> ---';
		$currencySymbol = get_currSymbol($payment_data['currency']);
		
		// email stuff (change data below)
		$to = $payment_data['sold_to_email']; 
		$from = $wpgft_options['admin_email']; 
		$subject = "Your Certificate Order"; 
		
		//include the user supplied message
		$message = $wpgft_options['email_message'];
		//Build the Certificate out
		$message .= '<br /><br />
		<table style="border: solid 1px #000;width: 600px;">
			<tr>
				<td style="padding-top:10px; font-weight: bold; padding-right: 10px; text-align: right; padding-bottom: 25px; width:110px;">CERT Num:</td>
				<td style="padding-top:10px; padding-bottom: 25px; width:310px;">'.$payment_data['cert_num'].'</td>
				<td style="padding-top:10px; padding-bottom: 25px;">Issued: '.date("m/d/y").'</td>
			</tr>
			<tr>
				<td style="font-weight: bold;text-align: right; padding-right: 10px; width:110px;">To:</td>
				<td style="border-bottom: solid 3px #000;">'.$payment_data['recipient'].'</td>
				<td style="border: solid 1px #000;">'.$currencySymbol.$payment_data['cert_amount'].'</td>
			</tr>
			<tr>
				<td colspan="3" style="padding-left: 15px; padding-top: 25px; border-bottom: solid 3px #000; font-style: italic;">'.$wordAmount.'</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;" ><span style="font-size:32px;">'.$company.'</span><br/>'.$companyinfo.'</td>
				<td>'.$imgCode.'</td>
			</tr>
			</table>
		';

		// a random hash will be necessary to send mixed content
		$separator = md5(time());

		// carriage return type (we use a PHP end of line constant)
		$eol = PHP_EOL;

		// main header (multipart mandatory)
		$headers  = "From: ". $company . " <" . $from . ">". $eol;
		$headers .= "MIME-Version: 1.0".$eol; 
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol.$eol; 
		$headers .= "Content-Transfer-Encoding: 7bit".$eol;
		$headers .= "This is a MIME encoded message.".$eol.$eol;

		// message
		$headers .= "--".$separator.$eol;
		$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		//$headers .= $message.$eol.$eol;

		// send message
		$sendit = wp_mail($to, $subject, $message, $headers);
		
		//Code to troubleshoot email problems.
		/*	if(!$sendit) {
					$tempOptions = get_option('wpgft_options');
					$tempOptions['admin_email'] = "EMAIL FAILED";
					update_option('wpgft_options', $tempOptions);
				
				}
		*/

}

function wpgft_cert_edit_status(){
	global $wpdb;
	$table_name = $wpdb->prefix . "wpgft_data";
	
	$cert_id = $_POST['cert_id'];
	$cert_status = $_POST['cert_status'];
	
	$wpdb->update($table_name, array('Status'  => $cert_status), array('ID' => $cert_id),array('%s'),array('%d'));
	
}
if($_REQUEST['wpgft_admin_action'] == 'cert_edit_status') {
	add_action('admin_init', 'wpgft_cert_edit_status');
}

function wpgft_scripts() {
	global $wpgft_content_url;
	
	wp_enqueue_script('wpgft-admin', $wpgft_content_url.'/plugins/wp-gift-cert/js/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.0');
}
	add_action('admin_init', 'wpgft_scripts');
	
//Look through the posts and see if there are any shortcodes, then include our CSS.
	add_filter('the_posts', 'wpgft_add_scripts_and_styles'); // the_posts gets triggered before wp_head

function wpgft_add_scripts_and_styles($posts){
	
	if (empty($posts)) return $posts;
	
	$wpgft_options = get_option("wpgft_options");
	//Check to see if the user has disabled CSS, if they have then don't bother with the css scripts
	if ($wpgft_options['disable_css'] == "yes") return $posts;
	
	$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
	foreach ($posts as $post) {
		if (stripos($post->post_content, '[wpgft')  !== false) {
			$shortcode_found = true; // bingo!
			break;
		}
	}
 
	if ($shortcode_found) {
		global $wpgft_content_url;
		// enqueue here
		$fileLoc = $wpgft_content_url."/plugins/wp-gift-cert/css/wpgift.css";
		wp_register_style('wpgft-style', $fileLoc);
		wp_enqueue_style('wpgft-style');
		
	}
 
	return $posts;
}

//Function to return the appropriate Symbol for the currency being used

function get_currSymbol($currency) {
	switch ($currency) {
			case "AUD":
				$currSymbol = "$";
				break;
			
			case "CAD":
				$currSymbol = "$";
				break;
			
			case "GBP":
				$currSymbol = "&pound;";
				break;
				
			case "NZD":
				$currSymbol = "$";
				break;
			case "EUR":
				$currSymbol = "&euro;";
				break;
				
			default:
				$currSymbol = "$";
	}
	
	return $currSymbol;
}

/** 
*  Function:   convert_number 
*
*  Description: 
*  Converts a given integer (in range [0..1T-1], inclusive) into 
*  alphabetical format ("one", "two", etc.)
*
*  @int
*
*  @return string
*
*/ 
function convert_number($number) 
{ 
    if (($number < 0) || ($number > 999999999)) 
    { 
    throw new Exception("Number is out of range");
    } 

    $Gn = floor($number / 1000000);  /* Millions (giga) */ 
    $number -= $Gn * 1000000; 
    $kn = floor($number / 1000);     /* Thousands (kilo) */ 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       /* Tens (deca) */ 
    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Million"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
            $res .= " "; 
        } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
} 

?>