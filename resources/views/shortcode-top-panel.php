<?php
global $pdf_light_viewer_config;
?>

<?php if ($pdf_light_viewer_config['download_page_allowed']) { ?>
    <li>
        <a href="#!" class="js-pdf-light-viewer-download-options" title="<?php _e('Download',PDF_LIGHT_VIEWER_PLUGIN)?>">
            <i class="slicon-cloud-download"></i>
        </a>
        <div class="js-pdf-light-viewer-download-options-contaner pdf-light-viewer-print-options-contaner">
            <ul>
                <?php if ($pdf_light_viewer_config['download_allowed']) { ?>
                    <li>
                        <a href="<?php echo $pdf_light_viewer_config['download_link']?>" target="_blank">
                            <i class="slicon-cloud-download"></i>
                            <?php _e('Download',PDF_LIGHT_VIEWER_PLUGIN)?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a
                        href="#!"
                        target="_blank"
                        download=""
                        class="js-pdf-light-viewer-download-page">
                            <i class="icons slicon-picture"></i>
                            <?php _e('Download Page',PDF_LIGHT_VIEWER_PLUGIN);?>
                    </a>
                </li>
                <li>
                    <a
                        style="display: none"
                        href="#!"
                        target="_blank"
                        download=""
                        class="js-pdf-light-viewer-download-neighborhood-page">
                            <i class="icons slicon-picture"></i>
                            <?php _e('Download Right Page',PDF_LIGHT_VIEWER_PLUGIN);?>
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php } else if ($pdf_light_viewer_config['download_allowed']) { ?>
    <li>
        <a title="<?php _e('Download',PDF_LIGHT_VIEWER_PLUGIN);?>" href="<?php echo $pdf_light_viewer_config['download_link']?>" target="_blank">
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
        <a class="js-pdf-light-viewer-toggle-zoom" href="#!" title="<?php _e('Zoom enabled',PDF_LIGHT_VIEWER_PLUGIN);?>">
            <i class="icons slicon-frame"></i>
        </a>
    </li>
<?php } ?>

<?php if ($pdf_light_viewer_config['show_toolbar_next_previous']) { ?>
    <li>
        <a href="#!" title="<?php _e('Previous page',PDF_LIGHT_VIEWER_PLUGIN);?>" class="js-pdf-light-viewer-previous-page">
            <i class="icons slicon-arrow-left"></i>
        </a>
        <a href="#!" title="<?php _e('Next page',PDF_LIGHT_VIEWER_PLUGIN);?>" class="js-pdf-light-viewer-next-page">
            <i class="icons slicon-arrow-right"></i>
        </a>
    </li>
<?php } ?>

<?php if ($pdf_light_viewer_config['show_toolbar_goto_page']) { ?>
    <li>
        <a href="#!" class="js-pdf-light-viewer-goto-page">Open</a>
        <input type="text" class="js-pdf-light-viewer-goto-page-input pdf-light-viewer-goto-page-input" />
    </li>
<?php } ?>
    
<?php if ($pdf_light_viewer_config['show_page_numbers']) { ?>
    <li>
        <span title="<?php _e('Current page',PDF_LIGHT_VIEWER_PLUGIN);?>" class="js-pdf-light-viewer-current-page-indicator"></span>
    </li>
<?php } ?>
