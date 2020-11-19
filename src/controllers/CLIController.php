<?php if (!defined('WPINC')) die();
/**
 * PDF Light Viewer Plugin cli interface
 */
class PdfLightViewer_CLIController extends WP_CLI_Command
{
    protected function checkRequirements()
    {
        $plugin_title = PdfLightViewer_Helpers_Plugins::getPluginData('Title');
        $requirements_met = PdfLightViewer_Plugin::requirements(true);
        if (!$requirements_met) {
            $message = $plugin_title.': '
            .esc_html__('requirements not met, please check plugin settings page for more information.',PDF_LIGHT_VIEWER_PLUGIN);
            WP_ClI::error($message, true);
        }
        else {
            WP_CLI::log($plugin_title.': '.esc_html__("requirements are met, happy using!", PDF_LIGHT_VIEWER_PLUGIN));
        }
    }

    protected function getImageGeometry($pdf_file_path)
    {
        $width = null;
        $height = null;

        $im = PdfLightViewer_Plugin::getXMagick();

        $im->readImage($pdf_file_path);

        foreach($im as $_img) {
            $geometry = $_img->getImageGeometry();
            $width = $geometry['width'];
            $height = $geometry['height'];
            break;
        }

        if (!$width && method_exists($im, 'getImageGeometry')) {
            $geometry = $im->getImageGeometry();
            $width = $geometry['width'];
            $height = $geometry['height'];
        }

        $im->destroy();
        unset($im);

        return array($width, $height);
    }

    protected function processPdf(
        $pdf_file_path,
        $jpeg_compression_quality,
        $jpeg_resolution,
        $output_biggest_side,
        $post_status,
        $import_pdf_file,
        &$all_pdfs_progress,
        $force
    )
    {
        if (!$force) {
            $importedPdf = PdfLightViewer_Model::getPdfByImportPath($pdf_file_path);

            if (!empty($importedPdf)) {
                $all_pdfs_progress->tick();
                return WP_CLI::success(
                    vsprintf(esc_html__('Import of PDF %s skipped, PDF post already exists with ID %d', PDF_LIGHT_VIEWER_PLUGIN), [$pdf_file_path, $importedPdf->ID])
                );
            }
        }

        // get number of pages and geometry
        $pdf_pages_number = PdfLightViewer_PdfController::getPDFPagesNumber($pdf_file_path);
        list($width, $height) = static::getImageGeometry($pdf_file_path);

        if (function_exists('\WP_CLI\Utils\make_progress_bar')) {
            $current_pdf_progress = \WP_CLI\Utils\make_progress_bar(
                sprintf(esc_html__("Processing PDF file %s", PDF_LIGHT_VIEWER_PLUGIN), $pdf_file_path),
                $pdf_pages_number
            );
        } else {
            $current_pdf_progress = new \cli\progress\Bar(
                sprintf(esc_html__("Processing PDF file %s", PDF_LIGHT_VIEWER_PLUGIN), $pdf_file_path),
                $pdf_pages_number
            );
        }

        // create PDF post
            $post_id = wp_insert_post(array(
                'post_type' => PdfLightViewer_PdfController::$type,
                'post_status' => $post_status,
                'post_name' => sanitize_title(pathinfo($pdf_file_path, PATHINFO_FILENAME)),
                'post_title' => pathinfo($pdf_file_path, PATHINFO_FILENAME),
            ));

        if (is_wp_error($post_id)) {
            WP_CLI::error(sprintf(esc_html__("Could not create PDF post: %s", PDF_LIGHT_VIEWER_PLUGIN), $post_id->get_error_message()), false);
        } else {

            // save pdf to media library
                if ($import_pdf_file) {
                    $image_data = file_get_contents($pdf_file_path);
                    $attach_id = PdfLightViewer_Components_Thumbnail::create_media_from_data(
                        pathinfo($pdf_file_path, PATHINFO_BASENAME),
                        $image_data
                    );
                    update_post_meta($post_id, 'pdf_file_id', $attach_id);
                }

            $pdf_upload_dir = PdfLightViewer_Components_Uploader::createUploadDirectory($post_id);
            $current_page = 1;
            $ratio = $width / $height;

            do_action(PDF_LIGHT_VIEWER_PLUGIN.':before_import', $post_id, $pdf_file_path);

            update_post_meta($post_id, '_pdf-light-viewer-import-status', PdfLightViewer_PdfController::STATUS_CLI_PROCESSING);
            update_post_meta($post_id, '_pdf-light-viewer-import-progress', 0);
            update_post_meta($post_id, '_pdf-light-viewer-import-current-page', $current_page);
            update_post_meta($post_id, '_pdf-light-viewer-import-path', $pdf_file_path);
            update_post_meta($post_id, 'pdf-pages-number', $pdf_pages_number);
            update_post_meta($post_id, 'pdf-page-width', $width);
            update_post_meta($post_id, 'pdf-page-height', $height);

            // process pages
            for ($current_page; $current_page <= $pdf_pages_number; $current_page++) {
                $page_number = sprintf('%1$05d',$current_page);
                $current_pdf_progress->tick();

                if (file_exists($pdf_upload_dir.'/page-'.$page_number.'.jpg')) continue;

                try {
                    PdfLightViewer_PdfController::process_pdf_page(
                        $post_id,
                        $current_page,
                        $current_page,
                        $page_number,
                        $pdf_pages_number,
                        $pdf_file_path,
                        $pdf_upload_dir,
                        $jpeg_resolution,
                        $jpeg_compression_quality,
                        $ratio,
                        $output_biggest_side
                    );
                } catch (Exception $e) {
                    PdfLightViewer_Components_Logger::log('Import exception: '.$e->getMessage(), print_r($e, true));
                    $error = $e->getMessage();
                    update_post_meta($post_id,'_pdf-light-viewer-import-status', PdfLightViewer_PdfController::STATUS_FAILED);
                    WP_CLI::warning(sprintf(esc_html__('Import of PDF %s failed: %s', PDF_LIGHT_VIEWER_PLUGIN), $pdf_file_path, $error), false);
                }
            }

            do_action(PDF_LIGHT_VIEWER_PLUGIN.':after_import', $post_id, $pdf_file_path);
            do_action(PDF_LIGHT_VIEWER_PLUGIN.':finished_import', $post_id, $pdf_file_path);
            update_post_meta($post_id, '_pdf-light-viewer-import-status', PdfLightViewer_PdfController::STATUS_FINISHED);
            WP_CLI::success(sprintf(esc_html__('Import of PDF %s finished', PDF_LIGHT_VIEWER_PLUGIN), $pdf_file_path));
        }

        $all_pdfs_progress->tick();
    }

