<?php if (!defined('WPINC')) die();

class PdfLightViewer_AdminController {
    
    public static function init() {
        static::settingsInit();
        
        if (!defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')) {
            add_action(PDF_LIGHT_VIEWER_PLUGIN.':settings_view_after_settings', array(__CLASS__, 'settings_view_after_settings'), 100);
        }
    }
    
    public static function settings_view_after_settings() {
		include_once(PDF_LIGHT_VIEWER_APPPATH.'/views/pro-placeholder.php');
	}
	
	public static function initGentleNotifications() {
		
		if (
			!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-pro-ad-viewed')
			&& !defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')
		) {
			PdfLightViewer_AdminController::showDirectMessage(
				sprintf(
					__('PDF Light Viewer Team: We created PRO Addon with printing, search and SEO-friendly mode <a class="button-primary js-pdf-light-viewer-hide-notification" data-notification="pro-ad-viewed" target="_blank" href="%s">Check It</a> <a class="button-secondary js-pdf-light-viewer-hide-notification" data-notification="pro-ad-viewed" href="#">Not interested</a>',PDF_LIGHT_VIEWER_PLUGIN),
					'http://codecanyon.net/item/pdf-light-viewer-pro-addon/14089505'
				)
			);
		}
		elseif (!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-survey-viewed')) {
			PdfLightViewer_AdminController::showDirectMessage(
				sprintf(
					__('PDF Light Viewer Team: Please, take part in our 1-minute survey to make PDF Light Viewer plugin better <a class="button-primary js-pdf-light-viewer-hide-notification" data-notification="survey-viewed" target="_blank" href="%s">Take Survey</a> <a class="button-secondary js-pdf-light-viewer-hide-notification" data-notification="survey-viewed" href="#">Not interested</a>',PDF_LIGHT_VIEWER_PLUGIN),
					'https://teamlead-power.typeform.com/to/Mr7eVs'
				)
			);
		}
		
        // imagetragick
        if (
			!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-imagetragick-viewed')
		) {
			PdfLightViewer_AdminController::showDirectMessage(
				sprintf(
					__('Protect your site! - ImageMagick Is On Fire — CVE-2016–3714 <a class="button-primary js-pdf-light-viewer-hide-notification" data-notification="imagetragick-viewed" target="_blank" href="%s">Learn how to protect yourself!</a> <a class="button-secondary js-pdf-light-viewer-hide-notification" data-notification="imagetragick-viewed" href="#">Hide this, I know how to protect my ImageMagick</a>',PDF_LIGHT_VIEWER_PLUGIN),
					'http://support.wp.teamlead.pw/q/warning-protect-your-site-imagemagick-is-on-fire%E2%80%8A-%E2%80%8Acve-2016-3714-multiple-vulnerabilities-in-imagemagick/'
				),
                true
			);
		}
	}
	
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
			
			if (!has_action('wp_ajax_'.PDF_LIGHT_VIEWER_PLUGIN.'_ping_import')) {
				add_action('wp_ajax_'.PDF_LIGHT_VIEWER_PLUGIN.'_ping_import', array('PdfLightViewer_PdfController','pdf_partially_import'));
			}
			
			add_action('wp_ajax_'.PDF_LIGHT_VIEWER_PLUGIN.'_notification_viewed', array(__CLASS__,'notification_viewed'));
            
            add_action('wp_ajax_'.PDF_LIGHT_VIEWER_PLUGIN.'_cancel_import', array('PdfLightViewer_PdfController', 'cancel_import'));
		}
		
		public static function notification_viewed() {
			if (!empty($_POST['notification'])) {
				switch($_POST['notification']) {
					case 'survey-viewed':
						update_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-survey-viewed', true);
					break;
					
					case 'pro-ad-viewed':
						update_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-pro-ad-viewed', true);
					break;
                
                    case 'imagetragick-viewed':
						update_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-imagetragick-viewed', true);
					break;
                
                    case 'installed-viewed':
						update_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-installed-viewed', true);
					break;
				}
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
				'show-post-type' => true,
				'do-not-check-gs' => false,
                'prefer-xmagick' => 'Imagick',
                'enable-hash-nav' => true,
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
                if (!get_option(PDF_LIGHT_VIEWER_PLUGIN.'-notification-installed-viewed')) {
                    self::showDirectMessage(
                        sprintf(
                            $plugin_title.': '.
                            __('requirements are met, happy using! <a class="button-primary js-pdf-light-viewer-hide-notification" data-notification="installed-viewed" target="_blank" href="%s">Check Settings</a> <a class="button-secondary js-pdf-light-viewer-hide-notification" data-notification="installed-viewed" href="#">Hide</a>',PDF_LIGHT_VIEWER_PLUGIN),
                            PdfLightViewer_Plugin::getSettingsUrl()
                        )
                    );
                }
			}
			else {
				self::showDirectMessage(
					$plugin_title.': '
					.sprintf(__('requirements not met, please check <a href="%s">plugin settings page</a> for more information.',PDF_LIGHT_VIEWER_PLUGIN),
                             PdfLightViewer_Plugin::getSettingsUrl())
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
