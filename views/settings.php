<?php if (!defined('WPINC')) die();?>

<div class="pdf-light-viewer-admin-settings js-pdf-light-viewer-admin-settings">
	
		<div class="pure-g">
			<div class="pure-u-lg-2-3 pure-u-md-1-1 pure-u-sm-1-1 pure-u-xs-1-1">
				
				<div class="pdf-light-viewer-bl row">
					<div class="row hdr">
						<h3>
							<span class="fa fa-info"></span>
							<?php _e('About', PDF_LIGHT_VIEWER_PLUGIN)?>
						</h3>
					</div>
					<div class="row container">
						<p class="center">
							<a class="logo" href="http://pdf-light-viewer.wp.teamlead.pw" target="_blank">
								PDF Light Viewer Plugin
							</a>
						</p>
						
						<blockquote>
							WordPress PDF Light Viewer Plugin &copy; <a target="_blank" href="http://teamlead.pw">Teamlead Power&nbsp;<span class="fa fa-external-link-square"></span></a>
						</blockquote>
						
						<blockquote>
							<p>
								Icons &copy; <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">Font Awesome&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								Logo Icons &copy; <a href="https://www.iconfinder.com/Re66y" target="_blank">Gregor Cresnar&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								CSS Framework &copy; <a href="http://purecss.io/" target="_blank">Pure.css&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								Flipbook &copy; <a href="http://www.turnjs.com/" target="_blank">Turn.js&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								Lazy Load &copy; <a href="http://www.appelsiini.net/projects/lazyload" target="_blank">Lazy Load Plugin for jQuery&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								Slider &copy; <a href="http://bxslider.com/" target="_blank">bxSlider&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								Fullscreen &copy; <a href="https://github.com/kayahr/jquery-fullscreen-plugin/" target="_blank">jQuery Fullscreen Plugin&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
							<p>
								Zoom &copy; <a href="http://www.jacklmoore.com/zoom/" target="_blank">jQuery Zoom&nbsp;<span class="fa fa-external-link-square"></span></a>
							</p>
						</blockquote>
					</div>
				</div>
			
			
				<div class="pdf-light-viewer-bl row">
					<div class="row hdr">
						<h3>
							<span class="fa fa-exclamation-triangle"></span>
							<?php _e('Plugin Requirements', PDF_LIGHT_VIEWER_PLUGIN)?>
						</h3>
					</div>
					<div class="row in container">
						<ul>
							<?php
								foreach($requirements as $requirement) {
									
									if ($requirement['status']) {
										?>
											<li>
												<span class="fa-stack pdf-light-viewer-requirement-success">
													<i class="fa fa-circle fa-stack-2x"></i>
													<i class="fa fa-check fa-stack-1x fa-inverse"></i>
												</span>
												<?php echo $requirement['name'];?> <?php echo $requirement['success'];?>
											</li>
										<?php
									}
									else {
										?>
											<li>
												<span class="fa-stack pdf-light-viewer-requirement-fail">
													<i class="fa fa-circle fa-stack-2x"></i>
													<i class="fa fa-exclamation fa-stack-1x fa-inverse"></i>
												</span>
												<?php echo $requirement['name'];?> <?php echo $requirement['fail'];?>
											</li>
										<?php
									}
								}
							?>
						</ul>
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
						<legend><span class="fa fa-cogs"></span><?php _e('Main settings', PDF_LIGHT_VIEWER_PLUGIN)?></legend>
							
						<p class="pure-control-group">
							<label for="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[show-post-type]">
								<span class="fa-stack">
									<i class="fa fa-circle fa-stack-2x"></i>
									<i class="fa fa-th-list fa-stack-1x fa-inverse"></i>
								</span>
								<?php _e('Show PDFs in Menu', PDF_LIGHT_VIEWER_PLUGIN)?>
								<a href="#!" class="js-tip tip" title="<?php _e('Show PDFs menu item in the left admin sidebar', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="fa fa-question-circle"></span></a>
							</label>
							<input
								type="hidden"
								name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[show-post-type]"
								value="0"
								/>
							<input
								type="checkbox"
								name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[show-post-type]"
								value="1"
								<?php echo ( PdfLightViewer_AdminController::getSetting('show-post-type') ? 'checked="checked"' : '' )?>
								/>
						</p>
						
						<hr />
					
						<div class="row">
							<button class="button-primary" type="submit">
								<span class="fa fa-save"></span>
								<?php _e('Save', WP_APN_PLUGIN)?>
							</button>
						</div>
					</div>
					
				</form>
			
			</div>
			
			
			
			<div class="pure-u-lg-1-3 pure-u-md-1-1 pure-u-sm-1-1 pure-u-xs-1-1">
			
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
