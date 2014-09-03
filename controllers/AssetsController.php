<?php if (!defined('WPINC')) die();
		
class PdfLightViewer_AssetsController {	
	
	public static function admin_head() {
		
		// styles
			wp_enqueue_style(
				'purecss.grids.responsive',
				plugins_url('bower_components/pure/grids-responsive-min.css',  PDF_LIGHT_VIEWER_FILE)
			);
			wp_enqueue_style(
				'purecss.grids.core',
				plugins_url('bower_components/pure/grids-core-min.css',  PDF_LIGHT_VIEWER_FILE)
			);
			wp_enqueue_style(
				'purecss.forms',
				plugins_url('bower_components/pure/forms-min.css',  PDF_LIGHT_VIEWER_FILE)
			);
			wp_enqueue_style(
				'font-awesome',
				plugins_url('bower_components/fontawesome/css/font-awesome.min.css',  PDF_LIGHT_VIEWER_FILE)
			);
			
			wp_enqueue_style(
				'jquery.bxslider.css',
				plugins_url('bower_components/bxslider-4/jquery.bxslider.css',  PDF_LIGHT_VIEWER_FILE)
			);
			
			wp_enqueue_style(
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('css/magazine.css',  PDF_LIGHT_VIEWER_FILE)
			);
			
			wp_enqueue_style(
				'admin.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('css/admin.css',  PDF_LIGHT_VIEWER_FILE ),
				array()
			);
		
		// scripts
			wp_enqueue_script(
				'jquery.scrollstop',
				plugins_url('bower_components/jquery.lazyload/jquery.scrollstop.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'jquery.lazyload',
				plugins_url('bower_components/jquery.lazyload/jquery.lazyload.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'hash.turn.js',
				plugins_url('js/turn.js/hash.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);

			
			wp_enqueue_script(
				'jquery.fullscreen.js',
				plugins_url('bower_components/kayahr-jquery-fullscreen-plugin/jquery.fullscreen-min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'html4.turn.js',
				plugins_url('js/turn.js/turn.html4.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'turn.js',
				plugins_url('js/turn.js/turn.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'jquery.bxslider.js',
				plugins_url('bower_components/bxslider-4/jquery.bxslider.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			
			wp_enqueue_script(
				'pdf-light-viewer-magazine',
				plugins_url('js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload', 'turn.js')
			);
			
			// javascript settings
				/*wp_localize_script('pdf-light-viewer-admin', 'PdfLightViewer', array(
					'url' => array(
						'ajaxurl' => admin_url('admin-ajax.php')
					),
					'lang' => array(
						//'Type a few letters from article title...' => __('Type a few letters from article title...', PDF_LIGHT_VIEWER_PLUGIN),
					)
				));*/
			
	}
	
	public static function frontend_head() {
		
		// styles
			wp_enqueue_style(
				'jquery.bxslider.css',
				plugins_url('bower_components/bxslider-4/jquery.bxslider.css',  PDF_LIGHT_VIEWER_FILE)
			);
			
			wp_enqueue_style(
				'magazine.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('css/magazine.css',  PDF_LIGHT_VIEWER_FILE)
			);
			
			wp_enqueue_style(
				'frontend.'.PDF_LIGHT_VIEWER_PLUGIN,
				plugins_url('css/frontend.css',  PDF_LIGHT_VIEWER_FILE)
			);
		
		// scripts
			wp_enqueue_script(
				'jquery.scrollstop',
				plugins_url('bower_components/jquery.lazyload/jquery.scrollstop.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'jquery.lazyload',
				plugins_url('bower_components/jquery.lazyload/jquery.lazyload.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'hash.turn.js',
				plugins_url('js/turn.js/hash.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'jquery.fullscreen.js',
				plugins_url('bower_components/kayahr-jquery-fullscreen-plugin/jquery.fullscreen-min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'html4.turn.js',
				plugins_url('js/turn.js/turn.html4.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			wp_enqueue_script(
				'turn.js',
				plugins_url('js/turn.js/turn.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			
			wp_enqueue_script(
				'jquery.bxslider.js',
				plugins_url('bower_components/bxslider-4/jquery.bxslider.min.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery')
			);
			
			
			wp_enqueue_script(
				'pdf-light-viewer-magazine',
				plugins_url('js/magazine.js', PDF_LIGHT_VIEWER_FILE),
				array('jquery', 'jquery.lazyload', 'turn.js')
			);
			
			// javascript settings
			/*	wp_localize_script('pdf-light-viewer-frontend', 'PdfLightViewer', array(
					'url' => array(
						'ajaxurl' => admin_url('admin-ajax.php')
					),
					'lang' => array(
						//'Type a few letters from article title...' => __('Type a few letters from article title...', PDF_LIGHT_VIEWER_PLUGIN),
					)
				));*/
	}

	
}
