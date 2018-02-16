<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title><?php echo $title ?></title>
	
	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo BASEPATH.'/vendors/bootstrap/css/bootstrap.min.css'; ?>">
	<!-- JavaScript -->
    <script src="<?php echo BASEPATH.'/vendors/bootstrap/js/bootstrap.min.js'; ?>"></script>
</head>
<body>
	<?php include ROOT_DIR.'/application/core/views/'.$content_view; ?>
</body>
</html>
