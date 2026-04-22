<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<!-- New HTML based version -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSS Post Importer Admin UI</title>
</head>
<body>
    <div class="wrap">
        <div id="main_ui">
            <h2>RSS Post Importer Feeds and Settings</h2>

            <div id="rss_pi_progressbar"></div>
            <div id="rss_pi_progressbar_label"></div>

            <form method="post" id="rss_pi-settings-form" enctype="multipart/form-data" action="<?php echo esc_url($rss_post_importer->page_link); ?>">
                <input type="hidden" name="info_update" id="info_update" value="true">
                <input type="hidden" name="save_to_db" id="save_to_db">
                <input type="hidden" name="import_now" id="import_now" value="false">
                <?php wp_nonce_field('rss_pi_save_settings_action', 'rss_pi_nonce_field'); ?>
                <input type="hidden" id="rss_pi_ajax_nonce" value="<?php echo esc_attr(wp_create_nonce('rss_pi_ajax_nonce_action')); ?>" />

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder">
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="postbox">
                                <div class="inside">
                                    <div class="misc-pub-section">
                                        <h3 class="version">V. 2.8.5</h3>
                                        <ul>
                                            <li><strong>Latest import:</strong> <span id="latest-import">never</span></li>
                                            <li><a href="#" class="load-log">View the log</a></li>
                                        </ul>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <button type="button" class="button button-large button-primary" id="save-all-btn">Save All<span class="unsaved-indicator" style="display:none;">*</span></button>
                                    </div>
                                </div>
                            </div>
                            <div id="rate-box-container"></div>
                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <!-- Feeds Table -->
                            <div class="postbox">
                                <h3>Feeds</h3>
                                <div class="inside">
                                    <div class="table-wrapper" id="rss_pi-feed-table">
                                        <!-- Header Row -->
                                        <div class="table-row header-row">
                                            <div class="table-cell">Feed name</div>
                                            <div class="table-cell">Feed url</div>
                                            <div class="table-cell">Max posts / import</div>
                                        </div>
                                        <!-- Data rows will be inserted here -->
                                        <div id="feeds-tbody"></div>
                                    </div>

                                    <div style="padding: 12px; border-top: 1px solid #eee;">
                                        <button type="button" class="button button-large button-primary" id="add-feed-btn">Add new feed</button>
                                        <input type="hidden" name="deleted_feeds" id="deleted_feeds" value="">
                                        <input type="hidden" name="modified_feeds" id="modified_feeds" value="">
                                        <input type="hidden" name="new_feeds" id="new_feeds" value="">
                                        <input type="hidden" id="paused_feeds" name="paused_feeds" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Table -->
                            <div class="postbox">
                                <button type="button" class="rsspi_settings_control_button button button-primary" id="toggle-rsspi-settings-table">
                                    Settings
                                    <span class="dashicons dashicons-arrow-down settings-table-wrapper" aria-hidden="true">▼</span>
                                </button>
                                <div id="rsspi-settings-table" class="rss_pi_close">
                                    <div class="inside">
                                        <table class="widefat rss_pi-table" id="rss_pi-settings-table">
                                            <tbody class="setting-rows" id="settings-tbody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </form>
        </div>
        <div class="ajax_content"></div>
    </div>

    <script>
        class RSSPostImporter {
            constructor() {
                this.feeds = [];
                this.settings = {
                    frequency: 'hourly',
                    post_template: '{$content}\nSource: {$feed_title}',
                    post_status: 'publish',
                    author_id: 1,
                    allow_comments: 'open',
                    block_indexing: 'false',
                    nofollow_outbound: 'false',
                    enable_logging: 'false',
                    import_images_locally: 'false',
                    disable_thumbnail: 'false',
                    tw_show: '0',
                    og_show: '0'
                };
                this.unsavedChanges = false;
                this.modifiedFeeds = new Set();
                this.newFeeds = new Set();
                this.deletedFeeds = new Set();
                this.nextFeedId = 1;
                this.init();
            }

            init() {
                this.loadSampleData();
                this.setupEventListeners();
                this.renderFeeds();
                this.renderSettings();
            }

            loadSampleData() {
                this.feeds = [
                    {
                        id: 1,
                        name: 'TechCrunch',
                        url: 'https://techcrunch.com/feed/',
                        max_posts: 5,
                        feed_status: 'active',
                        author_id: 1,
                        category_id: [1],
                        tags_id: [],
                        strip_html: 'false',
                        nofollow_outbound: 'false',
                        automatic_import_categories: 'false',
                        automatic_import_author: 'false',
                        canonical_urls: 'my_blog'
                    },
                    {
                        id: 2,
                        name: 'Hacker News',
                        url: 'https://news.ycombinator.com/rss',
                        max_posts: 10,
                        feed_status: 'active',
                        author_id: 1,
                        category_id: [2],
                        tags_id: [],
                        strip_html: 'false',
                        nofollow_outbound: 'true',
                        automatic_import_categories: 'false',
                        automatic_import_author: 'false',
                        canonical_urls: 'source_blog'
                    }
                ];
                this.nextFeedId = 3;
            }

            setupEventListeners() {
                document.getElementById('add-feed-btn').addEventListener('click', () => this.addNewFeed());
                document.getElementById('save-all-btn').addEventListener('click', () => this.saveAll());
                document.getElementById('toggle-rsspi-settings-table').addEventListener('click', () => this.toggleSettingsTable());

                document.addEventListener('click', (e) => {
                    if (e.target.classList.contains('toggle-edit')) {
                        e.preventDefault();
                        const feedId = e.target.getAttribute('data-target');
                        this.toggleEditRow(feedId);
                    }
                    if (e.target.classList.contains('delete-row')) {
                        e.preventDefault();
                        const feedId = e.target.getAttribute('data-target');
                        this.deleteFeed(feedId);
                    }
                    if (e.target.classList.contains('status-row')) {
                        e.preventDefault();
                        const feedId = e.target.getAttribute('data-target');
                        const action = e.target.getAttribute('data-action');
                        this.toggleFeedStatus(feedId, action);
                    }
                });

                document.addEventListener('change', () => this.markUnsavedChanges());
                document.addEventListener('input', () => this.markUnsavedChanges());
            }

            addNewFeed() {
                const newFeed = {
                    id: this.nextFeedId++,
                    name: 'New feed',
                    url: '',
                    max_posts: 5,
                    feed_status: 'active',
                    author_id: 1,
                    category_id: [1],
                    tags_id: [],
                    strip_html: 'false',
                    nofollow_outbound: 'false',
                    automatic_import_categories: 'false',
                    automatic_import_author: 'false',
                    canonical_urls: 'my_blog'
                };
                this.feeds.push(newFeed);
                this.newFeeds.add(newFeed.id);
                this.markUnsavedChanges();
                this.renderFeeds();
                this.toggleEditRow(newFeed.id);
            }

            deleteFeed(feedId) {
                const index = this.feeds.findIndex(f => f.id == feedId);
                if (index > -1) {
                    this.feeds.splice(index, 1);
                    this.deletedFeeds.add(parseInt(feedId));
                    this.newFeeds.delete(parseInt(feedId));
                    this.modifiedFeeds.delete(parseInt(feedId));
                    this.markUnsavedChanges();
                    this.renderFeeds();
                }
            }

            toggleFeedStatus(feedId, action) {
                const feed = this.feeds.find(f => f.id == feedId);
                if (feed) {
                    feed.feed_status = action === 'pause' ? 'pause' : 'active';
                    this.modifiedFeeds.add(parseInt(feedId));
                    this.markUnsavedChanges();
                    this.renderFeeds();
                }
            }

            toggleEditRow(feedId) {
                const displayRow = document.getElementById(`display_${feedId}`);
                const editRow = document.getElementById(`edit_${feedId}`);
                if (displayRow) displayRow.classList.toggle('hidden');
                if (editRow) editRow.classList.toggle('show');
            }

            toggleSettingsTable() {
                const table = document.getElementById('rsspi-settings-table');
                const btn = document.getElementById('toggle-rsspi-settings-table');
                table.classList.toggle('rss_pi_close');
                table.classList.toggle('rss_pi_open');
                btn.querySelector('.settings-table-wrapper').classList.toggle('open');
            }

            markUnsavedChanges() {
                this.unsavedChanges = true;
                const indicator = document.querySelector('.unsaved-indicator');
                if (indicator) indicator.style.display = 'inline';
            }

            collectFormData() {
                const formData = {};

                // Collect settings
                document.querySelectorAll('#settings-tbody input, #settings-tbody select, #settings-tbody textarea').forEach(field => {
                    const name = field.name;
                    if (field.type === 'checkbox') {
                        formData[name] = field.checked ? '1' : '0';
                    } else if (field.type === 'radio') {
                        if (field.checked) {
                            formData[name] = field.value;
                        }
                    } else {
                        formData[name] = field.value;
                    }
                });

                // Collect feeds
                const feedsData = [];
                this.feeds.forEach(feed => {
                    const feedData = { ...feed };
                    feedsData.push(feedData);
                });

                return {
                    settings: formData,
                    feeds: feedsData,
                    deleted_feeds: Array.from(this.deletedFeeds),
                    modified_feeds: Array.from(this.modifiedFeeds),
                    new_feeds: Array.from(this.newFeeds)
                };
            }

            saveAll() {
                const data = this.collectFormData();
                console.log('Saving data to backend:', data);

                // Update hidden inputs
                document.getElementById('deleted_feeds').value = JSON.stringify(data.deleted_feeds);
                document.getElementById('modified_feeds').value = JSON.stringify(data.modified_feeds);
                document.getElementById('new_feeds').value = JSON.stringify(data.new_feeds);

                // Show progress
                const progressBar = document.getElementById('rss_pi_progressbar');
                progressBar.classList.add('active');

                // Simulate backend call
                setTimeout(() => {
                    this.unsavedChanges = false;
                    this.modifiedFeeds.clear();
                    this.newFeeds.clear();
                    this.deletedFeeds.clear();

                    const indicator = document.querySelector('.unsaved-indicator');
                    if (indicator) indicator.style.display = 'none';

                    progressBar.classList.remove('active');
                    alert('All changes saved successfully!');
                }, 1500);
            }

            renderFeeds() {
                const tbody = document.getElementById('feeds-tbody');
                tbody.innerHTML = '';

                if (this.feeds.length === 0) {
                    const emptyRow = document.createElement('div');
                    emptyRow.className = 'empty_table';
                    emptyRow.innerHTML = `You haven't specified any feeds to import yet, why don't you <a href="#" class="add-row">add one now</a>?`;
                    tbody.appendChild(emptyRow);
                    return;
                }

                this.feeds.forEach(feed => {
                    const dataRow = document.createElement('div');
                    dataRow.id = `display_${feed.id}`;
                    dataRow.className = 'data-row show table-row';
                    dataRow.innerHTML = `
                        <div class="table-cell rss_pi-feed_name" data-label="Feed name">
                            <strong>
                                <a href="#" class="toggle-edit" data-target="${feed.id}">
                                    <span class="field-name">${feed.name}</span>
                                </a>
                                <span class="feed-status-badge ${feed.feed_status === 'active' ? 'feed-status-active' : 'feed-status-paused'}">
                                    ${feed.feed_status === 'active' ? 'Active' : 'Paused'}
                                </span>
                            </strong>
                            <div class="row-options">
                                <a href="#" class="toggle-edit" data-target="${feed.id}">Edit</a>
                                <a href="#" class="delete-row" data-target="${feed.id}">Delete</a>
                                ${feed.feed_status === 'active' ? `<a href="#" class="status-row" data-action="pause" data-target="${feed.id}">Pause</a>` : `<a href="#" class="status-row" data-action="enable" data-target="${feed.id}">Enable Feed</a>`}
                            </div>
                        </div>
                        <div class="table-cell rss_pi-feed_url" data-label="Feed url">
                            <span class="field-url">${feed.url || '<em>No URL specified</em>'}</span>
                        </div>
                        <div class="table-cell rss_pi_feed_max_posts" data-label="Max posts / import">
                            <span class="field-max_posts">${feed.max_posts}</span>
                        </div>
                    `;

                    const editRow = document.createElement('div');
                    editRow.id = `edit_${feed.id}`;
                    editRow.className = 'edit-row';
                    def_max_posts = feed.max_posts || 5;
                    editRow.innerHTML = `
                        <div class="edit-table">
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-name">Feed name</label></div>
                                <div class="edit-table-cell"><input type="text" class="field-name" name="${feed.id}-name" id="${feed.id}-name" value="${feed.name}"></div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-url">Feed url</label><p class="description">e.g. "https://interq.link/42/6x7.php?v=rss&channel=238"</p></div>
                                <div class="edit-table-cell"><input type="text" class="field-url" name="${feed.id}-url" id="${feed.id}-url" value="${feed.url}"></div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-max_posts">Max posts / import</label></div>
                                <div class="edit-table-cell"><input type="number" class="field-max_posts" name="${feed.id}-max_posts" id="${feed.id}-max_posts" value="${def_max_posts}" min="1" max="1000"></div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-nofollow_outbound">Nofollow option for all outbound links?</label><p class="description">Add rel="nofollow" to all outbounded links.</p></div>
                                <div class="edit-table-cell">
                                    <ul class="radiolist">
                                        <li><label><input type="radio" name="${feed.id}-nofollow_outbound" value="true" ${feed.nofollow_outbound === 'true' ? 'checked' : ''}>Yes</label></li>
                                        <li><label><input type="radio" name="${feed.id}-nofollow_outbound" value="false" ${feed.nofollow_outbound === 'false' ? 'checked' : ''}>No</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-canonical_urls">SEO canonical URLs?</label></div>
                                <div class="edit-table-cell">
                                    <ul class="radiolist">
                                        <li><label><input type="radio" name="${feed.id}-canonical_urls" value="my_blog" ${feed.canonical_urls === 'my_blog' ? 'checked' : ''}>My Blog URLs</label></li>
                                        <li><label><input type="radio" name="${feed.id}-canonical_urls" value="source_blog" ${feed.canonical_urls === 'source_blog' ? 'checked' : ''}>Source Blog URLs</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-automatic_import_author">Automatic import of Authors?</label></div>
                                <div class="edit-table-cell">
                                    <ul class="radiolist">
                                        <li><label><input type="radio" name="${feed.id}-automatic_import_author" value="true" ${feed.automatic_import_author === 'true' ? 'checked' : ''}>Yes</label></li>
                                        <li><label><input type="radio" name="${feed.id}-automatic_import_author" value="false" ${feed.automatic_import_author === 'false' ? 'checked' : ''}>No</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-automatic_import_categories">Automatic import of Categories?</label></div>
                                <div class="edit-table-cell">
                                    <ul class="radiolist">
                                        <li><label><input type="radio" name="${feed.id}-automatic_import_categories" value="true" ${feed.automatic_import_categories === 'true' ? 'checked' : ''}>Yes</label></li>
                                        <li><label><input type="radio" name="${feed.id}-automatic_import_categories" value="false" ${feed.automatic_import_categories === 'false' ? 'checked' : ''}>No</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><label for="${feed.id}-strip_html">Strip html tags</label></div>
                                <div class="edit-table-cell">
                                    <ul class="radiolist">
                                        <li><label><input type="radio" name="${feed.id}-strip_html" value="true" ${feed.strip_html === 'true' ? 'checked' : ''}>Yes</label></li>
                                        <li><label><input type="radio" name="${feed.id}-strip_html" value="false" ${feed.strip_html === 'false' ? 'checked' : ''}>No</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="edit-table-row">
                                <div class="edit-table-cell"><input type="hidden" name="id" value="${feed.id}"></div>
                                <div class="edit-table-cell"><button type="button" class="button button-large toggle-edit" data-target="${feed.id}">Close</button></div>
                            </div>
                        </div>
                    `;

                    // Track changes for this feed
                    editRow.addEventListener('change', () => {
                        this.modifiedFeeds.add(feed.id);
                        const name = editRow.querySelector(`input[name="${feed.id}-name"]`).value;
                        const url = editRow.querySelector(`input[name="${feed.id}-url"]`).value;
                        const maxPosts = editRow.querySelector(`input[name="${feed.id}-max_posts"]`).value;
                        const nofollow = editRow.querySelector(`input[name="${feed.id}-nofollow_outbound"]:checked`)?.value;
                        const canonical = editRow.querySelector(`input[name="${feed.id}-canonical_urls"]:checked`)?.value;
                        const autoAuthor = editRow.querySelector(`input[name="${feed.id}-automatic_import_author"]:checked`)?.value;
                        const autoCategories = editRow.querySelector(`input[name="${feed.id}-automatic_import_categories"]:checked`)?.value;
                        const stripHtml = editRow.querySelector(`input[name="${feed.id}-strip_html"]:checked`)?.value;

                        feed.name = name;
                        feed.url = url;
                        feed.max_posts = maxPosts;
                        if (nofollow) feed.nofollow_outbound = nofollow;
                        if (canonical) feed.canonical_urls = canonical;
                        if (autoAuthor) feed.automatic_import_author = autoAuthor;
                        if (autoCategories) feed.automatic_import_categories = autoCategories;
                        if (stripHtml) feed.strip_html = stripHtml;

                        this.markUnsavedChanges();
                    });

                    tbody.appendChild(dataRow);
                    tbody.appendChild(editRow);
                });
            }

            renderSettings() {
                const tbody = document.getElementById('settings-tbody');
                tbody.innerHTML = `
                    <tr class="edit-row show">
                        <td colspan="2">
                            <table class="widefat edit-table">
                                <tr>
                                    <td>
                                        <label for="frequency">Frequency</label>
                                        <p class="description">How often will the import run.</p>
                                    </td>
                                    <td>
                                        <select name="frequency" id="frequency">
                                            <option value="hourly" ${this.settings.frequency === 'hourly' ? 'selected' : ''}>Every hour</option>
                                            <option value="twicedaily" ${this.settings.frequency === 'twicedaily' ? 'selected' : ''}>Twice daily</option>
                                            <option value="daily" ${this.settings.frequency === 'daily' ? 'selected' : ''}>Daily</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="post_template">Template</label>
                                        <p class="description">This is how the post will be formatted.</p>
                                        <p class="description">Available tags:
                                            <dl>
                                                <dt><code>{$content}</code></dt>
                                                <dt><code>{$permalink}</code></dt>
                                                <dt><code>{$title}</code></dt>
                                                <dt><code>{$feed_title}</code></dt>
                                                <dt><code>{$excerpt:n}</code></dt>
                                            </dl>
                                        </p>
                                    </td>
                                    <td>
                                        <textarea name="post_template" id="post_template">${this.settings.post_template}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="post_status">Post status</label></td>
                                    <td>
                                        <select name="post_status" id="post_status">
                                            <option value="publish" ${this.settings.post_status === 'publish' ? 'selected' : ''}>Published</option>
                                            <option value="draft" ${this.settings.post_status === 'draft' ? 'selected' : ''}>Draft</option>
                                            <option value="pending" ${this.settings.post_status === 'pending' ? 'selected' : ''}>Pending</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="author_id">Author</label></td>
                                    <td>
                                        <select name="author_id" id="author_id">
                                            <option value="1" ${this.settings.author_id == 1 ? 'selected' : ''}>Admin</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Allow comments</label></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="radio" name="allow_comments" value="open" ${this.settings.allow_comments === 'open' ? 'checked' : ''}>Yes</label></li>
                                            <li><label><input type="radio" name="allow_comments" value="false" ${this.settings.allow_comments === 'false' ? 'checked' : ''}>No</label></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Block search indexing?</label><p class="description">Prevent your content from appearing in search results.</p></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="radio" name="block_indexing" value="true" ${this.settings.block_indexing === 'true' ? 'checked' : ''}>Yes</label></li>
                                            <li><label><input type="radio" name="block_indexing" value="false" ${this.settings.block_indexing === 'false' ? 'checked' : ''}>No</label></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Nofollow option for all outbound links?</label><p class="description">Add rel="nofollow" to all outbounded links.</p></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="radio" name="nofollow_outbound" value="true" ${this.settings.nofollow_outbound === 'true' ? 'checked' : ''}>Yes</label></li>
                                            <li><label><input type="radio" name="nofollow_outbound" value="false" ${this.settings.nofollow_outbound === 'false' ? 'checked' : ''}>No</label></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Enable logging?</label><p class="description">The logfile can be found <a href="#" class="load-log">here</a>.</p></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="radio" name="enable_logging" value="true" ${this.settings.enable_logging === 'true' ? 'checked' : ''}>Yes</label></li>
                                            <li><label><input type="radio" name="enable_logging" value="false" ${this.settings.enable_logging === 'false' ? 'checked' : ''}>No</label></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Download and save images locally?</label><p class="description">Images in the feeds will be downloaded and saved in the WordPress media.</p></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="radio" name="import_images_locally" value="true" ${this.settings.import_images_locally === 'true' ? 'checked' : ''}>Yes</label></li>
                                            <li><label><input type="radio" name="import_images_locally" value="false" ${this.settings.import_images_locally === 'false' ? 'checked' : ''}>No</label></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Disable the featured image?</label><p class="description">Don't set a featured image for the imported posts.</p></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="radio" name="disable_thumbnail" value="true" ${this.settings.disable_thumbnail === 'true' ? 'checked' : ''}>Yes</label></li>
                                            <li><label><input type="radio" name="disable_thumbnail" value="false" ${this.settings.disable_thumbnail === 'false' ? 'checked' : ''}>No</label></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Social Media Optimization and Open Graph</label><p class="description">Social Media and Open Graph optimization</p></td>
                                    <td>
                                        <ul class="radiolist">
                                            <li><label><input type="checkbox" name="tw_show" value="1" ${this.settings.tw_show === '1' ? 'checked' : ''}>Twitter</label></li>
                                            <li><label><input type="checkbox" name="og_show" value="1" ${this.settings.og_show === '1' ? 'checked' : ''}>Facebook OpenGraph</label></li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                `;

                // Track settings changes
                tbody.addEventListener('change', () => {
                    document.querySelectorAll('#settings-tbody select, #settings-tbody input[type="radio"], #settings-tbody input[type="checkbox"], #settings-tbody textarea').forEach(field => {
                        if (field.name === 'frequency') this.settings.frequency = field.value;
                        if (field.name === 'post_template') this.settings.post_template = field.value;
                        if (field.name === 'post_status') this.settings.post_status = field.value;
                        if (field.name === 'author_id') this.settings.author_id = field.value;
                        if (field.name === 'allow_comments' && field.checked) this.settings.allow_comments = field.value;
                        if (field.name === 'block_indexing' && field.checked) this.settings.block_indexing = field.value;
                        if (field.name === 'nofollow_outbound' && field.checked) this.settings.nofollow_outbound = field.value;
                        if (field.name === 'enable_logging' && field.checked) this.settings.enable_logging = field.value;
                        if (field.name === 'import_images_locally' && field.checked) this.settings.import_images_locally = field.value;
                        if (field.name === 'disable_thumbnail' && field.checked) this.settings.disable_thumbnail = field.value;
                        if (field.name === 'tw_show') this.settings.tw_show = field.checked ? '1' : '0';
                        if (field.name === 'og_show') this.settings.og_show = field.checked ? '1' : '0';
                    });
                    this.markUnsavedChanges();
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new RSSPostImporter();
        });
    </script>
</body>
</html>