<?php

use common\models\Model_DictScans;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationAchievs as Model_ApplicationAchievs;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_ApplicationPlacesExams as Model_ApplicationPlacesExams;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_DictForeignLangs as DictForeignLangs;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Personal as Personal;
use frontend\models\Model_Application as Model_Application;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

// check data
if (!isset($data)) {
    Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', NULL, nl2br("Ошибка направлений подготовки!\nСвяжитесь с администратором."));
}
// get application
$app = new Application();
$app->id = $data['id'];
$app_row = $app->get();
//check if app is active and belongs to authenticated user
if ($app_row['active'] < 1 || $app_row['id_user'] != $_SESSION[APP_CODE]['user_id']) {
    Basic_Helper::redirect(APP_NAME, 204, APP['ctr'], 'Index', NULL, nl2br("Заявление удалено или неактивно"));
}
// get education document
$docs = new DocsEduc();
$docs->id = $app_row['id_docseduc'];
$docs_row = $docs->getForField();
// get foreign language
$langs = new DictForeignLangs();
$langs->id = $app_row['id_lang'];
$langs_row = $langs->get();
// get citizenship
$personal = new Personal();
$citizenship = $personal->getCitizenshipByUser();
// manage scans
$place = new ApplicationPlaces();
$place->pid = $data['id'];
$exam = new ApplicationPlacesExams();
$exam->pid = $data['id'];
// application_2
if ($place->getByAppForSpecial()) {
    $application_2 = 0;
} else {
    $application_2 = 1;
}
// photo3x4
if ($place->getByAppForBachelorSpec() && $exam->existsExams()) {
    $photo3x4 = 1;
} else {
    $photo3x4 = 1;
}
// medical_certificate
if ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1()) {
    $medical_certificate = 1;
} else {
    $medical_certificate = 0;
}
// inila
if ($place->getByAppForClinical() && $citizenship['abroad'] == 0) {
    $inila = 1;
} else {
    $inila = 0;
}

