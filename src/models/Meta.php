<?php

class PdfLightViewer_Models_Meta
{
    private static $cached_meta = array();
	static public function get_post_meta($post_id, $key = '', $single = false)
	{
		if (!isset(self::$cached_meta[$post_id])) {
			self::$cached_meta[$post_id] = array();
			$meta_data = get_post_meta($post_id);
			if (!empty($meta_data)) {
				foreach($meta_data as $meta_key => $meta) {
					if (is_serialized($meta[0])) {
						$meta[0] = unserialize($meta[0]);
					}
					self::$cached_meta[$post_id][$meta_key] = $meta[0];
				}
			}
		}

		if (!$key && isset(self::$cached_meta[$post_id])) {
			return self::$cached_meta[$post_id];
		}
		else if (isset(self::$cached_meta[$post_id][$key])) {
			return self::$cached_meta[$post_id][$key];
		}
		else {
			return null;
		}
	}

	private static $cached_usermeta = array();
	static public function get_user_meta($user_id, $key = '', $single = false)
	{
		if (!isset(self::$cached_usermeta[$user_id])) {
			self::$cached_usermeta[$user_id] = array();
			$meta_data = get_user_meta($user_id);
			if (!empty($meta_data) && is_array($meta_data)) {
				foreach($meta_data as $meta_key => $meta) {
					if (is_serialized($meta[0])) {
						$meta[0] = unserialize($meta[0]);
					}
					self::$cached_usermeta[$user_id][$meta_key] = $meta[0];
				}
			}
			else {
				return null;
			}
		}

		if (!$key && isset(self::$cached_usermeta[$user_id])) {
			return self::$cached_usermeta[$user_id];
		}
		else if (isset(self::$cached_usermeta[$user_id][$key])) {
			return self::$cached_usermeta[$user_id][$key];
		}
		else {
			return null;
		}
	}
}
