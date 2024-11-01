<?php
function wpgft_clearPosts() {
	$wpgft_options_arr = get_option('wpgft_options');
	$posts = array();
	$post = new stdClass();
	
	$post->ID = 999777999777;
	$post->post_type = 'page';
	$post->post_title = 'Payment Success';
	$post->post_content = esc_attr($wpgft_options_arr['return_page']);
	$post->comment_status = 'closed';
	
	$posts[] = $post;
	return $posts;
}
add_filter('the_posts', 'wpgft_clearPosts');
?>