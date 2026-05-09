<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!isset($ajax_add)) {
    $ajax_add = false;
}
if (!isset($ajax_edit)) {
    $ajax_edit = false;
}

$show = '';
if (!isset($f)) {
    $f = [
        'id' => $ajax_feed_id ?? 0,
        'name' => 'New feed',
        'url' => '',
        'max_posts' => 5,
        'author_id' => 1,
        'category_id' => 1,
        'tags_id' => [],
        'strip_html' => 'false',
        'nofollow_outbound' => 'false',
        'automatic_import_categories' => 'false',
        'automatic_import_author' => 'false',
        'canonical_urls' => 'my_blog'
    ];
    $show = ' show';
}

$tag = '';
$tagarray = [];
if (is_array($f['tags_id'])) {
    if (!empty($f['tags_id'])) {
        foreach ($f['tags_id'] as $tag_id) {
            $tagname = get_tag($tag_id);
            if ($tagname && isset($tagname->name)) {
                $tagarray[] = $tagname->name;
            }
        }
        $tag = join(',', $tagarray);
    } else {
        $tag = '';
    }
} else {
    if (empty($f['tags_id'])) {
        $f['tags_id'] = [];
        $tag = '';
    } else {
        $f['tags_id'] = [$f['tags_id']];
        $tagname = get_tag(intval($f['tags_id'][0]));
        $tag = $tagname && isset($tagname->name) ? $tagname->name : '';
    }
}

$category = '';
$catarray = [];
if (is_array($f['category_id'])) {
    foreach ($f['category_id'] as $cat) {
        $catarray[] = get_cat_name($cat);
    }
    $category = join(',', $catarray);
} else {
    if (empty($f['category_id'])) {
        $f['category_id'] = [1];
        $category = get_the_category_by_ID(1);
    } else {
        $f['category_id'] = [$f['category_id']];
        $category = get_the_category_by_ID(intval($f['category_id'][0]));
    }
}

?>

<?php
if ($ajax_add || !$ajax_edit):
?>
<tr id="display_<?php echo esc_html( $f['id'] ); ?>" class="data-row<?php echo esc_html($show); ?>" data-fields="name,url,max_posts">
    <td class="rss_pi-feed_name">
        <strong><a href="#" class="edit_<?php echo esc_html( $f['id'] ); ?> toggle-edit" data-target="<?php echo esc_html( $f['id'] ); ?>"><span class="field-name"><?php echo esc_html(stripslashes($f['name'])); ?></span></a></strong>
        <div class="row-options">
            <?php
            if (isset($f['feed_status'])): ?>
            <a href="#" id="edit_btn_<?php echo esc_html( $f['id'] ); ?>" class="edit_<?php echo esc_html( $f['id'] ); ?> toggle-edit" data-target="<?php echo esc_html( $f['id'] ); ?>"><?php esc_html_e('Edit', 'interq-rss-pi'); ?></a> |
            <?php
            endif;
            ?>
            <a href="#" class="delete-row" data-target="<?php echo esc_html( $f['id'] ); ?>"><?php esc_html_e('Delete', 'interq-rss-pi'); ?></a>
            <?php
            if (isset($f['feed_status']) && $f['feed_status'] == "active") { ?>
            | <a href="#" class="status-row" data-action="pause" data-target="<?php echo esc_html( $f['id'] ); ?>"><?php esc_html_e('Pause', 'interq-rss-pi'); ?></a>
            <?php } elseif (isset($f['feed_status']) && $f['feed_status'] == "pause") { ?>
            | <a href="#" class="status-row" data-action="enable" data-target="<?php echo esc_html( $f['id'] ); ?>"><?php esc_html_e('Enable Feed', 'interq-rss-pi'); ?></a>
            <?php } ?>
        </div>
    </td>
    <td class="rss_pi-feed_url"><span class="field-url"><?php echo esc_url(stripslashes($f['url'])); ?></span></td>
    <td class="rss_pi_feed_max_posts"><span class="field-max_posts"><?php echo esc_html($f['max_posts']); ?></span></td>
   <!-- <td width="20%"><?php //echo $category;  ?></td>-->
