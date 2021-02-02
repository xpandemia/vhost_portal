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

function check_ip()
{
    /*
    $ip_arr=explode('/','172.16.0.0/12');
    $network_long=ip2long($ip_arr[0]);
    $x=ip2long($ip_arr[1]);
    $mask=long2ip($x)==$ip_arr[1]?$x:0xffffffff<<(32-$ip_arr[1]);
    $ip_long=ip2long($_SERVER['REMOTE_ADDR']);
    return($ip_long&$mask)==($network_long&$mask);
    */
    
    /*
    $ip_arr=explode('/','172.26.51.0/24');
    $network_long=ip2long($ip_arr[0]);
    $x=ip2long($ip_arr[1]);
    $mask=long2ip($x)==$ip_arr[1]?$x:0xffffffff<<(32-$ip_arr[1]);
    $ip_long=ip2long($_SERVER['REMOTE_ADDR']);
    return($ip_long&$mask)==($network_long&$mask);
    */
    
    return True;
}

if(check_ip()) {
    require_once ROOT_DIR.'/application/bootstrap.php';
} else {
    header('HTTP/1.0 403 Forbidden');
    
    echo 'Подача документов будет доступна после начала приемной компании университета!';
}
