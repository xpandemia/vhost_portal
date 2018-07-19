<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_User as User;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="container-fluid rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2><?php echo USER['hdr']; ?></h2>
	
	<table class="table table-bordered table-hover">
	<?php
		$user = new User();
		echo '<thead class="thead-dark">';
		echo '<tr>';
		foreach ($user->grid() as $key => $value) {
			echo '<th class="align-text-top">'.$value['name'].'</th>';
		}
		echo '</tr>';
		echo '</thead>';
		$user_arr = $user->getGrid();
		if ($user_arr) {
			foreach ($user_arr as $user_row) {
				echo '<tr>';
				echo '<td>'.$user_row['id'].'</td>';
				echo '<td>'.$user_row['username'].'</td>';
				echo '<td>'.$user_row['email'].'</td>';
				echo '<td>'.$user_row['role'].'</td>';
				echo '<td>'.$user_row['status'].'</td>';
				echo '<td>'.$user_row['dt_created'].'</td>';
				echo '<td>';
				echo HTML_Helper::setHrefButtonIcon(USER['ctr'], 'Mask/?id='.$user_row['id'], 'font-weight-bold', 'fas fa-user-secret fa-2x', 'Войти как');
				echo '</td>';
				echo '</tr>';
			}
		}
	?>
	</table>
</div>
