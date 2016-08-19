<ul>
    <li>
        <div class="i pdf-light-viewer-slide">
            <a href="<?php echo PdfLightViewer_FrontController::getPageLink(1) ?>" class="page-1 js-page-thumbnail">
                <img
                    src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][0];?>"
                    alt=""
                    />
            </a>
            <span>
                1
                <?php if ($pdf_light_viewer_config['download_page_allowed']) { ?>
                    <a
                        target="_blank"
                        download="<?php echo PdfLightViewer_FrontController::getPageDownloadTitle($pdf_light_viewer_config['pages'][0]) ?>"
                        href="<?php echo PdfLightViewer_FrontController::getPageDownloadLink($pdf_light_viewer_config['pages'][0]) ?>">
                        <i class="icons slicon-cloud-download"></i>
                    </a>
                <?php } ?>
            </span>
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
                <a href="<?php echo PdfLightViewer_FrontController::getPageLink($i+1) ?>" class="page-<?php echo ($i+1);?> js-page-thumbnail">
                    <img
                        src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$thumb;?>"
                        alt=""
                        />
                </a>
                <a href="<?php echo PdfLightViewer_FrontController::getPageLink($i+2) ?>" class="page-<?php echo ($i+2);?> js-page-thumbnail">
                    <img
                        src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$next_thumb;?>"
                        alt=""
                        />
                </a>
                <span>
                    <?php if ($pdf_light_viewer_config['download_page_allowed']) { ?>
                        <a
                            target="_blank"
                            download="<?php echo PdfLightViewer_FrontController::getPageDownloadTitle($pdf_light_viewer_config['pages'][$i]) ?>"
                            href="<?php echo PdfLightViewer_FrontController::getPageDownloadLink($pdf_light_viewer_config['pages'][$i]) ?>">
                            <i class="icons slicon-cloud-download"></i>
                        </a>
                    <?php } ?>
                    
                    <?php echo ($i+1);?>-<?php echo ($i+2);?>
                    
                    <?php if ($pdf_light_viewer_config['download_page_allowed']) { ?>
                        <a
                            target="_blank"
                            download="<?php echo PdfLightViewer_FrontController::getPageDownloadTitle($pdf_light_viewer_config['pages'][$i+1]) ?>"
                            href="<?php echo PdfLightViewer_FrontController::getPageDownloadLink($pdf_light_viewer_config['pages'][$i+1]) ?>">
                            <i class="icons slicon-cloud-download"></i>
                        </a>
                    <?php } ?>
                </span>
            </div>
        </li>
    <?php } ?>
    
    <?php if ($last_page) { ?>
        <li>
            <div class="i pdf-light-viewer-slide">
                <a href="<?php echo PdfLightViewer_FrontController::getPageLink($last_thumb_index+1) ?>" class="page-<?php echo ($last_thumb_index+1);?> js-page-thumbnail">
                    <img
                        src="<?php echo $pdf_upload_dir_url.'-thumbs/'.$pdf_light_viewer_config['thumbs'][$last_thumb_index];?>"
                        alt=""
                        />
                </a>
                <span>
                    <?php echo ($last_thumb_index+1);?>
                    <?php if ($pdf_light_viewer_config['download_page_allowed']) { ?>
                        <a
                            target="_blank"
                            download="<?php echo PdfLightViewer_FrontController::getPageDownloadTitle($pdf_light_viewer_config['pages'][$last_thumb_index]) ?>"
                            href="<?php echo PdfLightViewer_FrontController::getPageDownloadLink($pdf_light_viewer_config['pages'][$last_thumb_index]) ?>">
                            <i class="icons slicon-cloud-download"></i>
                        </a>
                    <?php } ?>
                </span>
            </div>
        </li>
    <?php } ?>
</ul>
