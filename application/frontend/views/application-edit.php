<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_ApplicationPlacesExams as Model_ApplicationPlacesExams;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use common\models\Model_ApplicationAchievs as Model_ApplicationAchievs;
use frontend\models\Model_Application as Model_Application;

	// check data
	if (!isset($data)) {
		Basic_Helper::alertGlobal('Ошибка направлений подготовки! Свяжитесь с администратором.');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Заявление</h2>
	<?php
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		/* status */
		echo Model_Application::showStatus($data['status']);
	?>
	<hr><h5>Направления подготовки</h5><br>
	<div class="alert alert-warning">
		<strong>Внимание!</strong>
		<p>Чтобы добавить/изменить направления подготовки, нажмите "Создать запись".</p>
	</div>
	<?php
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
			<input type="hidden" id="status" name="status" value="<?php echo $data['status']; ?>"/>
		</div>
		<div class="form-group">
			<h5>Вступительные испытания</h5><br>
			<table class="table table-bordered table-hover">
				<thead class="thead-dark">
					<tr>
						<th>Предмет</th>
						<th>Тип испытания</th>
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
							$test = new Model_DictTestingScopes();
							$test_arr = $test->getEntranceExams();
							if ($test_arr) {
								echo '<td><select class="form-control" id="exam'.$exams_row['discipline_code'].'" name="exam'.$exams_row['discipline_code'].'">';
								echo '<option value=""'.(empty($exams_row['code']) ? ' selected' : '').'></option>';
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
						<th>Индивидуальное достижение</th>
						<th>Документ</th>
						<th>Выдан</th>
						<th>Дата выдачи</th>
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
				// docs shipment
				echo Form_Helper::setFormSelectListDB(['label' => 'Тип возврата документов',
														'control' => 'docs_ship',
														'class' => $data['docs_ship_cls'],
														'required' => 'yes',
														'required_style' => 'StarUp',
														'model_class' => 'common\\models\\Model_DictDocships',
														'model_method' => 'getAll',
														'model_field' => 'code',
														'model_field_name' => 'description',
														'value' => $data['docs_ship'],
														'success' => $data['docs_ship_scs'],
														'error' => $data['docs_ship_err']]);
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
				/* save application as PDF */
				echo HTML_Helper::setHrefButtonIcon('ApplicationSpec', 'SavePdf', 'font-weight-bold', 'fas fa-print fa-3x', 'Распечатать заявление');
			?>
		</div>
		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
					echo HTML_Helper::setHrefButton('ApplicationSpec', 'Cancel', 'btn btn-warning', 'Отмена');
				?>
			</div>
		</div>
	</form>
</div>
