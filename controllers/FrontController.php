<?php if (!defined('WPINC')) die();

class PdfLightViewer_FrontController {
    
    public static function init() {
		// toolbar
			add_action(PDF_LIGHT_VIEWER_PLUGIN.':shortcode_template_top_panel', array(__CLASS__, 'shortcode_template_top_panel'), 101, 1);
	}
    
    public static function shortcode_template_top_panel($post_id) {
		include PDF_LIGHT_VIEWER_APPPATH.'/views/shortcode-top-panel.php';
	}
	
	public static function getConfig($atts, $post) {
		$pdf_light_viewer_config = self::parseDefaultsSettings($atts, $post);
	
		// download options
			if ($pdf_light_viewer_config['download_allowed']) {
				$pdf_file_id = PdfLightViewer_Model::getPDFFileId($post->ID);
				$pdf_file_url = wp_get_attachment_url($pdf_file_id);
				
				$alternate_download_link = PdfLightViewer_Plugin::get_post_meta($post->ID, 'alternate_download_link', true);
				
				$pdf_light_viewer_config['download_link'] = ($alternate_download_link ? $alternate_download_link : $pdf_file_url);
			}
		
		$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post->ID);
		$pdf_upload_dir_url = PdfLightViewer_Plugin::getUploadDirectoryUrl($post->ID);
		
		$pdf_light_viewer_config['pdf_upload_dir_url'] = $pdf_upload_dir_url;
		
		$pages = directory_map($pdf_upload_dir);
		$thumbs = directory_map($pdf_upload_dir.'-thumbs');
        $pdfPages = directory_map($pdf_upload_dir.'-pdfs');
		
		if (empty($pages) || empty($thumbs)) {
			echo '
                <div style="color: Salmon">'.__('[pdf-light-viewer] shortcode cannot be rendered due to the error: No converted pages found.',PDF_LIGHT_VIEWER_PLUGIN).'</div>
                <div style="color: Salmon">'.sprintf(__('If PDF has been already converted, then probably there were some errors during the import. Please check <a href="%s" target="_blank">plugin error log on the settings page</a> and your site error log for errors.',PDF_LIGHT_VIEWER_PLUGIN), PdfLightViewer_Plugin::getSettingsUrl()).'</div>
            ';
			return;
		}
		
		sort($pages);
		sort($thumbs);
		
		// check permissions
			$current_user = wp_get_current_user();
			$current_user_roles = $current_user->roles;
		
			$pages_limits = PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf_light_viewer_permissions_metabox_repeat_group', true);
			
			$limit = 0;
            $visiblePages = array();
			if (!empty($pages_limits)) {
				foreach($pages_limits as $pages_limit) {
					if (empty($current_user_roles) && $pages_limit['pages_limit_user_role'] == 'anonymous') {
						$limit = isset($pages_limit['pages_limit_visible_pages'])
                            ? $pages_limit['pages_limit_visible_pages']
                            : 0;
                            
                        // TODO: not implemented yet
                        //$visiblePages = isset($pages_limit['pages_limit_visible_pages_ranges'])
                        //    ? PdfLightViewer_PdfController::parsePages($pages_limit['pages_limit_visible_pages_ranges'])
                        //    : array();
					}
					else if(in_array($pages_limit['pages_limit_user_role'], $current_user_roles)) {
						$limit = isset($pages_limit['pages_limit_visible_pages'])
                            ? $pages_limit['pages_limit_visible_pages']
                            : 0;
                            
                        // TODO: not implemented yet
                        //$visiblePages = isset($pages_limit['pages_limit_visible_pages_ranges'])
                        //    ? PdfLightViewer_PdfController::parsePages($pages_limit['pages_limit_visible_pages_ranges'])
                        //    : array();
					}
				}
			}
			
		// limit allowed pages for user role
			if (!$limit /*&& empty($visiblePages)*/) {
				$pdf_light_viewer_config['pages'] = $pages;
				$pdf_light_viewer_config['thumbs'] = $thumbs;
                
                // TODO: not implemented yet
                //for($page = 0; $page < count($pdf_light_viewer_config['pages']); $page++) {
                //    $pdf_light_viewer_config['pagesIndexes'][$page] = $page;
                //}
			}
			else {
                
                if (!empty($visiblePages)) {
                    foreach($visiblePages as $page) {
                        $i = $page - 1;
                        $pdf_light_viewer_config['pages'][$i] = $pages[$i];
                        $pdf_light_viewer_config['thumbs'][$i] = $thumbs[$i];
                        
                        // TODO: not implemented yet
                        //$pdf_light_viewer_config['pagesIndexes'][$i] = $i;
                    }
                }
				else {
                    for($page = 0; $page < $limit; $page++) {
                        $pdf_light_viewer_config['pages'][$page] = $pages[$page];
                        $pdf_light_viewer_config['thumbs'][$page] = $thumbs[$page];
                        
                        // TODO: not implemented yet
                        //$pdf_light_viewer_config['pagesIndexes'][$page] = $page;
                    }
                }
			}
			
