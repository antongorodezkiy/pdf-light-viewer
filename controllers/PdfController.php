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
		
	}
	
	public static function register() {
		global $pagenow;
		
		register_post_type(self::$type,
			array(
				'labels' => array(
					'name' => __('PDFs', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => __('PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => __('Add PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new' => __('Add PDF', PDF_LIGHT_VIEWER_PLUGIN),
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
		
		//$defaults['country'] = __('Countries', PDF_LIGHT_VIEWER_PLUGIN);

		return $defaults;
	}
	
	public static function custom_columns_views($column_name, $post_id) {
	 		
		switch($column_name) {
			case 'country':
				$connections = p2p_get_connections('songs_to_country',array(
					'from' => $post_id
				));
				?>
				<ul>
					<?php foreach($connections as $connection) {
						$country = WordPress\ORM\Model\Post::find_one_by('id',$connection->p2p_to)->to_array();
						$edit_url = admin_url('post.php?post='.$country['ID'].'&action=edit');
						$filter_songs_url = admin_url('edit.php?post_type='.self::$type.'&countries='.$country['ID'].'&action=-1');
						?>
							<li>
								<div>
									<?php echo $country['post_title'];?>
								</div>
								<div class="row-actions">
									<a href="<?php echo $edit_url;?>"><?php echo __('Edit');?></a>
									|
									<a href="<?php echo $filter_songs_url;?>"><?php echo __('Filter Songs');?></a>
								</div>
							</li>
						<?php
					}
					?>
				</ul>
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
					'name' => 'PDF File',
					'desc' => '',
					'id' => 'pdf_file',
					'type' => 'file'
				),
			),
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
		
		include_once(PDF_LIGHT_VIEWER_APPPATH.'/libraries/directory_helper.php');
		
		
		
		$pdf_file_id = $form_data['pdf_file_id'];
		$pdf_file_path = get_attached_file($pdf_file_id);
		
		$pdf_upload_dir = PdfLightViewer_Plugin::getUploadDirectory($post->ID);
		$pdf_upload_dir_url = PdfLightViewer_Plugin::getUploadDirectoryUrl($post->ID);
		
		$pages = directory_map($pdf_upload_dir);
		sort($pages);
		
		if (!empty($pages)) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/views/metaboxes/pdf-dashboard-preview.php');
		}
	}
	

	public static function save_post($post_id) {
		
		if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || !$_POST ) {
			return;
		}
		
		if (current_user_can('edit_posts')) {
			$form_data = $_REQUEST;
			
			$pdf_file_id = $form_data['pdf_file_id'];
			$pdf_file_path = get_attached_file($pdf_file_id);
			
			$pdf_upload_dir = PdfLightViewer_Plugin::createUploadDirectory($post_id);
			
			$im = new Imagick();
			$im->setResolution(200, 200);
			$im->readImage($pdf_file_path);
			$i = 0;
			foreach($im as $_img) {
				$i++;
				$_img->setImageCompression(Imagick::COMPRESSION_JPEG);
				$_img->setImageCompressionQuality(40);
				$_img->setImageFormat('jpeg');
				$page_number = sprintf('%1$04d',$i);
				$_img->writeImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
			}
			$im->destroy();
			
		}
	}
	

	
}
