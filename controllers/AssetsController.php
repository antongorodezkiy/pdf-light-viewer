<?php if (!defined('WPINC')) die();
		
class PdfLightViewer_AssetsController {	
	
	public static function admin_head() {
		
		// styles
			$styles = array(
				'purecss.grids.responsive' => 'assets/bower_components/pure/grids-responsive-min.css',
				'purecss.grids.core' => 'assets/bower_components/pure/grids-core-min.css',
				'purecss.forms' => 'assets/bower_components/pure/forms-min.css',
				'font-awesome' => 'assets/bower_components/fontawesome/css/font-awesome.min.css',
				'jquery.bxslider.css' => 'assets/bower_components/bxslider-4/jquery.bxslider.css',
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/magazine.css',
				'admin.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/admin.css'
			);
			
			foreach($styles as $id => $file) {
				wp_enqueue_style(
					$id,
					plugins_url($file, PDF_LIGHT_VIEWER_FILE)
				);
			}

		
		// scripts
			$jquery_plugins = array(
				'jquery.scrollstop' => 'assets/bower_components/jquery.lazyload/jquery.scrollstop.min.js',
				'jquery.lazyload' => 'assets/bower_components/jquery.lazyload/jquery.lazyload.min.js',
				'hash.turn.js' => 'assets/js/turn.js/hash.js',
				'jquery.fullscreen.js' => 'assets/bower_components/kayahr-jquery-fullscreen-plugin/jquery.fullscreen-min.js',
				'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
				'turn.js' => 'assets/js/turn.js/turn.min.js',
				'jquery.bxslider.js' => 'assets/bower_components/bxslider-4/jquery.bxslider.min.js'
			);
			
			foreach($jquery_plugins as $id => $file) {
				wp_enqueue_script(
					$id,
					plugins_url($file, PDF_LIGHT_VIEWER_FILE),
					array('jquery')
				);
			}
			
			wp_enqueue_script(
				'magazine.'.PDF_LIGHT_VIEWER_FILE,
				plugins_url('assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload', 'turn.js')
			);
			
			wp_enqueue_script(
				'admin.'.PDF_LIGHT_VIEWER_FILE,
				plugins_url('assets/js/admin.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload', 'turn.js')
			);
			
			// javascript settings
				if (!PdfLightViewer_Model::$unimported) {
					PdfLightViewer_Model::$unimported = PdfLightViewer_Model::getOneUnimported();
				}
				
				if (!empty(PdfLightViewer_Model::$unimported)) {
					wp_localize_script('admin.'.PDF_LIGHT_VIEWER_FILE, 'PdfLightViewer', array(
						'url' => array(
							'ajaxurl' => admin_url('admin-ajax.php')
						),
						'flags' => array(
							'ping_import' => true
						)
					));
				}
			
	}
	
	public static function frontend_head() {
		
		// styles
			$styles = array(
				'font-awesome' => 'assets/bower_components/fontawesome/css/font-awesome.min.css',
				'jquery.bxslider.css' => 'assets/bower_components/bxslider-4/jquery.bxslider.css',
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/magazine.css',
				'frontend.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/frontend.css'
			);
			
			foreach($styles as $id => $file) {
				wp_enqueue_style(
					$id,
					plugins_url($file, PDF_LIGHT_VIEWER_FILE)
				);
			}
		
		// scripts
			$jquery_plugins = array(
				'jquery.scrollstop' => 'assets/bower_components/jquery.lazyload/jquery.scrollstop.min.js',
				'jquery.lazyload' => 'assets/bower_components/jquery.lazyload/jquery.lazyload.min.js',
				'hash.turn.js' => 'assets/js/turn.js/hash.js',
				'jquery.fullscreen.js' => 'assets/bower_components/kayahr-jquery-fullscreen-plugin/jquery.fullscreen-min.js',
				'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
				'turn.js' => 'assets/js/turn.js/turn.min.js',
				'jquery.bxslider.js' => 'assets/bower_components/bxslider-4/jquery.bxslider.min.js'
			);
			
			foreach($jquery_plugins as $id => $file) {
				wp_enqueue_script(
					$id,
					plugins_url($file, PDF_LIGHT_VIEWER_FILE),
					array('jquery')
				);
			}
			
			wp_enqueue_script(
				'pdf-light-viewer-magazine',
				plugins_url('assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload', 'turn.js')
			);

	}

	
}
