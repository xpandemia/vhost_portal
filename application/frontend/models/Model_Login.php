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
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Логин обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для логина можно использовать только буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!']
                               ],
                'pwd' => [
                            'type' => 'password',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!']
                           ]
            ];
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
			return false;
		} else if (!$user->checkHash($_SESSION[$this->form]['pwd'], $row['pwd_hash'])) {
			// invalid password
			$_SESSION[$this->form]['error_msg'] = 'Неверный пароль!';
			return false;
		} else {
			$user->id = $row['id'];
			$user->username = $row['username'];
			$user->role = $row['role'];
			$user->status = $row['status'];
			switch ($row['status']) {
				case $user::STATUS_NOTACTIVE:
					$_SESSION[$this->form]['error_msg'] = 'Учетная запись не активирована!';
					$user->unsetUser();
					return false;
				case $user::STATUS_ACTIVE:
					$_SESSION[$this->form]['error_msg'] = null;
					$user->setUser();
					return true;
				case $user::DELETED:
					$_SESSION[$this->form]['error_msg'] = 'Учетная запись удалена!';
					$user->unsetUser();
					return false;
				default:
					$_SESSION[$this->form]['error_msg'] = 'Учетная запись в неизвестном состоянии!';
					$user->unsetUser();
					return false;
			}
		}
	}
}
