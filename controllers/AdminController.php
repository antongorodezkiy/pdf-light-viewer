<?php if (!defined('WPINC')) die();

class PdfLightViewer_AdminController {

	// settings
		public static function registerMenuPage() {
			add_options_page('PDF Light Viewer', 'PDF Light Viewer', 'manage_options', PDF_LIGHT_VIEWER_PLUGIN, array('PdfLightViewer_AdminController','showSettings'));
		}
	
		public static function showSettings() {
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/views/settings.php');
		}
		
		
		public static function settingsInit() {
			register_setting(PDF_LIGHT_VIEWER_PLUGIN, PDF_LIGHT_VIEWER_PLUGIN.'-add-to-post-content');
		}
}
