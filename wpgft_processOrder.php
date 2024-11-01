<?php
//This file is used to process the order and send the data to PayPal.

function wpgft_clearSlate($content) {
	//Define the US State Array to be used to generate the STATE select box
	$usStates = array(
				"AL" => "Alamaba",
				"AK" => "Alaska",
				"AZ" => "Arizona",
				"AR" => "Arkansas",
				"CA" => "California",
				"CO" => "Colorado",
				"CT" => "Conneticut",
				"DE" => "Delaware",
				"FL" => "Florida",
				"GA" => "Georgia",
				"HI" => "Hawaii",
				"ID" => "Idaho",
				"IL" => "Illinois",
				"IN" => "Indiana",
				"IA" => "Iowa",
				"KS" => "Kansas",
				"KY" => "Kentucky",
				"LA" => "Louisiana",
				"ME" => "Maine",
				"MD" => "Maryland",
				"MA" => "Massachusetts",
				"MI" => "Michigan",
				"MN" => "Minnesota",
				"MS" => "Mississippi",
				"MO" => "Missouri",
				"MT" => "Montana",
				"NE" => "Nebraska",
				"NV" => "Nevada",
				"NH" => "New Hampshire",
				"NJ" => "New Jersey",
				"NM" => "New Mexico",
				"NY" => "New York",
				"NC" => "North Carolina",
				"ND" => "North Dakota",
				"OH" => "Ohio",
				"OK" => "Oklahoma",
				"OR" => "Oregon",
				"PA" => "Pennsylvania",
				"RI" => "Rhode Island",
				"SC" => "South Carolina",
				"SD" => "South Dakota",
				"TN" => "Tennessee",
				"TX" => "Texas",
				"UT" => "Utah",
				"VT" => "Vermont",
				"VA" => "Virginia",
				"WA" => "Washington",
				"WV" => "West Virginia",
				"WI" => "Wisconsin",
				"WY" => "Wyoming");
	
	//Defined Supported Countries
	$countries = array(
				"AU" => "Australia",
				"CA" => "Canada",
				"FR" => "France",
				"DE" => "Germany",
				"IE" => "Ireland",
				"IT" => "Italy",
				"NL" => "Netherlands",
				"NZ" => "New Zealand",
				"ES" => "Spain",
				"GB" => "United Kingdom",
				"US" => "United States");
	$currency_country = array(
				"AUD" => "AU",
				"CAD" => "CA",
				"EUR" => "FR",
				"GBP" => "GB",
				"NZD" => "NZ",
				"USD" => "US");
				
	 //Stupid Magic Quotes
	$_POST = array_map( 'stripslashes_deep', $_POST ); 
	
	$posts = array();
	$post = new stdClass();
	
	//Get The Paypal Options
	$wpgft_options_arr = get_option('wpgft_options');
	$add_req = $wpgft_options_arr['require_address'];
	$currency = $wpgft_options_arr['currency'];
	$paypal_id = $wpgft_options_arr['paypal_id'];
	$paypal_url = $wpgft_options_arr['paypal_url'];
	$return_page = $wpgft_options_arr['custom_return'];
	 
	$currencySymbol = get_currSymbol($currency);
	
	//Check to see if a country was selected, if not base defualt on the currency
	if(isset($_POST['orderCountry'])) {
		$country = $_POST['orderCountry'];
	} else {
		$defaultCountry = $currency_country[$currency];
		$country = $defaultCountry;
	}
	
	//Set the Default Values
	$error_Fname = "";
	$error_Lname = "";
	$error_email = "";
	$error_address = "";
	$error_state = "";
	$error_zip = "";
	$error_country = "";
	$error_phone = "";
	$error_amount = "";
	$errorCombo = "";
	$ro = "";
	$form_act = "#";
	$error_amount = wpgft_DollarCheck($_POST['amount']);
	//Check to see if the form was submitted.
	if(isset($_POST['wpgft_purch'])) {
		
		//Start error checking.
		
		
		$phoneCombo = $_POST['orderPhoneArea'] . $_POST['orderPhoneExc'] . $_POST['orderPhoneNum'];
		$error_Fname = notBlank($_POST['orderFName'], "First Name");
		$error_Lname = notBlank($_POST['orderLName'], "Last Name");
		if(!is_email($_POST['orderEmail'])) {
			$error_email = "Email Address Invalid";
		}
		
		//Check the address fields only if the address is being required
		if($add_req != "No") {
			$error_address = notBlank($_POST['orderAdd'], "Address");
			$error_city = notBlank($_POST['orderCity'], "City");
			if($country == "US") $error_state = notBlank($_POST['orderState'], "State");
			$error_zip = notBlank($_POST['orderZip'], "Zip");
			$error_country = notBlank($country, "Country");
		}
		
		if($country == "US") {
			$error_phone = notBlank($phoneCombo, "Phone");
			if(!$error_phone) {
				$error_phone = phone_check($phoneCombo);
			}
		} else {
			$error_phone = notBlank($_POST['orderPhoneArea'], 'Country Code');
			if($error_phone) $error_phone .= "<br />";
			$error_phone .= notBlank($_POST['orderPhoneExc'], 'Phone Number');
		}
		
		$errorCombo = $error_Fname . $error_Lname . $error_email . $error_address . $error_city . $error_state . $error_zip . $error_phone . $error_amount;
		
		//Check to see if there were errors, if everything is good then disable input fields.
		if(!$errorCombo) {
			$ro = 'readonly="true"';
			$form_act = $paypal_url;
			}
		}
	
		//get the current button information.
		$current_button = get_current_button($_POST['button_id']);
		$amount = $current_button['amount'];
		$description = $current_button['description'] ;//$current_button['description'];
		
		//Check to see if it was a custom amount, if so ignore the stored button amount.
		if($current_button['amount'] == 0 || $current_button['amount'] =="")  $amount = $_POST['amount'];
		
			$post_content = '<form id="wpgft_orderProc" class="wpgftOrder" method="Post" action="'. $form_act. '" enctype="multipart/form">';
			$post_content .= '<fieldset class="wpgft_fieldset">';
			$post_content .= '<dl class="wpgft_orderList">';
			$post_content .= '<div class="wpgft_titleBox">Your Order</div>';
			$post_content .= '<dt>Amount:</dt>';
			
			if( $error_amount ) {
				$post_content .= '<dd>'. $currencySymbol . '<input type="text" name="amount" value="'. $amount .' " /> <input type="hidden" name="button_id" value="'. esc_attr($_POST['button_id']).'" /><br /><span class="wpgft_error">'. $error_amount.'></span></dd>';
			} else {
					$post_content .= '<dd>'.$currencySymbol.$amount.'<input type="hidden" name="amount" value="'.$amount.'" /><input type="hidden" name="button_id" value="'.esc_attr($_POST['button_id']).'" /></dd>';
			}
			
			$post_content .= '<dt>Description:</dt>';
			$post_content .= '<dd>'.$description.'</dd>';
			$post_content .= '</dl>';
			$post_content .= '<dl class="wpgft_mainList">';
			$post_content .= '<div class="wpgft_titleBox">Your Information</div>';
			$post_content .= '<dt><span class="wpgft_req">*</span>First Name:</dt>';
			$post_content .= '<dd><input type="text"'.$ro.' name="orderFName" id="orderFName" value="'.esc_attr($_POST['orderFName']).'"/>';
			
			if($error_Fname) $post_content .= '<br /><span class="wpgft_error">'.$error_Fname.'</span></dd>';
			
			$post_content .= '<dt><span class="wpgft_req">*</span>Last Name:</dt>';
			$post_content .= '<dd><input type="text"'.$ro.' name="orderLName" id="orderLName" value="'.esc_attr($_POST['orderLName']).'"/>';
			
			if($error_Lname) $post_content .= '<br /><span class="wpgft_error">'.$error_Lname.'</span></dd>';
					
			$post_content .= '<dt><span class="wpgft_req">*</span>eMail:</dt>';
			$post_content .= '<dd><input type="text"'.$ro.' name="orderEmail" id="orderEmail" value="'.esc_attr($_POST['orderEmail']).'" />';
			if($error_email) $post_content .= '<br /><span class="wpgft_error">'.$error_email.'</span></dd>';
			
			//only show address fields if they are being required
			if($add_req != "No") {
						$post_content .= '<dt><span class="wpgft_req">*</span>Address 1:</dt>';
						$post_content .= '<dd><input type="text"'.$ro.' name="orderAdd" id="orderAdd" value="'.esc_attr($_POST['orderAdd']).'" />';
						if($error_address) $post_content .= '<br /><span class="wpgft_error">'.$error_address.'</span></dd>';
						
						$post_content .= '<dt>Address 2:</dt>';
						$post_content .= '<dd><input type="text"'.$ro.' name="orderAdd2" id="orderAdd2" value="'.esc_attr($_POST['orderAdd2']).'"/> </dd>';
						$post_content .= '<dt><span class="wpgft_req">*</span>City:</dt>';
						$post_content .= '<dd><input type="text"'.$ro.' name="orderCity" id="orderCity" value="'.esc_attr($_POST['orderCity']).'" />';
						if($error_city) $post_content .= '<br /><span class="wpgft_error">'.$error_city.'</span></dd>';

						if($country == "US") { 
							$post_content .= '<dt><span class="wpgft_req">*</span>State:</dt>';
							$post_content .= '<dd>';
						
							$post_content .= '<select name="orderState"'.$ro.'>';
							
							//Loop through the state array to build the state select box
							foreach( $usStates as $key => $value ) {
								$selected = "";
								if($_POST['orderState'] == $key) $selected = "selected";
								$post_content .= '<option value="'.$key.'" ' . $selected . '>'. $value .'</option>';
							}
								
							$post_content .='</select>';
							if($error_state) $post_content .='<br /><span class="wpgft_error">'.$error_state.'</span></dd>';
						} else {
							$post_content .='<dt>State / Province / Region:</dt>';
							$post_content .='<dd><input type="text"'.$ro.' name="orderState" id="orderState" value="'.esc_attr($_POST['orderState']).'"/>';
			
							if($error_state) $post_content .='<br /><span class="wpgft_error">'.$error_state.'</span></dd>';
						}
						
						$post_content .='<dt><span class="wpgft_req">*</span>Postal Code:</dt>';
						$post_content .='<dd><input type="text"'.$ro.' name="orderZip" id="orderZip" value="'.esc_attr($_POST['orderZip']).'"/>';
						if($error_zip) $post_content .='<br /><span class="wpgft_error">'.$error_zip.'</span></dd>';
						
						$post_content .='<dt><span class="wpgft_req">*</span>Country:</dt>';
							$post_content .='<dd>';
						
								$post_content .='<select name="orderCountry"'.$ro.'>';
									foreach($countries as $key => $value) {
										$selected ="";
										if($country == $key) $selected = "selected";
										
										$post_content .='<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
									}									
								$post_content .='</select>';
							$post_content .='</dd>';
					}

					if($country == "US") {
						$test = "(";
						$post_content .='<dt><span class="wpgft_req">*</span>Phone:</dt>';
						$post_content .='<dd> <input type="text" '.$ro.' name="orderPhoneArea" id="orderPhoneArea" value="'.esc_attr($_POST['orderPhoneArea']).'" />  <input type="text" '.$ro.' name="orderPhoneExc" id="orderPhoneExc" size="3" maxlength="3" value="'.esc_attr($_POST['orderPhoneExc']).'" /> <input type="text" '.$ro.' name="orderPhoneNum" id="orderPhoneNum" size="5" maxlength="4" value="'.esc_attr($_POST['orderPhoneNum']).'" />';
						if($error_phone) $post_content .='<br /><span class="wpgft_error">'.$error_phone.'</span></dd>';
					} else {
						$post_content .='<dt><span class="wpgft_req">*</span>Phone:</dt>';
						$post_content .='<dd><input type="text" '.$ro.' name="orderPhoneArea" id="orderPhoneArea" size="3" maxlength="3" value="'.esc_attr($_POST['orderPhoneArea']).'" /> <input type="text" '.$ro.' name="orderPhoneExc" id="orderPhoneNum" value="'.esc_attr($_POST['orderPhoneExc']).'" />';
						if($error_phone) $post_content .='<br />-<span class="wpgft_error">'.$error_phone.'</span></dd>';
					} 
					
				$post_content .='</dl>';
				
				$post_content .='<dl class="wpgft_recipientList">';
					$post_content .='<div class="wpgft_titleBox">Recipient Information</div>';
					$post_content .='<dt>Name:</dt>';
					$post_content .='<dd><input type="text" '.$ro.' name="recpName" id="recpName" value="'.esc_attr($_POST['recpName']).'" /></dd>';
				$post_content .='</dl>';
			$post_content .='</fieldset>';
				
				//Build out the fields for PayPal, As long as we are Error Free
			if(!$errorCombo && isset($_POST['wpgft_purch']) ) {
				$notifyURL = get_option('siteurl')."/?ipn_request='true'";
				if($return_page == "") {
					$returnURL = get_option('siteurl')."/?paypal_return='true'";
				} else {
					$returnURL = $return_page;
				}
				
				//Work around to get the phone number, PayPal IPN was failing to pass the contact_phone field
				$customFields = $_POST['recpName']."~".$_POST['orderEmail']."~".$_POST['orderPhoneArea']."-".$_POST['orderPhoneExc'].$_POST['orderPhoneNum']."~".$_POST['button_id'];
			
				$post_content .='<input type="hidden" name="cmd" value="_xclick" />';
				$post_content .='<input type="hidden" name="notify_url" value="'.$notifyURL.'" />';
				$post_content .='<input type="hidden" name="return" value="'.$returnURL.'" />';
				$post_content .='<input type="hidden" name="currency_code" value="'.$currency.'" />';
				$post_content .='<input type="hidden" name="business" value="'.$paypal_id.'" />';
				$post_content .='<input type="hidden" name="amount" value="'.esc_attr($amount).'" />';
				$post_content .='<input type="hidden" name="item_name" value="'.esc_attr($description).'" />';
				
				if($add_req != "No") { 
					$post_content .='<input type="hidden" name="no_shipping" value="0" />';
					$post_content .='<input type="hidden" name="address1" value="'.esc_attr($_POST['orderAdd']).'" />';
					$post_content .='<input type="hidden" name="address2" value="'.esc_attr($_POST['orderAdd2']).'" />';
					$post_content .='<input type="hidden" name="city" value="'.esc_attr($_POST['orderCity']).'" />';
					$post_content .='<input type="hidden" name="state" value="'.esc_attr($_POST['orderState']).'" />';
					$post_content .='<input type="hidden" name="zip" value="'.esc_attr($_POST['orderZip']).'" />';
					$post_content .='<input type="hidden" name="country" value="'.esc_attr($_POST['orderCountry']).'" />';
				} else { 
					$post_content .='<input type="hidden" name="no_shipping" value="1" />';
				} 
				$post_content .='<input type="hidden" name="first_name" value="'.esc_attr($_POST['orderFName']).'" />';
				$post_content .='<input type="hidden" name="last_name" value="'.esc_attr($_POST['orderLName']).'" />';
				$post_content .='<input type="hidden" name="email" value="'.esc_attr($_POST['orderEmail']).'" />';
				$post_content .='<input type="hidden" name="night_phone_a" value="'.esc_attr($_POST['orderPhoneArea']).'" />';
				$post_content .='<input type="hidden" name="night_phone_b" value="'.esc_attr($_POST['orderPhoneExc']).'" />';
				$post_content .='<input type="hidden" name="night_phone_c" value="'.esc_attr($_POST['orderPhoneNum']).'" />';
				$post_content .='<input type="hidden" name="custom" value="'.esc_attr($customFields).'" />';
				$post_content .='<input type="submit" value="Purchase Cert" name="wpgft_purch" class="button-primary">';
				
			} else {
				$post_content .='<input type="submit" value="Review Information" name="wpgft_purch" class="button-primary">';
			}
				$nonceID = 'wpgft_nonce_check'.$_POST['button_id'];
				if( function_exists('wp_nonce_field') ) $post_content .= wp_nonce_field($nonceID);				
		$post_content .='</form>';
		
		//Setup our "post" and then return it
		$post->ID = 999777999777;
		$post->post_type = 'page';
		$post->post_title = 'Review Order';
		$post->post_content = $post_content;
		$post->comment_status = 'closed';
		$posts[] = $post;
		return $posts;
	
}
add_filter('the_posts', 'wpgft_clearSlate');

function orderCSS() {
	$wpgft_options = get_option("wpgft_options");
	//Check to see if the user has disabled CSS, if they have then don't bother with the css scripts
	if ($wpgft_options['disable_css'] != "yes") {
		global $wpgft_content_dir, $wpgft_content_url, $wpgft_plugin_dir;
		$fileLoc = $wpgft_content_url."/plugins/wp-gift-cert/css/wpgift.css";
		wp_register_style('wpgft-style', $fileLoc);
		wp_enqueue_style( 'wpgft-style');
	}
}
add_action('wp_print_styles', 'orderCSS');

//Validation Functions (Gotta Check those inputs cause Users Suck
function notBlank($data, $field) {
	if($data == "") {
		$return_txt = "$field Required";
		return $return_txt;
	} else {
		return "";
	}
}

function phone_check($phoneCombo) {
	if(!is_numeric($phoneCombo)) return "Phone Invalid";
	if(strlen($phoneCombo) != 10) return "Phone Invalid";
	return "";
}

function wpgft_DollarCheck($amount) {
	if ( preg_match('/^\$?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,} (\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/', $amount) && $amount != 0 && $amount != ""){
		return "";
	} else {
		return "Amount Specified is ZERO or Invalid";
	}
	
}

?>