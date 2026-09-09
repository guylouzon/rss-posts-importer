/**
 * Admin Import Trigger
 */
(function($) {
    'use strict';

    $(function() {
        // Check if our localized data exists
        if (typeof interq_rss_pi_import_data !== 'undefined' && interq_rss_pi_import_data.feed_ids) {
            var feedList = interq_rss_pi_import_data.feed_ids;
            
            if (typeof feeds !== 'undefined' && typeof feeds.set === 'function') {
                feeds.set(feedList);
            } else {
                window.feeds = feedList;
            }
        }
    });
})(jQuery);