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

// tables structure
define('DB_TABLES', array(
						'user',
						'personal',
						'dict_countries'));
