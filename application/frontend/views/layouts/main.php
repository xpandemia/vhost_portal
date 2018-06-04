<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title><?php echo $title ?></title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="/vendors/bootstrap/css/bootstrap.min.css">
	<!-- jQuery -->
	<script src="/vendors/jquery/jquery.min.js"></script>
	<!-- JQuery Masked Input -->
	<script src="/vendors/maskedinput/jquery.maskedinput.min.js"></script>
	<!-- Popper JS -->
	<script src="/vendors/popper/popper.min.js"></script>
	<!-- Bootstrap JS -->
    <script src="/vendors/bootstrap/js/bootstrap.min.js"></script>
	<!-- Font Awesome -->
	<script defer src="/vendors/fontawesome/fontawesome-all.min.js"></script>
	<link rel="stylesheet" href="/vendors/fontawesome/fontawesome-all.css">
</head>
<body>
	<nav class="navbar navbar-expand-md bg-primary navbar-dark fixed-top">
		<div class="navbar-brand"><?php echo APP_NAME; ?></div>
		<div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="На главную" href="<?php echo Basic_Helper::appUrl('Main', 'Index'); ?>"><i class="fas fa-sign-out-alt fa-2x" data-fa-transform="rotate-180"></i></a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="<?php echo RESUME['hdr']; ?>" href="<?php echo Basic_Helper::appUrl('Main', RESUME['ctr']); ?>"><i class="fas fa-id-card fa-2x"></i></a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="Документы об образовании" href="<?php echo Basic_Helper::appUrl('Main', DOCS_EDUC['ctr']); ?>"><i class="fas fa-graduation-cap fa-2x"></i></a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="<?php echo EGE['hdr']; ?>" href="<?php echo Basic_Helper::appUrl('Main', EGE['ctr']); ?>"><i class="fas fa-table fa-2x"></i></a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="Индивидуальные достижения" href="<?php echo Basic_Helper::appUrl('Main', IND_ACHIEVS['ctr']); ?>"><i class="fas fa-trophy fa-2x"></i></a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="Заявления" href="<?php echo Basic_Helper::appUrl('Main', APP['ctr']); ?>"><i class="fas fa-file-alt fa-2x"></i></a>
			    </li>
				<?php if (isset($_SESSION[APP_CODE]['user_name'])) { ?>
			    <li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="Выход" href="<?php echo Basic_Helper::appUrl('Main', 'Logout'); ?>"><i class="fas fa-sign-out-alt fa-2x"></i></a>
			    </li>
				<?php } ?>
			</ul>
		</div>
	</nav>

	<div class="container-fluid" style="margin-top:80px">
		<?php include ROOT_DIR.'/application/frontend/views/'.$content_view; ?>
	</div>
	<footer>
		<div class="row fixed-bottom font-weight-bold">
			<div class="col text-left">
				<?php echo nl2br("Версия ".APP_VERSION."\nРазработчик: ".APP_DEVELOPER."\nТелефон: ".APP_DEVELOPER_PHONE.", эл. почта: <a href=\"mailto:".APP_DEVELOPER_EMAIL."\">".APP_DEVELOPER_EMAIL."</a>"); ?>
			</div>
			<div class="col text-right">
				<a href="http://abitur.bsu.edu.ru/abitur/help/contacts/">Контакты Приёмной комиссии</a>
			</div>
		</div>
	</footer>
	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
