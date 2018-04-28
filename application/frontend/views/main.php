<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="row">
	<div class="col"><h3>Добро пожаловать, <?php echo $_SESSION[APP_CODE]['user_name']; ?>!</h2></div>
	<div class="col text-right"><img src="/images/new-bsulogo.jpg" alt="Logo" style="width:60px;heigth:90px"></div>
</div>
<div class="row">
	<div class="col">
		Для получения дополнительной информации Вы можете обратиться в Приемную комиссию:<br>
		E-mail: <a href="mailto:Exam@bsu.edu.ru">Exam@bsu.edu.ru</a><br>
		Тел: (4722) 30-18-80, 30-18-90<br>
		Время работы: с 9.00 до 18.00, перерыв с 13.00 до 14.00
	</div>
	<div class="col text-right">
		<a href="http://abitur.bsu.edu.ru/abitur/">Ваше будущее в ваших руках!</a><br>
		<a href="http://abitur.bsu.edu.ru/abitur/help/contacts/">Контакты Приёмной комиссии</a>
	</div>
</div>
<?php
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
?>
