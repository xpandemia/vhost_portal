<?php
	
use tinyframe\core\helpers\Captcha_Helper as Captcha_Helper;

	$captcha = new Captcha_Helper();
	$captcha->create();
	
	// check login
	if (isset($_SESSION['user_logon']) && $_SESSION['user_logon'] == 1) {
		$basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
	}
	
	$form = 'signup';
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form action="/<?php echo BEHAVIOR; ?>/Signup/Signup" method="post" id="form_signup" novalidate>
		<legend><?php echo SIGNUP_HDR; ?></legend>
		<div class="form-group row">
			<label class="form-control-label text-danger" for="username"><i class="fas fa-user fa-2x"></i></label>
			<div class="col">
				<input id="username" name="username" type="text" class="<?php echo $_SESSION[$form]['username_cls']; ?>" aria-describedby="usernameHelpBlock" placeholder="<?php echo USERNAME_PLC; ?>" value="<?php echo $_SESSION[$form]['username'] ?>">
<?php if (!empty($_SESSION[$form]['username_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['username_err']; ?></div>
<?php } ?>
				<p id="usernameHelpBlock" class="form-text text-muted"><?php echo USERNAME_HELP; ?></p>
			</div>
		</div>
		<div class="form-group row">
			<label class="form-control-label text-danger" for="email"><i class="fas fa-envelope fa-2x"></i></label>
			<div class="col">
				<input id="email" name="email" type="email" class="<?php echo $_SESSION[$form]['email_cls']; ?>" aria-describedby="emailHelpBlock" placeholder="<?php echo EMAIL_PLC; ?>" value="<?php echo $_SESSION[$form]['email'] ?>">
<?php if (!empty($_SESSION[$form]['email_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['email_err']; ?></div>
<?php } ?>
				<p id="emailHelpBlock" class="form-text text-muted"><?php echo EMAIL_HELP; ?></p>
			</div>
		</div>
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
		<hr>
		<img id="img-captcha" src="/images/temp/captcha/captcha_<?php echo session_id(); ?>.png">
		<a href="/<?php echo BEHAVIOR; ?>/Signup/Captcha" class="btn btn-primary"><i class="fas fa-sync"></i> Обновить</a>
		<div class="form-group has-feedback">
            <label id="label-captcha" for="captcha" class="control-label">Пожалуйста, введите указанный на изображении код:</label>
	    	<input id="captcha" name="captcha" type="text" class="<?php echo $_SESSION[$form]['captcha_cls']; ?>" value="<?php echo $_SESSION[$form]['captcha'] ?>">
<?php if (!empty($_SESSION[$form]['captcha_err'])) { ?>
			<div class="invalid-feedback"><?php echo $_SESSION[$form]['captcha_err']; ?></div>
<?php } ?>
        </div>
		<div class="form-group">
			<div class="col">
				<button type="submit" class="btn btn-success" id="btn_signup" name="btn_signup">Зарегистрироваться</button>
				<a href="/<?php echo BEHAVIOR; ?>/Signup/Reset" class="btn btn-danger">Сбросить</a>
				<a href="/<?php echo BEHAVIOR; ?>/Login/Index" class="btn btn-primary">Войти</a>
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
