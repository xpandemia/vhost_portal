<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Personal as Personal;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_ApplicationPlacesExams as Model_ApplicationPlacesExams;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use common\models\Model_ApplicationAchievs as Model_ApplicationAchievs;
use frontend\models\Model_Application as Model_Application;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка направлений подготовки!\nСвяжитесь с администратором."));
	}
	// get app
	$app = new Application();
	$app->id = $data['id'];
	$app_row = $app->get();
	// get citizenship
	$personal = new Personal();
	$citizenship = $personal->getCitizenshipByUser();
	// manage scans
	$place = new ApplicationPlaces();
	$place->pid = $data['id'];
	$exam = new ApplicationPlacesExams();
	$exam->pid = $data['id'];
	// photo3x4
	if ($place->getByAppForBachelorSpec() && $exam->existsExams()) {
		$photo3x4 = 1;
	} else {
		$photo3x4 = 0;
	}
	// medical certificate
	if ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1()) {
		$medical_certificate = 1;
	} else {
		$medical_certificate = 0;
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Заявление № <?php echo $app_row['numb']; ?></h2>
	<?php
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		/* type */
		echo Model_Application::showType($app_row['type']);
		/* status */
		echo Model_Application::showStatus($app_row['status']);
		/* comment */
		if ($app_row['status'] == $app::STATUS_REJECTED) {
			$applog = new ApplicationStatus();
			$applog->id_application = $app_row['id'];
			$applog_row = $applog->getLast();
			echo HTML_Helper::setAlert('Причина отклонения: <strong>'.$applog_row['comment'].'</strong>', 'alert-danger');
		}
	?>
	<hr><h5>Направления подготовки</h5><br>
	<?php
		echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЧтобы добавить/изменить <strong>направления подготовки</strong>, нажмите <i class=\"far fa-file\"></i>\n<strong>При изменении направлений подготовки загруженные до этого скан-копии удаляются!</strong>"), 'alert-warning');
		if ($app_row['pay'] == 0) {
			$pay = 'Вы можете поступать и на бесплатную, и на платную основу обучения.';
		} else {
			$pay = 'Вы можете поступать <strong>только на платную</strong> основу обучения!';
		}
		echo HTML_Helper::setAlert($pay, 'alert-info');
		echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_ApplicationPlaces',
									'model_method' => 'getGrid',
									'model_filter' => 'pid',
									'model_filter_var' => $data['id'],
									'grid' => 'grid',
									'controller' => 'ApplicationSpec',
									'action_add' => 'AddPlaces/?pid='.$data['id'],
									'home_hdr' => 'Направления подготовки']);
	?>
	<form enctype="multipart/form-data" action="<?php echo Basic_Helper::appUrl('ApplicationSpec', 'Save'); ?>" method="post" id="form_app_spec" novalidate>
		<div class="form-group">
			<input type="hidden" id="id" name="id" value="<?php echo $data['id']; ?>"/>
			<input type="hidden" id="photo3x4_required" name="photo3x4_required" value="<?php echo $photo3x4; ?>"/>
			<input type="hidden" id="medical_certificate_required" name="medical_certificate_required" value="<?php echo $medical_certificate; ?>"/>
		</div>
		<div class="form-group">
			<h5>Вступительные испытания</h5><br>
			<table class="table table-bordered table-hover">
				<thead class="thead-dark">
					<tr>
						<th class="align-text-top">Предмет</th>
						<th class="align-text-top">Тип испытания</th>
					</tr>
			    </thead>
			<?php
				$exams = new Model_ApplicationPlacesExams();
				$exams->pid = $data['id'];
				$exams_arr = $exams->getExamsByApplication();
				if ($exams_arr) {
					foreach ($exams_arr as $exams_row) {
						echo '<tr>';
						echo '<td>'.$exams_row['discipline_name'].'</td>';
							if ($citizenship['code'] != '643') {
								$disabled = 1;
							} else {
								if ($citizenship['code'] == '643' && $app->checkCertificate()) {
									$disabled = 1;
								} else {
									$disabled = 0;
								}
							}
							$test = new Model_DictTestingScopes();
							$test_arr = $test->getEntranceExams();
							if ($test_arr) {
								echo '<td><select class="form-control" id="exam'.$exams_row['discipline_code'].'" name="exam'.$exams_row['discipline_code'].'"'.(($disabled == 1) ? ' disabled' : '').'>';
								foreach ($test_arr as $test_row) {
									echo '<option value="'.$test_row['code'].'"'.
										(($exams_row['code'] === $test_row['code']) ? ' selected' : '').'>'.
										$test_row['description'].
										'</option>';
								}
								echo '</select></td>';
							}
						echo '</tr>';
					}
				}
			?>
			</table><br>
			<h5>Индивидуальные достижения</h5><br>
			<table class="table table-bordered table-hover">
				<thead class="thead-dark">
					<tr>
						<th class="align-text-top">Индивидуальное достижение</th>
						<th class="align-text-top">Документ</th>
						<th class="align-text-top">Выдан</th>
						<th class="align-text-top">Дата выдачи</th>
					</tr>
			    </thead>
			<?php
				$ia = new Model_ApplicationAchievs();
				$ia->pid = $data['id'];
				$ia_arr = $ia->getGrid();
				if ($ia_arr) {
					foreach ($ia_arr as $ia_row) {
						echo '<tr>';
						echo '<td>'.$ia_row['achiev'].'</td>';
						echo '<td>'.$ia_row['doc'].'</td>';
						echo '<td>'.$ia_row['company'].'</td>';
						echo '<td>'.$ia_row['dt_issue'].'</td>';
						echo '</tr>';
					}
				}
			?>
			</table><br>
			<?php
				/* additional info */
				echo Form_Helper::setFormHeaderSub('Дополнительная информация');
				// campus
				echo Form_Helper::setFormCheckbox(['label' => 'Нуждаюсь в общежитии',
													'control' => 'campus',
													'class' => $data['campus_cls'],
													'value' => $data['campus'],
													'success' => $data['campus_scs'],
													'error' => $data['campus_err']]);
				// conds
				echo Form_Helper::setFormCheckbox(['label' => 'Прошу создать специальные условия (например: присутствие ассистента, наличие звукоусиливающей аппаратуры)',
													'control' => 'conds',
													'class' => $data['conds_cls'],
													'value' => $data['conds'],
													'success' => $data['conds_scs'],
													'error' => $data['conds_err']]);
				// remote
				echo Form_Helper::setFormCheckbox(['label' => 'Прошу разрешить сдачу вступительных испытаний с использованием дистанционных технологий (только для поступающих на платную основу заочной формы обучения)',
													'control' => 'remote',
													'class' => $data['remote_cls'],
													'value' => $data['remote'],
													'success' => $data['remote_scs'],
													'error' => $data['remote_err']]);
				/* save application as PDF */
				echo '<p></p>';
				echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nПожалуйста, не вносите изменения в печатную форму заявления.\nПросто распечатайте её, подпишите, отсканируйте и загрузите в электронное заявление."), 'alert-warning');
				echo HTML_Helper::setHrefButtonIcon('ApplicationSpec', 'SavePdf/?pid='.$data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Распечатать заявление');
				/* scans */
				echo Form_Helper::setFormHeaderSub('Скан-копии');
				echo Form_Helper::setFormFileListDB(['required' => 'required',
													'required_style' => 'StarUp',
													'model_class' => 'common\\models\\Model_DictScans',
													'model_method' => 'getByDocument',
													'model_filter' => 'doc_code',
													'model_filter_var' => 'application',
													'model_field' => 'scan_code',
													'model_field_name' => 'scan_name',
													'data' => $data,
													'home_ctr' => 'Application',
													'home_hdr' => 'Направления подготовки',
													'home_act' => 'Edit/?id='.$data['id'],
													'ext' => FILES_EXT_SCANS]);
			?>
		</div>
		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-info', 'btn_save', 'Сохранить');
					echo HTML_Helper::setHrefButton('ApplicationSpec', 'Send/?id='.$data['id'], 'btn btn-success', 'Отправить');
					echo HTML_Helper::setHrefButton('ApplicationSpec', 'Change/?id='.$data['id'], 'btn btn-primary', 'Изменить');
					echo HTML_Helper::setHrefButton('ApplicationSpec', 'Recall/?id='.$data['id'], 'btn btn-danger', 'Отозвать');
					echo HTML_Helper::setHrefButton('ApplicationSpec', 'Cancel', 'btn btn-warning', 'Отмена');
				?>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function(){
		formInit();
		formEvents();
	});
</script>

<script>
	// form init
	function formInit()
	{
		// photo3x4 required
		if ($('#photo3x4_required').val() == 1) {
			$("label[for='photo3x4']").html('Фотография 3х4*');
		} else {
			$("label[for='photo3x4']").html('Фотография 3х4');
		}
		// medical certificate required
		if ($('#medical_certificate_required').val() == 1) {
			$("label[for='medical_certificate_face']").html('Медицинская справка (лицевая сторона)*');
			$("label[for='medical_certificate_back']").html('Медицинская справка (оборотная сторона)*');
		} else {
			$("label[for='medical_certificate_face']").html('Медицинская справка (лицевая сторона)');
			$("label[for='medical_certificate_back']").html('Медицинская справка (оборотная сторона)');
		}
	}
</script>

<script>
	// form events
	function formEvents()
	{
		// submit click
		$('#btn_save').click(function() {
			$("select[name^='exam']").prop('disabled', false);
		});
	}
</script>
