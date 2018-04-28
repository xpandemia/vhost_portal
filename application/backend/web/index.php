<?php

# Controller/page that you want to call first time
# By default set to
#	'Login' if logon type 'login'
#	'Main' if logon type 'cas'
#	'Login' if logon type ''

switch ($logon) {
	case 'login':
		$controllerName = 'Login';
		break;
	case 'cas':
		$controllerName = 'Main';
		break;
	default:
		$controllerName = 'Login';
}

# Action/method that you want to call first time
# By default set to 'Index'

$actionName = 'Index';

# ---------------------------------------------------------------
# START ENGINE
# ---------------------------------------------------------------

require_once ROOT_DIR.'/application/bootstrap.php';
