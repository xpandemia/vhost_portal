<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<h1 class="text-center text-white">Добро пожаловать, <?php echo $_SESSION[APP_CODE]['user_name']; ?>!</h1>
