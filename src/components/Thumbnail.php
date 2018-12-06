<?php

class PdfLightViewer_Components_Thumbnail
{
    public static function set_featured_image($post_id, $file, $media_name)
    {
        $image_data = file_get_contents($file);
        $attach_id = self::create_media_from_data($media_name, $image_data);
        return set_post_thumbnail($post_id, $attach_id);
    }

    public static function create_media_from_data($filename, $image_data)
    {
        $upload_dir = wp_upload_dir();

        $file = $upload_dir['path'].'/'.$filename;

        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $file, 0);

        $url = wp_get_attachment_image_src($attach_id, 'full');
        apply_filters('wp_handle_upload', array(
            'file' => $file,
            'url' => $url,
            'type' => $wp_filetype['type']
        ), 'upload');

        require_once(ABSPATH.'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }
}
