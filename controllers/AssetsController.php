<?php if (!defined('WPINC')) die();
		
class PdfLightViewer_AssetsController {	
	
	public static function admin_head() {
		
		// styles
			wp_enqueue_style(
				'purecss.grids',
				plugins_url('bower_components/yahoo-pure/src/grids/css/grids-core.css',  PDF_LIGHT_VIEWER_FILE)
			);
			wp_enqueue_style(
				'purecss.forms',
				plugins_url('bower_components/yahoo-pure/src/forms/css/forms.css',  PDF_LIGHT_VIEWER_FILE)
			);
			wp_enqueue_style(
				'purecss.forms-r',
				plugins_url('bower_components/yahoo-pure/src/forms/css/forms-r.css',  PDF_LIGHT_VIEWER_FILE)
			);
			wp_enqueue_style(
				'font-awesome',
				plugins_url('bower_components/fontawesome/css/font-awesome.min.css',  PDF_LIGHT_VIEWER_FILE)
			);
			
			wp_enqueue_style('admin.'.PDF_LIGHT_VIEWER_PLUGIN, plugins_url('css/admin.css',  PDF_LIGHT_VIEWER_FILE ), array('purecss.grids'));
		
		// scripts
			wp_enqueue_script(
				'jquery.scrollstop',
				plugins_url('bower_components/tuupola-jquery_lazyload/jquery.scrollstop.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'jquery.lazyload',
				plugins_url('bower_components/tuupola-jquery_lazyload/jquery.lazyload.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'turn.js',
				plugins_url('bower_components/turn.js/turn.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'pdf-light-viewer-admin',
				plugins_url('js/admin.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload', 'turn.js')
			);
			
			// javascript settings
				wp_localize_script('pdf-light-viewer-admin', 'PdfLightViewer', array(
					'url' => array(
						'ajaxurl' => admin_url('admin-ajax.php')
					),
					'lang' => array(
						/*'Type a few letters from article title...' => __('Type a few letters from article title...', PDF_LIGHT_VIEWER_PLUGIN),*/
					)
				));
			
	}
	
	public static function frontend_head() {
		
		// styles
			wp_enqueue_style('frontend.'.PDF_LIGHT_VIEWER_PLUGIN, plugins_url('css/frontend.css',  PDF_LIGHT_VIEWER_FILE ), array('purecss.grids'));
		
		// scripts
			wp_enqueue_script(
				'jquery.scrollstop',
				plugins_url('bower_components/tuupola-jquery_lazyload/jquery.scrollstop.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'jquery.lazyload',
				plugins_url('bower_components/tuupola-jquery_lazyload/jquery.lazyload.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'turn.js',
				plugins_url('bower_components/turn.js/turn.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'pdf-light-viewer-frontend',
				plugins_url('js/frontend.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery.lazyload', 'turn.js')
			);
			
			// javascript settings
				wp_localize_script('pdf-light-viewer-frontend', 'PdfLightViewer', array(
					'url' => array(
						'ajaxurl' => admin_url('admin-ajax.php')
					),
					'lang' => array(
						/*'Type a few letters from article title...' => __('Type a few letters from article title...', PDF_LIGHT_VIEWER_PLUGIN),*/
					)
				));
	}

	
}
