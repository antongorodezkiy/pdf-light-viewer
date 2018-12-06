<?php

class PdfLightViewer_Components_Assets
{
    public static function enqueueScripts($scripts)
    {
        $prefix = 'resources/';
        foreach($scripts as $id => $file) {
            if (stristr($file, 'http:') || stristr($file, 'https:')) {
                wp_enqueue_script( $id, $file, array('jquery'), PdfLightViewer_Helpers_Plugins::getPluginData('Version') );
            }
            else {
                $ver = null;
                if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file)) {
                    $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file);
                }

                wp_enqueue_script( $id, plugins_url($prefix.$file, PDF_LIGHT_VIEWER_FILE), array('jquery'), $ver );
            }
        }
    }

    public static function enqueueStyles($styles)
    {
        $prefix = 'resources/';
        foreach($styles as $id => $file) {
            if (stristr($file, 'http:') || stristr($file, 'https:')) {
                wp_enqueue_style( $id, $file, null, PdfLightViewer_Helpers_Plugins::getPluginData('Version') );
            }
            else {
                $ver = null;
                if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file)) {
                    $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file);
                }

                wp_enqueue_style( $id, plugins_url($prefix.$file, PDF_LIGHT_VIEWER_FILE), null, $ver );
            }
        }
    }

    public static function registerScripts($scripts)
    {
        $prefix = 'resources/';
        foreach($scripts as $id => $file) {
            $ver = null;
            if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file)) {
                $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file);
            }

            wp_register_script( $id, plugins_url($prefix.$file, PDF_LIGHT_VIEWER_FILE), array('jquery'), $ver );
        }
    }

    public static function registerStyles($styles)
    {
        $prefix = 'resources/';
        foreach($styles as $id => $file) {
            $ver = null;
            if (file_exists(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file)) {
                $ver = filemtime(PDF_LIGHT_VIEWER_APPPATH.'/'.$prefix.$file);
            }

            wp_register_style( $id, plugins_url($prefix.$file, PDF_LIGHT_VIEWER_FILE), null, $ver );
        }
    }
}
