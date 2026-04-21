<div class="wrap">
	<h2><?php esc_html_e("Rss Post Importer Log", 'interq-rss-pi'); ?></h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="postbox-container">
				<p class="large">
					<?php
					printf(
						/* translators: %s: The base URL of the website used for cron job triggering. */
						esc_html__( 'If your imports are not running regularly according to your settings you might need to set up a scheduled task. There are several ways to do this; the most convenient is to set up a scheduled task on your server and simply ask it to hit your site\'s URL (%s) regularly. There are also external sites that offer the same service, such as:', 'interq-rss-pi' ),
						esc_url( get_site_url() )
					);
					?>
				<ul>
					<li><a href="http://www.mywebcron.com" target="_blank">www.mywebcron.com</a></li>
					<li><a href="http://www.onlinecronjobs.com" target="_blank">www.onlinecronjobs.com</a></li>
					<li><a href="http://www.easycron.com" target="_blank">www.easycron.com</a></li>
					<li><a href="http://cronless.com" target="_blank">cronless.com</a></li>
				</ul>
				</p>
				<a href="#" class="button button-large button-primary show-main-ui"><?php esc_html_e("Ok, all done", "interq-rss-pi"); ?></a> 
				<a href="#" class="button button-large button-warning clear-log"><?php esc_html_e("Clear log", "interq-rss-pi"); ?></a> 
				<div class="log">
					<!-- <code><?php /* echo(esc_html(wpautop($log, true))); */ ?></code> -->
						<code>
							<?php 
							// 1. Convert double line breaks to <p> tags
							$log_html = wpautop( $log, true );

							// 2. Define exactly which HTML tags are allowed in a log
							$allowed_log_html = array(
								'p'      => array(),
								'br'     => array(),
								'strong' => array(),
								'em'     => array(),
								'span'   => array( 'class' => array() ),
								'small'  => array(),
							);

							// 3. Output safely without turning tags into visible text
							echo wp_kses( $log_html, $allowed_log_html ); 
							?>
						</code>
				</div>
			</div>
		</div>
	</div>
</div>