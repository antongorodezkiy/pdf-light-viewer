<?php if (!defined('WPINC')) die();

class PdfLightViewer_AdminController {
	
	// show message
		public static function showMessage($message, $errormsg = false) {
			
			if (!session_id() && !headers_sent()) {
				session_start();
			}
			
			if (!isset($_SESSION[PDF_LIGHT_VIEWER_PLUGIN.'admin_notice'])) {
				$_SESSION[PDF_LIGHT_VIEWER_PLUGIN.'admin_notice'] = array();
			}
			
			$_SESSION[PDF_LIGHT_VIEWER_PLUGIN.'admin_notice'][] = array(
				'text' => $message,
				'error' => $errormsg
			);
		}
		
		public static function showDirectMessage($message, $errormsg = false) {
			
			if ($errormsg) {
				$css_class = 'error';
			}
			else {
				$css_class = 'updated';
			}
			
			echo '<div class="'.$css_class.'"><p>'.$message.'</p></div>';
		}

		public static function showAdminNotifications() {
			if (!session_id() && !headers_sent()) {
				session_start();
			}
			
			if (isset($_SESSION[PDF_LIGHT_VIEWER_PLUGIN.'admin_notice'])) {
				foreach($_SESSION[PDF_LIGHT_VIEWER_PLUGIN.'admin_notice'] as $key => $notice) {
					
					if ($notice['error']) {
						$css_class = 'error';
					}
					else {
						$css_class = 'updated';
					}
					
					echo '<div class="'.$css_class.'"><p>'.$notice['text'].'</p></div>';
				}
				$_SESSION[PDF_LIGHT_VIEWER_PLUGIN.'admin_notice'] = array();
			}
		}
		
	// ajax
		public static function registerAjaxHandlers() {
			
			if (!PdfLightViewer_Model::$unimported) {
				PdfLightViewer_Model::$unimported = PdfLightViewer_Model::getOneUnimported();
			}
			if (!empty(PdfLightViewer_Model::$unimported)) {
				add_action('wp_ajax_'.PDF_LIGHT_VIEWER_PLUGIN.'_ping_import', array('PdfLightViewer_PdfController','pdf_partially_import'));
			}
		}

	// settings
		public static function registerMenuPage() {
			add_options_page('PDF Light Viewer', 'PDF Light Viewer', 'manage_options', PDF_LIGHT_VIEWER_PLUGIN, array('PdfLightViewer_AdminController','showSettings'));
		}
	
		public static function showSettings() {
			$requirements = PdfLightViewer_Plugin::requirements();
			include_once(PDF_LIGHT_VIEWER_APPPATH.'/views/settings.php');
		}
		
		public static function settingsInit() {
			register_setting(PDF_LIGHT_VIEWER_PLUGIN, PDF_LIGHT_VIEWER_PLUGIN);
		}

		public static function getSettings() {
			$config = array(
				'show-post-type' => true
			);	
			return wp_parse_args(get_option(PDF_LIGHT_VIEWER_PLUGIN),$config);
		}
		
		public static $cached_settings = null;
		public static function getSetting($name) {
			if (self::$cached_settings == null) {
				self::$cached_settings = self::getSettings();
			}
			
			if (isset(self::$cached_settings[$name])) {
				return self::$cached_settings[$name];
			}
			else {
				return null;
			}
		}
		
		
	// after install notifications
		public static function showActivationMessages() {
			$requirements_met = PdfLightViewer_Plugin::requirements(true);
			
			$plugin_title = PdfLightViewer_Plugin::getData('Title');
			
			if ($requirements_met) {
				self::showMessage($plugin_title.': '.__('requirements are met, happy using!',PDF_LIGHT_VIEWER_PLUGIN));
			}
			else {
				self::showMessage(
					$plugin_title.': '
					.sprintf(__('requirements not met, please check <a href="%s">plugin settings page</a> for more information.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
				, true);
			}
		}
		
		public static function showActivationPointers() {
			wp_enqueue_style('wp-pointer');
			wp_enqueue_script('wp-pointer');
			add_action('admin_print_footer_scripts', array('PdfLightViewer_AdminController','showPDFPointer'));
		}
		
		public static function showPDFPointer() {
			$plugin_title = PdfLightViewer_Plugin::getData('Title');
			$pointer_content = '<h3>'.$plugin_title.'</h3>';
			$pointer_content .= '<p>'.__("We have just created new section called PDFs in your dashboard. Use it to import and publish your cool PDF files.",PDF_LIGHT_VIEWER_PLUGIN).'</p>';
			
			?>
				<script type="text/javascript">
				(function($){
					$(document).ready( function($) {
						$('#menu-posts-<?php echo PdfLightViewer_PdfController::$type;?>').pointer({
							content: '<?php echo $pointer_content; ?>',
							position: {
								edge: 'left',
								align: 'center'
							},
							close: function() {
								// Once the close button is hit
							}
						}).pointer('open');
					});
				})(jQuery);
				</script>
			<?php
		}

}
