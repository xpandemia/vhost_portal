<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Индивидуальные достижения</h2>
	<?php
	echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
	echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
	echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_IndAchievs',
								'model_method' => 'getByUserGrid',
								'model_filter' => 'id_user',
								'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
								'grid' => 'grid',
								'controller' => IND_ACHIEVS['ctr'],
								'action_add' => 'Add',
								'action_edit' => 'Edit',
								'action_delete' => 'DeleteConfirm',
								'home_hdr' => 'Индивидуальные достижения']);
	?>
</div>
