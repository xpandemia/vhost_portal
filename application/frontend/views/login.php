<?php
	// check login
	if (isset($_SESSION['user_logon']) && $_SESSION['user_logon'] == 1) {
		$basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
	}
	$form = 'login';
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form action="/<?php echo BEHAVIOR; ?>/Login/Login" method="post" id="form_login" novalidate>
		<legend><?php echo LOGIN_HDR; ?></legend>
		<div class="form-group row">
			<label class="form-control-label text-danger" for="username"><i class="fas fa-user fa-2x"></i></label>
			<div class="col">
				<input type="text" class="<?php echo $_SESSION[$form]['username_cls']; ?>" aria-describedby="usernameHelpBlock" id="username" name="username" placeholder="<?php echo USERNAME_PLC; ?>" value="<?php echo $_SESSION[$form]['username'] ?>">
<?php if (!empty($_SESSION[$form]['username_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['username_err']; ?></div>
<?php } ?>
				<p id="usernameHelpBlock" class="form-text text-muted"><?php echo USERNAME_HELP; ?></p>
			</div>
		</div>
		<div class="form-group row">
			<label class="form-control-label text-danger" for="pwd"><i class="fas fa-keyboard fa-2x"></i></label>
			<div class="col">
				<input type="password" class="<?php echo $_SESSION[$form]['pwd_cls']; ?>" aria-describedby="pwdHelpBlock" id="pwd" name="pwd" placeholder="<?php echo PWD_PLC; ?>" value="<?php echo $_SESSION[$form]['pwd'] ?>">
<?php if (!empty($_SESSION[$form]['pwd_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['pwd_err']; ?></div>
<?php } ?>
				<p id="pwdHelpBlock" class="form-text text-muted"><?php echo PWD_HELP; ?></p>
			</div>
		</div>
		<div class="form-group">
			<div class="col">
				<button type="submit" class="btn btn-success" id="btn_login" name="btn_login">Войти</button>
				<a href="/<?php echo BEHAVIOR; ?>/Login/Reset" class="btn btn-danger">Сбросить</a>
			</div>
		</div>
	</form>
	<p><a href="/<?php echo BEHAVIOR; ?>/Signup/Index" class="font-weight-bold text-primary">Ещё нет учётной записи? Зарегистрируйтесь.</a></p>
	<p><a href="/<?php echo BEHAVIOR; ?>/ResetPwdRequest/Index" class="font-weight-bold text-primary">Забыли пароль? Восстановите.</a></p>
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
