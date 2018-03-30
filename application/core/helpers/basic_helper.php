<?php

namespace tinyframe\core\helpers;

class Basic_Helper
{
	/*
		Basic processing
	*/

	/**
     * Creates URL with BASEPATH.
     *
     * @return string
     */
	public static function baseUrl($url = '')
	{
		if ($url != '') {
			return BASEPATH.$url;
		} else {
			return BASEPATH;
		}
	}

	/**
     * Creates URL with behavior/controller/action.
     *
     * @return string
     */
	public static function appUrl($controller, $action)
	{
		return '/'.BEHAVIOR.'/'.$controller.'/'.$action;
	}

	/**
     * Redirects to page.
     *
     * @return void
     */
	public static function redirect($header, $response_code, $controller, $action)
	{
		header($_SERVER['SERVER_PROTOCOL'].' '.$header);
		header("Status: ".http_response_code($response_code));
		header('Location: '.self::appUrl($controller, $action));
		exit();
	}
}