$debug = FALSE;
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <h2>Заявление № <?php echo $app_row['numb']; ?></h2>
    <?php
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
    /* type */
    echo '<div class="row">';
    echo '<div class="col">';
    echo Model_Application::showType($app_row['type']);
    echo '</div>';
    /* status */
    echo '<div class="col">';
    echo Model_Application::showStatus($app_row['status']);
    echo '</div>';
    echo '</div>';
    /* comment */
    if ($app_row['status'] == $app::STATUS_REJECTED) {
        $applog = new ApplicationStatus();
        $applog->id_application = $app_row['id'];
        $applog_row = $applog->getLast();
        echo HTML_Helper::setAlert('Причины отклонения: <strong>' . $applog_row['comment'] . '</strong>', 'alert-danger');
    }
    /* education document */
    echo HTML_Helper::setAlert('Документ об образовании: <strong>' . $docs_row['docs_educ'] . '</strong>', 'alert-info');
    /* foreign language */
    echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЧтобы изменить изучаемый иностранный язык необходимо удалить текущее заявление и создать новое."),
        'alert-warning');
    echo HTML_Helper::setAlert('Изучаемый иностранный язык: <strong>' . $langs_row['description'] . '</strong>', 'alert-info');
    ?>
    <hr>
    <h5>Направления подготовки</h5><br>
    <?php
    echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЧтобы добавить/изменить <strong>направления подготовки</strong>, нажмите <i class=\"far fa-file\"></i>\n<strong>При изменении направлений подготовки загруженные до этого скан-копии удаляются!</strong>"),
        'alert-warning');
    if ($app_row['pay'] == 0) {
        $pay = 'Вы можете поступать и на бесплатной, и на платной основе обучения.';
    } else {
        $pay = 'Вы можете поступать <strong>только на платной</strong> основе обучения!';
    }

    echo HTML_Helper::setAlert($pay, 'alert-info');
    echo HTML_Helper::setGridDB([
        'model_class' => ApplicationPlaces::class,
        'model_method' => 'getGrid',
        'model_filter' => 'pid',
        'model_filter_var' => $data['id'],
        'grid' => 'grid',
        'controller' => 'ApplicationSpec',
        'action_add' => 'AddPlaces/?pid=' . $data['id'],
        'home_hdr' => 'Направления подготовки',
        'button_label' => 'Выбрать'
    ]);
    ?>
    <form enctype="multipart/form-data" action="<?php echo Basic_Helper::appUrl('ApplicationSpec', 'Save'); ?>"
          method="post" id="form_app_spec" novalidate>
        <div class="form-group">
            <input type="hidden" id="id" name="id" value="<?php echo $data['id']; ?>"/>
            <input type="hidden" id="application_2_required" name="application_2_required"
                   value="<?php echo $application_2; ?>"/>
            <input type="hidden" id="photo3x4_required" name="photo3x4_required" value="<?php echo $photo3x4; ?>"/>
            <input type="hidden" id="medical_certificate_required" name="medical_certificate_required"
                   value="<?php echo $medical_certificate; ?>"/>
            <input type="hidden" id="inila_required" name="inila_required" value="<?php echo $inila; ?>"/>
        </div>
        <div class="form-group">
            <h5>Вступительные испытания</h5><br>
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th class="align-text-top">Предмет</th>
                    <th class="align-text-top">Тип испытания</th>
                    <th class="align-text-top">Кол-во баллов</th>
                    <th class="align-text-top">Год сдачи</th>
                </tr>
                </thead>
                <?php

                $exams = new Model_ApplicationPlacesExams();
                $exams->pid = $data['id'];                
                //$exams_arr = $exams->getExamsByApplication(); // Ильяшенко: заменено на код ниже
                //begin Ильяшенко 08.02.2021 добавление возможности выбора испытания
                $exams_arr = $exams->getExamsByApplicationWithSelection();                
                //begin Ильяшенко 08.02.2021 добавление возможности выбора испытания

                if ($exams_arr) {                
                	$currPlaceId = NULL;
                    foreach ($exams_arr as $exams_row) {
                    	if ($currPlaceId !== $exams_row['application_place_id']){                    	
                        	echo '<tr class="table-secondary"><td colspan="4"><strong>';
                        	echo $exams_row['speciality_name']. ', ' . $exams_row['finance_name'] . ', ' . $exams_row['eduform_name'] . ', ' . $exams_row['spec_group_name'];
                        	echo '</strong></td></tr>';
                        	$currPlaceId = $exams_row['application_place_id'];
                    	}
                        echo '<tr>';
                        echo '<td>';
                        //begin Ильяшенко 08.02.2021 добавление возможности выбора испытания
                        $app_place_id = $exams_row['application_place_id'];
                       	$disableSelection = $app_row['status'] != $app::STATUS_CREATED && $app_row['status'] != $app::STATUS_SAVED && $app_row['status'] != $app::STATUS_CHANGED;                        
                        if ($exams_row['selected'] == ApplicationPlacesExams::EXAM_NO_SELECTION || $exams_row['selected'] == ApplicationPlacesExams::EXAM_SELECTION){
                        	$discipline_code = $exams_row['discipline_code'];
                        	$checkedSelection = $exams_row['selected'] == ApplicationPlacesExams::EXAM_SELECTION;                        	
                        	echo "<input name=\"exam_selected_$app_place_id\" type=\"radio\" class=\"exam_selected\" value=\"$discipline_code\" data-app_place_id=\"$app_place_id\" " . 
                        		($checkedSelection ? 'checked' : '') . " " . ($disableSelection ? 'disabled' : '') . "/> ";
                        }
                        //end Ильяшенко 08.02.2021 добавление возможности выбора испытания
                        echo $exams_row['discipline_name'] . '</td>';
                        if ($debug) {
                            echo $exams_row['discipline_name'] . '<br/>';
                        }

                        $test = new Model_DictTestingScopes();
                        $is_ege = FALSE;

                        if ($place->getByAppForBachelorSpec()) {
                            if ($debug) {
                                echo 'X: getByAppForBachelorSpec<br>';
                            }
                            // bachelors and specialists
                            $test_arr = $test->getEntranceExams();
                            if ($citizenship['code'] == '643' && $app->checkCertificate()) {
                                if ($debug) {
                                    echo 'X: citizenship dead + checkCertificate<br>';
                                }
                                $disabled = 1;
                            } else {
                                if ($debug) {
                                    echo 'X: citizenship OK<br>';
                                }
                                if ($disableSelection)
                                {
                                	$disabled = 1;
                                }                
                                else
                                {                
	                                $disabled = 0;
								}
                            }

                            if ($test_arr) {
                                if ($debug) {
                                    echo 'X: test_arr OK<br>';
                                }

                                echo '<td><select class="form-control exam_type_selected" data-app_place_id="' . $app_place_id . '" data-disc="' . $exams_row['discipline_code'] . '" id="exam' . $exams_row['discipline_code'] . '" name="exam' . $exams_row['discipline_code'] . '"' . (($disabled == 1)
                                        ? ' disabled' : '') . '>';
                                foreach ($test_arr as $test_row) {
                                    if ($exams_row['code'] === $test_row['code'] && $exams_row['code'] == '000000001') {
                                        $is_ege = True;
                                    }

                                    echo '<option value="' . $test_row['code'] . '"' .
                                        (($exams_row['code'] === $test_row['code']) ? ' selected' : '') . '>' .
                                        $test_row['description'] .
                                        '</option>';
                                }
                                echo '</select>';
                                /*echo '<script>';
                                echo '$("#exam' . $exams_row['discipline_code'] . '").change(function () {
                                    changeExam("' . $data['id'] . '", "' . $exams_row['discipline_code'] . '");
                                });';
                                echo '</script>';*/
                            }
                        } elseif ($place->getByAppForMagister() || $place->getByAppForSpecial()) {
                            // magisters, specials
                            if ($debug) {
                                echo 'X: getByAppForMagister OR getByAppForSpecial<br>';
                            }

                            $_t = $test->getTest();
                            if (is_array($_t) && count($_t) > 0) {
                                echo '<td>' . $_t['description'] . '</td>';
                            } else {
                                $_t = $test->getExam();
                                echo '<td>' . $_t['description'] . '</td>';
                            }

                            /*
                            $disabled = 1;
                            $test_row = $test->getExam();
                            if( $test_row ) {
                                if( $debug ) {
                                    echo 'X: test_arr OK<br>';
                                }
    
                                if($exams_row['code'] === $test_row['code'] && $exams_row['code'] == '000000001' ) {
                                    $is_ege = True;
                                }
                                
                                echo '<td><select class="form-control" id="exam'.$exams_row['discipline_code'].'" name="exam'.$exams_row['discipline_code'].'"'.( ( $disabled == 1 )
                                        ? ' disabled' : '' ).'>';
                                echo '<option value="'.$test_row['code'].'"'.
                                     ( ( $exams_row['code'] === $test_row['code'] ) ? ' selected' : '' ).'>'.
                                     $test_row['description'].
                                     '</option>';
                                echo '</select>';
                            }
                            */
                        } elseif ($place->getByAppForClinical()) {
                            // attending physicians
                            if ($debug) {
                                echo 'X: getByAppForClinical<br>';
                            }

                            $disabled = 1;
                            $test_row = $test->getTest();
                            if ($test_row) {
                                if ($debug) {
                                    echo 'X: test_arr OK<br>';
                                }

                                if ($exams_row['code'] === $test_row['code'] && $exams_row['code'] == '000000001') {
                                    $is_ege = True;
                                }

                                echo '<td><select class="form-control" id="exam' . $exams_row['discipline_code'] . '" name="exam' . $exams_row['discipline_code'] . '"' . (($disabled == 1)
                                        ? ' disabled' : '') . '>';
                                echo '<option value="' . $test_row['code'] . '"' .
                                    (($exams_row['code'] === $test_row['code']) ? ' selected' : '') . '>' .
                                    $test_row['description'] .
                                    '</option>';
                                echo '</select>';
                            }
                        } else {
                            $_t = $test->getTest();
                            if (is_array($_t) && count($_t) > 0) {
                                echo '<td>' . $_t['description'] . '</td>';
                            } else {
                                $_t = $test->getExam();
                                echo '<td>' . $_t['description'] . '</td>';
                            }
                        }
                        if ($is_ege) {
                            echo '<td>' . $exams_row['points'] . '</td>';
                            echo '<td>' . $exams_row['reg_year'] . '</td>';
                        } else {
                            echo '<td>-</td>';
                            echo '<td>-</td>';
                        }
                        echo '</tr>';
                    }
                }

                $debug = FALSE;
                ?>
            </table>
            <div class="row">
                <div class="">
                    <h5>Индивидуальные достижения</h5>
                </div>
                <div class="col">
                    <?php echo HTML_Helper::setHrefButtonIcon('ApplicationSpec', 'SyncIa/?id=' . $data['id'], 'btn btn-primary', 'fas fa-sync',
                        'Обновить индивидуальные достижения'); ?>
                </div>
            </div>
            <br>
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th class="align-text-top">Индивидуальное достижение</th>
                    <th class="align-text-top">Документ</th>
                    <th class="align-text-top">Выдан</th>
                    <th class="align-text-top">Дата выдачи</th>
                </tr>
                </thead>
                <?php
                $ia = new Model_ApplicationAchievs();
                $ia->pid = $data['id'];
                $ia_arr = $ia->getGrid();
                if ($ia_arr) {
                    foreach ($ia_arr as $ia_row) {
                        echo '<tr>';
                        echo '<td>' . $ia_row['achiev'] . '</td>';
                        echo '<td>' . $ia_row['doc'] . '</td>';
                        echo '<td>' . $ia_row['company'] . '</td>';
                        echo '<td>' . $ia_row['dt_issue'] . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </table>
            <br>
            <?php
            /* additional info */
            echo Form_Helper::setFormHeaderSub('Дополнительная информация');
            // inila
            echo Form_Helper::setFormInput([
                'label' => 'СНИЛС',
                'control' => 'inila',
                'type' => 'text',
                'class' => $data['inila_cls'],
                'required' => 'no',
                'value' => $data['inila'],
                'success' => $data['inila_scs'],
                'error' => $data['inila_err'],
                'events' => [
                    'input' => 'console.log($("#inila").val());'
                ]
            ]);
            // campus
            echo Form_Helper::setFormCheckbox([
                'label' => 'Нуждаюсь в общежитии',
                'control' => 'campus',
                'class' => $data['campus_cls'],
                'value' => $data['campus'],
                'success' => $data['campus_scs'],
                'error' => $data['campus_err']
            ]);
            // conds
            echo Form_Helper::setFormCheckbox([
                'label' => 'Прошу создать специальные условия (например: присутствие ассистента, наличие звукоусиливающей аппаратуры)',
                'control' => 'conds',
                'class' => $data['conds_cls'],
                'value' => $data['conds'],
                'success' => $data['conds_scs'],
                'error' => $data['conds_err']
            ]);
            // remote
            echo Form_Helper::setFormCheckbox([
                'label' => 'Прошу разрешить сдачу вступительных испытаний с использованием дистанционных технологий',
                'control' => 'remote',
                'class' => $data['remote_cls'],
                'value' => $data['remote'],
                'success' => $data['remote_scs'],
                'error' => $data['remote_err']
            ]);
            /* save application as PDF */
            echo '<p></p>';
            echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЕсли Вы увидите на экране печатную форму заявления, где большая часть данных отсутствует, <strong>не пытайтесь её распечатывать из браузера</strong>. Вместо это сначала сохраните печатную форму заявления на диск (кнопка <strong>\"Загрузить\"</strong> или <strong>\"Скачать\"</strong>) и распечатайте полученный файл."),
                'alert-warning');
            echo "<div id='Make_app'>";
            echo HTML_Helper::setHrefButtonIcon('ApplicationSpec', 'SavePdf/?pid=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x ', 'Сформировать заявление', 1);
            echo "</div>";
			echo "</div>";
            /* scans */
            echo Form_Helper::setFormHeaderSub('Скан-копии');
            echo Form_Helper::setFormFileListDB([
                'required' => 'required',
                'required_style' => 'StarUp',
                'model_class' => Model_DictScans::class,
                'model_method' => 'getByDocument',
                'model_filter' => 'doc_code',
                'model_filter_var' => 'application',
                'model_field' => 'scan_code',
                'model_field_name' => 'scan_name',
                'data' => $data,
                'home_ctr' => 'Application',
                'home_hdr' => 'Направления подготовки',
                'home_act' => 'Edit/?id=' . $data['id'],
                'ext' => FILES_EXT_SCANS
            ]);
            ?>
        </div>
        <!-- controls -->
        <div class="form-group">
            <div class="col">
                <?php
                switch ($app_row['status']) {
                    case $app::STATUS_CREATED:
                        echo HTML_Helper::setSubmit('btn btn-info', 'btn_save', 'Сохранить', 'Сохраняет данные заявления');
                        break;
                    case $app::STATUS_SAVED:
                        echo HTML_Helper::setSubmit('btn btn-info', 'btn_save', 'Обновить', 'Обновляет данные заявления');

                        $_app = new \common\models\Model_Application();
                        $_app->id = $data['id'];
                        if($_app->checkCanSend()) {
                            echo HTML_Helper::setHrefButton('ApplicationSpec', 'Send/?id=' . $data['id'], 'btn btn-success', 'Отправить', 'Отправляет данные заявления на проверку модератору');
                        } else {
                            echo HTML_Helper::setAlert('На одно из направлений подготовки, которое вы выбрали уже закончена подача заявлений', 'alert-danger');
                        }

                        break;
                    case $app::STATUS_APPROVED:
                        switch ($app_row['type']) {
                            case $app::TYPE_NEW:
                            case $app::TYPE_CHANGE:
                                //echo HTML_Helper::setHrefButton('ApplicationSpec', 'Change/?id=' . $data['id'], 'btn btn-primary', 'Изменить', 'Создаёт заявление на изменение');
                                echo HTML_Helper::setHrefButton('ApplicationSpec', 'Recall/?id=' . $data['id'], 'btn btn-danger', 'Отозвать', 'Создаёт заявление на отзыв');
                                break;
                            case $app::TYPE_RECALL:
                            default:
                                break;
                        }
                        break;
                    case $app::STATUS_REJECTED:
                    case $app::STATUS_SENDED:
                    default:
                        break;
                }
                echo HTML_Helper::setHrefButton('ApplicationSpec', 'Cancel', 'btn btn-warning', 'К списку заявлений');
                ?>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        formInit();
        formEvents();
    });
