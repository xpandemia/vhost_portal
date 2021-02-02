<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# 1C CONFIGURATION
# ---------------------------------------------------------------
#
# Set your 1c configuration here
# NOTE: Leave them blank if you not use 1c

switch (APP_DATA) {
	case 'local':
		define('ODATA_1C', 'http://s21.bsu.edu.ru/priem_test/odata/standard.odata'); // 1C OData
		define('WSDL_1C', 'http://s21.bsu.edu.ru/priem_test/ws/WebAbit.1cws?wsdl'); // 1C WSDL
		define('USER_1C', 'nikitin_o'); // 1C username
		define('PASSWORD_1C', 'avitsena159'); // 1C password
		break;
	case 'test':
        define('ODATA_1C', 'https://s37.bsu.edu.ru:8443/University_PK_test/odata/standard.odata'); // 1C OData
        define('WSDL_1C', 'https://info.bsu.edu.ru:8443/University_PK_test/ws/ws1.1cws?wsdl');
        define('USER_1C', 'abiturweb2'); // 1C username
        define('PASSWORD_1C', 'tO3wypyz'); // 1C password
	    
		//define('ODATA_1C', 'http://s21.bsu.edu.ru/priem_test/odata/standard.odata'); // 1C OData
		//define('WSDL_1C', 'http://s21.bsu.edu.ru/priem_test/ws/WebAbit.1cws?wsdl'); // 1C WSDL
		//define('USER_1C', 'nikitin_o'); // 1C username
		//define('PASSWORD_1C', 'avitsena159'); // 1C password
		break;
	case 'main':
		define('ODATA_1C', 'https://info.bsu.edu.ru::8443/University_PK/odata/standard.odata'); // 1C OData
		define('WSDL_1C', 'https://info.bsu.edu.ru:8443/University_PK/ws/AbiturWeb.1cws?wsdl'); // 1C WSDL
        //define('ODATA_1C', 'https://s37.bsu.edu.ru:8443/University_PK/odata/standard.odata'); // 1C OData
        //define('WSDL_1C', 'https://s37.bsu.edu.ru:8443/University_PK/ws/ws1.1cws?wsdl'); // 1C WSDL

        define('USER_1C', 'abiturweb2'); // 1C username
		define('PASSWORD_1C', 'tO3wypyz'); // 1C password
		break;
	default:
		define('ODATA_1C', 's21.bsu.edu.ru/priem_test/odata/standard.odata'); // 1C OData
		define('WSDL_1C', 'http://s21.bsu.edu.ru/priem_test/ws/WebAbit.1cws?wsdl'); // 1C WSDL
		define('USER_1C', 'nikitin_o'); // 1C username
		define('PASSWORD_1C', 'avitsena159'); // 1C password
}
