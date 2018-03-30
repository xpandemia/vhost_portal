<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_id'])) {
		Basic_Helper::redirect(LOGIN['hdr'], 401, LOGIN['ctr'], 'Index');
	}
?>
<h1 class="text-center text-white">Добро пожаловать, <?php echo $_SESSION[APP_CODE]['user_name']; ?>!</h1>
<?php
	echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
	echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
?>