</script>

<script>
    // form init
    function formInit() {
        // application_2 required
        if ($('#application_2_required').val() == 1) {
            $("label[for='application_2']").html('Заявление о приеме в БелГУ (второй лист)<span style="color: #ff0000;">*</span>');
        } else {
            $("label[for='application_2']").html('Заявление о приеме в БелГУ (второй лист)');
        }
        // photo3x4 required
        if ($('#photo3x4_required').val() == 1) {
            $("label[for='photo3x4']").html('Фотография 3х4 (кроме PDF)<span style="color: #ff0000;">*</span>');
        } else {
            $("label[for='photo3x4']").html('Фотография 3х4 (кроме PDF)');
        }
        // medical_certificate required
        //if ($('#medical_certificate_required').val() == 1) {
        //    $("label[for='medical_certificate_face']").html('Медицинская справка (лицевая сторона)<span style="color: #ff0000;">*</span>');
        //    $("label[for='medical_certificate_back']").html('Медицинская справка (оборотная сторона)<span style="color: #ff0000;">*</span>');
        //} else {
        $("label[for='medical_certificate_face']").html('Медицинская справка (лицевая сторона)');
        $("label[for='medical_certificate_back']").html('Медицинская справка (оборотная сторона)');
        //}
        // inila required
        if ($('#inila_required').val() == 1) {
            $("label[for='inila']").html('СНИЛС*');
            $("label[for='inila_face']").html('СНИЛС (лицевая сторона)<span style="color: #ff0000;">*</span>');
        } else {
            $("label[for='inila']").html('СНИЛС');
            $("label[for='inila_face']").html('СНИЛС (лицевая сторона)');
        }
    }
