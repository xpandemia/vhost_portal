<?php

namespace tinyframe\core;

class View
{
	/*
		BASE View

		Views show users appropriate content
	*/

	/**
     * Generates web page.
     *
     * @return mixed
     */
	public function generate($content_view, $layout_view, $title, $data = null)
	{
		include ROOT_DIR.'/application/'.BEHAVIOR.'/views/layouts/'.$layout_view;
	}
}
