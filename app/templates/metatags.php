<?php
/**
 * @package Facebook Open Graph Meta Tags for WordPress
 * @subpackage Settings Page
 *
 * @since 0.1
 * @author Webdados
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// First we save!
if (isset($_POST['action'])) {
    // Nonce verification
    check_admin_referer('wonderm00n_open_graph_settings_save', 'wonderm00n_open_graph_nonce');

    if (trim(sanitize_text_field(wp_unslash($_POST['action']))) === 'save') {
        $usersettings = [
            'fb_app_id_show' => intval(webdados_fb_open_graph_post('fb_app_id_show')),
            'fb_app_id' => trim(webdados_fb_open_graph_post('fb_app_id')),
            'fb_admin_id_show' => intval(webdados_fb_open_graph_post('fb_admin_id_show')),
            'fb_admin_id' => trim(webdados_fb_open_graph_post('fb_admin_id')),
            'fb_locale_show' => intval(webdados_fb_open_graph_post('fb_locale_show')),
            'fb_locale' => trim(webdados_fb_open_graph_post('fb_locale')),
            'fb_sitename_show' => intval(webdados_fb_open_graph_post('fb_sitename_show')),
            'fb_title_show' => intval(webdados_fb_open_graph_post('fb_title_show')),
            'fb_title_show_schema' => intval(webdados_fb_open_graph_post('fb_title_show_schema')),
            'fb_title_show_twitter' => intval(webdados_fb_open_graph_post('fb_title_show_twitter')),
            'fb_url_show' => intval(webdados_fb_open_graph_post('fb_url_show')),
            'fb_url_show_twitter' => intval(webdados_fb_open_graph_post('fb_url_show_twitter')),
            'fb_url_canonical' => intval(webdados_fb_open_graph_post('fb_url_canonical')),
            'fb_url_add_trailing' => intval(webdados_fb_open_graph_post('fb_url_add_trailing')),
            'fb_type_show' => intval(webdados_fb_open_graph_post('fb_type_show')),
            'fb_type_homepage' => trim(webdados_fb_open_graph_post('fb_type_homepage')),
            'fb_article_dates_show' => intval(webdados_fb_open_graph_post('fb_article_dates_show')),
            'fb_article_sections_show' => intval(webdados_fb_open_graph_post('fb_article_sections_show')),
            'fb_publisher_show' => intval(webdados_fb_open_graph_post('fb_publisher_show')),
            'fb_publisher' => trim(webdados_fb_open_graph_post('fb_publisher')),
            'fb_publisher_show_schema' => intval(webdados_fb_open_graph_post('fb_publisher_show_schema')),
            'fb_publisher_schema' => trim(webdados_fb_open_graph_post('fb_publisher_schema')),
            'fb_publisher_show_twitter' => intval(webdados_fb_open_graph_post('fb_publisher_show_twitter')),
            'fb_publisher_twitteruser' => trim(webdados_fb_open_graph_post('fb_publisher_twitteruser')),
            'fb_author_show' => intval(webdados_fb_open_graph_post('fb_author_show')),
            'fb_author_show_meta' => intval(webdados_fb_open_graph_post('fb_author_show_meta')),
            'fb_author_show_linkrelgp' => intval(webdados_fb_open_graph_post('fb_author_show_linkrelgp')),
            'fb_author_show_twitter' => intval(webdados_fb_open_graph_post('fb_author_show_twitter')),
            'fb_author_hide_on_pages' => intval(webdados_fb_open_graph_post('fb_author_hide_on_pages')),
            'fb_desc_show' => intval(webdados_fb_open_graph_post('fb_desc_show')),
            'fb_desc_show_meta' => intval(webdados_fb_open_graph_post('fb_desc_show_meta')),
            'fb_desc_show_schema' => intval(webdados_fb_open_graph_post('fb_desc_show_schema')),
            'fb_desc_show_twitter' => intval(webdados_fb_open_graph_post('fb_desc_show_twitter')),
            'fb_desc_chars' => intval(webdados_fb_open_graph_post('fb_desc_chars')),
            'fb_desc_homepage' => trim(webdados_fb_open_graph_post('fb_desc_homepage')),
            'fb_desc_homepage_customtext' => trim(webdados_fb_open_graph_post('fb_desc_homepage_customtext')),
            'fb_image_show' => intval(webdados_fb_open_graph_post('fb_image_show')),
            'fb_image_size_show' => intval(webdados_fb_open_graph_post('fb_image_size_show')),
            'fb_image_show_schema' => intval(webdados_fb_open_graph_post('fb_image_show_schema')),
            'fb_image_show_twitter' => intval(webdados_fb_open_graph_post('fb_image_show_twitter')),
            'fb_image' => trim(webdados_fb_open_graph_post('fb_image')),
            'fb_image_rss' => intval(webdados_fb_open_graph_post('fb_image_rss')),
            'fb_image_use_specific' => intval(webdados_fb_open_graph_post('fb_image_use_specific')),
            'fb_image_use_featured' => intval(webdados_fb_open_graph_post('fb_image_use_featured')),
            'fb_image_use_content' => intval(webdados_fb_open_graph_post('fb_image_use_content')),
            'fb_image_use_media' => intval(webdados_fb_open_graph_post('fb_image_use_media')),
            'fb_image_use_default' => intval(webdados_fb_open_graph_post('fb_image_use_default')),
            'fb_show_wpseoyoast' => intval(webdados_fb_open_graph_post('fb_show_wpseoyoast')),
            'fb_show_subheading' => intval(webdados_fb_open_graph_post('fb_show_subheading')),
            'fb_show_businessdirectoryplugin' => intval(webdados_fb_open_graph_post('fb_show_businessdirectoryplugin')),
            'fb_adv_force_local' => intval(webdados_fb_open_graph_post('fb_adv_force_local')),
            'fb_adv_notify_fb' => intval(webdados_fb_open_graph_post('fb_adv_notify_fb')),
            'fb_adv_supress_fb_notice' => intval(webdados_fb_open_graph_post('fb_adv_supress_fb_notice')),
            'fb_twitter_card_type' => trim(webdados_fb_open_graph_post('fb_twitter_card_type')),
        ];
        // Update
        update_option('wonderm00n_open_graph_settings', $usersettings);
        // WPML - Register custom website description
        if (function_exists('icl_object_id') && function_exists('icl_register_string')) {
            icl_register_string('wd-fb-og', 'wd_fb_og_desc_homepage_customtext', trim(webdados_fb_open_graph_post('fb_desc_homepage_customtext')));
        }
    }
}

// Load the settings
extract(webdados_fb_open_graph_load_settings());

?>
	<div class="wrap">
		
	<h2><?php echo esc_html($webdados_fb_open_graph_plugin_name); ?> - <?php echo esc_html($webdados_fb_open_graph_plugin_name); ?> (<?php echo esc_html($webdados_fb_open_graph_plugin_version); ?>)</h2>
	<br class="clear"/>
	<p><?php esc_html_e('Please set some default values and which tags should, or should not, be included. It may be necessary to exclude some tags if other plugins are already including them.', 'interq-rss-pi'); ?></p>
	
	<?php
	settings_fields('wonderm00n_open_graph');
	?>
	
	<div class="postbox-container og_left_col">
		<div id="poststuff">
			<form name="form1" method="post">
				<?php wp_nonce_field('wonderm00n_open_graph_settings_save', 'wonderm00n_open_graph_nonce'); ?>
				
				<div id="webdados_fb_open_graph-settings" class="postbox">
					<h3 id="settings"><?php esc_html_e('Settings','interq-rss-pi'); ?></h3>
					<div class="inside">
						<table width="100%" class="form-table">
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Facebook Platform App ID (fb:app_id) tag', 'interq-rss-pi'); ?></th>
								<td>
									<input type="checkbox" name="fb_app_id_show" id="fb_app_id_show" value="1" <?php echo (intval($fb_app_id_show)==1 ? ' checked="checked"' : ''); ?> onclick="showAppidOptions();"/>
								</td>
							</tr>
							<tr class="fb_app_id_options">
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Facebook Platform App ID', 'interq-rss-pi'); ?>:</th>
								<td>
									<input type="text" name="fb_app_id" id="fb_app_id" size="30" value="<?php echo (esc_attr(trim($fb_app_id))); ?>"/>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Facebook Admin(s) ID (fb:admins) tag', 'interq-rss-pi'); ?></th>
								<td>
									<input type="checkbox" name="fb_admin_id_show" id="fb_admin_id_show" value="1" <?php echo (intval($fb_admin_id_show)==1 ? ' checked="checked"' : ''); ?> onclick="showAdminOptions();"/>
								</td>
							</tr>
							<tr class="fb_admin_id_options">
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Facebook Admin(s) ID', 'interq-rss-pi'); ?>:</th>
								<td>
									<input type="text" name="fb_admin_id" id="fb_admin_id" size="30" value="<?php echo (esc_attr(trim($fb_admin_id))); ?>"/>
									<br/>
									<?php esc_html_e('Comma separated if more than one', 'interq-rss-pi'); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include locale (fb:locale) tag', 'interq-rss-pi'); ?></th>
								<td>
									<input type="checkbox" name="fb_locale_show" id="fb_locale_show" value="1" <?php echo (intval($fb_locale_show)==1 ? ' checked="checked"' : ''); ?> onclick="showLocaleOptions();"/>
								</td>
							</tr>
							<tr class="fb_locale_options">
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Locale', 'interq-rss-pi'); ?>:</th>
								<td>
									<select name="fb_locale" id="fb_locale">
										<option value=""<?php if (trim($fb_locale)=='') echo ' selected="selected"'; ?>><?php esc_html_e('WordPress current locale/language', 'interq-rss-pi'); ?> (<?php echo esc_html(get_locale()); ?>)&nbsp;</option>
										<?php
											$listLocales=false;
											$loadedOnline=false;
											$loadedOffline=false;
											//Online
											if (!empty($_GET['localeOnline'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
												if (intval($_GET['localeOnline'])==1) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
													$response = wp_remote_get('http://www.facebook.com/translations/FacebookLocales.xml');
													if (!is_wp_error($response)) {
														$http_code = wp_remote_retrieve_response_code($response);
														if (intval($http_code) === 200) {
															$fb_locales = wp_remote_retrieve_body($response);
															// Save the file locally using WP_Filesystem
															global $wp_filesystem;
															if (empty($wp_filesystem)) {
																require_once ABSPATH . '/wp-admin/includes/file.php';
																WP_Filesystem();
															}
															$locale_file = RSS_PL_PATH . 'wonderm00ns-simple-facebook-open-graph-tags/includes/FacebookLocales.xml';
															$wp_filesystem->put_contents($locale_file, $fb_locales, FS_CHMOD_FILE);
															$listLocales = true;
															$loadedOnline = true;
														}
													}
												}
											}
											//Offline
											if (!$listLocales) {
												$locale_file = RSS_PL_PATH . 'wonderm00ns-simple-facebook-open-graph-tags/includes/FacebookLocales.xml';
												global $wp_filesystem;
												if (empty($wp_filesystem)) {
													require_once ABSPATH . '/wp-admin/includes/file.php';
													WP_Filesystem();
												}
												if ($wp_filesystem->exists($locale_file)) {
													$fb_locales = $wp_filesystem->get_contents($locale_file);
													if ($fb_locales) {
														$listLocales = true;
														$loadedOffline = true;
													}
												}
											}
											//OK
											if ($listLocales) {
												$xml=simplexml_load_string($fb_locales);
												$json = json_encode($xml);
												$locales = json_decode($json,TRUE);
												if (is_array($locales['locale'])) {
													foreach ($locales['locale'] as $locale) {
														?><option value="<?php echo esc_attr($locale['codes']['code']['standard']['representation']); ?>"<?php if (trim($fb_locale)==trim($locale['codes']['code']['standard']['representation'])) echo ' selected="selected"'; ?>><?php echo esc_html($locale['englishName']); ?> (<?php echo esc_html($locale['codes']['code']['standard']['representation']); ?>)</option><?php
													}
												}
											}
										?>
									</select>
									<br/>
									<?php
									if ($loadedOnline) {
										esc_html_e('List loaded from Facebook (online)', 'interq-rss-pi');
									} else {
										if ($loadedOffline) {
											esc_html_e('List loaded from local cache (offline)', 'interq-rss-pi'); ?> - <a href="?page=wonderm00n-open-graph.php&amp;localeOnline=1" onClick="return(confirm('<?php esc_html_e('You\\\'l lose any changes you haven\\\'t saved. Are you sure?', 'interq-rss-pi'); ?>'));"><?php esc_html_e('Reload from Facebook', 'interq-rss-pi'); ?></a><?php
										} else {
											esc_html_e('List not loaded', 'interq-rss-pi');
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Site Name (og:site_name) tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_sitename_show" id="fb_sitename_show" value="1" <?php echo (intval($fb_sitename_show)==1 ? ' checked="checked"' : ''); ?>/>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Post/Page title (og:title) tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_title_show" id="fb_title_show" value="1" <?php echo (intval($fb_title_show)==1 ? ' checked="checked"' : ''); ?> onclick="showTitleOptions();"/>
								</td>
							</tr>
							<tr class="fb_title_options">
								<th scope="row"><i class="dashicons-before dashicons-googleplus"></i><?php esc_html_e('Include Schema.org "itemprop" Name tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_title_show_schema" id="fb_title_show_schema" value="1" <?php echo (intval($fb_title_show_schema)==1 ? ' checked="checked"' : ''); ?>/>
									<br/>
									<i>&lt;meta itemprop="name" content="..."/&gt;</i>
									<br/>
									<?php esc_html_e('Recommended for Google+ sharing purposes if no other plugin is setting it already', 'interq-rss-pi');?>
								</td>
							</tr>
							<tr class="fb_title_options">
								<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Include Twitter Card Title tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_title_show_twitter" id="fb_title_show_twitter" value="1" <?php echo (intval($fb_title_show_twitter)==1 ? ' checked="checked"' : ''); ?>/>
									<br/>
									<i>&lt;meta name="twitter:title" content=..."/&gt;</i>
									<br/>
									<?php esc_html_e('Recommended for Twitter sharing purposes if no other plugin is setting it already', 'interq-rss-pi');?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include URL (og:url) tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_url_show" id="fb_url_show" value="1" <?php echo (intval($fb_url_show)==1 ? ' checked="checked"' : ''); ?> onclick="showUrlOptions();"/>
								</td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Include Twitter Card URL tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_url_show_twitter" id="fb_url_show_twitter" value="1" <?php echo (intval($fb_url_show_twitter)==1 ? ' checked="checked"' : ''); ?>/>
								</td>
							</tr>
							<tr class="fb_url_options">
								<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Add trailing slash at the end', 'interq-rss-pi');?>:</th>
								<td>
									<input type="checkbox" name="fb_url_add_trailing" id="fb_url_add_trailing" value="1" <?php echo (intval($fb_url_add_trailing)==1 ? ' checked="checked"' : ''); ?> onclick="showUrlTrail();"/>
									<br/>
									<?php esc_html_e('On the homepage will be', 'interq-rss-pi');?>: <i><?php echo esc_html(get_option('siteurl')); ?><span id="fb_url_add_trailing_example">/</span></i>
								</td>
							</tr>
							<tr class="fb_url_options">
								<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Set Canonical URL', 'interq-rss-pi');?>:</th>
								<td>
									<input type="checkbox" name="fb_url_canonical" id="fb_url_canonical" value="1" <?php echo (intval($fb_url_canonical)==1 ? ' checked="checked"' : ''); ?>/>
									<br/>
									<i>&lt;link rel="canonical" href="..."/&gt;</i>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Type (og:type) tag', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_type_show" id="fb_type_show" value="1" <?php echo (intval($fb_type_show)==1 ? ' checked="checked"' : ''); ?> onclick="showTypeOptions();"/>
									<br/>
									<?php printf(
										/* translators: 1: The schema type for posts (article), 2: Schema type for homepage (website), 3: Alternative schema type (blog). */
										esc_html__('Will be "%1$s" for posts and pages and "%2$s" or "%3$s"; for the homepage', 'interq-rss-pi'), 'article', 'website', 'blog');
									?>
								</td>
							</tr>
							<tr class="fb_type_options">
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Homepage type', 'interq-rss-pi');?>:</th>
								<td>
									<?php esc_html_e('Use', 'interq-rss-pi');?>
									<select name="fb_type_homepage" id="fb_type_homepage">
										<option value="website"<?php if (trim($fb_type_homepage)=='' || trim($fb_type_homepage)=='website') echo ' selected="selected"'; ?>>website&nbsp;</option>
										<option value="blog"<?php if (trim($fb_type_homepage)=='blog') echo ' selected="selected"'; ?>>blog&nbsp;</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include published and modified dates (article:published_time, article:modified_time and og:updated_time) tags', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_article_dates_show" id="fb_article_dates_show" value="1" <?php echo (intval($fb_article_dates_show)==1 ? ' checked="checked"' : ''); ?>/>
									<br/>
									<?php esc_html_e('Works for posts only', 'interq-rss-pi'); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include article section (article:section) tags', 'interq-rss-pi');?></th>
								<td>
									<input type="checkbox" name="fb_article_sections_show" id="fb_article_sections_show" value="1" <?php echo (intval($fb_article_sections_show)==1 ? ' checked="checked"' : ''); ?>/>
									<br/>
									<?php esc_html_e('Works for posts only', 'interq-rss-pi'); ?>, <?php esc_html_e('from the categories', 'interq-rss-pi'); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
									<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Publisher Page (article:publisher) tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_publisher_show" id="fb_publisher_show" value="1" <?php echo (intval($fb_publisher_show)==1 ? ' checked="checked"' : ''); ?> onclick="showPublisherOptions();"/>
										<br/>
										<?php esc_html_e('Links the website to the publisher Facebook Page.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_publisher_options">
									<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Website\'s Facebook Page', 'interq-rss-pi');?>:</th>
									<td>
										<input type="text" name="fb_publisher" id="fb_publisher" size="50" value="<?php echo esc_attr(trim($fb_publisher)); ?>"/>
										<br/>
										<?php esc_html_e('Full URL with http://', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr>
									<th scope="row"><i class="dashicons-before dashicons-googleplus"></i><?php esc_html_e('Include Google+ "publisher" tag', 'interq-rss-pi');?>:</th>
									<td>
										<input type="checkbox" name="fb_publisher_show_schema" id="fb_publisher_show_schema" value="1" <?php echo (intval($fb_publisher_show_schema)==1 ? ' checked="checked"' : ''); ?> onclick="showPublisherSchemaOptions();"/>
										<br/>
										<?php esc_html_e('Links the website to the publisher Google+ Page.', 'interq-rss-pi');?>
									</td>

								</tr>
								<tr class="fb_publisher_schema_options">
									<th scope="row"><i class="dashicons-before dashicons-googleplus"></i><?php esc_html_e('Website\'s Google+ Page', 'interq-rss-pi');?>:</th>
									<td>
										<input type="text" name="fb_publisher_schema" id="fb_publisher_schema" size="50" value="<?php echo esc_attr(trim($fb_publisher_schema)); ?>"/>
										<br/>
										<?php esc_html_e('Full URL with http://', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr>
									<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Include Twitter Card Website Username tag', 'interq-rss-pi');?>:</th>
									<td>
										<input type="checkbox" name="fb_publisher_show_twitter" id="fb_publisher_show_twitter" value="1" <?php echo (intval($fb_publisher_show_twitter)==1 ? ' checked="checked"' : ''); ?> onclick="showPublisherTwitterOptions();"/>
										<br/>
										<?php esc_html_e('Links the website to the publisher Twitter Username.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_publisher_twitter_options">
									<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Website\'s Twitter Username', 'interq-rss-pi');?>:</th>
									<td>
										<input type="text" name="fb_publisher_twitteruser" id="fb_publisher_twitteruser" size="20" value="<?php echo esc_attr(trim($fb_publisher_twitteruser)); ?>"/>
										<br/>
										<?php esc_html_e('Twitter username (without @)', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr>
									<td colspan="2"><hr/></td>
								</tr>

								<tr>
									<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Author Profile (article:author) tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_author_show" id="fb_author_show" value="1" <?php echo (intval($fb_author_show)==1 ? ' checked="checked"' : ''); ?> onclick="showAuthorOptions();"/>
										<br/>
										<?php esc_html_e('Links the article to the author Facebook Profile. The user\'s Facebook profile URL must be filled in.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_author_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Include Meta Author tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_author_show_meta" id="fb_author_show_meta" value="1" <?php echo (intval($fb_author_show_meta)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta name="author" content="..."/&gt;</i>
										<br/>
										<?php esc_html_e('Sets the article author name', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_author_options">
									<th scope="row"><i class="dashicons-before dashicons-googleplus"></i><?php esc_html_e('Include Google+ link rel "author" tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_author_show_linkrelgp" id="fb_author_show_linkrelgp" value="1" <?php echo (intval($fb_author_show_linkrelgp)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;link rel="author" href="..."/&gt;</i>
										<br/>
										<?php esc_html_e('Links the article to the author Google+ Profile (authorship). The user\'s Google+ profile URL must be filled in.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_author_options">
									<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Include Twitter Card Creator tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_author_show_twitter" id="fb_author_show_twitter" value="1" <?php echo (intval($fb_author_show_twitter)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta name="twitter:creator" content="@..."/&gt;</i>
										<br/>
										<?php esc_html_e('Links the article to the author Twitter profile. The user\'s Twitter user must be filled in.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_author_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Hide author on pages', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_author_hide_on_pages" id="fb_author_hide_on_pages" value="1" <?php echo (intval($fb_author_hide_on_pages)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<?php esc_html_e('Hides all author tags on pages.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr>
									<td colspan="2"><hr/></td>
								</tr>

								<tr>
									<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Description (og:description) tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_desc_show" id="fb_desc_show" value="1" <?php echo (intval($fb_desc_show)==1 ? ' checked="checked"' : ''); ?> onclick="showDescriptionOptions();"/>
									</td>
								</tr>
								<tr class="fb_description_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Include Meta Description tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_desc_show_meta" id="fb_desc_show_meta" value="1" <?php echo (intval($fb_desc_show_meta)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta name="description" content="..."/&gt;</i>
										<br/>
										<?php esc_html_e('Recommended for SEO purposes if no other plugin is setting it already', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_description_options">
									<th scope="row"><i class="dashicons-before dashicons-googleplus"></i><?php esc_html_e('Include Schema.org "itemprop" Description tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_desc_show_schema" id="fb_desc_show_schema" value="1" <?php echo (intval($fb_desc_show_schema)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta itemprop="description" content="..."/&gt;</i>
										<br/>
										<?php esc_html_e('Recommended for Google+ sharing purposes if no other plugin is setting it already', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_description_options">
									<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Include Twitter Card Description tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_desc_show_twitter" id="fb_desc_show_twitter" value="1" <?php echo (intval($fb_desc_show_twitter)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta name="twitter:description" content"..."/&gt;</i>
										<br/>
										<?php esc_html_e('Recommended for Twitter sharing purposes if no other plugin is setting it already', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_description_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Description maximum length', 'interq-rss-pi');?>:</th>
									<td>
										<input type="text" name="fb_desc_chars" id="fb_desc_chars" size="3" maxlength="3" value="<?php echo (intval($fb_desc_chars)>0 ? intval($fb_desc_chars) : ''); ?>"/> characters,
										<br/>
										<?php esc_html_e('0 or blank for no maximum length', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_description_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Homepage description', 'interq-rss-pi');?>:</th>
									<td>
										<?php
										$hide_home_description=false;
										if (get_option('show_on_front')=='page') {
											$hide_home_description=true;
											esc_html_e('The description of your front page:', 'interq-rss-pi');
											echo ' <a href="'. esc_url(get_edit_post_link(get_option('page_on_front'))).'" target="_blank">'. esc_html(get_the_title(get_option('page_on_front'))).'</a>';
										}; ?>
										<div<?php if ($hide_home_description) echo ' style="display: none;"'; ?>><?php esc_html_e('Use', 'interq-rss-pi');?>
											<select name="fb_desc_homepage" id="fb_desc_homepage" onchange="showDescriptionCustomText();">
												<option value=""<?php if (trim($fb_desc_homepage)=='') echo ' selected="selected"'; ?>><?php esc_html_e('Website tagline', 'interq-rss-pi');?>&nbsp;</option>
												<option value="custom"<?php if (trim($fb_desc_homepage)=='custom') echo ' selected="selected"'; ?>><?php esc_html_e('Custom text', 'interq-rss-pi');?>&nbsp;</option>
											</select>
											<div id="fb_desc_homepage_customtext_div">
												<textarea name="fb_desc_homepage_customtext" id="fb_desc_homepage_customtext" rows="3" cols="50"><?php echo esc_attr(trim($fb_desc_homepage_customtext)); ?></textarea>
												<?php
												if (function_exists('icl_object_id') && function_exists('icl_register_string')) {
													?>
													<br/>
													<?php
													printf(
														/* translators: 1: Link to WPML String translation page. */
														esc_html__('WPML users: Set the default language description here, save changes and then go to <a href="%s">WPML &gt; String translation</a> to set it for other languages.', 'interq-rss-pi'),
														'admin.php?page=wpml-string-translation/menu/string-translation.php&amp;context=wd-fb-og'
													); 
												}
												?>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><hr/></td>
								</tr>
								<tr>
									<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Image (og:image) tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_image_show" id="fb_image_show" value="1" <?php echo (intval($fb_image_show)==1 ? ' checked="checked"' : ''); ?> onclick="showImageOptions();"/>
										<br/>
										<?php esc_html_e('All images MUST have at least 200px on both dimensions in order to Facebook to load them at all.<br/>1200x630px for optimal results.<br/>Minimum of 600x315px is recommended.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_image_options">
									<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Include Image size (og:image:width and og:image:height) tags', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_image_size_show" id="fb_image_size_show" value="1" <?php echo (intval($fb_image_size_show)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<?php esc_html_e('Recommended only if Facebook is having problems loading the image when the post is shared for the first time.', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_image_options">
									<th scope="row"><i class="dashicons-before dashicons-googleplus"></i><?php esc_html_e('Include Schema.org "itemprop" Image tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_image_show_schema" id="fb_image_show_schema" value="1" <?php echo (intval($fb_image_show_schema)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta itemprop="image" content="..."/&gt;</i>
										<br/>
										<?php esc_html_e('Recommended for Google+ sharing purposes if no other plugin is setting it already', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_image_options">
									<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Include Twitter Card Image tag', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_image_show_twitter" id="fb_image_show_twitter" value="1" <?php echo (intval($fb_image_show_twitter)==1 ? ' checked="checked"' : ''); ?>/>
										<br/>
										<i>&lt;meta name="twitter:image:src" content="..."/&gt;</i>
										<br/>
										<?php esc_html_e('Recommended for Twitter sharing purposes if no other plugin is setting it already', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_image_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Default image', 'interq-rss-pi');?>:</th>
									<td>
										<input type="text" name="fb_image" id="fb_image" size="50" value="<?php echo esc_attr(trim($fb_image)); ?>"/>
										<input id="fb_image_button" class="button" type="button" value="Upload/Choose image" />
										<br/>
										<?php esc_html_e('Full URL with http://', 'interq-rss-pi');?>
										<br/>
										<?php esc_html_e('Recommended size: 1200x630px', 'interq-rss-pi'); ?>
									</td>
								</tr>
								<tr class="fb_image_options">
									<th scope="row"><i class="dashicons-before dashicons-rss"></i><?php esc_html_e('Add image to RSS/RSS2 feeds', 'interq-rss-pi');?></th>
									<td>
										<input type="checkbox" name="fb_image_rss" id="fb_image_rss" value="1" <?php echo (intval($fb_image_rss)==1 ? ' checked="checked"' : ''); ?> onclick="showImageOptions();"/>
										<br/>
										<?php esc_html_e('For auto-posting apps like RSS Graffiti, twitterfeed, ...', 'interq-rss-pi');?>
									</td>
								</tr>
								<tr class="fb_image_options">
									<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('On posts/pages', 'interq-rss-pi');?>:</th>
									<td>
										<div>
											1) <input type="checkbox" name="fb_image_use_specific" id="fb_image_use_specific" value="1" <?php echo (intval($fb_image_use_specific)==1 ? ' checked="checked"' : ''); ?>/>
											<?php esc_html_e('Image will be fetched from the specific "Open Graph Image" custom field on the post', 'interq-rss-pi');?>
										</div>
										<div>
											2) <input type="checkbox" name="fb_image_use_featured" id="fb_image_use_featured" value="1" <?php echo (intval($fb_image_use_featured)==1 ? ' checked="checked"' : ''); ?>/>
											<?php esc_html_e('If it\'s not set, image will be fetched from post/page featured/thumbnail picture', 'interq-rss-pi');?>
										</div>
										<div>
											3) <input type="checkbox" name="fb_image_use_content" id="fb_image_use_content" value="1" <?php echo (intval($fb_image_use_content)==1 ? ' checked="checked"' : ''); ?>/>
											<?php esc_html_e('If it doesn\'t exist, use the first image from the post/page content', 'interq-rss-pi');?>
										</div>
										<div>
											4) <input type="checkbox" name="fb_image_use_media" id="fb_image_use_media" value="1" <?php echo (intval($fb_image_use_media)==1 ? ' checked="checked"' : ''); ?>/>
											<?php esc_html_e('If it doesn\'t exist, use first image from the post/page media gallery', 'interq-rss-pi');?>
										</div>
										<div>
											5) <input type="checkbox" name="fb_image_use_default" id="fb_image_use_default" value="1" <?php echo (intval($fb_image_use_default)==1 ? ' checked="checked"' : ''); ?>/>
											<?php esc_html_e('If it doesn\'t exist, use the default image above', 'interq-rss-pi');?>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><hr/></td>
								</tr>
								<tr>
									<th scope="row"><i class="dashicons-before dashicons-twitter"></i><?php esc_html_e('Twitter Card Type', 'interq-rss-pi');?>:</th>
									<td>
										<select name="fb_twitter_card_type" id="fb_twitter_card_type">
											<option value="summary"<?php if (trim($fb_twitter_card_type)=='summary') echo ' selected="selected"'; ?>><?php esc_html_e('Summary Card', 'interq-rss-pi');?></option>
											<option value="summary_large_image"<?php if (trim($fb_twitter_card_type)=='summary_large_image') echo ' selected="selected"'; ?>><?php esc_html_e('Summary Card with Large Image', 'interq-rss-pi');?></option>
										</select>
									</td>
								</tr>
						</table>
					</div>
				</div>

				<div id="webdados_fb_open_graph-thirdparty" class="postbox">
					<h3 id="thirdparty"><?php esc_html_e('3rd Party Integration', 'interq-rss-pi');?></h3>
					<div class="inside">
						<?php
						$thirdparty=false;
						//WordPress SEO by Yoast
						if ( defined('WPSEO_VERSION') ) {
							$thirdparty=true;
							?>
							<hr/>
							<a name="wpseo"></a>
							<h4><a href="http://wordpress.org/plugins/wordpress-seo/" target="_blank">WordPress SEO by Yoast</a></h4>
							<p><?php esc_html_e('It\'s HIGHLY recommended to go to <a href="admin.php?page=wpseo_social" target="_blank">SEO &gt; Social</a> and disable "Add Open Graph meta data", "Add Twitter card meta data" and "Add Google+ specific post meta data"', 'interq-rss-pi'); ?> <?php esc_html_e('even if you don\'t enable integration bellow. You will get duplicate tags if you don\'t do this.', 'interq-rss-pi'); ?></p>
							<table width="100%" class="form-table">
									<tr>
										<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Use title, url (canonical) and description from WPSEO', 'interq-rss-pi');?></th>
										<td>
											<input type="checkbox" name="fb_show_wpseoyoast" id="fb_show_wpseoyoast" value="1" <?php echo (intval($fb_show_wpseoyoast)==1 ? ' checked="checked"' : ''); ?>/>
										</td>
									</tr>
								</table>
							<?php
						}
						//SubHeading
						if (webdados_fb_open_graph_subheadingactive()) {
							$thirdparty=true;
							?>
							<hr/>
							<h4><a href="http://wordpress.org/extend/plugins/subheading/" target="_blank">SubHeading</a></h4>
							<table width="100%" class="form-table">
									<tr>
										<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Add SubHeading to Post/Page title', 'interq-rss-pi');?></th>
										<td>
											<input type="checkbox" name="fb_show_subheading" id="fb_show_subheading" value="1" <?php echo (intval($fb_show_subheading)==1 ? ' checked="checked"' : ''); ?>/>
										</td>
									</tr>
								</table>
							<?php
						}
						//Business Directory Plugin 
						if(is_plugin_active('business-directory-plugin/wpbusdirman.php')) {
							$thirdparty=true;
							?>
							<hr/>
							<h4><a href="http://wordpress.org/extend/plugins/business-directory-plugin/" target="_blank">Business Directory Plugin</a></h4>
							<table width="100%" class="form-table">
									<tr>
										<th scope="row"><i class="dashicons-before dashicons-admin-site"></i><?php esc_html_e('Use BDP listing contents as OG tags', 'interq-rss-pi');?></th>
										<td>
											<input type="checkbox" name="fb_show_businessdirectoryplugin" id="fb_show_businessdirectoryplugin" value="1" <?php echo (intval($fb_show_businessdirectoryplugin)==1 ? ' checked="checked"' : ''); ?>/>
											<br/>
											<?php esc_html_e('Setting "Include URL", "Set Canonical URL", "Include Description" and "Include Image" options above is HIGHLY recommended', 'interq-rss-pi');?>
										</td>
									</tr>
								</table>
							<?php
						}
						if (!$thirdparty) {
							?>
							<p><?php esc_html_e('You don\'t have any compatible 3rd Party plugin installed/active.', 'interq-rss-pi');?></p>
							<p><?php esc_html_e('This plugin is currently compatible with:', 'interq-rss-pi');?></p>
							<ul>
								<li><a href="http://wordpress.org/extend/plugins/wordpress-seo/" target="_blank">WordPress SEO by Yoast</a></li>
								<li><a href="http://wordpress.org/extend/plugins/subheading/" target="_blank">SubHeading</a></li>
								<li><a href="http://wordpress.org/extend/plugins/business-directory-plugin/" target="_blank">Business Directory Plugin</a></li>
							</ul>
							<?php
						}
						?>
					</div>
				</div>

				<div id="webdados_fb_open_graph-advanced" class="postbox">
					<h3 id="advanced"><?php esc_html_e('Advanced settings', 'interq-rss-pi');?></h3>
					<div class="inside">
						<p><?php esc_html_e('Don\'t mess with this unless you know what you\'re doing', 'interq-rss-pi');?></p>
						<table width="100%" class="form-table">
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-admin-generic"></i><?php esc_html_e('Force getimagesize on local file even if allow_url_fopen=1', 'interq-rss-pi'); ?></th>
								<td>
									<input type="checkbox" name="fb_adv_force_local" id="fb_adv_force_local" value="1" <?php echo (intval($fb_adv_force_local)==1 ? ' checked="checked"' : ''); ?>/>
									<br/>
									<?php esc_html_e('May cause problems with some multisite configurations but fix "HTTP request failed" errors', 'interq-rss-pi');?>
								</td>
							</tr>
							<tr>
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Try to update Facebook Open Graph Tags cache when saving the post', 'interq-rss-pi'); ?></th>
								<td>
									<input type="checkbox" name="fb_adv_notify_fb" id="fb_adv_notify_fb" value="1" onclick="showFBNotifyOptions();"<?php echo (intval($fb_adv_notify_fb)==1 ? ' checked="checked"' : ''); ?>/>
								</td>
							</tr>
							<tr class="fb_adv_notify_fb_options">
								<th scope="row"><i class="dashicons-before dashicons-facebook-alt"></i><?php esc_html_e('Supress Facebook Open Graph Tags cache updated notice', 'interq-rss-pi'); ?></th>
								<td>
									<input type="checkbox" name="fb_adv_supress_fb_notice" id="fb_adv_supress_fb_notice" value="1" <?php echo (intval($fb_adv_supress_fb_notice)==1 ? ' checked="checked"' : ''); ?>/>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
				<p class="submit">
					<input type="hidden" name="action" value="save"/>
					<input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes','interq-rss-pi'); ?>" />
				</p>

			</form>
		</div>
	</div>
	
	<?php
		$links[0]['text']=__('Test your URLs at Facebook URL Linter / Debugger', 'interq-rss-pi');
		$links[0]['url']='https://developers.facebook.com/tools/debug';
		
		$links[5]['text']=__('Test (and request approval for) your URLs at Twitter Card validator', 'interq-rss-pi');
		$links[5]['url']='https://cards-dev.twitter.com/validator';

		$links[10]['text']=__('About the Open Graph Protocol (on Facebook)', 'interq-rss-pi');
		$links[10]['url']='https://developers.facebook.com/docs/opengraph/';

		$links[20]['text']=__('The Open Graph Protocol (official website)', 'interq-rss-pi');
		$links[20]['url']='http://ogp.me/';

		$links[25]['text']=__('About Twitter Cards', 'interq-rss-pi');
		$links[25]['url']='hhttps://dev.twitter.com/cards/getting-started';

		$links[30]['text']=__('Plugin official URL', 'interq-rss-pi');
		$links[30]['url']='http://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/facebook-open-graph-meta-tags-wordpress/?utm_source=fb_og_wp_plugin_settings&amp;utm_medium=link&amp;utm_campaign=fb_og_wp_plugin';

		$links[40]['text']=__('Author\'s website: Webdados', 'interq-rss-pi');
		$links[40]['url']='http://www.webdados.pt/?utm_source=fb_og_wp_plugin_settings&amp;utm_medium=link&amp;utm_campaign=fb_og_wp_plugin';

		$links[50]['text']=__('Author\'s Facebook page: Webdados', 'interq-rss-pi');
		$links[50]['url']='http://www.facebook.com/Webdados';

		$links[60]['text']=__('Author\'s Twitter account: @Wonderm00n<br/>(Webdados founder)', 'interq-rss-pi');
		$links[60]['url']='http://twitter.com/wonderm00n';
	?>
	<div class="postbox-container og_right_col">
		
		<div id="poststuff">
			<div id="webdados_fb_open_graph_links" class="postbox">
				<h3 id="settings"><?php esc_html_e('Rate this plugin', 'interq-rss-pi');?></h3>
				<div class="inside">
					<?php esc_html_e('If you like this plugin,', 'interq-rss-pi');?> <a href="http://wordpress.org/extend/plugins/wonderm00ns-simple-facebook-open-graph-tags/" target="_blank"><?php esc_html_e('please give it a high Rating', 'interq-rss-pi');?></a>.
				</div>
			</div>
		</div>
		
		<div id="poststuff">
			<div id="webdados_fb_open_graph_links" class="postbox">
				<h3 id="settings"><?php esc_html_e('Useful links', 'interq-rss-pi');?></h3>
				<div class="inside">
					<ul>
						<?php foreach($links as $link) { ?>
							<li>- <a href="<?php echo esc_attr($link['url']); ?>" target="_blank"><?php echo esc_html($link['text']); ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="clear">
		<p><br/>&copy 2011<?php if(gmdate('Y')>2011) echo esc_html('-'.gmdate('Y')); ?> <a href="http://www.webdados.pt/?utm_source=fb_og_wp_plugin_settings&amp;utm_medium=link&amp;utm_campaign=fb_og_wp_plugin" target="_blank">Webdados</a> &amp; <a href="http://wonderm00n.com/?utm_source=fb_og_wp_plugin_settings&amp;utm_medium=link&amp;utm_campaign=fb_og_wp_plugin" target="_blank">Marco Almeida (Wonderm00n)</a></p>
	</div>
		
	</div>
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#fb_image_button').click(function(){
				tb_show('',"media-upload.php?type=image&TB_iframe=true");
			});
			window.send_to_editor = function(html) {
				var imgurl = jQuery('<div>'+html+'</div>').find('img').attr('src');
				jQuery("input"+"#fb_image").val(imgurl);
				tb_remove();
			}
			showAppidOptions();
			showAdminOptions();
			showLocaleOptions();
			showTypeOptions();
			showPublisherOptions();
			showPublisherSchemaOptions();
			showPublisherTwitterOptions();
			showAuthorOptions();
			showUrlOptions();
			showUrlTrail();
			jQuery('.fb_description_options').hide();
			showDescriptionOptions();
			showTitleOptions();
			jQuery('#fb_desc_homepage_customtext').hide();
			showDescriptionCustomText();
			showImageOptions();
			showFBNotifyOptions();
		});
		function showAppidOptions() {
			if (jQuery('#fb_app_id_show').is(':checked')) {
				jQuery('.fb_app_id_options').show();
			} else {
				jQuery('.fb_app_id_options').hide();
			}
		}
		function showAdminOptions() {
			if (jQuery('#fb_admin_id_show').is(':checked')) {
				jQuery('.fb_admin_id_options').show();
			} else {
				jQuery('.fb_admin_id_options').hide();
			}
		}
		function showLocaleOptions() {
			if (jQuery('#fb_locale_show').is(':checked')) {
				jQuery('.fb_locale_options').show();
			} else {
				jQuery('.fb_locale_options').hide();
			}
		}
		function showUrlOptions() {
			/*if (jQuery('#fb_url_show').is(':checked')) {
				jQuery('.fb_url_options').show();
			} else {
				jQuery('.fb_url_options').hide();
			}*/
			jQuery('.fb_url_options').show();
		}
		function showUrlTrail() {
			if (jQuery('#fb_url_add_trailing').is(':checked')) {
				jQuery('#fb_url_add_trailing_example').show();
			} else {
				jQuery('#fb_url_add_trailing_example').hide();
			}
		}
		function showTypeOptions() {
			if (jQuery('#fb_type_show').is(':checked')) {
				jQuery('.fb_type_options').show();
			} else {
				jQuery('.fb_type_options').hide();
			}
		}
		function showAuthorOptions() {
			/*if (jQuery('#fb_author_show').is(':checked')) {
				jQuery('.fb_author_options').show();
			} else {
				jQuery('.fb_author_options').hide();
			}*/
			jQuery('.fb_author_options').show();
		}
		function showPublisherOptions() {
			if (jQuery('#fb_publisher_show').is(':checked')) {
				jQuery('.fb_publisher_options').show();
			} else {
				jQuery('.fb_publisher_options').hide();
			}
		}
		function showPublisherTwitterOptions() {
			if (jQuery('#fb_publisher_show_twitter').is(':checked')) {
				jQuery('.fb_publisher_twitter_options').show();
			} else {
				jQuery('.fb_publisher_twitter_options').hide();
			}
		}
		function showPublisherSchemaOptions() {
			if (jQuery('#fb_publisher_show_schema').is(':checked')) {
				jQuery('.fb_publisher_schema_options').show();
			} else {
				jQuery('.fb_publisher_schema_options').hide();
			}
		}
		function showDescriptionOptions() {
			/*if (jQuery('#fb_desc_show').is(':checked')) {
				jQuery('.fb_description_options').show();
			} else {
				jQuery('.fb_description_options').hide();
			}*/
			jQuery('.fb_description_options').show();
		}
		function showTitleOptions() {
			/*if (jQuery('#fb_title_show').is(':checked')) {
				jQuery('.fb_title_options').show();
			} else {
				jQuery('.fb_title_options').hide();
			}*/
			jQuery('.fb_title_options').show();  //Not exclusive
		}
		function showDescriptionCustomText() {
			if (jQuery('#fb_desc_homepage').val()=='custom') {
				jQuery('#fb_desc_homepage_customtext').show().focus();
			} else {
				jQuery('#fb_desc_homepage_customtext').hide();
			}
		}
		function showImageOptions() {
			/*if (jQuery('#fb_image_show').is(':checked')) {
				jQuery('.fb_image_options').show();
			} else {
				jQuery('.fb_image_options').hide();
			}*/
			jQuery('.fb_image_options').show();
		}
		function showFBNotifyOptions() {
			if (jQuery('#fb_adv_notify_fb').is(':checked')) {
				jQuery('.fb_adv_notify_fb_options').show();
			} else {
				jQuery('.fb_adv_notify_fb_options').hide();
			}
		}
	</script>
	<style type="text/css">
		.og_left_col {
			width: 69%;
		}
		.og_right_col {
			width: 29%;
			float: right;
		}
		.og_left_col #poststuff,
		.og_right_col #poststuff {
			min-width: 0;
		}
		table.form-table tr th,
		table.form-table tr td {
			line-height: 1.5;
		}
		table.form-table tr th {
			font-weight: bold;
		}
		table.form-table tr th[scope=row] {
			min-width: 300px;
		}
		table.form-table tr td hr {
			height: 1px;
			margin: 0px;
			background-color: #DFDFDF;
			border: none;
		}
		table.form-table .dashicons-before {
			margin-right: 10px;
			font-size: 12px;
			opacity: 0.5;
		}
		table.form-table .dashicons-facebook-alt {
			color: #3B5998;
		}
		table.form-table .dashicons-googleplus {
			color: #D34836;
		}
		table.form-table .dashicons-twitter {
			color: #55ACEE;
		}
		table.form-table .dashicons-rss {
			color: #FF6600;
		}
		table.form-table .dashicons-admin-site,
		table.form-table .dashicons-admin-generic {
			color: #666;
		}
	</style>