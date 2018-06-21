<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

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

	<style>
		.footer {
			position: fixed;
			left: 0;
		    bottom: 0;
		    width: 100%;
		    height: 8%;
			background: #d8d8d8;
			text-align: center;
	    }
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col col-sm-2 text-left">
				<a data-toggle="tooltip" title="Приёмная комиссия: абитуриент" href="http://abitur.bsu.edu.ru/" target="_blank">
					<img src="/images/logo_abitur.jpg" alt="LogoAbitur" style="width:100px;heigth:100px">
				</a>
			</div>
			<div class="col col-sm-8 text-center">
				<p class="font-weight-bold">Белгородский государственный национальный исследовательский университет</p>
				<h1><?php echo APP_NAME; ?></h1>
			</div>
			<div class="col col-sm-2 text-right">
				<a data-toggle="tooltip" title="НИУ БелГУ" href="https://www.bsu.edu.ru/" target="_blank">
					<img src="/images/logo_bsu.jpg" alt="LogoBsu" style="width:60px;heigth:90px">
				</a>
			</div>
		</div>
	</div>

	<nav class="navbar navbar-expand-md bg-primary navbar-dark sticky-top">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="mainNavbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', 'Index'); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="На главную"><i class="fas fa-home fa-2x"></i></span>
					</a>
			    </li>
                <li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', RESUME['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="<?php echo RESUME['hdr']; ?>"><i class="fas fa-id-card fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', DOCS_EDUC['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Документы об образовании"><i class="fas fa-graduation-cap fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', EGE['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="<?php echo EGE['hdr']; ?>"><i class="fas fa-table fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', IND_ACHIEVS['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Индивидуальные достижения"><i class="fas fa-trophy fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', APP['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Заявления"><i class="fas fa-file-alt fa-2x"></i></span>
					</a>
			    </li>
				<?php if (isset($_SESSION[APP_CODE]['user_name'])) { ?>
			    <li class="nav-item">
					<a class="nav-link text-white" href="<?php echo Basic_Helper::appUrl('Main', 'Logout'); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Выход"><i class="fas fa-sign-out-alt fa-2x"></i></span>
					</a>
			    </li>
				<?php } ?>
			</ul>
		</div>
	</nav>

	<noscript>
		<div class="container-fluid">
			<div class="row">
				<div class="col alert alert-danger">
					<strong>Внимание!</strong> Ваш браузер не поддерживает Java-скрипты. Пожалуйста, включите поддержку Java-скриптов или перейдите в другой браузер.
				</div>
			</div>
		</div>
	</noscript>

	<?php
		$browser = Basic_Helper::getBrowser();
		$browser['version'] = stristr($browser['version'], '.', true);
		$browser_act = Basic_Helper::checkBrowser($browser['name'], $browser['version']);
		if ($browser_act) {
	?>
	<div class="container-fluid">
		<div class="row">
			<?php
				if ($browser_act == 'install') {
			?>
			<div class="col alert alert-warning">
				<strong>Внимание!</strong> Вы используете устаревший браузер. Рекомендуем выбрать и установить один из современных:
			</div>
			<div class="col">
				<a data-toggle="tooltip" title="Google Chrome" href="http://www.google.com/chrome/" target="_blank">
					<img src="/images/chrome.png" alt="LogoChrome" style="width:60px;heigth:90px">
				</a>
				<a data-toggle="tooltip" title="Opera" href="http://www.opera.com/" target="_blank">
					<img src="/images/opera.png" alt="LogoOpera" style="width:60px;heigth:90px">
				</a>
				<a data-toggle="tooltip" title="Mozilla Firefox" href="http://www.mozilla-europe.org/" target="_blank">
					<img src="/images/firefox.png" alt="LogoFirefox" style="width:60px;heigth:90px">
				</a>
			</div>
			<?php } else { ?>
			<div class="col alert alert-warning">
				<strong>Внимание!</strong> Вы используете подходящий браузер, но его версия устарела. Рекомендуем обновить браузер.
			</div>
			<?php } ?>
		</div>
	</div>
	<?php
		}
	?>

	<div class="container-fluid" style="margin-bottom:80px">
		<?php include ROOT_DIR.'/application/frontend/views/'.$content_view; ?>
	</div>

	<footer class="footer">
		<p>
			© <?php echo date('Y'); ?> <a href="https://www.bsu.edu.ru/" target="_blank">НИУ «БелГУ»</a>.<br>
			По вопросам электронной подачи документов для поступления обращаться в <a href="http://abitur.bsu.edu.ru/abitur/help/contacts/" target="_blank">Приёмную комиссию</a>. E-mail: <a href="mailto:Abitur@bsu.edu.ru">Abitur@bsu.edu.ru</a><br>
			Разработка и техническая поддержка: <a href="https://www.bsu.edu.ru/bsu/structure/detail.php?ID=10247" target="_blank">управление информатизации НИУ «БелГУ»</a>. E-mail: <a href="mailto:WebmasterAbitur@bsu.edu.ru">WebmasterAbitur@bsu.edu.ru</a>
		</p>
	</footer>

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>
