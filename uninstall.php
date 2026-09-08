<?php

//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

$interq_rss_pi_options = [
	'interq_rss_pi_feeds',
	'interq_rss_pi_deleted_posts',
	'interq_rss_pi_imported_posts',
	'interq_rss_pi_imported_posts_migrated',
	'interq_rss_pi_custom_cron_frequency',
];

// For Single site
if (!is_multisite()) {
	foreach ($interq_rss_pi_options as $option_name) {
		delete_option($option_name);
	}
	wp_clear_scheduled_hook('interq_rss_pi_cron');
} else {
	// For Multisite
	global $wpdb;
	$blog_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT blog_id FROM {$wpdb->blogs} WHERE archived = %d AND deleted = %d",
			0,
			0
		)
	);
	$original_blog_id = get_current_blog_id();
	foreach ($blog_ids as $blog_id) {
		switch_to_blog($blog_id);
		foreach ($interq_rss_pi_options as $option_name) {
			delete_option($option_name);
		}
		wp_clear_scheduled_hook('interq_rss_pi_cron');
	}
	switch_to_blog($original_blog_id);
}
