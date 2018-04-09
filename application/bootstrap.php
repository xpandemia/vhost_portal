<?php

use tinyframe\core\Routing as Routing;

	// exceptions
	require_once ROOT_DIR.'/application/core/exceptions/upload_exceptions.php'; // UPLOADS exceptions
	// helpers
	require_once ROOT_DIR.'/application/core/helpers/basic_helper.php'; // BASE processing
	require_once ROOT_DIR.'/application/core/helpers/calc_helper.php'; // CALCULATIONS processing
	require_once ROOT_DIR.'/application/core/helpers/captcha_helper.php'; // CAPTCHA processing
	require_once ROOT_DIR.'/application/core/helpers/db_helper.php'; // DB processing
	require_once ROOT_DIR.'/application/core/helpers/files_helper.php'; // FILES processing
	require_once ROOT_DIR.'/application/core/helpers/form_helper.php'; // FORMS processing
	require_once ROOT_DIR.'/application/core/helpers/html_helper.php'; // HTML processing
	require_once ROOT_DIR.'/application/core/helpers/mail_helper.php'; // EMAIL processing
	require_once ROOT_DIR.'/application/core/helpers/xml_helper.php'; // XML processing
	// configs
	require_once ROOT_DIR.'/application/core/config/1c_config.php'; // 1C configuration
	require_once ROOT_DIR.'/application/core/config/db_config.php'; // DB configuration
	require_once ROOT_DIR.'/application/core/config/files_config.php'; // FILES configuration
	require_once ROOT_DIR.'/application/core/config/form_config.php'; // FORMS configuration
	require_once ROOT_DIR.'/application/core/config/mail_config.php'; // EMAIL configuration
	// base classes
	require_once ROOT_DIR.'/application/core/model.php';
	require_once ROOT_DIR.'/application/core/view.php';
	require_once ROOT_DIR.'/application/core/controller.php';
	// data classes
	if (defined('DB_TABLES')) {
		if (is_array(DB_TABLES)) {
			foreach (DB_TABLES as $table_name) {
		        $model = explode('_', $table_name);
		        $model_name = '';
		        foreach ($model as $model_value) {
		            $model_name .= ucfirst($model_value);
		        }
		        require_once ROOT_DIR.'/application/common/models/Model_'.$model_name.'.php';
		    }
		}
	}
	// vendors
	require_once ROOT_DIR.'/vendors/PHPMailer/src/Exception.php';
	require_once ROOT_DIR.'/vendors/PHPMailer/src/PHPMailer.php';
	require_once ROOT_DIR.'/vendors/PHPMailer/src/SMTP.php';


# ---------------------------------------------------------------
# BEHAVIOR
# ---------------------------------------------------------------
if ($behavior === '') {
	$behavior = 'frontend';
}
define('BEHAVIOR', $behavior);

# ---------------------------------------------------------------
# ENVIRONMENT
# ---------------------------------------------------------------
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

# Set SESSION vars
if (!isset($_SESSION[APP_CODE]['captcha'])) {
	$_SESSION[APP_CODE]['captcha'] = null;
}

# Clear TEMP
if (file_exists(ROOT_DIR.'/images/temp/captcha/captcha_'.session_id().'.png')) {
	unlink(ROOT_DIR.'/images/temp/captcha/captcha_'.session_id().'.png');
}

# Start routing
require_once ROOT_DIR.'/application/core/routing.php';
Routing::execute(); // запускаем маршрутизатор
