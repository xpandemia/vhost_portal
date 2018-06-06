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
	<div class="container-fluid">
		<div class="row">
			<div class="col col-sm-2 text-left">
				<img src="/images/logo_abitur.jpg" alt="Logo" style="width:100px;heigth:100px">
			</div>
			<div class="col col-sm-8 text-center">
				<p class="font-weight-bold">Белгородский государственный национальный исследовательский университет</p>
				<h1><?php echo APP_NAME; ?></h1>
			</div>
			<div class="col col-sm-2 text-right">
				<img src="/images/logo_bsu.jpg" alt="Logo" style="width:60px;heigth:90px">
			</div>
		</div>
	</div>

	<nav class="navbar navbar-expand-md bg-primary navbar-dark sticky-top">
		<div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link text-dark" data-toggle="tooltip" title="На главную" href="<?php echo Basic_Helper::appUrl('Main', 'Index'); ?>"><i class="fas fa-home fa-2x"></i></a>
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

	<div class="container-fluid" style="margin-bottom:80px">
		<?php include ROOT_DIR.'/application/frontend/views/'.$content_view; ?>
	</div>

	<div class="fixed-bottom" style="background:#d8d8d8">
		<div class="row">
			<div class="col text-center">
				<p>
					© <?php echo date('Y'); ?> <a href="https://www.bsu.edu.ru/" target="_blank">НИУ «БелГУ»</a>.<br>
					По вопросам электронной подачи документов для поступления обращаться в <a href="http://abitur.bsu.edu.ru/abitur/help/contacts/" target="_blank">Приемную комиссию</a>. E-mail: <a href="mailto:Abitur@bsu.edu.ru">Abitur@bsu.edu.ru</a><br>
					Разработка и техническая поддержка: <a href="https://www.bsu.edu.ru/bsu/structure/detail.php?ID=10247" target="_blank">управление информатизации НИУ «БелГУ»</a>. E-mail: <a href="mailto:WebmasterAbitur@bsu.edu.ru">WebmasterAbitur@bsu.edu.ru</a>
				</p>
			</div>
		</div>
    </div>

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
