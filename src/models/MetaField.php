<?php

class PdfLightViewer_Models_MetaField
{
    public static function fields()
    {
        return array(
            'hide_thumbnails_navigation' => array(
                'name' => '<i class="slicons slicon-directions"></i> ' . __('Hide thumbnail navigation', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'hide_thumbnails_navigation',
                'type' => 'checkbox'
            ),

            'page_layout' => array(
                'name' => '<i class="slicons slicon-book-open"></i> ' . __('Flipbook page layout', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'page_layout',
                'type'    => 'select',
                'options' => array(
                    'adaptive' => __('Adaptive', PDF_LIGHT_VIEWER_PLUGIN),
                    'single' => __('Single', PDF_LIGHT_VIEWER_PLUGIN),
                    'double' => __('Double', PDF_LIGHT_VIEWER_PLUGIN)
                ),
                'default' => 'adaptive',
            ),

            'max_book_width' => array(
                'name' => '<i class="slicons slicon-frame"></i> ' . __('Max book width', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => '(px)',
                'id' => 'max_book_width',
                'type' => 'text'
            ),

            'max_book_height' => array(
                'name' => '<i class="slicons slicon-frame"></i> ' . __('Max book height', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => '(px)',
                'id' => 'max_book_height',
                'type' => 'text'
            ),

            'limit_fullscreen_book_height' => array(
                'name' => '<i class="slicons slicon-frame"></i> ' . __('Limit book height by the viewport in fullscreen mode', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('The book will fit the screen in fullscreen mode ', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'limit_fullscreen_book_height',
                'type' => 'checkbox'
            ),

            'disable_lazy_loading' => array(
                'name' => '<i class="slicons slicon-picture"></i> ' . __('Disable lazy loading', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('May be useful to prevent issues when using other lazy loading systems', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'disable_lazy_loading',
                'type' => 'checkbox'
            ),

            'disable_images_preloading' => array(
                'name' => '<i class="slicons slicon-picture"></i> ' . __('Disable images pre-loading', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('You may want to not preload images for big documents', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'disable_images_preloading',
                'type' => 'checkbox'
            ),

            'download_allowed' => array(
                'name' => '<i class="slicons slicon-cloud-download"></i> ' . __('Allow download', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('Check this if you want to show download button on the frontend', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_allowed',
                'type' => 'checkbox'
            ),

            'alternate_download_link' => array(
                'name' => '<i class="slicons slicon-link"></i> ' . __('Alternate download link', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('If not set, will be used link from PDF File', PDF_LIGHT_VIEWER_PLUGIN),
                'id'   => 'alternate_download_link',
                'type' => 'text',
                'default' => ''
            ),

            'download_page_allowed' => array(
                'name' => '<i class="slicons slicon-cloud-download"></i> ' . __('Allow per-page download', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('Check this if you want to show download button in the thumbnails to allow downloading of single page images', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_page_allowed',
                'type' => 'checkbox'
            ),

            'download_page_format' => array(
                'name' => '' . __('Per-page download format', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('Per page download in JPG or PDF formats', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_page_format',
                'type'    => 'select',
                'options' => array(
                    'jpg' => 'jpg',
                    'pdf' => 'pdf'
                ),
                'default' => 'jpg',
            ),

            'hide_fullscreen_button' => array(
                'name' => '<i class="slicons slicon-size-fullscreen"></i> ' . __('Hide fullscreen button', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'hide_fullscreen_button',
                'type' => 'checkbox'
            ),

            'disable_page_zoom' => array(
                'name' => '<i class="slicons slicon-magnifier"></i> ' . __('Disable page zoom', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'disable_page_zoom',
                'type' => 'checkbox'
            ),

            'zoom_magnify' => array(
                'name' => '<i class="slicons slicon-magnifier"></i> ' . __('Zoom magnify multiplier', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('This value is multiplied against the full size of the zoomed image. The default value is 1, meaning the zoomed image should be at 100% of its natural width and height.', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'zoom_magnify',
                'type' => 'text',
                'default' => 1
            ),

            'show_toolbar_next_previous' => array(
                'name' => '<i class="slicons slicon-arrow-left"></i><i class="slicons slicon-arrow-right"></i> ' . __('Show toolbar next and previous page arrows', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'show_toolbar_next_previous',
                'type' => 'checkbox'
            ),

            'show_toolbar_goto_page' => array(
                'name' => '<i class="slicons slicon-directions"></i> ' . __('Show toolbar go to page control', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'show_toolbar_goto_page',
                'type' => 'checkbox'
            ),

            'show_page_numbers' => array(
                'name' => '<i class="slicons slicon-info"></i> ' . __('Show page numbers', PDF_LIGHT_VIEWER_PLUGIN),
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
