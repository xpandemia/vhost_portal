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
	<div class="col text-primary"><h3>Добро пожаловать!</h3></div>
</div>
<div class="row">
	<div class="col">
		<strong>Для получения дополнительной информации Вы можете обратиться в Приемную комиссию:</strong><br>
		E-mail: <a href="mailto:Exam@bsu.edu.ru"><strong>Exam@bsu.edu.ru</strong></a><br>
		Тел: (4722) 30-18-80, 30-18-90<br>
		Время работы: с 9:00 до 18:00, перерыв с 13:00 до 14:00
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
		if ($resume_row && $resume_row['status'] != $resume::STATUS_CREATED) {
			stepSuccess();
		} else {
			stepError(1);
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
			</div>
			<div class="modal-body text-justify">
				<p>
					<strong>Уважаемый абитуриент!</strong><br><br>
					Когда Вы видите свою анкету впервые, она пуста и имеет состояние <strong>"<?php echo $resume::STATUS_CREATED_NAME; ?>"</strong>. Это означает, что она готова для заполнения. Пожалуйста, аккуратно заполните нужные поля <strong>(* - означает, что поле обязательно для заполнения)</strong> и нажмите кнопку <strong>"Сохранить"</strong>. После этого система проверит введенные Вами данные и<br>
					<u>- если ошибок нет</u>, сохранит их для дальнейшего использования, анкета получит состояние <strong>"<?php echo $resume::STATUS_SAVED_NAME; ?>"</strong> и Вы увидите выделенное зелёным цветом сообщение "Анкета успешно сохранена."<br>
					<u>- если ошибки есть</u>, они отобразятся красным цветом, а состояние анкеты не изменится. Исправьте ошибки и снова нажмите <strong>"Сохранить"</strong>. Повторяйте до тех пор, пока не увидите выделенное зелёным цветом сообщение "Анкета успешно сохранена."<br><br>
					Анкету в состоянии <strong>"<?php echo $resume::STATUS_SAVED_NAME; ?>"</strong> можно исправлять без каких бы то ни было ограничений, но на её основании нельзя формировать заявления на поступление. Чтобы получить такую возможность, необходимо перевести анкету в состояние <strong>"<?php echo $resume::STATUS_SENDED_NAME; ?>"</strong>. Для этого нажмите кнопку <strong>"Отправить"</strong>, когда будете готовы двигаться дальше.<br><br>
					Анкеты в состояние <strong>"<?php echo $resume::STATUS_SENDED_NAME; ?>"</strong> исправлять уже нельзя, так как они попали на обработку к модератору. Если по каким-либо причинам исправления всё-таки надо внести, пожалуйста, свяжитесь с приёмной комиссией лично.<br><br>
					Кнопка <strong>"Очистить"</strong> используется лишь для обнуления данных анкеты, <strong>сохранения при этом не происходит!</strong> То есть, когда Вам, например, надо внести значительные изменения в анкету, целесообразно нажать кнопку <strong>"Очистить"</strong>, поработать с анкетой, как с чистым листом, и потом сохранить её.
				</p>
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
			</div>
			<div class="modal-body">
				<p>Находится в разработке, приносим свои извинения.</p>
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
			</div>
			<div class="modal-body">
				<p>Находится в разработке, приносим свои извинения.</p>
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
			</div>
			<div class="modal-body">
				<p>Находится в разработке, приносим свои извинения.</p>
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
			</div>
			<div class="modal-body">
				<p>Находится в разработке, приносим свои извинения.</p>
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
