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
				),
				array(
					'key' => '_pdf-light-viewer-import-status',
					'value' => PdfLightViewer_PdfController::STATUS_FAILED,
					'compare' => '!='
				),
				array(
					'key' => '_pdf-light-viewer-import-status',
					'value' => PdfLightViewer_PdfController::STATUS_CLI_PROCESSING,
					'compare' => '!='
				),
				array(
					'key' => '_pdf-light-viewer-import-status',
					'value' => PdfLightViewer_PdfController::STATUS_FINISHED,
					'compare' => '!='
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
	
	public static function getPDFFileId($post_id) {
		
		if ((int)get_post_meta($post_id, 'pdf_file_id', true)) {
			$pdf_file_id = get_post_meta($post_id, 'pdf_file_id', true);
		}
		else {
			$pdf_file_id = get_post_meta($post_id, 'pdf_file', true);
		}
		
		return $pdf_file_id;
	}
}
