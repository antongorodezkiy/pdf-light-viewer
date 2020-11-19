<?php

class PdfLightViewer_Plugin
{
	public static function activation()
    {
		self::includes();

		PdfLightViewer_Components_Uploader::createUploadDirectory();
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-pointers-viewed', false);
		update_option(PDF_LIGHT_VIEWER_PLUGIN.'-show-post-type', true);
	}

	public static function initEarlyActions()
    {

	}

	public static function registerPluginActions($links, $file)
    {
		if (stristr($file, PDF_LIGHT_VIEWER_PLUGIN.'/')) {
			$links = array_merge(array(
                '<a href="'.esc_attr(PdfLightViewer_Helpers_Url::getSettingsUrl()).'">' . esc_html__('Settings', PDF_LIGHT_VIEWER_PLUGIN) . '</a>',
    			'<a href="'.esc_attr(PdfLightViewer_Helpers_Url::getDocsUrl()).'">' . esc_html__('Docs', PDF_LIGHT_VIEWER_PLUGIN) . '</a>',
    			'<a target="_blank" href="'.esc_attr(PdfLightViewer_Helpers_Url::getSupportUrl()).'">' . esc_html__('Support', PDF_LIGHT_VIEWER_PLUGIN) . '</a>'
            ), $links);
		}
		return $links;
	}

	public static function localization()
    {
		load_plugin_textdomain(
			PDF_LIGHT_VIEWER_PLUGIN,
			false,
			dirname(plugin_basename(PDF_LIGHT_VIEWER_FILE)).'/resources/lang/'.get_locale().'/'
        );
	}

	// register post types
		public static function registerPostTypes()
        {
			PdfLightViewer_PdfController::init();
		}

	// register shortcodes
		public static function registerShortcodes()
        {
			add_shortcode(
				'pdf-light-viewer',
				array('PdfLightViewer_FrontController', 'display_pdf_book')
			);

            add_shortcode(
				'pdf-light-viewer-enqueue-assets',
                array('PdfLightViewer_AssetsController', 'frontendEnqueue')
			);
		}

