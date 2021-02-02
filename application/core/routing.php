<?php

namespace tinyframe\core;

class Routing
{
	/*
		Router processes HTTP-queries
	*/

	static function execute() 
	{
		$routes = explode('/', $_SERVER['REQUEST_URI']);
		
		// controller name
		if (!empty($routes[1]) && $routes[1] != 'admin' && $routes[1] != 'index' && mb_substr($routes[1], 6, 6) != 'ticket') {
			$controllerName = $routes[1];
		} else {
			$controllerName = CONTROLLER;
		}
		// action name
		if (!empty($routes[2])) {
			$actionName = $routes[2];
		} else {
			$actionName = ACTION;
		}

		// adding prefixes
		$modelName = 'Model_'.$controllerName;
		$controllerName = 'Controller_'.$controllerName;
		$actionName = 'action'.$actionName;

		// attach model file
		$fileWithModel = $modelName.'.php';
		$fileWithModelPath	= ROOT_DIR.'/application/'.BEHAVIOR.'/models/'.$fileWithModel;
		if (file_exists($fileWithModelPath)) {
			include $fileWithModelPath;
		}

		// attach controller file
		$fileWithController = $controllerName.'.php';
		$fileWithControllerPath = ROOT_DIR.'/application/'.BEHAVIOR.'/controllers/'.$fileWithController;
		if (file_exists($fileWithControllerPath)) {
			include $fileWithControllerPath;
		}
		else {
			Routing::ErrorPage404();
		}
		// create controller
		$controllerClass = BEHAVIOR.'\\controllers\\'.$controllerName;
		$controller = new $controllerClass;

		// call controller action
		$action = $actionName;
		
		if (method_exists($controller, $action)) {
			$controller->$action();
		}
		else {
			Routing::ErrorPage404();
		}
	}
	
	static function ErrorPage404()
	{
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
		header("Status: ".http_response_code(404));
		header('Location:'.$host.'404');
    }
}
