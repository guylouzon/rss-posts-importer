<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class InterQ_Rss_Pi_Admin_Processor {

/**
 * Creates the feeds array from the submitted data
 *
 * @param array $feeds
 * @return array
 */
    private function process_feeds(array $feeds): array {
        $nonce = isset( $_POST['interq_rss_pi_nonce_field'] ) ? sanitize_key( wp_unslash( $_POST['interq_rss_pi_nonce_field'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'interq_rss_pi_save_settings_action' ) ) {
            return $feeds;
        }

        $paused_feeds = [];
        if (isset($_POST['paused_feeds'])) {
            $paused_feeds_raw = sanitize_text_field(wp_unslash($_POST['paused_feeds']));
            $paused_feeds = array_filter( array_map( 'sanitize_text_field', explode( ',', $paused_feeds_raw )));
        }

        $deleted_feeds = [];
        if (isset($_POST['deleted_feeds'])) {
            $deleted_feeds_raw = sanitize_text_field(wp_unslash($_POST['deleted_feeds']));
            $deleted_feeds = array_filter(array_map('sanitize_text_field', explode( ',', $deleted_feeds_raw)));
        }

        $modified_feeds = [];
        if (isset($_POST['modified_feeds'])) {
            $modified_feeds_raw = sanitize_text_field(wp_unslash($_POST['modified_feeds']));
            $modified_feeds = array_filter( array_map( 'sanitize_text_field', explode( ',', $modified_feeds_raw) ) );
        }

        $new_feeds = [];
        if (isset($_POST['new_feeds'])) {
            $new_feeds_raw = sanitize_text_field(wp_unslash($_POST['new_feeds']));
            $new_feeds = array_filter( array_map( 'sanitize_text_field', explode( ',', $new_feeds_raw)));
        }

        foreach ($feeds as $key => $feed) {
            if (in_array($feed['id'], $paused_feeds)) {
                $feeds[$key]['feed_status'] = 'pause';
            } else {
                $feeds[$key]['feed_status'] = 'active';
            }

            if (in_array($feed['id'], $deleted_feeds)) {
                unset($feeds[$key]);
                continue;
            }

            if ( in_array( $feed['id'], $modified_feeds, true ) ) {
                $keywords = [];
                $keyword_str = isset( $_POST[ $feed['id'] . '-keywords' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-keywords' ] ) ) : '';
                if ( ! empty( $keyword_str ) ) {
                    $keywords = explode( ',', $keyword_str );
                }

                $feed['url'] = isset( $_POST[ $feed['id'] . '-url' ] ) ? esc_url_raw( wp_unslash( $_POST[ $feed['id'] . '-url' ] ) ) : '';
                $feed['name'] = isset( $_POST[ $feed['id'] . '-name' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-name' ] ) ) : '';
                $feed['max_posts'] = isset( $_POST[ $feed['id'] . '-max_posts' ] ) ? intval( wp_unslash( $_POST[ $feed['id'] . '-max_posts' ] ) ) : 0;
                $feed['author_id'] = isset( $_POST['author_id'] ) ? intval( wp_unslash( $_POST['author_id'] ) ) : intval( $feed['author_id'] ?? 1 );
                $feed['category_id'] = isset( $_POST[ $feed['id'] . '-category_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-category_id' ] ) ) : '';
                $feed['tags_id'] = isset( $_POST[ $feed['id'] . '-tags_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-tags_id' ] ) ) : '';
                $feed['keywords'] = array_map( 'trim', $keywords );
                $feed['strip_html'] = isset( $_POST[ $feed['id'] . '-strip_html' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-strip_html' ] ) ) : '';
                $feed['nofollow_outbound'] = isset( $_POST[ $feed['id'] . '-nofollow_outbound' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-nofollow_outbound' ] ) ) : '';
                $feed['automatic_import_categories'] = isset( $_POST[ $feed['id'] . '-automatic_import_categories' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-automatic_import_categories' ] ) ) : '';
                $feed['automatic_import_author'] = isset( $_POST[ $feed['id'] . '-automatic_import_author' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-automatic_import_author' ] ) ) : '';
                $feed['canonical_urls'] = isset( $_POST[ $feed['id'] . '-canonical_urls' ] ) ? esc_url_raw( wp_unslash( $_POST[ $feed['id'] . '-canonical_urls' ] ) ) : '';

                $feeds[$key] = $feed;
            }
        }

        foreach ($new_feeds as $id) {
            if (!$id)  continue;

            $keywords = [];
            $keyword_str = '';
            if (isset($_POST[$id . '-keywords'])) {
                $keyword_str = sanitize_text_field(wp_unslash($_POST[$id . '-keywords']));
            }
            if (!empty($keyword_str)) {
                $keywords = explode(',', $keyword_str);
            }

            $feed_status = in_array($id, $paused_feeds) ? 'pause' : 'active';

            $feeds[] = [
                'id' => $id,
                'url' => sanitize_text_field(wp_unslash($_POST[$id . '-url'] ?? '')),
                'name' => sanitize_text_field(wp_unslash($_POST[$id . '-name'] ?? '')),
                'max_posts' => intval(sanitize_text_field(wp_unslash($_POST[$id . '-max_posts'] ?? 0))),
                'author_id' => isset( $_POST['author_id'] ) ? intval( wp_unslash( $_POST['author_id'] ) ) : 1,
                'category_id' => sanitize_text_field(wp_unslash($_POST[$id . '-category_id'] ?? '')),
                'tags_id' => sanitize_text_field(wp_unslash($_POST[$id . '-tags_id'] ?? '')),
                'keywords' => array_map('trim', $keywords),
                'strip_html' => sanitize_text_field(wp_unslash($_POST[$id . '-strip_html'] ?? '')),
                'nofollow_outbound' => sanitize_text_field(wp_unslash($_POST[$id . '-nofollow_outbound'] ?? '')),
                'automatic_import_categories' => sanitize_text_field(wp_unslash($_POST[$id . '-automatic_import_categories'] ?? '')),
                'automatic_import_author' => sanitize_text_field(wp_unslash($_POST[$id . '-automatic_import_author'] ?? '')),
                'canonical_urls' => sanitize_text_field(wp_unslash($_POST[$id . '-canonical_urls'] ?? '')),
                'feed_status' => $feed_status
            ];
        }

        return $feeds;
    }

    public function process(): void {
        global $interq_rss_post_importer;

        // bail if there's nothing to process or the data is invalid
        $nonce = isset( $_POST['interq_rss_pi_nonce_field'] ) ? sanitize_key( wp_unslash( $_POST['interq_rss_pi_nonce_field'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'interq_rss_pi_save_settings_action' ) ) {
            return;
        }

        // Sanitize POST data
        //$_POST['info_update'] = sanitize_text_field($_POST['info_update']);
        $save_to_db = isset($_POST['save_to_db'] ) && 'true' === sanitize_text_field( wp_unslash($_POST['save_to_db']));
        $import_now  = (isset($_POST['import_now']) && 'true' === sanitize_text_field( wp_unslash( $_POST['import_now'])));

        // process settings

        $frequency_check = isset( $_POST['frequency'] ) ? sanitize_text_field( wp_unslash( $_POST['frequency'] ) ) : '';

        if ($frequency_check === "custom_frequency") {
            $rss_custom_frequency = isset( $_POST['rss_custom_frequency'] ) ? intval( wp_unslash( $_POST['rss_custom_frequency'] ) ) : 0;
            $frequency = "minutes_" . $rss_custom_frequency;
            $custom_frequency = 'true';
            // Adding option for custom cron
            $interq_rss_pi_custom_cron_frequency = serialize(
                [
                    'time' => $rss_custom_frequency,
                    'frequency' => $frequency
                ]
            );

            delete_option('interq_rss_pi_custom_cron_frequency');
            add_option('interq_rss_pi_custom_cron_frequency', $interq_rss_pi_custom_cron_frequency);
        } else {
            $frequency = isset( $_POST['frequency'] ) ? sanitize_text_field( wp_unslash( $_POST['frequency'] ) ) : '';
            $custom_frequency = 'false';

            // Delete custom cron if not exists
            delete_option('interq_rss_pi_custom_cron_frequency');
        }

        $settings = [
            'frequency'                => $frequency,
            'post_template'            => isset( $_POST['post_template'] ) ? wp_kses_post( wp_unslash( $_POST['post_template'] ) ) : '',
            'post_status'              => isset( $_POST['post_status'] ) ? sanitize_text_field( wp_unslash( $_POST['post_status'] ) ) : '',
            'author_id'                => isset( $_POST['author_id'] ) ? intval( wp_unslash( $_POST['author_id'] ) ) : 0,
            'allow_comments'           => isset( $_POST['allow_comments'] ) ? sanitize_text_field( wp_unslash( $_POST['allow_comments'] ) ) : '',
            'block_indexing'           => isset( $_POST['block_indexing'] ) ? sanitize_text_field( wp_unslash( $_POST['block_indexing'] ) ) : '',
            'nofollow_outbound'        => isset( $_POST['nofollow_outbound'] ) ? sanitize_text_field( wp_unslash( $_POST['nofollow_outbound'] ) ) : '',
            'enable_logging'           => isset( $_POST['enable_logging'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_logging'] ) ) : '',
            'tw_show'                  => isset( $_POST['tw_show'] ) ? sanitize_text_field( wp_unslash( $_POST['tw_show'] ) ) : '',
            'gg_show'                  => isset( $_POST['gg_show'] ) ? sanitize_text_field( wp_unslash( $_POST['gg_show'] ) ) : '',
            'og_show'                  => isset( $_POST['og_show'] ) ? sanitize_text_field( wp_unslash( $_POST['og_show'] ) ) : '',
            'import_images_locally'    => isset( $_POST['import_images_locally'] ) ? sanitize_text_field( wp_unslash( $_POST['import_images_locally'] ) ) : '',
            'disable_thumbnail'        => isset( $_POST['disable_thumbnail'] ) ? sanitize_text_field( wp_unslash( $_POST['disable_thumbnail'] ) ) : '',
            'keywords'                 => [],
            'cache_deleted'            => 'true',
            'custom_frequency'         => $custom_frequency,
        ];

        global $interq_rss_post_importer;

        // set up keyword filtering
        $keyword_str = '';
        if (isset($_POST['keyword_filter'])) {
            // Strip Slashes for RegEx
            $keyword_str = sanitize_text_field(wp_unslash($_POST['keyword_filter']));
        }

        $keywords = [];

        if (!empty($keyword_str)) {
            $keywords = explode(',', $keyword_str);
        }

        $settings['keywords'] = array_map('trim', $keywords);

        // set up "import deleted posts" cache
        $settings['cache_deleted'] = sanitize_text_field(wp_unslash($_POST['cache_deleted'] ?? 'true'));

        // update cron settings
        $this->update_cron($settings['frequency']);

        $feeds = $this->process_feeds($interq_rss_post_importer->options['feeds']);
        // process feeds start
        $paused_feeds = [];
        if (isset($_POST['paused_feeds'])) {
            $paused_feeds_raw = sanitize_text_field(wp_unslash($_POST['paused_feeds']));
            $paused_feeds = array_filter( array_map( 'sanitize_text_field', explode( ',', $paused_feeds_raw )));
        }

        $deleted_feeds = [];
        if (isset($_POST['deleted_feeds'])) {
            $deleted_feeds_raw = sanitize_text_field(wp_unslash($_POST['deleted_feeds']));
            $deleted_feeds = array_filter(array_map('sanitize_text_field', explode( ',', $deleted_feeds_raw)));
        }

        $modified_feeds = [];
        if (isset($_POST['modified_feeds'])) {
            $modified_feeds_raw = sanitize_text_field(wp_unslash($_POST['modified_feeds']));
            $modified_feeds = array_filter( array_map( 'sanitize_text_field', explode( ',', $modified_feeds_raw) ) );
        }

        $new_feeds = [];
        if (isset($_POST['new_feeds'])) {
            $new_feeds_raw = sanitize_text_field(wp_unslash($_POST['new_feeds']));
            $new_feeds = array_filter( array_map( 'sanitize_text_field', explode( ',', $new_feeds_raw)));
        }

        foreach ($feeds as $key => $feed) {
            if (in_array($feed['id'], $paused_feeds)) {
                $feeds[$key]['feed_status'] = 'pause';
            } else {
                $feeds[$key]['feed_status'] = 'active';
            }

            if (in_array($feed['id'], $deleted_feeds)) {
                unset($feeds[$key]);
                continue;
            }

            if ( in_array( $feed['id'], $modified_feeds, true ) ) {
                $keywords = [];
                $keyword_str = isset( $_POST[ $feed['id'] . '-keywords' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-keywords' ] ) ) : '';
                if ( ! empty( $keyword_str ) ) {
                    $keywords = explode( ',', $keyword_str );
                }

                $feed['url'] = isset( $_POST[ $feed['id'] . '-url' ] ) ? esc_url_raw( wp_unslash( $_POST[ $feed['id'] . '-url' ] ) ) : '';
                $feed['name'] = isset( $_POST[ $feed['id'] . '-name' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-name' ] ) ) : '';
                $feed['max_posts'] = isset( $_POST[ $feed['id'] . '-max_posts' ] ) ? intval( wp_unslash( $_POST[ $feed['id'] . '-max_posts' ] ) ) : 0;
                $feed['author_id'] = isset( $_POST['author_id'] ) ? intval( wp_unslash( $_POST['author_id'] ) ) : intval( $feed['author_id'] ?? 1 );
                $feed['category_id'] = isset( $_POST[ $feed['id'] . '-category_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-category_id' ] ) ) : '';
                $feed['tags_id'] = isset( $_POST[ $feed['id'] . '-tags_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-tags_id' ] ) ) : '';
                $feed['keywords'] = array_map( 'trim', $keywords );
                $feed['strip_html'] = isset( $_POST[ $feed['id'] . '-strip_html' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-strip_html' ] ) ) : '';
                $feed['nofollow_outbound'] = isset( $_POST[ $feed['id'] . '-nofollow_outbound' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-nofollow_outbound' ] ) ) : '';
                $feed['automatic_import_categories'] = isset( $_POST[ $feed['id'] . '-automatic_import_categories' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-automatic_import_categories' ] ) ) : '';
                $feed['automatic_import_author'] = isset( $_POST[ $feed['id'] . '-automatic_import_author' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $feed['id'] . '-automatic_import_author' ] ) ) : '';
                $feed['canonical_urls'] = isset( $_POST[ $feed['id'] . '-canonical_urls' ] ) ? esc_url_raw( wp_unslash( $_POST[ $feed['id'] . '-canonical_urls' ] ) ) : '';

                $feeds[$key] = $feed;
            }
        }

        foreach ($new_feeds as $id) {
            if (!$id)  continue;

            $keywords = [];
            $keyword_str = '';
            if (isset($_POST[$id . '-keywords'])) {
                $keyword_str = sanitize_text_field(wp_unslash($_POST[$id . '-keywords']));
            }
            if (!empty($keyword_str)) {
                $keywords = explode(',', $keyword_str);
            }

            $feed_status = in_array($id, $paused_feeds) ? 'pause' : 'active';

            $feeds[] = [
                'id' => $id,
                'url' => sanitize_text_field(wp_unslash($_POST[$id . '-url'] ?? '')),
                'name' => sanitize_text_field(wp_unslash($_POST[$id . '-name'] ?? '')),
                'max_posts' => intval(sanitize_text_field(wp_unslash($_POST[$id . '-max_posts'] ?? 0))),
                'author_id' => isset( $_POST['author_id'] ) ? intval( wp_unslash( $_POST['author_id'] ) ) : 1,
                'category_id' => sanitize_text_field(wp_unslash($_POST[$id . '-category_id'] ?? '')),
                'tags_id' => sanitize_text_field(wp_unslash($_POST[$id . '-tags_id'] ?? '')),
                'keywords' => array_map('trim', $keywords),
                'strip_html' => sanitize_text_field(wp_unslash($_POST[$id . '-strip_html'] ?? '')),
                'nofollow_outbound' => sanitize_text_field(wp_unslash($_POST[$id . '-nofollow_outbound'] ?? '')),
                'automatic_import_categories' => sanitize_text_field(wp_unslash($_POST[$id . '-automatic_import_categories'] ?? '')),
                'automatic_import_author' => sanitize_text_field(wp_unslash($_POST[$id . '-automatic_import_author'] ?? '')),
                'canonical_urls' => sanitize_text_field(wp_unslash($_POST[$id . '-canonical_urls'] ?? '')),
                'feed_status' => $feed_status
            ];
        }

        // process feeds end

        // import OPML file
        // @since v2.1.3
        // if (
        //     isset($_FILES['import_opml']) &&
        //     isset($_FILES['import_opml']['tmp_name']) &&
        //     is_uploaded_file($_FILES['import_opml']['tmp_name'])
        // ) {
        //     $opml = new Rss_pi_opml();
        //     $feeds = $opml->import($feeds);
        //     $opml_errors = $opml->errors;
        // } else {
        //     $opml_errors = [];
        // }

        // save and reload the options
        $this->save_reload_options($settings, $feeds);

        if ($import_now) {
            // yield the routine for import feeds via AJAX when needed
            do_action('interq_rss_pi_cron');
        }

        wp_redirect(add_query_arg(
            [
                'settings-updated' => 'true',
                // yield the routine for import feeds via AJAX when needed
                'import' => $save_to_db,
                'message' => 1,
                //'opml_errors' => $opml_errors ? urlencode(implode('<br/>', $opml_errors)) : '',
            ],
            $interq_rss_post_importer->page_link
        ));

        exit;
    }

    /**
     * Purge "deleted_posts" cache from wp_options
     * @return void
     */

    public function purge_deleted_posts_cache(): void {
        $nonce = isset( $_POST['interq_rss_pi_nonce_field'] ) ? sanitize_key( wp_unslash( $_POST['interq_rss_pi_nonce_field'] ) ) : '';
        if (
            empty( $nonce ) || ! wp_verify_nonce( $nonce, 'interq_rss_pi_save_settings_action' ) ||
            !isset($_POST['purge_deleted_cache'])
        ) {
            return;
        }

        $_POST['purge_deleted_cache'] = sanitize_text_field( wp_unslash( $_POST['purge_deleted_cache'] ) );

        delete_option('interq_rss_pi_deleted_posts');
        delete_option('interq_rss_pi_imported_posts');

        global $interq_rss_post_importer;

        wp_redirect(add_query_arg(
            [
                'deleted_cache_purged' => 'true',
            ],
            $interq_rss_post_importer->page_link
        ));

        exit;
    }

    /**
     * Import CSV function to import CSV file data into database
     * @param array $feeds
     * @return array
     */
    // private function import_csv( array $feeds ): array {

    //     if (
    //         !isset( $_FILES['import_csv']['tmp_name'])
    //        // !is_uploaded_file( $_FILES['import_csv']['tmp_name'] )
    //     ) {
    //         return $feeds;
    //     }
    //     $file = sanitize_text_field( wp_unslash( $_FILES['import_csv']['tmp_name'] ) ) ?? '';

    //     if ( ! empty( $file ) && ! file_exists( $file ) ) {
    //         return $feeds;
    //     }

    //     // Use WP Filesystem API instead of direct file operations
    //     global $wp_filesystem;

    //     if ( ! function_exists( 'WP_Filesystem' ) ) {
    //         require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/file.php' );
    //     }

    //     WP_Filesystem();

    //     // Read file using WP Filesystem
    //     if ( ! $wp_filesystem->is_file( $file ) ) {
    //         return $feeds;
    //     }

    //     $file_contents = $wp_filesystem->get_contents( $file );

    //     if ( ! $file_contents ) {
    //         return $feeds;
    //     }

    //     // Parse CSV contents
    //     $lines = explode( "\n", $file_contents );
    //     $titlearray = [];
    //     $importdata = [];
    //     $t = 0;

    //     foreach ( $lines as $csv_line ) {
    //         $csv_line = trim( $csv_line );

    //         if ( empty( $csv_line ) ) {
    //             continue;
    //         }

    //         $fields = str_getcsv( $csv_line );

    //         if ( 0 === $t ) {
    //             // First line contains headers
    //             $titlearray = $fields;
    //         } else {
    //             // Subsequent lines contain data
    //             $row = [];
    //             foreach ( $fields as $i => $field ) {
    //                 if ( isset( $titlearray[ $i ] ) ) {
    //                     $row[ $titlearray[ $i ] ] = sanitize_text_field( $field );
    //                 }
    //             }

    //             if ( ! empty( $row ) ) {
    //                 $row['id'] = 'uniqid_' . uniqid( '54d4c' );
    //                 $importdata['feeds'][] = $row;
    //             }
    //         }

    //         $t++;
    //     }

    //     if ( ! empty( $importdata['feeds'] ) ) {
    //         foreach ( $importdata['feeds'] as $r => $feed ) {
    //             // Set defaults if not present
    //             if ( ! isset( $feed['category_id'] ) ) {
    //                 $feed['category_id'] = [1];
    //                 $feed['tags_id'] = '';
    //                 $feed['keywords'] = '';
    //                 $feed['strip_html'] = 'false';
    //             } else {
    //                 $feed['category_id'] = array_map( 'intval', explode( ',', $feed['category_id'] ) );
    //                 $feed['tags_id'] = explode( ',', $feed['tags_id'] ?? '' );
    //                 $feed['keywords'] = explode( ',', $feed['keywords'] ?? '' );
    //                 $feed['strip_html'] = isset( $feed['strip_html'] ) ? sanitize_text_field( $feed['strip_html'] ) : 'false';
    //             }

    //             $check_result = $this->check_feed_exist( $feeds, $feed );

    //             if ( ! $check_result ) {
    //                 $feeds[] = $feed;
    //             }
    //         }
    //     }

    //     return $feeds;
    // }

    /**
     * @param array $feeds
     * @param array $csvlink
     * @return bool
     */
    public function check_feed_exist(array $feeds, array $csvlink): bool {
        if (!empty($feeds) && !empty($csvlink)) {
            foreach ($feeds as $feed) {
                if (isset($feed['url'], $csvlink['url']) && $feed['url'] === $csvlink['url']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Process submitted data to formulate settings array
     *
     * @global object $interq_rss_post_importer
     * @return array
     */

    /**
     * Update the frequency of the import cron job
     *
     * @param string $frequency
     */
    private function update_cron(string $frequency): void {

        // If cron settings have changed
        if (wp_get_schedule('interq_rss_pi_cron') != $frequency) {

            // Reset cron
            wp_clear_scheduled_hook('interq_rss_pi_cron');
            wp_schedule_event(time(), $frequency, 'interq_rss_pi_cron');
        }
    }

    /**
     * Creates the feeds array from the submitted data
     *
     * @param array $feeds
     * @return array
     */

    /**
     * Update options and reload global options
     *
     * @global type $interq_rss_post_importer
     * @param array $settings
     * @param array $feeds
     */
    private function save_reload_options(array $settings, array $feeds): void {
        global $interq_rss_post_importer;

        // existing options
        $options = $interq_rss_post_importer->options;

        // new data
        $new_options = [
            'feeds' => $feeds,
            'settings' => $settings,
            'latest_import' => $options['latest_import'] ?? null,
            'imports' => $options['imports'] ?? null,
            'upgraded' => $options['upgraded'] ?? null
        ];

        // update in db
        update_option('interq_rss_pi_feeds', $new_options);

        // reload so that the new options are used henceforth
        $interq_rss_post_importer->load_options();
    }

}
