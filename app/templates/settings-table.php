<button type="button" class="rsspi_settings_control_button button button-primary" id="toggle-rsspi-settings-table">
    Settings 
    <span class="dashicons dashicons-arrow-down settings-table-wrapper" aria-hidden="true"></span>
</button>
<div id="rsspi-settings-table" class="rss_pi_close">
	<table class="widefat rss_pi-table" id="rss_pi-settings-table">
		<thead>
		</thead>
		<tbody class="setting-rows">
			<tr class="edit-row show">
				<td colspan="4">
					<table class="widefat edit-table">
						<tr>
							<td>
								<label for="frequency"><?php _e('Frequency', "rss-post-importer"); ?></label>
								<p class="description"><?php _e('How often will the import run.', "rss-post-importer"); ?></p>
								<p class="description"><?php _e('Custom Frequency in minutes only.', "rss-post-importer"); ?></p>
							</td>
							<td>
							<?php
								$schedules = wp_get_schedules();
								$custom_cron_options = get_option('rss_custom_cron_frequency', []);
								if (!empty($custom_cron_options) && is_string($custom_cron_options)) {
									$rss_custom_cron = @unserialize($custom_cron_options);
									if (!is_array($rss_custom_cron)) {
										$rss_custom_cron = [];
									}
								} else {
									$rss_custom_cron = [];
								}
							?>
								<select name="frequency" id="frequency">
									<?php
										foreach (array_keys($schedules) as $interval) :
										if (empty($rss_custom_cron) || ($rss_custom_cron['frequency'] ?? '') != $interval) :
									?>
											<option value="<?php echo $interval; ?>"
													<?php if ($this->options['settings']['frequency'] == $interval) echo('selected="selected"'); ?>
											><?php echo $schedules[$interval]['display']; ?></option>
									<?php
										endif;
										endforeach;
									?>

									<option value="custom_frequency"
											<?php if (isset($this->options['settings']['custom_frequency']) && $this->options['settings']['custom_frequency'] == "true") echo('selected="selected"'); ?>
									><?php _e('Custom frequency', "rss-post-importer"); ?></option>
								</select>
								&nbsp;

								<input type="text" id="rss_custom_frequency" name="rss_custom_frequency" value="<?php echo(isset($rss_custom_cron['time']) ? $rss_custom_cron['time'] : ''); ?>" placeholder="Minutes"
									<?php
											if (isset($this->options['settings']['custom_frequency']) && $this->options['settings']['custom_frequency'] == 'true') {
												echo('style="display:inline"');
											} else {
												echo('style="display:none"');
											}
										?>
								/>


							</td>
						</tr>
						<tr>
							<td>
								<label for="post_template"><?php _e('Template', 'rss-post-importer'); ?></label>
								<p class="description"><?php _e('This is how the post will be formatted.', "rss-post-importer"); ?></p>
								<p class="description">
									<?php _e('Available tags:', "rss-post-importer"); ?>
								<dl>
									<dt><code>&lcub;$content&rcub;</code></dt>
									<dt><code>&lcub;$permalink&rcub;</code></dt>
									<dt><code>&lcub;$title&rcub;</code></dt>
									<dt><code>&lcub;$feed_title&rcub;</code></dt>
									<dt><code>&lcub;$excerpt:n&rcub;</code></dt>
									<dt><code>&lcub;$inline_image&rcub;</code> <small>insert the featured image inline into the post content</small></dt>
								</dl>
								</p>
							</td>
							<td>
								<textarea name="post_template" id="post_template" cols="30" rows="10"><?php
									$value = (
											$this->options['settings']['post_template'] != '' ? $this->options['settings']['post_template'] : '{$content}' . "\nSource: " . '{$feed_title}'
											);

									$value = str_replace(array('\r', '\n'), array(chr(13), chr(10)), $value);

									echo esc_textarea(stripslashes($value));
									?></textarea>
							</td>
						</tr>

						<tr>
							<td><label for="post_status"><?php _e('Post status', "rss-post-importer"); ?></label></td>
							<td>

								<select name="post_status" id="post_status">
									<?php
									$statuses = get_post_stati('', 'objects');

									foreach ($statuses as $status) {
										?>
										<option value="<?php echo($status->name); ?>" <?php
										if ($this->options['settings']['post_status'] == $status->name) : echo('selected="selected"');
										endif;
										?>><?php echo($status->label); ?></option>
												<?php
											}
											?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php _e('Author', 'rss-post-importer'); ?></td>
							<td>
								<?php
								$args = array(
									'id' => 'author_id',
									'name' => 'author_id',
									'selected' => $this->options['settings']['author_id']
								);
								wp_dropdown_users($args);
								?>
							</td>
						</tr>
						<tr>
							<td><?php _e('Allow comments', "rss-post-importer"); ?></td>
							<td>
								<ul class="radiolist">
									<li>
										<label><input type="radio" id="allow_comments_open" name="allow_comments" value="open" <?php echo($this->options['settings']['allow_comments'] == 'open' ? 'checked="checked"' : ''); ?> /> <?php _e('Yes', 'rss-post-importer'); ?></label>
									</li>
									<li>
										<label><input type="radio" id="allow_comments_false" name="allow_comments" value="false" <?php echo($this->options['settings']['allow_comments'] == 'false' ? 'checked="checked"' : ''); ?> /> <?php _e('No', 'rss-post-importer'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Block search indexing?', "rss-post-importer"); ?>
								<p class="description"><?php _e('Prevent your content from appearing in search results.', "rss-post-importer"); ?></p>
							</td>
							<td>
								<ul class="radiolist">
									<li>
										<label><input type="radio" id="block_indexing_true" name="block_indexing" value="true" <?php echo($this->options['settings']['block_indexing'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php _e('Yes', 'rss-post-importer'); ?></label>
									</li>
									<li>
										<label><input type="radio" id="block_indexing_false" name="block_indexing" value="false" <?php echo($this->options['settings']['block_indexing'] == 'false' || $this->options['settings']['block_indexing'] == '' ? 'checked="checked"' : ''); ?> /> <?php _e('No', 'rss-post-importer'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Nofollow option for all outbound links?', "rss-post-importer"); ?>
								<p class="description"><?php _e('Add rel="nofollow" to all outbounded links.', "rss-post-importer"); ?></p>
							</td>
							<td>
								<ul class="radiolist">
									<li>
										<label><input type="radio" id="nofollow_outbound_true" name="nofollow_outbound" value="true" <?php echo($this->options['settings']['nofollow_outbound'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php _e('Yes', 'rss-post-importer'); ?></label>
									</li>
									<li>
										<label><input type="radio" id="nofollow_outbound_false" name="nofollow_outbound" value="false" <?php echo($this->options['settings']['nofollow_outbound'] == 'false' || $this->options['settings']['nofollow_outbound'] == '' ? 'checked="checked"' : ''); ?> /> <?php _e('No', 'rss-post-importer'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Enable logging?', "rss-post-importer"); ?>
								<p class="description"><?php _e('The logfile can be found <a href="#" class="load-log">here</a>.', "rss-post-importer"); ?></p>
							</td>
							<td>
								<ul class="radiolist">
									<li>
										<label><input type="radio" id="enable_logging_true" name="enable_logging" value="true" <?php echo($this->options['settings']['enable_logging'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php _e('Yes', 'rss-post-importer'); ?></label>
									</li>
									<li>
										<label><input type="radio" id="enable_logging_false" name="enable_logging" value="false" <?php echo($this->options['settings']['enable_logging'] == 'false' || $this->options['settings']['enable_logging'] == '' ? 'checked="checked"' : ''); ?> /> <?php _e('No', 'rss-post-importer'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Download and save images locally?', "rss-post-importer"); ?>
								<p class="description"><?php _e('Images in the feeds will be downloaded and saved in the WordPress media.', "rss-post-importer"); ?></p>
							</td>
							<td>
								<ul class="radiolist">
									<li>
										<label><input type="radio" id="import_images_locally_true" name="import_images_locally" value="true" <?php echo($this->options['settings']['import_images_locally'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php _e('Yes', 'rss-post-importer'); ?></label>
									</li>
									<li>
										<label><input type="radio" id="import_images_locally_false" name="import_images_locally" value="false" <?php echo($this->options['settings']['import_images_locally'] == 'false' || $this->options['settings']['enable_logging'] == '' ? 'checked="checked"' : ''); ?> /> <?php _e('No', 'rss-post-importer'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Disable the featured image?', "rss-post-importer"); ?>
								<p class="description"><?php _e('Don\'t set a featured image for the imported posts.', "rss-post-importer"); ?></p>
							</td>
							<td>
								<ul class="radiolist">
									<li>
										<label><input type="radio" id="disable_thumbnail_true" name="disable_thumbnail" value="true" <?php echo($this->options['settings']['disable_thumbnail'] == 'true' ? 'checked="checked"' : ''); ?> /> <?php _e('Yes', 'rss_pi'); ?></label>
									</li>
									<li>
										<label><input type="radio" id="disable_thumbnail_false" name="disable_thumbnail" value="false" <?php echo($this->options['settings']['disable_thumbnail'] == 'false' || $this->options['settings']['disable_thumbnail'] == '' ? 'checked="checked"' : ''); ?> /> <?php _e('No', 'rss-post-importer'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Social Media Optimization and Open Graph', "rss-post-importer"); ?>
								<p class="description"><?php _e('Social Media and Open Graph optimization', "rss-post-importer"); ?></p>
							</td>
							<td>
							<ul class="radiolist">
									<li>
										<label>
											<input type="checkbox" name="tw_show" id="tw_show" value="1"
												<?php echo(isset($this->options['settings']['tw_show']) && $this->options['settings']['tw_show'] == '1' ? '' : 'checked="checked"'); ?>
											/>
											<?php _e('X', 'rss-post-importer'); ?>
										</label>
									</li>
									<li>
										<label>
											<input type="checkbox" name="og_show" id="og_show" value="1"
												<?php echo(isset($this->options['settings']['og_show']) && $this->options['settings']['og_show'] == '1' ? '' : 'checked="checked"'); ?>
											/>
											<?php _e('Facebook Opengraph', 'rss-post-importer'); ?>
										</label>
									</li>
								</ul>
							</td>
						</tr>


					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>