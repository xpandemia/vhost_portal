<?php
	$form = 'resume';
	// check resume
	if (!isset($_SESSION[$form]['is_edit'])) {
		$_SESSION['main']['error_msg'] = 'Признак изменения персональных данных не установлен!';
		$basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
	} else {
		if ($_SESSION[$form]['is_edit'] === true) {
			$_SESSION[$form]['personal_vis'] = false;
		}
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form action="/<?php echo BEHAVIOR; ?>/Resume/Resume" method="post" id="form_personal" novalidate>
		<legend class="font-weight-bold"><?php echo RESUME_HDR; ?></legend>
		<div class="form-group">
			<label class="form-control-label text-danger font-weight-bold" for="name_first"><?php echo FIRSTNAME_PLC; ?></label>
			<div class="col">
				<input type="text" class="<?php echo $_SESSION[$form]['name_first_cls']; ?>" aria-describedby="name_firstHelpBlock" id="name_first" name="name_first" placeholder="<?php echo FIRSTNAME_PLC; ?>" value="<?php echo $_SESSION[$form]['name_first'] ?>">
<?php if (!empty($_SESSION[$form]['name_first_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['name_first_err']; ?></div>
<?php } ?>
				<p id="name_firstHelpBlock" class="form-text text-muted"><?php echo FIRSTNAME_HELP; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="form-control-label font-weight-bold" for="name_middle"><?php echo MIDDLENAME_PLC; ?></label>
			<div class="col">
				<input type="text" class="<?php echo $_SESSION[$form]['name_middle_cls']; ?>" aria-describedby="name_middleHelpBlock" id="name_middle" name="name_middle" placeholder="<?php echo MIDDLENAME_PLC; ?>" value="<?php echo $_SESSION[$form]['name_middle'] ?>">
<?php if (!empty($_SESSION[$form]['name_middle_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['name_middle_err']; ?></div>
<?php } ?>
				<p id="name_firstHelpBlock" class="form-text text-muted"><?php echo MIDDLENAME_HELP; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="form-control-label text-danger font-weight-bold" for="name_last"><?php echo LASTNAME_PLC; ?></label>
			<div class="col">
				<input type="text" class="<?php echo $_SESSION[$form]['name_last_cls']; ?>" aria-describedby="name_lastHelpBlock" id="name_last" name="name_last" placeholder="<?php echo LASTNAME_PLC; ?>" value="<?php echo $_SESSION[$form]['name_last'] ?>">
<?php if (!empty($_SESSION[$form]['name_last_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['name_last_err']; ?></div>
<?php } ?>
				<p id="name_lastHelpBlock" class="form-text text-muted"><?php echo LASTNAME_HELP; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="form-control-label text-danger font-weight-bold" for="sex">Пол</label>
			<div class="col">
				<label class="radio-inline"><input type="radio" id="male" name="sex" value="1" <?php echo ($_SESSION[$form]['sex']==='1')?'checked':'' ?>>Мужской</label>
				<label class="radio-inline"><input type="radio" id="female" name="sex" value="0" <?php echo ($_SESSION[$form]['sex']==='0')?'checked':'' ?>>Женский</label>
				<?php echo "<p class='text-danger'>".$_SESSION[$form]['sex_err']."</p>"; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-control-label text-danger font-weight-bold" for="birth_dt">Дата рождения</label>
			<div class="col">
				<input type="text" class="<?php echo $_SESSION[$form]['birth_dt_cls']; ?>" id="birth_dt" name="birth_dt" value="<?php echo $_SESSION[$form]['birth_dt'] ?>">
<?php if (!empty($_SESSION[$form]['birth_dt_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['birth_dt_err']; ?></div>
<?php } ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-control-label text-danger font-weight-bold" for="birth_place"><?php echo BIRTHPLACE_PLC; ?></label>
			<div class="col">
				<input type="text" class="<?php echo $_SESSION[$form]['birth_place_cls']; ?>" aria-describedby="birth_placeHelpBlock" id="birth_place" name="birth_place" placeholder="<?php echo BIRTHPLACE_PLC; ?>" value="<?php echo $_SESSION[$form]['birth_place'] ?>">
<?php if (!empty($_SESSION[$form]['birth_place_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['birth_place_err']; ?></div>
<?php } ?>
				<p id="birth_placeHelpBlock" class="form-text text-muted"><?php echo BIRTHPLACE_HELP; ?></p>
			</div>
		</div>

<?php if ($_SESSION[$form]['personal_vis'] == true) { ?>
		<div class="form-group row">
			<div class="col">
				<input type="checkbox" class="<?php echo $_SESSION[$form]['personal_cls']; ?>" id="personal" name="personal" <?php echo $_SESSION[$form]['personal'] ?>><b>Я даю согласие на обработку своих персональных данных в соответствии с Федеральным законом РФ от 27 июля 2006 г. №152-ФЗ "О персональных данных"</b>
<?php if (!empty($_SESSION[$form]['personal_err'])) { ?>
				<div class="invalid-feedback"><?php echo $_SESSION[$form]['personal_err']; ?></div>
<?php } ?>
			</div>
		</div>
<?php } ?>
		<div class="form-group">
			<div class="col">
				<button type="submit" class="btn btn-success" id="btn_save" name="btn_save">Сохранить</button>
				<a href="/<?php echo BEHAVIOR; ?>/Resume/Reset" class="btn btn-danger">Очистить</a>
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

<script>
	$(function(){
	  $("#birth_dt").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	});
</script>
