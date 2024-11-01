<?php
	// if uninstall/delete not called from Wordpress, then exit
	if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit ();
	
	// Delete Options from the Options Table
	delete_option( 'wpgft_options_arr' );
	delete_option( 'wpgft_db_version' );
	
	//remove additional options and custom tables
	global $wpdb;
	
	$table_name = $wpdb->prefix . "wpgft_data";
	
	//Build the Query to Drop the Table
	$sql = "DROP TABLE " . $table_name . ";";
	//execute the query
	$wp-db->query($sql);
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
?>