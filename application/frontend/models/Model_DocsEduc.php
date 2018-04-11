<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_User as Model_User;

class Model_DocsEduc extends Model
{
	/*
		Education documents processing
	*/

	/**
     * Education documents rules.
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
}
