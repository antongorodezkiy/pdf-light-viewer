<?php

class PdfLightViewer_Plugin {
	
	public static function activation() {
		self::createUploadDirectory();
	}

	// plugin actions
		public static function registerPluginActions($links, $file) {
			if (stristr($file, PDF_LIGHT_VIEWER_PLUGIN)) {
				$settings_link = '<a href="options-general.php?page='.PDF_LIGHT_VIEWER_PLUGIN.'">' . __('Settings', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$docs_link = '<a href="'.self::getDocsUrl().'">' . __('Docs', PDF_LIGHT_VIEWER_PLUGIN) . '</a>';
				$links = array_merge(array($settings_link, $docs_link), $links);
			}
			return $links;
		}
		
		
	// register post types
		public static function registerPostTypes() {			
			PdfLightViewer_PdfController::init();
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
	
	public function getDocsUrl() {
		if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/documentation/index_'.WPLANG.'.html')) {
			$documentation_url = 'documentation/index'.WPLANG.'.html';
		}
		else {
			$documentation_url = 'documentation/index.html';
		}
		$documentation_url = plugins_url($documentation_url, PDF_LIGHT_VIEWER_FILE);
		return $documentation_url;
	}
	
	public function createUploadDirectory($id = '') {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['basedir'];
		
		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;
		
		if (!file_exists($main_upload_dir)) {
			mkdir($main_upload_dir);
		}
		
		if ($id) {
			$pdf_upload_dir = $main_upload_dir.'/'.$id;
			if (!file_exists($pdf_upload_dir)) {
				mkdir($pdf_upload_dir);
			}
			
			return $pdf_upload_dir;
		}
		
		return $main_upload_dir;
	}
	
	
	public function getUploadDirectory($id) {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['basedir'];
		
		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		$pdf_upload_dir = $main_upload_dir.'/'.$id;

		return $pdf_upload_dir;
	}
	
	public function getUploadDirectoryUrl($id) {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['baseurl'];
		
		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		$pdf_upload_dir = $main_upload_dir.'/'.$id;

		return $pdf_upload_dir;
	}

}
