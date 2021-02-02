<?php

namespace tinyframe\core;

class Controller
{

	/*
		BASE Controller

		Controllers process users actions
	*/
	public $model;
	public $view;
	
	function __construct()
	{
		$this->model = new Model();
		$this->view = new View();
	}

	/**
     * Displays document delete confirmation page.
     *
     * @return mixed
     */
	function actionDelDocConfirm($form, $get)
	{
		if (isset($get['id']) && !empty($get['id'])) {
			$form['id'] = htmlspecialchars($get['id']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р документа!</p>");
		}
		if (isset($get['pid']) && !empty($get['pid'])) {
			$form['pid'] = htmlspecialchars($get['pid']);
		} else {
			$form['pid'] = null;
		}
		if (isset($get['hdr']) && !empty($get['hdr'])) {
			$form['hdr'] = htmlspecialchars($get['hdr']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует заголовок документа!</p>");
		}
		if (isset($get['ctr']) && !empty($get['ctr'])) {
			$form['ctr'] = htmlspecialchars($get['ctr']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует контроллер документа!</p>");
		}
		$form['error_msg'] = null;
		return $this->view->generate('delete-confirm.php', 'form.php', 'Удаление документа '.$form['ctr'], $form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
