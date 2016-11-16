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
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed', false);
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-show-post-type', true);
	}
	
	public static function initEarlyActions() {
		
	}

	// plugin actions
		public static function registerPluginActions($links, $file) {
			if (stristr($file, PDF_LIGHT_VIEWER_PLUGIN.'/')) {
				$settings_link = '<a href="'.self::getSettingsUrl().'">' . __('Settings', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$docs_link = '<a href="'.self::getDocsUrl().'">' . __('Docs', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$support_link = '<a target="_blank" href="'.self::getSupportUrl().'">' . __('Support', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$links = array_merge(array($settings_link, $docs_link, $support_link), $links);
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
				array('PdfLightViewer_FrontController', 'display_pdf_book')
			);
            
            add_shortcode(
				'pdf-light-viewer-enqueue-assets',
                array('PdfLightViewer_AssetsController', 'frontendEnqueue')
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
		if (defined('WPLANG') && file_exists(PDF_LIGHT_VIEWER_APPPATH.'/documentation/index_'.WPLANG.'.html')) {
			$documentation_url = 'documentation/index'.WPLANG.'.html';
		}
		else {
			$documentation_url = 'documentation/index.html';
		}
		$documentation_url = plugins_url($documentation_url, PDF_LIGHT_VIEWER_FILE);
		return $documentation_url;
	}
	
	public static function getSupportUrl() {
		return 'http://support.wp.teamlead.pw/';
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
            
            $pdf_pdfs_upload_dir = $main_upload_dir.'/'.$id.'-pdfs';
			if (!file_exists($pdf_pdfs_upload_dir)) {
				@mkdir($pdf_pdfs_upload_dir);
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
	
	
	public static function getLogsPath() {
		return WP_CONTENT_DIR.'/'.PDF_LIGHT_VIEWER_PLUGIN.'-logs/';
	}
	
	public static function createLogsDirectory() {
		$log_path = self::getLogsPath();
		if ( ! file_exists($log_path)) {
			mkdir($log_path);
		}
		
		return file_exists($log_path);
	}
	
	public static function log($label, $msg) {
		
		if (is_array($msg) || is_object($msg)) {
			$msg = print_r($msg,true);
		}
			
		$log_path = self::getLogsPath();
		
		if ( ! file_exists($log_path)) {
			mkdir($log_path);
		}
		
		$filename = date('Y-m-d').'.php';
		$filepath = $log_path.$filename;
			
		$message = '';

		if (!file_exists($filepath)) {
			$message .= "<"."?php if ( ! defined('WPINC')) exit('No direct script access allowed'); ?".">\n\n";
		}

		if (!$fp = fopen($filepath, 'ab')) {
			return FALSE;
		}

		$message .= "======================\n".date('d-m-Y H-i-s')."\n".' ---------------------- '."\n".$label.' >>> '.$msg."\n\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($filepath, 0666);
		return TRUE;
	}
	
	
	// plugin requirements
		public static function requirements($boolean = false) {
			$upload_dir_message = __('Upload folder',PDF_LIGHT_VIEWER_PLUGIN).': <code>'.PdfLightViewer_Plugin::getUploadDirectory().'</code>';
			
			$host_url = site_url();
            
            $ImagickVersion = null;
            $pdf_format_support = null;
            $Imagick = static::getXMagick();
            if ($Imagick) {
                $ImagickVersion = $Imagick->getVersion();
                $pdf_format_support = in_array('PDF', $Imagick->queryFormats());
            }
            
			if (PdfLightViewer_AdminController::getSetting('do-not-check-gs')) {
				$ghostscript_version = true;
			}
			else {
                list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();
			}
			
			$logs_dir_message = __('Logs folder',PDF_LIGHT_VIEWER_PLUGIN).': <code>'.self::getLogsPath().'</code>';
			
			$requirements = array(
				array(
					'name' => 'PHP',
					'status' => version_compare(PHP_VERSION, '5.3.0', '>='),
					'success' => sprintf(__('is %s or higher',PDF_LIGHT_VIEWER_PLUGIN), '5.3'),
					'fail' => sprintf(__('is lower than %s',PDF_LIGHT_VIEWER_PLUGIN), '5.3'),
					'description' => ''
				),
				array(
					'name' => __('ImageMagick or GraphicsMagick PHP Extension',PDF_LIGHT_VIEWER_PLUGIN),
					'status' => (extension_loaded('imagick') || extension_loaded('gmagick')),
					'success' => __('is loaded',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => __('is not loaded',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => __("ImageMagick/GraphicsMagick PHP Extension is required for PDF -> JPEG convertation. It cannot be included with the plugin unfortunately, so you or your hosting provider/server administrator should install it.",PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => __('Imagick or Gmagick PHP Wrapper',PDF_LIGHT_VIEWER_PLUGIN),
					'status' => $Imagick,
					'success' => __('is supported',PDF_LIGHT_VIEWER_PLUGIN).($ImagickVersion ? '. v.'.$ImagickVersion['versionString'] : ''),
					'fail' => __('is not supported',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => __("Imagick/Gmagick PHP Wrapper is required to make available Imagick PHP Extension functionality in the plugin. Usually it's integrated through the PECL plugin. It cannot be included with the plugin unfortunately, so you or your hosting provider/server administrator should install it.",PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => __('Imagick or Gmagick PDF Support',PDF_LIGHT_VIEWER_PLUGIN),
					'status' => ($Imagick && $pdf_format_support),
					'success' => __('is enabled',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => __('is not enabled',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => __("Imagick/Gmagick PDF Support is required for PDF -> JPEG convertation.",PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => 'GhostScript',
					'status' => $ghostscript_version,
					'success' => __('is supported',PDF_LIGHT_VIEWER_PLUGIN).($ghostscript_version && is_string($ghostscript_version) ? '. v.'.$ghostscript_version : ''),
					'fail' => __('is not supported',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => __("GhostScript is required for Imagick/Gmagick PDF Support. For cases, when you are sure that GhostScript is installed, but it was not detected by the plugin correctly you can disable this requirement in options below.",PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => $upload_dir_message,
					'status' => PdfLightViewer_Plugin::createUploadDirectory(),
					'success' => __('is writable',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => __('is not writable',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => __("This is the folder for converted images.",PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => $logs_dir_message,
					'status' => self::createLogsDirectory() && is_writable(self::getLogsPath()),
					'success' => __('is writable',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => __('is not writable',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => __("We will save plugin-specific log files in this folder.",PDF_LIGHT_VIEWER_PLUGIN)
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
        
    public static function getXMagick() {
        $Imagick = null;
        
        if (class_exists('Imagick') && class_exists('Gmagick')) {
            if (PdfLightViewer_AdminController::getSetting('prefer-xmagick') == 'Imagick') {
                $Imagick = new Imagick();
            }
            else {
                $Imagick = new Gmagick();
            }
        }
        else if (class_exists('Imagick')) {
            $Imagick = new Imagick();
        }
        else if (class_exists('Gmagick')) {
            $Imagick = new Gmagick();
        }
        
        return $Imagick;
    }
		
    public static function getGhostscript() {
        $gsPath = null;
        $ghostscript_version = null;
            
        if (function_exists('shell_exec')) {
            
            if (stristr(php_uname('s'), 'win')) {
                $gsPath = 'gs';
                $ghostscript_version = @shell_exec($gsPath.' --version');
            }
            else {
                $gsPath = '$(command -v gs)';
                $ghostscript_version = @shell_exec($gsPath.' --version');
                
                if (!$ghostscript_version) {
                    $gsPath = '$(which gs)';
                    $ghostscript_version = @shell_exec($gsPath.' --version');
                }
                
                if (!$ghostscript_version) {
                    $gsPath = 'gs';
                    $ghostscript_version = @shell_exec($gsPath.' --version');
                }
            }
        }
        
        return array($gsPath, $ghostscript_version);
    }
		
	public static function init() {
		// initialization
			register_activation_hook(PDF_LIGHT_VIEWER_FILE, array('PdfLightViewer_Plugin','activation'));
			
		// plugin actions
			add_filter('plugin_action_links', array('PdfLightViewer_Plugin','registerPluginActions'), 10, 2);
			
		// Create Text Domain For Translations
			add_action('plugins_loaded', array('PdfLightViewer_Plugin','localization'));
			
		// run main action
			add_action('after_setup_theme', array('PdfLightViewer_Plugin', 'run'));
	}
		
		
	public static function run() {
		// third party
			if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
				include_once(PDF_LIGHT_VIEWER_APPPATH.'/vendor/autoload.php');
			}
            
            if (is_admin()) {
                require_once PDF_LIGHT_VIEWER_APPPATH.'/vendor/webdevstudios/cmb2/init.php';
            }
			
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/libraries/directory_helper.php');
		
		// 	
		if (!class_exists('PdfLightViewer_AssetsController')) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/controllers/AssetsController.php');
		}
		
		if (!class_exists('PdfLightViewer_AdminController')) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/controllers/AdminController.php');
		}
		
		if (!class_exists('PdfLightViewer_FrontController')) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/controllers/FrontController.php');
		}
		
		if (!class_exists('PdfLightViewer_PdfController')) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/controllers/PdfController.php');
		}
		
		if ( defined('WP_CLI') && WP_CLI ) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/controllers/CLIController.php');
		}
		
		if (!class_exists('PdfLightViewer_Model')) {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/models/Model.php');
		}
		
		// assets
			if (is_admin()) {
				add_action('admin_enqueue_scripts', array('PdfLightViewer_AssetsController', 'admin_head'));
			}
			else {
				add_action('wp_enqueue_scripts', array('PdfLightViewer_AssetsController', 'frontendRegister'));
			}
		
		// post types
			add_action('init', array('PdfLightViewer_Plugin','registerPostTypes'));
            add_action('init', array('PdfLightViewer_FrontController','init'));
		
		// shortcodes
			PdfLightViewer_Plugin::registerShortcodes();
		
		//ADMIN
		if (is_admin() && (current_user_can('edit_posts') || current_user_can('edit_pages'))) {
			
			// settings init
                add_action('admin_init', array('PdfLightViewer_AdminController','init'));
				add_action('admin_notices', array('PdfLightViewer_AdminController', 'initGentleNotifications'));
				
			// admin page
				add_action('admin_menu', array('PdfLightViewer_AdminController','registerMenuPage'));
				
			// admin ajax
				add_action('admin_init', array('PdfLightViewer_AdminController','registerAjaxHandlers'));
				
			// notifications init
				add_action('admin_notices', array('PdfLightViewer_AdminController','showAdminNotifications'));
		}
		
		add_action('admin_notices', array('PdfLightViewer_AdminController','showActivationMessages'));
		
		if (!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed')) {
			add_action('admin_enqueue_scripts', array('PdfLightViewer_AdminController','showActivationPointers'));
		}
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed', true);
	}

}
