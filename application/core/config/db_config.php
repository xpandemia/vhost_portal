<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# DATABASE CONFIGURATION
# ---------------------------------------------------------------
#
# Set your database configuration here
# NOTE: Leave them blank if you not use database

define('DB_HOST', 'localhost'); // MySQL hostname
define('DB_USER', 'root'); // MySQL database username
define('DB_PASSWORD', ''); // MySQL database password
define('DB_NAME', 'portalbsu'); // MySQL database name

# Tables structure
# NOTE: Add your tables here
define('DB_TABLES', array(
						'address',
						'admission_campaign',
						'application',
						'application_achievs',
						'application_places',
						'application_places_exams',
						'application_status',
						'contacts',
						'dict_countries',
						'dict_discipline',
						'dict_docships',
						'dict_doctypes',
						'dict_educforms',
						'dict_educlevels',
						'dict_eductypes',
						'dict_ege',
						'dict_entrance_exams',
						'dict_finances',
						'dict_foreign_langs',
						'dict_ind_achievs',
						'dict_ind_achievs_ac',
						'dictionary_manager',
						'dictionary_manager_log',
						'dict_scans',
						'dict_speciality',
						'dict_testing_scopes',
						'dict_university',
						'docs',
						'docs_educ',
						'educlevels_doctypes',
						'eductypes_doctypes',
						'ege',
						'ege_disciplines',
						'foreign_langs',
						'ind_achievs',
						'kladr',
						'kladr_abbrs',
						'passport',
						'personal',
						'resume',
						'scans',
						'user'));
