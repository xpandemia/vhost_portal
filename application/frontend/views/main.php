<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Ege as Ege;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_Application as Application;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="row">
	<div class="col text-primary"><h3>Добро пожаловать!</h3></div>
</div>
<div class="row">
	<div class="col">
		<strong>Для получения дополнительной информации Вы можете обратиться в Приемную комиссию:</strong><br>
		E-mail: <a href="mailto:Exam@bsu.edu.ru"><strong>Exam@bsu.edu.ru</strong></a><br>
		Тел: (4722) 30-18-80, 30-18-90<br>
		Время работы: с 9:00 до 18:00, перерыв с 13:00 до 14:00
	</div>
</div>
<?php
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
	echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
?>
<div class="row">
	<div class="col text-center text-primary">
		<h3>Схема работы (* - обязательные шаги)</h3>
	</div>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpResume">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', RESUME['ctr']); ?>">Шаг 1*: Анкета <i class="fas fa-id-card"></i></a>
		</h4>
	</div>
	<?php
		$resume = new Resume();
		$resume->id_user = $_SESSION[APP_CODE]['user_id'];
		$resume_row = $resume->checkByUser();
		if ($resume_row && $resume_row['status'] != $resume::STATUS_CREATED) {
			stepSuccess();
		} else {
			stepError(1);
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpDocsEduc">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', DOCS_EDUC['ctr']); ?>">Шаг 2*: Документы об образовании <i class="fas fa-graduation-cap"></i></a>
		</h4>
	</div>
	<?php
		$docs = new DocsEduc();
		$docs->id_user = $_SESSION[APP_CODE]['user_id'];
		$docs_arr = $docs->getByUser();
		if ($docs_arr) {
			stepSuccess('документов', count($docs_arr));
		} else {
			stepError(1);
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpEge">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', EGE['ctr']); ?>">Шаг 3: Результаты ЕГЭ <i class="fas fa-table"></i></a>
		</h4>
	</div>
	<?php
		$ege = new Ege();
		$ege->id_user = $_SESSION[APP_CODE]['user_id'];
		$ege_arr = $ege->getByUser();
		if ($ege_arr) {
			stepSuccess('результатов ЕГЭ', count($ege_arr));
		} else {
			stepError(0);
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', IND_ACHIEVS['ctr']); ?>">Шаг 4: Индивидуальные достижения <i class="fas fa-trophy"></i></a>
		</h4>
	</div>
	<?php
		$ia = new IndAchievs();
		$ia->id_user = $_SESSION[APP_CODE]['user_id'];
		$ia_arr = $ia->getByUser();
		if ($ia_arr) {
			stepSuccess('индивидуальных достижений', count($ia_arr));
		} else {
			stepError(0);
		}
	?>
</div>
<div class="row">
	<?php
		if ($resume_row && $resume_row['app'] == 1 && $resume_row['status'] != $resume::STATUS_CREATED && $resume_row['status'] != $resume::STATUS_REJECTED && $docs_arr) {
			echo '<div class="col col-sm-1"></div>';
			echo '<div class="col col-sm-10 text-center alert alert-success"><h5>Подача заявлений разрешена</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		} else {
			echo '<div class="col col-sm-1"></div>';
			echo '<div class="col col-sm-10 text-center alert alert-danger"><h5>Подача заявлений запрещена</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		}
	?>
</div>
<div class="row">
	<div class="col col-sm-5 text-right text-primary">
		<h4>
			<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpApp">Инструкция</button>
			<a href="<?php echo Basic_Helper::appUrl('Main', APP['ctr']); ?>">Шаг 5*: Заявления <i class="fas fa-file-alt"></i></a>
		</h4>
	</div>
	<?php
		$app = new Application();
		$app->id_user = $_SESSION[APP_CODE]['user_id'];
		$app_arr = $app->getByUser();
		if ($app_arr) {
			stepSuccess('заявлений', count($app_arr));
		} else {
			stepError(1);
		}
	?>
</div>

<div class="modal fade" id="helpResume" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Анкета (инструкция)</h4>
			</div>
			<div class="modal-body text-justify">
				<p>
					<strong>Уважаемый абитуриент!</strong><br><br>
					Когда Вы видите свою анкету впервые, она пуста и имеет состояние <strong>"<?php echo $resume::STATUS_CREATED_NAME; ?>"</strong>. Это означает, что она готова для заполнения. Пожалуйста, аккуратно заполните нужные поля <strong>(* - означает, что поле обязательно для заполнения)</strong> и нажмите кнопку <strong>"Сохранить"</strong>. После этого система проверит введенные Вами данные и<br>
					<u>- если ошибок нет</u>, сохранит их для дальнейшего использования, анкета получит состояние <strong>"<?php echo $resume::STATUS_SAVED_NAME; ?>"</strong> и Вы увидите выделенное зелёным цветом сообщение "Анкета успешно сохранена."<br>
					<u>- если ошибки есть</u>, они отобразятся красным цветом, а состояние анкеты не изменится. Исправьте ошибки и снова нажмите <strong>"Сохранить"</strong>. Повторяйте до тех пор, пока не увидите выделенное зелёным цветом сообщение "Анкета успешно сохранена."<br><br>
					Анкету в состоянии <strong>"<?php echo $resume::STATUS_SAVED_NAME; ?>"</strong> можно исправлять без каких бы то ни было ограничений, но на её основании нельзя формировать заявления на поступление. Чтобы получить такую возможность, необходимо перевести анкету в состояние <strong>"<?php echo $resume::STATUS_SENDED_NAME; ?>"</strong>. Для этого нажмите кнопку <strong>"Отправить"</strong>, когда будете готовы двигаться дальше.<br><br>
					Анкеты в состояние <strong>"<?php echo $resume::STATUS_SENDED_NAME; ?>"</strong> исправлять уже нельзя, так как они попали на обработку к модератору. Если по каким-либо причинам исправления всё-таки надо внести, пожалуйста, свяжитесь с приёмной комиссией лично.<br><br>
					Кнопка <strong>"Очистить"</strong> используется лишь для обнуления данных анкеты, <strong>сохранения при этом не происходит!</strong> То есть, когда Вам, например, надо внести значительные изменения в анкету, целесообразно нажать кнопку <strong>"Очистить"</strong>, поработать с анкетой, как с чистым листом, и потом сохранить её.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpDocsEduc" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Документы об образовании (инструкция)</h4>
			</div>
			<div class="modal-body text-justify">
				<p>
					<strong>Уважаемый абитуриент!</strong><br><br>
					Для ввода сведений о документе об образовании нажмите <i class="far fa-file"></i><br><br>
					Чтобы внести изменения в ранее созданный документ об образовании, нажмите <i class="far fa-edit"></i> на нужном документе, внесите изменения и нажмите кнопку <strong>"Сохранить"</strong>.<br><br>
					Чтобы удалить ранее созданный документ об образовании, нажмите <i class="fas fa-times"></i> на нужном документе и подтвердите удаление.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpEge" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Результаты ЕГЭ (инструкция)</h4>
			</div>
			<div class="modal-body text-justify">
				<p>
					<strong>Уважаемый абитуриент!</strong><br><br>
					Если Вы не сдавали ЕГЭ, пропустите этот шаг.<br><br>
					Для ввода сведений о результатах ЕГЭ нажмите <i class="far fa-file"></i>, укажите год сдачи ЕГЭ и нажмите кнопку <strong>"Сохранить"</strong>.<br><br>
					Чтобы указать дисциплины и результаты ЕГЭ, нажмите <i class="far fa-edit"></i>, после перехода на страницу "Дисциплины ЕГЭ" нажмите <i class="far fa-file"></i>, выберите из списка предмет, который Вы сдавали, укажите результат в баллах и нажмите кнопку <strong>"Сохранить"</strong>.<br>
					Если Вы не можете указать результаты ЕГЭ в баллах (не знаете, не помните и т.д.), оставьте это поле пустым и нажмите кнопку <strong>"Сохранить"</strong>.<br><br>
					Если Вы имеете результаты ЕГЭ разных лет, то в систему необходимо внести их все, добавив год сдачи.<br><br>
					<strong>Вносить изменения в результаты ЕГЭ нельзя - ненужный или ошибочно введённый год сдачи можно только удалить.</strong><br><br>
					Чтобы удалить сведения о результатах ЕГЭ, нажмите <i class="fas fa-times"></i> и подтвердите удаление.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpIndAchievs" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Индивидуальные достижения (инструкция)</h4>
			</div>
			<div class="modal-body">
				<p>
					<strong>Уважаемый абитуриент!</strong><br><br>
					Если у Вас нет индивидуальных достижений, учитываемых при поступлении, пропустите этот шаг.<br><br>
					Для ввода сведений об индивидуальных достижениях (ИД) нажмите <i class="far fa-file"></i>, выберите вид индивидуального достижения, которое Вы хотите внести, из выпадающего списка, заполните реквизиты документа, подтверждающего ИД, загрузите его скан-копию и нажмите кнопку <strong>"Сохранить"</strong>.<br><br>
					Если у Вас несколько ИД, необходимо внести их все.<br><br>
					Чтобы внести изменения в ранее созданное индивидуальное достижение, нажмите <i class="far fa-edit"></i> на нужном достижении, внесите изменения и нажмите кнопку <strong>"Сохранить"</strong>.<br><br>
					Чтобы удалить ранее созданное индивидуальное достижение, нажмите <i class="fas fa-times"></i> на нужном достижении и подтвердите удаление.<br><br>
					Индивидуальные достижения, внесённые на этом этапе, автоматически отражаются в заявлении на поступление и в дальнейшем их изменить нельзя. Если внести изменения в ИД всё же необходимо, можно воспользоваться кнопкой <i class="fas fa-sync"></i> из заявления.<br><br>
					В заявлении отображаются ИД, соответствующие указанному уровню образования. Например, золотой знак ГТО будет учитываться при поступлении в бакалавриат и специалитет, но не будет учтён при поступлении в ординатуру и т.п.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpApp" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Заявления (инструкция)</h4>
			</div>
			<div class="modal-body">
				<p>
					<strong>Уважаемый абитуриент!</strong><br><br>
					Для подачи заявления нажмите <i class="far fa-file"></i>, заполните открывшиеся поля, выбрав из выдающего списка место поступления, приёмную кампанию и документ, на основании которого осуществляется поступление, и нажмите <strong>"Сохранить"</strong>.<br>
					На странице заявлений появится созданное заявление, которому присвоен порядковый номер и статус <strong>"<?php echo $app::STATUS_CREATED_NAME; ?>"</strong>. Внимательно проверьте внесённую информацию и, если всё верно, нажмите <i class="far fa-edit"></i>, чтобы внести направления подготовки (специальности).<br>
					Если данные введены ошибочно, удалите данное заявление нажатием <i class="fas fa-times"></i><br><br>
					Чтобы внести направления подготовки (специальности) в заявление:<br>
					- выберите направления подготовки (после выбора направлений, загрузится список вступительных испытаний);<br>
					- измените тип вступительных испытаний (при необходимости);<br>
					- обновите индивидуальные достижения (при необходимости);<br>
					- проверьте флажки дополнительной информации;<br>
					- распечатайте заявление, нажав <i class="fas fa-print"></i>;<br>
					- подпишите распечатанное заявление;<br>
					- отсканируйте/сфотографируйте подписанное заявление (очень высокое качество не нужно - достаточно, чтобы текст был хорошо различим);<br>
					- загрузите отсканированное и подписанное заявление и другие скан-копии;<br>
					- нажмите <strong>"Сохранить"</strong>.<br>
					Статус заявления изменится с <strong>"<?php echo $app::STATUS_CREATED_NAME; ?>"</strong> на <strong>"<?php echo $app::STATUS_SAVED_NAME; ?>"</strong>.<br>
					<u>Теперь заявление готово к отправке в приёмную комиссию!</u><br><br>
					Перед отправкой заявления в приёмную комиссию внимательно проверьте его. Если нужно, внесите изменения. Сохраните заявление.  Для отправки заявления на рассмотрение приёмной комиссии нажмите <strong>"Отправить"</strong>. При этом статус заявления изменится с <strong>"<?php echo $app::STATUS_SAVED_NAME; ?>"</strong> на <strong>"<?php echo $app::STATUS_SENDED_NAME; ?>"</strong>.<br><br>
					После рассмотрения заявления приемной комиссией статус заявления изменится с <strong>"<?php echo $app::STATUS_SENDED_NAME; ?>"</strong> на <strong>"<?php echo $app::STATUS_APPROVED_NAME; ?>"</strong> или <strong>"<?php echo $app::STATUS_REJECTED_NAME; ?>"</strong>.<br>
					Если Ваше заявление отклонено, Вы можете узнать причины отклонения, нажав на <i class="far fa-edit"></i>. После этого Вы можете попробовать подать заявление ещё раз.<br><br>
					<strong>ИЗМЕНЕНИЕ ЗАЯВЛЕНИЯ</strong><br>
					Для того, чтобы внести изменения в принятое или отклонённое заявление, необходимо открыть его, нажав на <i class="far fa-edit"></i>, и нажать кнопку <strong>"Изменить"</strong>. После внесения необходимых изменений, распечатайте заявление, подпишите, отсканируйте/сфотографируйте, загрузите его в специально предназначенные для этого поля, сохраните и отправьте в приёмную комиссию.<br><br>
					<strong>ОТЗЫВ ЗАЯВЛЕНИЯ</strong><br>
					Если Вы хотите отозвать принятое заявление, откройте его, нажав на <i class="far fa-edit"></i>, нажмите кнопку <strong>"Отозвать"</strong>. Распечатайте заявление на отзыв, подпишите, отсканируйте/сфотографируйте, загрузите его в специально предназначенные для этого поля, сохраните и отправьте в приёмную комиссию.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<?php
	function stepSuccess($count_msg = null, $count = null)
	{
		echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
		if (empty($count_msg) || empty($count)) {
			echo '<div class="col col-sm-3"></div>';
			echo '<div class="col col-sm-1"></div>';
		} else {
			echo '<div class="col col-sm-3 alert alert-info"><h5>Кол-во '.$count_msg.' - '.$count.'</h5></div>';
			echo '<div class="col col-sm-1"></div>';
		}
	}
	function stepError($required = 0)
	{
		echo '<div class="col col-sm-3 alert alert-'.(($required == 1) ? 'danger' : 'warning').'"><h5>Состояние шага - не пройден</h5></div>';
		echo '<div class="col col-sm-3"></div>';
		echo '<div class="col col-sm-1"></div>';
	}
?>
