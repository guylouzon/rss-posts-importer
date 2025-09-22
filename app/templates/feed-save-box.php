<div class="postbox">
    <div class="inside">
        <div class="misc-pub-section">
            <h3 class="version">V. <?php echo RSS_PI_VERSION; ?></h3>
            <ul>
                <li>
                    <i class="icon-calendar"></i> <?php _e("Latest import:", 'rss-post-importer'); ?> <strong><?php echo $this->options['latest_import'] ?? 'never'; ?></strong>
                </li>
                <li><i class="icon-eye-open"></i> <a href="#" class="load-log"><?php _e("View the log", 'rss-post-importer'); ?></a></li>
            </ul>
        </div>
        <div id="major-publishing-actions">
            <input class="button button-large right" type="submit" name="info_update" value="<?php _e('Save All', 'rss-post-importer'); ?>" style="background-color:#d63638;color:#fff;border-color:#d63638;" />
            <input class="button button-large" type="submit" name="info_update" value="<?php _e('Save Settings and import', "rss-post-importer"); ?>" id="save_and_import" style="background-color:#d63638;color:#fff;border-color:#d63638;" />
        </div>
    </div>
</div>
<?php if (($this->options['imports'] ?? 0) > 10) : ?>
    <div class="rate-box">
        <h4><?php printf(__('%d posts imported and counting!', "rss-post-importer"), $this->options['imports']); ?></h4>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <p class="description"><a href="http://wordpress.org/plugins/rss-post-importer/" target="_blank">Please support this plugin by rating it!</a></p>
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
        $rand_str .= $base62[rand(0, 61)];
    }
    $result = $result . $rand_str;
?>
<a href="https://interq.link/42/6x7.php?v=webapp&channel=238" target="_blank"><img src="https://interq.link/pub/images/backgrounds/trending_banner.png" class="rss_pi_banner_img"></a>
<?php $banner_url =  "https://interq.link/ads/image.php?adloadid=$result&source=rsspi&w=281&h=281"; ?>
<a target="_blank" href="https://interq.link/ads/adclick.php?adloadid=<?php echo $result . '&source=rsspi'; ?>">
    <img class='rss_pi_banner_img' src="<?php echo $banner_url; ?>" />
</a>