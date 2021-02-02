<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# FORMS CONFIGURATION
# ---------------------------------------------------------------
#
# Set your forms configuration here
# NOTE: Leave them blank if you not use forms

/* Common */
// Login
define('LOGIN', array('id' => 'form_login',
					'hdr' => 'Авторизация',
					'ctr' => 'Login',
					'act' => 'Login'));

/* Frontend */
// Signup
define('fSIGNUP', array('id' => 'form_signup',
						'hdr' => 'Регистрация',
						'ctr' => 'Signup',
						'act' => 'Signup'));
// Reset password request
define('RESET_PWD_REQUEST', array('id' => 'form_reset_pwd_request',
								'hdr' => 'Запрос изменения пароля',
								'ctr' => 'ResetPwdRequest',
								'act' => 'SendEmail'));
// Reset password
define('RESET_PWD', array('id' => 'form_reset_pwd',
						'hdr' => 'Изменение пароля',
						'ctr' => 'ResetPwd',
						'act' => 'ResetPwd'));
// Resume
define('RESUME', array('id' => 'form_resume',
						'hdr' => 'Анкета',
						'ctr' => 'Resume',
						'act' => 'Resume'));
// Education documents
define('DOCS_EDUC', array('id' => 'form_docseduc',
						'hdr' => 'Документ об образовании',
						'ctr' => 'DocsEduc',
						'act' => 'Save'));
// Ege
define('EGE', array('id' => 'form_ege',
					'hdr' => 'Результаты ЕГЭ',
					'ctr' => 'Ege',
					'act' => 'Save'));
// Ege disciplines
define('EGE_DSP', array('id' => 'form_egedsp',
						'hdr' => 'Дисциплина ЕГЭ',
						'ctr' => 'EgeDisciplines',
						'act' => 'Save'));

define('IND_ACHIEVS', array('id' => 'form_indachievs',
                            'hdr' => 'Индивидуальное достижение',
                            'ctr' => 'IndAchievs',
                            'act' => 'Save'));

define('FEATURES', array('id' => 'form_features',
                            'hdr' => 'Права на прием без вступительных испытаний ',
                            'ctr' => 'Features',
                            'act' => 'Save'));

define('PRIV_QUOTA', array('id' => 'form_privillege_quota',
                         'hdr' => 'Право на прием на обучение за счет бюджетных ассигнований в пределах особой квоты ',
                         'ctr' => 'PrivillegeQuota',
                         'act' => 'Save'));

define('PRIV_ADV', array('id' => 'form_privillege_adv',
                           'hdr' => 'Преимущественное право зачисления ',
                           'ctr' => 'PrivillegeAdvanced',
                           'act' => 'Save'));

// Individual achievments
define('TARGET_QUOTA', array('id' => 'form_target_quota',
							'hdr' => 'Целевая квота',
							'ctr' => 'TargetQuota',
							'act' => 'Save'));
// Applications
define('APP', array('id' => 'form_app',
					'hdr' => 'Заявление о приеме на обучение',
					'ctr' => 'Application',
					'act' => 'Save'));

define('APP_CONFIRM', array('id' => 'form_app_confirm',
                    'hdr' => 'Заявление о согласии на зачисление',
                    'ctr' => 'ApplicationConfirm',
                    'act' => 'Save'));

define('AGREEMENT', ['id' => 'form_agreement',
    'hdr'=>'Договор о оплате обучения',
    'ctr' => 'Agreement',
    'act' => 'Save']);

/* Backend */
// Users
define('USER', array('id' => null,
					'hdr' => 'Пользователи',
					'ctr' => 'User',
					'act' => 'Index'));
define('USER_ADD', array(
						'id' => 'form_user_add',
						'hdr' => 'Создание пользователя',
						'ctr' => 'User',
						'act' => 'Create'));
define('USER_EDIT', array('id' => 'form_user_edit',
						'hdr' => 'Изменение пользователя',
						'ctr' => 'User',
						'act' => 'Change'));
// Dictionary manager
define('DICT_MANAGER', array('id' => 'form_dict_manager',
							'hdr' => 'Управление справочниками',
							'ctr' => 'DictionaryManager',
							'act' => 'Save'));
// Dictionary countries
define('DICT_COUNTRIES', array('id' => 'form_dict_countries',
								'hdr' => 'Страна мира',
								'ctr' => 'DictCountries',
								'act' => 'Save'));
// Documents
define('DOCS', array('id' => 'form_docs',
					'hdr' => 'Документ',
					'ctr' => 'Docs',
					'act' => 'Save'));
// Dictionary scans
define('DICT_SCANS', array('id' => 'form_dict_scans',
							'hdr' => 'Скан-копия',
							'ctr' => 'DictScans',
							'act' => 'Save'));
// Dictionary university
define('DICT_UNIVERSITY', array(
								'id' => 'form_dict_university',
								'hdr' => 'Место поступления',
								'ctr' => 'DictUniversity',
								'act' => 'Save'));
// Dictionary ege
define('DICT_EGE', array(
						'id' => 'form_dict_ege',
						'hdr' => 'Дисциплина ЕГЭ',
						'ctr' => 'DictEge',
						'act' => 'Save'));
// Educlevels doctypes
define('EDUCLEVELS_DOCTYPES', array('id' => 'form_educlevels_doctypes',
									'hdr' => 'Связь документов с уровнями подготовки',
									'ctr' => 'EduclevelsDoctypes',
									'act' => 'Save'));
// Eductypes doctypes
define('EDUCTYPES_DOCTYPES', array('id' => 'form_eductypes_doctypes',
									'hdr' => 'Связь документов с видами образования',
									'ctr' => 'EductypesDoctypes',
									'act' => 'Save'));
// Langs
define('LANGS', array('id' => 'form_langs',
						'hdr' => 'Язык',
						'ctr' => 'Langs',
						'act' => 'Save'));
