<?php
	
class PdfLightViewer_PdfController {

	const STATUS_SCHEDULED = 'scheduled';
	const STATUS_STARTED = 'started';
	const STATUS_PROCESSING = 'processing';
	const STATUS_CLI_PROCESSING = 'cli_processing';
	const STATUS_FINISHED = 'finished';
	const STATUS_FAILED = 'failed';
	
	public static $type = 'pdf_lv';
    
    public static function getImagickVersion() {
        
        $Imagick = PdfLightViewer_Plugin::getXMagick();
        
        if (class_exists('Imagick') && $Imagick instanceof Imagick) {
            $v = Imagick::getVersion();
            preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $v['versionString'], $v);
        }
        else if (class_exists('Gmagick') && $Imagick instanceof Gmagick) {
            $v = $Imagick->getVersion();
            preg_match('/GraphicsMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $v['versionString'], $v);
        }
        
        return isset($v[1]) ? $v[1] : null;
    }
    
    public static function parsePages($pagesString)
    {
        $pages = array();
        
        $pagesExploded = explode(',', $pagesString);
        foreach($pagesExploded as $someRange) {
            if (strpos($someRange, '-')) {
                list($start, $end) = explode('-', $someRange);
                for($i = (int)$start; $i <= (int)$end; $i++) {
                    $pages[] = $i;
                }
            }
            else {
                $pages[] = (int)$someRange;
            }
        }
        
        return $pages;
    }
	