</tr>
<?php
endif;
?>

<?php
if ($ajax_add || $ajax_edit):
?>
<tr id="edit_<?php echo esc_html( $f['id'] ); ?>" class="edit-row<?php echo esc_html($show); ?>">
    <td colspan="4">
        <table class="widefat edit-table">
            <tr>
                <td><label for="<?php echo esc_html( $f['id'] ); ?>-name"><?php esc_html_e("Feed name", 'interq-rss-pi'); ?></label></td>
                <td>
                    <input type="text" class="field-name" name="<?php echo esc_html( $f['id'] ); ?>-name" id="<?php echo esc_html( $f['id'] ); ?>-name" value="<?php echo esc_attr(stripslashes($f['name'])); ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo esc_html( $f['id'] ); ?>-url"><?php esc_html_e("Feed url", 'interq-rss-pi'); ?></label>
                    <p class="description">e.g. "https://interq.link/42/6x7.php?v=rss&channel=238"</p>
                </td>
                <td><input type="text" class="field-url" name="<?php echo esc_html( $f['id'] ); ?>-url" id="<?php echo esc_html( $f['id'] ); ?>-url" value="<?php echo esc_attr(stripslashes($f['url'])); ?>" /></td>
            </tr>
            <tr>
                <td><label for="<?php echo esc_html( $f['id'] ); ?>-max_posts"><?php esc_html_e("Max posts / import", 'interq-rss-pi'); ?></label></td>
                <td><input type="number" class="field-max_posts" name="<?php echo esc_html( $f['id'] ); ?>-max_posts" id="<?php echo esc_html( $f['id'] ); ?>-max_posts" value="<?php echo esc_html($f['max_posts']); ?>" min="1" max="1000" /></td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo esc_html( $f['id'] ); ?>-nofollow_outbound"><?php esc_html_e('Nofollow option for all outbound links?', "interq-rss-pi"); ?></label>
                    <p class="description"><?php esc_html_e('Add rel="nofollow" to all outbounded links.', "interq-rss-pi"); ?></p>
                </td>
                <td>
                    <ul class="radiolist">
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-nofollow_outbound_true" name="<?php echo esc_html( $f['id'] ); ?>-nofollow_outbound" value="true" <?php echo($f['nofollow_outbound'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('Yes', 'interq-rss-pi'); ?></label>
                        </li>
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-nofollow_outbound_false" name="<?php echo esc_html( $f['id'] ); ?>-nofollow_outbound" value="false" <?php echo($f['nofollow_outbound'] == 'false' || $f['nofollow_outbound'] == '' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('No', 'interq-rss-pi'); ?></label>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo esc_html( $f['id'] ); ?>-canonical_urls"><?php esc_html_e('SEO canonical URLs', "interq-rss-pi"); ?></label>
                </td>
                <td>
                    <ul class="radiolist">
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-canonical_urls_myblog" name="<?php echo esc_html( $f['id'] ); ?>-canonical_urls" value="my_blog" <?php echo($f['canonical_urls'] == 'my_blog' || $f['canonical_urls'] == '' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('My Blog URLs', 'interq-rss-pi'); ?></label>
                        </li>
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-canonical_urls_sourceblog" name="<?php echo esc_html( $f['id'] ); ?>-canonical_urls" value="source_blog" <?php echo($f['canonical_urls'] == 'source_blog' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('Source Blog URLs', 'interq-rss-pi'); ?></label>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo esc_html( $f['id'] ); ?>-automatic_import_author"><?php esc_html_e('Automatic import of Authors', "interq-rss-pi"); ?></label>
                </td>
                <td>
                    <ul class="radiolist">
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-automatic_import_author_true" name="<?php echo esc_html( $f['id'] ); ?>-automatic_import_author" value="true" <?php echo($f['automatic_import_author'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('Yes', 'interq-rss-pi'); ?></label>
                        </li>
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-automatic_import_author_false" name="<?php echo esc_html( $f['id'] ); ?>-automatic_import_author" value="false" <?php echo($f['automatic_import_author'] == 'false' || $f['automatic_import_author'] == '' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('No', 'interq-rss-pi'); ?></label>
                        </li>
                    </ul>
                </td>
            </tr>
  
            <tr>
                <td>
                    <label for="<?php echo esc_html( $f['id'] ); ?>-automatic_import_categories"><?php esc_html_e('Automatic import of Categories', "interq-rss-pi"); ?></label>
                </td>
                <td>
                    <ul class="radiolist">
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-automatic_import_categories_true" name="<?php echo esc_html( $f['id'] ); ?>-automatic_import_categories" value="true" <?php echo($f['automatic_import_categories'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('Yes', 'interq-rss-pi'); ?></label>
                        </li>
                        <li>
                            <label><input type="radio" id="<?php echo esc_html( $f['id'] ); ?>-automatic_import_categories_false" name="<?php echo esc_html( $f['id'] ); ?>-automatic_import_categories" value="false" <?php echo($f['automatic_import_categories'] == 'false' || $f['automatic_import_categories'] == '' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('No', 'interq-rss-pi'); ?></label>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td><label for=""><?php esc_html_e("Category", 'interq-rss-pi'); ?></label></td>
                <td>
                    <?php
                    $rss_post_pi_admin = new rssPIAdmin();
                    ?>
                        <div class="rpi-category-container">
                            <ul class="category_container">
                                <?php
                                // 1. Generate the checklist HTML
                                $allcats = $rss_post_pi_admin->wp_category_checklist_rss_pi( 0, false, $f['category_id'] );

                                // 2. Replace the name attribute for custom form handling
                                // Use esc_attr to ensure the ID attribute is safe for the HTML string
                                $custom_name = esc_attr( $f['id'] ) . '-category_id[]';
                                $allcats     = str_replace( 'name="post_category[]"', 'name="' . $custom_name . '"', $allcats );

                                /**
                                 * SAFETY CHECK: 
                                 * Since $allcats contains complex HTML generated by WP (checkboxes/labels), 
                                 * we cannot use esc_html() or it will break the UI.
                                 * Instead, we use wp_kses() or ensure the source function is trusted.
                                 */
                                echo $allcats; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                ?>
                            </ul>
                        </div>
                </td>
            </tr>
            <tr>
                <td><label for=""><?php esc_html_e("Tags", 'interq-rss-pi'); ?></label></td>
                <td>
                    <div class="tags_container">
                        <?php
                        echo wp_kses_post($rss_post_pi_admin->rss_pi_tags_checkboxes($f['id'], $f['tags_id']));
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for=""><?php esc_html_e("Strip html tags", 'interq-rss-pi'); ?></label></td>
                <td>
                    <ul class="radiolist">
                        <li>
                            <label><input type="radio" id="<?php echo esc_attr( $f['id'] ); ?>-strip_html" name="<?php echo esc_attr( $f['id'] ); ?>-strip_html" value="true" <?php echo($f['strip_html'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('Yes', 'interq-rss-pi'); ?></label>
                        </li>
                        <li>
                            <label><input type="radio" id="<?php echo esc_attr( $f['id'] ); ?>-strip_html" name="<?php echo esc_attr( $f['id'] ); ?>-strip_html" value="false" <?php echo($f['strip_html'] == 'false' ? 'checked="checked"' : ''); ?> /> <?php esc_html_e('No', 'interq-rss-pi'); ?></label>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td><input type="hidden" name="id" value="<?php echo esc_html( $f['id'] ); ?>" /></td>
            </tr>
        </table>
    </td>
</tr>
<?php
endif;
?>
