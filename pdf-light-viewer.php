<?php
/*
	Plugin Name: PDF Light Viewer Plugin
	Plugin URI: http://pdf-light-viewer.wp.teamlead.pw/
	Description: Wordpress plugin to embed normal, large and very large pdf documents to the wordpress site.
	Version: 1.0.0
	Author: E. Kozachek
	Author URI: http://eduard.kozachek.net/
	License: GPLv2
	Text Domain: pdf-light-viewer
*/

if (!defined('WPINC')) die();

define('PDF_LIGHT_VIEWER_PLUGIN','pdf-light-viewer');
define('PDF_LIGHT_VIEWER_APPPATH',dirname(__FILE__));
define('PDF_LIGHT_VIEWER_FILE',__FILE__);

if (!class_exists('PdfLightViewer_Plugin')) {
	include_once(PDF_LIGHT_VIEWER_APPPATH.'/controllers/Plugin.php');
}

// initialization
	register_activation_hook(__FILE__, array('PdfLightViewer_Plugin','activation'));
	
// plugin actions
	add_filter('plugin_action_links', array('PdfLightViewer_Plugin','registerPluginActions'), 10, 2);

function wp_pdf_light_viewer_init() {
	
	// third party
		include_once(PDF_LIGHT_VIEWER_APPPATH.'/vendor/autoload.php');
		
		function cmb_initialize_cmb_meta_boxes() {
			if (!class_exists('cmb_Meta_Box')) {
				require_once(PDF_LIGHT_VIEWER_APPPATH.'/vendor/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/init.php');
			}
		}
		add_action('init', 'cmb_initialize_cmb_meta_boxes', 9999);
		
		include_once(PDF_LIGHT_VIEWER_APPPATH.'/libraries/directory_helper.php');
	
	// 
	if (!class_exists('PdfLightViewer_PdfModel')) {
		include_once(PDF_LIGHT_VIEWER_APPPATH.'/models/PdfModel.php');
	}
	
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
	
	// assets
		if (is_admin()) {
			add_action('admin_enqueue_scripts', array('PdfLightViewer_AssetsController', 'admin_head'));
		}
		else {
			add_action('wp_enqueue_scripts', array('PdfLightViewer_AssetsController', 'frontend_head'));
		}
	
	// post types
		add_action('init', array('PdfLightViewer_Plugin','registerPostTypes'));
	
	// shortcodes
		PdfLightViewer_Plugin::registerShortcodes();
	
	//ADMIN
	if (is_admin() && (current_user_can('edit_posts') || current_user_can('edit_pages'))) {
		
		// settings init
			add_action('admin_init', array('PdfLightViewer_AdminController','settingsInit'));
			
		// admin page
			add_action( 'admin_menu', array('PdfLightViewer_AdminController','registerMenuPage'));
	}
}
add_action('after_setup_theme','wp_pdf_light_viewer_init');
