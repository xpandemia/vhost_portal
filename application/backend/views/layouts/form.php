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
	<?php include ROOT_DIR.'/application/backend/views/'.$content_view; ?>

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
