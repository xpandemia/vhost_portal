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
	public static function redirect($header, $response_code, $controller, $action, $success = null, $error = null)
	{
		if (empty($success) && empty($error)) {
			self::msgReset();
		} else {
			if (!empty($success)) {
				self::msgSuccess($success);
			} else {
				self::msgError($error);
			}
		}
		header($_SERVER['SERVER_PROTOCOL'].' '.$header);
		header("Status: ".http_response_code($response_code));
		header('Location: '.self::appUrl($controller, $action));
		exit();
	}

	/**
     * Redirects to page.
     *
     * @return void
     */
	public static function redirectHome()
	{
		self::msgReset();
		header($_SERVER['SERVER_PROTOCOL'].' '.APP_CODE);
		header("Status: ".http_response_code(401));
		header('Location: /');
		exit();
	}

	/**
     * Resets global messages.
     *
     * @return void
     */
	public static function msgReset()
	{
		$_SESSION[APP_CODE]['success_msg'] = null;
		$_SESSION[APP_CODE]['error_msg'] = null;
	}

	/**
     * Sets global success message.
     *
     * @return void
     */
	public static function msgSuccess($msg)
	{
		$_SESSION[APP_CODE]['success_msg'] = $msg;
		$_SESSION[APP_CODE]['error_msg'] = null;
	}

	/**
     * Sets global error message.
     *
     * @return void
     */
	public static function msgError($msg)
	{
		$_SESSION[APP_CODE]['success_msg'] = null;
		$_SESSION[APP_CODE]['error_msg'] = $msg;
	}
}
