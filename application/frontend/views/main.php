<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_id'])) {
		Basic_Helper::redirect(LOGIN_HDR, 401, BEHAVIOR.'/Login', 'Index');
	}
?>
<h1 class="text-center text-white">Добро пожаловать, <?php echo $_SESSION[APP_CODE]['user_name']; ?>!</h1>
