# InterQ Rss Posts Importer - 2026 updated

![RSS Post Importer Logo](rss-pi-small.png)


I took a simple, old WordPress plugin that imports RSS feeds as posts — and brought it up to date with modern standards.

Use RSS for what it is - a syndication service
Pull data from RSS feeds and display it on your site, as posts


### ✅ What it can do:
- Imports RSS feeds items as wordpress posts
- Controls pulling frequency
- Auto Categorise/ tag any item to your WP predefined category/ tag
- Imports featured images
- Cusomizable content addition
- Saves original link in wp_options


### ✅ What's Changed
- Upgraded the code to support the **latest PHP versions**
- Replaced jQuery with **vanilla JavaScript** where JS upgraded were required
- Ensured full compatibility with the **latest WordPress (6.x)**

🧠 Rewritten **entirely with AI assistance**.

Coming soon - integration with [interQ.link](https://interq.link) ! an RSS generator based on user sharing


Original readme Below

Starting a new changelog !

== Change Log ==

= Version 2025.7.01 = 4/7/2025
 * Upgraded to php 8
 * Changed jQuery to Vanilla js


![interQ.link pixel](https://interq.link/ads/pixel.php)
***
OLD readme starts here
***

=== RSS Post Importer ===
Contributors: feedsapi
Donate link: (outdated)
Tags: rss aggregation, wordpress autoblog aggregator, Autoblogger, rss autopost and syndicator, blog content curation, feedsyndicate, feedwordpress, multiple rss feed importer, rss feeds to post, content syndication, blog migration, yahoo pipes
Requires at least: 3.5
Tested up to: 4.7.4
Stable tag: 2.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

RSS Post Importer is the Most Powerful & Easy to use WordPress RSS Aggregator plugin and does exactly what its name suggests: Import Full RSS Posts


== Description ==


**The RSS Post Importer plugin fetchs an RSS feed and publishes the full article content of each Feed Item as stand-alone post.**

Content syndication allows a blogger to import an rss feed from other blogs in his niche, allowing him to share relevant content with his readers, with far less of a time commitment than writing his own posts or scheduling guest bloggers and ghost writers. RSS Post Importer is very easy to install

**RSS Post Importer** handles all of that, it will import and publish Full Text RSS Posts from one or several RSS feeds sources to your WordPress blog on Auto-pilot while you are laying on the beach of doing some other work, making your blogging, content syndication strategy & autoblogging life much more easier!

Not only does this WordPress RSS Aggregator Plugin import a snippet of the rss feed, it automatically imports the entire **Full Text RSS Feed** , allowing you to post the entire article on your site if you want. This means you're providing your readers with relevant content on a daily, weekly, or even hourly basis, depending on the top of blog you're running. RSS Post Importer is a great plugin for news aggregators, content syndicator, company blogs, current events bloggers, or deal bloggers, as it keeps the fresh content that your readers love you for, coming on a regular basis!


**Features include:**


* Importing feeds automatically using cron.
* Importing the full text rss feeds content.
* Display the full content of the articles.
* Chose to only display the titles of posts.
* Set number of posts and category per feed.
* Set what author to assign imported content to.
* Simple template for formatting imported content.
* Append prefilled HTML code or text to each published Post.
* Append the no-follow tag to all outbond links for **better SEO.**
* Idiot-proof Templating system allowing you to add backlinks and excerpts.
* Block search indexing to prevent your content from appearing in search results.


== Installation ==


1. Upload the files to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Set up what feeds to import and when!


== Change Log ==

= Version 2.5.2 =
 * Bug fixing and improvement.

= Version 2.5.1 =
 * Important security update

= Version 2.5.0 =
 * Security update

= Version 2.4.0 =
 * Several bugs fixing and stability improvement

= Version 2.3.0 =
 * Featured Image Fix
 * Logs Fix

= Version 2.2.4 =
 * German Encoding Bug fix
 * Bug fixing

= Version 2.2.3 =
 * Security Improvement
 * Bug fixing

= Version 2.2.2 =
 * Bug fixing and improvement.
 * Security Improvement

= Version 2.2.1 =
 * Bug fixing and improvement.

= Version 2.2.0 =
 * Small Bugs fixing and Open Graph tweaks

= Version 2.1.9 =
 * Diverse bug fixes.
 * SEO Best Practices implementation.
 * Social Media and Open Graph optimization.

= Version 2.1.8 =
 * New: Pause / Play feed processing and content import from a a specific feed.
 * BUG Fix: Charting time-out for big content DB has been fixed.
 * Various small bugs fixes.

= Version 2.1.7 =
 * Support for Automatic Authors Importing
 * Support for Automatic Categories importing
 * Bug fixes

= Version 2.1.6 =
 * Custom scheduling
 * SEO: Feeds level no-follow
 * Bug fixes

= Version 2.1.5 =
 * Duplicate Post protection issue fixed
 * RegEx filter bug fixed
 * More Bug fixes and Stress-Test

= Version 2.1.4 =
 * Possible duplicate post issue
 * Some encoding and structure issues with OPML Export/Import
 * API related issue for feed URLs with parameters

= Version 2.1.3 =
 * Template item inline_image to insert Featured image inline into post
 * Import/Export Feeds + Feeds' Settings as OPML file

= Version 2.1.2 =
 * Interactive Feedback when adding/editing new feeds
 * Disable Banner for Premium users
 * Option to purge deleted posts from the memory, so you can re-import deleted posts.
 * Import/Export Feeds + Feeds' Settings as CSV file
 * Improved Charts and Statistics
 * Option to Import already imported or deleted Posts
 * Several code improvements & Bug fixes for better stability

= Version 2.1.1 =
 * Jquery Bug fix

= Version 2.1.0 =
 * Ajax implementation
 * Feeds Level Keywords Filters
 * Feeds Level RegEx Filters
 * Insanely faster
 * Improvement of the Charts
 * Disable Duplicated Featured Images for specific templates

= Version 2.0.17 =
 * Bug Fixing: Encoding Bugs for Arabic, Russian, German and other languages and special characters fixed

= Version 2.0.16 =
* Added advanced stats

= Version 2.0.15 =
* Duplicate post issue
* Warning messages
* Encoding issue

= Version 2.0.14 =
* Added option to import/export settings

= Version 2.0.13 =
* Bug Fixing - API related

= Version 2.0.12 =
* Added export/import option to backup/restore feeds and settings.

= Version 2.0.11 =
* Changed API URL

= Version 2.0.10 =
* Added option to download images locally instead of hotlinking.

= Version 2.0.9 =
* Bug fixed and Improvements in code.

= Version 2.0.8 =
* default category always selected issue resolved.
* {excerpt:n} not working - Fixed.

= Version 2.0.7 =
* Bug fixed and Improvements in code.

= Version 2.0.6 =
* nofollow option for outbound links.
* Bug fixed($ sign not removing from content when feed importing)
* Bug fixed and Improvements in code.

= Version 2.0.5 =
* Broken links to images solved.
* Added cron option for 10 minutes.
* Bug fixed and Improvements in code.

= Version 2.0.4 =
* Added Single tag(Without API key) and Multiple tags(With API key) assigned to imported posts

= Version 2.0.3 =
* show Category removed from feeds section to prevent UI breaking
* Comments closed issue solved.

= Version 2.0.2 =
* Block search indexing
* Multiple category selection for premium users

= Version 2.0.1 =
* Fixed cron error
* Fixed permalink placeholder truncation

= Version 2.0 =
* Re-factored and rewritten almost all code
* Fixed featured image and shortcode placeholders
* Added support for keyword/regex based filtering
* Added support for per feed author
* Update readme

= Version 1.0.10 =
* Fixed replacement shortcodes

= Version 1.0.9 =
* Added support for excerpts
* Fixed some warnings and notices

= Version 1.0.6 =
* Removed a bug causing save to reset and trigger a new cron.

= Version 1.0.5 =
* Minor improvements.

= Version 1.0.4 =
* Fixed bug that kept cron from running correctly.

= Version 1.0.3 =
* Made the log available through UI instead of just over ftp.
* Design improvements.

= Version 1.0.2 =
* Fixed bug that caused posts to be duplicated when post status was set to anything but 'Publish'.
* Added possibility to log each time imports are made in a textfile (for debugging purposes).

= Version 1.0.1 =
* Fixed some localization issues.

