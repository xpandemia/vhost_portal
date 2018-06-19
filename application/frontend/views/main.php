<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use common\models\Model_Personal as Personal;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Ege as Ege;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_Application as Application;
use frontend\models\Model_Resume as Model_Resume;

include ROOT_DIR.'/application/frontend/models/Model_Resume.php';

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="row">
	<?php
		$personal = new Personal();
		$personal_row = $personal->getFioByUser();
		if ($personal_row) {
			$welcome = 'Добро пожаловать, '.$personal_row['name_last'].' '.$personal_row['name_first'].' '.$personal_row['name_middle'].'!';
		} else {
			$welcome = 'Добро пожаловать!';
		}
	?>
	<div class="col text-primary"><h3><?php echo $welcome; ?></h3></div>
</div>
<?php
	$personal_row = $personal->getCode1sByUser();
	if (!empty($personal_row['code1s'])) {
		echo '<div class="row">';
		echo '<div class="col"><h5>Ваш идентификатор абитуриента <a href="http://abitur.bsu.edu.ru/abitur/exam/sched/" target="_blank">для просмотра расписания вступительных испытаний на сайте</a>: <strong>'.$personal_row['code1s'].'</strong></h5></div>';
		echo '</div>';
	}
?>
<div class="row">
	<div class="col">
		<strong>Для получения дополнительной информации Вы можете обратиться в <a href="http://abitur.bsu.edu.ru/abitur/help/contacts/" target="_blank">Приёмную комиссию</a></strong><br>
	</div>
</div>
<?php
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
?>
<div class="row">
	<div class="col text-center text-primary">
		<h3>Схема работы (* - обязательные шаги)</h3>
	</div>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpResume">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', RESUME['ctr']); ?>">Шаг 1*: Анкета <i class="fas fa-id-card"></i></a>
		</h4>
	</div>
	<?php
		$resume = new Resume();
		$resume->id_user = $_SESSION[APP_CODE]['user_id'];
		$resume_row = $resume->checkByUser();
		if (!$resume_row) {
			echo '<div class="col col-sm-3 alert alert-danger"><h5>Состояние шага - не пройден</h5></div>';
			echo '<div class="col col-sm-3"></div>';
			echo '<div class="col col-sm-1"></div>';
		} elseif ($resume_row['status'] == $resume::STATUS_CREATED) {
			echo '<div class="col col-sm-3 alert alert-danger"><h5>Состояние шага - не пройден</h5></div>';
			echo Model_Resume::showStatus($resume_row['status'], 3);
			echo '<div class="col col-sm-1"></div>';
		} else {
			echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
			echo Model_Resume::showStatus($resume_row['status'], 3);
			echo '<div class="col col-sm-1"></div>';
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpDocsEduc">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', DOCS_EDUC['ctr']); ?>">Шаг 2*: Документы об образовании <i class="fas fa-graduation-cap"></i></a>
		</h4>
	</div>
	<?php
		$docs = new DocsEduc();
		$docs->id_user = $_SESSION[APP_CODE]['user_id'];
		$docs_arr = $docs->getByUser();
		if ($docs_arr) {
			stepSuccess('документов', count($docs_arr));
		} else {
			stepError(1);
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpEge">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', EGE['ctr']); ?>">Шаг 3: Результаты ЕГЭ <i class="fas fa-table"></i></a>
		</h4>
	</div>
	<?php
		$ege = new Ege();
		$ege->id_user = $_SESSION[APP_CODE]['user_id'];
		$ege_arr = $ege->getByUser();
		if ($ege_arr) {
			stepSuccess('результатов ЕГЭ', count($ege_arr));
		} else {
			stepError(0);
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', IND_ACHIEVS['ctr']); ?>">Шаг 4: Индивидуальные достижения <i class="fas fa-trophy"></i></a>
		</h4>
	</div>
	<?php
		$ia = new IndAchievs();
		$ia->id_user = $_SESSION[APP_CODE]['user_id'];
		$ia_arr = $ia->getByUser();
		if ($ia_arr) {
			stepSuccess('индивидуальных достижений', count($ia_arr));
		} else {
			stepError(0);
		}
	?>
</div>
<div class="row">
	<?php
		if ($resume_row && $resume_row['app'] == 1 && $resume_row['status'] != $resume::STATUS_CREATED && $resume_row['status'] != $resume::STATUS_REJECTED && $docs_arr) {
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
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpApp">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', APP['ctr']); ?>">Шаг 5*: Заявления <i class="fas fa-file-alt"></i></a>
		</h4>
	</div>
	<?php
		$app = new Application();
		$app->id_user = $_SESSION[APP_CODE]['user_id'];
		$app_arr = $app->getByUser();
		if ($app_arr) {
			stepSuccess('заявлений', count($app_arr));
		} else {
			stepError(1);
		}
	?>
</div>

<div class="modal fade" id="helpResume" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Анкета (инструкция)</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-justify">
				<?php echo Help_Helper::resume_help(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpDocsEduc" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Документы об образовании (инструкция)</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-justify">
				<?php echo Help_Helper::docs_educ_help(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpEge" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Результаты ЕГЭ (инструкция)</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-justify">
				<?php echo Help_Helper::ege_help(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpIndAchievs" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Индивидуальные достижения (инструкция)</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<?php echo Help_Helper::ind_achievs_help(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpApp" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Заявления (инструкция)</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<?php echo Help_Helper::app_help(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<?php
	function stepSuccess($count_msg = null, $count = null)
	{
		echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
		if (empty($count_msg) || empty($count)) {
			echo '<div class="col col-sm-3"></div>';
			echo '<div class="col col-sm-1"></div>';
		} else {
			echo '<div class="col col-sm-3 alert alert-info"><h5>Кол-во '.$count_msg.' - '.$count.'</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		}
	}
	function stepError($required = 0)
	{
		echo '<div class="col col-sm-3 alert alert-'.(($required == 1) ? 'danger' : 'warning').'"><h5>Состояние шага - не пройден</h5></div>';
		echo '<div class="col col-sm-3"></div>';
		echo '<div class="col col-sm-1"></div>';
	}
?>
