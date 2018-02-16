<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_User as Model_User;

class Model_Login extends Model
{
	/*
		Login processing
	*/

	public $form = 'login';

	/**
     * Login rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'username' => [
                                'required' => ['value' => 'true', 'default' => '', 'msg' => 'Логин обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для логина можно использовать только буквы!'],
                                'width' => ['value' => 'true', 'format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!'],
                                'unique' => ['value' => 'false', 'class' => '', 'method' => '', 'msg' => '']
                               ],
                'pwd' => [
                            'required' => ['value' => 'true', 'default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['value' => 'true', 'format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
                            'unique' => ['value' => 'false', 'class' => '', 'method' => '', 'msg' => '']
                           ]
            ];
	}

	/**
     * Login reset.
     *
     * @return nothing
     */
	public function reset($vars)
	{
		$this->resetForm($vars, $this->form, $this->rules());
	}

	/**
     * Gets login page data.
     *
     * @return nothing
     */
	public function getPost($post)
	{
		foreach ($post as $varname => $varvalue) {
			$_SESSION[$this->form][$varname] = htmlspecialchars($varvalue);
		}
	}

	/**
     * Validates login page.
     *
     * @return boolean
     */
	public function validate()
	{
		$this->reset(false);
		$form_helper = new Form_Helper();
		return $form_helper->validate($this->form, $_SESSION[$this->form], $this->rules());
	}

	/**
     * Checks login data.
     *
     * @return boolean
     */
	public function check()
	{
		$user = new Model_User();
		$user->username = $_SESSION[$this->form]['username'];
		$row = $user->getUserByName();
		if (empty($row['id'])) {
			// user not found
			$_SESSION[$this->form]['error_msg'] = 'Пользователь не найден!';
			return FALSE;
		} else if (!$user->checkHash($_SESSION[$this->form]['pwd'], $row['pwd_hash'])) {
			// invalid password
			$_SESSION[$this->form]['error_msg'] = 'Неверный пароль!';
			return FALSE;
		} else {
			switch ($row['status']) {
				case $user::STATUS_NOTACTIVE:
					$_SESSION[$this->form]['error_msg'] = 'Учетная запись не активирована!';
					return FALSE;
				case $user::STATUS_ACTIVE:
					$_SESSION[$this->form]['error_msg'] = null;
					return TRUE;
				case $user::DELETED:
					$_SESSION[$this->form]['error_msg'] = 'Учетная запись удалена!';
					return FALSE;
				default:
					$_SESSION[$this->form]['error_msg'] = 'Учетная запись в неизвестном состоянии!';
					return FALSE;
			}
		}
	}
}
