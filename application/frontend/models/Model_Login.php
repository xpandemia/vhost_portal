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
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!'],
                                'success' => 'Логин заполнен верно.'
                               ],
                'pwd' => [
                            'type' => 'password',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
                            'success' => 'Пароль заполнен верно.'
                           ]
            ];
	}

	/**
     * Checks login data.
     *
     * @return array
     */
	public function check($form)
	{
		$user = new Model_User();
		$user->username = $form['username'];
		$row = $user->getUserByName();
		if (empty($row['id'])) {
			// user not found
			$form['error_msg'] = 'Пользователь не найден!';
		} else if (!$user->checkHash($form['pwd'], $row['pwd_hash'])) {
			// invalid password
			$form['error_msg'] = 'Неверный пароль!';
		} else {
			$user->id = $row['id'];
			$user->username = $row['username'];
			$user->role = $row['role'];
			$user->status = $row['status'];
			switch ($row['status']) {
				case $user::STATUS_NOTACTIVE:
					$form['error_msg'] = 'Учетная запись не активирована!';
					$user->unsetUser();
					break;
				case $user::STATUS_ACTIVE:
					$form['error_msg'] = null;
					$user->setUser();
					break;
				case $user::DELETED:
					$form['error_msg'] = 'Учетная запись удалена!';
					$user->unsetUser();
					break;
				default:
					$form['error_msg'] = 'Учетная запись в неизвестном состоянии!';
					$user->unsetUser();
			}
		}
		return $form;
	}
}