	public static function init() {
		
		self::register();
		
		// columns
			add_filter( 'manage_edit-'.self::$type.'_columns', array(__CLASS__, 'custom_columns_registration'), 10 );
			add_action( 'manage_'.self::$type.'_posts_custom_column', array(__CLASS__, 'custom_columns_views'), 10, 2 );
		
		// metaboxes
			add_filter('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
			add_filter('cmb2_meta_boxes', array(__CLASS__, 'cmb_metaboxes'));
		
		// saving
			add_action('save_post_'.self::$type, array(__CLASS__, 'save_post'), 1000);
		
		// show import message, which will show progress
			add_action('admin_notices', array(__CLASS__,'showImportProgressMessages'));
		
		// delete generated images
			add_action('deleted_post', array(__CLASS__, 'deleted_post'));
	}
	
	public static function register() {
		global $pagenow;
		
		register_post_type(self::$type,
			array(
				'labels' => array(
					'name' => __('PDFs', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => __('PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => __('Import PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new' => __('Import PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'edit' => __('Edit', PDF_LIGHT_VIEWER_PLUGIN),
					'edit_item' => __('Edit PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'new_item' => __('New PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'view' => __('View', PDF_LIGHT_VIEWER_PLUGIN),
					'view_item' => __('View PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'search_items' => __('Search PDFs', PDF_LIGHT_VIEWER_PLUGIN),
					'not_found' => __('No PDFs found', PDF_LIGHT_VIEWER_PLUGIN),
				),
				'description' => __('For PDFs', PDF_LIGHT_VIEWER_PLUGIN),
				'public' => false,
				'show_ui' => (bool)PdfLightViewer_AdminController::getSetting('show-post-type'),
				'_builtin' => false,
				'capability_type' => 'post',
				'menu_icon' => plugins_url('assets/img/pdf.png', PDF_LIGHT_VIEWER_FILE),
				'hierarchical' => false,
				'map_meta_cap' => true,
				'supports' => array('title', 'thumbnail'/*, 'custom-fields'*/),
			)
		);
        
        register_taxonomy(self::$type.'_category', self::$type, 
			array( 
				'hierarchical' => true, 
				'labels' => array(
					'name' => __('Categories', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => __('Category', PDF_LIGHT_VIEWER_PLUGIN),
					'search_items' =>  __('Search in Category', PDF_LIGHT_VIEWER_PLUGIN),
					'popular_items' => __('Popular Categories', PDF_LIGHT_VIEWER_PLUGIN),
					'all_items' => __('All Categories', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item' => __('Parent Category', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item_colon' => __('Parent Category:', PDF_LIGHT_VIEWER_PLUGIN),
					'edit_item' => __('Edit Category', PDF_LIGHT_VIEWER_PLUGIN),
					'update_item' => __('Update Category', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => __('Add New Category', PDF_LIGHT_VIEWER_PLUGIN),
					'new_item_name' => __('New Category Name', PDF_LIGHT_VIEWER_PLUGIN)
				),
				'public' => false,
				'show_ui' => (bool)PdfLightViewer_AdminController::getSetting('show-post-type'),
				'query_var' => false, 
				'show_admin_column' => true
			) 
		);
		
		
		register_taxonomy(self::$type.'_tag', self::$type, 
			array( 
				'hierarchical' => false, 
				'labels' => array(
					'name' => __('Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => __('Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'search_items' =>  __('Search in Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'popular_items' => __('Popular Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'all_items' => __('All Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item' => __('Parent Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item_colon' => __('Parent Tag:', PDF_LIGHT_VIEWER_PLUGIN),
					'edit_item' => __('Edit Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'update_item' => __('Update Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => __('Add New Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'new_item_name' => __('New Tag Name', PDF_LIGHT_VIEWER_PLUGIN)
				),
				'public' => false,
				'show_ui' => (bool)PdfLightViewer_AdminController::getSetting('show-post-type'),
				'query_var' => false, 
				'show_admin_column' => true
			) 
		);

	}
	
	// after import started
		public static function showImportProgressMessages() {
			if (!PdfLightViewer_Model::$unimported) {
				PdfLightViewer_Model::$unimported = PdfLightViewer_Model::getOneUnimported();
			}
				
			if (!empty(PdfLightViewer_Model::$unimported)) {
				$status = PdfLightViewer_Plugin::get_post_meta(PdfLightViewer_Model::$unimported->ID,'_pdf-light-viewer-import-status', true);
				$progress = PdfLightViewer_Plugin::get_post_meta(PdfLightViewer_Model::$unimported->ID,'_pdf-light-viewer-import-progress', true);
	
				PdfLightViewer_AdminController::showDirectMessage(sprintf(
					__('<i class="icons slicon-settings"></i> <b>%s</b> PDF import is <span class="js-pdf-light-viewer-current-status">%s</span>. <span class="js-pdf-light-viewer-current-progress">%d</span>%% is complete. <i>Please do not leave the admin interface until the import would not finished. %s</i>',PDF_LIGHT_VIEWER_PLUGIN)
                    . '<a class="button-secondary js-pdf-light-viewer-cancel-import" href="#">'.__('Cancel', PDF_LIGHT_VIEWER_PLUGIN).'</a>',
					PdfLightViewer_Model::$unimported->post_title,
					$status,
					$progress,
					'<a href="#!" class="js-tip tip" title="'.__('Otherwise the import will be continued during your next visit.', PDF_LIGHT_VIEWER_PLUGIN).'"><span class="icons slicon-question"></span></a>'
				), false);
			}
		}
	
	public static function custom_columns_registration( $defaults ) {
		$defaults['preview'] = __('Preview', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['usage'] = __('Usage', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['pages'] = __('Pages',PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['import_status'] = __('Import status',PDF_LIGHT_VIEWER_PLUGIN);
		return $defaults;
	}
	
	public static function custom_columns_views($column_name, $post_id) {
	 		
		switch($column_name) {
			case 'usage':
				$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post_id);
				$pages = directory_map($pdf_upload_dir);
				include(PDF_LIGHT_VIEWER_APPPATH.'/views/metabox/usage.php');
			break;
		
			case 'preview':
				if (has_post_thumbnail($post_id)) {
					$image_array = wp_get_attachment_image_src(get_post_thumbnail_id( $post_id ), 'full');
					$full_img_url = $image_array[0];
					?>
						<img class="pdf-light-viewer-dashboard-page-preview" src="<?php echo $full_img_url;?>" alt="<?php echo __('Preview', PDF_LIGHT_VIEWER_PLUGIN);?>" />
					<?php
				}
			break;
			
			case 'pages':
				$dir = PdfLightViewer_Plugin::getUploadDirectory($post_id);
				$directory_map = directory_map($dir);
				
				$pdf_pages_number = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-pages-number',true);
				
				if (!empty($directory_map)) {
					$count = count($directory_map);
				}
				else {
					$count = 0;
				}
				?>
					<?php echo $count; ?> / <?php echo $pdf_pages_number;?>
				<?php
			break;
			
			case 'import_status':
				$status = PdfLightViewer_Plugin::get_post_meta($post_id,'_pdf-light-viewer-import-status',true);
				$progress = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'_pdf-light-viewer-import-progress',true);
				
				switch($status) {
					case static::STATUS_SCHEDULED:
						$status_label = __('Import scheduled',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case static::STATUS_STARTED:
						$status_label = __('Import started',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case static::STATUS_PROCESSING:
						$status_label = __('Import in progress',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case static::STATUS_FINISHED:
						$status_label = __('Import finished',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case static::STATUS_FAILED:
						$status_label = __('Import failed',PDF_LIGHT_VIEWER_PLUGIN);
					break;
                
                    default:
                        $status_label = __('Import status unknown',PDF_LIGHT_VIEWER_PLUGIN);
				}
				
				?>
					<div><?php echo $status_label ?></div>
					<div><?php echo $progress ?>%</div>
				<?php
			break;
			
		}
	}
	
	public static function cmb_metaboxes($meta_boxes) {
		
		$meta_boxes['pdf_light_viewer_file_metabox'] = array(
			'id' => 'pdf_light_viewer_file_metabox',
			'title' => __('PDF', PDF_LIGHT_VIEWER_PLUGIN),
			'object_types' => array(self::$type), // post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
				array(
					'name' => __('Enable import', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => '<b>'.__('Check to import or re-import PDF file', PDF_LIGHT_VIEWER_PLUGIN).'</b>',
					'id' => 'enable_pdf_import',
					'type' => 'checkbox'
				),
                array(
					'name' => __('Import pages', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => __('Leave empty to import all. Use numbers for single pages or (e.g. 1-3) for ranges. Few numbers or ranges could be separated by commas (e.g. 2-5,7,9-15).', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'import_pages',
					'type' => 'text',
					'default' => ''
				),
				array(
					'name' => __('JPEG compression quality', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => __('Affects quality and size of resulting page images. Bigger value means better quality and bigger size; also will take more server resources during the import process.', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'jpeg_compression_quality',
					'type' => 'text',
					'default' => 60
				),
				array(
					'name' => __('JPEG resolution', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => __('Affects quality and size of resulting page images. Bigger value means better quality and bigger size; also will take more server resources during the import process.', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'jpeg_resolution',
					'type' => 'text',
					'default' => 300
				),
                array(
					'name' => __('Output biggest side', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => __('Also affects quality and size of resulting page images. Bigger value means better quality and bigger size; also will take more server resources during the import process.', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'output_biggest_side',
					'type' => 'text',
					'default' => 1024
				),
				array(
					'name' => __('PDF File', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('Choose what PDF file will be imported. Also will be used as default link for downloading if download option is enabled.', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'pdf_file',
					'type' => 'file',
                    'readonly' => 'readonly',
                    'options' => array(
                        'url' => false, // Hide the text input for the url
                    )
				)
			),
		);
		
		
		$meta_boxes['pdf_light_viewer_options_metabox'] = array(
			'id' => 'pdf_light_viewer_options_metabox',
			'title' => __('Output Options', PDF_LIGHT_VIEWER_PLUGIN),
			'object_types' => array(self::$type), // post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
                array(
					'name' => '<i class="slicons slicon-directions"></i> ' . __('Hide thumbnail navigation', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'hide_thumbnails_navigation',
					'type' => 'checkbox'
				),
                array(
					'name' => '<i class="slicons slicon-book-open"></i> ' . __('Flipbook page layout', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'page_layout',
					'type'    => 'select',
                    'options' => array(
                        'adaptive' => __('Adaptive', PDF_LIGHT_VIEWER_PLUGIN),
                        'single' => __('Single', PDF_LIGHT_VIEWER_PLUGIN),
                        'double' => __('Double', PDF_LIGHT_VIEWER_PLUGIN)
                    ),
                    'default' => 'adaptive',
				),
                array(
					'name' => '<i class="slicons slicon-frame"></i> ' . __('Max book width', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => '(px)',
					'id' => 'max_book_width',
					'type' => 'text'
				),
                array(
					'name' => '<i class="slicons slicon-frame"></i> ' . __('Max book height', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => '(px)',
					'id' => 'max_book_height',
					'type' => 'text'
				),
                array(
					'name' => '<i class="slicons slicon-frame"></i> ' . __('Limit book height by the viewport in fullscreen mode', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => '',
					'id' => 'limit_fullscreen_book_height',
					'type' => 'checkbox'
				),
			),
		);
        
        $meta_boxes['pdf_light_viewer_toolbar_options_metabox'] = array(
			'id' => 'pdf_light_viewer_toolbar_options_metabox',
			'title' => __('Toolbar Options', PDF_LIGHT_VIEWER_PLUGIN),
			'object_types' => array(self::$type), // post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
				array(
					'name' => '<i class="slicons slicon-cloud-download"></i> ' . __('Allow download', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('Check this if you want to show download button on the frontend', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'download_allowed',
					'type' => 'checkbox'
				),
				array(
					'name' => '<i class="slicons slicon-link"></i> ' . __('Alternate download link', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('If not set, will be used link from PDF File', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'alternate_download_link',
					'type' => 'text',
					'default' => ''
				),
                array(
					'name' => '<i class="slicons slicon-cloud-download"></i> ' . __('Allow per-page download', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('Check this if you want to show download button in the thumbnails to allow downloading of single page images', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'download_page_allowed',
					'type' => 'checkbox'
				),
                array(
					'name' => '' . __('Per-page download format', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('Per page download in JPG or PDF formats', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'download_page_format',
					'type'    => 'select',
                    'options' => array(
                        'jpg' => 'jpg',
                        'pdf' => 'pdf'
                    ),
                    'default' => 'jpg',
				),
				array(
					'name' => '<i class="slicons slicon-size-fullscreen"></i> ' . __('Hide fullscreen button', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'hide_fullscreen_button',
					'type' => 'checkbox'
				),
				array(
					'name' => '<i class="slicons slicon-magnifier"></i> ' . __('Disable page zoom', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'disable_page_zoom',
					'type' => 'checkbox'
				),
                array(
					'name' => '<i class="slicons slicon-magnifier"></i> ' . __('Zoom magnify multiplier', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => __('This value is multiplied against the full size of the zoomed image. The default value is 1, meaning the zoomed image should be at 100% of its natural width and height.', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'zoom_magnify',
					'type' => 'text',
					'default' => 1
				),
                array(
					'name' => '<i class="slicons slicon-arrow-left"></i><i class="slicons slicon-arrow-right"></i> ' . __('Show toolbar next and previous page arrows', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'show_toolbar_next_previous',
					'type' => 'checkbox'
				),
                array(
					'name' => '<i class="slicons slicon-directions"></i> ' . __('Show toolbar go to page control', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'show_toolbar_goto_page',
					'type' => 'checkbox'
				),
                array(
					'name' => '<i class="slicons slicon-info"></i> ' . __('Show page numbers', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'show_page_numbers',
					'type' => 'checkbox'
				),
			),
		);
		
		// user roles pages limits
		if ( !function_exists('get_editable_roles') ) {
			require_once(ABSPATH.'/wp-admin/includes/user.php');
		}
		
		$editable_roles = get_editable_roles();
		$roles = array(
			'anonymous' => __('Anonymous / s2Member Level 0', PDF_LIGHT_VIEWER_PLUGIN)
		);
		
		foreach($editable_roles as $role_id => $editable_role) {
			$roles[$role_id] = $editable_role['name'];
		}
		
		$meta_boxes['pdf_light_viewer_permissions_metabox'] = array(
			'id' => 'pdf_light_viewer_permissions_metabox',
			'title' => __('Permissions', PDF_LIGHT_VIEWER_PLUGIN),
			'object_types' => array(self::$type), // post type
			'context' => 'advanced',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
				array(
					'id'          => 'pdf_light_viewer_permissions_metabox_repeat_group',
					'type'        => 'group',
					'description' => __('Pages limits for different user roles', PDF_LIGHT_VIEWER_PLUGIN),
					'options'     => array(
						'group_title'   => __('Pages Limit', PDF_LIGHT_VIEWER_PLUGIN), // since version 1.1.4, {#} gets replaced by row number
						'add_button'    => __('Add another limit', PDF_LIGHT_VIEWER_PLUGIN),
						'remove_button' => __('Remove limit', PDF_LIGHT_VIEWER_PLUGIN),
						'sortable'      => false, // beta
					),
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'fields'      => array(
						array(
							'name'    => __('User Role', PDF_LIGHT_VIEWER_PLUGIN),
							'desc'    => __('Select role which you want to pages limit', PDF_LIGHT_VIEWER_PLUGIN),
							'id'      => 'pages_limit_user_role',
							'type'    => 'select',
							'options' => $roles,
							'default' => '',
						),
						array(
							'name' => __('Visible pages limit', PDF_LIGHT_VIEWER_PLUGIN),
                            'desc' => __('Use for the higher limit. Has lower priority than "Visible pages"', PDF_LIGHT_VIEWER_PLUGIN),
							'id'   => 'pages_limit_visible_pages',
							'type' => 'text'
						),
//                        array(
//							'name' => __('Visible pages', PDF_LIGHT_VIEWER_PLUGIN),
//                            'desc' => __('Leave empty to show all. Use numbers for single pages or (e.g. 1-3) for ranges. Few numbers or ranges could be separated by commas (e.g. 2-5,7,9-15).', PDF_LIGHT_VIEWER_PLUGIN),
//							'id'   => 'pages_limit_visible_pages_ranges',
//							'type' => 'text'
//						)
					)
				)
			)
		);
	
		return $meta_boxes;
	}
	
    public static function isToolbarVisible($pdf_light_viewer_config) {
        return (
            $pdf_light_viewer_config['download_allowed']
            || $pdf_light_viewer_config['download_page_allowed']
            || !$pdf_light_viewer_config['hide_fullscreen_button']
            || !$pdf_light_viewer_config['disable_page_zoom']
            || !empty($pdf_light_viewer_config['print_allowed'])
            || !empty($pdf_light_viewer_config['print_page_allowed'])
            || !empty($pdf_light_viewer_config['enabled_archive'])
            || !empty($pdf_light_viewer_config['enabled_pdf_search'])
            || !empty($pdf_light_viewer_config['show_page_numbers'])
            || !empty($pdf_light_viewer_config['show_toolbar_next_previous'])
            || !empty($pdf_light_viewer_config['show_toolbar_goto_page'])
        );
    }
	
	public static function add_meta_boxes() {
		global $pagenow;
		
		if ($pagenow != 'post-new.php') {
			// preview
				add_meta_box(
					'pdf_light_viewer_dashboard_preview',
					__('PDF Preview', PDF_LIGHT_VIEWER_PLUGIN),
					array(__CLASS__, 'metabox_dashboard_preview'),
					self::$type,
					'advanced',
					'low',
					array()
				);
				
			// usage
				add_meta_box(
					'pdf_light_viewer_dashboard_usage',
					__('Usage', PDF_LIGHT_VIEWER_PLUGIN),
					array(__CLASS__, 'metabox_dashboard_usage'),
					self::$type,
					'advanced',
					'high',
					array()
				);
		}
	}
	
	public static function metabox_dashboard_preview($post) {
		echo PdfLightViewer_FrontController::display_pdf_book(array('id' => $post->ID));
	}
	
	
	public static function metabox_dashboard_usage($post) {
		
		$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post->ID);
		$pages = directory_map($pdf_upload_dir);
		
		$post_id = $post->ID;
		include_once(PDF_LIGHT_VIEWER_APPPATH.'/views/metabox/usage.php');
	}
	

	public static function save_post($post_id) {
		
		if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || !$_POST ) {
			return;
		}
		
		if (current_user_can('edit_posts')) {
			$form_data = $_REQUEST;
			
			$pdf_file_id = (isset($form_data['pdf_file_id']) ? $form_data['pdf_file_id'] : PdfLightViewer_Model::getPDFFileId($post_id));
			
			$pdf_file_path = get_attached_file($pdf_file_id);
			
			if (
				(
					(
						isset($form_data['enable_pdf_import'])
						&& $form_data['enable_pdf_import'] == 'on'
					)
					|| (
						isset($form_data['enable_pdf_import'])
						&& (
							isset($form_data['enable_pdf_import']['cmb-field-0'])
							|| $form_data['enable_pdf_import']['cmb-field-0']
						)
					)
				)
				&& $pdf_file_id
			) {
				
				do_action(PDF_LIGHT_VIEWER_PLUGIN.':before_import_scheduled', $pdf_file_path);
				
				$jpeg_compression_quality = (isset($form_data['jpeg_compression_quality']) ? $form_data['jpeg_compression_quality'] :get_post_meta($post_id, 'jpeg_compression_quality', true));
				$jpeg_resolution = (isset($form_data['jpeg_resolution']) ? $form_data['jpeg_resolution'] : get_post_meta($post_id, 'jpeg_resolution', true));
				
				$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
				
				// delete all files
				self::delete_pages_by_pdf_id($post_id, $pdf_upload_dir);
				
                $im = PdfLightViewer_Plugin::getXMagick();
				if ($im) {
					                    
					$im->readImage($pdf_file_path);
					$pages_number = $im->getNumberImages();
					
                    $width = null;
                    $height = null;
					foreach($im as $_img) {
						$geometry = $_img->getImageGeometry();
						$width = $geometry['width'];
						$height = $geometry['height'];
						break;
					}
                    
                    if (!$width && method_exists($im, 'getImageGeometry')) {
                        $geometry = $im->getImageGeometry();
						$width = $geometry['width'];
						$height = $geometry['height'];
                    }
					
					$im->destroy();
					
					update_post_meta($post_id,'_pdf-light-viewer-import-status', static::STATUS_SCHEDULED);
					update_post_meta($post_id,'_pdf-light-viewer-import-progress',0);
					update_post_meta($post_id,'_pdf-light-viewer-import-current-page',1);
                    
                    $importPages = array();
                    if (!empty($form_data['import_pages'])) {
                        $importPages = static::parsePages($form_data['import_pages']);
                        
                        if (!empty($importPages)) {
                            $pages_number = count($importPages);
                        }
                    }
                    
                    update_post_meta($post_id,'pdf-import-pages', $importPages);
                    update_post_meta($post_id,'pdf-pages-number', $pages_number);
					update_post_meta($post_id,'pdf-page-width', $width);
					update_post_meta($post_id,'pdf-page-height', $height);
					
					PdfLightViewer_AdminController::showMessage(
						sprintf(__('PDF import scheduled.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, false);
				}
				else {
					PdfLightViewer_AdminController::showMessage(
						sprintf(__('Imagick/Gmagick not found, please check other requirements on <a href="%s">plugin settings page</a> for more information.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, true);
				}
			}
		}
		
		unset($_REQUEST['enable_pdf_import']);
		unset($_POST['enable_pdf_import']);
        unset($_REQUEST['import_pages']);
        unset($_POST['import_pages']);
	}
	
	
	
	public static function pdf_partially_import() {
		$unimported = PdfLightViewer_Model::getOneUnimported();
		$post_id = $unimported->ID;
		
		if (!$post_id) {
			return wp_send_json(array(
				'status' => 'error',
				'progress' => 0,
				'error' => __('Currently there are no unimported files in the queue.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}
		
		$status = PdfLightViewer_Plugin::get_post_meta($post_id,'_pdf-light-viewer-import-status',true);
		if ($status == static::STATUS_SCHEDULED) {
			$status_label = __('scheduled', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', static::STATUS_STARTED);
		}
		else if ($status == static::STATUS_STARTED) {
			$status_label = __('started', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', 'processing');
		}
		else {
			$status_label = __('processing', PDF_LIGHT_VIEWER_PLUGIN);
		}
		
		ignore_user_abort(true);
        if (!ini_get('safe_mode')) {
            set_time_limit(0);
        }
		
		$pdf_file_id = PdfLightViewer_Model::getPDFFileId($post_id);
		$pdf_file_path = get_attached_file($pdf_file_id);
		
		do_action(PDF_LIGHT_VIEWER_PLUGIN.':before_import', $post_id, $pdf_file_path);
		
		$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
		
		$jpeg_resolution = PdfLightViewer_Plugin::get_post_meta($post_id,'jpeg_resolution',true);
		$jpeg_compression_quality = PdfLightViewer_Plugin::get_post_meta($post_id,'jpeg_compression_quality',true);
		
        $pdf_import_pages = (array)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-import-pages',true);
		$pdf_pages_number = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-pages-number',true);
		$current_page = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'_pdf-light-viewer-import-current-page',true);
		
		$width = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-page-width',true);
		$height = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-page-height',true);
        $output_biggest_side = (int)PdfLightViewer_Plugin::get_post_meta($post_id, 'output_biggest_side', true);
        if (!$output_biggest_side) {
            $output_biggest_side = 1024;
        }
		
		if (!$width || !$height) {
            return wp_send_json(array(
				'status' => 'error',
				'progress' => 0,
				'error' => __('Cannot get width and height of the first page.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}
		
		$ratio = $width / $height;
	
		if (!$current_page) {
			return wp_send_json(array(
				'status' => 'error',
				'progress' => 0,
				'error' => __('Cannot detect current imported PDF page.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}
	
		$error = '';
        $percent = null;
        
        if (empty($pdf_import_pages)) {
            for($current_page; $current_page <= $pdf_pages_number; $current_page++) {
                $page_number = sprintf('%1$05d',$current_page);
                if (!file_exists($pdf_upload_dir.'/page-'.$page_number.'.jpg')) {
                    
                    try {
                        $percent = static::process_pdf_page(
                            $post_id,
                            $current_page,
                            $current_page,
                            $page_number,
                            $pdf_pages_number,
                            $pdf_file_path,
                            $pdf_upload_dir,
                            $jpeg_resolution,
                            $jpeg_compression_quality,
                            $ratio,
                            $output_biggest_side
                        );
                    }
                    catch(Exception $e) {
                        PdfLightViewer_Plugin::log('Import exception: '.$e->getMessage(), print_r($e, true));
                        $status_label = __('failed', PDF_LIGHT_VIEWER_PLUGIN);
                        $error = $e->getMessage();
                        update_post_meta($post_id,'_pdf-light-viewer-import-status', static::STATUS_FAILED);
                    }
                    
                    break;
                }
            }
        }
        else {
            foreach($pdf_import_pages as $current_page => $current_page_doc) {
                $current_page++;
                $page_number = sprintf('%1$05d',$current_page);
                if (!file_exists($pdf_upload_dir.'/page-'.$page_number.'.jpg')) {
                    
                    try {
                        $percent = static::process_pdf_page(
                            $post_id,
                            $current_page,
                            $current_page_doc,
                            $page_number,
                            $pdf_pages_number,
                            $pdf_file_path,
                            $pdf_upload_dir,
                            $jpeg_resolution,
                            $jpeg_compression_quality,
                            $ratio,
                            $output_biggest_side
                        );
                    }
                    catch(Exception $e) {
                        PdfLightViewer_Plugin::log('Import exception: '.$e->getMessage(), print_r($e, true));
                        $status_label = __('failed', PDF_LIGHT_VIEWER_PLUGIN);
                        $error = $e->getMessage();
                        update_post_meta($post_id,'_pdf-light-viewer-import-status', static::STATUS_FAILED);
                    }
                    
                    break;
                }
            }
        }
        
		
		
		do_action(PDF_LIGHT_VIEWER_PLUGIN.':after_import', $post_id, $pdf_file_path);
			
		if ($percent >= 100) {
			do_action(PDF_LIGHT_VIEWER_PLUGIN.':finished_import', $post_id, $pdf_file_path);
			$status_label = __('finished', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', static::STATUS_FINISHED);
		}
		
		return wp_send_json(array(
			'status' => $status_label,
			'progress' => (int)$percent,
			'error' => $error
		));
	}
    
    public static function cancel_import() {
		$unimported = PdfLightViewer_Model::getOneUnimported();
		$post_id = $unimported->ID;
		
		if (!$post_id) {
			return wp_send_json(array(
				'status' => 'error',
				'error' => __('Currently there are no unimported files in the queue.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}
		
		update_post_meta($post_id,'_pdf-light-viewer-import-status', static::STATUS_FAILED);
		
		return wp_send_json(array(
			'status' => 'error',
            'error' => __('Import cancelled', PDF_LIGHT_VIEWER_PLUGIN),
		));
	}
	
	public static function process_pdf_page(
        $post_id, $current_page, $current_page_doc,
        $page_number, $pdf_pages_number, $pdf_file_path,
        $pdf_upload_dir, $jpeg_resolution, $jpeg_compression_quality,
        $ratio, $output_biggest_side
    ) {
        
        $Imagick = PdfLightViewer_Plugin::getXMagick();
        $ImagickClass = get_class($Imagick);
        
		$_img = new $ImagickClass();
        
        if (class_exists('Imagick') && $Imagick instanceof Imagick) {
            $_img->setResolution($jpeg_resolution, $jpeg_resolution);
        }
        else if (class_exists('Gmagick') && $Imagick instanceof Gmagick && method_exists($_img, 'setResolution')) {
            $_img->setResolution($jpeg_resolution, $jpeg_resolution);
        }
        
        // main page image in PDF
        list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();
        
        if (
            $gsPath
            && $ghostscript_version
        ) {
            $commnad = $gsPath.' '
                .'-dBATCH '
                .'-dNOPAUSE '
                .'-dQUIET '
                .'-sDEVICE=pdfwrite '
                .'-dFirstPage='.($current_page_doc).' '
                .'-dLastPage='.($current_page_doc).' '
                .'-sOutputFile='.escapeshellcmd($pdf_upload_dir.'-pdfs/page-'.$page_number.'.pdf').' '
                .escapeshellcmd($pdf_file_path);
                
            @shell_exec($commnad);
            
            // main page image
            // if possible, use ghostscript directly
            $commnad = $gsPath.' '
                .'-dBATCH '
                .'-dNOPAUSE '
                .'-dQUIET '
                .'-sDEVICE=jpeg '
                .'-r'.((int)$jpeg_resolution).' '
                .'-dJPEGQ='.$jpeg_compression_quality.' '
                .'-dFirstPage='.($current_page_doc).' '
                .'-dLastPage='.($current_page_doc).' '
                .'-sOutputFile='.escapeshellcmd($pdf_upload_dir.'/page-'.$page_number.'.jpg').' '
                .escapeshellcmd($pdf_file_path);
                
            @shell_exec($commnad);
            
            $_img->readImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
        }
        else {
            $_img->readImage($pdf_file_path.'['.($current_page_doc - 1).']');
        }
        
			$_img->setImageCompression($ImagickClass::COMPRESSION_JPEG);
			$_img->resizeImage($output_biggest_side, round($output_biggest_side/$ratio), $ImagickClass::FILTER_BESSEL, 1, false);
            
            if (class_exists('Imagick') && $Imagick instanceof Imagick) {
                $_img->setImageCompressionQuality($jpeg_compression_quality);
            }
            else if (class_exists('Gmagick') && $Imagick instanceof Gmagick) {
                $_img->setCompressionQuality($jpeg_compression_quality);
            }
            
			$_img->setImageFormat('jpg');
			
			// IMPORTANT: imagick changed SRGB and RGB profiles after vesion 6.7.6
            if (method_exists($_img, 'transformImageColorspace')) {
                if (
                    $_img->getImageColorspace() != $ImagickClass::COLORSPACE_RGB
                    && $_img->getImageColorspace() != $ImagickClass::COLORSPACE_SRGB
                ) {
                    if (version_compare(static::getImagickVersion(), '6.7.6', '>=')) {
                        $_img->transformImageColorspace($ImagickClass::COLORSPACE_SRGB);
                    }
                    else {
                        $_img->transformImageColorspace($ImagickClass::COLORSPACE_RGB);
                    }
                }
            }
            
            // main page image
                $white = new $ImagickClass();
                $white->newImage($output_biggest_side, round($output_biggest_side/$ratio), "white");
                $white->compositeimage($_img, $ImagickClass::COMPOSITE_OVER, 0, 0);
                $white->setImageFormat('jpg');
                $white->setImageColorspace($_img->getImageColorspace());
                $white->writeImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
			
            // thumbnail
                $_img->resizeImage(76, round(76/$ratio),$ImagickClass::FILTER_BESSEL, 1, false);
        
                $white = new $ImagickClass();
                $white->newImage(76, round(76/$ratio), "white");
                $white->compositeimage($_img, $ImagickClass::COMPOSITE_OVER, 0, 0);
                $white->setImageFormat('jpg');
                $white->setImageColorspace($_img->getImageColorspace());
                $white->writeImage($pdf_upload_dir.'-thumbs/page-'.$page_number.'-100x76.jpg');
			
			if ($current_page == 1) {
				$file = $pdf_upload_dir.'/page-'.$page_number.'.jpg';
				PdfLightViewer_Plugin::set_featured_image($post_id, $file, 'pdf-'.$post_id.'-page-'.$page_number.'.jpg');
			}
		
			$percent = (($current_page)/$pdf_pages_number)*100;
			update_post_meta($post_id,'_pdf-light-viewer-import-progress',$percent);
			update_post_meta($post_id,'_pdf-light-viewer-import-current-page',$current_page);	
			
		$_img->destroy();
		unset($_img);
		
		return $percent;
	}
	
	protected static function delete_pages_by_pdf_id($post_id, $pdf_upload_dir) {
				
		if (!$pdf_upload_dir) {
			return false;
		}
				
		$directory_map = directory_map($pdf_upload_dir);
		
		if (!empty($directory_map)) {
			foreach($directory_map as $file) {
				unlink($pdf_upload_dir.'/'.$file);
			}
		}
		
		$directory_map = directory_map($pdf_upload_dir.'-thumbs');
		if (!empty($directory_map)) {
			foreach($directory_map as $file) {
				unlink($pdf_upload_dir.'-thumbs/'.$file);
			}
		}
        
        $directory_map = directory_map($pdf_upload_dir.'-pdfs');
		if (!empty($directory_map)) {
			foreach($directory_map as $file) {
				unlink($pdf_upload_dir.'-pdfs/'.$file);
			}
		}
		
		do_action(PDF_LIGHT_VIEWER_PLUGIN.':delete_pages_by_pdf_id', $post_id, $pdf_upload_dir);
	}
	
	
	public static function deleted_post($post_id = '', $arg2 = '') {
		if ($post_id && get_post_type($post_id) == self::$type) {
			$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
			
			if ($pdf_upload_dir) {
				self::delete_pages_by_pdf_id($post_id, $pdf_upload_dir);
				rmdir($pdf_upload_dir);
				rmdir($pdf_upload_dir.'-thumbs');
                rmdir($pdf_upload_dir.'-pdfs');
			}
		}
	}
}
