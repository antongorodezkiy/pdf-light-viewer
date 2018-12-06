<?php

class PdfLightViewer_Helpers_Server
{
    public static function serverInfo()
	{
		global $wp_version, $wpdb;

		$mysql = $wpdb->get_row("SHOW VARIABLES LIKE 'version'");

		return array(
			'os' => php_uname(),
			'php' => phpversion(),
			'mysql' => $mysql->Value,
			'wordpress' => $wp_version
		);
	}
}
