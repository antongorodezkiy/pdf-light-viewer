<?php

class PdfLightViewer_PdfController {

	const STATUS_SCHEDULED = 'scheduled';
	const STATUS_STARTED = 'started';
	const STATUS_PROCESSING = 'processing';
	const STATUS_CLI_PROCESSING = 'cli_processing';
	const STATUS_FINISHED = 'finished';
	const STATUS_FAILED = 'failed';

    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

	public static $type = 'pdf_lv';

    public static function getImagickVersion() {

        $Imagick = PdfLightViewer_Plugin::getXMagick();

        if (class_exists('Imagick') && $Imagick instanceof Imagick) {
            $v = Imagick::getVersion();
            preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $v['versionString'], $v);
        }
        else if (class_exists('Gmagick') && $Imagick instanceof Gmagick) {
            $v = $Imagick->getVersion();
            preg_match('/GraphicsMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $v['versionString'], $v);
        }

        return isset($v[1]) ? $v[1] : null;
    }

    public static function parsePages($pagesString)
    {
        $pages = array();

        $pagesExploded = explode(',', $pagesString);
        foreach($pagesExploded as $someRange) {
            if (strpos($someRange, '-')) {
                list($start, $end) = explode('-', $someRange);
                for($i = (int)$start; $i <= (int)$end; $i++) {
                    $pages[] = $i;
                }
            }
            else {
                $pages[] = (int)$someRange;
            }
        }

        return $pages;
    }

    public static function getQuickEditOptions()
    {
        $keys = array(
            'download_allowed',
            'download_page_allowed',
            'download_page_format',
            'hide_thumbnails_navigation',
            'hide_fullscreen_button',
            'disable_page_zoom',
            'zoom_magnify',
            'show_toolbar_next_previous',
            'show_toolbar_goto_page',
            'show_page_numbers',
            'page_layout',
            'max_book_width',
            'max_book_height',
            'limit_fullscreen_book_height',
            'disable_lazy_loading',
            'disable_images_preloading'
        );

        $options = array();
        foreach ($keys as $key) {
            $options[$key] = PdfLightViewer_Models_MetaField::getFieldConfig($key);
        }

        return $options;
    }

