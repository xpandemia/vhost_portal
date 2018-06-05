<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка удаления записи!\nСвяжитесь с администратором."));
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form action="<?php echo Basic_Helper::appUrl($data['ctr'], 'Delete'); ?>" method="post" id="form_del_confirm" novalidate>
		<legend class="font-weight-bold" align="center">Вы действительно хотите удалить запись "<?php echo $data['hdr']; ?>" № <?php echo $data['id']; ?> ?</legend>
		<div class="form-group">
			<input type="hidden" id="id" name="id" value="<?php echo $data['id']; ?>"/>
			<input type="hidden" id="pid" name="pid" value="<?php echo (isset($data['pid'])) ? $data['pid'] : null; ?>"/>
			<input type="hidden" id="hdr" name="hdr" value="<?php echo $data['hdr']; ?>"/>
			<input type="hidden" id="ctr" name="ctr" value="<?php echo $data['ctr']; ?>"/>
		</div>
		<div class="form-group row">
			<div class="col text-right">
				<button type="submit" class="btn btn-success" id="del_yes" name="del_yes">Да</button>
			</div>
			<div class="col">
				<?php
					echo HTML_Helper::setHrefButton($data['ctr'], 'Index'.((!empty($data['pid'])) ? '/?pid='.$data['pid'] : ''), 'btn btn-danger', 'Нет');
				?>
			</div>
		</div>
	</form>
	<?php echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger'); ?>
</div>
