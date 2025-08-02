<?php

/**
 * Manipulates log files
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
class rssPILog {

    /**
     * Initialise
     */
    public function init(): void {

        // hook ajax for loading and clearing log on admin screen
        add_action('wp_ajax_rss_pi_load_log', [$this, 'load_log']);
        add_action('wp_ajax_rss_pi_clear_log', [$this, 'clear_log']);
    }

    /**
     * Loads log contents
     */
    public function load_log(): void {

        // get the log file's contents
        if (!file_exists(RSS_PI_LOG_PATH . 'log.txt')) {
            return '';
        }
        else {
            $log = file_get_contents(RSS_PI_LOG_PATH . 'log.txt');
        }

        // include the template to display it
        include(RSS_PI_PATH . 'app/templates/log.php');
        die();
    }

    public function clear_log(): void {

        // get the log file
        $log_file = RSS_PI_LOG_PATH . 'log.txt';

        if (!file_exists($log_file)) {
            die();
        }

        // empty it
        file_put_contents($log_file, '');
        ?>
        <div id="message" class="updated">
            <p><strong><?php _e('Log has been cleared.', "rss-post-importer"); ?></strong></p>
        </div>
        <?php
        die();
    }

    /**
     * Static method to add log messages
     * 
     * @global object $rss_post_importer Global object
     * @param int $post_count Number of posts imported
     * @return void
     */
    public static function log(int $post_count): void {

        global $rss_post_importer;

        // if logging is disabled, return early
        if (($rss_post_importer->options['settings']['enable_logging'] ?? '') !== 'true') {
            return;
        }

        // prepare the log entry
        $log = date("Y-m-d H:i:s") . "\t Imported " . $post_count . " new posts. \n";

        $log_file = RSS_PI_LOG_PATH . 'log.txt';

        // add it to the log file
        file_put_contents($log_file, $log, FILE_APPEND);
    }

}