</script>

<script>
    // form events
    function formEvents() {
        // exam change
        $("select[name^='exam']").change(function () {
            var exams = 0;
            var exam = 0;
            $("select[name^='exam']").each(function (i, elem) {
                if ($(elem).val() == '000000002') {
                    exam = exam + 1;
                }
                exams = exams + 1;
            });
            if (exams == exam) {
                $("label[for='photo3x4']").html('Фотография 3х4 (кроме PDF)*');
            } else {
                $("label[for='photo3x4']").html('Фотография 3х4 (кроме PDF)');
            }
        });
        // submit click
        $('#btn_save').click(function () {
            $("select[name^='exam']").prop('disabled', false);
        });
    }
</script>

<script>
    $(function () {
    	function displaySuccess(msg){
        	$.toast({
			    heading: 'Успех',
			    text: msg,
			    showHideTransition: 'slide',
			    position: 'top-right',
			    icon: 'success',
			    hideAfter : 3000
			});    		
    	}
    	
    	function displayError(msg){
        	$.toast({
			    heading: 'Ошибка',
			    text: msg,
			    showHideTransition: 'slide',
			    position: 'top-right',
			    icon: 'error',
			    hideAfter : 5000
			});    		
    	}    	
    
        $("#inila").mask("999-999-999-99");
        
        $('input:radio.exam_selected').change(function () {        
        	$el = $(this);
	        $.post(
	            "<?= HTML_Helper::getServerUrl('/frontend/Application/ChooseExam') ?>",
	            {app_place_id: $el.attr('data-app_place_id'), disc_code: $el.val()},
	            function (data, status) {
	                if (data === 'ok'){
	                	displaySuccess('Вступительное испытание успешно обновлено');
					}
	                else{
	                	displayError('Ошибка при сохранении выбранного экзамена. Попробуйте перезагрузить страницу и повторить выбор');
	                }
	            }).fail(function() {
    				displayError('Ошибка при сохранении выбранного экзамена. Попробуйте перезагрузить страницу и повторить выбор');
  				});
        });        
        
        $('select.exam_type_selected').change(function(){
        	var app_place_id = $(this).attr('data-app_place_id');
        	var disc_code = $(this).attr('data-disc');
	        var new_code = $(this).children("option:selected").val();
	        var url = "<?= HTML_Helper::getServerUrl('/frontend/Application/ChangeExam') ?>";
	        $.post(
	            url,
	            {app_place_id: app_place_id, disc_code: disc_code, new_code: new_code},
	            function (data, status) {
	                if (data === 'ok'){
	                	displaySuccess('Тип испытания успешно обновлен');
					}
	                else{
	                	displayError('Ошибка при сохранении типа испытания. Попробуйте перезагрузить страницу и повторить выбор');
	                }
	            }).fail(function() {
    				displayError('Ошибка при сохранении типа испытания. Попробуйте перезагрузить страницу и повторить выбор');
  				});
        });
    });
</script>
