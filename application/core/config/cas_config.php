<?php

$phpcas_path = '../../source/';

define('CAS_DOMAIN', 'bsu.edu.ru');

///////////////////////////////////////
// Basic Config of the phpCAS client //
///////////////////////////////////////

// Full Hostname of your CAS Server
define('CAS_HOST', 'auth.bsu.edu.ru');

// Context of the CAS Server
define('CAS_CONTEXT', '/cas');

// Port of your CAS server. Normally for a https server it's 443
define('CAS_PORT', 443);

// Path to the ca chain that issued the cas server certificate
$cas_server_ca_cert_path = '/path/to/cachain.pem';

//////////////////////////////////////////
// Advanced Config for special purposes //
//////////////////////////////////////////

// The "real" hosts of clustered cas server that send SAML logout messages
// Assumes the cas server is load balanced across multiple hosts
$cas_real_hosts = array('cas-real-1.example.com', 'cas-real-2.example.com');

// Client config for cookie hardening
$client_domain = '127.0.0.1';
$client_path = 'phpcas';
$client_secure = true;
$client_httpOnly = true;
$client_lifetime = 0;

// Database config for PGT Storage
$db = 'pgsql:host=localhost;dbname=phpcas';
//$db = 'mysql:host=localhost;dbname=phpcas';
$db_user = 'phpcasuser';
$db_password = 'mysupersecretpass';
$db_table = 'phpcastabel';
$driver_options = '';

///////////////////////////////////////////
// End Configuration -- Don't edit below //
///////////////////////////////////////////

// Generating the URLS for the local cas example services for proxy testing
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $curbase = 'https://' . $_SERVER['SERVER_NAME'];
} else {
    $curbase = 'http://' . $_SERVER['SERVER_NAME'];
}
if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
    $curbase .= ':' . $_SERVER['SERVER_PORT'];
}

$curdir = dirname($_SERVER['REQUEST_URI']) . "/";

// CAS client nodes for rebroadcasting pgtIou/pgtId and logoutRequest
$rebroadcast_node_1 = 'http://cas-client-1.example.com';
$rebroadcast_node_2 = 'http://cas-client-2.example.com';

// access to a single service
$serviceUrl = $curbase . $curdir . 'example_service.php';
// access to a second service
$serviceUrl2 = $curbase . $curdir . 'example_service_that_proxies.php';

$pgtBase = preg_quote(preg_replace('/^http:/', 'https:', $curbase . $curdir), '/');
$pgtUrlRegexp = '/^' . $pgtBase . '.*$/';

$cas_url = 'https://' . CAS_HOST;
if (CAS_PORT != '443') {
    $cas_url = $cas_url . ':' . CAS_PORT;
}
$cas_url = $cas_url . CAS_CONTEXT;

// Set the session-name to be unique to the current script so that the client script
// doesn't share its session with a proxied script.
// This is just useful when running the example code, but not normally.
session_name(
    'session_for:'
    . preg_replace('/[^a-z0-9-]/i', '_', basename($_SERVER['SCRIPT_NAME']))
);
// Set an UTF-8 encoding header for internation characters (User attributes)
header('Content-Type: text/html; charset=utf-8');
