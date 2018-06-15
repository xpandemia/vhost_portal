<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;

	$resume = new Resume();
	$resume->id_user = $_SESSION[APP_CODE]['user_id'];
	$resume_row = $resume->checkByUser();
		$docs = new DocsEduc();
		$docs->id_user = $_SESSION[APP_CODE]['user_id'];
		$docs_arr = $docs->getByUser();
	if (!$resume_row || $resume_row['app'] == 0 || $resume_row['status'] == $resume::STATUS_CREATED || $resume_row['status'] == $resume::STATUS_REJECTED || !$docs_arr) {
		Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Вам не разрешена подача заявлений!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(APP['ctr'], APP['act'], APP['id'], APP['hdr'], 0, '/images/logo_bsu_transparent.gif');
		// university
		echo Form_Helper::setFormSelectListDB(['label' => 'Место поступления',
												'control' => 'university',
												'class' => $data['university_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictUniversity',
												'model_method' => 'getAll',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['university'],
												'success' => $data['university_scs'],
												'error' => $data['university_err']]);
		// campaign
		echo Form_Helper::setFormSelectListDB(['label' => 'Приёмная кампания',
												'control' => 'campaign',
												'class' => $data['campaign_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_AdmissionCampaign',
												'model_method' => ((!empty($data['university'])) ? 'getByUniversity' : null),
												'model_field' => 'code',
												'model_field_name' => 'description',
												'model_filter' => 'university',
												'model_filter_val' => $data['university'],
												'value' => $data['campaign'],
												'success' => $data['campaign_scs'],
												'error' => $data['campaign_err']]);
		// education docs
		echo Form_Helper::setFormSelectListDB(['label' => 'Документ об образовании',
												'control' => 'docs_educ',
												'class' => $data['docs_educ_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DocsEduc',
												'model_method' => 'getByUserSl',
												'model_field' => 'id',
												'model_field_name' => 'description',
												'model_filter' => 'id_user',
												'model_filter_val' => $_SESSION[APP_CODE]['user_id'],
												'value' => $data['docs_educ'],
												'success' => $data['docs_educ_scs'],
												'error' => $data['docs_educ_err']]);
		// foreign language
		echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЗдесь указывается иностранный язык, который Вы будете <strong>продолжать</strong> изучать.\nЕсли позиция останется незаполненной, в заявлении будет автоматически указан первый подходящий язык <strong>(английский, немецкий или французский)</strong> из Вашей анкеты или <strong>\"Не изучал(а)\"</strong>, если в анкете не указан ни один."), 'alert-warning');
		echo Form_Helper::setFormSelectListDB(['label' => 'Иностранный язык',
												'control' => 'foreign_lang',
												'class' => $data['foreign_lang_cls'],
												'required' => 'no',
												'model_class' => 'common\\models\\Model_ForeignLangs',
												'model_method' => 'getBsuByResumeList',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'model_filter' => 'id_resume',
												'model_filter_val' => $resume_row['id'],
												'value' => $data['foreign_lang'],
												'success' => $data['foreign_lang_scs'],
												'error' => $data['foreign_lang_err']]);
		/* additional info */
		echo Form_Helper::setFormHeaderSub('Дополнительная информация');
		// campus
		echo Form_Helper::setFormCheckbox(['label' => 'Нуждаюсь в общежитии',
											'control' => 'campus',
											'class' => $data['campus_cls'],
											'value' => $data['campus'],
											'success' => $data['campus_scs'],
											'error' => $data['campus_err']]);
		// conds
		echo Form_Helper::setFormCheckbox(['label' => 'Прошу создать специальные условия (например: присутствие ассистента, наличие звукоусиливающей аппаратуры)',
											'control' => 'conds',
											'class' => $data['conds_cls'],
											'value' => $data['conds'],
											'success' => $data['conds_scs'],
											'error' => $data['conds_err']]);
		// remote
		echo Form_Helper::setFormCheckbox(['label' => 'Прошу разрешить сдачу вступительных испытаний с использованием дистанционных технологий (только для поступающих на платную основу заочной формы обучения)',
											'control' => 'remote',
											'class' => $data['remote_cls'],
											'value' => $data['remote'],
											'success' => $data['remote_scs'],
											'error' => $data['remote_err']]);
	?>
	<!-- controls -->
	<br>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить', 'Сохраняет данные заявления');
				echo HTML_Helper::setHrefButton(APP['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Обнуляет форму ввода');
				echo HTML_Helper::setHrefButton(APP['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>

<script>
	$(document).ready(function(){
		formEvents();
	});
</script>

<script>
	// form events
	function formEvents() {
		// university change
		$('#university').change(function() {
			var university = $('#university').val();
			if (university == '') {
				$('#campaign').empty();
			} else {
				getCampaignAJAX('/frontend/AdmissionCampaign/AdmCampByUniversityJSON', university, '#campaign');
			}
		});
		// campaign change
		$('#campaign').change(function() {
			var campaign = $('#campaign').val();
			var campaign_name = $('#campaign :selected').text();
			if (campaign == '') {
				$('#docs_educ').empty();
			} else {
				getDocseducAJAX('/frontend/DocsEduc/DiplomasByUserCampaignJSON', campaign, '#docs_educ');
			}
		});
	}

	function getCampaignAJAX(url, code, select)
	{
		startLoadingAnimation();
		$.ajax({
	      url: url,
	      type: 'POST',
	      data: {format: 'json'},
		  dataType: 'json',
		  data: {code: code},
	      success: function(result) {
	        $(select).empty();
            $(select).append('<option></option>');
	        if (!jQuery.isEmptyObject(result)) {
		        $.each(result, function(key, value){
		            $(select).append('<option value="' + value.code + '">' + value.description + '</option>');
		        });
			}
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
	    $(select).val('');
	}

	function getDocseducAJAX(url, code, select)
	{
		startLoadingAnimation();
		$.ajax({
	      url: url,
	      type: 'POST',
	      data: {format: 'json'},
		  dataType: 'json',
		  data: {code: code},
	      success: function(result) {
	        $(select).empty();
            $(select).append('<option></option>');
	        if (!jQuery.isEmptyObject(result)) {
		        $.each(result, function(key, value){
		            $(select).append('<option value="' + value.id + '">' + value.description + '</option>');
		        });
			}
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
	    $(select).val('');
	}

	function startLoadingAnimation()
	{
	  var imgObj = $("#loadImg");
	  imgObj.show();

	  var centerY = $(window).scrollTop() + ($(window).height() + imgObj.height())/2;
	  var centerX = $(window).scrollLeft() + ($(window).width() + imgObj.width())/2;

	  imgObj.offset({ top:centerY, left:centerX });
	}

	function stopLoadingAnimation()
	{
	  $("#loadImg").hide();
	}
</script>
