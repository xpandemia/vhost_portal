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

	/**
     * Gets user browser.
     *
     * @return array
     */
	public static function getBrowser()
	{
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= '';

	    // First get the platform
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }

	    // Next get the name of the useragent yes separately and for good reason
	    if (preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
			$bname = 'Internet Explorer';
			$ub = 'MSIE';
	    } elseif (preg_match('/Firefox/i',$u_agent)) {
			$bname = 'Mozilla Firefox';
			$ub = 'Firefox';
		} elseif (preg_match('/Chrome/i',$u_agent)) {
	        $bname = 'Google Chrome';
	        $ub = 'Chrome';
	    } elseif (preg_match('/Safari/i',$u_agent)) {
	        $bname = 'Apple Safari';
	        $ub = 'Safari';
	    } elseif (preg_match('/Opera/i',$u_agent)) {
	        $bname = 'Opera';
	        $ub = 'Opera';
	    } elseif (preg_match('/Netscape/i',$u_agent)) {
	        $bname = 'Netscape';
	        $ub = 'Netscape';
	    }

	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>'.join('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }

	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        // we will have two since we are not using 'other' argument yet
	        // see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)) {
	            $version= $matches['version'][0];
	        } else {
	            $version= $matches['version'][1];
	        }
	    } else {
			$version= $matches['version'][0];
	    }

	    // check if we have a number
	    if ($version == null || $version == '') {
			$version = '?';
	    }

	    return [
		        'userAgent' => $u_agent,
		        'name' => $bname,
		        'version' => $version,
		        'platform' => $platform,
		        'pattern' => $pattern
			];
	}

	/**
     * Checks user browser.
     *
     * @return string
     */
	public static function checkBrowser($name, $version)
	{
		switch ($name) {
			case 'Mozilla Firefox':
				if ($version < 60) {
					return 'update';
				} else {
					return null;
				}
			case 'Google Chrome':
				if ($version < 67) {
					return 'update';
				} else {
					return null;
				}
			case 'Opera':
				if ($version < 53) {
					return 'update';
				} else {
					return null;
				}
			default:
				return 'install';
		}
	}
}
