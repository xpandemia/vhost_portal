<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_ApplicationPlacesExams as Model_ApplicationPlacesExams;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use frontend\models\Model_Application as Model_Application;

	// check data
	if (!isset($data)) {
		Basic_Helper::alertGlobal('Ошибка направлений подготовки! Свяжитесь с администратором.');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Заявление</h2>
	<?php echo Model_Application::showStatus($data['status']); ?>
	<hr><h5>Направления подготовки</h5><br>
	<div class="alert alert-warning">
		<strong>Внимание!</strong>
		<p>Чтобы добавить/изменить направления подготовки, нажмите "Создать запись".</p>
	</div>
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_ApplicationPlaces',
									'model_method' => 'getGrid',
									'model_filter' => 'pid',
									'model_filter_var' => $data['id'],
									'grid' => 'grid',
									'controller' => 'ApplicationSpec',
									'action_add' => 'AddPlaces/?pid='.$data['id'],
									'home_hdr' => 'Направления подготовки']);
	?>
	<hr><h5>Вступительные испытания</h5><br>
	<form enctype="multipart/form-data" action="<?php echo Basic_Helper::appUrl('ApplicationSpec', 'Save'); ?>" method="post" id="form_app_spec" novalidate>
		<div class="form-group">
			<input type="hidden" id="id" name="id" value="<?php echo $data['id']; ?>"/>
		</div>
		<div class="form-group">
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
			</table>
			<?php
				// save application as PDF
				echo HTML_Helper::setHrefButtonIcon('ApplicationSpec', 'SavePdf', 'font-weight-bold', 'fas fa-print fa-3x', 'Распечатать заявление');
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
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				?>
			</div>
		</div>
	</form>
</div>
