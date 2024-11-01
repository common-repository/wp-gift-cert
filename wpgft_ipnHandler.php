<?php

	global $wpdb;
	$wpgft_options = get_option('wpgft_options');
	$paypal_url = $wpgft_options['paypal_url'];
	$paypal_email = $wpgft_options['paypal_id'];
	
	$received_values = array();
	$received_values['cmd'] = '_notify-validate';
	$received_values += $_POST;
	
	$options = array(
		'timeout' 	=> 5,
		'body'		=> $received_values
		);
	
	$response = wp_remote_post($paypal_url, $options);
	
	
	if(strpos($response['body'], 'VERIFIED') !== false && $_POST['payment_status'] == "Completed") {
		//Assign IPN Post Values to Local Variables
		$ipn_business = $_POST['business'];
		$comboString = $_POST['txn_id'].$_POST['payment_date'];
		
		//Work around to get the phone number, PayPal IPN was failing to pass the contact_phone field
		$data = explode('~',$_POST['custom']);
		
		$ipn_data = array(
			'cert_num'		=> $_POST['txn_id'],
			'recipient'		=> $data[0],
			'cert_amount'	=> $_POST['mc_gross'],
			'sold_to'		=> $_POST['first_name'] . " " . $_POST['last_name'],
			'sold_to_email'	=> $data[1],
			'sold_to_phone' => $data[2],
			'sold_to_address'	=> $_POST['address_street'] . ", " . $_POST['address_city'] . ", " . $_POST['address_state']  . "  " . $_POST['address_zip'],
			'sale_date'		=> $_POST['payment_date'],
			'status'		=> "Issued",
			'secret'		=> sha1($comboString),
			'button_id'		=> $data[3]
		);
		$ipn_format = array('%s','%s','%s','%s','%s','%s', '%s', '%s', '%s', '%s', '%s');
		if($ipn_business == $paypal_email) {
			
			$wpdb->insert($wpdb->prefix.'wpgft_data', $ipn_data, $ipn_format);
			$ipn_data['currency'] = $_POST['mc_currency'];
			
			send_cert_email($ipn_data);
		}

	} else {
		exit("IPN Request Failure");
	}
	
?>