<?php if (!defined('WPINC')) die();
		
class PdfLightViewer_AssetsController {	
	
	public static function admin_head() {
		
		// styles
			$styles = array(
				'purecss.grids.responsive' => 'assets/bower_components/pure/grids-responsive-min.css',
				'purecss.grids.core' => 'assets/bower_components/pure/grids-core-min.css',
				'purecss.forms' => 'assets/bower_components/pure/forms-min.css',
				'font-awesome' => 'assets/bower_components/fontawesome/css/font-awesome.min.css',
				'jquery.bxslider.css' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.css',
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/magazine.css',
				'admin.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/admin.css'
			);
			
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

		
		// scripts
			$jquery_plugins = array(
				'jquery.scrollstop.js' => 'assets/bower_components/jquery.lazyload/jquery.scrollstop.js',
				'jquery.lazyload.js' => 'assets/bower_components/jquery.lazyload/jquery.lazyload.js',
				'hash.turn.js' => 'assets/js/turn.js/hash.js',
				'jquery.fullscreen.js' => 'assets/bower_components/kayahr-jquery-fullscreen-plugin/jquery.fullscreen-min.js',
				'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
				'turn.js' => 'assets/js/turn.js/turn.min.js',
				'jquery.bxslider.js' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.min.js',
				'jquery.zoom.js' => 'assets/bower_components/jquery-zoom/jquery.zoom.min.js'
			);
			
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
			
			wp_enqueue_script(
				'magazine.'.PDF_LIGHT_VIEWER_FILE,
				plugins_url('assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload.js', 'turn.js'),
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/assets/js/magazine.js')
			);
			
			wp_enqueue_script(
				'admin.'.PDF_LIGHT_VIEWER_FILE,
				plugins_url('assets/js/admin.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload.js', 'turn.js'),
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/assets/js/admin.js')
			);
			
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
				)
			));
	}
	
	public static function frontend_head() {
		
		// styles
			$styles = array(
				'font-awesome' => 'assets/bower_components/fontawesome/css/font-awesome.min.css',
				'jquery.bxslider.css' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.css',
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN => 'assets/css/magazine.css'
			);
			
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
		
		// scripts
			$jquery_plugins = array(
				'jquery.scrollstop.js' => 'assets/bower_components/jquery.lazyload/jquery.scrollstop.js',
				'jquery.lazyload.js' => 'assets/bower_components/jquery.lazyload/jquery.lazyload.js',
				'hash.turn.js' => 'assets/js/turn.js/hash.js',
				'jquery.fullscreen.js' => 'assets/bower_components/kayahr-jquery-fullscreen-plugin/jquery.fullscreen-min.js',
				'html4.turn.js' => 'assets/js/turn.js/turn.html4.min.js',
				'turn.js' => 'assets/js/turn.js/turn.min.js',
				'jquery.bxslider.js' => 'assets/bower_components/bxslider-4/dist/jquery.bxslider.min.js',
				'jquery.zoom.js' => 'assets/bower_components/jquery-zoom/jquery.zoom.min.js'
			);
			
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
			
			wp_enqueue_script(
				'pdf-light-viewer-magazine',
				plugins_url('assets/js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload.js', 'turn.js'),
				filemtime(PDF_LIGHT_VIEWER_APPPATH.'/assets/js/magazine.js')
			);

	}

	
}
