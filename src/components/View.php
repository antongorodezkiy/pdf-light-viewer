<?php

class PdfLightViewer_Components_View
{
    public static function render($view, $data = array())
    {
        $view = str_replace('.', '/', $view);
		extract($data);

		ob_start();
			require PDF_LIGHT_VIEWER_APPPATH.'/resources/views/'.$view.'.php';
		$html = ob_get_clean();

		return $html;
	}
}
