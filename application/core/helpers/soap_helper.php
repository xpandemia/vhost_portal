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
	public static function loadWsdl($wsdl, $method, $username = null, $password = null, $params = NULL)
	{
		// check php-soap
		if (!function_exists('is_soap_fault')) {
            throw new \RuntimeException('Web-сервер не настроен - не найден модуль php-soap!');
        }
		// set soap client
        
        $context = stream_context_create([
                                             'ssl' => [
                                                 'verify_peer' => false,
                                                 'verify_peer_name' => false,
                                                 'allow_self_signed' => true
                                             ]
                                         ]);
        
        try {
            $client = new \SoapClient($wsdl,
	                                   array('login'          => $username,
	                                         'password'       => $password,
	                                         'soap_version'   => SOAP_1_2,
	                                         'cache_wsdl'     => WSDL_CACHE_NONE,
	                                         'exceptions'     => true,
	                                         'stream_context' => $context,
	                                         'trace'          => 1));
        } catch (SoapFault $e) {
            throw new \RuntimeException('Ошибка подключения или внутренняя ошибка сервера!');
        }
        // get xml
        if (is_soap_fault($client)) {
            throw new \RuntimeException('Ошибка подключения или внутренняя ошибка сервера!');
        } else {
            if($method == 'GetPlanNabora') {
                $params = ['Year' => 21];
            }

            if($params == NULL) {
                return $client->$method();
            } else {
                return $client->$method($params);
            }
		}
	}

    /**
     * Loads XML from WSDL.
     *
     * @return array
     */
    public static function getAvailableFunctions($wsdl, $username = null, $password = null)
    {
        // check php-soap
        if (!function_exists('is_soap_fault')) {
            throw new \RuntimeException('Web-сервер не настроен - не найден модуль php-soap!');
        }
        // set soap client

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        try {
            $client = new \SoapClient($wsdl,
                array('login'          => $username,
                    'password'       => $password,
                    'soap_version'   => SOAP_1_2,
                    'cache_wsdl'     => WSDL_CACHE_NONE,
                    'exceptions'     => true,
                    'stream_context' => $context,
                    'trace'          => 1));
        } catch (SoapFault $e) {
            throw new \RuntimeException('Ошибка подключения или внутренняя ошибка сервера!');
        }
        // get xml
        if (is_soap_fault($client)) {
            throw new \RuntimeException('Ошибка подключения или внутренняя ошибка сервера!');
        } else {
            return $client->__getFunctions();
        }
    }
}
