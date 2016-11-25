<?php if (!defined('WPINC')) die();
		
class PdfLightViewer_AssetsController {
    
    protected static function enqueueScripts($jquery_plugins)
    {
        foreach($jquery_plugins as $id => $file) {
            $ver = null;
            if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$file)) {
                $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$file);
            }
            
            wp_enqueue_script(
                $id,
                plugins_url($file, PDF_LIGHT_VIEWER_FILE),
                array('jquery'),
                $ver
            );
        }
    }
    
    protected static function enqueueStyles($styles)
    {
        foreach($styles as $id => $file) {
            $ver = null;
            if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$file)) {
                $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$file);
            }
            
            wp_enqueue_style(
                $id,
                plugins_url($file, PDF_LIGHT_VIEWER_FILE),
                null,
                $ver
            );
        }
    }
    
    protected static function registerScripts($jquery_plugins)
    {
        foreach($jquery_plugins as $id => $file) {
            $ver = null;
            if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$file)) {
                $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$file);
            }
            
            wp_register_script(
                $id,
                plugins_url($file, PDF_LIGHT_VIEWER_FILE),
                array('jquery'),
                $ver
            );
        }
    }
    
    protected static function registerStyles($styles)
    {
        foreach($styles as $id => $file) {
            $ver = null;
            if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$file)) {
                $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$file);
            }
            
            wp_register_style(
                $id,
                plugins_url($file, PDF_LIGHT_VIEWER_FILE),
                null,
                $ver
            );
        }
    }
	
	public static function admin_head() {
		global $post;
        
		// styles
			static::enqueueStyles(array(
				'purecss.grids.responsive' => 'assets/bower_components/pure/grids-responsive-min.css',
				'purecss.grids.core' => 'assets/bower_components/pure/grids-core-min.css',
				'purecss.forms' => 'assets/bower_components/pure/forms-min.css',
				'jquery.bxslider.css' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.css',
                'jquery.qtip.css' => 'assets/bower_components/qtip2/jquery.qtip.min.css',
				'backend.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/backend.css'
			));
		
		// scripts
			if (
                (isset($_GET['post_type']) && $_GET['post_type'] == PdfLightViewer_PdfController::$type)
                || ($post && $post->post_type == PdfLightViewer_PdfController::$type)
            ) {
                static::enqueueScripts(array(
                    'jquery.scrollstop.js' => 'assets/bower_components/jquery.lazyload/jquery.scrollstop.js',
                    'jquery.lazyload.js' => 'assets/bower_components/jquery.lazyload/jquery.lazyload.js',
                    'hash.turn.js' => 'assets/js/turn.js/hash.js',
                    'screenfull.js' => 'assets/bower_components/screenfull/dist/screenfull.min.js',
                    'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
                    'turn.js' => 'assets/js/turn.js/turn.min.js',
                    'jquery.bxslider.js' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.min.js',
                    'jquery.zoom.js' => 'assets/bower_components/jquery-zoom/jquery.zoom.min.js',
                ));
                
                wp_enqueue_script(
                    'magazine.'.PDF_LIGHT_VIEWER_FILE,
                    plugins_url('assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
                    array('jquery', 'jquery.lazyload.js', 'turn.js'),
                    filemtime(PDF_LIGHT_VIEWER_APPPATH.'/assets/js/magazine.js')
                );
            }
            
            static::enqueueScripts(array(
                'jquery.qtip.js' => 'assets/bower_components/qtip2/jquery.qtip.min.js',
                'admin.'.PDF_LIGHT_VIEWER_FILE => 'assets/js/admin.js'
            ));
			
			// javascript settings
				if (!PdfLightViewer_Model::$unimported) {
					PdfLightViewer_Model::$unimported = PdfLightViewer_Model::getOneUnimported();
				}
				
				
			wp_localize_script('admin.'.PDF_LIGHT_VIEWER_FILE, 'PdfLightViewer', array(
				'url' => array(
					'ajaxurl' => admin_url('admin-ajax.php')
				),
				'flags' => array(
					'ping_import' => (bool)PdfLightViewer_Model::$unimported
				),
				'__' => array(
					'Import process was successfully finished. Please check results on the PDF page.' => __('Import process was successfully finished. Please check results on the PDF page.', PDF_LIGHT_VIEWER_PLUGIN),
					'Import process failed due to the unknown error.' => __('Import process failed due to the unknown error.', PDF_LIGHT_VIEWER_PLUGIN),
					'Import process failed due to the error:' => __('Import process failed due to the error:', PDF_LIGHT_VIEWER_PLUGIN)
				),
                'settings' => array(
                    'enable_hash_nav' => (bool)PdfLightViewer_AdminController::getSetting('enable-hash-nav')
                )
			));
	}
	
	public static function frontendRegister() {
		
        global $post;
        
		// styles
			static::registerStyles(array(
				'jquery.bxslider.css' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.css',
                'jquery.qtip.css' => 'assets/bower_components/qtip2/jquery.qtip.min.css'
			));
            
            wp_register_style(
				'frontend.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('assets/css/frontend.css', PDF_LIGHT_VIEWER_FILE),
				array(
                    'jquery.bxslider.css',
                    'jquery.qtip.css'
                ),
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/assets/css/frontend.css')
			);
		
		// scripts
			static::registerScripts(array(
				'jquery.scrollstop.js' => 'assets/bower_components/jquery.lazyload/jquery.scrollstop.js',
				'jquery.lazyload.js' => 'assets/bower_components/jquery.lazyload/jquery.lazyload.js',
				'hash.turn.js' => 'assets/js/turn.js/hash.js',
				'screenfull.js' => 'assets/bower_components/screenfull/dist/screenfull.min.js',
				'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
				'turn.js' => 'assets/js/turn.js/turn.min.js',
				'jquery.bxslider.js' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.min.js',
				'jquery.zoom.js' => 'assets/bower_components/jquery-zoom/jquery.zoom.min.js',
                'jquery.qtip.js' => 'assets/bower_components/qtip2/jquery.qtip.min.js'
			));
			
			wp_register_script(
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
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
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/assets/js/magazine.js')
			);
            wp_localize_script('magazine.'.PDF_LIGHT_VIEWER_PLUGIN, 'PdfLightViewer', array(
                'settings' => array(
                    'enable_hash_nav' => (bool)PdfLightViewer_AdminController::getSetting('enable-hash-nav')
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
            static::frontendEnqueue();
        }
	}

    public static function frontendEnqueue() {
        wp_enqueue_script('magazine.'.PDF_LIGHT_VIEWER_PLUGIN);
        wp_enqueue_style('frontend.'.PDF_LIGHT_VIEWER_PLUGIN);
    }
}
