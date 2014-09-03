<?php
	global $pdf_light_viewer_config;
	$pdf_upload_dir_url = $pdf_light_viewer_config['pdf_upload_dir_url'];
	
	$last_thumb_index = count($pdf_light_viewer_config['thumbs']) - 1;
	
	if (!empty($pdf_light_viewer_config['pages'])) {
	?>
		<div class="pdf-light-viewer js-pdf-light-viewer">
			
			<div class="pdf-light-viewer-magazine-viewport">
				<div class="js-pdf-light-viewer-fullscreen zoom-icon zoom-icon-in"></div>
				<div class="pdf-light-viewer-magazine-viewport-container">	
					<div class="js-pdf-light-viewer-magazine pdf-light-viewer-magazine">
						<?php foreach($pdf_light_viewer_config['pages'] as $page) {
							$size = getimagesize($pdf_upload_dir_url.'/'.$page);
							?>
							<div style="background-image:url('<?php echo plugins_url('img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>');">
								<div class="gradient"></div>
								<img
									class="js-pdf-light-viewer-lazy-loading"
									src="<?php echo plugins_url('img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE );?>"
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
			<?php if (!empty($pdf_light_viewer_config['thumbs'])) { ?>
				<div class="pdf-light-viewer-magazine-thumbnails js-pdf-light-viewer-magazine-thumbnails">
					
						<ul>
							<li class="i slide">
								<img src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][0];?>" width="76" height="100" class="page-1">
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
									<img src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$thumb;?>" width="76" height="100" class="page-<?php echo ($i+1);?>">
									<img src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$next_thumb;?>" width="76" height="100" class="page-<?php echo ($i+2);?>">
									<span><?php echo ($i+1);?>-<?php echo ($i+2);?></span>
								</li>
							<?php } ?>
							
							<?php if ($last_page) { ?>
								<li class="i slide">
									<img src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][$last_thumb_index];?>" width="76" height="100" class="page-<?php echo ($last_thumb_index+1);?>">
									<span><?php echo ($last_thumb_index+1);?></span>
								</li>
							<?php } ?>
						</ul>
					
				</div>
			<?php } ?>
				
		</div>
		
<?php } ?>
