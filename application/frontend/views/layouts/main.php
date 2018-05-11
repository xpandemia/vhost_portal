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
	<link rel="stylesheet" href="<?php echo Basic_Helper::baseUrl('/vendors/bootstrap/css/bootstrap.min.css'); ?>">
	<!-- jQuery -->
	<script src="<?php echo Basic_Helper::baseUrl('/vendors/jquery/jquery.min.js'); ?>"></script>
	<!-- JQuery Masked Input -->
	<script src="<?php echo Basic_Helper::baseUrl('/vendors/maskedinput/jquery.maskedinput.min.js'); ?>"></script>
	<!-- Popper JS -->
	<script src="<?php echo Basic_Helper::baseUrl('/vendors/popper/popper.min.js'); ?>"></script>
	<!-- Bootstrap JS -->
    <script src="<?php echo Basic_Helper::baseUrl('/vendors/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<!-- Font Awesome -->
	<script defer src="<?php echo Basic_Helper::baseUrl('/vendors/fontawesome/fontawesome-all.js'); ?>"></script>
</head>
<body>
	<nav class="navbar navbar-expand-md bg-primary navbar-dark fixed-top">
	  <a class="navbar-brand" href="<?php echo Basic_Helper::appUrl('Main', 'Index'); ?>"><?php echo APP_NAME." ".APP_VERSION; ?></a>
	  <div class="collapse navbar-collapse" id="collapsibleNavbar">
		  <ul class="navbar-nav">
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

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
