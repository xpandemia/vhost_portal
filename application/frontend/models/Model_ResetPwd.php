<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_User as Model_User;

class Model_ResetPwd extends Model
{
	/*
		Reset password processing
	*/

	/**
     * Reset password rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'pwd' => [
                            'type' => 'password',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
                            'success' => 'Пароль заполнен верно.'
                           ],
				'pwd_confirm' => [
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
     * Resets user password.
     *
     * @return array
     */
	public function resetPwd($form)
	{
		$user = new Model_User();
		$user->email = $_SESSION[APP_CODE]['email'];
		$row = $user->getByEmail();
		if (!empty($row)) {
			if ($_SESSION[APP_CODE]['pwd_token'] === $row['pwd_token']) {
				if ($form['pwd'] === $form['pwd_confirm']) {
					$user->id = $row['id'];
					$user->pwd_hash = $user->GetHash($form['pwd']);
					if ($user->changePwd()) {
						$form['error_msg'] = null;
					} else {
						$form['error_msg'] = 'Ошибка изменения пароля!';
					}
				} else {
					$form['error_msg'] = 'Пароли не совпадают!';
				}
			} else {
				$form['error_msg'] = 'Признак изменения пароля неверен!';
			}
		} else {
			$form['error_msg'] = 'Адрес эл. почты не найден!';
		}
		return $form;
	}
}
