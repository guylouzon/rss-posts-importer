<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * One class to rule them all
 *
 */

class InterQ_Rss_Posts_Importer {

    /**
     * A var to store the options in
     * @var array
     */
    public array $options = [];

    /**
     * A var to store the link to the plugin page
     * @var string
     */
    public string $page_link = '';

    /**
     * To initialise the admin and cron classes
     *
     * @var InterQ_Rss_Pi_Admin
     */
    private InterQ_Rss_Pi_Admin $admin;

    /**
     * @var InterQ_Rss_Pi_Cron
     */
    private InterQ_Rss_Pi_Cron $cron;

    /**
     * @var InterQ_Rss_Pi_Front
     */
    private InterQ_Rss_Pi_Front $front;

    /**
     * Start
     */
    public function __construct() {
        // populate the options first
        $this->load_options();

        // do any upgrade if needed
        $this->upgrade();


        // setup this plugin options page link
        $this->page_link = admin_url(
            'options-general.php?page=interq_rss_pi&version=' . INTERQ_RSS_PI_VERSION
        );


        add_filter(
            'plugin_action_links_' . INTERQ_RSS_PI_BASENAME,
            [$this, 'settings_link']
        );
    }

    /**
     * Migrate pre-prefix option names and the legacy cron hook.
     * One-time upgrade for installs from before the interq_rss_pi_ prefix.
     */
    public function migrate_option_names(): void {

        $option_map = [
            'rss_pi_feeds' => 'interq_rss_pi_feeds',
            'rss_pi_deleted_posts' => 'interq_rss_pi_deleted_posts',
            'rss_pi_imported_posts' => 'interq_rss_pi_imported_posts',
            'rss_pi_imported_posts_migrated' => 'interq_rss_pi_imported_posts_migrated',
            'rsspi_custom_cron_frequency' => 'interq_rss_pi_custom_cron_frequency',
        ];

        foreach ($option_map as $old_name => $new_name) {
            $old_value = get_option($old_name, null);
            if ($old_value !== null) {
                if (get_option($new_name, null) === null) {
                    add_option($new_name, $old_value);
                }
                delete_option($old_name);
            }
        }

        // clear the legacy cron hook; the new one is scheduled by InterQ_Rss_Pi_Cron::schedule()
        if (wp_next_scheduled('rss_pi_cron')) {
            wp_clear_scheduled_hook('rss_pi_cron');
        }
    }

    /**
     * Load options from the db
     */
    public function load_options(): void {

        $this->migrate_option_names();

        $default_settings = [
            'enable_logging' => true,
            'frequency' => 0,
            'post_template' => "{\$content}\n<hr>\nContinue reading: {\$permalink}\n",
            'post_status' => 'publish',
            'author_id' => 1,
            'allow_comments' => 'open',
            'block_indexing' => false,
            'nofollow_outbound' => true,
            'keywords' => [],
            'import_images_locally' => false,
            'disable_thumbnail' => false,
            'cache_deleted' => true,
        ];

        $options = get_option('interq_rss_pi_feeds', []);

        // prepare default options when there is no record in the database
        if (!isset($options['feeds']))  {
            $options['feeds'] = [];
        }
        if (!isset($options['settings'])) {
            $options['settings'] = [];
        }
        if (!isset($options['latest_import'])) {
            $options['latest_import'] = '';
        }
        if (!isset($options['imports'])) {
            $options['imports'] = 0;
        }
        if (!isset($options['upgraded'])) {
            $options['upgraded'] = [];
        }

        $options['settings'] = wp_parse_args($options['settings'], $default_settings);

        if (!array_key_exists('imports', $options)) {
            $options['imports'] = 0;
        }

        $this->options = $options;

        if (empty($options['feeds'])) {
            $default_feed = [
                [
                    'id' => uniqid(),
                    'name' => 'interQ Trending',
                    'url' => 'https://interq.link/42/6x7.php?v=rss&channel=238',
                    'max_posts' => 10,
                    'author_id' => 1,
                    'category_id' => [1],
                    'tags_id' => [],
                    'strip_html' => 'false',
                    'nofollow_outbound' => 'false',
                    'automatic_import_categories' => 'false',
                    'automatic_import_author' => 'false',
                    'feed_status' => 'pause',
                    'canonical_urls' => 'my_blog'
                ]
            ];
            
            $new_options = array(
                'feeds' => $default_feed,
                'settings' => $this->options['settings'],
                'latest_import' => $this->options['latest_import'] ?? '',
                'imports' => $this->options['imports'] ?? 0,
                'upgraded' => $this->options['upgraded'] ?? null
            );
            // update in db
            update_option('interq_rss_pi_feeds', $new_options);
        }
    }

