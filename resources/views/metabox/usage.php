<?php if($pages) { ?>
	<div>
		WordPress Shortcode:
		<code>[pdf-light-viewer id="<?php echo (int)$post_id ?>"]</code>
	</div>
	<br />
	<div>
		PHP:
		<code>PdfLightViewer_FrontController::display_pdf_book(array('id' => '<?php echo (int)$post_id;?>'));</code>
	</div>
<?php } else { ?>
	<?php echo esc_html__('You have not yet converted the PDF.',PDF_LIGHT_VIEWER_PLUGIN)?>
	<i><?php echo esc_html__('Note: it normally takes just 60 to 90 seconds for the conversion of small PDF file after submission. Thank you for your patience!',PDF_LIGHT_VIEWER_PLUGIN)?></i>
<?php } ?>
