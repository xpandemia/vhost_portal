				<?php

use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
				?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5" id="docs_educ">
	<div class="row">
		<div class="page_name">
			<h2>Документы об образовании</h2>
		</div>
	</div>
	<div class="row">
			<div class="col text-left">
				
				<?php
				/*echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную');*/
				echo HTML_Helper::setUrlHrefButtonIcon('https://vk.com/video-102554211_456239634?list=602ef8419218210a7e', 'btn btn-primary', 'fab fa-youtube', 'Видеоинструкция', true);
				echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
				echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
				?>
				<button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpDocsEduc">Инструкция</button>
			</div>
	</div>
	<hr>
	<div class="row">
			<div class="col text-left">
				<?php
	echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_DocsEduc',
								'model_method' => 'getByUserGrid',
								'model_filter' => 'id_user',
								'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
								'grid' => 'grid',
								'controller' => DOCS_EDUC['ctr'],
								'action_add' => 'Add',
								'action_edit' => 'Edit',
								'action_delete' => 'DeleteConfirm',
								'home_hdr' => 'Документы об образовании']);
				?>
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
