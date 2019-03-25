<?php

class PdfLightViewer_Models_MetaField
{
    public static function fields()
    {
        return array(
            'hide_thumbnails_navigation' => array(
                'name' => '<i class="slicons slicon-directions"></i> ' . esc_html__('Hide thumbnail navigation', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'hide_thumbnails_navigation',
                'type' => 'checkbox'
            ),

            'page_layout' => array(
                'name' => '<i class="slicons slicon-book-open"></i> ' . esc_html__('Flipbook page layout', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'page_layout',
                'type'    => 'select',
                'options' => array(
                    'adaptive' => esc_html__('Adaptive', PDF_LIGHT_VIEWER_PLUGIN),
                    'single' => esc_html__('Single', PDF_LIGHT_VIEWER_PLUGIN),
                    'double' => esc_html__('Double', PDF_LIGHT_VIEWER_PLUGIN)
                ),
                'default' => 'adaptive',
            ),

            'max_book_width' => array(
                'name' => '<i class="slicons slicon-frame"></i> ' . esc_html__('Max book width', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => '(px)',
                'id' => 'max_book_width',
                'type' => 'text'
            ),

            'max_book_height' => array(
                'name' => '<i class="slicons slicon-frame"></i> ' . esc_html__('Max book height', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => '(px)',
                'id' => 'max_book_height',
                'type' => 'text'
            ),

            'limit_fullscreen_book_height' => array(
                'name' => '<i class="slicons slicon-frame"></i> ' . esc_html__('Limit book height by the viewport in fullscreen mode', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('The book will fit the screen in fullscreen mode ', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'limit_fullscreen_book_height',
                'type' => 'checkbox'
            ),

            'disable_lazy_loading' => array(
                'name' => '<i class="slicons slicon-picture"></i> ' . esc_html__('Disable lazy loading', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('May be useful to prevent issues when using other lazy loading systems', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'disable_lazy_loading',
                'type' => 'checkbox'
            ),

            'disable_images_preloading' => array(
                'name' => '<i class="slicons slicon-picture"></i> ' . esc_html__('Disable images pre-loading', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('You may want to not preload images for big documents', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'disable_images_preloading',
                'type' => 'checkbox'
            ),

            'download_allowed' => array(
                'name' => '<i class="slicons slicon-cloud-download"></i> ' . esc_html__('Allow download', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('Check this if you want to show download button on the frontend', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_allowed',
                'type' => 'checkbox'
            ),

            'alternate_download_link' => array(
                'name' => '<i class="slicons slicon-link"></i> ' . esc_html__('Alternate download link', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('If not set, will be used link from PDF File', PDF_LIGHT_VIEWER_PLUGIN),
                'id'   => 'alternate_download_link',
                'type' => 'text',
                'default' => ''
            ),

            'download_page_allowed' => array(
                'name' => '<i class="slicons slicon-cloud-download"></i> ' . esc_html__('Allow per-page download', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('Check this if you want to show download button in the thumbnails to allow downloading of single page images', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_page_allowed',
                'type' => 'checkbox'
            ),

            'download_page_format' => array(
                'name' => '' . esc_html__('Per-page download format', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('Per page download in JPG or PDF formats', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_page_format',
                'type'    => 'select',
                'options' => array(
                    'jpg' => 'jpg',
                    'pdf' => 'pdf'
                ),
                'default' => 'jpg',
            ),

            'hide_fullscreen_button' => array(
                'name' => '<i class="slicons slicon-size-fullscreen"></i> ' . esc_html__('Hide fullscreen button', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'hide_fullscreen_button',
                'type' => 'checkbox'
            ),

            'disable_page_zoom' => array(
                'name' => '<i class="slicons slicon-magnifier"></i> ' . esc_html__('Disable page zoom', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'disable_page_zoom',
                'type' => 'checkbox'
            ),

            'zoom_magnify' => array(
                'name' => '<i class="slicons slicon-magnifier"></i> ' . esc_html__('Zoom magnify multiplier', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => esc_html__('This value is multiplied against the full size of the zoomed image. The default value is 1, meaning the zoomed image should be at 100% of its natural width and height.', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'zoom_magnify',
                'type' => 'text',
                'default' => 1
            ),

            'show_toolbar_next_previous' => array(
                'name' => '<i class="slicons slicon-arrow-left"></i><i class="slicons slicon-arrow-right"></i> ' . esc_html__('Show toolbar next and previous page arrows', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'show_toolbar_next_previous',
                'type' => 'checkbox'
            ),

            'show_toolbar_goto_page' => array(
                'name' => '<i class="slicons slicon-directions"></i> ' . esc_html__('Show toolbar go to page control', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'show_toolbar_goto_page',
                'type' => 'checkbox'
            ),

            'show_page_numbers' => array(
                'name' => '<i class="slicons slicon-info"></i> ' . esc_html__('Show page numbers', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'show_page_numbers',
                'type' => 'checkbox'
            ),
        );
    }

    public static function getFieldConfig($name)
    {
        $fields = static::fields();
        return !empty($fields[$name]) ? $fields[$name] : null;
    }
}
