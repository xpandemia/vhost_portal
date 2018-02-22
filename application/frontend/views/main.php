<?php
	// check login
	if (!isset($_SESSION['user']['id'])) {
		$basic_helper->redirect(LOGIN_HDR, 401, BEHAVIOR.'/Login', 'Index');
	}
	$form = 'main';
?>
<h1 class="text-center text-white">Добро пожаловать, <?php echo $_SESSION['user']['username']; ?>!</h1>
<?php if (!empty($_SESSION[$form]['success_msg'])) { ?>
	<div class="alert alert-success">
		<?php echo $_SESSION[$form]['success_msg']; ?>
    </div>
<?php } ?>
<?php if (!empty($_SESSION[$form]['error_msg'])) { ?>
	<div class="alert alert-danger">
		<?php echo $_SESSION[$form]['error_msg']; ?>
    </div>
<?php } ?>
