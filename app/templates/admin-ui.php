<?php
// Ensure this file is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <div id="main_ui">
<?php
$new_version = INTERQ_RSS_PI_VERSION;
?>

        <h2><?php esc_html_e("Rss Post Importer Feeds and Settings", 'interq-rss-posts-importer'); ?></h2>

        <div id="rss_pi_progressbar"></div>
        <div id="rss_pi_progressbar_label"></div>

        <form method="post" id="rss_pi-settings-form" enctype="multipart/form-data" action="<?php echo esc_url($interq_rss_post_importer->page_link); ?>">

            <input type="hidden" name="info_update" id="info_update" value="true">
            <input type="hidden" name="save_to_db" id="save_to_db" >
            <input type="hidden" name="import_now" id="import_now" value="false">

            <?php wp_nonce_field('interq_rss_pi_save_settings_action', 'interq_rss_pi_nonce_field'); ?>
            <input type="hidden" name="interq_rss_pi_nonce_field" id="interq_rss_pi_ajax_nonce" value="<?php echo esc_attr(wp_create_nonce('interq_rss_pi_ajax_nonce_action')); ?>" />

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">

                    <div id="postbox-container-1" class="postbox-container">
                        <?php include_once INTERQ_RSS_PI_PATH . 'app/templates/feed-save-box.php'; ?>
                    </div>

                    <div id="postbox-container-2" class="postbox-container">

                        <?php
                        include_once INTERQ_RSS_PI_PATH . 'app/templates/feed-table.php';
                        include_once INTERQ_RSS_PI_PATH . 'app/templates/settings-table.php';
                        ?>
                    </div>

                </div>
                <br class="clear" />
            </div>
        </form>

    </div>

    <div class="ajax_content"></div>
</div>