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
					__('<i class="fa fa-cog fa-spin"></i> <b>%s</b> PDF import is <span class="js-pdf-light-viewer-current-status">%s</span>. <span class="js-pdf-light-viewer-current-progress">%d</span>%% is complete. <i>Please do not leave the admin interface until the import would not finished. %s</i>',PDF_LIGHT_VIEWER_PLUGIN),
					PdfLightViewer_Model::$unimported->post_title,
					$status,
					$progress,
					'<a href="#!" class="js-tip tip" title="'.__('Otherwise the import will be continued during your next visit.', PDF_LIGHT_VIEWER_PLUGIN).'"><span class="fa fa-question-circle"></span></a>'
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
					case 'scheduled':
						$status_label = __('Import scheduled',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case 'started':
						$status_label = __('Import started',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case 'processing':
						$status_label = __('Import in progress',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				
					case 'finished':
						$status_label = __('Import finished',PDF_LIGHT_VIEWER_PLUGIN);
					break;
				}
				
				?>
					<div><?php echo $status_label; ?></div>
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
					'name' => __('Enable import', PDF_LIGHT_VIEWER_PLUGIN),
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
		
		
		$meta_boxes['pdf_light_viewer_options_metabox'] = array(
			'id' => 'pdf_light_viewer_options_metabox',
			'title' => __('Output Options', PDF_LIGHT_VIEWER_PLUGIN),
			'pages' => array(self::$type), // post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
				array(
					'name' => __('Allow download', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('Check this if you want to show download button on the frontend', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'download_allowed',
					'type' => 'checkbox'
				),
				array(
					'name' => __('Alternate download link', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => __('If not set, will be used link from PDF File', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'alternate_download_link',
					'type' => 'text',
					'default' => ''
				),
				array(
					'name' => __('Hide thumbnail navigation', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'hide_thumbnails_navigation',
					'type' => 'checkbox'
				),
				array(
					'name' => __('Hide fullscreen button', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'hide_fullscreen_button',
					'type' => 'checkbox'
				),
				array(
					'name' => __('Disable page zoom', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'disable_page_zoom',
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
		
		global $pdf_light_viewer_config;
		$pdf_light_viewer_config = array();
		
		// download options
			$pdf_light_viewer_config['download_allowed'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'download_allowed', true);
			if ($pdf_light_viewer_config['download_allowed']) {
				$pdf_file_id = PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf_file_id', true);
				$pdf_file_url = wp_get_attachment_url($pdf_file_id);
				
				$alternate_download_link = PdfLightViewer_Plugin::get_post_meta($post->ID, 'alternate_download_link', true);
				
				$pdf_light_viewer_config['download_link'] = ($alternate_download_link ? $alternate_download_link : $pdf_file_url);
			}
			else {
				$pdf_light_viewer_config['download_link'] = '';
			}
			
		$pdf_light_viewer_config['hide_thumbnails_navigation'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'hide_thumbnails_navigation', true);
		$pdf_light_viewer_config['hide_fullscreen_button'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'hide_fullscreen_button', true);
		$pdf_light_viewer_config['disable_page_zoom'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'disable_page_zoom', true);
		
		
		$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post->ID);
		$pdf_upload_dir_url = PdfLightViewer_Plugin::getUploadDirectoryUrl($post->ID);
		
		$pdf_light_viewer_config['pdf_upload_dir_url'] = $pdf_upload_dir_url;
		
		$pdf_light_viewer_config['pages'] = directory_map($pdf_upload_dir);
		if (!empty($pdf_light_viewer_config['pages'])) {
			sort($pdf_light_viewer_config['pages']);
		}
		
		$pdf_light_viewer_config['thumbs'] = directory_map($pdf_upload_dir.'-thumbs');
		if (!empty($pdf_light_viewer_config['thumbs'])) {
			sort($pdf_light_viewer_config['thumbs']);
		}
		
		$pdf_light_viewer_config['page_width'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf-page-width', true);
		$pdf_light_viewer_config['page_height'] = PdfLightViewer_Plugin::get_post_meta($post->ID, 'pdf-page-height', true);
			
		if (!empty($pdf_light_viewer_config['pages'])) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/templates/shortcode-pdf-light-viewer.php');
		}
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
			
			$pdf_file_id = $form_data['pdf_file_id'];
			$pdf_file_path = get_attached_file($pdf_file_id);
			
			if ($form_data['enable_pdf_import'] == 'on' && $pdf_file_id) {
				
				$jpeg_compression_quality = $form_data['jpeg_compression_quality'];
				$jpeg_resolution = $form_data['jpeg_resolution'];
				
				$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
				
				// delete all files
				self::delete_pages_by_pdf_id($post_id, $pdf_upload_dir);
				
				if (class_exists('Imagick')) {
					
					$im = new Imagick();
					$im->readImage($pdf_file_path);
					$pages_number = $im->getNumberImages();
					
					foreach($im as $_img) {
						$geometry = $_img->getImageGeometry();
						$width = $geometry['width'];
						$height = $geometry['height'];
						break;
					}
					
					$im->destroy();
					
					update_post_meta($post_id,'_pdf-light-viewer-import-status', 'scheduled');
					update_post_meta($post_id,'_pdf-light-viewer-import-progress',0);
					update_post_meta($post_id,'_pdf-light-viewer-import-current-page',1);
					update_post_meta($post_id,'pdf-pages-number', $pages_number);
					update_post_meta($post_id,'pdf-page-width', $width);
					update_post_meta($post_id,'pdf-page-height', $height);
					
					PdfLightViewer_AdminController::showMessage(
						sprintf(__('PDF import scheduled.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, false);
				}
				else {
					PdfLightViewer_AdminController::showMessage(
						sprintf(__('Imagick not found, please check other requirements on <a href="%s">plugin settings page</a> for more information.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, true);
				}
			}
		}
		
		unset($_REQUEST['enable_pdf_import']);
		unset($_POST['enable_pdf_import']);
	}
	
	
	
	public static function pdf_partially_import() {
		$unimported = PdfLightViewer_Model::getOneUnimported();
		$post_id = $unimported->ID;
		
		if (!$post_id) {
			return;
		}
		
		$status = PdfLightViewer_Plugin::get_post_meta($post_id,'_pdf-light-viewer-import-status',true);
		if ($status == 'scheduled') {
			$status_label = __('scheduled', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', 'started');
		}
		else if ($status == 'started') {
			$status_label = __('started', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', 'processing');
		}
		else {
			$status_label = __('processing', PDF_LIGHT_VIEWER_PLUGIN);
		}
		
		ignore_user_abort(true);
		set_time_limit(0);
		
		$pdf_file_id = PdfLightViewer_Plugin::get_post_meta($post_id,'pdf_file_id',true);
		$pdf_file_path = get_attached_file($pdf_file_id);
		
		$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
		
		$jpeg_resolution = PdfLightViewer_Plugin::get_post_meta($post_id,'jpeg_resolution',true);
		$jpeg_compression_quality = PdfLightViewer_Plugin::get_post_meta($post_id,'jpeg_compression_quality',true);
		
		$pdf_pages_number = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-pages-number',true);
		$current_page = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'_pdf-light-viewer-import-current-page',true);
		
		$width = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-page-width',true);
		$height = (int)PdfLightViewer_Plugin::get_post_meta($post_id,'pdf-page-height',true);
		
		if (!$width || !$height) {
			return;
		}
		
		$ratio = $width / $height;
	
		if (!$current_page) {
			return;
		}
	
		$error = '';
		for($current_page; $current_page <= $pdf_pages_number; $current_page++) {
			$page_number = sprintf('%1$05d',$current_page);
			if (!file_exists($pdf_upload_dir.'/page-'.$page_number.'.jpg')) {
				
				try {
					$_img = new Imagick();
					$_img->setResolution($jpeg_resolution, $jpeg_resolution);
					$_img->readImage($pdf_file_path.'['.($current_page-1).']');
			
					
						$_img->setImageCompression(Imagick::COMPRESSION_JPEG);
						$_img->resizeImage(1024, round(1024/$ratio), Imagick::FILTER_BESSEL, 1, false);
						$_img->setImageCompressionQuality($jpeg_compression_quality);
						$_img->setImageFormat('jpg');
						//$_img->setImageInterlaceScheme(Imagick::INTERLACE_JPEG);
						$_img->transformImageColorspace(Imagick::COLORSPACE_SRGB);
						//$_img->setBackgroundColor(new ImagickPixel('#FFFFFF'));
						
						// Remove transparency, fill transparent areas with white rather than black.
						//$_img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
						
						// Convert to RGB to prevent creating a jpg with CMYK colors.
						
				
						$white = new Imagick();
						$white->newImage(1024, round(1024/$ratio), "white");
						$white->compositeimage($_img, Imagick::COMPOSITE_OVER, 0, 0);
						$white->setImageFormat('jpg');
						$white->setImageColorspace($_img->getImageColorspace());
						$white->writeImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
						
						$_img->resizeImage(76, round(76/$ratio),Imagick::FILTER_BESSEL, 1, false);
				
						$white = new Imagick();
						$white->newImage(76, round(76/$ratio), "white");
						$white->compositeimage($_img, Imagick::COMPOSITE_OVER, 0, 0);
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
				}
				catch(Exception $e) {
					$status = 'failed';
					$status_label = __('failed', PDF_LIGHT_VIEWER_PLUGIN);
					$error = $e->getMessage();
					update_post_meta($post_id,'_pdf-light-viewer-import-status', $status);
				}
				
				break;
			}
		}
			
		if ($percent >= 100) {
			$status = 'finished';
			$status_label = __('finished', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', $status);
		}
		
		echo json_encode(array(
			'status' => $status_label,
			'progress' => (int)$percent,
			'error' => $error
		));
		exit;
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
	}
	
	
	public static function deleted_post($post_id = '', $arg2 = '') {
		if ($post_id && get_post_type($post_id) == self::$type) {
			$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
			
			if ($pdf_upload_dir) {
				self::delete_pages_by_pdf_id($post_id, $pdf_upload_dir);
				rmdir($pdf_upload_dir);
				rmdir($pdf_upload_dir.'-thumbs');
			}
		}
	}
}
