<?php

use tinyframe\core\Routing as Routing;

	// configs
	require_once ROOT_DIR.'/application/core/config/db_config.php'; // DB configuration
	require_once ROOT_DIR.'/application/core/config/form_config.php'; // FORMS configuration
	require_once ROOT_DIR.'/application/core/config/mail_config.php'; // EMAIL configuration
	// helpers
	require_once ROOT_DIR.'/application/core/helpers/basic_helper.php'; // base processing
	require_once ROOT_DIR.'/application/core/helpers/captcha_helper.php'; // CAPTCHA processing
	require_once ROOT_DIR.'/application/core/helpers/form_helper.php'; // forms processing
	require_once ROOT_DIR.'/application/core/helpers/db_helper.php'; // DB processing
	require_once ROOT_DIR.'/application/core/helpers/mail_helper.php'; // email processing
	// base classes
	require_once ROOT_DIR.'/application/core/model.php';
	require_once ROOT_DIR.'/application/core/view.php';
	require_once ROOT_DIR.'/application/core/controller.php';
	// data classes
	include_once ROOT_DIR.'/application/common/models/Model_User.php'; // users
	// vendors
	require_once ROOT_DIR.'/vendors/PHPMailer/src/Exception.php';
	require_once ROOT_DIR.'/vendors/PHPMailer/src/PHPMailer.php';
	require_once ROOT_DIR.'/vendors/PHPMailer/src/SMTP.php';

if ($behavior === '') {
	$behavior = 'frontend';
}
define('BEHAVIOR', $behavior);

if ($environment === '') {
	$environment = 'development';
}
define('ENVIRONMENT', $environment);

# ---------------------------------------------------------------
# ERROR REPORTING
# ---------------------------------------------------------------
#
# Different environments will require different levels of error reporting.
# By default development will show errors but testing and live will hide them.
if (defined('ENVIRONMENT')) {

	switch (ENVIRONMENT) {
		case 'development':
			error_reporting(E_ALL);
			break;
		case 'production':
			error_reporting(0);
			break;
		default:
			exit('The application environment is not set correctly.');
	}
}

# Defined first page that opened first time (welcome page)
if ($controllerName == '' ) {
	$controllerName = 'Main';
}
define('CONTROLLER', $controllerName);

# Defined first method that opened when opened page. eg: www.site.com/welcome/index
if ($actionName == '') {
	$actionName = 'Index';
}
define('ACTION', $actionName);

# Start routing

require_once ROOT_DIR.'/application/core/routing.php';
Routing::execute(); // запускаем маршрутизатор
