document.addEventListener('DOMContentLoaded', function () {

    // Helper functions
    function $(selector, context = document) {
        return context.querySelector(selector);
    }
    function $all(selector, context = document) {
        return Array.from(context.querySelectorAll(selector));
    }
    function on(event, selector, handler) {
        document.body.addEventListener(event, function (e) {
            if (e.target.closest(selector)) {
                handler.call(e.target.closest(selector), e);
            }
        });
    }
    function ajax(options) {
        const xhr = new XMLHttpRequest();
        xhr.open(options.type || 'GET', options.url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                options.success && options.success(xhr.responseText);
            }
        };
        let data = '';
        if (options.data) {
            data = Object.entries(options.data).map(([k, v]) => encodeURIComponent(k) + '=' + encodeURIComponent(v)).join('&');
        }
        xhr.send(data);
    }

    function uniqid() {
        return 'id' + Math.random().toString(36).substr(2, 9);
    }

    function toggleEdit(ths) {
        const target = ths.getAttribute('data-target');
        let modified = $('#modified_feeds').value;
        modified = modified ? modified.split(',') : [target];
        const index = modified.indexOf(target);

        const displayRow = $('#display_' + target);
        const editRow = $('#edit_' + target);

        if (displayRow.classList.contains('show')) {
            if (editRow) editRow.remove();
            displayRow.classList.toggle('show');
            if (index > -1) modified.splice(index, 1);
            return false;
        } else {
            if (index === -1) modified.push(target);
        }
        $('#modified_feeds').value = modified.join(',');

        ajax({
            type: 'POST',
            url: rss_pi.ajaxurl,
            data: {
                action: 'rss_pi_edit_row',
                feed_id: target
            },
            success: function (data) {
                displayRow.insertAdjacentHTML('afterend', data);
                const newEditRow = $('#edit_' + target);
                if (newEditRow) newEditRow.classList.toggle('show');
                displayRow.classList.toggle('show');
            }
        });       
    }

    // Toggle edit
    on('click', 'a.toggle-edit', function (e) {
        e.preventDefault();
        ths = document.getElementById(this.getAttribute('id'));
        toggleEdit(ths);
    });

    on('click', 'a.just_save', function (e) {
        e.preventDefault();
        do_save = true;
        ths = document.getElementById(this.getAttribute('id'));
        console.log(ths);
        toggleEdit(ths);
    });


    // Delete row
    on('click', 'a.delete-row', function (e) {
        e.preventDefault();
        const target = this.getAttribute('data-target');
        let deleted = $('#deleted_feeds').value;
        let modified = $('#modified_feeds').value;
        let new_feeds = $('#new_feeds').value;

        deleted = deleted ? deleted.split(',') : [];
        deleted.push(target);

        if (modified) {
            modified = modified.split(',');
            const idx = modified.indexOf(target);
            if (idx > -1) modified.splice(idx, 1);
            $('#modified_feeds').value = modified.join(',');
        }
        if (new_feeds) {
            new_feeds = new_feeds.split(',');
            const idx = new_feeds.indexOf(target);
            if (idx > -1) new_feeds.splice(idx, 1);
            $('#new_feeds').value = new_feeds.join(',');
        }
        $('#deleted_feeds').value = deleted.join(',');

        const editRow = $('#edit_' + target);
        const displayRow = $('#display_' + target);
        if (editRow) editRow.remove();
        if (displayRow) displayRow.remove();
    });

    // Status row
    on('click', 'a.status-row', function (e) {
        e.preventDefault();
        const action = this.getAttribute('data-action');
        const target = this.getAttribute('data-target');
        if (action === 'pause') {
            this.setAttribute('data-action', 'enable');
            this.innerHTML = 'Enable Feed';
        } else {
            this.setAttribute('data-action', 'pause');
            this.innerHTML = 'Pause';
        }
        let paused_feeds = $('#paused_feeds').value;
        paused_feeds = paused_feeds ? paused_feeds.split(',') : [];
        paused_feeds.push(target);
        $('#paused_feeds').value = paused_feeds.join(',');
    });

    // Feed table change detection
    const feedTable = $('#rss_pi-feed-table');
    if (feedTable) {
        feedTable.addEventListener('change', function (e) {
            const tr = e.target.closest('tr.edit-row');
            if (tr) {
                const id = tr.id.replace('edit_', '');
                const displayTr = $('#display_' + id);
                const editTr = $('#edit_' + id);
                const fields = displayTr.dataset.fields.split(',');
                fields.forEach(function (field) {
                    const fieldSelector = '.field-' + field;
                    const displayField = displayTr.querySelector(fieldSelector);
                    const editField = editTr.querySelector(fieldSelector);
                    if (displayField && editField) {
                        displayField.textContent = editField.value;
                    }
                });
                displayTr.classList.add('rss-pi-unsaved');
            }
        });
        let do_save = false;
        window.addEventListener('beforeunload', function (e) {
            if (!do_save && $all("#rss_pi-feed-table .rss-pi-unsaved").length) {
                e.preventDefault();
                e.returnValue = rss_pi.l18n.unsaved;
                return rss_pi.l18n.unsaved;
            }
        });
        $('#rss_pi-settings-form').addEventListener('submit', function () {
            do_save = true;
        });
    }

    // Add new feed row
    $all('a.add-row').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const target = uniqid();
            ajax({
                type: 'POST',
                url: rss_pi.ajaxurl,
                data: {
                    action: 'rss_pi_add_row',
                    feed_id: target
                },
                success: function (data) {
                    $('.rss-rows').insertAdjacentHTML('beforeend', data);
                    const input = $('#' + target + '-name');
                    if (input) {
                        input.focus();
                        input.select && input.select();
                    }
                }
            });
            let new_feeds = $('#new_feeds').value;
            new_feeds = new_feeds ? new_feeds.split(',') : [];
            new_feeds.push(target);
            $('#new_feeds').value = new_feeds.join(',');
        });
    });

    // Save and import
    const saveAndImport = $('#save_and_import');
    if (saveAndImport) {
        saveAndImport.addEventListener('click', function () {
            $('#save_to_db').value = 'true';
        });
    }

    // Max posts validation
    if (window.Modernizr && Modernizr.input.min && Modernizr.input.max) {
        $all("#rss_pi-settings-form [type='submit']").forEach(function (btn) {
            btn.addEventListener('click', function () {
                $all("[name$='-max_posts']").forEach(function (input) {
                    const val = parseInt(input.value);
                    const min = parseInt(input.getAttribute('min'));
                    const max = parseInt(input.getAttribute('max'));
                    const id = input.id.replace("-max_posts", "");
                    if (val < min || val > max) {
                        $('#edit_' + id).classList.add('show');
                        $('#display_' + id).classList.add('show');
                    }
                });
            });
        });
    }

    document.getElementById('toggle-rsspi-settings-table').addEventListener('click', function() {
        document.getElementById('rsspi-settings-table').classList.toggle('rss_pi_open');
    });


    // Load log
    $all('a.load-log').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            $('#main_ui').style.display = 'none';
            $('.ajax_content').innerHTML = '<img src="/wp-admin/images/wpspin_light.gif" alt="" class="loader" />';
            ajax({
                type: 'POST',
                url: rss_pi.ajaxurl,
                data: { action: 'rss_pi_load_log' },
                success: function (data) {
                    $('.ajax_content').innerHTML = data;
                }
            });
        });
    });

    // Show main UI
    on('click', 'a.show-main-ui', function (e) {
        e.preventDefault();
        $('#main_ui').style.display = '';
        $('.ajax_content').innerHTML = '';
    });

    // Clear log
    on('click', 'a.clear-log', function (e) {
        e.preventDefault();
        ajax({
            type: 'POST',
            url: rss_pi.ajaxurl,
            data: { action: 'rss_pi_clear_log' },
            success: function (data) {
                $('.log').innerHTML = data;
            }
        });
    });

    // Datepicker (requires a vanilla JS datepicker library if needed)
    if ($('#from_date')) $('#from_date').type = 'date';
    if ($('#till_date')) $('#till_date').type = 'date';

    // Stats placeholder AJAX
    if ($('#rss_pi-stats-placeholder')) {
        function rss_filter_stats(form) {
            const data = {
                action: "rss_pi_stats",
                rss_from_date: $('#from_date') ? $('#from_date').value : "",
                rss_till_date: $('#till_date') ? $('#till_date').value : ""
            };
            let loading = false;
            if (form && $('#submit-rss_filter_stats')) {
                data.rss_filter_stats = $('#submit-rss_filter_stats').value;
            } else {
                loading = document.createElement('div');
                loading.className = 'rss_pi_overlay';
                loading.innerHTML = '<img class="rss_pi_loading" src="' + rss_pi.pluginurl + 'app/assets/img/loading.gif" /><p>Stats are loading. Please wait...</p>';
                $('#rss_pi-stats-placeholder').appendChild(loading);
            }
            ajax({
                type: "POST",
                url: rss_pi.ajaxurl,
                data: data,
                success: function (data) {
                    if (loading) loading.remove();
                    $('#rss_pi-stats-placeholder').innerHTML = data;
                    if (typeof drawChart === 'function') drawChart();
                    if ($('#from_date')) $('#from_date').type = 'date';
                    if ($('#till_date')) $('#till_date').type = 'date';
                    const submitBtn = $('#submit-rss_filter_stats');
                    if (submitBtn) {
                        submitBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            rss_filter_stats(true);
                        });
                    }
                }
            });
        }
        rss_filter_stats();
    }

    // Progress bar (requires a vanilla JS progress bar if needed)
    if ($('#rss_pi_progressbar') && typeof feeds !== 'undefined' && feeds.count) {
        function import_feed(id) {
            ajax({
                type: 'POST',
                url: rss_pi.ajaxurl,
                data: {
                    action: 'rss_pi_import',
                    feed: id
                },
                success: function (resp) {
                    let data;
                    try { data = JSON.parse(resp).data || {}; } catch (e) { data = {}; }
                    const progressbar = $('#rss_pi_progressbar');
                    if (progressbar) {
                        progressbar.value = feeds.processed();
                        progressbar.max = feeds.total();
                    }
                    $('#rss_pi_progressbar_label .processed').textContent = feeds.processed();
                    if (data.count !== undefined) feeds.imported(data.count);
                    if (feeds.left()) {
                        $('#rss_pi_progressbar_label .count').textContent = feeds.imported();
                        import_feed(feeds.get());
                    } else {
                        $('#rss_pi_progressbar_label').innerHTML = "Import completed. Imported posts: " + feeds.imported();
                    }
                }
            });
        }
        const progressbar = $('#rss_pi_progressbar');
        if (progressbar) {
            progressbar.value = 0;
            progressbar.max = feeds.total();
        }
        $('#rss_pi_progressbar_label').innerHTML = "Import in progres. Processed feeds: <span class='processed'>0</span> of <span class='max'>" + feeds.total() + "</span>. Imported posts so far: <span class='count'>0</span>";
        import_feed(feeds.get());
    }

    // Custom frequency
    const freqSelect = $('#frequency');
    const customFreq = $('#rss_custom_frequency');
    if (freqSelect && customFreq) {
        freqSelect.addEventListener('change', function () {
            if (this.value === "custom_frequency") {
                customFreq.style.display = 'inline';
                customFreq.focus();
            } else {
                customFreq.value = '';
                customFreq.style.display = 'none';
            }
        });
    }

    // URL version param logic
    const url = location.href;
    const myParam1 = location.search.split('version=')[1];
    if (typeof (myParam1) === 'undefined') {
        let api_add = (typeof new_js_url !== 'undefined' && new_js_url !== '') ? "premium" : "normal";
        window.location.assign(window.location.href + '&version=' + new_js_version + '&type=' + api_add);
    }
});

// Helper for updating feed IDs
function update_ids() {
    const feed_ids = Array.from(document.querySelectorAll("#rss_pi-feed-table > tbody input[name='id']")).map(function (el) {
        return el.value;
    }).join();
    const feed_ids_input = document.getElementById('feed_ids');
    if (feed_ids_input) feed_ids_input.value = feed_ids;
}

// Feeds object (global)
var feeds = {
    ids: typeof feeds !== 'undefined' && feeds.ids ? feeds.ids : [],
    count: typeof feeds !== 'undefined' && feeds.count ? feeds.count : 0,
    imported_posts: 0,
    set: function (ids) {
        this.ids = ids;
        this.count = ids.length;
    },
    get: function () {
        return this.ids.splice(0, 1)[0];
    },
    left: function () {
        return this.ids.length;
    },
    processed: function () {
        return this.count - this.ids.length;
    },
    total: function () {
        return this.count;
    },
    imported: function (num) {
        if (num !== undefined && !isNaN(parseInt(num))) this.imported_posts += parseInt(num);
        return this.imported_posts;
    }
};
