<?php

/**
 * Handles cron jobs
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
class rssPICron {

    /**
     * Initialise
     */
    public function init(): void {

        // hook up scheduled events
        add_action('wp', [$this, 'schedule']);

        add_action('rss_pi_cron', [$this, 'import']);
    }

    /**
     * Check and confirm scheduling
     */
    public function schedule(): void {

        if (!wp_next_scheduled('rss_pi_cron')) {
            wp_schedule_event(time(), 'hourly', 'rss_pi_cron');
        }
    }

    /**
     * Import the feeds on schedule
     *
     */
    public function import(): void {

        $engine = new rssPIEngine();
        $engine->import_feed();
    }

}
