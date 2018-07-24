<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# FILES CONFIGURATION
# ---------------------------------------------------------------
#
# Set your files configuration here
# NOTE: Leave them blank if you not use files

// upload files temp dir
define('FILES_TEMP', ROOT_DIR.'/files/temp/');
// upload files size
define('FILES_SIZE', ['size' => 'MB', 'value' => 2]);
// upload files extension
define('FILES_EXT_SCANS', [
							'jpg' => 'image/jpeg',
							'png' => 'image/png',
							'gif' => 'image/gif'
							]);
// upload files name
define('FILES_NAME', 45);
