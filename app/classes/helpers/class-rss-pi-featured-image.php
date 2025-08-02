<?php

/**
 * Sets a featured image
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
if (!function_exists('download_url')) {
    require_once(ABSPATH . '/wp-admin/includes/file.php');
}

if (!function_exists('media_handle_sideload')) {
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
}

class rssPIFeaturedImage {

    /**
     * Prepare featured image
     * 
     * @param object $item Feed item
     * @param int $post_id Post id
     * @return int|false
     */
    public function _prepare($item, int $post_id): int|false {
        try {
            // Validate input parameters
            if (!$item || !is_object($item)) {
                error_log("RSS PI Featured Image: Invalid item object provided");
                return false;
            }
            
            if ($post_id <= 0) {
                error_log("RSS PI Featured Image: Invalid post ID provided: {$post_id}");
                return false;
            }
            
            // Get content with fallback to description
            $content = '';
            try {
                $content = method_exists($item, 'get_content') && $item->get_content() ? 
                        $item->get_content() : 
                        (method_exists($item, 'get_description') ? $item->get_description() : '');
            } catch (Exception $e) {
                error_log("RSS PI Featured Image: Error getting item content: " . $e->getMessage());
                return false;
            }
            
            if (empty($content) || !is_string($content)) {
                error_log("RSS PI Featured Image: No content available from feed item");
                return false;
            }
            
            // Extract base reference URL with error handling
            $baseref = '';
            if (preg_match('/href="([^"]+)"/i', $content, $matches)) {
                $baseref = isset($matches[1]) && is_string($matches[1]) ? trim($matches[1]) : '';
            }
            
            // Extract image URL with multiple fallback patterns
            $img_url = $this->extract_image_url($content);
            
            if (empty($img_url)) {
                error_log("RSS PI Featured Image: No image found in content for post ID: {$post_id}");
                return false;
            }
            
            // Validate and construct absolute URL
            $absolute_img_url = $this->make_absolute_url($img_url, $baseref);
            
            if (!$absolute_img_url) {
                error_log("RSS PI Featured Image: Could not construct absolute URL from: {$img_url}");
                return false;
            }
            
            // Validate the final URL
            if (!$this->is_valid_image_url($absolute_img_url)) {
                error_log("RSS PI Featured Image: Invalid image URL: {$absolute_img_url}");
                return false;
            }
            
            // Attempt to sideload the image with error handling
            $featured_id = $this->_sideload($absolute_img_url, $post_id);
            
            if ($featured_id === false) {
                error_log("RSS PI Featured Image: Failed to sideload image: {$absolute_img_url}");
                return false;
            }
            
            error_log("RSS PI Featured Image: Successfully prepared image with ID: {$featured_id}");
            return $featured_id;
            
        } catch (Exception $e) {
            error_log("RSS PI Featured Image: Unexpected error in _prepare: " . $e->getMessage());
            return false;
        } catch (Error $e) {
            error_log("RSS PI Featured Image: Fatal error in _prepare: " . $e->getMessage());
            return false;
        }
    }

    private function make_absolute_url(string $img_url, string $baseref): string|false {
        // If already absolute URL, validate and return
        if (filter_var($img_url, FILTER_VALIDATE_URL)) {
            return $img_url;
        }
        
        // Try to parse the image URL to see if it has a host
        $img_parsed = parse_url($img_url);
        if ($img_parsed === false) {
            error_log("RSS PI Featured Image: Could not parse image URL: {$img_url}");
            return false;
        }
        
        // If image URL has host, it's absolute
        if (!empty($img_parsed['host'])) {
            return $img_url;
        }
        
        // Need base reference for relative URLs
        if (empty($baseref)) {
            error_log("RSS PI Featured Image: No base reference for relative URL: {$img_url}");
            return false;
        }
        
        // Parse base reference
        $base_parsed = parse_url($baseref);
        if ($base_parsed === false || empty($base_parsed['host'])) {
            error_log("RSS PI Featured Image: Invalid base reference URL: {$baseref}");
            return false;
        }
        
        // Construct absolute URL
        $scheme = $base_parsed['scheme'] ?? 'http';
        $host = $base_parsed['host'];
        $port = !empty($base_parsed['port']) ? ':' . $base_parsed['port'] : '';
        
        // Handle different types of relative URLs
        if (strpos($img_url, '/') === 0) {
            // Absolute path (starts with /)
            $absolute_url = $scheme . '://' . $host . $port . $img_url;
        } else {
            // Relative path
            $base_path = $base_parsed['path'] ?? '/';
            $base_dir = dirname($base_path);
            if ($base_dir === '.') {
                $base_dir = '/';
            }
            $absolute_url = $scheme . '://' . $host . $port . rtrim($base_dir, '/') . '/' . ltrim($img_url, '/');
        }
        
        return $absolute_url;
    }


    private function extract_image_url(string $content): string|false {
        // Multiple regex patterns to catch different image formats
        $patterns = [
            // Standard img tag with src
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
            // Img tag with spaces around equals
            '/<img[^>]+src\s*=\s*["\']([^"\']+)["\'][^>]*>/i',
            // Self-closing img tag
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*\/>/i',
            // Img tag without quotes (less common but possible)
            '/<img[^>]+src=([^\s>]+)[^>]*>/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                if (isset($matches[1]) && !empty(trim($matches[1]))) {
                    return trim($matches[1]);
                }
            }
        }
        
        return false;
    }

    /**
     * Sets featured image
     * 
     * @param object $item Feed item
     * @param int $post_id Post id
     * @return int
     */
    public function _set($item, int $post_id): int {

        $featured_id = $this->_prepare($item, $post_id);

        if (!is_wp_error($featured_id) && $featured_id) {
            do_action('set_rss_pi_featured_image', $featured_id, $post_id);
            // set as featured image
            $meta_id = set_post_thumbnail($post_id, $featured_id);
        } else {
            $meta_id = 0;
        }

        return $meta_id;
    }

    /**
     *  Modification of default media_sideload_image
     * 
     * @param string $file
     * @param int $post_id
     * @param string|null $desc
     * @return int|\WP_Error
     */
    private function _sideload(string $file, int $post_id, ?string $desc = null): int|\WP_Error {

        $id = 0;

        if (!empty($file)) {
            // Set variables for storage, fix file filename for query strings.
            preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $file, $matches);
            $file_array = [];
            $file_array['name'] = basename($file);

            // Download file to temp location.
            $file_array['tmp_name'] = @download_url($file);

            // If error storing temporarily, return the error.
            if (is_wp_error($file_array['tmp_name'])) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.
            $id = media_handle_sideload($file_array, $post_id, $desc);

            // If error storing permanently, unlink.
            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                return $id;
            }
        }

        return $id;
    }

}
