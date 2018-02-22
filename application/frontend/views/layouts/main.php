<?php
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

$basic_helper = new Basic_Helper();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title><?php echo $title ?></title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $basic_helper->baseUrl('/vendors/bootstrap/css/bootstrap.min.css'); ?>">
	<!-- jQuery -->
	<script src="<?php echo $basic_helper->baseUrl('/vendors/jquery/jquery.min.js'); ?>"></script>
	<!-- JQuery Masked Input -->
	<script src="<?php echo $basic_helper->baseUrl('/vendors/maskedinput/jquery.maskedinput.min.js'); ?>"></script>
	<!-- Popper JS -->
	<script src="<?php echo $basic_helper->baseUrl('/vendors/popper/popper.min.js'); ?>"></script>
	<!-- Bootstrap JS -->
    <script src="<?php echo $basic_helper->baseUrl('/vendors/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<!-- Font Awesome -->
	<script defer src="<?php echo $basic_helper->baseUrl('/vendors/fontawesome/fontawesome-all.js'); ?>"></script>
</head>
<body class="bg-primary">
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
	  <a class="navbar-brand" href="/<?php echo BEHAVIOR; ?>/Main/Index"><?php echo APP_NAME." ".APP_VERSION; ?></a>
	  <ul class="navbar-nav">
	    <li class="nav-item">
	      <a class="nav-link" data-toggle="tooltip" title="<?php echo RESUME_HDR; ?>" href="/<?php echo BEHAVIOR; ?>/Main/Resume"><i class="fas fa-id-card fa-2x"></i></a>
	    </li>
	    <?php if (isset($_SESSION['user']['id'])) { ?>
	    <li class="nav-item">
	      <a class="nav-link" data-toggle="tooltip" title="Выход" href="/<?php echo BEHAVIOR; ?>/Main/Logout"><i class="fas fa-sign-out-alt fa-2x"></i></a>
	    </li>
	    <?php } ?>
	  </ul>
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
