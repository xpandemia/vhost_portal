<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Files_Helper as Files_Helper;

	// check data
	if (!isset($data)) {
		$data['error_msg'] = 'Ошибка просмотра скан-копии! Свяжитесь с администратором.';
		Basic_Helper::redirect(APP_NAME, 401, 'Main', 'Index', $data);
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<p class="font-weight-bold">Номер файла: <?php echo $data['id']; ?></p>
	<p class="font-weight-bold">Имя файла: <?php echo $data['file_name']; ?></p>
	<p class="font-weight-bold">Тип файла: <?php echo $data['file_type']; ?></p>
	<p class="font-weight-bold">Размер файла: <?php echo Files_Helper::getSize($data['file_size'], 'MB').' Мб'; ?></p>
	<?php
		echo HTML_Helper::setImageLOB($data['file_type'], $data['file_data']);
		if (isset($data['pid']) && !empty($data['pid'])) {
			echo HTML_Helper::setHrefText($data['ctr'], $data['act'].'/?id='.$data['pid'], 'Вернуться');
		} else {
			echo HTML_Helper::setHrefText($data['ctr'], $data['act'], 'Вернуться');
		}
	?>
</div>
