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
						'dict_citizenship',
						'dict_countries',
						'dictionary_manager',
						'dictionary_manager_log',
						'kladr_abbrs',
						'personal',
						'resume',
						'user'));