    /**
     * Upgrade plugin settings
     */
    public function upgrade(): void {

        global $wpdb;
        $upgraded = false;
        $bail = false;

        // migrate to interq_rss_pi_deleted_posts only items from interq_rss_pi_imported_posts that are actually deleted, discard the others
        // do this in iterations so not to degrade the UX
        if (!isset($this->options['upgraded']['deleted_posts'])) {
            // get meta data for "deleted" and "imported" posts
            $interq_rss_pi_deleted_posts = get_option('interq_rss_pi_deleted_posts', []);
            $interq_rss_pi_imported_posts = get_option('interq_rss_pi_imported_posts', []);
            $interq_rss_pi_imported_posts_migrated = get_option('interq_rss_pi_imported_posts_migrated', []);
            // limit execution time (in seconds)
            $_limit = ((defined('DOING_CRON') && DOING_CRON) ? 20 : ((defined('DOING_AJAX') && DOING_AJAX) ? 10 : 3));
            $_start = microtime(true);
            // iterate through all imported posts' source URLs
            foreach ($interq_rss_pi_imported_posts as $k => $source_url) {
                // hash the URL for storage
                $source_md5 = md5($source_url);
                // properly format the URL for comparison
                $source_url = esc_url($source_url);
                // skip if we already have "migrated" this item
                if (in_array($k, $interq_rss_pi_imported_posts_migrated)) {
                    continue;
                }
                // skip if we already have "deleted" metadata for this item
                if (in_array($source_md5, $interq_rss_pi_deleted_posts)) {
                    continue;
                }
                $interq_rss_pi_imported_posts_migrated[] = $k;
                // check if there is a post with this source URL

                $posts = get_posts( [
                    'post_type'   => 'any',
                    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- One-time migration must locate posts by the plugin's source URL metadata.
                    'meta_query'  => [
                        [
                            'key'     => 'rss_pi_source_url',
                            'value'   => $source_url,
                            'compare' => '=',
                        ],
                    ],
                    'fields'      => 'ids',
                    'posts_per_page' => 1,
                    'no_found_rows'  => true, // Don't count total rows
                ] );

                // $posts = get_posts( [
                //     'post_type'  => 'any',
                //     'meta_key'   => 'rss_pi_source_url',
                //     'meta_value' => $source_url,
                //     'fields'     => 'ids', // Only return the ID for performance
                //     'limit'      => 1,
                // ] );

                $post_id = ! empty( $posts ) ? $posts[0] : null;
                // when there is no such post (it was deleted?)
                if (!$post_id) {
                    // add this source URL to "deleted" metadata
                    $interq_rss_pi_deleted_posts[] = $source_md5;
                } else {
                    // otherwise update the post metadata to include hashed URL
                    update_post_meta($post_id, 'rss_pi_source_md5', $source_md5);
                }
                // remove it from "imported" metadata
                $_curr = microtime(true);
                if ($_curr - $_start > $_limit) {
                    // bail out when the "max execution time" limit is exhausted
                    $bail = true;
                    break;
                }
            }
            // shed any duplicates
            $interq_rss_pi_deleted_posts = array_unique($interq_rss_pi_deleted_posts);
            update_option('interq_rss_pi_deleted_posts', $interq_rss_pi_deleted_posts);
            // keep record of migrated items
            update_option('interq_rss_pi_imported_posts_migrated', $interq_rss_pi_imported_posts_migrated);
            // are there still source URLs in the "imported" metadata?
            if (count($interq_rss_pi_imported_posts_migrated) < count($interq_rss_pi_imported_posts)) {
                // not finished yet
            } else {
                // remove the "imported" metadata from database
                delete_option('interq_rss_pi_imported_posts_migrated');
                delete_option('interq_rss_pi_imported_posts');
                // mark this upgrade as completed
                $this->options['upgraded']['deleted_posts'] = true;
                $upgraded = true;
            }
        }
        // check after each upgrade routine
        if ($bail) {
            return;
        }

        // if there is something to record as an upgrade
        if ($upgraded) {
            update_option('interq_rss_pi_feeds', $this->options);
        }
    }

    /**
     * Initialise
     */
    public function init(): void {

        // initialise admin and cron
        $this->cron = new InterQ_Rss_Pi_Cron();
        $this->cron->init();

        $this->admin = new InterQ_Rss_Pi_Admin();
        $this->admin->init();

        $this->front = new InterQ_Rss_Pi_Front();
        $this->front->init();
    }

    /**
     * Adds a settings link
     *
     * @param array $links Existing links
     * @return array
     */
    public function settings_link(array $links): array {
        $settings_link = [
            '<a href="' . $this->page_link . '">Settings</a>',
        ];
        return array_merge($settings_link, $links);
    }

}
