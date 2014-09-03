<?php
	
class PdfLightViewer_PdfController {
	
	public static $type = 'pdf_lv';
	
	public static function init() {
		
		self::register();
		
		// columns
		add_filter( 'manage_edit-'.self::$type.'_columns', array(__CLASS__, 'custom_columns_registration'), 10 );
		add_action( 'manage_'.self::$type.'_posts_custom_column', array(__CLASS__, 'custom_columns_views'), 10, 2 );
		
		// metaboxes
		add_filter('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
		add_filter('cmb_meta_boxes', array(__CLASS__, 'cmb_metaboxes'));
		
		// saving
		add_action('save_post_'.self::$type, array(__CLASS__, 'save_post'), 100);
		
		add_action(
			'PdfLightViewer_PdfController_scheduled_pdf_import',
			'PdfLightViewer_PdfController::scheduled_pdf_import',
			10,
			5
		);
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
				'show_ui' => true, 
				'_builtin' => false,
				'capability_type' => 'post',
				'menu_icon' => plugins_url('img/pdf.png', PDF_LIGHT_VIEWER_FILE),
				'hierarchical' => false,
				'map_meta_cap' => true,
				'supports' => array('title', 'thumbnail'/*, 'custom-fields'*/),
			)
		);

	}
	
	public static function custom_columns_registration( $defaults ) {
		$defaults['preview'] = __('Preview', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['shortcode'] = __('Shortcodes', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['pages'] = __('Pages',PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['import_status'] = __('Import status',PDF_LIGHT_VIEWER_PLUGIN);
		return $defaults;
	}
	
	public static function custom_columns_views($column_name, $post_id) {
	 		
		switch($column_name) {
			case 'shortcode':
				?>
				<code>[pdf-light-viewer id="<?php echo $post_id;?>" template=""]</code>
				<?php
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
				
				if (!empty($directory_map)) {
					$count = count($directory_map);
				}
				else {
					$count = '&mdash;';
				}
				?>
					<?php echo $count; ?>
				<?php
			break;
			
			case 'import_status':
				$status = get_post_meta($post_id,'_pdf-light-viewer-import-status',true);
				$progress = (int)get_post_meta($post_id,'_pdf-light-viewer-import-progress',true);
				?>
					<div><?php echo $status; ?></div>
					<div><?php echo $progress; ?>%</div>
				<?php
			break;
			
		}
	}
	
	
	public static function cmb_metaboxes($meta_boxes) {
		
		$meta_boxes['pdf_light_viewer_file_metabox'] = array(
			'id' => 'pdf_light_viewer_file_metabox',
			'title' => __('PDF', PDF_LIGHT_VIEWER_PLUGIN),
			'pages' => array(self::$type), // post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
				array(
					'name' => 'Enable import',
					'desc' => __('Check this if you want to import or re-import PDF file', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'enable_pdf_import',
					'type' => 'checkbox'
				),
				array(
					'name' => __('JPEG compression quality', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'jpeg_compression_quality',
					'type' => 'text',
					'default' => 60
				),
				array(
					'name' => __('JPEG resolution', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'jpeg_resolution',
					'type' => 'text',
					'default' => 300
				),
				array(
					'name' => __('PDF File', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('Choose what PDF file will be imported', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'pdf_file',
					'type' => 'file'
				)
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
			'pages' => array(self::$type), // post type
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
							'name' => __('Visible pages', PDF_LIGHT_VIEWER_PLUGIN),
							'id'   => 'pages_limit_visible_pages',
							'type' => 'text'
						)
					)
				)
			)
		);
	
		return $meta_boxes;
	}
	
	
	public static function add_meta_boxes() {
		global $pagenow;
		
		if ($pagenow != 'post-new.php') {
			// add meta box for employer match
				add_meta_box(
					'pdf_light_viewer_dashboard_preview',
					__('PDF Preview', PDF_LIGHT_VIEWER_PLUGIN),
					array(__CLASS__, 'metabox_dashboard_preview'),
					self::$type,
					'advanced',
					'default',
					array()
				);
		}
	}
	
	public static function metabox_dashboard_preview($post) {
		
		global $pdf_light_viewer_config;
		
		$pdf_file_id = $form_data['pdf_file_id'];
		$pdf_file_path = get_attached_file($pdf_file_id);
		
		$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post->ID);
		$pdf_upload_dir_url = PdfLightViewer_Plugin::getUploadDirectoryUrl($post->ID);
		
		$pdf_light_viewer_config['pdf_upload_dir_url'] = $pdf_upload_dir_url;
		
		$pdf_light_viewer_config['pages'] = directory_map($pdf_upload_dir);
		sort($pdf_light_viewer_config['pages']);
		
		$pdf_light_viewer_config['thumbs'] = directory_map($pdf_upload_dir.'-thumbs');
		sort($pdf_light_viewer_config['thumbs']);
		
		if (!empty($pdf_light_viewer_config['pages'])) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/templates/shortcode-pdf-light-viewer.php');
		}
	}
	

	public static function save_post($post_id) {
		
		if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || !$_POST ) {
			return;
		}
		
		if (current_user_can('edit_posts')) {
			$form_data = $_REQUEST;
			
			if ($form_data['enable_pdf_import'] == 'on') {
				
				$pdf_file_id = $form_data['pdf_file_id'];
				$pdf_file_path = get_attached_file($pdf_file_id);
				
				$jpeg_compression_quality = $form_data['jpeg_compression_quality'];
				$jpeg_resolution = $form_data['jpeg_resolution'];
				
				$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
				
				$directory_map = directory_map($pdf_upload_dir);
				foreach($directory_map as $file) {
					unlink($pdf_upload_dir.'/'.$file);
				}
				
				$directory_map = directory_map($pdf_upload_dir.'-thumbs');
				foreach($directory_map as $file) {
					unlink($pdf_upload_dir.'-thumbs/'.$file);
				}
				
				if (class_exists('Imagick')) {
					// plan async scheduled task
					wp_schedule_single_event(
						time(),
						'PdfLightViewer_PdfController_scheduled_pdf_import',
						array(
							$post_id,
							$pdf_file_path,
							$pdf_upload_dir,
							$jpeg_resolution,
							$jpeg_compression_quality
						)
					);
					
					update_post_meta($post_id,'_pdf-light-viewer-import-status', __('Import scheduled',PDF_LIGHT_VIEWER_PLUGIN));
					update_post_meta($post_id,'_pdf-light-viewer-import-progress',0);
					
					/*PdfLightViewer_AdminController::showMessage(
						sprintf(__('PDF import scheduled.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, false);*/
				}
				else {
					/*PdfLightViewer_AdminController::showMessage(
						sprintf(__('Imagick not found, please check other requirements on <a href="%s">plugin settings page</a> for more information.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, true);*/
				}
			}
		}
		
		unset($_REQUEST['enable_pdf_import']);
		unset($_POST['enable_pdf_import']);
	}
	
	
	public static function scheduled_pdf_import($post_id, $pdf_file_path, $pdf_upload_dir, $jpeg_resolution, $jpeg_compression_quality) {
		ignore_user_abort(true);
		set_time_limit(0);
		
		update_post_meta($post_id,'_pdf-light-viewer-import-status', __('Import started',PDF_LIGHT_VIEWER_PLUGIN));
		
		$im = new Imagick();
		$im->setResolution($jpeg_resolution, $jpeg_resolution);
		$im->readImage($pdf_file_path);
		$i = 0;
		
		foreach($im as $_img) {
			
			update_post_meta($post_id,'_pdf-light-viewer-import-status', __('Import in progress',PDF_LIGHT_VIEWER_PLUGIN));
			
			$i++;
			$_img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$_img->resizeImage(768, 1024, Imagick::FILTER_LANCZOS, 1, false);
			$_img->setImageCompressionQuality($jpeg_compression_quality);
			$_img->setImageFormat('jpg');
			$_img->setBackgroundColor(new ImagickPixel('#FFFFFF'));
			$page_number = sprintf('%1$05d',$i);
			//$_img->writeImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
			
			$white = new Imagick();
			$white->newImage(768, 1024, "white");
			$white->compositeimage($_img, Imagick::COMPOSITE_OVER, 0, 0);
			$white->setImageFormat('jpg');
			$white->writeImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
			
			$_img->resizeImage(76,100,Imagick::FILTER_LANCZOS, 1, false);
			//$_img->writeImage($pdf_upload_dir.'-thumbs/page-'.$page_number.'-100x76.jpg');
			
			$white = new Imagick();
			$white->newImage(76, 100, "white");
			$white->compositeimage($_img, Imagick::COMPOSITE_OVER, 0, 0);
			$white->setImageFormat('jpg');
			$white->writeImage($pdf_upload_dir.'-thumbs/page-'.$page_number.'-100x76.jpg');
			
			if ($i == 1) {
				$file = $pdf_upload_dir.'/page-'.$page_number.'.jpg';
				PdfLightViewer_Plugin::set_featured_image($post_id, $file, 'pdf-'.$post_id.'-page-'.$page_number.'.jpg');
			}
			
			$percent = (($i-1)/count($im))*100;
			update_post_meta($post_id,'_pdf-light-viewer-import-progress',$percent);
		}
		$im->destroy();
		update_post_meta($post_id,'_pdf-light-viewer-import-status', __('Import finished',PDF_LIGHT_VIEWER_PLUGIN));
	}
	
}
