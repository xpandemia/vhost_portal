<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка добавления документа об образовании!\nСвяжитесь с администратором."));
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DOCS_EDUC['ctr'], DOCS_EDUC['act'], DOCS_EDUC['id'], DOCS_EDUC['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
		<input type="hidden" id="doc_type_hidden" name="doc_type_hidden" value="<?php echo $data['doc_type']; ?>"/>
	</div>
	<?php
		// educ type
		echo Form_Helper::setFormSelectListDB(['label' => 'Вид образования',
												'control' => 'educ_type',
												'class' => $data['educ_type_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictEductypes',
												'model_method' => 'getEducs',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['educ_type'],
												'success' => $data['educ_type_scs'],
												'error' => $data['educ_type_err']]);
		// doc type
		echo Form_Helper::setFormSelectListDB(['label' => 'Тип документа',
												'control' => 'doc_type',
												'class' => $data['doc_type_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictDoctypes',
												'model_method' => ((!empty($data['educ_type'])) ? 'getDiplomasByEducCode' : null),
												'model_field' => 'code',
												'model_field_name' => 'description',
												'model_filter' => 'code_educ',
												'model_filter_val' => $data['educ_type'],
												'value' => $data['doc_type'],
												'success' => $data['doc_type_scs'],
												'error' => $data['doc_type_err']]);
		// series
		echo Form_Helper::setFormInput(['label' => 'Серия',
										'control' => 'series',
										'type' => 'text',
										'class' => $data['series_cls'],
										'required' => 'no',
										'value' => $data['series'],
										'success' => $data['series_scs'],
										'error' => $data['series_err']]);
		// numb
		echo Form_Helper::setFormInput(['label' => 'Номер',
										'control' => 'numb',
										'type' => 'text',
										'class' => $data['numb_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['numb'],
										'success' => $data['numb_scs'],
										'error' => $data['numb_err']]);
		// school
		echo Form_Helper::setFormInput(['label' => 'Наименование учебного заведения',
										'control' => 'school',
										'type' => 'text',
										'class' => $data['school_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['school'],
										'success' => $data['school_scs'],
										'error' => $data['school_err']]);
		// dt_issue
		echo Form_Helper::setFormInput(['label' => 'Дата выдачи',
										'control' => 'dt_issue',
										'type' => 'text',
										'class' => $data['dt_issue_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['dt_issue'],
										'success' => $data['dt_issue_scs'],
										'error' => $data['dt_issue_err']]);
		// end_year
		echo Form_Helper::setFormInput(['label' => 'Год окончания',
										'control' => 'end_year',
										'type' => 'text',
										'class' => $data['end_year_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['end_year'],
										'success' => $data['end_year_scs'],
										'error' => $data['end_year_err']]);
		// educ form
		echo Form_Helper::setFormSelectListDB(['label' => 'Форма обучения',
												'control' => 'educ_form',
												'class' => $data['educ_form_cls'],
												'required' => 'no',
												'model_class' => 'common\\models\\Model_DictEducforms',
												'model_method' => 'getAll',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['educ_form'],
												'success' => $data['educ_form_scs'],
												'error' => $data['educ_form_err']]);
		// speciality
		echo Form_Helper::setFormInput(['label' => 'Специальность по диплому',
										'control' => 'speciality',
										'type' => 'text',
										'class' => $data['speciality_cls'],
										'required' => 'no',
										'value' => $data['speciality'],
										'success' => $data['speciality_scs'],
										'error' => $data['speciality_err']]);
		/* change_name */
		echo Form_Helper::setFormCheckbox(['label' => 'На момент получения документа об образовании мои фамилия, имя или отчество были другими',
												'control' => 'change_name_flag',
												'class' => $data['change_name_flag_cls'],
												'value' => $data['change_name_flag'],
												'success' => $data['change_name_flag_scs'],
												'error' => $data['change_name_flag_err']]);
		echo '<br>';
		echo Form_Helper::setFormFile(['label' => 'Свидетельство о перемене имени',
										'control' => 'change_name',
										'required' => 'yes',
										'required_style' => 'StarUp',
										'data' => $data,
										'home_id' => (isset($data['id'])) ? $data['id'] : null,
										'home_ctr' => DOCS_EDUC['ctr'],
										'home_hdr' => DOCS_EDUC['hdr'],
										'home_act' => 'Edit',
										'ext' => FILES_EXT_SCANS]);
		/* scans */
		echo Form_Helper::setFormHeaderSub('Скан-копии');
		echo Form_Helper::setFormFileListDB(['required' => 'required',
											'required_style' => 'StarUp',
											'model_class' => 'common\\models\\Model_DictScans',
											'model_method' => 'getByDocument',
											'model_filter' => 'doc_code',
											'model_filter_var' => 'docs_educ',
											'model_field' => 'scan_code',
											'model_field_name' => 'scan_name',
											'data' => $data,
											'home_id' => (isset($data['id'])) ? $data['id'] : null,
											'home_ctr' => DOCS_EDUC['ctr'],
											'home_hdr' => DOCS_EDUC['hdr'],
											'home_act' => 'Edit',
											'ext' => FILES_EXT_SCANS]);
	?>
	<!-- controls -->
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DOCS_EDUC['ctr'], 'Reset', 'btn btn-danger', 'Очистить');
				echo HTML_Helper::setHrefButton(DOCS_EDUC['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>

<script>
	$(document).ready(function(){
		formInit();
		formEvents();
	});
</script>

<script>
	// form init
	function formInit() {
		// education type
		setEductype();
		// change name flag
		if ($('#change_name_flag').prop('checked')) {
			$('#change_name_div').show();
		} else {
			$('#change_name_div').hide();
		}
	}
</script>

<script>
	// form events
	function formEvents() {
		// education type change
		$('#educ_type').change(function() {
			setEductype();
		});
		// change name flag change
		$('#change_name_flag').change(function() {
			if ($('#change_name_flag').prop('checked')) {
				$('#change_name_div').show();
			} else {
				$('#change_name_div').hide();
			}
		});
	}

	function setEductype()
	{
		var eductypes_diploma = ['000000001', '000000003', '000000004', '000000006'];
		var educ_type = $('#educ_type').val();
		if (educ_type == '') {
			$('#doc_type').empty();
			$("label[for='educ_form']").html('Форма обучения');
			$('#educ_form_div').hide();
			$("label[for='speciality']").html('Специальность по диплому');
			$('#speciality_div').hide();
		} else {
			getDiplomaAJAX('/frontend/DictDoctypes/DiplomasByEducCodeJSON', educ_type, '#doc_type', $('#doc_type_hidden').val());
			if (jQuery.inArray(educ_type, eductypes_diploma) == -1) {
				$("label[for='educ_form']").html('Форма обучения');
				$('#educ_form_div').hide();
				$("label[for='speciality']").html('Специальность по диплому');
				$('#speciality_div').hide();
			} else {
				$("label[for='educ_form']").html('Форма обучения*');
				$('#educ_form_div').show();
				$("label[for='speciality']").html('Специальность по диплому*');
				$('#speciality_div').show();
			}
		}
	}

	function getDiplomaAJAX(url, code, select, value_test)
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
		            if (value_test == value.code) {
						$(select).append('<option value="' + value.code + '" selected>' + value.description + '</option>');
					} else {
						$(select).append('<option value="' + value.code + '">' + value.description + '</option>');
					}
		        });
			}
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
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

<script>
	$(function(){
	  $("#dt_issue").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#end_year").mask("9999");
	});
</script>
