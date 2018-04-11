<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# FORMS CONFIGURATION
# ---------------------------------------------------------------
#
# Set your forms configuration here
# NOTE: Leave them blank if you not use forms

/* Common */
// Login form
define('LOGIN', array(
						'id' => 'form_login',
						'hdr' => 'Авторизация',
						'ctr' => 'Login',
						'act' => 'Login'));

/* Frontend */
// Signup form
define('SIGNUP', array(
						'id' => 'form_signup',
						'hdr' => 'Регистрация',
						'ctr' => 'Signup',
						'act' => 'Signup'));
// Reset password request form
define('RESET_PWD_REQUEST', array(
								'id' => 'form_reset_pwd_request',
								'hdr' => 'Запрос изменения пароля',
								'ctr' => 'ResetPwdRequest',
								'act' => 'SendEmail'));
// Reset password form
define('RESET_PWD', array(
						'id' => 'form_reset_pwd',
						'hdr' => 'Изменение пароля',
						'ctr' => 'ResetPwd',
						'act' => 'ResetPwd'));
// Resume form
define('RESUME', array(
						'id' => 'form_resume',
						'hdr' => 'Анкета',
						'ctr' => 'Resume',
						'act' => 'Resume'));
// Education docs form
define('DOCS_EDUC', array(
						'id' => 'form_docseduc',
						'hdr' => 'Документы об образовании',
						'ctr' => 'DocsEduc',
						'act' => 'DocsEduc'));

/* Backend */
// Dictionary manager form
define('DICT_MANAGER', array(
							'id' => 'form_dict_manager',
							'hdr' => 'Управление справочниками',
							'ctr' => 'DictionaryManager',
							'act' => 'Renew'));
