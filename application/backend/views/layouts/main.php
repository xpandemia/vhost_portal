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

	<style>
		.sticky_top {
		    position: -webkit-sticky;
		    position: sticky;
		    top: 7%;
		    padding-left: 2.5%;
		    padding-top: 1.5%;
		    padding-right: 2.5%;
		    padding-bottom: 1.5%;
		}
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
<body class="bg-secondary">
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
		<a class="navbar-brand" href="/<?php echo BEHAVIOR; ?>/Main/Index"><?php echo APP_NAME; ?></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="mainNavbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', USER['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="<?php echo USER['hdr']; ?>"><i class="fas fa-users fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', DICT_MANAGER['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="<?php echo DICT_MANAGER['hdr']; ?>"><i class="fas fa-database fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', DICT_COUNTRIES['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Страны мира"><i class="fas fa-globe fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', DOCS['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Документы"><i class="fas fa-file-alt fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', DICT_SCANS['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Скан-копии"><i class="fas fa-file-image fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', DICT_UNIVERSITY['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Места поступления"><i class="fas fa-university fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', DICT_EGE['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Дисциплины ЕГЭ"><i class="fas fa-table fa-2x"></i></span>
					</a>
			    </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', FEATURES['ctr']); ?>">
                        <span data-toggle="tooltip" data-placement="auto" title="Отличительные признаки"><i class="fab fa-creative-commons-by fa-2x"></i></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', TARGET_QUOTA['ctr']); ?>">
                        <span data-toggle="tooltip" data-placement="auto" title="Целевая квота"><i class="fa fa-address-book fa-2x"></i></span>
                    </a>
                </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', EDUCLEVELS_DOCTYPES['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Настройка связи уровней подготовки с типами документов"><i class="fas fa-graduation-cap fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item">
					<a class="nav-link" href="<?php echo Basic_Helper::appUrl('Main', EDUCTYPES_DOCTYPES['ctr']); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Настройка связи видов образования с типами документов"><i class="fas fa-school fa-2x"></i></span>
					</a>
			    </li>
			    <li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
						<span data-toggle="tooltip" data-placement="auto" title="Интернационализация"><i class="fas fa-language fa-2x"></i></span>
					</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?php echo Basic_Helper::appUrl('Main', LANGS['ctr']); ?>">Языки</a>
					</div>
			    </li>
			    <?php if (isset($_SESSION[APP_CODE]['user_id'])) { ?>
			    <li class="nav-item">
					<a class="nav-link" data-toggle="tooltip" title="Выход" href="<?php echo Basic_Helper::appUrl('Main', 'Logout'); ?>">
						<span data-toggle="tooltip" data-placement="auto" title="Выход"><i class="fas fa-sign-out-alt fa-2x"></i></span>
					</a>
			    </li>
			    <?php } ?>
			</ul>
		</div>
	</nav>

	<div class="container-fluid" style="margin-top:80px; margin-bottom:80px">
		<?php include ROOT_DIR.'/application/backend/views/'.$content_view; ?>
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
