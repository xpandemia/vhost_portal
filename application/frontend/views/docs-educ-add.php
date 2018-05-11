<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DOCS_EDUC['ctr'], DOCS_EDUC['act'], DOCS_EDUC['id'], DOCS_EDUC['hdr']);
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
		formEvents();
	});
</script>

<script>
	// form events
	function formEvents() {
		// educ_type change
		$('#educ_type').change(function() {
			var educ_type = $('#educ_type').val();
			if (educ_type == '') {
				$('#doc_type').empty();
			} else {
				getDiplomaAJAX('/frontend/DictDoctypes/DiplomasByEducCodeJSON', educ_type, '#doc_type');
			}
		});
	}

	function getDiplomaAJAX(url, code, select)
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
	        $.each(result, function(key, value){
	            $(select).append('<option value="' + value.code + '">' + value.description + '</option>');
	        });
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

<script>
	$(function(){
	  $("#dt_issue").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#end_year").mask("9999");
	});
</script>
