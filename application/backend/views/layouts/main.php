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
<body class="bg-secondary">
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
		<a class="navbar-brand" href="/<?php echo BEHAVIOR; ?>/Main/Index"><?php echo APP_NAME." ".APP_VERSION; ?></a>
		<ul class="navbar-nav">
		    <li class="nav-item">
				<a class="nav-link" data-toggle="tooltip" title="<?php echo DICT_MANAGER['hdr']; ?>" href="<?php echo Basic_Helper::appUrl('Main', 'DictManager'); ?>"><i class="fas fa-database fa-2x"></i></a>
		    </li>
		    <li class="nav-item">
				<a class="nav-link" data-toggle="tooltip" title="Настройка связи уровней подготовки с типами документов" href="<?php echo Basic_Helper::appUrl('Main', EDUCLEVELS_DOCTYPES['ctr']); ?>"><i class="fas fa-graduation-cap fa-2x"></i></a>
		    </li>
		    <li class="nav-item">
				<a class="nav-link" data-toggle="tooltip" title="Настройка связи видов образования с типами документов" href="<?php echo Basic_Helper::appUrl('Main', EDUCTYPES_DOCTYPES['ctr']); ?>"><i class="fas fa-school fa-2x"></i></a>
		    </li>
		    <?php if (isset($_SESSION[APP_CODE]['user_id'])) { ?>
		    <li class="nav-item">
				<a class="nav-link" data-toggle="tooltip" title="Выход" href="<?php echo Basic_Helper::appUrl('Main', 'Logout'); ?>"><i class="fas fa-sign-out-alt fa-2x"></i></a>
		    </li>
		    <?php } ?>
		</ul>
	</nav>

	<div class="container-fluid" style="margin-top:80px">
		<?php include ROOT_DIR.'/application/backend/views/'.$content_view; ?>
	</div>

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
