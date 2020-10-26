<?php

class PdfLightViewer_Components_Logger
{
    public static function getLogsPath()
    {
		return WP_CONTENT_DIR.'/'.PDF_LIGHT_VIEWER_PLUGIN.'-logs/';
	}

    public static function createLogsDirectory()
    {
		$log_path = self::getLogsPath();
		if ( ! file_exists($log_path)) {
			mkdir($log_path);
		}

		return file_exists($log_path);
	}

    public static function log($label, $msg) {

		if (is_array($msg) || is_object($msg)) {
			$msg = print_r($msg,true);
		}

		$log_path = self::getLogsPath();

		if ( ! file_exists($log_path)) {
			mkdir($log_path);
		}

		$filename = date('Y-m-00').'.php';
		$filepath = $log_path.$filename;

		$message = '';

		if (!file_exists($filepath)) {
			$message .= "<"."?php if ( ! defined('WPINC')) exit('No direct script access allowed'); ?".">\n\n";
		}

		if (!$fp = fopen($filepath, 'ab')) {
			return FALSE;
		}

		$message .= "======================\n".date('d-m-Y H-i-s')."\n".' ---------------------- '."\n".$label.' >>> '.$msg."\n\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

        try {
        	chmod($filepath, 0666);
		} catch (Exception $e) {
			error_log($e);
		}

		return TRUE;
	}

    public static function getMostRecentFile()
    {
        $logs = directory_map(static::getLogsPath());
        arsort($logs);
        $logs = array_values($logs);

        return (!empty($logs[0]))
            ? static::getLogsPath().$logs[0]
            : null;
    }
}
