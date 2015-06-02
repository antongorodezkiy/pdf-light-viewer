<?php
	global $pdf_light_viewer_config;
	
	$last_thumb_index = count($pdf_light_viewer_config['thumbs']) - 1;
	
	if (!empty($pdf_light_viewer_config['pages'])) {
	?>
		<div class="pdf-light-viewer js-pdf-light-viewer"
			data-enable-zoom="<?php echo !$pdf_light_viewer_config['disable_page_zoom'];?>"
			>
			
			<div class="pdf-light-viewer-magazine-viewport">
				
				<div class="pdf-light-viewer-features-top-panel">
					<?php if ($pdf_light_viewer_config['download_allowed']) { ?>
						<a title="<?php _e('Download',PDF_LIGHT_VIEWER_PLUGIN);?>" href="<?php echo $pdf_light_viewer_config['download_link'];?>" target="_blank">
							<i class="fa fa-download fa-2x"></i>
						</a>
					<?php } ?>
					
					<?php if (!$pdf_light_viewer_config['hide_fullscreen_button']) { ?>
						<a title="<?php _e('Fullscreen',PDF_LIGHT_VIEWER_PLUGIN);?>" href="#!" class="js-pdf-light-viewer-fullscreen">
							<i class="fa fa-arrows-alt fa-2x"></i>
							<i class="fa fa-compress fa-2x initially-hidden"></i>
						</a>
					<?php } ?>
				</div>
				
				<div class="pdf-light-viewer-magazine-viewport-container"
					style="width: <?php echo ($pdf_light_viewer_config['page_width']*2);?>px;">	
					<div class="js-pdf-light-viewer-magazine pdf-light-viewer-magazine"
						data-width="<?php echo $pdf_light_viewer_config['page_width'];?>"
						data-height="<?php echo $pdf_light_viewer_config['page_height'];?>"
						>
						<?php foreach($pdf_light_viewer_config['pages'] as $page) {
							?>
							<div style="background-image:url('<?php echo plugins_url('assets/img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>');">
								<div class="gradient"></div>
								<img
									class="js-pdf-light-viewer-lazy-loading"
									src="<?php echo plugins_url('assets/img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>"
									data-original="<?php echo $pdf_upload_dir_url.'/'.$page;?>"
									width="100%"
									height="100%"
									/>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<!-- Thumbnails -->
			<?php if (!$pdf_light_viewer_config['hide_thumbnails_navigation'] && !empty($pdf_light_viewer_config['thumbs'])) { ?>
				<div class="pdf-light-viewer-magazine-thumbnails js-pdf-light-viewer-magazine-thumbnails">
					
						<ul>
							<li class="i slide">
								<img
									src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][0];?>"
									class="page-1"
									/>
								<span>1</span>
							</li>
							<?php
							$last_page = false;
							for($i = 1; $i < $last_thumb_index; $i+=2) {
								if ($i+1 == $last_thumb_index) {
									$last_page = false;
								}
								else {
									$last_page = true;
								}
								$thumb = $pdf_light_viewer_config['thumbs'][$i];
								$next_thumb = $pdf_light_viewer_config['thumbs'][$i+1];
								?>
								<li class="d slide">
									<img
										src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$thumb;?>"
										class="page-<?php echo ($i+1);?>"
										/>
									<img
										src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$next_thumb;?>"
										class="page-<?php echo ($i+2);?>"
										/>
									<span><?php echo ($i+1);?>-<?php echo ($i+2);?></span>
								</li>
							<?php } ?>
							
							<?php if ($last_page) { ?>
								<li class="i slide">
									<img
										src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][$last_thumb_index];?>"
										class="page-<?php echo ($last_thumb_index+1);?>"
										/>
									<span><?php echo ($last_thumb_index+1);?></span>
								</li>
							<?php } ?>
						</ul>
					
				</div>
			<?php } ?>
				
		</div>
		
<?php } ?>
