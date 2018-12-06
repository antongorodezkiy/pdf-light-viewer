<?php

class PdfLightViewer_Helpers_Http
{
    public static function get($key, $sanitization = 'sanitize_text_field')
    {
		$keys = explode('.',$key);

		if (count($keys) == 1) {
			$value = isset($_GET[$keys[0]]) ? $_GET[$keys[0]] : null;
		}
		elseif (count($keys) == 2) {
			$value = isset($_GET[$keys[0]]) && isset($_GET[$keys[0]][$keys[1]]) ? $_GET[$keys[0]][$keys[1]] : null;
		}

		if (is_array($value)) {

			$values = array();
			foreach($value as $arr_value) {
				$values[] = $sanitization($arr_value);
			}
			return $values;
		}
		else {
			$value = $sanitization($value);
			return $value;
		}
	}

	public static function post($key, $sanitization = 'sanitize_text_field')
    {
		$keys = explode('.',$key);

		if (count($keys) == 1) {
			$value = isset($_POST[$keys[0]]) ? $_POST[$keys[0]] : null;
		}
		elseif (count($keys) == 2) {
			$value = isset($_POST[$keys[0]]) && isset($_POST[$keys[0]][$keys[1]]) ? $_POST[$keys[0]][$keys[1]] : null;
		}

		if (is_array($value)) {

			$values = array();
			foreach($value as $arr_value) {
				$values[] = $sanitization($arr_value);
			}
			return $values;
		}
		else {
			$value = $sanitization($value);
			return $value;
		}
	}

	public static function isGet()
    {
		return ($_SERVER['REQUEST_METHOD'] === 'GET');
	}

	public static function isPost()
    {
		return ($_SERVER['REQUEST_METHOD'] === 'POST');
	}
}
