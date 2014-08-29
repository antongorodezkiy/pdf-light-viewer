<div class="js-pdf-light-viewer-preview pdf-light-viewer-preview">
	<?php foreach($pages as $page) {
		$size = getimagesize($pdf_upload_dir_url.'/'.$page);
		//die('<pre>'.(__FILE__).':'.(__LINE__).'<hr />'.print_r($size,true).'</pre>');
		?>
		<div
			class="js-pdf-light-viewer-lazy-loading"
			data-original="<?php echo $pdf_upload_dir_url.'/'.$page;?>"
			style="background-image:url('<?php echo plugins_url('img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>');"></div>
	<?php } ?>
</div>
