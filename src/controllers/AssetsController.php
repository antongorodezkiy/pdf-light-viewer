<?php if (!defined('WPINC')) die();

class PdfLightViewer_AssetsController
{
	public static function admin_head()
    {
		global $post;

        if (
            (PdfLightViewer_Helpers_Http::get('post_type') && PdfLightViewer_Helpers_Http::get('post_type') == PdfLightViewer_PdfController::$type)
            || ($post && $post->post_type == PdfLightViewer_PdfController::$type)
        ) {
		    CMB2_hookup::enqueue_cmb_css();
        }

		// styles
			PdfLightViewer_Components_Assets::enqueueStyles(array(
				'purecss.grids.responsive' => 'assets/node_modules/purecss/build/grids-responsive-min.css',
				'purecss.grids.core' => 'assets/node_modules/purecss/build/grids-core-min.css',
				'purecss.forms' => 'assets/node_modules/purecss/build/forms-min.css',
				'jquery.bxslider.css' => 'assets/node_modules/bxslider/dist/jquery.bxslider.min.css',
                'jquery.qtip.css' => 'assets/bower_components/qtip2/jquery.qtip.min.css',
				'backend.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/dist/pdf-light-viewer-backend.css'
			));

		// scripts
			if (
                (PdfLightViewer_Helpers_Http::get('post_type') && PdfLightViewer_Helpers_Http::get('post_type') == PdfLightViewer_PdfController::$type)
                || ($post && $post->post_type == PdfLightViewer_PdfController::$type)
            ) {
                PdfLightViewer_Components_Assets::enqueueScripts(array(
                    'jquery.scrollstop.js' => 'assets/node_modules/jquery-lazyload/jquery.scrollstop.js',
                    'jquery.lazyload.js' => 'assets/node_modules/jquery-lazyload/jquery.lazyload.js',
                    'hash.turn.js' => 'assets/js/turn.js/hash.js',
                    'screenfull.js' => 'assets/node_modules/screenfull/dist/screenfull.js',
                    'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
                    'turn.js' => 'assets/js/turn.js/turn.min.js',
                    'jquery.bxslider.js' => 'assets/node_modules/bxslider/dist/jquery.bxslider.min.js',
                    'jquery.zoom.js' => 'assets/node_modules/jquery-zoom/jquery.zoom.min.js',
                ));

                wp_enqueue_script(
                    'magazine.'.PDF_LIGHT_VIEWER_FILE,
                    plugins_url('resources/assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
                    array('jquery', 'jquery.lazyload.js', 'turn.js'),
                    filemtime(PDF_LIGHT_VIEWER_APPPATH.'/resources/assets/js/magazine.js')
                );
            }

            PdfLightViewer_Components_Assets::enqueueScripts(array(
                'jquery.qtip.js' => 'assets/bower_components/qtip2/jquery.qtip.min.js',
                'quick-edit.'.PDF_LIGHT_VIEWER_FILE => 'assets/js/quick-edit.js',
                'admin.'.PDF_LIGHT_VIEWER_FILE => 'assets/js/admin.js'
            ));

			// javascript settings
				// for serverless we will handle unimported in other way
				if (!PdfLightViewer_Model::$unimported && !defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')) {
					PdfLightViewer_Model::$unimported = PdfLightViewer_Model::getOneUnimported();
				}

            $theme = defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')
                ? PdfLightViewerPro_AdminController::getSetting('theme', PdfLightViewer_PdfController::THEME_LIGHT)
                : PdfLightViewer_PdfController::THEME_LIGHT;

			wp_localize_script('admin.'.PDF_LIGHT_VIEWER_FILE, 'PdfLightViewer', array(
				'url' => array(
					'ajaxurl' => admin_url('admin-ajax.php')
				),
				'flags' => array(
					'ping_import' => (bool)PdfLightViewer_Model::$unimported
				),
				'__' => array(
					'Import process was successfully finished. Please check results on the PDF page.' => esc_html__('Import process was successfully finished. Please check results on the PDF page.', PDF_LIGHT_VIEWER_PLUGIN),
					'Import process failed due to an unknown error.' => esc_html__('Import process failed due to the unknown error.', PDF_LIGHT_VIEWER_PLUGIN),
					'Import process failed due to the error:' => esc_html__('Import process failed due to the error:', PDF_LIGHT_VIEWER_PLUGIN)
				),
                'settings' => array(
                    'enable_hash_nav' => (bool)PdfLightViewer_AdminController::getSetting('enable-hash-nav'),
                    'theme' => $theme,
                    'theme_class' => PdfLightViewer_PdfController::getThemeClass(array('theme' => $theme))
                )
			));
	}

	public static function frontendRegister()
    {

        global $post;

		// styles
			PdfLightViewer_Components_Assets::registerStyles(array(
				'jquery.bxslider.css' => 'assets/node_modules/bxslider/dist/jquery.bxslider.min.css',
                'jquery.qtip.css' => 'assets/bower_components/qtip2/jquery.qtip.min.css'
			));

            wp_register_style(
				'frontend.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('resources/assets/dist/pdf-light-viewer-frontend.css', PDF_LIGHT_VIEWER_FILE),
				array(
                    'jquery.bxslider.css',
                    'jquery.qtip.css'
                ),
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/resources/assets/dist/pdf-light-viewer-frontend.css')
			);

		// scripts
			PdfLightViewer_Components_Assets::registerScripts(array(
                'jquery.scrollstop.js' => 'assets/node_modules/jquery-lazyload/jquery.scrollstop.js',
                'jquery.lazyload.js' => 'assets/node_modules/jquery-lazyload/jquery.lazyload.js',
                'hash.turn.js' => 'assets/js/turn.js/hash.js',
                'screenfull.js' => 'assets/node_modules/screenfull/dist/screenfull.js',
                'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
                'turn.js' => 'assets/js/turn.js/turn.min.js',
                'jquery.bxslider.js' => 'assets/node_modules/bxslider/dist/jquery.bxslider.min.js',
                'jquery.zoom.js' => 'assets/node_modules/jquery-zoom/jquery.zoom.min.js',
                'jquery.qtip.js' => 'assets/bower_components/qtip2/jquery.qtip.min.js'
			));

			wp_register_script(
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('resources/assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array(
                    'jquery',
                    'jquery.scrollstop.js',
                    'jquery.lazyload.js',
                    'hash.turn.js',
                    'screenfull.js',
                    'html4.turn.js',
                    'turn.js',
                    'jquery.bxslider.js',
                    'jquery.zoom.js',
                    'jquery.qtip.js'
                ),
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/resources/assets/js/magazine.js')
			);

            $theme = defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')
                ? PdfLightViewerPro_AdminController::getSetting('theme', PdfLightViewer_PdfController::THEME_LIGHT)
                : PdfLightViewer_PdfController::THEME_LIGHT;

            wp_localize_script('magazine.'.PDF_LIGHT_VIEWER_PLUGIN, 'PdfLightViewer', array(
                'settings' => array(
                    'enable_hash_nav' => (bool)PdfLightViewer_AdminController::getSetting('enable-hash-nav'),
                    'theme' => $theme,
                    'theme_class' => PdfLightViewer_PdfController::getThemeClass(array('theme' => $theme))
                )
			));

        if (
            // single pdf page
            (
                is_single(array(PdfLightViewer_PdfController::$type))
                || (
                    $post
                    && get_post_type($post) == PdfLightViewer_PdfController::$type
                )
            )

            // pdf archive
            || is_post_type_archive(array(PdfLightViewer_PdfController::$type))

            // pdf is in shortcode inside post_content
            || (
                $post
                && $post->post_content
                && (
                    has_shortcode($post->post_content, 'pdf-light-viewer')
                    || has_shortcode($post->post_content, 'pdf-light-viewer-archive')
                )
            )
            || apply_filters(PDF_LIGHT_VIEWER_PLUGIN.':should_enqueue_frontend_assets', false)
        ) {
            self::frontendEnqueue();
        }
	}

    public static function frontendEnqueue()
    {
        wp_enqueue_script('magazine.'.PDF_LIGHT_VIEWER_PLUGIN);
        wp_enqueue_style('frontend.'.PDF_LIGHT_VIEWER_PLUGIN);
    }
}
