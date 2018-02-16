<?php $form = 'reset_pwd_request'; ?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form action="/<?php echo BEHAVIOR; ?>/ResetPwdRequest/SendEmail" method="post" id="form_send_email" novalidate>
		<legend><?php echo RESET_PWD_REQUEST_HDR; ?></legend>
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
		<div class="form-group">
			<div class="col">
				<button type="submit" class="btn btn-success" id="btn_send_email" name="btn_send_email">Выслать письмо</button>
				<a href="/<?php echo BEHAVIOR; ?>/ResetPwdRequest/Reset" class="btn btn-danger">Сбросить</a>
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
