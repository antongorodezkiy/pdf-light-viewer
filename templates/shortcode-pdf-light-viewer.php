<?php
	global $pdf_light_viewer_config;
	
	$pdf_upload_dir_url = $pdf_light_viewer_config['pdf_upload_dir_url'];
	$last_thumb_index = count($pdf_light_viewer_config['thumbs']) - 1;
	
	$toolbarVisible = (
		$pdf_light_viewer_config['download_allowed']
		|| !$pdf_light_viewer_config['hide_fullscreen_button']
		|| !$pdf_light_viewer_config['disable_page_zoom']
		|| !empty($pdf_light_viewer_config['print_allowed'])
        || !empty($pdf_light_viewer_config['print_page_allowed'])
		|| !empty($pdf_light_viewer_config['enabled_archive'])
		|| !empty($pdf_light_viewer_config['enabled_pdf_search'])
	);
?>


	<?php if (!empty($pdf_light_viewer_config['pages'])) {
	?>
		<div class="pdf-light-viewer js-pdf-light-viewer"
			data-enable-zoom="<?php echo !$pdf_light_viewer_config['disable_page_zoom'];?>"
			>
			
			<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_start', $pdf_light_viewer_config['id']) ?>
			
			<div class="pdf-light-viewer-magazine-viewport js-pdf-light-viewer-magazine-viewport with-nav-bottom">
				
				<?php if ($toolbarVisible) { ?>
					<ul class="pdf-light-viewer-features-top-panel">
						
						<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_top_panel', $pdf_light_viewer_config['id']) ?>
						
						<?php if ($pdf_light_viewer_config['download_allowed']) { ?>
							<li>
								<a title="<?php _e('Download',PDF_LIGHT_VIEWER_PLUGIN);?>" href="<?php echo $pdf_light_viewer_config['download_link'];?>" target="_blank">
									<i class="icons slicon-cloud-download"></i>
								</a>
							</li>
						<?php } ?>
						
						<?php if (!$pdf_light_viewer_config['hide_fullscreen_button']) { ?>
							<li>
								<a title="<?php _e('Fullscreen',PDF_LIGHT_VIEWER_PLUGIN);?>" href="#!" class="js-pdf-light-viewer-fullscreen">
									<i class="icons slicon-size-fullscreen"></i>
									<i class="icons slicon-size-actual initially-hidden"></i>
								</a>
							</li>
						<?php } ?>
						
						<?php if (!$pdf_light_viewer_config['disable_page_zoom']) { ?>
							<li>
								<span title="<?php _e('Zoom enabled',PDF_LIGHT_VIEWER_PLUGIN);?>">
									<i class="icons slicon-frame"></i>
								</span>
							</li>
						<?php } ?>
						
					</ul>
				<?php } ?>
				
				<div class="pdf-light-viewer-magazine-viewport-container">	
					<div class="js-pdf-light-viewer-magazine pdf-light-viewer-magazine"
                        data-max-book-width="<?php echo $pdf_light_viewer_config['max_book_width'] ?>"
						data-width="<?php echo $pdf_light_viewer_config['page_width'] ?>"
						data-height="<?php echo $pdf_light_viewer_config['page_height'] ?>"
						data-pages-count="<?php echo count($pdf_light_viewer_config['pages']) ?>"
						data-force-one-page-layout="<?php echo $pdf_light_viewer_config['force_one_page_layout'] ?>">
						<?php foreach($pdf_light_viewer_config['pages'] as $number => $page) {
							?>
							<div style="background-image:url('<?php echo plugins_url('assets/img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>');">
								<div class="gradient"></div>
								<img
									class="js-pdf-light-viewer-lazy-loading js-pdf-light-viewer-lazy-loading-<?php echo ($number + 1) ?> initially-hidden"
									src="<?php echo plugins_url('assets/img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>"
									data-original="<?php echo $pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page;?>"
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
					
					<div class="pdf-light-viewer-features-bottom-panel">
						<ul>
							<li>
								<div class="i pdf-light-viewer-slide">
									<a href="<?php echo PdfLightViewer_FrontController::getPageLink(1) ?>" class="page-1">
										<img
											src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][0];?>"
											/>
									</a>
									<span>1</span>
								</div>
							</li>
							<?php
							
							if ($last_thumb_index == 1) {
								$last_page = true;
							}
							else {
								$last_page = false;
							}
							
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
								<li>
									<div class="d pdf-light-viewer-slide">
										<a href="<?php echo PdfLightViewer_FrontController::getPageLink($i+1) ?>" class="page-<?php echo ($i+1);?>">
											<img
												src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$thumb;?>"
												/>
										</a>
										<a href="<?php echo PdfLightViewer_FrontController::getPageLink($i+2) ?>" class="page-<?php echo ($i+2);?>">
											<img
												src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$next_thumb;?>"
												/>
										</a>
										<span><?php echo ($i+1);?>-<?php echo ($i+2);?></span>
									</div>
								</li>
							<?php } ?>
							
							<?php if ($last_page) { ?>
								<li>
									<div class="i pdf-light-viewer-slide">
										<a href="<?php echo PdfLightViewer_FrontController::getPageLink($last_thumb_index+1) ?>" class="page-<?php echo ($last_thumb_index+1);?>">
											<img
												src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][$last_thumb_index];?>"
												/>
										</a>
										<span><?php echo ($last_thumb_index+1);?></span>
									</div>
								</li>
							<?php } ?>
						</ul>
					</div>
					
				</div>
			<?php } ?>
				
		</div>
		
<?php } ?>
