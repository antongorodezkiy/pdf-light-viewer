<?php if (!defined('WPINC')) die();?>

<div class="pdf-light-viewer-admin-settings js-pdf-light-viewer-admin-settings">

    <div class="pure-g">
        <div class="pure-u-lg-2-3 pure-u-md-1-1 pure-u-sm-1-1 pure-u-xs-1-1">

            <div class="pdf-light-viewer-content pdf-light-viewer-bl settings-pure-form pure-form-aligned pure-form" method="post" action="options.php">
            	<div class="row hdr">
            		<h3>
            			<span class="icons slicon-trophy"></span>
            			<?php _e('Vote for PDF Light Viewer plugin!', PDF_LIGHT_VIEWER_PLUGIN)?>
            		</h3>
            	</div>
            	<div class="row in">

                    <?php _e('Support PDF Light Viewer team spirit!', PDF_LIGHT_VIEWER_PLUGIN)?>
                    <a class="button-primary" href="https://wordpress.org/support/view/plugin-reviews/pdf-light-viewer?rate=5#postform" target="_blank">
                        <?php _e('Give 5 stars', PDF_LIGHT_VIEWER_PLUGIN)?>
                        <span class="icons slicon-star"></span>
                        <span class="icons slicon-star"></span>
                        <span class="icons slicon-star"></span>
                        <span class="icons slicon-star"></span>
                        <span class="icons slicon-star"></span>
                    </a>

            	</div>
            </div>

            <div class="pdf-light-viewer-bl row">
                <div class="row hdr">
                    <h3>
                        <span class="icons slicon-info"></span>
                        <?php _e('About', PDF_LIGHT_VIEWER_PLUGIN)?>
                    </h3>
                </div>
                <div class="row container">
                    <div class="pure-g">
                        <div class="pure-u-lg-2-3 pure-u-md-1-1 pure-u-sm-1-1 pure-u-xs-1-1">
                            <p class="center">
                                <a class="logo" href="http://pdf-light-viewer.wp.teamlead.pw" target="_blank">
                                    PDF Light Viewer Plugin
                                </a>
                            </p>

                            <blockquote>
                                <div>
                                    PDF Light Viewer Plugin &copy; <a target="_blank" href="http://teamlead.pw">Teamlead Power&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <?php if (defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')): ?>
                                    <div>
                                        PDF Light Viewer PRO Addon &copy; <a target="_blank" href="http://teamlead.pw">Teamlead Power&nbsp;<span class="icons slicon-link"></span></a>
                                    </div>
                                <?php endif ?>

                                <?php if (defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')): ?>
                                    <div>
                                        PDF Light Viewer Serverless Addon &copy; <a target="_blank" href="http://teamlead.pw">Teamlead Power&nbsp;<span class="icons slicon-link"></span></a>
                                    </div>
                                <?php endif ?>
                            </blockquote>
                        </div>
                        <div class="pure-u-lg-1-3 pure-u-md-1-1 pure-u-sm-1-1 pure-u-xs-1-1">
                            <blockquote>
                                <div>
                                    Icons &copy; <a href="http://thesabbir.github.io/simple-line-icons/" target="_blank">Simple line icons&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Logo Icons &copy; <a href="https://www.iconfinder.com/Re66y" target="_blank">Gregor Cresnar&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    CSS Framework &copy; <a href="http://purecss.io/" target="_blank">Pure.css&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Flipbook &copy; <a href="http://www.turnjs.com/" target="_blank">Turn.js&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Lazy Load &copy; <a href="http://www.appelsiini.net/projects/lazyload" target="_blank">Lazy Load Plugin for jQuery&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Slider &copy; <a href="http://bxslider.com/" target="_blank">bxSlider&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Fullscreen &copy; <a href="https://github.com/sindresorhus/screenfull.js" target="_blank">screenfull.js&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Zoom &copy; <a href="http://www.jacklmoore.com/zoom/" target="_blank">jQuery Zoom&nbsp;<span class="icons slicon-link"></span></a>
                                </div>
                                <div>
                                    Metaboxes &copy; <a href="https://github.com/WebDevStudios/CMB2" target="_blank">CMB2&nbsp;<span class="icons icon-link"></span></a>
                                </div>
                                <?php if (defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')): ?>
                                    <hr />
                                    <div>
                                        Printing &copy; <a href="https://github.com/posabsolute/jQuery-printPage-plugin" target="_blank">jQuery PrintPage plugin&nbsp;<span class="icons slicon-link"></span></a>
                                    </div>
                                    <div>
                                        Grid &copy; <a href="http://isotope.metafizzy.co/" target="_blank">Isotope&nbsp;<span class="icons slicon-link"></span></a>
                                    </div>
                                    <div>
                                        Pdf Text Parser &copy; <a href="https://github.com/smalot/pdfparser" target="_blank">Smalot PdfParser&nbsp;<span class="icons slicon-link"></span></a>
                                    </div>
                                <?php endif ?>
                                <?php if (defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')): ?>
                                    <hr />
                                    <div>
                                        PDF.js &copy; <a href="https://mozilla.github.io/pdf.js/" target="_blank">Mozilla PDF.js&nbsp;<span class="icons icon-link"></span></a>
                                    </div>
                                <?php endif ?>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>


            <div class="pdf-light-viewer-bl row">
                <div class="row hdr">
                    <h3>
                        <span class="icons slicon-list"></span>
                        <?php _e('Plugin Requirements', PDF_LIGHT_VIEWER_PLUGIN)?>
                    </h3>
                </div>
                <div class="row in container">
                    <?php include(dirname(__FILE__).'/requirements.php') ?>
                    <?php if (!defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')): ?>
                        <?php include(dirname(__FILE__).'/server-libs-requirements-doc.php') ?>
                    <?php endif ?>
                </div>
            </div>

            <?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':settings_view_after_requirements') ?>

            <form class="pdf-light-viewer-content pdf-light-viewer-bl settings-pure-form pure-form-aligned pure-form" method="post" action="options.php">

                <?php settings_fields(PDF_LIGHT_VIEWER_PLUGIN); ?>

                <div class="row hdr">
                    <h3>
                        <span class="icons slicon-equalizer"></span>
                        <?php _e('Settings', PDF_LIGHT_VIEWER_PLUGIN)?>
                    </h3>
                </div>

                <div class="row in">
                    <legend><span class="icons slicon-settings"></span>
                        <?php _e('Main settings', PDF_LIGHT_VIEWER_PLUGIN)?>
                        / <?php _e('CLI settings', PDF_LIGHT_VIEWER_PLUGIN)?>
                    </legend>

                    <p class="pure-control-group">
                        <label for="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[show-post-type]">
                            <i class="icons slicon-menu"></i>
                            <?php _e('Show PDFs in Menu', PDF_LIGHT_VIEWER_PLUGIN)?>
                            <a href="#!" class="js-tip tip" title="<?php _e('Show PDFs menu item in the left admin sidebar', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="icons slicon-question"></span></a>
                        </label>
                        <input
                            type="hidden"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[show-post-type]"
                            value="0"
                            />
                        <input
                            type="checkbox"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[show-post-type]"
                            value="1"
                            <?php echo ( PdfLightViewer_AdminController::getSetting('show-post-type') ? 'checked="checked"' : '' )?>
                            />
                    </p>

                    <p class="pure-control-group">
                        <label for="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[do-not-check-gs]">
                            <i class="icons slicon-wrench"></i>
                            <?php _e('Do not check GhostScript installation', PDF_LIGHT_VIEWER_PLUGIN)?>
                            <a href="#!" class="js-tip tip" title="<?php _e('For cases, when you are sure that GhostScript is installed, but it was not detected by the plugin correctly.', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="icons slicon-question"></span></a>
                        </label>
                        <input
                            type="hidden"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[do-not-check-gs]"
                            value="0"
                            />
                        <input
                            type="checkbox"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[do-not-check-gs]"
                            value="1"
                            <?php echo ( PdfLightViewer_AdminController::getSetting('do-not-check-gs') ? 'checked="checked"' : '' )?>
                            />
                    </p>

                    <p class="pure-control-group">
                        <label>
                            <i class="icons slicon-magic-wand"></i>
                            <?php _e('Prefer Imagick or Gmagick', PDF_LIGHT_VIEWER_PLUGIN)?>
                            <a href="#!" class="js-tip tip" title="<?php _e('For cases, when you have both. Otherwise only existing will be used.', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="icons slicon-question"></span></a>
                        </label>
                        <input
                            type="radio"
                            id="prefer-xmagick-imagick"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[prefer-xmagick]"
                            value="Imagick"
                            <?php echo ( PdfLightViewer_AdminController::getSetting('prefer-xmagick') == 'Imagick' ? 'checked="checked"' : '' )?>
                            />
                            <label for="prefer-xmagick-imagick" class="inline">
                                <?php _e('Imagick', PDF_LIGHT_VIEWER_PLUGIN)?>
                                <?php if (!class_exists('Imagick')) { ?>
                                    <span class="pdf-light-viewer-requirement-fail">
                                        (<?php _e('seems to be not installed', PDF_LIGHT_VIEWER_PLUGIN)?>)
                                    </span>
                                <?php } ?>
                            </label>
                        <input
                            type="radio"
                            id="prefer-xmagick-gmagick"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[prefer-xmagick]"
                            value="Gmagick"
                            <?php echo ( PdfLightViewer_AdminController::getSetting('prefer-xmagick') == 'Gmagick' ? 'checked="checked"' : '' )?>
                            />
                            <label for="prefer-xmagick-gmagick" class="inline">
                                <?php _e('Gmagick', PDF_LIGHT_VIEWER_PLUGIN)?>
                                <?php if (!class_exists('Gmagick')) { ?>
                                    <span class="pdf-light-viewer-requirement-fail">
                                        (<?php _e('seems to be not installed', PDF_LIGHT_VIEWER_PLUGIN)?>)
                                    </span>
                                <?php } ?>
                            </label>
                    </p>


                    <p class="pure-control-group">
                        <label for="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[enable-hash-nav]">
                            <i class="icons slicon-wrench"></i>
                            <?php _e('Enable hash navigation', PDF_LIGHT_VIEWER_PLUGIN)?>
                            <a href="#!" class="js-tip tip" title="<?php _e('In some cases turning this off can solve compatibility issues.', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="icons slicon-question"></span></a>
                        </label>
                        <input
                            type="hidden"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[enable-hash-nav]"
                            value="0"
                            />
                        <input
                            type="checkbox"
                            name="<?php echo PDF_LIGHT_VIEWER_PLUGIN?>[enable-hash-nav]"
                            value="1"
                            <?php echo ( PdfLightViewer_AdminController::getSetting('enable-hash-nav') ? 'checked="checked"' : '' )?>
                            />
                    </p>

                    <hr />

                    <div class="row">
                        <button class="button-primary" type="submit">
                            <?php _e('Save', PDF_LIGHT_VIEWER_PLUGIN)?>
                        </button>
                    </div>
                </div>

            </form>

            <?php do_action(PDF_LIGHT_VIEWER_PLUGIN.':settings_view_after_settings') ?>
        </div>



        <div class="pure-u-lg-1-3 pure-u-md-1-1 pure-u-sm-1-1 pure-u-xs-1-1">

            <?php if (!defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')): ?>
                <?php echo PdfLightViewer_Components_View::render('serverless-placeholder') ?>
            <?php endif ?>

            <div class="pdf-light-viewer-bl row">
                <div class="row hdr">
                    <h3>
                        <span class="icons slicon-support"></span>
                        <?php _e('Support', PDF_LIGHT_VIEWER_PLUGIN)?>
                    </h3>
                </div>
                <div class="row in container">

                    <div class="row">
                        <blockquote>
                            <p>
                                <?php

                                $subject = sprintf(__('Support request, plugin: %s (time: %s)', PDF_LIGHT_VIEWER_PLUGIN),
                                    PDF_LIGHT_VIEWER_PLUGIN,
                                    date('d.m.Y H:i:s')
                                );

                                echo sprintf(__('To get support please contact us on forum <a target="_blank" href="%s">%s</a>. Please also attach server information and log information below to let us know more about your server and site environment - this could be helpful to solve the issue.', PDF_LIGHT_VIEWER_PLUGIN),
                                    PdfLightViewer_Helpers_Url::getSupportUrl(),
                                    PdfLightViewer_Helpers_Url::getSupportUrl().'&nbsp;<span class="icons slicon-link"></span>'
                                );?>
                            </p>
                        </blockquote>
                        <p>
                            <?php _e('Subject', PDF_LIGHT_VIEWER_PLUGIN)?>: <?php echo $subject;?>
                        </p>
                    </div>

                    <div class="row">
                        <h5 class="row">
                            <?php _e('Plugin Requirements', PDF_LIGHT_VIEWER_PLUGIN)?>
                        </h5>
                        <?php include(dirname(__FILE__).'/requirements.php') ?>
                    </div>

                    <hr />

                    <div class="row">
                        <h5 class="row">
                            <?php _e('Server Info', PDF_LIGHT_VIEWER_PLUGIN)?>
                        </h5>
                        <ul>
                            <?php
                                foreach(PdfLightViewer_Helpers_Server::serverInfo() as $option => $val) {
                                    $info = $option.' -> '.$val;
                                    ?>
                                        <li>
                                            <?php echo $info; ?>
                                        </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>

                    <hr />

                    <div class="row">
                        <h5 class="row">
                            <?php _e('Theme', PDF_LIGHT_VIEWER_PLUGIN)?>
                        </h5>
                        <?php $current_theme = wp_get_theme(); ?>
                        <p>
                            <?php echo $current_theme->get('Name');?>,
                            <?php echo $current_theme->get('Version');?>,
                            <?php echo $current_theme->get('ThemeURI');?>
                        </p>
                        <p>
                            <?php _e('from', PDF_LIGHT_VIEWER_PLUGIN)?> <?php echo $current_theme->get('Author');?>,
                            <?php echo $current_theme->get('AuthorURI');?>
                        </p>

                    </div>

                    <hr />

                    <div class="row">
                        <h5 class="row">
                            <?php _e('Plugins', PDF_LIGHT_VIEWER_PLUGIN)?>
                        </h5>
                        <ul>
                            <?php
                                foreach(PdfLightViewer_Helpers_Plugins::getActivePlugins() as $pl) {
                                    $plugin = $pl['Name'].', '.$pl['Version'].', '.$pl['PluginURI'];
                                    ?>
                                        <li>
                                            <?php echo $plugin; ?>
                                        </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>

	<div class="pure-u-1">
		<div class="pdf-light-viewer-bl">

			<div class="row hdr">
				<h3>
					<span class="icons slicon-note"></span>
					<?php _e("Today's log file", PDF_LIGHT_VIEWER_PLUGIN)?>
				</h3>
			</div>

			<div class="row in pdf-light-viewer-logfile-preview">
				<code><pre><?php
					if (file_exists(PdfLightViewer_Components_Logger::getLogsPath().date('Y-m-d').'.php')) {
						include_once(PdfLightViewer_Components_Logger::getLogsPath().date('Y-m-d').'.php');
					}
					else {
						_e("Today's log file doesn't exist", PDF_LIGHT_VIEWER_PLUGIN);
					}
					?></pre></code>
			</div>

		</div>
	</div>

	<div class="pure-u-1">
		<?php
			$documentation_url = PdfLightViewer_Helpers_Url::getDocsUrl();
		?>
		<div class="pdf-light-viewer-bl">
			<div class="row hdr">
				<h3>
					<span class="icons slicon-doc"></span>
					<?php _e('Documentation', PDF_LIGHT_VIEWER_PLUGIN)?>
					<a class="right" target="_blank" href="<?php echo $documentation_url?>" title="<?php _e('open in the separate tab', PDF_LIGHT_VIEWER_PLUGIN)?>"><span class="icons slicon-link"></span></a>
				</h3>
			</div>
			<div class="row in container">
				<iframe class="pdf-light-viewer-iframe" src="<?php echo $documentation_url?>" frameborder="0"></iframe>
			</div>
		</div>
	</div>

</div>
