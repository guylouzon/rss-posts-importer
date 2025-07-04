<?php

/**
 * Parses content according to settings
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
class rssPIParser {

    /**
     * Parse content
     * 
     * @global object $rss_post_importer
     * @param object $item Feed item
     * @param string $feed_title Feed title
     * @param string $strip_html whether to strip html tags
     * @return string
     */
    public function _parse($item, string $feed_title, string $strip_html): string {

        global $rss_post_importer;

        // get the saved template
        $post_template = $rss_post_importer->options['settings']['post_template'];

        // get the content
        $c = $item->get_content() != "" ? $item->get_content() : $item->get_description();

        $c = apply_filters('pre_rss_pi_parse_content', $c);

        $c = $this->escape_backreference($c);

        // do all the replacements
        $parsed_content = preg_replace('/\{\$content\}/i', $c, $post_template);
        $parsed_content = preg_replace('/\{\$feed_title\}/i', $feed_title, $parsed_content);
        $parsed_content = preg_replace('/\{\$title\}/i', $item->get_title(), $parsed_content);

        // check if we need an excerpt
        $parsed_content = $this->_excerpt($parsed_content, $c);

        // strip html, if needed
        if ($strip_html === 'true') {
            $parsed_content = strip_tags($parsed_content);
        }

        $parsed_content = preg_replace(
            '/\{\$permalink\}/i',
            '<a href="' . esc_url($item->get_permalink()) . '" target="_blank">' . $item->get_title() . '</a>',
            $parsed_content
        );

        $parsed_content = apply_filters('after_rss_pi_parse_content', $parsed_content);

        return $parsed_content;
    }

    /*
     *
     * 	Escape $n backreferences
     */
    public function escape_backreference(string $x): string {

        return preg_replace('/\$(\d)/', '\\\$$1', $x);
    }

    /**
     * Checks and creates an excerpt
     * 
     * @param string $content Content
     * @param string $c Original content
     * @return string
     */
    private function _excerpt(string $content, string $c): string {

        // if there's an excerpt placeholder
        preg_match('/\{\$excerpt\:(\d+)\}/i', $content, $matches);

        // if there's a wordcount
        $e_size = (is_array($matches) && !empty($matches)) ? (int)$matches[1] : 0;

        // cut it down and replace the placeholder
        if ($e_size) {
            $trimmed_c = preg_replace('/<!--(.|\s)*?-->/', '', $c);
            // compulsorily strip html otherwise there'll be broken html all over
            $stripped_c = strip_tags($trimmed_c);
            $content = preg_replace('/\{\$excerpt\:\d+\}/i', wp_trim_words($stripped_c, $e_size), $content);
        }

        return $content;
    }
}