	// plugin requirements
		public static function requirements($boolean = false)
        {
			$upload_dir_message = esc_html__('Upload folder',PDF_LIGHT_VIEWER_PLUGIN).': <code>'.PdfLightViewer_Components_Uploader::getUploadDirectory().'</code>';

			$host_url = site_url();

            $ImagickVersion = null;
            $pdf_format_support = self::getPDFFormatSupport();
            $Imagick = self::getXMagick();
            if ($Imagick) {
                $ImagickVersion = $Imagick->getVersion();
            }

			if (PdfLightViewer_AdminController::getSetting('do-not-check-gs')) {
				$ghostscript_version = true;
			}
			else {
                list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();
			}

			$logs_dir_message = esc_html__('Logs folder',PDF_LIGHT_VIEWER_PLUGIN).': <code>'.esc_html(PdfLightViewer_Components_Logger::getLogsPath()).'</code>';

			$requirements = array_filter(array(
				array(
					'name' => 'PHP',
					'status' => version_compare(PHP_VERSION, '5.3.0', '>='),
					'success' => sprintf(esc_html__('is %s or higher',PDF_LIGHT_VIEWER_PLUGIN), '5.3'),
					'fail' => sprintf(esc_html__('is lower than %s',PDF_LIGHT_VIEWER_PLUGIN), '5.3'),
					'description' => ''
				),
				!defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')
                    ? array(
    					'name' => esc_html__('ImageMagick or GraphicsMagick PHP Extension',PDF_LIGHT_VIEWER_PLUGIN),
    					'status' => (extension_loaded('imagick') || extension_loaded('gmagick')),
    					'success' => esc_html__('is loaded',PDF_LIGHT_VIEWER_PLUGIN),
    					'fail' => esc_html__('is not loaded',PDF_LIGHT_VIEWER_PLUGIN),
    					'description' => esc_html__("ImageMagick/GraphicsMagick PHP Extension is required for PDF -> JPEG convertation. It cannot be included with the plugin unfortunately, so you or your hosting provider/server administrator should install it.",PDF_LIGHT_VIEWER_PLUGIN)
    				)
                    : null,
                !defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')
    				? array(
    					'name' => esc_html__('Imagick or Gmagick PHP Wrapper',PDF_LIGHT_VIEWER_PLUGIN),
    					'status' => $Imagick,
    					'success' => esc_html__('is supported',PDF_LIGHT_VIEWER_PLUGIN).($ImagickVersion ? '. v.'.$ImagickVersion['versionString'] : ''),
    					'fail' => esc_html__('is not supported',PDF_LIGHT_VIEWER_PLUGIN),
    					'description' => esc_html__("Imagick/Gmagick PHP Wrapper is required to make available Imagick PHP Extension functionality in the plugin. Usually it's integrated through the PECL plugin. It cannot be included with the plugin unfortunately, so you or your hosting provider/server administrator should install it.",PDF_LIGHT_VIEWER_PLUGIN)
    				)
                    : null,
                !defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')
    				? array(
    					'name' => esc_html__('Imagick or Gmagick PDF Support',PDF_LIGHT_VIEWER_PLUGIN),
    					'status' => ($Imagick && $pdf_format_support),
    					'success' => esc_html__('is enabled',PDF_LIGHT_VIEWER_PLUGIN),
    					'fail' => esc_html__('is not enabled',PDF_LIGHT_VIEWER_PLUGIN),
    					'description' => esc_html__("Imagick/Gmagick PDF Support is required for PDF -> JPEG convertation.",PDF_LIGHT_VIEWER_PLUGIN)
    				)
                    : null,
                !defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')
    				? array(
    					'name' => 'GhostScript',
    					'status' => $ghostscript_version,
    					'success' => esc_html__('is supported',PDF_LIGHT_VIEWER_PLUGIN).($ghostscript_version && is_string($ghostscript_version) ? '. v.'.$ghostscript_version : ''),
    					'fail' => esc_html__('is not supported',PDF_LIGHT_VIEWER_PLUGIN),
    					'description' => esc_html__("GhostScript is required for Imagick/Gmagick PDF Support. For cases, when you are sure that GhostScript is installed, but it was not detected by the plugin correctly you can disable this requirement in options below.",PDF_LIGHT_VIEWER_PLUGIN)
    				)
                    : null,
				array(
					'name' => $upload_dir_message,
					'status' => PdfLightViewer_Components_Uploader::createUploadDirectory(),
					'success' => esc_html__('is writable',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => esc_html__('is not writable',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => esc_html__("This is the folder for converted images.",PDF_LIGHT_VIEWER_PLUGIN)
				),
				array(
					'name' => $logs_dir_message,
					'status' => PdfLightViewer_Components_Logger::createLogsDirectory() && is_writable(PdfLightViewer_Components_Logger::getLogsPath()),
					'success' => esc_html__('is writable',PDF_LIGHT_VIEWER_PLUGIN),
					'fail' => esc_html__('is not writable',PDF_LIGHT_VIEWER_PLUGIN),
					'description' => esc_html__("We will save plugin-specific log files in this folder.",PDF_LIGHT_VIEWER_PLUGIN)
				)
            ));


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

    public static function getXMagick()
    {
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

	public static function getPDFFormatSupport()
    {
        $Imagick = self::getXMagick();
        list($gsPath, $ghostscript_version) = self::getGhostscript();

        if ($gsPath && $ghostscript_version) {
            return true;
        }
        else if ($Imagick) {
            return in_array('PDF', $Imagick->queryFormats());
        }
        else {
            return false;
        }
	}

    public static function getGhostscript()
    {
        $gsPath = null;
        $ghostscript_version = null;

        if (function_exists('shell_exec')) {

            if (
                defined('PDF_LIGHT_VIEWER_GHOSTSCRIPT_PATH')
                && PDF_LIGHT_VIEWER_GHOSTSCRIPT_PATH
            ) {
                $gsPath = PDF_LIGHT_VIEWER_GHOSTSCRIPT_PATH;

                try {
                    $ghostscript_version = shell_exec($gsPath.' --version');
                } catch (Exception $e) {
                    error_log($e);
                }
            } else if (stristr(php_uname('s'), 'win')) {
                $gsPath = 'gs';

                try {
                    $ghostscript_version = shell_exec($gsPath.' --version');
                } catch (Exception $e) {
                    error_log($e);
                }
            } else {
                $gsPath = '$(command -v gs)';
                try {
                    $ghostscript_version = shell_exec($gsPath.' --version');
                } catch (Exception $e) {
                    error_log($e);
                }

                if (!$ghostscript_version) {
                    $gsPath = '$(which gs)';

                    try {
                        $ghostscript_version = shell_exec($gsPath.' --version');
                    } catch (Exception $e) {
                        error_log($e);
                    }
                }

                if (!$ghostscript_version) {
                    $gsPath = 'gs';

                    try {
                        $ghostscript_version = shell_exec($gsPath.' --version');
                    } catch (Exception $e) {
                        error_log($e);
                    }
                }
            }
        }

        return array($gsPath, $ghostscript_version);
    }

    public static function isGhostscriptAvailableViaCli()
    {
        list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();
        return (
            !empty($gsPath)
            && !empty($ghostscript_version)
        );
    }

	public static function init()
    {
		// initialization
			register_activation_hook(PDF_LIGHT_VIEWER_FILE, array('PdfLightViewer_Plugin','activation'));

		// plugin actions
			add_filter('plugin_action_links', array('PdfLightViewer_Plugin','registerPluginActions'), 10, 2);

		// Create Text Domain For Translations
			add_action('plugins_loaded', array('PdfLightViewer_Plugin','localization'));

		// run main action
			add_action('after_setup_theme', array('PdfLightViewer_Plugin', 'run'));
	}


	public static function run()
    {
		self::includes();

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

	protected static function includes()
	{
		// third party
			if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
				include_once(PDF_LIGHT_VIEWER_APPPATH.'/vendor/autoload.php');
			}

            if (is_admin()) {
                require_once PDF_LIGHT_VIEWER_APPPATH.'/vendor/webdevstudios/cmb2/init.php';
            }

			include_once(PDF_LIGHT_VIEWER_APPPATH.'/src/libraries/directory_helper.php');

        $includes = array_filter(array(
            'PdfLightViewer_Components_Assets' => '/components/Assets.php',
            'PdfLightViewer_Components_Logger' => '/components/Logger.php',
            'PdfLightViewer_Components_Uploader' => '/components/Uploader.php',
            'PdfLightViewer_Components_View' => '/components/View.php',
            'PdfLightViewer_Components_Thumbnail' => '/components/Thumbnail.php',

            'PdfLightViewer_Helpers_Http' => '/helpers/Http.php',
            'PdfLightViewer_Helpers_Url' => '/helpers/Url.php',
            'PdfLightViewer_Helpers_Plugins' => '/helpers/Plugins.php',
            'PdfLightViewer_Helpers_Server' => '/helpers/Server.php',

            'PdfLightViewer_AssetsController' => '/controllers/AssetsController.php',
            'PdfLightViewer_AdminController' => '/controllers/AdminController.php',
            'PdfLightViewer_FrontController' => '/controllers/FrontController.php',
            'PdfLightViewer_PdfController' => '/controllers/PdfController.php',
            'PdfLightViewer_CLIController' => ( defined('WP_CLI') && WP_CLI ) ? '/controllers/CLIController.php' : null,
            'PdfLightViewer_Model' => '/models/Model.php',
            'PdfLightViewer_Models_Meta' => '/models/Meta.php',
            'PdfLightViewer_Models_MetaField' => '/models/MetaField.php',
        ));
        foreach ($includes as $className => $includePath) {
            if (!class_exists($className, false)) {
    			include_once(PDF_LIGHT_VIEWER_APPPATH.'/src'.$includePath);
    		}
        }
	}

    /**********************
     * Legacy
     */

    public static function getDocsUrl()
    {
		return PdfLightViewer_Helpers_Url::getDocsUrl();
	}

	public static function getSupportUrl()
    {
		return PdfLightViewer_Helpers_Url::getSupportUrl();
	}

	public static function getSettingsUrl()
    {
		return PdfLightViewer_Helpers_Url::getSettingsUrl();
	}

	public static function get_post_meta($post_id, $key = '', $single = false)
    {
		return PdfLightViewer_Models_Meta::get_post_meta($post_id, $key, $single);
	}
}
