<?php

class PdfLightViewer_Components_Uploader
{
    public static function createUploadDirectory($id = '')
    {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['basedir'];

		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		if (file_exists($main_upload_dir)) {
			$created = true;
		}
		else {
            try {
            	mkdir($main_upload_dir);
    		} catch (Exception $e) {
    			error_log($e);
    		}
		}

		if ($id) {
			$pdf_upload_dir = $main_upload_dir.'/'.$id;
			if (!file_exists($pdf_upload_dir)) {
                try {
                	mkdir($pdf_upload_dir);
        		} catch (Exception $e) {
        			error_log($e);
        		}
			}

			$pdf_thumbs_upload_dir = $main_upload_dir.'/'.$id.'-thumbs';
			if (!file_exists($pdf_thumbs_upload_dir)) {
                try {
                	mkdir($pdf_thumbs_upload_dir);
        		} catch (Exception $e) {
        			error_log($e);
        		}
			}

            $pdf_pdfs_upload_dir = $main_upload_dir.'/'.$id.'-pdfs';
			if (!file_exists($pdf_pdfs_upload_dir)) {
                try {
                	mkdir($pdf_pdfs_upload_dir);
        		} catch (Exception $e) {
        			error_log($e);
        		}
			}

			if (file_exists($pdf_upload_dir)) {
				return $pdf_upload_dir;
			}
			else {
				return false;
			}
		}

		if (file_exists($main_upload_dir)) {
			return $main_upload_dir;
		}
		else {
			return false;
		}
	}


	public static function getUploadDirectory($id = '')
    {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['basedir'];

		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		if ($id) {
			$pdf_upload_dir = $main_upload_dir.'/'.$id;
			return $pdf_upload_dir;
		}
		else {
			return $main_upload_dir;
		}
	}

	public static function getUploadDirectoryUrl($id)
    {
		$wp_upload_dir = wp_upload_dir();
		$basedir = $wp_upload_dir['baseurl'];

		$main_upload_dir = $basedir.'/'.PDF_LIGHT_VIEWER_PLUGIN;

		$pdf_upload_dir = $main_upload_dir.'/'.$id;

		return $pdf_upload_dir;
	}
}
