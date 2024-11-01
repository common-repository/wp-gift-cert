<?php
	if(isset($_POST['wpgft_add_cert']) && isset($_POST['wpgft_certBtn_amount']) && $_POST['wpgft_certBtn_description'] && $_POST['wpgft_certBtn_button']) {
		$wpgft_cert_arr = get_option('wpgft_buttons');

		$wpgft_Button_Amount = $_POST['wpgft_certBtn_amount'];
		$wpgft_Button_Description = $_POST['wpgft_certBtn_description'];
		$wpgft_Button_Button = $_POST['wpgft_certBtn_button'];
		$wpgft_Button_Company = $_POST['wpgft_certBtn_company'];
		$wpgft_Button_CoInfo = $_POST['wpgft_certBtn_coinfo'];
		
		if($_POST['wpgft_id'] == "") {
			$wpgft_cert_arr[] = array(
				"amount"		=> $wpgft_Button_Amount,
				"description"	=> $wpgft_Button_Description,
				"button"		=> $wpgft_Button_Button,
				"company"		=> $wpgft_Button_Company,
				"coinfo"		=> $wpgft_Button_CoInfo
			);
		} else {
			$wpgft_id = $_POST['wpgft_id'];
			$wpgft_cert_arr[$wpgft_id] = array(
				"amount"		=> $wpgft_Button_Amount,
				"description"	=> $wpgft_Button_Description,
				"button"		=> $wpgft_Button_Button,
				"company"		=> $wpgft_Button_Company,
				"coinfo"		=> $wpgft_Button_CoInfo
			);
		}
		
		update_option('wpgft_buttons', $wpgft_cert_arr);
	}
	
	if($_GET['delete'] == 'true' && isset($_GET['id']) && $_GET['page'] == 'certificate_management') {
		//echo "TESTING TESTING TESTING";
		$wpgft_delete_id = $_GET['id'];
		$wpgft_cert_arr = get_option('wpgft_buttons');
		
		unset($wpgft_cert_arr[$wpgft_delete_id]);
		update_option('wpgft_buttons', $wpgft_cert_arr);
		
	}
	
	if($_GET['delete'] == 'true' && isset($_GET['id']) && $_GET['page'] == 'manage_sold') {
		//echo "TESTING TESTING TESTING";
		$wpgft_delete_id = $_GET['id'];
		$sql = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."wpgft_data WHERE ID=".$wpgft_delete_id);
		$wpdb->query($sql);
	}
	
?>