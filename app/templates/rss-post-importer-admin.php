<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<!-- New HTML based version -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterQ RSS Post Importer Admin UI</title>
</head>
<body>
    <div class="wrap">
        <div id="main_ui">
            <h2>InterQ RSS Post Importer Feeds and Settings</h2>

            <div id="rss_pi_progressbar"></div>
            <div id="rss_pi_progressbar_label"></div>

            <form method="post" id="rss_pi-settings-form" enctype="multipart/form-data" action="<?php echo esc_url($rss_post_importer->page_link); ?>">
                <input type="hidden" name="info_update" id="info_update" value="true">
                <input type="hidden" name="save_to_db" id="save_to_db">
                <input type="hidden" name="import_now" id="import_now" value="false">
                <?php wp_nonce_field('rss_pi_save_settings_action', 'rss_pi_nonce_field'); ?>
                <input type="hidden" id="rss_pi_ajax_nonce" value="<?php echo esc_attr(wp_create_nonce('rss_pi_ajax_nonce_action')); ?>" />

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder">
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="postbox">
                                <div class="inside">
                                    <div class="misc-pub-section">
                                        <h3 class="version">V. 2.8.5</h3>
                                        <ul>
                                            <li><strong>Latest import:</strong> <span id="latest-import">never</span></li>
                                            <li><a href="#" class="load-log">View the log</a></li>
                                        </ul>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <button type="button" class="button button-large button-primary" id="save-all-btn">Save All<span class="unsaved-indicator" style="display:none;">*</span></button>
                                    </div>
                                </div>
                            </div>
                            <div id="rate-box-container"></div>
                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <!-- Feeds Table -->
                            <div class="postbox">
                                <h3>Feeds</h3>
                                <div class="inside">
                                    <div class="table-wrapper" id="rss_pi-feed-table">
                                        <!-- Header Row -->
                                        <div class="table-row header-row">
                                            <div class="table-cell">Feed name</div>
                                            <div class="table-cell">Feed url</div>
                                            <div class="table-cell">Max posts / import</div>
                                        </div>
                                        <!-- Data rows will be inserted here -->
                                        <div id="feeds-tbody"></div>
                                    </div>

                                    <div style="padding: 12px; border-top: 1px solid #eee;">
                                        <button type="button" class="button button-large button-primary" id="add-feed-btn">Add new feed</button>
                                        <input type="hidden" name="deleted_feeds" id="deleted_feeds" value="">
                                        <input type="hidden" name="modified_feeds" id="modified_feeds" value="">
                                        <input type="hidden" name="new_feeds" id="new_feeds" value="">
                                        <input type="hidden" id="paused_feeds" name="paused_feeds" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Table -->
                            <div class="postbox">
                                <button type="button" class="rsspi_settings_control_button button button-primary" id="toggle-rsspi-settings-table">
                                    Settings
                                    <span class="dashicons dashicons-arrow-down settings-table-wrapper" aria-hidden="true">▼</span>
                                </button>
                                <div id="rsspi-settings-table" class="rss_pi_close">
                                    <div class="inside">
                                        <table class="widefat rss_pi-table" id="rss_pi-settings-table">
                                            <tbody class="setting-rows" id="settings-tbody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </form>
        </div>
        <div class="ajax_content"></div>
    </div>

</body>
</html>