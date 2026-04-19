<?php
/**
 * Settings Table Template for InterQ RSS posts importer
 * Ensures compliance with WordPress.org security and translation standards.
 */
?>

<button type="button" class="rsspi_settings_control_button button button-primary" id="toggle-rsspi-settings-table">
    <?php esc_html_e('Settings', 'interq-rss-pi'); ?>
    <span class="dashicons dashicons-arrow-down settings-table-wrapper" aria-hidden="true"></span>
</button>

<div id="rsspi-settings-table" class="rss_pi_close">
    <table class="widefat rss_pi-table" id="rss_pi-settings-table">
        <thead>
            </thead>
        <tbody class="setting-rows">
            <tr class="edit-row show">
                <td colspan="4">
                    <table class="widefat edit-table">
                        <tr>
                            <td>
                                <label for="frequency"><?php esc_html_e('Frequency', 'interq-rss-pi'); ?></label>
                                <p class="description"><?php esc_html_e('How often will the import run.', 'interq-rss-pi'); ?></p>
                                <p class="description"><?php esc_html_e('Custom Frequency in minutes only.', 'interq-rss-pi'); ?></p>
                            </td>
                            <td>
                                <?php
                                $schedules = wp_get_schedules();
                                $custom_cron_options = get_option('rss_custom_cron_frequency', []);
                                
                                // Clean up serialization logic
                                if (!empty($custom_cron_options) && is_string($custom_cron_options)) {
                                    $rss_custom_cron = maybe_unserialize($custom_cron_options);
                                    if (!is_array($rss_custom_cron)) {
                                        $rss_custom_cron = [];
                                    }
                                } else {
                                    $rss_custom_cron = is_array($custom_cron_options) ? $custom_cron_options : [];
                                }
                                ?>
                                <select name="frequency" id="frequency">
                                    <?php
                                    foreach ($schedules as $interval => $details) :
                                        // Skip if this is the currently active custom cron to avoid duplicates
                                        if (empty($rss_custom_cron) || ($rss_custom_cron['frequency'] ?? '') != $interval) :
                                    ?>
                                            <option value="<?php echo esc_attr($interval); ?>" <?php selected($this->options['settings']['frequency'], $interval); ?>>
                                                <?php echo esc_html($details['display']); ?>
                                            </option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>

                                    <option value="custom_frequency" <?php selected(($this->options['settings']['custom_frequency'] ?? ''), 'true'); ?>>
                                        <?php esc_html_e('Custom frequency', 'interq-rss-pi'); ?>
                                    </option>
                                </select>
                                &nbsp;

                                <input type="text" id="rss_custom_frequency" name="rss_custom_frequency" 
                                    value="<?php echo esc_attr($rss_custom_cron['time'] ?? ''); ?>" 
                                    placeholder="<?php esc_attr_e('Minutes', 'interq-rss-pi'); ?>"
                                    style="display: <?php echo (isset($this->options['settings']['custom_frequency']) && $this->options['settings']['custom_frequency'] == 'true') ? 'inline' : 'none'; ?>;" 
                                />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for="post_template"><?php esc_html_e('Template', 'interq-rss-pi'); ?></label>
                                <p class="description"><?php esc_html_e('This is how the post will be formatted.', 'interq-rss-pi'); ?></p>
                                <div class="description">
                                    <?php esc_html_e('Available tags:', 'interq-rss-pi'); ?>
                                    <dl>
                                        <dt><code>{$content}</code></dt>
                                        <dt><code>{$permalink}</code></dt>
                                        <dt><code>{$title}</code></dt>
                                        <dt><code>{$feed_title}</code></dt>
                                        <dt><code>{$excerpt:n}</code></dt>
                                        <dt><code>{$inline_image}</code> <small><?php esc_html_e('insert the featured image inline into the post content', 'interq-rss-pi'); ?></small></dt>
                                    </dl>
                                </div>
                            </td>
                            <td>
                                <textarea name="post_template" id="post_template" cols="30" rows="10"><?php
                                    $value = (!empty($this->options['settings']['post_template']) ? $this->options['settings']['post_template'] : '{$content}' . "\nSource: " . '{$feed_title}');
                                    // Properly handle newline characters and slashes
                                    $value = str_replace(array('\r', '\n'), array(chr(13), chr(10)), $value);
                                    echo esc_textarea(stripslashes($value));
                                ?></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="post_status"><?php esc_html_e('Post status', 'interq-rss-pi'); ?></label></td>
                            <td>
                                <select name="post_status" id="post_status">
                                    <?php
                                    $statuses = get_post_stati('', 'objects');
                                    foreach ($statuses as $status) :
                                    ?>
                                        <option value="<?php echo esc_attr($status->name); ?>" <?php selected($this->options['settings']['post_status'], $status->name); ?>>
                                            <?php echo esc_html($status->label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><?php esc_html_e('Author', 'interq-rss-pi'); ?></td>
                            <td>
                                <?php
                                wp_dropdown_users(array(
                                    'id'       => 'author_id',
                                    'name'     => 'author_id',
                                    'selected' => $this->options['settings']['author_id']
                                ));
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td><?php esc_html_e('Allow comments', 'interq-rss-pi'); ?></td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="radio" id="allow_comments_open" name="allow_comments" value="open" <?php checked($this->options['settings']['allow_comments'], 'open'); ?> /> 
                                            <?php esc_html_e('Yes', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" id="allow_comments_false" name="allow_comments" value="false" <?php checked($this->options['settings']['allow_comments'], 'false'); ?> /> 
                                            <?php esc_html_e('No', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php esc_html_e('Block search indexing?', 'interq-rss-pi'); ?>
                                <p class="description"><?php esc_html_e('Prevent your content from appearing in search results.', 'interq-rss-pi'); ?></p>
                            </td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="radio" id="block_indexing_true" name="block_indexing" value="true" <?php checked($this->options['settings']['block_indexing'], 'true'); ?> /> 
                                            <?php esc_html_e('Yes', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" id="block_indexing_false" name="block_indexing" value="false" <?php checked(empty($this->options['settings']['block_indexing']) || $this->options['settings']['block_indexing'] === 'false'); ?> /> 
                                            <?php esc_html_e('No', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php esc_html_e('Nofollow option for all outbound links?', 'interq-rss-pi'); ?>
                                <p class="description"><?php esc_html_e('Add rel="nofollow" to all outbound links.', 'interq-rss-pi'); ?></p>
                            </td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="radio" id="nofollow_outbound_true" name="nofollow_outbound" value="true" <?php checked($this->options['settings']['nofollow_outbound'], 'true'); ?> /> 
                                            <?php esc_html_e('Yes', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" id="nofollow_outbound_false" name="nofollow_outbound" value="false" <?php checked(empty($this->options['settings']['nofollow_outbound']) || $this->options['settings']['nofollow_outbound'] === 'false'); ?> /> 
                                            <?php esc_html_e('No', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php esc_html_e('Enable logging?', 'interq-rss-pi'); ?>
                                <p class="description">
                                    <?php 
                                    printf(
                                        /* translators: %s: opening and closing anchor tags */
                                        esc_html__( 'The logfile can be found %1$shere%2$s.', 'interq-rss-pi' ),
                                        '<a href="#" class="load-log">',
                                        '</a>'
                                    ); 
                                    ?>
                                </p>
                            </td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="radio" id="enable_logging_true" name="enable_logging" value="true" <?php checked($this->options['settings']['enable_logging'], 'true'); ?> /> 
                                            <?php esc_html_e('Yes', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" id="enable_logging_false" name="enable_logging" value="false" <?php checked(empty($this->options['settings']['enable_logging']) || $this->options['settings']['enable_logging'] === 'false'); ?> /> 
                                            <?php esc_html_e('No', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php esc_html_e('Download and save images locally?', 'interq-rss-pi'); ?>
                                <p class="description"><?php esc_html_e('Images in the feeds will be downloaded and saved in the WordPress media.', 'interq-rss-pi'); ?></p>
                            </td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="radio" id="import_images_locally_true" name="import_images_locally" value="true" <?php checked($this->options['settings']['import_images_locally'], 'true'); ?> /> 
                                            <?php esc_html_e('Yes', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" id="import_images_locally_false" name="import_images_locally" value="false" <?php checked(empty($this->options['settings']['import_images_locally']) || $this->options['settings']['import_images_locally'] === 'false'); ?> /> 
                                            <?php esc_html_e('No', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php esc_html_e('Disable the featured image?', 'interq-rss-pi'); ?>
                                <p class="description"><?php esc_html_e("Don't set a featured image for the imported posts.", 'interq-rss-pi'); ?></p>
                            </td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="radio" id="disable_thumbnail_true" name="disable_thumbnail" value="true" <?php checked($this->options['settings']['disable_thumbnail'], 'true'); ?> /> 
                                            <?php esc_html_e('Yes', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" id="disable_thumbnail_false" name="disable_thumbnail" value="false" <?php checked(empty($this->options['settings']['disable_thumbnail']) || $this->options['settings']['disable_thumbnail'] === 'false'); ?> /> 
                                            <?php esc_html_e('No', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php esc_html_e('Social Media Optimization and Open Graph', 'interq-rss-pi'); ?>
                                <p class="description"><?php esc_html_e('Social Media and Open Graph optimization', 'interq-rss-pi'); ?></p>
                            </td>
                            <td>
                                <ul class="radiolist">
                                    <li>
                                        <label>
                                            <input type="checkbox" name="tw_show" id="tw_show" value="1" <?php checked(isset($this->options['settings']['tw_show']) && $this->options['settings']['tw_show'] == '1'); ?> />
                                            <?php esc_html_e('X', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="og_show" id="og_show" value="1" <?php checked(isset($this->options['settings']['og_show']) && $this->options['settings']['og_show'] == '1'); ?> />
                                            <?php esc_html_e('Facebook Opengraph', 'interq-rss-pi'); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>