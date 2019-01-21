<?php
	global $pdf_light_viewer_config;

	$pdf_upload_dir_url = $pdf_light_viewer_config['pdf_upload_dir_url'];
	$pages = !empty($pdf_light_viewer_config['thumbs']) ? array_keys($pdf_light_viewer_config['thumbs']) : array();
	$last_thumb_index = end($pages);

	$toolbarVisible = PdfLightViewer_PdfController::isToolbarVisible($pdf_light_viewer_config);
    $imagePlaceholder = PdfLightViewer_PdfController::getThemeImagePlaceholder($pdf_light_viewer_config);
    $themeClass = PdfLightViewer_PdfController::getThemeClass($pdf_light_viewer_config);
?>


	<?php if (!empty($pdf_light_viewer_config['pages'])) {
	?>
		<div class="pdf-light-viewer js-pdf-light-viewer <?php echo $themeClass ?>"
			data-enable-zoom="<?php echo !$pdf_light_viewer_config['disable_page_zoom'];?>"
			data-zoom-magnify="<?php echo $pdf_light_viewer_config['zoom_magnify']?>">

			<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_start', $pdf_light_viewer_config['id']) ?>

            <!-- Thumbnails -->
			<?php if (!$pdf_light_viewer_config['hide_thumbnails_navigation'] && !empty($pdf_light_viewer_config['thumbs'])) { ?>
				<div class="pdf-light-viewer-magazine-thumbnails js-pdf-light-viewer-magazine-thumbnails">

					<div class="pdf-light-viewer-features-top-nav-panel">
						<?php include(PDF_LIGHT_VIEWER_APPPATH.'/resources/views/shortcode-thumbnails.php') ?>

					</div>

				</div>
			<?php } ?>

			<div class="pdf-light-viewer-magazine-viewport js-pdf-light-viewer-magazine-viewport with-nav-top">
				<div class="pdf-light-viewer-magazine-viewport-container">
					<div class="js-pdf-light-viewer-magazine pdf-light-viewer-magazine"
                        data-max-book-width="<?php echo $pdf_light_viewer_config['max_book_width'] ?>"
                        data-max-book-height="<?php echo $pdf_light_viewer_config['max_book_height'] ?>"
                        data-limit-fullscreen-book-height="<?php echo $pdf_light_viewer_config['limit_fullscreen_book_height'] ?>"
						data-width="<?php echo $pdf_light_viewer_config['page_width'] ?>"
						data-height="<?php echo $pdf_light_viewer_config['page_height'] ?>"
						data-pages-count="<?php echo count($pdf_light_viewer_config['pages']) ?>"
						data-page-layout="<?php echo $pdf_light_viewer_config['page_layout'] ?>"
                        data-download-page-format="<?php echo $pdf_light_viewer_config['download_page_format'] ?>"
                        data-theme="<?php echo $pdf_light_viewer_config['theme'] ?>"
                        data-disable-images-preloading="<?php echo $pdf_light_viewer_config['disable_images_preloading'] ?>">
						<?php foreach($pdf_light_viewer_config['pages'] as $number => $page) {
							?>
							<div style="background-image:url('<?php echo $imagePlaceholder ?>');">
								<div class="gradient"></div>
                                <?php if ((bool)$pdf_light_viewer_config['lazy_loading_disabled']): ?>
    								<img
                                        class="js-pdf-light-viewer-lazy-loading js-pdf-light-viewer-lazy-loading-<?php echo ($number + 1) ?>"
    									src="<?php echo $pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page;?>"
                                        data-original="<?php echo $pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page;?>"
    									width="100%"
    									height="100%"
    									/>
                                <?php else : ?>
                                    <img
    									class="js-pdf-light-viewer-lazy-loading js-pdf-light-viewer-lazy-loading-<?php echo ($number + 1) ?> initially-hidden"
    									src="<?php echo $imagePlaceholder ?>"
    									data-original="<?php echo $pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page;?>"
    									width="100%"
    									height="100%"
    									/>
                                <?php endif ?>
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
