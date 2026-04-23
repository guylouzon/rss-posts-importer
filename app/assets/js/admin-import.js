/**
 * Admin Import Trigger
 */
(function($) {
    'use strict';

    $(function() {
        // Check if our localized data exists
        if (typeof rss_pi_import_data !== 'undefined' && rss_pi_import_data.feed_ids) {
            var feedList = rss_pi_import_data.feed_ids;
            
            if (typeof feeds !== 'undefined' && typeof feeds.set === 'function') {
                feeds.set(feedList);
            } else {
                window.feeds = feedList;
            }
        }
    });
})(jQuery);