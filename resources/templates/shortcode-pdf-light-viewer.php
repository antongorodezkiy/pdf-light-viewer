<?php
	global $pdf_light_viewer_config;

	$pdf_upload_dir_url = $pdf_light_viewer_config['pdf_upload_dir_url'];
    $pages = !empty($pdf_light_viewer_config['thumbs']) ? array_keys($pdf_light_viewer_config['thumbs']) : array();
	$last_thumb_index = end($pages);

	$toolbarVisible = PdfLightViewer_PdfController::isToolbarVisible($pdf_light_viewer_config);
	$imagePlaceholder = PdfLightViewer_PdfController::getThemeImagePlaceholder($pdf_light_viewer_config);
	$themeClass = PdfLightViewer_PdfController::getThemeClass($pdf_light_viewer_config);
?>


<?php if (!empty($pdf_light_viewer_config['pages'])): ?>
	<div class="pdf-light-viewer js-pdf-light-viewer <?php echo esc_attr($themeClass) ?>"
		data-enable-zoom="<?php echo (bool)(!$pdf_light_viewer_config['disable_page_zoom']) ?>"
        data-zoom-magnify="<?php echo (int)$pdf_light_viewer_config['zoom_magnify']?>">

		<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_start', $pdf_light_viewer_config['id']) ?>

		<div class="pdf-light-viewer-magazine-viewport js-pdf-light-viewer-magazine-viewport with-nav-bottom">

			<?php if ($toolbarVisible): ?>
				<ul class="pdf-light-viewer-features-top-panel">
					<?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_top_panel', $pdf_light_viewer_config['id']) ?>
				</ul>
			<?php endif ?>

			<div class="pdf-light-viewer-magazine-viewport-container">
				<div class="js-pdf-light-viewer-magazine pdf-light-viewer-magazine"
                    data-max-book-width="<?php echo (int)$pdf_light_viewer_config['max_book_width'] ?>"
                    data-max-book-height="<?php echo (int)$pdf_light_viewer_config['max_book_height'] ?>"
                    data-limit-fullscreen-book-height="<?php echo (int)$pdf_light_viewer_config['limit_fullscreen_book_height'] ?>"
					data-width="<?php echo (int)$pdf_light_viewer_config['page_width'] ?>"
					data-height="<?php echo (int)$pdf_light_viewer_config['page_height'] ?>"
					data-pages-count="<?php echo (int)count($pdf_light_viewer_config['pages']) ?>"
					data-page-layout="<?php echo esc_attr($pdf_light_viewer_config['page_layout']) ?>"
                    data-download-page-format="<?php echo esc_attr($pdf_light_viewer_config['download_page_format']) ?>"
                    data-theme="<?php echo esc_attr($pdf_light_viewer_config['theme']) ?>"
                    data-disable-images-preloading="<?php echo (int)$pdf_light_viewer_config['disable_images_preloading'] ?>">
					<?php foreach($pdf_light_viewer_config['pages'] as $number => $page): ?>
						<div style="background-image:url('<?php echo esc_attr($imagePlaceholder) ?>');">
							<div class="gradient"></div>
                            <?php if ((bool)$pdf_light_viewer_config['disable_lazy_loading']): ?>
								<img
                                    class="js-pdf-light-viewer-lazy-loading js-pdf-light-viewer-lazy-loading-<?php echo esc_attr($number + 1) ?>"
									src="<?php echo esc_attr($pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page) ?>"
                                    data-original="<?php echo esc_attr($pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page) ?>"
									width="100%"
									height="100%"
									/>
                            <?php else : ?>
                                <img
									class="js-pdf-light-viewer-lazy-loading js-pdf-light-viewer-lazy-loading-<?php echo esc_attr($number + 1) ?> initially-hidden"
									src="<?php echo esc_attr($imagePlaceholder) ?>"
									data-original="<?php echo esc_attr($pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$page) ?>"
									width="100%"
									height="100%"
									/>
                            <?php endif ?>
						</div>
					<?php endforeach ?>
				</div>
			</div>
		</div>

		<!-- Thumbnails -->
		<?php if (!$pdf_light_viewer_config['hide_thumbnails_navigation'] && !empty($pdf_light_viewer_config['thumbs'])): ?>
			<div class="pdf-light-viewer-magazine-thumbnails js-pdf-light-viewer-magazine-thumbnails">

				<div class="pdf-light-viewer-features-bottom-panel">
					<?php include(PDF_LIGHT_VIEWER_APPPATH.'/resources/views/shortcode-thumbnails.php') ?>
				</div>

			</div>
		<?php endif ?>

	</div>

<?php endif ?>
