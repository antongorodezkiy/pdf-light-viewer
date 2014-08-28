<?php if (!defined('WPINC')) die(); ?>

<div class="wrap pdf-light-viewer js-pdf-light-viewer-admin-settings pure-g-r">
	
		<div class="pure-u-2-3">
			
			<div class="pdf-light-viewer-bl row">
				<div class="row hdr">
					<h3>
						<span class="fa fa-info"></span>
						<?php _e('About', PDF_LIGHT_VIEWER_PLUGIN)?>
					</h3>
				</div>
				<div class="row container">
					<p class="center">
						<a class="logo" href="" target="_blank">
							PDF Light Viewer Plugin
						</a>
					</p>

				</div>
			</div>

			<form class="pdf-light-viewer-content pdf-light-viewer-bl settings-pure-form pure-form-aligned pure-form" method="post" action="options.php">
				
				<?php settings_fields(PDF_LIGHT_VIEWER_PLUGIN); ?>
				
				<div class="row hdr">
					<h3>
						<span class="fa fa-sliders"></span>
						<?php _e('Settings', PDF_LIGHT_VIEWER_PLUGIN)?>
					</h3>
				</div>

				<div class="row in">

					<div class="row">
						<legend><span class="fa fa-paper-plane"></span><?php _e('Automatic mode', PDF_LIGHT_VIEWER_PLUGIN)?></legend>
						<p><?php _e('Automatic adding linked articles after the post content on a single post or custom post page', PDF_LIGHT_VIEWER_PLUGIN)?></p>
						<p class="pure-control-group">
							<label for="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>-add-to-post-content">
								<span class="fa-stack">
									<i class="fa fa-circle fa-stack-2x"></i>
									<i class="fa fa-paper-plane fa-stack-1x fa-inverse"></i>
								</span>
								<?php _e('Enable automatic mode', PDF_LIGHT_VIEWER_PLUGIN)?>
								<a href="#" class="js-tip tip" title="<?php _e('Automatic way is when linked articles will be automatically added to the post content on a single post page. You can also change linked articles visibility on the post editing page.', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="fa fa-question-circle"></span></a>
							</label>
							<input
								type="checkbox"
								name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>-add-to-post-content"
								id="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>-add-to-post-content"
								value="1"
								<?php echo ( get_option(PDF_LIGHT_VIEWER_PLUGIN.'-add-to-post-content') ? 'checked="checked"' : '' )?>
								/>
						</p>
					</div>
					
					<p>
						<blockquote>
							<?php echo sprintf(__('More information about settings you could find below in %s "Documentation" section, "Where to start?" subsection.',PDF_LIGHT_VIEWER_PLUGIN),'<span class="fa fa-file-code-o"></span>')?>
						</blockquote>
					</p>

					<hr />
					
					<div class="row">
						<button class="button-primary" type="submit">
							<span class="fa fa-save"></span>
							<?php _e('Save', PDF_LIGHT_VIEWER_PLUGIN)?>
						</button>
					</div>
				</div>
				
			</form>

		</div>
		
		<div class="pure-u-1-3">

			<div class="pdf-light-viewer-bl row">
				<div class="row hdr">
					<h3>
						<span class="fa fa-envelope-o"></span>
						<?php _e('Personal Support', PDF_LIGHT_VIEWER_PLUGIN)?>
					</h3>
				</div>
				<div class="row in container">

					<div class="row">
						<blockquote>
							<p>
								<?php

								$subject = sprintf(__('Support request, plugin: %s (time: %s)', PDF_LIGHT_VIEWER_PLUGIN),
									PDF_LIGHT_VIEWER_PLUGIN,
									date('d.m.Y H:i:s')
								);
								
								echo sprintf(__('To get support please contact us on address <a target="_blank" href="%s">%s</a>. Please also attach information below to let us know more about your server and site environment - this could be helpful to solve the issue.', PDF_LIGHT_VIEWER_PLUGIN),
									'mailto:support@teamlead.pw?subject='.$subject,
									'support@teamlead.pw&nbsp;<span class="fa fa-external-link-square"></span>'
								);?>
							</p>
						</blockquote>
						<p>
							Email: <a target="_blank" href="mailto:support@teamlead.pw?subject=<?php echo $subject;?>">support@teamlead.pw&nbsp;<span class="fa fa-external-link-square"></span></a>
						</p>
						<p>
							<?php _e('Subject', PDF_LIGHT_VIEWER_PLUGIN)?>: <?php echo $subject;?>
						</p>
					</div>
		
					<div class="row">
						<h5 class="row">
							<?php _e('Server Info', PDF_LIGHT_VIEWER_PLUGIN)?>
						</h5>
						<ul>
							<?php
								foreach(PdfLightViewer_Plugin::serverInfo() as $option => $val) {
									$info = $option.' -> '.$val;
									?>
										<li>
											<?php echo $info; ?>
										</li>
									<?php
								}
							?>
						</ul>
					</div>
					
					<hr />
					
					<div class="row">
						<h5 class="row">
							<?php _e('Theme', PDF_LIGHT_VIEWER_PLUGIN)?>
						</h5>
						<?php $current_theme = wp_get_theme(); ?>
						<p>
							<?php echo $current_theme->get('Name');?>,
							<?php echo $current_theme->get('Version');?>,
							<?php echo $current_theme->get('ThemeURI');?>
						</p>
						<p>
							<?php _e('from', PDF_LIGHT_VIEWER_PLUGIN)?> <?php echo $current_theme->get('Author');?>,
							<?php echo $current_theme->get('AuthorURI');?>
						</p>
						
					</div>
					
					<hr />
					
					<div class="row">
						<h5 class="row">
							<?php _e('Plugins', PDF_LIGHT_VIEWER_PLUGIN)?>
						</h5>
						<ul>
							<?php
								foreach(PdfLightViewer_Plugin::getActivePlugins() as $pl) {
									$plugin = $pl['Name'].', '.$pl['Version'].', '.$pl['PluginURI'];
									?>
										<li>
											<?php echo $plugin; ?>
										</li>
									<?php
								}
							?>
						</ul>
					</div>
			
				</div>
			</div>
			
		</div>
		
	<div class="pure-u-1">
		<?php
			$documentation_url = PdfLightViewer_Plugin::getDocsUrl();
		?>
		<div class="pdf-light-viewer-bl">
			<div class="row hdr">
				<h3>
					<span class="fa fa-file-code-o"></span>
					<?php _e('Documentation', PDF_LIGHT_VIEWER_PLUGIN)?>
					<a class="right" target="_blank" href="<?php echo $documentation_url?>" title="<?php _e('open in the separate tab', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="fa fa-external-link"></span></a>
				</h3>
			</div>
			<div class="row in container">
				<iframe class="pdf-light-viewer-iframe" src="<?php echo $documentation_url?>" frameborder="0"></iframe>
			</div>
		</div>
	</div>
		
</div>
