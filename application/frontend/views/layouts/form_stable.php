<?php
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
?>
<!DOCTYPE html>
<html lang="RU-ru">
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
<link rel="stylesheet" href="/application/frontend/mod.css">
	<style>
		#loadImg{position:absolute; z-index:1000; display:none}
	</style>

	<style>
		div.sticky_top {
		    position: -webkit-sticky;
		    position: sticky;
		    top: 0;
		    background-color: #007bff;
		    padding: 15px;
		    font-size: 20px;
		}
	</style>
</head>
<body class="bg-primary">

	<div class="container-fluid" style="margin-bottom:80px">
		<?php include ROOT_DIR.'/application/frontend/views/'.$content_view; ?>
	</div>

	<img id="loadImg" src="/images/big_roller.gif"/>

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
