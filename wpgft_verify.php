<?php

function verifyGiftCert(){
	global $wpdb;
	$cert_num = $amount = $secret = "";
	$posts = array();
	$post = new stdClass();
	
	//Get the Data
	if(isset($_GET['cert'])) $cert_num = $_GET['cert'];
	if(isset($_GET['amount'])) $amount = $_GET['amount'];
	if(isset($_GET['data'])) $secret = $_GET['data'];
	$dbName = $wpdb->prefix.'wpgft_data';
	if($cert_num && $amount && $secret){
			$db_certData = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpgft_data where cert_num = '".$cert_num."'"));
			if($db_certData && $secret == $db_certData->secret) {
				$post->ID = 999777999777;
				$post->post_type = 'page';
				$post->post_title = 'Certificate Verification';
				$post->comment_status = 'closed';
				
				if($db_certData->status == "Issued") {
					if($amount == $db_certData->cert_amount) {
						$post->post_content =  '<span style="color:green; font-size: 40px;">CERTIFICATE is VALID For: '.esc_attr($db_certData->cert_amount). '</span>';
					} else {
						$post->post_content =  '<span style="color:red; font-size: 40px;">Amount Invalid, Should be for: '.esc_attr($db_certData->cert_amount).'</span>';
					}
				} else {
					$post->post_content =  '<span style="color:red; font-size: 40px;">Certificate Status is listed as: ' . esc_attr($db_certData->status).'</span>';
				}
			} else {
				$post->post_content =  '<span style="color:red; font-size: 40px;">No Cert With that Number</span>';
			}
	} else {
		$post->post_content =  '<span style="color:red; font-size: 40px;">Invalid Request!</span>';
	}
	$posts[] = $post;
	return $posts;
	
}
add_filter('the_posts', 'verifyGiftCert');
?>