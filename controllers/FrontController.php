<?php if (!defined('WPINC')) die();

class PdfLightViewer_FrontController {
	
	public static function disaply_pdf_book($atts = array()) {
		global $pdf_light_viewer_config;
	
		if (!isset($atts['id']) || !$atts['id']) {
			return;
		}
		
		$post = get_post($atts['id']);
		if (empty($post) || !$post->ID) {
			return;
		}
		
		$pdf_light_viewer_config = self::parseDefaultsSettings($atts, $post);
	
		// download options
			$pdf_light_viewer_config['download_allowed'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'download_allowed', true);
			
			if ($pdf_light_viewer_config['download_allowed']) {
				$pdf_file_id = PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf_file_id', true);
				$pdf_file_url = wp_get_attachment_url($pdf_file_id);
				
				$alternate_download_link = PdfLightViewer_Plugin::get_post_meta($post->ID, 'alternate_download_link', true);
				
				$pdf_light_viewer_config['download_link'] = ($alternate_download_link ? $alternate_download_link : $pdf_file_url);
			}
		
		$pdf_light_viewer_config['hide_thumbnails_navigation'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'hide_thumbnails_navigation', true);
		
		$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post->ID);
		$pdf_upload_dir_url = PdfLightViewer_Plugin::getUploadDirectoryUrl($post->ID);
		
		$pdf_light_viewer_config['pdf_upload_dir_url'] = $pdf_upload_dir_url;
		$pages = directory_map($pdf_upload_dir);
		sort($pages);
		
		$thumbs = directory_map($pdf_upload_dir.'-thumbs');
		sort($thumbs);
		
		// check permissions
			$current_user = wp_get_current_user();
			$current_user_roles = $current_user->roles;
		
			$pages_limits = get_post_meta($post->ID, 'pdf_light_viewer_permissions_metabox_repeat_group', true);
			
			$limit = 0;
			if (!empty($pages_limits)) {
				foreach($pages_limits as $pages_limit) {
					if (empty($current_user_roles) && $pages_limit['pages_limit_user_role'] == 'anonymous') {
						$limit = $pages_limit['pages_limit_visible_pages'];
					}
					else if(in_array($pages_limit['pages_limit_user_role'], $current_user_roles)) {
						$limit = $pages_limit['pages_limit_visible_pages'];
					}
				}
			}
			
		// limit allowed pages for user role
			$pdf_light_viewer_config['pages'] = array();
			$pdf_light_viewer_config['thumbs'] = array();
			if (!$limit) {
				$pdf_light_viewer_config['pages'] = $pages;
				$pdf_light_viewer_config['thumbs'] = $thumbs;
			}
			else {
				for($page = 0; $page < $limit; $page++) {
					$pdf_light_viewer_config['pages'][$page] = $pages[$page];
					$pdf_light_viewer_config['thumbs'][$thumbs] = $thumbs[$page];
				}
			}
			
		
		ob_start();
		ob_clean();
		
		// the loop
			if (locate_template($pdf_light_viewer_config['template'].'.php') != '') {
				get_template_part($pdf_light_viewer_config['template']);
			}
			else {
				if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/templates/'.$pdf_light_viewer_config['template'].'.php')) {
					include(PDF_LIGHT_VIEWER_APPPATH.'/templates/'.$pdf_light_viewer_config['template'].'.php');
				}
				else {
					include(PDF_LIGHT_VIEWER_APPPATH.'/templates/shortcode-pdf-light-viewer.php');
				}
			}
			
	

		return ob_get_clean();
	}
	
	
	public static function parseDefaultsSettings($atts, $post = null) {
		$linked_articles_config = array();
		
		// template
			if (isset($atts['template']) && $atts['template']) {
				$linked_articles_config['template'] = $atts['template'];
			}
			else {
				$linked_articles_config['template'] = 'shortcode-pdf-light-viewer';
			}
		
		// download_link
			if (isset($atts['download_link']) && $atts['download_link']) {
				$linked_articles_config['download_link'] = $atts['download_link'];
			}
			else {
				$linked_articles_config['download_link'] = '';
			}	
			
						
		return $linked_articles_config;
	}
	
	
	public static function generate_template_item_css_classes() {
		$css_classes = '';
		return $css_classes;
	}
	
}
