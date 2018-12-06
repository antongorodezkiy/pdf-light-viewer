<?php

class PdfLightViewer_Helpers_Url
{
    public static function getDocsUrl()
    {
		if (defined('WPLANG') && file_exists(PDF_LIGHT_VIEWER_APPPATH.'/documentation/index_'.WPLANG.'.html')) {
			$documentation_url = 'documentation/index'.WPLANG.'.html';
		}
		else {
			$documentation_url = 'documentation/index.html';
		}
		$documentation_url = plugins_url($documentation_url, PDF_LIGHT_VIEWER_FILE);
        
		return $documentation_url;
	}

	public static function getSupportUrl()
    {
		return 'http://support.wp.teamlead.pw/';
	}

	public static function getSettingsUrl()
	{
		return admin_url('options-general.php?page='.PDF_LIGHT_VIEWER_PLUGIN);
	}
}
