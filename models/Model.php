<?php if (!defined('WPINC')) die();

class PdfLightViewer_Model {

	public static $unimported = null;
	public static function getOneUnimported() {
		
		$query = new WP_Query(array(
			'post_type' => PdfLightViewer_PdfController::$type,
			'meta_query' => array(
				array(
					'key' => '_pdf-light-viewer-import-progress',
					'value' => 100,
					'compare' => '<',
					'type' => 'NUMERIC'
				)
			),
			'nopaging' => true
		));
		
		if ($query->have_posts()) {
			$unimported = $query->post;
			return $query->post;
		}
		else {
			return array();
		}
	}
}
