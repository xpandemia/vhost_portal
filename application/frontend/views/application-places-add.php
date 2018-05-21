<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use common\models\Model_Application as Application;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;

	// check data
	if (!isset($data)) {
		Basic_Helper::alertGlobal('Ошибка выбора направлений подготовки! Свяжитесь с администратором.');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<form enctype="multipart/form-data" action="<?php echo Basic_Helper::appUrl('ApplicationSpec', 'SavePlaces'); ?>" method="post" id="form_app_places" novalidate>
		<div class="sticky">
			<legend class="font-weight-bold">Выбор направлений подготовки</legend>
			<?php
				echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
				echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Выбрать');
				echo HTML_Helper::setHrefButton('Application', 'Edit/?id='.$data['pid'], 'btn btn-warning', 'Отмена');
			?>
		</div>
		<div class="form-group">
			<input type="hidden" id="pid" name="pid" value="<?php echo $data['pid']; ?>"/>
		</div>
		<div class="form-group">
			<table class="table table-bordered table-hover">
				<thead class="thead-dark">
					<tr>
						<th></th>
						<th>Специальность</th>
						<th>Тип оплаты</th>
						<th>Форма обучения</th>
						<th>Уровень обучения</th>
					</tr>
			    </thead>
			<?php
				$app = new Application();
				$app->id = $data['pid'];
				$app_row = $app->get();
				$specs = new ApplicationPlaces();
				$specs->pid = $data['pid'];
				$docs = new DocsEduc();
				$docs->id = $app_row['id_docseduc'];
				$docs_row = $docs->get();
				if (in_array($docs_row['doc_type'], $docs::HIGH_BEFORE) || $app->checkMagistratureFirst() || $app->checkHighAfter()) {
					$specs_arr = $specs->getSpecsFirstForApp();
				} else {
					$specs_arr = $specs->getSpecsSecondForApp();
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
	</form>
</div>
