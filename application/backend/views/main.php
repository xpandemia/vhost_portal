<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_id'])) {
		Basic_Helper::redirect(LOGIN['hdr'], 401, 'Login', 'Index');
	}
?>
<h1 class="text-center text-white">Добро пожаловать, <?php echo $_SESSION[APP_CODE]['user_name']; ?>!</h1>
