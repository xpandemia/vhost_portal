<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Docs as Docs;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<div class="rounded bg-dark text-light sticky_top">
		<div class="row">
			<div class="">
				<h2>Скан-копии</h2>
			</div>
			<?php
				echo '<div class="col col-sm-3">';
				echo '<select class="form-control" id="filter_docs" name="filter_docs">';
				echo '<option value="" selected></option>';
				$docs = new Docs();
				$docs_arr = $docs->getAll();
				if ($docs_arr) {
					foreach ($docs_arr as $docs_row) {
						echo '<option value="'.$docs_row['doc_name'].'">'.$docs_row['doc_name'].'</option>';
					}
				}
				echo '</select>';
				echo '</div>';
				echo '<div class="col col-sm-2">';
				echo HTML_Helper::setButton('btn btn-success', 'btn_filter_apply', 'Применить фильтр');
				echo '</div>';
				echo '<div class="col col-sm-2">';
				echo HTML_Helper::setButton('btn btn-warning', 'btn_filter_cancel', 'Отменить фильтр');
				echo '</div>';
			?>
		</div>
			<?php
				echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
				echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
			?>
	</div>
	<br>
	<?php
		echo HTML_Helper::setGridDB(['id' => 'table_scans',
									'model_class' => 'common\\models\\Model_DictScans',
									'model_method' => 'getGrid',
									'grid' => 'grid',
									'controller' => DICT_SCANS['ctr'],
									'action_add' => 'Add',
									'action_edit' => 'Edit',
									'action_delete' => 'DeleteConfirm',
									'home_hdr' => 'Скан-копии']);
	?>
</div>

<script>
	$(document).ready(function(){
		formEvents();
	});
</script>

<script>
	// form events
	function formEvents()
	{
		// filter apply click
		$('#btn_filter_apply').click(function() {
			var filter_docs, table, tr, td, docs, i, checkbox;
			filter_docs = $('#filter_docs').val().toUpperCase();
			table = document.getElementById('table_scans');
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[0];
				docs = tr[i].getElementsByTagName("td")[1];
				if (td) {
					// use filters
					if (filter_docs != '') {
						if (docs.textContent.toUpperCase().indexOf(filter_docs) == 0) {
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
			$('#filter_docs').val('');
			var table, tr, i;
			table = document.getElementById('table_scans');
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				tr[i].style.display = "";
			}
		});
	}
</script>
