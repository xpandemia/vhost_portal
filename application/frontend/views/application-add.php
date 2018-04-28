<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(APP['ctr'], APP['act'], APP['id'], APP['hdr']);
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
	?>
	<!-- controls -->
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(APP['ctr'], 'Reset', 'btn btn-danger', 'Очистить');
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