		$pdf_light_viewer_config = apply_filters(PDF_LIGHT_VIEWER_PLUGIN.':front_config', $pdf_light_viewer_config, $post);
		
		return $pdf_light_viewer_config;
	}
	
	public static function display_pdf_book($atts = array()) {
		global $pdf_light_viewer_config;
        
        PdfLightViewer_AssetsController::frontendEnqueue();
	
		if (!isset($atts['id']) || !$atts['id']) {
			return;
		}
		
		$post = get_post($atts['id']);
        
		if (
            empty($post)
            || !$post->ID
            || $post->post_type != PdfLightViewer_PdfController::$type
            || !(
                $post->post_status == 'publish'
                || (
                    $post->post_status == 'private'
                    && current_user_can('read_private_posts', $post)
                )
            )
        ) {
			return;
		}
        
        if (post_password_required($post)) {
            return '<div class="pdf-light-viewer">'.get_the_password_form().'</div>';
        }
		
		$pdf_light_viewer_config = static::getConfig($atts, $post);
		
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
			
	

		return str_ireplace(array("\n", "\r"), ' ', ob_get_clean());
	}
	
	
	public static function parseDefaultsSettings($args, $post = null) {
		$defaults = array(
            'title' => $post->post_title,
			'template' => 'shortcode-pdf-light-viewer',
			'download_link' => '',
			'download_allowed' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'download_allowed', true),
            'download_page_allowed' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'download_page_allowed', true),
            'download_page_format' => PdfLightViewer_Plugin::get_post_meta($post->ID, 'download_page_format', true),
			'hide_thumbnails_navigation' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'hide_thumbnails_navigation', true),
			'hide_fullscreen_button' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'hide_fullscreen_button', true),
			'disable_page_zoom' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'disable_page_zoom', true),
            'zoom_magnify' => (float)PdfLightViewer_Plugin::get_post_meta($post->ID, 'zoom_magnify', true),
            'show_toolbar_next_previous' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'show_toolbar_next_previous', true),
            'show_toolbar_goto_page' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'show_toolbar_goto_page', true),
            'show_page_numbers' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'show_page_numbers', true),
			'page_width' => PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf-page-width', true),
			'page_height' => PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf-page-height', true),
            'page_layout' => PdfLightViewer_Plugin::get_post_meta($post->ID, 'page_layout', true),
            'max_book_width' => (int)PdfLightViewer_Plugin::get_post_meta($post->ID, 'max_book_width', true),
            'max_book_height' => (int)PdfLightViewer_Plugin::get_post_meta($post->ID, 'max_book_height', true),
            'limit_fullscreen_book_height' => (bool)PdfLightViewer_Plugin::get_post_meta($post->ID, 'limit_fullscreen_book_height', true),
		
			'pages' => array(),
			'thumbs' => array(),
		
			'print_allowed' => false,
			'enabled_pdf_text' => false,
			'enabled_pdf_search' => false,
			'enabled_archive' => false
		);
		
		return wp_parse_args($args, $defaults);
	}
	
	
	public static function generate_template_item_css_classes() {
		$css_classes = '';
		return $css_classes;
	}
	
	
	public static function getPageLink($number) {
		$url = apply_filters(PDF_LIGHT_VIEWER_PLUGIN.':front_get_page_link', $number);
		
		if (!$url || $url == $number) {
			$url = '#page/'.$number;
		}
		
		return $url;
	}
    
    public static function getPageDownloadLink($pageFile) {
		global $pdf_light_viewer_config;
        
        $url = '';
		if ($pageFile) {
            switch($pdf_light_viewer_config['download_page_format']) {
                case 'pdf':
                    $pageFile = mb_substr($pageFile, 0, -3);
                    $pageFile .= 'pdf';
                    $url = $pdf_light_viewer_config['pdf_upload_dir_url'].'-pdfs/'.$pageFile;
                    break;
                
                default:
                    $url = $pdf_light_viewer_config['pdf_upload_dir_url'].'/'.$pageFile;
            }
		}
		
		return $url;
	}
    
    public static function getPageDownloadTitle($pageFile) {
		global $pdf_light_viewer_config;
        
        $title = '';
		if ($pageFile) {
            switch($pdf_light_viewer_config['download_page_format']) {
                case 'pdf':
                    $pageFile = mb_substr($pageFile, 0, -3);
                    $pageFile .= 'pdf';
                    $title = htmlspecialchars($pdf_light_viewer_config['title']).' '.$pageFile;
                    break;
                
                default:
                    $title = htmlspecialchars($pdf_light_viewer_config['title']).' '.$pageFile;
            }
		}
		
		return $title;
	}
}
