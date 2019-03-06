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

            'download_allowed' => array(
                'name' => '<i class="slicons slicon-cloud-download"></i> ' . __('Allow download', PDF_LIGHT_VIEWER_PLUGIN),
                'desc' => __('Check this if you want to show download button on the frontend', PDF_LIGHT_VIEWER_PLUGIN),
                'id' => 'download_allowed',
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