	/**
     * Bulk PDF import
     *
     * ## OPTIONS
     *
     * <source-dir>
     * : required, source directory to import PDF files
     *
     * <jpeg-compression-quality>
     * : optional, jpeg compression quality, default 60
     *
     * <jpeg-resolution>
     * : optional, jpeg resolution, default 300
     *
     * <output-biggest-side>
     * : optional, output biggest side, default 1024
     *
     * <post-status>
     * : optional, PDF post status, default "draft"
     *
     * <import-pdf-file>
     * : flag, if set then import PDF file to Wordpress media library
     *
     * <force>
     * : flag, ignore the check whether the PDF was already imported
     *
     * ## EXAMPLES
     *
     *     wp pdf-light-viewer bulk-import --source-dir="/path/to/pdfs"
     *     wp pdf-light-viewer bulk-import --source-dir="/path/to/pdfs" --jpeg-compression-quality=60 --jpeg-resolution=300 --output-biggest-side=1024 --post-status=publish --import-pdf-file --force
     *
     * @synopsis --source-dir=<source-dir> [--jpeg-compression-quality=<jpeg-compression-quality>] [--jpeg-resolution=<jpeg-resolution>] [--output-biggest-side=<output-biggest-side-width>] [--post-status=<post-status>] [--import-pdf-file] [--force]
     * @subcommand bulk-import
     */
    public function bulk_import($args, $assoc_args) {

		// options
			$source_dir = $assoc_args['source-dir'];
			$jpeg_compression_quality = (
				isset($assoc_args['jpeg-compression-quality'])
				? (int)$assoc_args['jpeg-compression-quality']
				: 60
			);
			$jpeg_resolution = (
				isset($assoc_args['jpeg-resolution'])
				? (int)$assoc_args['jpeg-resolution']
				: 300
			);
            $output_biggest_side = (
				isset($assoc_args['output-biggest-side'])
				? (int)$assoc_args['output-biggest-side']
				: 1024
			);
			$post_status = (
				isset($assoc_args['post-status'])
				? $assoc_args['post-status']
				: 'draft'
			);

			$import_pdf_file = isset($assoc_args['import-pdf-file']);
			$force = isset($assoc_args['force']);

		// check requirements
			$this->checkRequirements();

		// check dir
			if (
				!is_readable($source_dir)
				|| !is_dir($source_dir)
			) {
				return WP_CLI::error(esc_html__("Source dir doesn't exist or it's not readable", PDF_LIGHT_VIEWER_PLUGIN), true);
			}
			else {
				WP_CLI::log(sprintf(esc_html__("Searching PDF files in %s", PDF_LIGHT_VIEWER_PLUGIN), $source_dir));
			}

		// check PDF files
			$pdf_files = glob($source_dir.'/*.pdf', GLOB_NOSORT);
			if (empty($pdf_files)) {
				return WP_CLI::error(esc_html__("Source dir doesn't contain PDF files", PDF_LIGHT_VIEWER_PLUGIN), true);
			}
			else {
				WP_CLI::log(sprintf(esc_html__("%d PDF files found", PDF_LIGHT_VIEWER_PLUGIN), count($pdf_files)));
			}

		// start import
		$pdf_files_count = count($pdf_files);

        if (function_exists('\WP_CLI\Utils\make_progress_bar')) {
            $all_pdfs_progress = \WP_CLI\Utils\make_progress_bar(
                esc_html__("Processing PDF files", PDF_LIGHT_VIEWER_PLUGIN),
    			$pdf_files_count
            );
        } else {
    		$all_pdfs_progress = new \cli\progress\Bar(
    			esc_html__("Processing PDF files", PDF_LIGHT_VIEWER_PLUGIN),
    			$pdf_files_count
    		);
        }

		foreach($pdf_files as $pdf_file_path) {
            $this->processPdf(
                $pdf_file_path,
                $jpeg_compression_quality,
                $jpeg_resolution,
                $output_biggest_side,
                $post_status,
                $import_pdf_file,
                $all_pdfs_progress,
                $force
            );
		}

        WP_CLI::success(esc_html__('Import finished', PDF_LIGHT_VIEWER_PLUGIN));
    }

}

WP_CLI::add_command(PDF_LIGHT_VIEWER_PLUGIN, 'PdfLightViewer_CLIController');
