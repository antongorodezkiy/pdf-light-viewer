<?php
/*
	Plugin Name: PDF Light Viewer Plugin
	Plugin URI: http://pdf-light-viewer.wp.teamlead.pw/
	Description: Wordpress plugin to embed normal, large and very large pdf documents to the wordpress site as flipbooks with thumbnail navigation.
	Version: 1.3.17
	Author: Teamlead Power
	Author URI: http://teamlead.pw/
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
PdfLightViewer_Plugin::init();
