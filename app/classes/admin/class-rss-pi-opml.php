<?php

/**
 * This class handles all OPML functionality
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
if (!class_exists("Rss_pi_opml")) {

    class Rss_pi_opml {

        public array $options = [];
        public array $errors = [];

        /*
         * The constructor
         */
        public function __construct() {
            $this->options = get_option('rss_pi_feeds', []);
        }

        /*
         * Exports all feeds, no Settings exported
         */
        public function export(): void {

            if (!empty($this->options['settings']['is_key_valid']) && $this->options['settings']['is_key_valid']) {

                $feeds = $this->options['feeds'];
                $title = get_option('blogname');
                $ownerEmail = get_option('admin_email');

                if (!is_array($feeds) || !count($feeds) || !trim($title) || !$ownerEmail) {
                    return;
                }

                $output = '';

                $output .= $this->_header($title, $ownerEmail);

                foreach ($feeds as $feed) {
                    $output .= $this->_entry($feed['url'], $feed['name']);
                }

                $output .= $this->_footer();

                $filename = "rss_pi_export_" . date("Y-m-d") . ".opml";
                $this->_send_headers($filename);
                echo "\xEF\xBB\xBF";
                print($output);
                die();

            }

        }

        /*
         * Imports feeds from file
         */
        public function import(array $feeds): array {

            if (!empty($this->options['settings']['is_key_valid']) && $this->options['settings']['is_key_valid']) {

                $file = $_FILES['import_opml']['tmp_name'];
                $opml = file_get_contents($file);
                @unlink($file);

                // apply some validation fixes:
                // - & -> &amp;
                $opml = preg_replace('/(&(?!amp;))/', '&amp;', $opml);

                $opmlParser = new OPMLParser($opml);

                $feeds = $this->_parse_data($opmlParser->data, $feeds);
                $this->options['feeds'] = $feeds;

            }

            return $feeds;

        }

        private function _parse_data($data, array $feeds): array {

            if (!is_array($data)) {
                return $feeds;
            }

            foreach ($data as $item) {

                if (isset($item['xmlurl']) && isset($item['text']) && trim($item['xmlurl']) !== '') {

                    if ($this->_feed_url_exists($feeds, $item['xmlurl'])) {
                        $this->errors[] = 'Duplicate Feed url: ' . $item['xmlurl'];
                        continue;
                    }
                    if ($this->_feed_name_exists($feeds, $item['text'])) {
                        $this->errors[] = 'Duplicate Feed name: ' . $item['text'];
                        continue;
                    }

                    $c = count($feeds);
                    $feeds[] = [
                        'id' => uniqid("54d4c" . $c),
                        'url' => $item['xmlurl'],
                        'name' => $item['text'],
                        // default values
                        'max_posts' => 10,
                        'author_id' => get_current_user_id(),
                        'category_id' => [1],
                        'tags_id' => '',
                        'keywords' => '',
                        'strip_html' => 'false',
                        'nofollow_outbound' => 'false',
                        'automatic_import_categories' => 'false',
                        'automatic_import_author' => 'false',
                        'feed_status' => 'active',
                        'canonical_urls' => 'my_blog'
                    ];

                } else {
                    $feeds = $this->_parse_data($item, $feeds);
                }

            }

            return $feeds;

        }

        private function _feed_url_exists(array $feeds, string $url): bool {

            if (!empty($feeds) && !empty($url)) {
                foreach ($feeds as $feed) {
                    if (isset($feed['url']) && $feed['url'] == $url) {
                        return true;
                    }
                }
            }
            return false;
        }

        private function _feed_name_exists(array $feeds, string $name): bool {

            if (!empty($feeds) && !empty($name)) {
                foreach ($feeds as $feed) {
                    if (isset($feed['name']) && $feed['name'] == $name) {
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * basic opml header
         * @param string $opmlTitle
         * @param string $opmlOwnerEmail
         * @return string
         */
        private function _header(string $title, string $ownerEmail): string {
            $result = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
                    . "<opml version=\"1.1\">\n"
                    . "<head>\n"
                    . "      <title>" . htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</title>\n"
                    . "    <dateCreated>" . date("r") . "</dateCreated>\n"
                    . "    <ownerEmail>" . htmlspecialchars($ownerEmail, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</ownerEmail>\n"
                    . "  </head>\n"
                    . "  <body>\n";
            return $result;
        }

        /**
         * just returns a test footer
         * @return string
         */
        private function _footer(): string {
            $result = "  </body>\n"
                    . "</opml>";
            return $result;
        }

        /**
         * creates an XML entry for the OPML file
         * @param string $feedURL
         * @param string $feedTitle
         * @return string
         */
        private function _entry(string $feedURL, string $feedTitle): string {
            $result = "    <outline text=\"" . htmlspecialchars($feedTitle, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "\" type=\"rss\" xmlUrl=\"" . htmlspecialchars($feedURL, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "\"/>\n";
            return $result;
        }

        private function _send_headers(string $filename): void {

            // disable caching
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 01 Jan 2000 01:00:00 GMT"); // a date in the past
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");
            header("Content-type: text/html;charset=utf-8");

        }

    }
    // Class Rss_pi_opml
			header("Content-Type: application/download");
			// disposition / encoding on response body			header("Content-Disposition: attachment;filename={$filename}");			header("Content-Transfer-Encoding: binary");			header("Content-type: text/html;charset=utf-8");		}	}	// CLass Rss_pi_opml}