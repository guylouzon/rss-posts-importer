<?php

/**
 * One class to rule them all
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */

class rssPostImporter {

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
     * @var rssPIAdmin
     */
    private rssPIAdmin $admin;

    /**
     * @var rssPICron
     */
    private rssPICron $cron;

    /**
     * @var rssPIFront
     */
    private rssPIFront $front;

    /**
     * Start
     */
    public function __construct() {
        // populate the options first
        $this->load_options();

        // do any upgrade if needed
        $this->upgrade();

        $settings = [];
        $valid_api_key = '';
        if (isset($_POST['feeds_api_key'])) {
            $settings = [
                'feeds_api_key' => sanitize_key($_POST['feeds_api_key'])
            ];

            // check if submitted api key is valid
            $valid_api_key = $this->is_valid_key($settings['feeds_api_key']);
        }

        // determine the API type
        $api_type = $valid_api_key == '' ? 'normal' : 'premium';

        // setup this plugin options page link
        $this->page_link = admin_url(
            'options-general.php?page=rss_pi&version=' . RSS_PI_VERSION .
            '&type=' . $api_type
        );

        // hook translations
        add_action('plugins_loaded', [$this, 'localize']);

        add_filter(
            'plugin_action_links_' . RSS_PI_BASENAME,
            [$this, 'settings_link']
        );
    }

    /**
     * Load options from the db
     */
    public function load_options(): void {

        $default_settings = [
            'enable_logging' => true,
            'feeds_api_key' => false,
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

        $options = get_option('rss_pi_feeds', []);

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
            update_option('rss_pi_feeds', $new_options);
        }
    }

    /**
     * Upgrade plugin settings
     */
    public function upgrade(): void {

        global $wpdb;
        $upgraded = false;
        $bail = false;

        // migrate to rss_pi_deleted_posts only items from rss_pi_imported_posts that are actually deleted, discard the others
        // do this in iterations so not to degrade the UX
        if (!isset($this->options['upgraded']['deleted_posts'])) {
            // get meta data for "deleted" and "imported" posts
            $rss_pi_deleted_posts = get_option('rss_pi_deleted_posts', []);
            $rss_pi_imported_posts = get_option('rss_pi_imported_posts', []);
            $rss_pi_imported_posts_migrated = get_option('rss_pi_imported_posts_migrated', []);
            // limit execution time (in seconds)
            $_limit = ((defined('DOING_CRON') && DOING_CRON) ? 20 : ((defined('DOING_AJAX') && DOING_AJAX) ? 10 : 3));
            $_start = microtime(true);
            // iterate through all imported posts' source URLs
            foreach ($rss_pi_imported_posts as $k => $source_url) {
                // hash the URL for storage
                $source_md5 = md5($source_url);
                // properly format the URL for comparison
                $source_url = esc_url($source_url);
                // skip if we already have "migrated" this item
                if (in_array($k, $rss_pi_imported_posts_migrated)) {
                    continue;
                }
                // skip if we already have "deleted" metadata for this item
                if (in_array($source_md5, $rss_pi_deleted_posts)) {
                    continue;
                }
                $rss_pi_imported_posts_migrated[] = $k;
                // check if there is a post with this source URL
                $post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'rss_pi_source_url' and meta_value = %s", $source_url));
                // when there is no such post (it was deleted?)
                if (!$post_id) {
                    // add this source URL to "deleted" metadata
                    $rss_pi_deleted_posts[] = $source_md5;
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
            $rss_pi_deleted_posts = array_unique($rss_pi_deleted_posts);
            update_option('rss_pi_deleted_posts', $rss_pi_deleted_posts);
            // keep record of migrated items
            update_option('rss_pi_imported_posts_migrated', $rss_pi_imported_posts_migrated);
            // are there still source URLs in the "imported" metadata?
            if (count($rss_pi_imported_posts_migrated) < count($rss_pi_imported_posts)) {
                // not finished yet
            } else {
                // remove the "imported" metadata from database
                delete_option('rss_pi_imported_posts_migrated');
                delete_option('rss_pi_imported_posts');
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
            update_option('rss_pi_feeds', $this->options);
        }
    }

    /**
     * Load translations
     */
    public function localize(): void {
        load_plugin_textdomain('rss-post-importer', false, RSS_PI_PATH . 'app/lang/');
    }

    /**
     * Initialise
     */
    public function init(): void {

        // initialise admin and cron
        $this->cron = new rssPICron();
        $this->cron->init();

        $this->admin = new rssPIAdmin();
        $this->admin->init();

        $this->front = new rssPIFront();
        $this->front->init();
    }

    /**
     * Check if a given API key is valid
     *
     * @param string $key
     * @return bool
     */
    public function is_valid_key(string $key): bool {

        if (empty($key)) {
            return false;
        }

        $url = "https://www.feedsapi.org/fetch.php?key=$key" .
            "&url=" . "http://dummyurl.com";

        $content = @file_get_contents($url);
        $content = trim((string)$content);

        if ($content == "A valid key must be supplied") {
            return false;
        }

        if ($content == "Invalid IP/DOMAIN") {
            return false;
        }

        if ($content == "No URL supplied") {
            return false;
        }

        return true;
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
