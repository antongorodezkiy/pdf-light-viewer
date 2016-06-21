<?php
	global $pdf_light_viewer_config;
	
	$pdf_upload_dir_url = $pdf_light_viewer_config['pdf_upload_dir_url'];
	$last_thumb_index = count($pdf_light_viewer_config['thumbs']) - 1;
	
	$toolbarVisible = PdfLightViewer_PdfController::isToolbarVisible($pdf_light_viewer_config);
?>


	<?php if (!empty($pdf_light_viewer_config['pages'])) {
	?>
		<div class="pdf-light-viewer js-pdf-light-viewer"
			data-enable-zoom="<?php echo !$pdf_light_viewer_config['disable_page_zoom'];?>"
			>
			
			<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_start', $pdf_light_viewer_config['id']) ?>
			
            <!-- Thumbnails -->
			<?php if (!$pdf_light_viewer_config['hide_thumbnails_navigation'] && !empty($pdf_light_viewer_config['thumbs'])) { ?>
				<div class="pdf-light-viewer-magazine-thumbnails js-pdf-light-viewer-magazine-thumbnails">
					
					<div class="pdf-light-viewer-features-top-nav-panel">
						<?php include(PDF_LIGHT_VIEWER_APPPATH.'/views/shortcode-thumbnails.php') ?>
					</div>
					
				</div>
			<?php } ?>
            
			<div class="pdf-light-viewer-magazine-viewport js-pdf-light-viewer-magazine-viewport with-nav-top">
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
                
                <?php if ($toolbarVisible) { ?>
					<ul class="pdf-light-viewer-features-bottom-toolbar-panel">
						<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_top_panel', $pdf_light_viewer_config['id']) ?>
					</ul>
				<?php } ?>
                
			</div>
				
		</div>
		
<?php } ?>
