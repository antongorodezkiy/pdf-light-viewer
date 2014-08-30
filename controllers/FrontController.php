<?php if (!defined('WPINC')) die();

class PdfLightViewer_FrontController {
	
	public static function init_shortcodes() {
		add_shortcode('pdf-light-viewer', array('PdfLightViewer_FrontController', 'disaply_pdf_book'));
	}
	
	
	public static function disaply_pdf_book($atts = array()) {
		
		ob_start();
		ob_clean();
		
		global
			$post,
			$linked_articles_config;
		
		$original_post = $post;
		if (isset($atts['post_id'])) {
			$post = get_post($atts['post_id']);
		}
		
		if (empty($post) || !$post->ID) {
			return;
		}
		
		$linked_articles_config = self::parseDefaultsSettings($atts, $post);
		
		$connected_post_ids = linkedArticlesModel::getConnectedPostsIds($post->ID, $linked_articles_config['include_the_post']);
		
		if(!empty($connected_post_ids)) {
			
			// the query
				$query_params = array(
				   'post_type' => linkedArticlesAdminController::get_allowed_post_types(),
				   'post__in' =>  $connected_post_ids
				);
				
				$query_params = array_merge($query_params, $linked_articles_config);
			
				$linked_articles_config['query'] = new WP_Query($query_params);
			
			// the loop
				if (locate_template($linked_articles_config['template'].'.php') != '') {
					get_template_part($linked_articles_config['template']);
				}
				else {
					if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/templates/'.$linked_articles_config['template'].'.php')) {
						include(PDF_LIGHT_VIEWER_APPPATH.'/templates/'.$linked_articles_config['template'].'.php');
					}
					else {
						include(PDF_LIGHT_VIEWER_APPPATH.'/templates/shortcode-pdf-light-viewer.php');
					}
				}
				
			// restore original post data
				wp_reset_postdata();
				$post = $original_post;
		}

		return ob_get_clean();
	}
	
	
	public static function parseDefaultsSettings($atts, $post = null) {
		$linked_articles_config = array();
		
		// template
			if (isset($atts['template'])) {
				$linked_articles_config['template'] = $atts['template'];
			}
			else {
				$linked_articles_config['template'] = 'shortcode-pdf-light-viewer';
			}
						
		return $linked_articles_config;
	}
	
	
	public static function generate_template_item_css_classes() {
		global
			$linked_articles_config;
		
		if (!$linked_articles_config['layout_columns']) {
			$linked_articles_config['layout_columns'] = 3;
		}
		
		if (
			$linked_articles_config['layout'] == 'slider'
			|| $linked_articles_config['layout'] == 'vertical_slider'
			|| $linked_articles_config['layout'] == 'slideshow'
			|| $linked_articles_config['layout'] == 'vertical_slideshow'
			|| $linked_articles_config['layout'] == 'carousel'
			|| $linked_articles_config['layout'] == 'ticker'
		) {
			$css_classes = 'slide';
		}
		else {
			$cols = $linked_articles_config['layout_columns'];
			$bootstrap_cols = (12/$cols);
			$css_classes = 'static-linked-article js-static-linked-article pure-u-1-'.$cols.' col-xs-12 col-sm-6 col-md-'.$bootstrap_cols.' col-lg-'.$bootstrap_cols;
		}
		
		return $css_classes;
	}
	
}
