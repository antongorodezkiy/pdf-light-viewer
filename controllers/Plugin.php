<?php

class PdfLightViewer_Plugin {
	
	private static $cached_meta = array();
	static public function get_post_meta($post_id, $key = '', $single = false) {
		if (!isset(self::$cached_meta[$post_id])) {
			self::$cached_meta[$post_id] = array();
			$meta_data = get_post_meta($post_id);
			if (!empty($meta_data)) {
				foreach($meta_data as $meta_key => $meta) {
					if (is_serialized($meta[0])) {
						$meta[0] = unserialize($meta[0]);
					}
					self::$cached_meta[$post_id][$meta_key] = $meta[0];
				}
			}
		}
		
		if (!$key && isset(self::$cached_meta[$post_id])) {
			return self::$cached_meta[$post_id];
		}
		else if (isset(self::$cached_meta[$post_id][$key])) {
			return self::$cached_meta[$post_id][$key];
		}
		else {
			return null;
		}
	}
	
	public static function getData($key = '') {
		$plugin = get_plugin_data(PDF_LIGHT_VIEWER_FILE, false, true);
		
		if ($key && isset($plugin[$key])) {
			return $plugin[$key];
		}
		else {
			return $plugin;
		}
	}
	
	public static function activation() {
		self::createUploadDirectory();
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-notifications-viewed', false);
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed', false);
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-show-post-type', true);
	}
	
	public static function initEarlyActions() {
		add_action(
			'PdfLightViewer_PdfController::scheduled_pdf_import',
			array('PdfLightViewer_PdfController','scheduled_pdf_import'),
			10,
			5
		);
	}

