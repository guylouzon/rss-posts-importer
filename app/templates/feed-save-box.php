<?php
    $pi = RSS_PI_VERSION;
    $latest_import = $this->options['latest_import'] ?? 'never';
    $banner = esc_url(plugins_url( 'assets/img/iq_rss_pi_banner.jpg', __FILE__ ));
?>
<div class="postbox">
    <div class="inside">
        <div class="misc-pub-section">
            <h3 class="version">V. <?php esc_html($pi); ?></h3>
            <ul>
                <li>
                    <i class="icon-calendar"></i> <?php esc_html_e("Latest import:", 'rss-posts-importer'); ?> <strong><?php esc_html($latest_import); ?></strong>
                </li>
                <li><i class="icon-eye-open"></i> <a href="#" class="load-log"><?php esc_html_e("View the log", 'rss-posts-importer'); ?></a></li>
            </ul>
        </div>
        <div id="major-publishing-actions">
            <input class="button button-large right" type="submit" name="info_update" value="<?php esc_attr_e('Save All', 'rss-posts-importer'); ?>" style="background-color:#d63638;color:#fff;border-color:#d63638;" />
        </div>
    </div>
</div>
<?php if (($this->options['imports'] ?? 0) > 10) : ?>
    <div class="rate-box">
       <h4><?php
        /* translators: 1: Number of imported posts */
        echo esc_html(sprintf(_n('%d post imported and counting!','%d posts imported and counting!',$this->options['imports'],'rss-posts-importer'),number_format_i18n($this->options['imports'] ))); 
       ?></h4>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <p class="description"><a href="http://wordpress.org/plugins/rss-posts-importer/" target="_blank">Please support this plugin by rating it!</a></p>
    </div>
<?php endif; ?>

<?php
    $diff = (int) (time() - strtotime('2000-01-01')) / (60 * 60 * 24);
    $base62 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $result = '';
    while ($diff > 0) {
        $result = $base62[(int)$diff % 62] . $result;
        $diff = (int) ($diff / 62);
    }
    $rand_str = '';
    for ($i = 0; $i < 12; $i++) {
        $rand_str .= $base62[wp_rand(0, 61)];
    }
    $result = $result . $rand_str;
?>
<a href="https://interq.link/42/6x7.php?v=webapp&channel=238" target="_blank"><img src="<?php echo esc_url(plugins_url( 'assets/img/trending_banner.png', RSS_PI_PATH )); ?>" class="rss_pi_banner_img"></a>
<a target="_blank" href="https://interq.link/?source=rsspi'">
    <img class="rss_pi_banner_img" src="<?php echo esc_url($banner); ?>" alt="<?php esc_attr_e('RSS Posts Importer Banner', 'rss-posts-importer'); ?>" />
</a>