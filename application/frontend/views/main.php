<?php
	// check login
	if (!isset($_SESSION['user_logon']) || $_SESSION['user_logon'] == 0) {
		$basic_helper->redirect(LOGIN_HDR, 401, BEHAVIOR.'/Login', 'Index');
	}
?>
<h1>Добро пожаловать, <?php echo $_SESSION['user_id']; ?>!</h1>
