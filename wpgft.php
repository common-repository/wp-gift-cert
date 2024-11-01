<?php
/*
Plugin Name: WP Printable Gift Certs
Plugin URI: http://www.suburbanmedia.net/wordpress-plugins/wp-gift-cert/
Description: This plugin allows you to sell, using PayPal, printable gift certificates as well as manage sold gift certificates.
Version: 1.1.1
Author: Kyle Cox
Author URI: http://www.suburbanmedia.net
*/
?>
<?php
/* COPYRIGHT 2010 SUBURBAN MEDIA (email: info@suburbanmedia.net)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTIBILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
?>
<?php
	//Define the Global Location Variables
	global $wpdb;
	$wpdb->wpgft_data = $wpdb->prefix . 'wpgft_data';
	global $wpgft_content_dir, $wpgft_content_url, $wpgft_plugin_dir;
	$wpgft_content_dir = ( defined('WP_CONTENT_DIR') ) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
	$wpgft_content_url = ( defined('WP_CONTENT_URL') ) ? WP_CONTENT_URL : get_option('siteurl') . '/wp-content';
	$wpgft_plugin_dir = ( defined('WP_PLUGIN_DIR') ) ? WP_PLUGIN_DIR : $wpgft_content_dir . '/plugins';
	
	$loaded = require_once(dirname(__FILE__)."/wpgft-loader.php");
	require_once(dirname(__FILE__)."/export.php");

	?>