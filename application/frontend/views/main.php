<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Ege as Ege;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_Application as Application;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="row">
	<div class="col text-primary"><h3>Добро пожаловать, <?php echo $_SESSION[APP_CODE]['user_name']; ?>!</h3></div>
	<div class="col text-right"><img src="/images/new-bsulogo.jpg" alt="Logo" style="width:60px;heigth:90px"></div>
</div>
<div class="row">
	<div class="col">
		<strong>Для получения дополнительной информации Вы можете обратиться в Приемную комиссию:</strong><br>
		E-mail: <a href="mailto:Exam@bsu.edu.ru"><strong>Exam@bsu.edu.ru</strong></a><br>
		Тел: (4722) 30-18-80, 30-18-90<br>
		Время работы: с 9.00 до 18.00, перерыв с 13.00 до 14.00
	</div>
	<div class="col text-right">
		<a href="http://abitur.bsu.edu.ru/abitur/"><strong>Ваше будущее в ваших руках!</strong></a><br>
		<a href="http://abitur.bsu.edu.ru/abitur/help/contacts/"><strong>Контакты Приёмной комиссии</strong></a>
	</div>
</div>
<?php
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
?>
<div class="row">
	<div class="col text-center">
		<h3>Схема работы</h3>
	</div>
</div>
<div class="row">
	<div class="col col-sm-6 text-right text-primary"><h4>Шаг 1*: Заполните анкету <i class="fas fa-id-card"></i></h4></div>
	<?php
		$resume = new Resume();
		$resume->id_user = $_SESSION[APP_CODE]['user_id'];
		$resume_row = $resume->checkByUser();
		if ($resume_row) {
			stepSuccess();
		} else {
			stepError();
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-6 text-right text-primary"><h4>Шаг 2*: Документы об образовании <i class="fas fa-graduation-cap"></i></h4></div>
	<?php
		$docs = new DocsEduc();
		$docs->id_user = $_SESSION[APP_CODE]['user_id'];
		$docs_arr = $docs->getByUser();
		if ($docs_arr) {
			stepSuccess('документов', count($docs_arr));
		} else {
			stepError();
		}
	?>
</div>
<div class="row">
	<div class="col text-right text-primary"><h4>Шаг 3: Результаты ЕГЭ <i class="fas fa-table"></i></h4></div>
	<?php
		$ege = new Ege();
		$ege->id_user = $_SESSION[APP_CODE]['user_id'];
		$ege_arr = $ege->getByUser();
		if ($ege_arr) {
			stepSuccess('результатов ЕГЭ', count($ege_arr));
		} else {
			stepError();
		}
	?>
</div>
<div class="row">
	<div class="col text-right text-primary"><h4>Шаг 4: Индивидуальные достижения <i class="fas fa-trophy"></i></h4></div>
	<?php
		$ia = new IndAchievs();
		$ia->id_user = $_SESSION[APP_CODE]['user_id'];
		$ia_arr = $ia->getByUser();
		if ($ia_arr) {
			stepSuccess('индивидуальных достижений', count($ia_arr));
		} else {
			stepError();
		}
	?>
</div>
<div class="row">
	<?php
		if ($resume_row && $resume_row['app'] == 1) {
			echo '<div class="col col-sm-1"></div>';
			echo '<div class="col col-sm-10 text-center alert alert-success"><h5>Подача заявлений разрешена</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		} else {
			echo '<div class="col col-sm-1"></div>';
			echo '<div class="col col-sm-10 text-center alert alert-danger"><h5>Подача заявлений запрещена</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		}
	?>
</div>
<div class="row">
	<div class="col text-right text-primary"><h4>Шаг 5*: Заявления <i class="fas fa-file-alt"></i></h4></div>
	<?php
		$app = new Application();
		$app->id_user = $_SESSION[APP_CODE]['user_id'];
		$app_arr = $app->getByUser();
		if ($app_arr) {
			stepSuccess('заявлений', count($app_arr));
		} else {
			stepError();
		}
	?>
</div>
<?php
	function stepSuccess($count_msg = null, $count = null)
	{
		echo '<div class="col col-sm-2 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
		if (empty($count_msg) || empty($count)) {
			echo '<div class="col col-sm-3"></div>';
			echo '<div class="col col-sm-1"></div>';
		} else {
			echo '<div class="col col-sm-3 alert alert-info"><h5>Кол-во '.$count_msg.' - '.$count.'</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		}
	}
	function stepError()
	{
		echo '<div class="col col-sm-2 alert alert-danger"><h5>Состояние шага - не пройден</h5></div>';
		echo '<div class="col col-sm-3"></div>';
		echo '<div class="col col-sm-1"></div>';
	}
?>
