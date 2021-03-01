<?php

use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5" id="ege">
	<div class="row">
		<div class="page_name">
			<h2>Результаты ЕГЭ</h2>
		</div>
	</div>
	<div class="row">	
		<div class="col">
			<?php
            echo HTML_Helper::setUrlHrefButtonIcon('https://vk.com/video-102554211_456239629?list=b18b34a8c207fc6858', 'btn btn-primary', 'fab fa-youtube', 'Видеоинструкция', true);
            ?>
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpEge">Инструкция</button>
			<?php 
			/*echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную'); */			
			echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
			echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
			echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
			echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
			echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\n<strong>Если Вы не сдавали ЕГЭ, пропустите этот шаг</strong>\nЧтобы добавить <strong>результаты ЕГЭ</strong>, нажмите кнопку «Добавить» \nЧтобы добавить/изменить<strong>дисциплины ЕГЭ</strong>, нажмите <i class=\"far fa-edit\"></i>. Для удаления используйте <i class=\"fa fa-times\" aria-hidden=\"true\"></i>."), 'alert-warning');
			?>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col">
			<?php
				echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_Ege',
									'model_method' => 'getByUserGrid',
									'model_filter' => 'id_user',
									'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
									'grid' => 'grid',
									'controller' => EGE['ctr'],
									'action_add' => 'Add',
									'action_edit' => 'Edit',
									'action_delete' => 'DeleteConfirm',
									'home_hdr' => EGE['hdr']]);
			?>
		
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
