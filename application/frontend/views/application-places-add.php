<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use common\models\Model_Application as Application;
use common\models\Model_AdmissionCampaign as AdmissionCampaign;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_DocsEduc as DocsEduc;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка выбора направлений подготовки!\nСвяжитесь с администратором."));
	}
	// get application
	$app = new Application();
	$app->id = $data['pid'];
	$app_row = $app->get();
	// check type
	if ($app_row['type'] == $app::TYPE_RECALL) {
		Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Нельзя изменять направления подготовки в заявлениях с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!');
	}
	// check status
	if ($app_row['status'] != $app::STATUS_CREATED && $app_row['status'] != $app::STATUS_SAVED && $app_row['status'] != $app::STATUS_CHANGED) {
		Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Изменять направления подготовки можно только в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_CHANGED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!');
	}
	// get admission campaign
	$camp = new AdmissionCampaign();
	$camp->id = $app_row['id_campaign'];
	$camp_row = $camp->getById();
		// check special education
		if (strpos($camp_row['description'], 'СПО') || strpos($camp_row['description'], 'СПО') === 0) {
			$special = 1;
		} else {
			$special = 0;
		}
	// get specs
	$specs = new ApplicationPlaces();
	$specs->pid = $data['pid'];
	// get education document
	$docs = new DocsEduc();
	$docs->id = $app_row['id_docseduc'];
	$docs_row = $docs->get();
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form enctype="multipart/form-data" action="<?php echo Basic_Helper::appUrl('ApplicationSpec', 'SavePlaces'); ?>" method="post" id="form_app_places" novalidate>
		<div class="sticky">
			<div class="form-group row">
				<div class="col">
					<?php
						echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЧтобы воспользоваться выбором, пожалуйста, отметьте в списке нужные направления подготовки и нажмите <strong>\"Выбрать\"</strong>."), 'alert-warning small');
					?>
				</div>
			</div>
			<div class="form-group row">
				<div class="col col-sm-4 font-weight-bold">Направления подготовки</div>
				<div class="col col-sm-3 font-weight-bold">разрешено выбрать: <?php echo $camp_row['max_spec']; ?></div>
				<div class="col text-center">
					<input type="checkbox" class="form-check-input" id="filters_flag" name="filters_flag"><b>Фильтры</b>
				</div>
			</div>
			<?php
				echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
				echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
			?>
			<div class="col" id="filters" name="filters">
				<div class="form-group row">
					<?php
						// filter_speciality
						if ($special == 1) {
							if (in_array($docs_row['doc_type'], $docs::CLASSES_9)) {
								$speciality_arr = $specs->getSpecialitySpecial9ForApp($app_row['pay']);
							} else {
								$speciality_arr = $specs->getSpecialityFirstForApp($app_row['pay']);
							}
						} else {
							if (in_array($docs_row['doc_type'], $docs::HIGH_BEFORE) || $app->checkMagistratureFirst() || $app->checkHighAfter()) {
								$speciality_arr = $specs->getSpecialityFirstForApp($app_row['pay']);
							} else {
								$speciality_arr = $specs->getSpecialitySecondForApp($app_row['pay']);
							}
						}
						if ($speciality_arr) {
							echo '<div class="col col-sm-3">';
							echo '<label class="font-weight-bold small" for="filter_speciality">Специальность/профиль</label>';
							echo '</div>';
							echo '<div class="col col-sm-9">';
							echo '<select class="form-control" id="filter_speciality" name="filter_speciality">';
							echo '<option value="" selected></option>';
							foreach ($speciality_arr as $speciality_row) {
								echo '<option value="'.$speciality_row['speciality_name'].((!empty($speciality_row['profil_name'])) ? ' '.$speciality_row['profil_name'] : '').'">'.$speciality_row['speciality_name'].((!empty($speciality_row['profil_name'])) ? ' '.$speciality_row['profil_name'] : '').'</option>';
							}
							echo '</select>';
							echo '</div>';
						}
					?>
				</div>
				<div class="form-group row">
					<?php
						// filter_finance
						if ($special == 1) {
							if (in_array($docs_row['doc_type'], $docs::CLASSES_9)) {
								$finance_arr = $specs->getFinanceSpecial9ForApp($app_row['pay']);
							} else {
								$finance_arr = $specs->getFinanceFirstForApp($app_row['pay']);
							}
						} else {
							if (in_array($docs_row['doc_type'], $docs::HIGH_BEFORE) || $app->checkMagistratureFirst() || $app->checkHighAfter()) {
								$finance_arr = $specs->getFinanceFirstForApp($app_row['pay']);
							} else {
								$finance_arr = $specs->getFinanceSecondForApp($app_row['pay']);
							}
						}
						if ($finance_arr) {
							echo '<div class="col col-sm-1">';
							echo '<label class="font-weight-bold small" for="filter_finance">Тип оплаты</label>';
							echo '</div>';
							echo '<div class="col col-sm-3">';
							echo '<select class="form-control" id="filter_finance" name="filter_finance">';
							echo '<option value="" selected></option>';
							foreach ($finance_arr as $finance_row) {
								echo '<option value="'.$finance_row['finance_name'].'">'.$finance_row['finance_name'].'</option>';
							}
							echo '</select>';
							echo '</div>';
						}
						// filter_eduform
						if ($special == 1) {
							if (in_array($docs_row['doc_type'], $docs::CLASSES_9)) {
								$eduform_arr = $specs->getEduformSpecial9ForApp($app_row['pay']);
							} else {
								$eduform_arr = $specs->getEduformFirstForApp($app_row['pay']);
							}
						} else {
							if (in_array($docs_row['doc_type'], $docs::HIGH_BEFORE) || $app->checkMagistratureFirst() || $app->checkHighAfter()) {
								$eduform_arr = $specs->getEduformFirstForApp($app_row['pay']);
							} else {
								$eduform_arr = $specs->getEduformSecondForApp($app_row['pay']);
							}
						}
						if ($eduform_arr) {
							echo '<div class="col col-sm-1">';
							echo '<label class="font-weight-bold small" for="filter_eduform">Форма обучения</label>';
							echo '</div>';
							echo '<div class="col col-sm-2">';
							echo '<select class="form-control" id="filter_eduform" name="filter_eduform">';
							echo '<option value="" selected></option>';
							foreach ($eduform_arr as $eduform_row) {
								echo '<option value="'.$eduform_row['eduform_name'].'">'.$eduform_row['eduform_name'].'</option>';
							}
							echo '</select>';
							echo '</div>';
						}
						// filter_edulevel
						if ($special == 1) {
							if (in_array($docs_row['doc_type'], $docs::CLASSES_9)) {
								$edulevel_arr = $specs->getEdulevelSpecial9ForApp($app_row['pay']);
							} else {
								$edulevel_arr = $specs->getEdulevelFirstForApp($app_row['pay']);
							}
						} else {
							if (in_array($docs_row['doc_type'], $docs::HIGH_BEFORE) || $app->checkMagistratureFirst() || $app->checkHighAfter()) {
								$edulevel_arr = $specs->getEdulevelFirstForApp($app_row['pay']);
							} else {
								$edulevel_arr = $specs->getEdulevelSecondForApp($app_row['pay']);
							}
						}
						if ($edulevel_arr) {
							echo '<div class="col col-sm-2">';
							echo '<label class="font-weight-bold small" for="filter_edulevel">Уровень обучения</label>';
							echo '</div>';
							echo '<div class="col col-sm-3">';
							echo '<select class="form-control" id="filter_edulevel" name="filter_edulevel">';
							echo '<option value="" selected></option>';
							foreach ($edulevel_arr as $edulevel_row) {
								echo '<option value="'.$edulevel_row['edulevel_name'].'">'.$edulevel_row['edulevel_name'].'</option>';
							}
							echo '</select>';
							echo '</div>';
						}
					?>
				</div>
				<div class="col">
					<?php
						echo HTML_Helper::setButton('btn btn-success', 'btn_filter_apply', 'Применить фильтры');
						echo HTML_Helper::setButton('btn btn-warning', 'btn_filter_cancel', 'Отменить фильтры');
					?>
				</div>
			</div>
		</div>
		<div class="form-group">
			<input type="hidden" id="pid" name="pid" value="<?php echo $data['pid']; ?>"/>
		</div>
		<div class="form-group">
			<table class="table table-bordered table-hover" id="table_specs" name="table_specs">
				<thead class="thead-dark">
					<tr>
						<th class="align-text-top"></th>
						<th class="align-text-top">Специальность/профиль</th>
						<th class="align-text-top">Тип оплаты</th>
						<th class="align-text-top">Форма обучения</th>
						<th class="align-text-top">Уровень обучения</th>
					</tr>
			    </thead>
			<?php
				if ($special == 1) {
					if (in_array($docs_row['doc_type'], $docs::CLASSES_9)) {
						$specs_arr = $specs->getSpecsSpecial9ForApp($app_row['pay']);
					} else {
						$specs_arr = $specs->getSpecsFirstForApp($app_row['pay']);
					}
				} else {
					if (in_array($docs_row['doc_type'], $docs::HIGH_BEFORE) || $app->checkMagistratureFirst() || $app->checkHighAfter()) {
						$specs_arr = $specs->getSpecsFirstForApp($app_row['pay']);
					} else {
						$specs_arr = $specs->getSpecsSecondForApp($app_row['pay']);
					}
				}
				if ($specs_arr) {
					foreach ($specs_arr as $specs_row) {
						echo '<tr>';
						echo '<td><input type="checkbox" name="spec'.$specs_row['id'].'" value="'.$specs_row['id'].'"/></td>';
						echo '<td>'.$specs_row['speciality_name'].((!empty($specs_row['profil_name'])) ? ' '.$specs_row['profil_name'] : '').'</td>';
						echo '<td>'.$specs_row['finance_name'].'</td>';
						echo '<td>'.$specs_row['eduform_name'].'</td>';
						echo '<td>'.$specs_row['edulevel_name'].'</td>';
						echo '</tr>';
					}
				}
			?>
			</table>
		</div>
		<div class="form-group fixed-bottom bg-primary text-center">
			<div class="col">
				<p></p>
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Выбрать');
					echo HTML_Helper::setHrefButton('ApplicationSpec', 'CancelPlaces/?id='.$data['pid'], 'btn btn-warning', 'Отмена');
				?>
				<p></p>
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
		$('#filters_flag').prop('checked', false);
		$('#filters').hide();
	}

	// form events
	function formEvents()
	{
		// filters
		$('#filters_flag').change(function() {
			if ($('#filters_flag').prop('checked')) {
				$('#filters').show();
			} else {
				$('#filters').hide();
			}
		});
		// filter apply click
		$('#btn_filter_apply').click(function() {
			var filter_speciality, filter_finance, filter_eduform, filter_edulevel, table, tr, td, finance, eduform, edulevel, i, checkbox;
			filter_speciality = $('#filter_speciality').val().toUpperCase();
			filter_finance = $('#filter_finance').val().toUpperCase();
			filter_eduform = $('#filter_eduform').val().toUpperCase();
			filter_edulevel = $('#filter_edulevel').val().toUpperCase();
			table = document.getElementById('table_specs');
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[0];
				speciality = tr[i].getElementsByTagName("td")[1];
				finance = tr[i].getElementsByTagName("td")[2];
				eduform = tr[i].getElementsByTagName("td")[3];
				edulevel = tr[i].getElementsByTagName("td")[4];
				checkbox  = $(td).find(":checkbox");
				$(checkbox).prop('checked', false);
				if (td) {
					// use filters
					if (filter_speciality != '' || filter_finance != '' || filter_eduform != '' || filter_edulevel != '') {
						if (speciality.textContent.toUpperCase().indexOf(filter_speciality) == 0 && finance.textContent.toUpperCase().indexOf(filter_finance) == 0 && eduform.textContent.toUpperCase().indexOf(filter_eduform) == 0 && edulevel.textContent.toUpperCase().indexOf(filter_edulevel) == 0) {
							tr[i].style.display = "";
					    } else {
							tr[i].style.display = "none";
					    }
					} else {
						tr[i].style.display = "";
					}
				}
			}
		});
		// filter cancel click
		$('#btn_filter_cancel').click(function() {
			$('#filter_speciality').val('');
			$('#filter_finance').val('');
			$('#filter_eduform').val('');
			$('#filter_edulevel').val('');
			var table, tr, i;
			table = document.getElementById('table_specs');
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				tr[i].style.display = "";
			}
		});
	}
</script>
