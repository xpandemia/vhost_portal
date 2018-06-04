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
define('fSIGNUP', array(
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
						'hdr' => 'Документ об образовании',
						'ctr' => 'DocsEduc',
						'act' => 'Save'));
// Ege form
define('EGE', array(
					'id' => 'form_ege',
					'hdr' => 'Результаты ЕГЭ',
					'ctr' => 'Ege',
					'act' => 'Save'));
// Ege form
define('EGE_DSP', array(
						'id' => 'form_egedsp',
						'hdr' => 'Дисциплина ЕГЭ',
						'ctr' => 'EgeDisciplines',
						'act' => 'Save'));
// Individual achievments form
define('IND_ACHIEVS', array(
							'id' => 'form_indachievs',
							'hdr' => 'Индивидуальное достижение',
							'ctr' => 'IndAchievs',
							'act' => 'Save'));
// Application form
define('APP', array(
					'id' => 'form_app',
					'hdr' => 'Заявление',
					'ctr' => 'Application',
					'act' => 'Save'));

/* Backend */
// Dictionary manager form
define('DICT_MANAGER', array(
							'id' => 'form_dict_manager',
							'hdr' => 'Управление справочниками',
							'ctr' => 'DictionaryManager',
							'act' => 'Renew'));
// Educlevels doctypes form
define('EDUCLEVELS_DOCTYPES', array(
									'id' => 'form_educlevels_doctypes',
									'hdr' => 'Связь документов с уровнями подготовки',
									'ctr' => 'EduclevelsDoctypes',
									'act' => 'Save'));
// Eductypes doctypes form
define('EDUCTYPES_DOCTYPES', array(
									'id' => 'form_eductypes_doctypes',
									'hdr' => 'Связь документов с видами образования',
									'ctr' => 'EductypesDoctypes',
									'act' => 'Save'));
