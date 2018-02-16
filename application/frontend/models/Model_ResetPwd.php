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

	public $form = 'reset_pwd';

	/**
     * Reset password rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'pwd' => [
                            'required' => ['value' => 'true', 'default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['value' => 'true', 'format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!']
                           ],
				'pwd_confirm' => [
                            'required' => ['value' => 'true', 'default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['value' => 'true', 'format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!']
                           ]
            ];
	}

	/**
     * Reset password reset.
     *
     * @return nothing
     */
	public function reset($vars)
	{
		$this->resetForm($vars, $this->form, $this->rules());
	}

	/**
     * Gets reset password page data.
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
     * Validates reset password page.
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
     * Resets user password.
     *
     * @return boolean
     */
	public function resetPwd()
	{
		$user = new Model_User();
		$user->email = $_SESSION[$this->form]['email'];
		$row = $user->getUserByEmail();
		if (!empty($row)) {
			if ($_SESSION[$this->form]['pwd_token'] === $row['pwd_token']) {
				if ($_SESSION[$this->form]['pwd'] === $_SESSION[$this->form]['pwd_confirm']) {
					$user->pwd_hash = $user->GetHash($_SESSION[$this->form]['pwd']);
					if ($user->changePwd()) {
						return TRUE;
					} else {
						$_SESSION[$this->form]['error_msg'] = 'Ошибка изменения пароля!';
						return FALSE;
					}
				} else {
					$_SESSION[$this->form]['error_msg'] = 'Пароли не совпадают!';
					return FALSE;
				}
			} else {
				$_SESSION[$this->form]['error_msg'] = 'Признак изменения пароля неверен!';
				return FALSE;
			}
		} else {
			$_SESSION[$this->form]['error_msg'] = 'Адрес эл. почты не найден!';
			return FALSE;
		}
	}
}
