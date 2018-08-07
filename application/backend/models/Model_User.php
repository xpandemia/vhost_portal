<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_User as User;

class Model_User extends Model
{
	/*
		Users processing
	*/

	/**
     * Users add rules.
     *
     * @return array
     */
	public function rules_add()
	{
		return [
                'username' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Логин обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для логина можно использовать '.MSG_ALPHA.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!'],
                                'unique' => ['class' => 'common\\models\\Model_User', 'method' => 'ExistsUsername', 'msg' => 'Такой логин уже есть!'],
                                'success' => 'Логин заполнен верно.'
                               ],
                'email' => [
                            'type' => 'email',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть '.MSG_EMAIL_LIGHT],
                            'width' => ['format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!'],
                            'unique' => ['class' => 'common\\models\\Model_User', 'method' => 'ExistsEmail', 'msg' => 'Такой адрес эл. почты уже есть!'],
                            'success' => 'Адрес эл. почты заполнен верно.'
                           ],
                'pwd' => [
							'type' => 'password',
                            'class' => 'form-control',
	                        'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
	                        'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Для пароля можно использовать '.MSG_ALPHA_NUMB.'!'],
	                        'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
	                        'success' => 'Пароль заполнен верно.'
	                       ],
	            'pwd_confirm' => [
	                            'type' => 'password',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
	                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Для пароля можно использовать '.MSG_ALPHA_NUMB.'!'],
	                            'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
	                            'success' => 'Пароль заполнен верно.'
	                           ],
	            'role' => [
							'type' => 'selectlist',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Роль обязательна для заполнения!'],
							'success' => 'Роль заполнена верно.'
                           ]
            ];
	}

	/**
     * Users edit rules.
     *
     * @return array
     */
	public function rules_edit()
	{
		return [
                'username' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Логин обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для логина можно использовать '.MSG_ALPHA.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!'],
                                'success' => 'Логин заполнен верно.'
                               ],
                'email' => [
                            'type' => 'email',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть '.MSG_EMAIL_LIGHT],
                            'width' => ['format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!'],
                            'success' => 'Адрес эл. почты заполнен верно.'
                           ],
                'role' => [
							'type' => 'selectlist',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Роль обязательна для заполнения!'],
							'success' => 'Роль заполнена верно.'
                           ]
            ];
	}

	/**
     * Gets user from database.
     *
     * @return array
     */
	public function get($id)
	{
		$user = new User();
		$user->id = $id;
		return $user->get();
	}

	/**
     * Deletes user from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$user = new User();
		$user->id = $form['id'];
		$user->status = $user::STATUS_DELETED;
		if ($user->changeStatus()) {
			$form['success_msg'] = 'Пользователь № '.$user->id.' удалён.';
		} else {
			$form['error_msg'] = 'Ошибка удаления пользователя № '.$user->id.'! Свяжитесь с администратором.';
		}
		return $form;
	}

	/**
     * Starts pagination.
     *
     * @return mixed
     */
	public function paginationStart()
	{
		$user = new User();
		$user_row = $user->getMin();
		if ($user_row) {
			return ['id' => $user_row['id'], 'page' => 1, 'step' => 'next'];
		} else {
			return null;
		}
	}

	/**
     * Gets user page number.
     *
     * @return array
     */
	public function getPageNumber($id)
	{
		$user = new User();
		$user->id = $id;
		return $user->getPageNumber();
	}

	/**
     * Creates user data.
     *
     * @return array
     */
	public function create($form)
	{
		if ($form['pwd'] == $form['pwd_confirm']) {
			$user = new User();
			$user->username = $form['username'];
			$user->email = $form['email'];
			$user->pwd_hash = $user->GetHash($form['pwd']);
			$user->role = $form['role'];
			$user->status = $user::STATUS_ACTIVE;
			$form['id'] = $user->save();
			if ($form['id'] > 0) {
				$form['success_msg'] = 'Создан пользователь № '.$form['id'].'.';
			} else {
				$form['error_msg'] = 'Ошибка при создании пользователя!';
			}
		} else {
			$form['error_msg'] = 'Пароли не совпадают!';
		}
		return $form;
	}

	/**
     * Changes user data.
     *
     * @return array
     */
	public function change($form)
	{
		$user = new User();
		$user->id = $form['id'];
		$user->username = $form['username'];
		$user->email = $form['email'];
		$user->role = $form['role'];
		$user->status = $form['status'];
		if ($user->existsUsernameExcept() && $user->existsEmailExcept()) {
			$form['error_msg'] = 'Такой пользователь уже есть!';
			return $form;
		} else {
			if ($user->changeAll()) {
				$form['success_msg'] = 'Изменён пользователь № '.$form['id'].'.';
			} else {
				$form['error_msg'] = 'Ошибка при изменении пользователя № '.$form['id'].'!';
			}
		}
		return $form;
	}

	/**
     * Searches for user in database.
     *
     * @return array
     */
	public function search($form, $post) : array
	{
		if ((isset($post['search_username']) && !empty($post['search_username'])) || (isset($post['search_email']) && !empty($post['search_email']))) {
			$conds = '';
			$params = [];
			// username
			if (isset($post['search_username']) && !empty($post['search_username'])) {
				$conds .= 'username like (:username)';
				$params[':username'] = '%'.htmlspecialchars($post['search_username']).'%';
			}
			// email
			if (isset($post['search_email']) && !empty($post['search_email'])) {
				if ($conds == '') {
					$conds .= 'email like (:email)';
				} else {
					$conds .= ' OR email like (:email)';
				}
				$params[':email'] = '%'.htmlspecialchars($post['search_email']).'%';
			}
			$user = new User();
			$user_arr = $user->search($conds, $params);
			if ($user_arr) {
				return $user_arr;
			} else {
				$form['error_msg'] = 'Ничего не найдено!';
				return $form;
			}
		} else {
			$form['error_msg'] = 'Не указаны критерии поиска!';
			return $form;
		}
	}

	/**
     * Logins as user.
     *
     * @return boolean
     */
	public function mask() : bool
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			echo '<p><strong>Ошибка!</strong> Отсутствует идент-р пользователя!</p>';
			return false;
		}
		$user = new User();
		$user->id = $id;
		$user_row = $user->get();
		if ($user_row) {
			$user->username = $user_row['username'];
			$user->email = $user_row['email'];
			$user->role = $user_row['role'];
			$user->status = $user_row['status'];
			$user->setUser();
			return true;
		} else {
			echo '<p><strong>Ошибка!</strong> Пользователь с ID '.$id.' не найден!</p>';
			return false;
		}
	}
}
