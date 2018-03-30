<?php

define('APP_NAME', 'Портал БелГУ');
define('APP_CODE', 'portalbsu'); // MUST BE UNIQUE
define('APP_VERSION', '0.1.3');

# Портал БелГУ
# Build with curiosity by Fiben on Tinyframe 0.1.5

// These headers tell the browser to not load anything from cache at all
// and force the browser to make a server request even on a Back click
// Allowing us to verify the token
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

session_start();
ob_start(); // start output buffer

# Please set your application configuration here
# If you do not have any configuration, please leave the
# following variables blank

# ---------------------------------------------------------------
# ENVIRONMENT
# ---------------------------------------------------------------

#  You can load different configurations depending on your
#  current environment. Setting the environment also influences
#  things like logging and error reporting.
#
#  This can be set to:
#
#      development
#      production
#
#
# By default set to 'development'

$environment = 'development';

# ---------------------------------------------------------------
# PATH
# ---------------------------------------------------------------

define('BASEPATH', 'http://'.$_SERVER['SERVER_NAME']);

# ---------------------------------------------------------------
# ROOT
# ---------------------------------------------------------------

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);

# ---------------------------------------------------------------
# GATEKEEPER
# ---------------------------------------------------------------
switch ($_SERVER['REQUEST_URI']) {
	case '/':
		$behavior = 'frontend';
		require_once ROOT_DIR.'/application/frontend/web/index.php';
		break;
	case '/admin':
		$behavior = 'backend';
		require_once ROOT_DIR.'/application/backend/web/index.php';
		break;
	case '/404':
		$content_view = '404.php';
		$layout_view = 'core.php';
		$title = APP_NAME;
		include ROOT_DIR.'/application/core/views/layouts/'.$layout_view;
		break;
	default:
		$routes = explode('/', $_SERVER['REQUEST_URI']);
		switch ($routes[1]) {
			case 'frontend':
				$behavior = 'frontend';
				$_SERVER['REQUEST_URI'] = '/'.$routes[2].'/'.$routes[3];
				require_once ROOT_DIR.'/application/frontend/web/index.php';
				break;
			case 'backend':
				$behavior = 'backend';
				$_SERVER['REQUEST_URI'] = '/'.$routes[2].'/'.$routes[3];
				require_once ROOT_DIR.'/application/backend/web/index.php';
				break;
			default:
				$content_view = '500.php';
				$layout_view = 'core.php';
				$title = APP_NAME;
				include ROOT_DIR.'/application/core/views/layouts/'.$layout_view;
		}
}
