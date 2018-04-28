<?php

namespace tinyframe\core\helpers;

class SOAP_Helper
{
	/*
		SOAP processing
	*/

	/**
     * Loads XML from WSDL.
     *
     * @return xml
     */
	public static function loadWsdl($wsdl, $method, $username = null, $password = null)
	{
		// check php-soap
		if (!function_exists('is_soap_fault')) {
            throw new \RuntimeException('Web-сервер не настроен - не найден модуль php-soap!');
        }
		// set soap client
        try {
            $client = new \SoapClient($wsdl,
	                                   array('login'          => $username,
	                                         'password'       => $password,
	                                         'soap_version'   => SOAP_1_2,
	                                         'cache_wsdl'     => WSDL_CACHE_NONE,
	                                         'exceptions'     => true,
	                                         'trace'          => 1));
        } catch (SoapFault $e) {
            throw new \RuntimeException('Ошибка подключения или внутренняя ошибка сервера!');
        }
        // get xml
        if (is_soap_fault($client)) {
            throw new \RuntimeException('Ошибка подключения или внутренняя ошибка сервера!');
        } else {
            return $client->$method();
		}
	}
}
