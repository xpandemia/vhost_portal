<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# FILES CONFIGURATION
# ---------------------------------------------------------------
#
# Set your files configuration here
# NOTE: Leave them blank if you not use files

define('FILES_TEMP', ROOT_DIR.'/files/temp/'); // upload temp dir
define('FILES_SIZE', ['size' => 'MB', 'value' => 2]); // upload file size
define('FILES_EXT_SCANS', [
							'jpg' => 'image/jpeg',
							'png' => 'image/png',
							'gif' => 'image/gif'
							]);