	// plugin actions
		public static function registerPluginActions($links, $file) {
			if (stristr($file, PDF_LIGHT_VIEWER_PLUGIN)) {
				$settings_link = '<a href="'.self::getSettingsUrl().'">' . __('Settings', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$docs_link = '<a href="'.self::getDocsUrl().'">' . __('Docs', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$links = array_merge(array($settings_link, $docs_link), $links);
			}
			return $links;
		}
		
	public static function localization() {
		load_plugin_textdomain(
			PDF_LIGHT_VIEWER_PLUGIN,
			false,
			dirname(plugin_basename(PDF_LIGHT_VIEWER_FILE)).'/languages/'.get_locale().'/');
	}
		
	// register post types
		public static function registerPostTypes() {			
			PdfLightViewer_PdfController::init();
		}
		
	// register shortcodes
		public static function registerShortcodes() {
			add_shortcode(
				'pdf-light-viewer',
				array('PdfLightViewer_FrontController', 'disaply_pdf_book')
			);
		}
		
	public static function getActivePlugins() {
		$apl = get_option('active_plugins');
		$plugins = get_plugins();
		$activated_plugins = array();
		foreach($apl as $p) {           
			if(isset($plugins[$p])) {
				array_push($activated_plugins, $plugins[$p]);
			}           
		}
		
		return $activated_plugins;
	}
	
	public static function serverInfo() {
		global $wp_version, $wpdb;
		
		$mysql = $wpdb->get_row("SHOW VARIABLES LIKE 'version'");
		
		$info = array(
			'os' => php_uname(),
			'php' => phpversion(),
			'mysql' => $mysql->Value,
			'wordpress' => $wp_version
		);
		
		return $info;
	}
	
	public static function getDocsUrl() {
		if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/documentation/index_'.WPLANG.'.html')) {
			$documentation_url = 'documentation/index'.WPLANG.'.html';
		}
		else {
			$documentation_url = 'documentation/index.html';
		}
		$documentation_url = plugins_url($documentation_url, PDF_LIGHT_VIEWER_FILE);
		return $documentation_url;
	}
	
	public static function getSettingsUrl() {
		return admin_url('options-general.php?page='.PDF_LIGHT_VIEWER_PLUGIN);
	}
	
	public static function createUploadDirectory($id = '') {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['basedir'];
		
		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;
		
		if (file_exists($main_upload_dir)) {
			$created = true;
		}
		else {
			$created = @mkdir($main_upload_dir);
		}
		
		if ($id) {
			$pdf_upload_dir = $main_upload_dir.'/'.$id;
			if (!file_exists($pdf_upload_dir)) {
				@mkdir($pdf_upload_dir);
			}
			
			$pdf_thumbs_upload_dir = $main_upload_dir.'/'.$id.'-thumbs';
			if (!file_exists($pdf_thumbs_upload_dir)) {
				@mkdir($pdf_thumbs_upload_dir);
			}
			
			if (file_exists($pdf_upload_dir)) {
				return $pdf_upload_dir;
			}
			else {
				return false;
			}
		}
		
		if (file_exists($main_upload_dir)) {
			return $main_upload_dir;
		}
		else {
			return false;
		}
	}
	
	
	public static function getUploadDirectory($id = '') {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['basedir'];
		
		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		if ($id) {
			$pdf_upload_dir = $main_upload_dir.'/'.$id;
			return $pdf_upload_dir;
		}
		else {
			return $main_upload_dir;
		}
	}
	
	public static function getUploadDirectoryUrl($id) {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['baseurl'];
		
		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		$pdf_upload_dir = $main_upload_dir.'/'.$id;

		return $pdf_upload_dir;
	}
	
	
	// plugin requirements
		public static function requirements($boolean = false) {
			$upload_dir_message = __('Upload folder',PDF_LIGHT_VIEWER_PLUGIN).': <code>'.PdfLightViewer_Plugin::getUploadDirectory().'</code>';
			
			$host_url = site_url();
			
			$requirements = array(
				array(
					'name' => 'PHP',
					'status' => version_compare(PHP_VERSION, '5.2.0', '>='),
					'success' => sprintf(__('is %s or higher',PDF_LIGHT_VIEWER_PLUGIN), '5.2'),
					'fail' => sprintf(__('is lower than %s',PDF_LIGHT_VIEWER_PLUGIN), '5.2')
				),
				array(
					'name' => 'Imagick',
					'status' => (extension_loaded('imagick') || class_exists("Imagick")),
					'success' => __('is supported',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => __('is not supported',PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => $upload_dir_message,
					'status' => PdfLightViewer_Plugin::createUploadDirectory(),
					'success' => __('is writable',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => __('is not writable',PDF_LIGHT_VIEWER_PLUGIN)
				)
			);
			
			if ($boolean) {
				$status = true;
				foreach($requirements as $requirement) {
					$status = $status && $requirement['status'];
				}
				return $status;
			}
			else {
				return $requirements;
			}
		}
		
		
	// thumbnails
		public static function set_featured_image($post_id, $file, $media_name) {
			$image_data = file_get_contents($file);
			$attach_id = self::create_media_from_data($media_name, $image_data);
			return set_post_thumbnail($post_id, $attach_id);
		}
		
		public static function create_media_from_data($filename, $image_data) {
			
			$upload_dir = wp_upload_dir();
	
			$file = $upload_dir['path'].'/'.$filename;
			
			file_put_contents($file, $image_data);
			
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment($attachment, $file, 0);
			
			$url = wp_get_attachment_image_src($attach_id, 'full');
			apply_filters('wp_handle_upload', array(
				'file' => $file,
				'url' => $url,
				'type' => $wp_filetype['type']
			), 'upload');
			
			require_once(ABSPATH.'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);
			
			return $attach_id;
		}
		
		
	public static function run() {
		$requirements_met = self::requirements(true);
		if (
			(!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-notifications-viewed') && $requirements_met)
			|| !$requirements_met
		) {
			add_action('admin_notices', array('PdfLightViewer_AdminController','showActivationMessages'));
		}
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-notifications-viewed', true);
		
		if (!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed')) {
			add_action('admin_enqueue_scripts', array('PdfLightViewer_AdminController','showActivationPointers'));
		}
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed', true);
	}

}
