<?php if (!defined('WPINC')) die();

class PdfLightViewer_AdminController {
	
	// show message
		public static function showMessage($message, $errormsg = false) {
			if ($errormsg) {
				echo '<div id="message" class="error">';
			}
			else {
				echo '<div id="message" class="updated fade">';
			}
			echo "<p><strong>$message</strong></p></div>";
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
			//register_setting(PDF_LIGHT_VIEWER_PLUGIN, PDF_LIGHT_VIEWER_PLUGIN.'-add-to-post-content');
		}
		
		
	// after install notifications
		public static function showActivationMessages() {
			$requirements_met = PdfLightViewer_Plugin::requirements(true);
			
			$plugin_title = PdfLightViewer_Plugin::getData('Title');
			
			if ($requirements_met) {
				self::showMessage($plugin_title.': '.__('requirements met, happy using!',PDF_LIGHT_VIEWER_PLUGIN));
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
