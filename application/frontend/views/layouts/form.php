<?php
	use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
	$basic_helper = new Basic_Helper;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title><?php echo $title ?></title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $basic_helper->baseUrl('/vendors/bootstrap/css/bootstrap.min.css'); ?>">
	<!-- jQuery library -->
	<script src="<?php echo $basic_helper->baseUrl('/vendors/jquery/jquery.min.js'); ?>"></script>
	<!-- JavaScript -->
    <script src="<?php echo $basic_helper->baseUrl('/vendors/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <!-- Font Awesome -->
	<script defer src="<?php echo $basic_helper->baseUrl('/vendors/fontawesome/fontawesome-all.js'); ?>"></script>
</head>
<body class="bg-primary">
	<?php include ROOT_DIR.'/application/frontend/views/'.$content_view; ?>
</body>
</html>
