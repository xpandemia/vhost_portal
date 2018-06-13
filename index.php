<?php

define('APP_NAME', 'Личный кабинет абитуриента');
define('APP_CODE', 'portalbsu'); // MUST BE UNIQUE
define('APP_VERSION', '0.3.4');
define('APP_DATA', 'local');

# Портал абитуриента
# Build with curiosity by Fiben on Tinyframe 0.2.0

// These headers tell the browser to not load anything from cache at all
// and force the browser to make a server request even on a Back click
// Allowing us to verify the token
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-cache"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

session_cache_limiter('private_no_expire');
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
# LOGON
# ---------------------------------------------------------------

#  You can use different logon types depending on your
#  current environment.
#
#  This can be set to:
#
#      login
#      cas
#
#
# By default set to 'login'

$logon = 'cas';

# ---------------------------------------------------------------
# SIGNUP
# ---------------------------------------------------------------

#  You can use different signup types depending on your
#  current environment.
#
#  This can be set to:
#
#      login
#      email
#
#
# By default set to 'email'

$signup = 'email';

# ---------------------------------------------------------------
# PATH
# ---------------------------------------------------------------

define('BASEPATH', 'http://'.$_SERVER['SERVER_NAME']);

# ---------------------------------------------------------------
# ROOT
# ---------------------------------------------------------------

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);

if ($logon == 'cas') {
	// Load the settings from the central config file
	require_once ROOT_DIR.'/application/core/config/cas_config.php'; // CAS configuration
	// Load the CAS lib
	require_once ROOT_DIR.'/vendors/cas/CAS.php';
	// Enable debugging
	phpCAS::setDebug();
	// Enable verbose error messages. Disable in production!
	phpCAS::setVerbose(true);
	// Initialize phpCAS
	phpCAS::client(CAS_VERSION_2_0, CAS_HOST, CAS_PORT, CAS_CONTEXT, false);
	// For production use set the CA certificate that is the issuer of the cert
	// on the CAS server and uncomment the line below
	// phpCAS::setCasServerCACert($cas_server_ca_cert_path);
	// For quick testing you can disable SSL validation of the CAS server.
	// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
	// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
	phpCAS::setNoCasServerValidation();
	// force CAS authentication
	phpCAS::forceAuthentication();

	// set session user
	$_SESSION[APP_CODE]['user_name'] = phpCAS::getUser();
}

# ---------------------------------------------------------------
# GATEKEEPER
# ---------------------------------------------------------------
switch ($_SERVER['REQUEST_URI']) {
	case '/':
		$behavior = 'frontend';
		require_once ROOT_DIR.'/application/frontend/web/index.php';
		break;
	case '/frontend':
		$behavior = 'frontend';
		require_once ROOT_DIR.'/application/frontend/web/index.php';
		break;
	case '/admin':
		$behavior = 'backend';
		require_once ROOT_DIR.'/application/backend/web/index.php';
		break;
	case '/backend':
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
