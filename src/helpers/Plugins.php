<?php

class PdfLightViewer_Helpers_Plugins
{
    public static function getActivePlugins()
	{
		$activePlugins = get_option('active_plugins');
		$plugins = get_plugins();
		$activated_plugins = array();
		foreach($activePlugins as $p) {
			if(isset($plugins[$p])) {
				array_push($activated_plugins, $plugins[$p]);
			}
		}

		return $activated_plugins;
	}

    public static function getData($pluginFile, $key = '')
    {
		$plugin = get_plugin_data($pluginFile, false, true);

		if ($key && isset($plugin[$key])) {
			return $plugin[$key];
		}
		else {
			return $plugin;
		}
	}

    public static function getPluginData($key = '')
	{
        return self::getData(PDF_LIGHT_VIEWER_FILE, $key);
	}
}