	public static function init() {

		self::register();

		// columns
			add_filter( 'manage_edit-'.self::$type.'_columns', array(__CLASS__, 'custom_columns_registration'), 10 );
			add_action( 'manage_'.self::$type.'_posts_custom_column', array(__CLASS__, 'custom_columns_views'), 10, 2 );
            add_filter( 'default_hidden_columns', array(__CLASS__, 'default_hidden_columns'), 10, 2 );
            add_action( 'quick_edit_custom_box', array(__CLASS__, 'quick_edit_custom_box'), 10, 2 );
            add_action( 'bulk_edit_custom_box', array(__CLASS__, 'quick_edit_custom_box'), 10, 2 );

		// metaboxes
			add_filter('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
			add_filter('cmb2_meta_boxes', array(__CLASS__, 'cmb_metaboxes'));

		// saving
			add_action('save_post_'.self::$type, array(__CLASS__, 'save_post'), 1000, 2);

		// show import message, which will show progress
			add_action('admin_notices', array(__CLASS__,'showImportProgressMessages'));

		// delete generated images
			add_action('deleted_post', array(__CLASS__, 'deleted_post'));
	}

	public static function register() {
		global $pagenow;

		register_post_type(self::$type,
			array(
				'labels' => array(
					'name' => esc_html__('PDFs', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => esc_html__('PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => esc_html__('Import PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new' => esc_html__('Import PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'edit' => esc_html__('Edit', PDF_LIGHT_VIEWER_PLUGIN),
					'edit_item' => esc_html__('Edit PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'new_item' => esc_html__('New PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'view' => esc_html__('View', PDF_LIGHT_VIEWER_PLUGIN),
					'view_item' => esc_html__('View PDF', PDF_LIGHT_VIEWER_PLUGIN),
					'search_items' => esc_html__('Search PDFs', PDF_LIGHT_VIEWER_PLUGIN),
					'not_found' => esc_html__('No PDFs found', PDF_LIGHT_VIEWER_PLUGIN),
				),
				'description' => esc_html__('For PDFs', PDF_LIGHT_VIEWER_PLUGIN),
				'public' => false,
				'show_ui' => (bool)PdfLightViewer_AdminController::getSetting('show-post-type'),
				'_builtin' => false,
				'capability_type' => 'post',
				'menu_icon' => plugins_url('resources/assets/img/pdf.png', PDF_LIGHT_VIEWER_FILE),
				'hierarchical' => false,
				'map_meta_cap' => true,
				'supports' => array('title', 'thumbnail'),
			)
		);

        register_taxonomy(self::$type.'_category', self::$type,
			array(
				'hierarchical' => true,
				'labels' => array(
					'name' => esc_html__('Categories', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => esc_html__('Category', PDF_LIGHT_VIEWER_PLUGIN),
					'search_items' =>  esc_html__('Search in Category', PDF_LIGHT_VIEWER_PLUGIN),
					'popular_items' => esc_html__('Popular Categories', PDF_LIGHT_VIEWER_PLUGIN),
					'all_items' => esc_html__('All Categories', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item' => esc_html__('Parent Category', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item_colon' => esc_html__('Parent Category:', PDF_LIGHT_VIEWER_PLUGIN),
					'edit_item' => esc_html__('Edit Category', PDF_LIGHT_VIEWER_PLUGIN),
					'update_item' => esc_html__('Update Category', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => esc_html__('Add New Category', PDF_LIGHT_VIEWER_PLUGIN),
					'new_item_name' => esc_html__('New Category Name', PDF_LIGHT_VIEWER_PLUGIN)
				),
				'public' => false,
				'show_ui' => (bool)PdfLightViewer_AdminController::getSetting('show-post-type'),
				'query_var' => false,
				'show_admin_column' => true
			)
		);

		register_taxonomy(self::$type.'_tag', self::$type,
			array(
				'hierarchical' => false,
				'labels' => array(
					'name' => esc_html__('Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'singular_name' => esc_html__('Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'search_items' =>  esc_html__('Search in Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'popular_items' => esc_html__('Popular Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'all_items' => esc_html__('All Tags', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item' => esc_html__('Parent Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'parent_item_colon' => esc_html__('Parent Tag:', PDF_LIGHT_VIEWER_PLUGIN),
					'edit_item' => esc_html__('Edit Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'update_item' => esc_html__('Update Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'add_new_item' => esc_html__('Add New Tag', PDF_LIGHT_VIEWER_PLUGIN),
					'new_item_name' => esc_html__('New Tag Name', PDF_LIGHT_VIEWER_PLUGIN)
				),
				'public' => false,
				'show_ui' => (bool)PdfLightViewer_AdminController::getSetting('show-post-type'),
				'query_var' => false,
				'show_admin_column' => true
			)
		);

	}

	// after import started
		public static function showImportProgressMessages() {
            // for serverless we will handle unimported in other way
			if (!PdfLightViewer_Model::$unimported && !defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')) {
				PdfLightViewer_Model::$unimported = PdfLightViewer_Model::getOneUnimported();
			}

			if (!empty(PdfLightViewer_Model::$unimported) && !defined('PDF_LIGHT_VIEWER_SERVERLESS_PLUGIN')) {
				$status = PdfLightViewer_Models_Meta::get_post_meta(PdfLightViewer_Model::$unimported->ID,'_pdf-light-viewer-import-status', true);
				$progress = PdfLightViewer_Models_Meta::get_post_meta(PdfLightViewer_Model::$unimported->ID,'_pdf-light-viewer-import-progress', true);

				PdfLightViewer_AdminController::showDirectMessage(sprintf(
					esc_html__('%s PDF import is %s. %s%% is complete. Please do not leave the admin interface until the import would not finished. %s',PDF_LIGHT_VIEWER_PLUGIN)
                    . '<a class="button-secondary js-pdf-light-viewer-cancel-import" href="#">
                        '.esc_html__('Cancel', PDF_LIGHT_VIEWER_PLUGIN).'
                    </a>',
					'<i class="icons slicon-settings"></i> <b>'.esc_html(PdfLightViewer_Model::$unimported->post_title).'</b>',
					'<span class="js-pdf-light-viewer-current-status">'.esc_html($status).'</span>',
					'<span class="js-pdf-light-viewer-current-progress">'.intval($progress).'</span>',
					'<i><a href="#!" class="js-tip tip" title="'.esc_html__('Otherwise the import will be continued during your next visit.', PDF_LIGHT_VIEWER_PLUGIN).'"><span class="icons slicon-question"></span></a></i>'
				), false);
			}
		}

	public static function custom_columns_registration( $defaults )
    {
		$defaults['preview'] = esc_html__('Preview', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['usage'] = esc_html__('Usage', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['pages'] = esc_html__('Pages', PDF_LIGHT_VIEWER_PLUGIN);
		$defaults['import_status'] = esc_html__('Import status', PDF_LIGHT_VIEWER_PLUGIN);

        $editOptions = static::getQuickEditOptions();
        foreach ($editOptions as $key => $editOption) {
            $defaults[$key] = $editOption['name'];
        }

		return $defaults;
	}

	public static function custom_columns_views($column_name, $post_id)
    {
        $pdf_upload_dir = PdfLightViewer_Components_Uploader::getUploadDirectory($post_id);
		switch($column_name) {
			case 'usage':
				echo PdfLightViewer_Components_View::render('metabox.usage', array(
					'pdf_upload_dir' => $pdf_upload_dir,
					'pages' => directory_map($pdf_upload_dir),
					'post_id' => $post_id
				));
			break;

			case 'preview':
				if (has_post_thumbnail($post_id)) {
					$image_array = wp_get_attachment_image_src(get_post_thumbnail_id( $post_id ), 'full');
					$full_img_url = $image_array[0];
					?>
						<img class="pdf-light-viewer-dashboard-page-preview" src="<?php echo esc_attr($full_img_url);?>" alt="<?php echo esc_html__('Preview', PDF_LIGHT_VIEWER_PLUGIN);?>" />
					<?php
				}
			break;

			case 'pages':
				$directory_map = directory_map($pdf_upload_dir);

				$pdf_pages_number = (int)PdfLightViewer_Models_Meta::get_post_meta($post_id,'pdf-pages-number',true);

				if (!empty($directory_map)) {
					$count = count($directory_map);
				}
				else {
					$count = 0;
				}
				?>
					<?php echo (int)$count; ?> / <?php echo (int)$pdf_pages_number ?>
				<?php
			break;

			case 'import_status':
				$status = PdfLightViewer_Models_Meta::get_post_meta($post_id,'_pdf-light-viewer-import-status',true);
				$progress = (int)PdfLightViewer_Models_Meta::get_post_meta($post_id,'_pdf-light-viewer-import-progress',true);

				switch($status) {
					case self::STATUS_SCHEDULED:
						$status_label = esc_html__('Import scheduled',PDF_LIGHT_VIEWER_PLUGIN);
					break;

					case self::STATUS_STARTED:
						$status_label = esc_html__('Import started',PDF_LIGHT_VIEWER_PLUGIN);
					break;

					case self::STATUS_PROCESSING:
						$status_label = esc_html__('Import in progress',PDF_LIGHT_VIEWER_PLUGIN);
					break;

					case self::STATUS_FINISHED:
						$status_label = esc_html__('Import finished',PDF_LIGHT_VIEWER_PLUGIN);
					break;

					case self::STATUS_FAILED:
						$status_label = esc_html__('Import failed',PDF_LIGHT_VIEWER_PLUGIN);
					break;

                    default:
                        $status_label = esc_html__('Import status unknown',PDF_LIGHT_VIEWER_PLUGIN);
				}

				?>
					<div><?php echo esc_html($status_label) ?></div>
					<div><?php echo esc_html($progress) ?>%</div>
				<?php
			break;
		}

		if (in_array($column_name, array_keys(static::getQuickEditOptions()))) {
			echo PdfLightViewer_Models_Meta::get_post_meta($post_id, $column_name, true);
		}
	}

    // hide dashboard columns by default for bulk edit columns
    public static function default_hidden_columns($hidden, $screen)
    {
        if ( isset( $screen->id ) && $screen->id == 'edit-'.self::$type ) {
            $hidden = array_merge($hidden, array_keys(static::getQuickEditOptions()));
        }

        return $hidden;
    }

    public static function quick_edit_custom_box($column_name, $post_type)
    {
        $editOptions = static::getQuickEditOptions();

        if ($post_type == static::$type && in_array($column_name, array_keys($editOptions))) {
            echo '<div class="cmb2-wrap form-table">';
                (new CMB2_Field(array( 'field_args' => $editOptions[$column_name])))->render_field();
            echo '</div>';
        }
    }

	public static function cmb_metaboxes($meta_boxes)
    {
        global $post, $pagenow;

        if (is_admin() && !$post && PdfLightViewer_Helpers_Http::get('post')) {
			$post = get_post(PdfLightViewer_Helpers_Http::get('post'));
		}

		$meta_boxes['pdf_light_viewer_file_metabox'] = array(
			'id' => 'pdf_light_viewer_file_metabox',
			'title' => esc_html__('PDF', PDF_LIGHT_VIEWER_PLUGIN),
			'object_types' => array(self::$type), // post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array_filter(array(
				array(
					'name' => esc_html__('Enable import', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => '<b>'.esc_html__('Check to import or re-import PDF file', PDF_LIGHT_VIEWER_PLUGIN).'</b>',
					'id' => 'enable_pdf_import',
					'type' => 'checkbox'
				),
                PdfLightViewer_Plugin::isGhostscriptAvailableViaCli()
                    ? array(
                        'desc' => esc_html__('Check to convert colors from CMYK to RGB (use only if you are not satisfied with PDF color results)', PDF_LIGHT_VIEWER_PLUGIN),
                        'name' => esc_html__('Convert colors', PDF_LIGHT_VIEWER_PLUGIN),
    					'id' => 'enable_pdf_convert',
    					'type' => 'checkbox',
                    )
                    : array(
						'desc' => '',
                        'name' => esc_html__('Convert colors (not supported by server environment)', PDF_LIGHT_VIEWER_PLUGIN),
    					'id' => 'enable_pdf_convert',
    					'type' => '',
                    ),
                array(
					'name' => esc_html__('Import pages', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => esc_html__('Leave empty to import all. Use numbers for single pages or (e.g. 1-3) for ranges. Few numbers or ranges could be separated by commas (e.g. 2-5,7,9-15).', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'import_pages',
					'type' => 'text',
					'default' => ''
				),
				array(
					'name' => esc_html__('JPEG compression quality', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => esc_html__('Affects quality and size of resulting page images. Bigger value means better quality and bigger size; also will take more server resources during the import process.', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'jpeg_compression_quality',
					'type' => 'text',
					'default' => 60
				),
				array(
					'name' => esc_html__('JPEG resolution', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => esc_html__('Affects quality and size of resulting page images. Bigger value means better quality and bigger size; also will take more server resources during the import process.', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'jpeg_resolution',
					'type' => 'text',
					'default' => 300
				),
                array(
					'name' => esc_html__('Output biggest side', PDF_LIGHT_VIEWER_PLUGIN),
                    'desc' => esc_html__('Also affects quality and size of resulting page images. Bigger value means better quality and bigger size; also will take more server resources during the import process.', PDF_LIGHT_VIEWER_PLUGIN),
					'id'   => 'output_biggest_side',
					'type' => 'text',
					'default' => 1024
				),
				array(
					'name' => esc_html__('PDF File', PDF_LIGHT_VIEWER_PLUGIN),
					'desc' => esc_html__('Choose what PDF file will be imported. Also will be used as default link for downloading if download option is enabled.', PDF_LIGHT_VIEWER_PLUGIN),
					'id' => 'pdf_file',
					'type' => 'file',
                    'readonly' => 'readonly',
                    'options' => array(
                        'url' => false, // Hide the text input for the url
                    )
				)
			)),
		);

        $pageFiles = array();
        if ($post) {
            $pdf_upload_dir = PdfLightViewer_Components_Uploader::getUploadDirectory($post->ID);
    		$pageFiles = directory_map($pdf_upload_dir);
        }

        if (!empty($pageFiles) || $pagenow != 'post-new.php') {
    		$meta_boxes['pdf_light_viewer_options_metabox'] = array(
    			'id' => 'pdf_light_viewer_options_metabox',
    			'title' => esc_html__('Output Options', PDF_LIGHT_VIEWER_PLUGIN),
    			'object_types' => array(self::$type), // post type
    			'context' => 'normal',
    			'priority' => 'high',
    			'show_names' => true, // Show field names on the left
    			'fields' => array(
                    PdfLightViewer_Models_MetaField::getFieldConfig('hide_thumbnails_navigation'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('page_layout'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('max_book_width'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('max_book_height'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('limit_fullscreen_book_height'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('disable_lazy_loading'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('disable_images_preloading'),
    			),
    		);

            $meta_boxes['pdf_light_viewer_toolbar_options_metabox'] = array(
    			'id' => 'pdf_light_viewer_toolbar_options_metabox',
    			'title' => esc_html__('Toolbar Options', PDF_LIGHT_VIEWER_PLUGIN),
    			'object_types' => array(self::$type), // post type
    			'context' => 'normal',
    			'priority' => 'high',
    			'show_names' => true, // Show field names on the left
    			'fields' => array(
                    PdfLightViewer_Models_MetaField::getFieldConfig('download_allowed'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('alternate_download_link'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('download_page_allowed'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('download_page_format'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('hide_fullscreen_button'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('disable_page_zoom'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('zoom_magnify'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('show_toolbar_next_previous'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('show_toolbar_goto_page'),
                    PdfLightViewer_Models_MetaField::getFieldConfig('show_page_numbers'),
    			),
    		);

            // export / import
            $exportConfig = null;
            if ($post) {
                $exportConfig = PdfLightViewer_FrontController::parseDefaultsSettings(array(), $post);
                $exportConfig = apply_filters(PDF_LIGHT_VIEWER_PLUGIN.':front_config', $exportConfig, $post);
                foreach ($exportConfig as $i => $value) {
                    if (in_array($i, array(
                        'title',
                        'template',
                        'download_link',
                        'alternate_download_link',
                        'pages',
                        'thumbs'
                    ))) {
                        unset($exportConfig[$i]);
                    }
                }
            }
            $meta_boxes['pdf_light_viewer_export_import_metabox'] = array(
    			'id' => 'pdf_light_viewer_export_import_metabox',
    			'title' => esc_html__('Export/Import Options', PDF_LIGHT_VIEWER_PLUGIN),
    			'object_types' => array(self::$type), // post type
    			'context' => 'normal',
    			'priority' => 'low',
    			'show_names' => true, // Show field names on the left
    			'fields' => array(
    				array(
    					'name' => '' . esc_html__('Export configuration', PDF_LIGHT_VIEWER_PLUGIN),
    					'id'   => 'export_config',
    					'type' => 'textarea',
    					'default' => json_encode($exportConfig),
                        'attributes'  => array(
                    		'rows'        => 3,
                            'readonly' => true,
                    	),
    				),
    				array(
    					'name' => '' . esc_html__('Import configuration', PDF_LIGHT_VIEWER_PLUGIN),
    					'id'   => 'import_config',
    					'type' => 'textarea',
                        'attributes'  => array(
                    		'placeholder' => esc_html__('Paste configuration here to import...', PDF_LIGHT_VIEWER_PLUGIN),
                    		'rows'        => 3,
                            'class' => 'pure-input-1-1'
                    	),
    				)
    			),
    		);

    		// user roles pages limits
    		if ( !function_exists('get_editable_roles') ) {
    			require_once(ABSPATH.'/wp-admin/includes/user.php');
    		}

    		$editable_roles = get_editable_roles();
    		$roles = array(
    			'anonymous' => esc_html__('Anonymous / s2Member Level 0', PDF_LIGHT_VIEWER_PLUGIN)
    		);

    		foreach($editable_roles as $role_id => $editable_role) {
    			$roles[$role_id] = $editable_role['name'];
    		}

    		$meta_boxes['pdf_light_viewer_permissions_metabox'] = array(
    			'id' => 'pdf_light_viewer_permissions_metabox',
    			'title' => esc_html__('Permissions', PDF_LIGHT_VIEWER_PLUGIN),
    			'object_types' => array(self::$type), // post type
    			'context' => 'advanced',
    			'priority' => 'high',
    			'show_names' => true, // Show field names on the left
    			'fields' => array(
    				array(
    					'id'          => 'pdf_light_viewer_permissions_metabox_repeat_group',
    					'type'        => 'group',
    					'description' => esc_html__('Pages limits for different user roles', PDF_LIGHT_VIEWER_PLUGIN),
    					'options'     => array(
    						'group_title'   => esc_html__('Pages Limit', PDF_LIGHT_VIEWER_PLUGIN), // since version 1.1.4, {#} gets replaced by row number
    						'add_button'    => esc_html__('Add another limit', PDF_LIGHT_VIEWER_PLUGIN),
    						'remove_button' => esc_html__('Remove limit', PDF_LIGHT_VIEWER_PLUGIN),
    						'sortable'      => false, // beta
    					),
    					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
    					'fields'      => array(
    						array(
    							'name'    => esc_html__('User Role', PDF_LIGHT_VIEWER_PLUGIN),
    							'desc'    => esc_html__('Select role which you want to pages limit', PDF_LIGHT_VIEWER_PLUGIN),
    							'id'      => 'pages_limit_user_role',
    							'type'    => 'select',
    							'options' => $roles,
    							'default' => '',
    						),
    						array(
    							'name' => esc_html__('Visible pages limit', PDF_LIGHT_VIEWER_PLUGIN),
                                'desc' => esc_html__('Use for the higher limit. Has lower priority than "Visible pages"', PDF_LIGHT_VIEWER_PLUGIN),
    							'id'   => 'pages_limit_visible_pages',
    							'type' => 'text'
    						),
    					)
    				)
    			)
    		);
        }

		return $meta_boxes;
	}

    public static function isToolbarVisible($pdf_light_viewer_config) {
        return (
            !empty($pdf_light_viewer_config['download_allowed'])
            || !empty($pdf_light_viewer_config['download_page_allowed'])
            || empty($pdf_light_viewer_config['hide_fullscreen_button'])
            || empty($pdf_light_viewer_config['disable_page_zoom'])
            || !empty($pdf_light_viewer_config['print_allowed'])
            || !empty($pdf_light_viewer_config['print_page_allowed'])
            || !empty($pdf_light_viewer_config['enabled_archive'])
            || !empty($pdf_light_viewer_config['enabled_pdf_search'])
            || !empty($pdf_light_viewer_config['show_page_numbers'])
            || !empty($pdf_light_viewer_config['show_toolbar_next_previous'])
            || !empty($pdf_light_viewer_config['show_toolbar_goto_page'])
        );
    }

    public static function getThemeImagePlaceholder($pdf_light_viewer_config)
    {
        if (!empty($pdf_light_viewer_config['theme'])) {
            switch ($pdf_light_viewer_config['theme']) {
                case self::THEME_DARK:
                    return plugins_url('resources/assets/img/dark-background.png',  PDF_LIGHT_VIEWER_FILE);
            }
        }

        return plugins_url('resources/assets/img/lightpaperfibers.png',  PDF_LIGHT_VIEWER_FILE);;
    }

    public static function getThemeClass($pdf_light_viewer_config = null)
    {
		if (empty($pdf_light_viewer_config)) {
			$theme = defined('PDF_LIGHT_VIEWER_PRO_PLUGIN')
				? PdfLightViewerPro_AdminController::getSetting('theme', PdfLightViewer_PdfController::THEME_LIGHT)
				: PdfLightViewer_PdfController::THEME_LIGHT;
		} else {
			$theme = !empty($pdf_light_viewer_config['theme'])
				? $pdf_light_viewer_config['theme']
				: PdfLightViewer_PdfController::THEME_LIGHT;
		}

        switch ($theme) {
            case self::THEME_DARK:
                return 'pdf-light-viewer--theme-dark';
        }

        return 'pdf-light-viewer--theme-light';
    }

	public static function add_meta_boxes() {
		global $pagenow;

		if ($pagenow != 'post-new.php') {
			// preview
				add_meta_box(
					'pdf_light_viewer_dashboard_preview',
					esc_html__('PDF Preview', PDF_LIGHT_VIEWER_PLUGIN),
					array(__CLASS__, 'metabox_dashboard_preview'),
					self::$type,
					'advanced',
					'low',
					array()
				);

			// usage
				add_meta_box(
					'pdf_light_viewer_dashboard_usage',
					esc_html__('Usage', PDF_LIGHT_VIEWER_PLUGIN),
					array(__CLASS__, 'metabox_dashboard_usage'),
					self::$type,
					'advanced',
					'high',
					array()
				);
		}
	}

	public static function metabox_dashboard_preview($post) {
		echo PdfLightViewer_FrontController::display_pdf_book(array('id' => $post->ID));
	}


	public static function metabox_dashboard_usage($post) {
        $pdf_upload_dir = PdfLightViewer_Components_Uploader::getUploadDirectory($post->ID);
        echo PdfLightViewer_Components_View::render('metabox.usage', array(
            'pdf_upload_dir' => $pdf_upload_dir,
            'pages' => directory_map($pdf_upload_dir),
            'post_id' => $post->ID
        ));
	}

	public static function save_post($post_id, $post = null)
    {
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) ) return;

        // save custom fields for quickbulk edit
        if (
            PdfLightViewer_Helpers_Http::get('bulk_edit')
            && PdfLightViewer_Helpers_Http::get('screen') == 'edit-'.static::$type
        ) {
            foreach ( CMB2_Boxes::get_all() as $cmb ) {
                if ( in_array($post->post_type, $cmb->prop( 'object_types' )) ) {
                    $cmb->save_fields( $post_id, 'post', $_GET );
                }
            }
        }

		if ( !$_POST ) return;

		if (current_user_can('edit_posts')) {
			$form_data = $_REQUEST;

			$pdf_file_id = (isset($form_data['pdf_file_id'])
                ? $form_data['pdf_file_id']
                : PdfLightViewer_Model::getPDFFileId($post_id));

			$pdf_file_path = get_attached_file($pdf_file_id);

            $pdf_upload_dir = PdfLightViewer_Components_Uploader::createUploadDirectory($post_id);

			if (
				(
					(
						isset($form_data['enable_pdf_import'])
						&& $form_data['enable_pdf_import'] == 'on'
					)
					|| (
						isset($form_data['enable_pdf_import'])
						&& (
							isset($form_data['enable_pdf_import']['cmb-field-0'])
							|| $form_data['enable_pdf_import']['cmb-field-0']
						)
					)
				)
				&& $pdf_file_id
			) {

				do_action(PDF_LIGHT_VIEWER_PLUGIN.':before_import_scheduled', $pdf_file_path);

				$jpeg_compression_quality = (isset($form_data['jpeg_compression_quality'])
                    ? (int) $form_data['jpeg_compression_quality']
                    : (int) get_post_meta($post_id, 'jpeg_compression_quality', true));
				$jpeg_resolution = (isset($form_data['jpeg_resolution'])
                    ? (int) $form_data['jpeg_resolution']
                    : (int) get_post_meta($post_id, 'jpeg_resolution', true));

				// delete all files
				self::delete_pages_by_pdf_id($post_id, $pdf_upload_dir);

                $pages_number = self::getPDFPagesNumber($pdf_file_path);
                $geometry = self::getFirstPageGeometry($jpeg_resolution, $jpeg_compression_quality, $pdf_upload_dir, $pdf_file_path);
                extract($geometry);

				if ($width && !empty($pages_number)) {

					update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_SCHEDULED);
					update_post_meta($post_id,'_pdf-light-viewer-import-progress', 0);
                    update_post_meta($post_id,'_pdf-light-viewer-import-current-page', 1);
                    update_post_meta($post_id, '_pdf-light-viewer-import-path', $pdf_file_path);

                    $importPages = array();
                    if (!empty($form_data['import_pages'])) {
                        $importPages = self::parsePages($form_data['import_pages']);

                        if (!empty($importPages)) {
                            $pages_number = count($importPages);
                        }
                    }

                    update_post_meta($post_id, 'pdf-import-pages', $importPages);
                    update_post_meta($post_id, 'pdf-pages-number', $pages_number);
					update_post_meta($post_id, 'pdf-page-width', $width);
					update_post_meta($post_id, 'pdf-page-height', $height);

                    if (!empty($form_data['enable_pdf_convert'])) {
                        update_post_meta($post_id, 'enable-pdf-convert', 1);
                    }

					PdfLightViewer_AdminController::showMessage(
						sprintf(esc_html__('PDF import scheduled.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
					, false);
				}
				else {
                    if (empty($pages_number)) {
                        PdfLightViewer_AdminController::showMessage(
                            sprintf(esc_html__('There was en error reading the source PDF file, please check the <a href="%s">plugin log</a> and server log for more details.', PDF_LIGHT_VIEWER_PLUGIN), PdfLightViewer_Plugin::getSettingsUrl())
                        , true);
                    } else {
                        PdfLightViewer_AdminController::showMessage(
                            sprintf(esc_html__('Imagick/Gmagick not found, please check other requirements on <a href="%s">plugin settings page</a> for more information.',PDF_LIGHT_VIEWER_PLUGIN),PdfLightViewer_Plugin::getSettingsUrl())
                        , true);
                    }
				}
			}

            // import config
            if (!empty($form_data['import_config'])) {
                $allowedMeta = array(
                    'download_allowed',
                    'download_page_allowed',
                    'download_page_format',
        			'hide_thumbnails_navigation',
        			'hide_fullscreen_button',
        			'disable_page_zoom',
                    'zoom_magnify',
                    'show_toolbar_next_previous',
                    'show_toolbar_goto_page',
                    'show_page_numbers',
                    'page_layout',
                    'max_book_width',
                    'max_book_height',
                    'limit_fullscreen_book_height',
                    'disable_lazy_loading',
        			'print_allowed',
        			'print_page_allowed',
        			'enabled_pdf_text',
        			'enabled_pdf_search',
        			'enabled_archive'
                );
                $metaToImport = str_replace('\"', '"', $form_data['import_config']);

                try {
                	$metaToImport = json_decode($metaToImport, true);
        		} catch (Exception $e) {
                    PdfLightViewer_Components_Logger::log('Parse PDF post config exception: '.$e->getMessage(), print_r($e, true));
        			error_log($e);
					$metaToImport = [];
        		}

                foreach ($metaToImport as $name => $value) {
                    if (in_array($name, $allowedMeta)) {

                        if ($value === true) $value = 'on';

                        if ($value === false) $value = 'off';

                        $_REQUEST[$name] = $value;
                        $_POST[$name] = $value;
                    }
                }
            }

            // save custom fields for quick edit
            if (PdfLightViewer_Helpers_Http::post('screen') == 'edit-'.static::$type) {
                foreach ( CMB2_Boxes::get_all() as $cmb ) {
            		if ( in_array($post->post_type, $cmb->prop( 'object_types' )) ) {
                        $cmb->save_fields( $post_id, 'post', $_POST );
            		}
            	}
            }
		}

		unset($_REQUEST['enable_pdf_import']);
		unset($_POST['enable_pdf_import']);
        unset($_REQUEST['enable_pdf_convert']);
        unset($_POST['enable_pdf_convert']);
        unset($_REQUEST['import_config']);
        unset($_POST['import_config']);
        unset($_REQUEST['export_config']);
        unset($_POST['export_config']);
	}

	public static function getPDFPagesNumber($pdf_file_path)
    {
        $Imagick = PdfLightViewer_Plugin::getXMagick();
        list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();

        $pagesNumber = 0;
        if ($gsPath && $ghostscript_version) {
            $commnad = $gsPath.' '
                .'-q '
                .'-dNODISPLAY '
                .'-dQUIET '

                // NOTE: needed for GS 9.50^ to avoid "Error: /invalidfileaccess in --file--"
                // Adds the designated list of directories at the head of the search path for library files.
                .'-I"'.escapeshellcmd(dirname($pdf_file_path)).'" '

                .'-c "('.escapeshellcmd($pdf_file_path).') (r) file runpdfbegin pdfpagecount = quit" ';

            try {
                $pagesNumber = (int)shell_exec($commnad);
            } catch (Exception $e) {
                PdfLightViewer_Components_Logger::log('Import exception with getting pages number: '.$e->getMessage(), print_r($e, true));
                error_log($e);
            }
        }

        // fallback to imagemagick
        if (empty($pagesNumber) && $Imagick) {
            try {
                $Imagick->readImage($pdf_file_path);
                $pagesNumber = $Imagick->getNumberImages();
                $Imagick->destroy();
            } catch (Exception $e) {
                PdfLightViewer_Components_Logger::log('Import exception with getting pages number: '.$e->getMessage(), print_r($e, true));
            }
        }

        return $pagesNumber;
    }

    public static function getFirstPageGeometry(
        $jpeg_resolution, $jpeg_compression_quality, $pdf_upload_dir, $pdf_file_path
    ) {
        $geometry = array(
            'width' => null,
            'height' => null,
        );
        $Imagick = PdfLightViewer_Plugin::getXMagick();
        list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();

        if ($gsPath && $ghostscript_version) {
            $current_page = 1;
            $page_number = sprintf('%1$05d', $current_page);

            // main page image
            // if possible, use ghostscript directly
            $commnad = $gsPath.' '
                .'-dBATCH '
                .'-dNOPAUSE '
                .'-dQUIET '
                .'-sDEVICE=jpeg '
                .'-r'.((int) $jpeg_resolution).' '
                .'-dJPEGQ='.((int) $jpeg_compression_quality).' '
                .'-dFirstPage='.((int) $current_page).' '
                .'-dLastPage='.((int) $current_page).' '
                .'-sOutputFile='.escapeshellcmd($pdf_upload_dir.'/page-'.$page_number.'.jpg').' '
                .escapeshellcmd($pdf_file_path);

            try {
                shell_exec($commnad);
            } catch (Exception $e) {
                PdfLightViewer_Components_Logger::log('Get first page geometry exception: '.$e->getMessage(), print_r($e, true));
                error_log($e);
            }

            $first_page_path = $first_page_image_path = $pdf_upload_dir.'/page-'.$page_number.'.jpg';
        }
        else {
            $first_page_path = $pdf_file_path;
        }

        if ($Imagick) {
            $Imagick->readImage($first_page_path);

            foreach($Imagick as $_img) {
                $geometry = $_img->getImageGeometry();
                break;
            }

            if (!$geometry['width'] && method_exists($Imagick, 'getImageGeometry')) {
                $geometry = $Imagick->getImageGeometry();
            }

            $Imagick->destroy();
        }

        if (file_exists($first_page_image_path) && is_file($first_page_image_path)) {
            unlink($first_page_image_path);
        }

        return $geometry;
    }

	public static function pdf_partially_import() {
		$unimported = PdfLightViewer_Model::getOneUnimported();
		$post_id = $unimported->ID;

		if (!$post_id) {
			return wp_send_json(array(
				'status' => 'error',
				'progress' => 0,
				'error' => esc_html__('Currently there are no unimported files in the queue.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}

		$status = PdfLightViewer_Models_Meta::get_post_meta($post_id,'_pdf-light-viewer-import-status',true);
		if ($status == self::STATUS_SCHEDULED) {
			$status_label = esc_html__('scheduled', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_STARTED);
		}
		else if ($status == self::STATUS_STARTED) {
			$status_label = esc_html__('started', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_PROCESSING);
		}
		else {
			$status_label = esc_html__('processing', PDF_LIGHT_VIEWER_PLUGIN);
		}

		ignore_user_abort(true);
        if (!ini_get('safe_mode')) {
            set_time_limit(0);
        }

		$pdf_file_id = PdfLightViewer_Model::getPDFFileId($post_id);
		$pdf_file_path = get_attached_file($pdf_file_id);

		do_action(PDF_LIGHT_VIEWER_PLUGIN.':before_import', $post_id, $pdf_file_path);

		$pdf_upload_dir = PdfLightViewer_Components_Uploader::createUploadDirectory($post_id);

        $enable_pdf_convert = (
            PdfLightViewer_Models_Meta::get_post_meta($post_id, 'enable-pdf-convert', true)
            && PdfLightViewer_Plugin::isGhostscriptAvailableViaCli()
        );

		$jpeg_resolution = (int) PdfLightViewer_Models_Meta::get_post_meta($post_id, 'jpeg_resolution', true);
		$jpeg_compression_quality = (int) PdfLightViewer_Models_Meta::get_post_meta($post_id, 'jpeg_compression_quality', true);

        $pdf_import_pages = (array) PdfLightViewer_Models_Meta::get_post_meta($post_id, 'pdf-import-pages' ,true);
		$pdf_pages_number = (int) PdfLightViewer_Models_Meta::get_post_meta($post_id, 'pdf-pages-number', true);
		$current_page = (int) PdfLightViewer_Models_Meta::get_post_meta($post_id, '_pdf-light-viewer-import-current-page', true);

		$width = (int)PdfLightViewer_Models_Meta::get_post_meta($post_id,'pdf-page-width',true);
		$height = (int)PdfLightViewer_Models_Meta::get_post_meta($post_id,'pdf-page-height',true);
        $output_biggest_side = (int)PdfLightViewer_Models_Meta::get_post_meta($post_id, 'output_biggest_side', true);
        if (!$output_biggest_side) {
            $output_biggest_side = 1024;
        }

		if (!$width || !$height) {
            return wp_send_json(array(
				'status' => 'error',
				'progress' => 0,
				'error' => esc_html__('Cannot get width and height of the first page.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}

		$ratio = $width / $height;

		if (!$current_page) {
			return wp_send_json(array(
				'status' => 'error',
				'progress' => 0,
				'error' => esc_html__('Cannot detect current imported PDF page.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}

        if ($enable_pdf_convert) {
            update_post_meta($post_id, 'enable-pdf-convert', false);
            self::convert_colors($pdf_file_path);
        }

		$error = '';
        $percent = null;

        if (empty($pdf_import_pages)) {
            for($current_page; $current_page <= $pdf_pages_number; $current_page++) {
                $page_number = sprintf('%1$05d',$current_page);
                if (!file_exists($pdf_upload_dir.'/page-'.$page_number.'.jpg')) {

                    try {
                        $percent = self::process_pdf_page(
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
                    }
                    catch(Exception $e) {
                        PdfLightViewer_Components_Logger::log('Import exception: '.$e->getMessage(), print_r($e, true));
                        $status_label = esc_html__('failed', PDF_LIGHT_VIEWER_PLUGIN);
                        $error = $e->getMessage();
                        update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_FAILED);
                    }

                    break;
                }
            }
        }
        else {
            foreach($pdf_import_pages as $current_page => $current_page_doc) {
                $current_page++;
                $page_number = sprintf('%1$05d',$current_page);
                if (!file_exists($pdf_upload_dir.'/page-'.$page_number.'.jpg')) {

                    try {
                        $percent = self::process_pdf_page(
                            $post_id,
                            $current_page,
                            $current_page_doc,
                            $page_number,
                            $pdf_pages_number,
                            $pdf_file_path,
                            $pdf_upload_dir,
                            $jpeg_resolution,
                            $jpeg_compression_quality,
                            $ratio,
                            $output_biggest_side
                        );
                    }
                    catch(Exception $e) {
                        PdfLightViewer_Components_Logger::log('Import exception: '.$e->getMessage(), print_r($e, true));
                        $status_label = esc_html__('failed', PDF_LIGHT_VIEWER_PLUGIN);
                        $error = $e->getMessage();
                        update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_FAILED);
                    }

                    break;
                }
            }
        }

		do_action(PDF_LIGHT_VIEWER_PLUGIN.':after_import', $post_id, $pdf_file_path);

		if ($percent >= 100) {
			do_action(PDF_LIGHT_VIEWER_PLUGIN.':finished_import', $post_id, $pdf_file_path);
			$status_label = esc_html__('finished', PDF_LIGHT_VIEWER_PLUGIN);
			update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_FINISHED);
		}

		return wp_send_json(array(
			'status' => $status_label,
			'progress' => (int)$percent,
			'error' => $error
		));
	}

    public static function cancel_import() {
		$unimported = PdfLightViewer_Model::getOneUnimported();
		$post_id = $unimported->ID;

		if (!$post_id) {
			return wp_send_json(array(
				'status' => 'error',
				'error' => esc_html__('Currently there are no unimported files in the queue.', PDF_LIGHT_VIEWER_PLUGIN)
			));
		}

		update_post_meta($post_id,'_pdf-light-viewer-import-status', self::STATUS_FAILED);

		return wp_send_json(array(
			'status' => 'error',
            'error' => esc_html__('Import cancelled', PDF_LIGHT_VIEWER_PLUGIN),
		));
	}

    public static function convert_colors($path)
    {
        list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();

        $commnad = $gsPath.' '
            .'-dSAFER '
            .'-dBATCH '
            .'-dNOPAUSE '
            .'-dNOCACHE '
            .'-dQUIET '
            .'-sDEVICE=pdfwrite '
            .'-sColorConversionStrategy=CMYK '
            .'-dProcessColorModel=/DeviceCMYK '
            .'-sOutputFile='.escapeshellcmd($path.'.new').' '
            .escapeshellcmd($path);

        try {
            shell_exec($commnad);
        } catch (Exception $e) {
            PdfLightViewer_Components_Logger::log('Convert colors exception: '.$e->getMessage(), print_r($e, true));
            error_log($e);
        }
    }

	public static function process_pdf_page(
        $post_id, $current_page, $current_page_doc,
        $page_number, $pdf_pages_number, $pdf_file_path,
        $pdf_upload_dir, $jpeg_resolution, $jpeg_compression_quality,
        $ratio, $output_biggest_side
    ) {

        $Imagick = PdfLightViewer_Plugin::getXMagick();
        $ImagickClass = get_class($Imagick);

		$_img = new $ImagickClass();

        if (class_exists('Imagick') && $Imagick instanceof Imagick) {
            $_img->setResolution($jpeg_resolution, $jpeg_resolution);
        }
        else if (class_exists('Gmagick') && $Imagick instanceof Gmagick && method_exists($_img, 'setResolution')) {
            $_img->setResolution($jpeg_resolution, $jpeg_resolution);
        }

        // main page image in PDF
        list($gsPath, $ghostscript_version) = PdfLightViewer_Plugin::getGhostscript();

        if (
            $gsPath
            && $ghostscript_version
        ) {
            $commnad = $gsPath.' '
                .'-dBATCH '
                .'-dNOPAUSE '
                .'-dQUIET '
                .'-sDEVICE=pdfwrite '
                .'-dFirstPage='.((int) $current_page_doc).' '
                .'-dLastPage='.((int) $current_page_doc).' '
                .'-sOutputFile='.escapeshellcmd($pdf_upload_dir.'-pdfs/page-'.$page_number.'.pdf').' '
                .escapeshellcmd($pdf_file_path);

            try {
            	shell_exec($commnad);
    		} catch (Exception $e) {
                PdfLightViewer_Components_Logger::log('Process PDF page exception (pdf): '.$e->getMessage(), print_r($e, true));
    			error_log($e);
    		}

            // main page image
            // if possible, use ghostscript directly
            $commnad = $gsPath.' '
                .'-dBATCH '
                .'-dNOPAUSE '
                .'-dQUIET '
                .'-sDEVICE=jpeg '
                .'-r'.((int)$jpeg_resolution).' '
                .'-dJPEGQ='.((int) $jpeg_compression_quality).' '
                .'-dFirstPage='.((int) $current_page_doc).' '
                .'-dLastPage='.((int) $current_page_doc).' '
                .'-sOutputFile='.escapeshellcmd($pdf_upload_dir.'/page-'.$page_number.'.jpg').' '
                .escapeshellcmd($pdf_file_path);

            try {
            	shell_exec($commnad);
    		} catch (Exception $e) {
                PdfLightViewer_Components_Logger::log('Process PDF page exception (jpg): '.$e->getMessage(), print_r($e, true));
    			error_log($e);
    		}

            $_img->readImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');
        }
        else {
            $_img->readImage($pdf_file_path.'['.($current_page_doc - 1).']');
        }

			$_img->setImageCompression($ImagickClass::COMPRESSION_JPEG);
			$_img->resizeImage($output_biggest_side, round($output_biggest_side/$ratio), $ImagickClass::FILTER_BESSEL, 1, false);

            if (class_exists('Imagick') && $Imagick instanceof Imagick) {
                $_img->setImageCompressionQuality($jpeg_compression_quality);
            }
            else if (class_exists('Gmagick') && $Imagick instanceof Gmagick) {
                $_img->setCompressionQuality($jpeg_compression_quality);
            }

			$_img->setImageFormat('jpg');

			// IMPORTANT: imagick changed SRGB and RGB profiles after vesion 6.7.6
            if (method_exists($_img, 'transformImageColorspace')) {
                if (
                    $_img->getImageColorspace() != $ImagickClass::COLORSPACE_RGB
                    && $_img->getImageColorspace() != $ImagickClass::COLORSPACE_SRGB
                ) {
                    if (version_compare(self::getImagickVersion(), '6.7.6', '>=')) {
                        $_img->transformImageColorspace($ImagickClass::COLORSPACE_SRGB);
                    }
                    else {
                        $_img->transformImageColorspace($ImagickClass::COLORSPACE_RGB);
                    }
                }
            }

            // main page image
                $white = new $ImagickClass();
                $white->newImage($output_biggest_side, round($output_biggest_side/$ratio), "white");
                $white->compositeimage($_img, $ImagickClass::COMPOSITE_OVER, 0, 0);
                $white->setImageFormat('jpg');
                $white->setImageColorspace($_img->getImageColorspace());
                $white->writeImage($pdf_upload_dir.'/page-'.$page_number.'.jpg');

            // thumbnail
                $_img->resizeImage(76, round(76/$ratio),$ImagickClass::FILTER_BESSEL, 1, false);

                $white = new $ImagickClass();
                $white->newImage(76, round(76/$ratio), "white");
                $white->compositeimage($_img, $ImagickClass::COMPOSITE_OVER, 0, 0);
                $white->setImageFormat('jpg');
                $white->setImageColorspace($_img->getImageColorspace());
                $white->writeImage($pdf_upload_dir.'-thumbs/page-'.$page_number.'-100x76.jpg');

			if ($current_page == 1) {
				$file = $pdf_upload_dir.'/page-'.$page_number.'.jpg';
				PdfLightViewer_Components_Thumbnail::set_featured_image($post_id, $file, 'pdf-'.$post_id.'-page-'.$page_number.'.jpg');
			}

			$percent = ($current_page / $pdf_pages_number) * 100;
			update_post_meta($post_id,'_pdf-light-viewer-import-progress', $percent);
			update_post_meta($post_id,'_pdf-light-viewer-import-current-page', $current_page);

		$_img->destroy();
		unset($_img);

		return $percent;
	}

	protected static function delete_pages_by_pdf_id($post_id, $pdf_upload_dir) {

		if (!$pdf_upload_dir) {
			return false;
		}

		$directory_map = directory_map($pdf_upload_dir);

		if (!empty($directory_map)) {
			foreach($directory_map as $file) {
				unlink($pdf_upload_dir.'/'.$file);
			}
		}

		$directory_map = directory_map($pdf_upload_dir.'-thumbs');
		if (!empty($directory_map)) {
			foreach($directory_map as $file) {
				unlink($pdf_upload_dir.'-thumbs/'.$file);
			}
		}

        $directory_map = directory_map($pdf_upload_dir.'-pdfs');
		if (!empty($directory_map)) {
			foreach($directory_map as $file) {
				unlink($pdf_upload_dir.'-pdfs/'.$file);
			}
		}

		do_action(PDF_LIGHT_VIEWER_PLUGIN.':delete_pages_by_pdf_id', $post_id, $pdf_upload_dir);
	}


	public static function deleted_post($post_id = '', $arg2 = '') {
		if ($post_id && get_post_type($post_id) == self::$type) {
			$pdf_upload_dir = PdfLightViewer_Components_Uploader::createUploadDirectory($post_id);

			if ($pdf_upload_dir) {
				self::delete_pages_by_pdf_id($post_id, $pdf_upload_dir);
				rmdir($pdf_upload_dir);
				rmdir($pdf_upload_dir.'-thumbs');
                rmdir($pdf_upload_dir.'-pdfs');
			}
		}
	}
}
