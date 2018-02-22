<?php
	$form = 'reset_pwd';
	// check password token
	if (!isset($_SESSION[$form]['pwd_token']) && !isset($_SESSION[$form]['email'])) {
		$basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Login', 'Index');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form action="/<?php echo BEHAVIOR; ?>/ResetPwd/ResetPwd" method="post" id="form_reset_pwd" novalidate>
		<legend class="font-weight-bold"><?php echo RESET_PWD_REQUEST_HDR; ?></legend>
		<div class="form-group row">
			<label class="form-control-label text-danger" for="pwd"><i class="fas fa-keyboard fa-2x"></i></label>
			<div class="col">
				<input id="pwd" name="pwd" type="password" class="<?php echo $_SESSION[$form]['pwd_cls']; ?>" aria-describedby="pwdHelpBlock" placeholder="<?php echo PWD_PLC; ?>" value="<?php echo $_SESSION[$form]['pwd'] ?>">
<?php if (!empty($_SESSION[$form]['pwd_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['pwd_err']; ?></div>
<?php } ?>
				<p id="pwdHelpBlock" class="form-text text-muted"><?php echo PWD_HELP; ?></p>
			</div>
		</div>
		<div class="form-group row">
			<label class="form-control-label text-danger" for="pwd_confirm"><i class="fas fa-keyboard fa-2x"></i></label>
			<div class="col">
				<input id="pwd_confirm" name="pwd_confirm" type="password" class="<?php echo $_SESSION[$form]['pwd_confirm_cls']; ?>" aria-describedby="pwd_confirmHelpBlock" placeholder="<?php echo PWD_CONFIRM_PLC; ?>" value="<?php echo $_SESSION[$form]['pwd_confirm'] ?>">
<?php if (!empty($_SESSION[$form]['pwd_confirm_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['pwd_confirm_err']; ?></div>
<?php } ?>
				<p id="pwd_confirmHelpBlock" class="form-text text-muted"><?php echo PWD_CONFIRM_HELP; ?></p>
			</div>
		</div>
		<div class="form-group">
			<div class="col">
				<button type="submit" class="btn btn-success" id="btn_reset_pwd" name="btn_reset_pwd">Сменить</button>
				<a href="/<?php echo BEHAVIOR; ?>/ResetPwd/Reset" class="btn btn-danger">Очистить</a>
			</div>
		</div>
	</form>
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
</div>
