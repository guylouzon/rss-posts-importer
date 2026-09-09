<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Handles cron jobs
 *
 */
class InterQ_Rss_Pi_Cron {

    /**
     * Initialise
     */
    public function init(): void {

        // hook up scheduled events
        add_action('wp', [$this, 'schedule']);

        add_action('interq_rss_pi_cron', [$this, 'import']);
    }

    /**
     * Check and confirm scheduling
     */
    public function schedule(): void {

        if (!wp_next_scheduled('interq_rss_pi_cron')) {
            wp_schedule_event(time(), 'hourly', 'interq_rss_pi_cron');
        }
    }

    /**
     * Import the feeds on schedule
     *
     */
    public function import(): void {

        $engine = new InterQ_Rss_Pi_Engine();
        $engine->import_feed();
    }

}
