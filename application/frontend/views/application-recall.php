<?php

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
if( !isset($data) ) {
    Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', NULL, nl2br("Ошибка направлений подготовки!\nСвяжитесь с администратором."));
}

// get application
$app     = new Application();
$app->id = $data['id'];
$app_row = $app->get();
// get education document
$docs     = new DocsEduc();
$docs->id = $app_row['id_docseduc'];
$docs_row = $docs->getForField();
// get foreign language
$langs     = new DictForeignLangs();
$langs->id = $app_row['id_lang'];
$langs_row = $langs->get();
// get citizenship
$personal    = new Personal();
$citizenship = $personal->getCitizenshipByUser();
// manage scans
$place      = new ApplicationPlaces();
$place->pid = $data['id'];
$exam       = new ApplicationPlacesExams();
$exam->pid  = $data['id'];
// application_2
if( $place->getByAppForSpecial() ) {
    $application_2 = 0;
} else {
    $application_2 = 1;
}
// photo3x4
if( $place->getByAppForBachelorSpec() && $exam->existsExams() ) {
    $photo3x4 = 1;
} else {
    $photo3x4 = 0;
}
// medical_certificate
if( $place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1() ) {
    $medical_certificate = 1;
} else {
    $medical_certificate = 0;
}
// inila
if( $place->getByAppForClinical() ) {
    $inila = 1;
} else {
    $inila = 0;
}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <h2>Заявление № <?php echo $app_row['numb']; ?></h2>
    <?php
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
    /* type */
    ?>
    <form enctype="multipart/form-data" action="<?php echo Basic_Helper::appUrl('ApplicationSpec', 'Recall'); ?>" method="post" novalidate>
        <div class="form-group">
            <input type="hidden" id="id" name="id" value="<?php echo $data['id']; ?>"/>
        </div>
        <div class="form-group">
            <?php
            /* scans */
            echo Form_Helper::setFormHeaderSub('Скан-копии');
            echo Form_Helper::setFormFileListDB([
                                                    'required' => 'required',
                                                    'required_style' => 'StarUp',
                                                    'model_class' => 'common\\models\\Model_DictScans',
                                                    'model_method' => 'getByDocument',
                                                    'model_filter' => 'doc_code',
                                                    'model_filter_var' => 'application_recall',
                                                    'model_field' => 'scan_code',
                                                    'model_field_name' => 'scan_name',
                                                    'data' => $data,
                                                    'home_ctr' => 'Application',
                                                    'home_hdr' => 'Направления подготовки',
                                                    'home_act' => 'DeleteConfirm/?id='.$data['id'],
                                                    'ext' => FILES_EXT_SCANS
                                                ]);
            ?>
        </div>
        <!-- controls -->
        <div class="form-group">
            <div class="col">
                <?php
                echo HTML_Helper::setSubmit('btn btn-danger', 'btn_save', 'Отозвать', 'Создаёт заявление на отзыв');
                echo HTML_Helper::setHrefButton('ApplicationSpec', 'Cancel', 'btn btn-warning', 'Отмена');
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
    }
</script>

<script>
    // form events
    function formEvents() {
        // submit click
        $('#btn_save').click(function () {
            $("select[name^='exam']").prop('disabled', false);
        });
    }
</script>
