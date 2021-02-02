<?php

use common\models\Model_Application;
use common\models\Model_ApplicationConfirm;
use common\models\Model_ApplicationConfirmPlaces as ApplicationConfirmPlaces;
use common\models\Model_DictScans;
use frontend\models\Model_Scans;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\Form_Helper;
use tinyframe\core\helpers\HTML_Helper;

// check data
if (!isset($data)) {
    Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', NULL, nl2br("Ошибка направлений подготовки!\nСвяжитесь с администратором."));
}
$conf = new Model_ApplicationConfirm();
$conf->id = $data['id'];
$conf_row = $conf->get();

// get application
$app = new Model_Application();
$app->id = $conf_row['id_application'];
$app_row = $app->get();
// manage scans
$place = new ApplicationConfirmPlaces();
$place->id_application_place = $data['id'];

$debug = FALSE;
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <?php
    if ($conf_row['type'] == $conf::TYPE_NEW) {
        echo '<h2>Согласие к заявлению № ' . $app_row['numb'] . '</h2>';
    } else {
        echo '<h2>Отзыв согласия к заявлению № ' . $app_row['numb'] . '</h2>';
    }

    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
    /* type */
    echo '<div class="row">';
    echo '<div class="col">';
    //echo Model_Application::showType($app_row['type']);
    echo '</div>';
    /* status */
    echo '<div class="col">';
    //echo Model_Application::showStatus($app_row['status']);
    echo '</div>';
    echo '</div>';
    /* comment */
    //TODO: переделать под заявление на отзыв
    if ($conf_row['id_status'] == $conf::STATUS_REJECTED) {
        echo HTML_Helper::setAlert('Причины отклонения: <strong>' . $conf_row['comment'] . '</strong>', 'alert-danger');
    }
    ?>
    <hr>
    <h5>Направления подготовки</h5><br>
    <?php
    echo HTML_Helper::setAlert(nl2br('<strong>Краткая инструкция</strong>'),
        'alert-warning');
    $selected = $conf->isSelected();

    if ($conf_row['type'] == $conf::TYPE_NEW) {
        $_filter = 'getGrid';
    } else {
        $_filter = 'getGridRecall';
        $selected = TRUE;
    }

    if (!$selected) {
        echo '<form id="form_spec_freeze" enctype="multipart/form-data" method="POST" action="' . Basic_Helper::appUrl('ApplicationConfirm', 'Freeze') . '">';
        echo '<input type="hidden" name="app_id" value="' . $data['id'] . '">';
        echo HTML_Helper::setGridDB([
            'model_class' => ApplicationConfirmPlaces::class,
            'model_method' => $_filter,
            'model_filter' => 'id_application_confirm',
            'model_filter_var' => $data['id'],
            'grid' => 'grid',
            'controller' => 'ApplicationConfirmPlaces',
            'home_hdr' => 'Направления подготовки'
        ]);

        if ($conf_row['type'] == $conf::TYPE_NEW) {
            echo HTML_Helper::setSubmit('btn btn-info', 'btn_spec_freeze', 'Сформировать согласие на зачисление', 'Подготавливает шаблон документа к загрузке');
        } else {
            echo HTML_Helper::setSubmit('btn btn-info', 'btn_spec_freeze', 'Сформировать отзыв согласия согласие на зачисление', 'Подготавливает шаблон документа к загрузке');
        }

        echo '</form>';
    } else {
        echo HTML_Helper::setGridDB([
            'model_class' => ApplicationConfirmPlaces::class,
            'model_method' => $_filter,
            'model_filter' => 'id_application_confirm',
            'model_filter_var' => $data['id'],
            'grid' => 'grid',
            'controller' => 'ApplicationConfirmPlaces',
            'home_hdr' => 'Направления подготовки'
        ]);
        echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЕсли Вы увидите на экране печатную форму согласия, где большая часть данных отсутствует, <strong>не пытайтесь её распечатывать из браузера</strong>. Вместо это сначала сохраните печатную форму заявления на диск (кнопка <strong>\"Загрузить\"</strong> или <strong>\"Скачать\"</strong>) и распечатайте полученный файл."),
            'alert-warning');
        echo HTML_Helper::setHrefButtonIcon(APP_CONFIRM['ctr'], 'SavePdf/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить шаблон согласия', 1);
        if ($conf_row['type'] == $conf::TYPE_NEW) {
            echo "<span style='color:red'> <strong>Сформировать согласие на зачисление</strong></span>";
        } else {
            echo "<span style='color:red'> <strong>Сформировать отзыв согласие на зачисление</strong></span>";
        }


        if ($conf_row['id_status'] == $conf::STATUS_APPROVED) {
            if ($conf_row['type'] == $conf::TYPE_NEW) {
                $_filter = 'application_confirm';
            } else {
                $_filter = 'app_confirm_recall';
            }

            echo '<form enctype="multipart/form-data" action="' . Basic_Helper::appUrl(APP_CONFIRM['ctr'], 'Recall') . '" method="post" id="form_app_spec" novalidate>';
            echo '<div class="form-group">';
            /* scans */
            echo '<input type="hidden" name="id" value="' . $data['id'] . '" />';
            echo Form_Helper::setFormHeaderSub('Скан-копии');
            echo Form_Helper::setFormFileListDB([
                'required' => 'required',
                'required_style' => 'StarUp',
                'model_class' => Model_DictScans::class,
                'model_method' => 'getByDocument',
                'model_filter' => 'doc_code',
                'model_filter_var' => $_filter,
                'model_field' => 'scan_code',
                'model_field_name' => 'scan_name',
                'data' => $data,
                'home_ctr' => 'ApplicationConfirm',
                'home_hdr' => 'Направления подготовки',
                'home_act' => 'Edit/?id=' . $data['id'],
                'ext' => FILES_EXT_SCANS
            ]);
            echo '</div>';
            /* controls */
            echo '<div class="form-group">';
            echo '<div class="col">';
            if($conf_row['type'] == 0) {
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save', 'Отозвать', 'Отправляет отзыв согласия о зачислении');
            }
            echo '</div></div></form>';
        } elseif ($conf_row['id_status'] >= $conf::STATUS_REJECTED || $conf_row['id_status'] == $conf::STATUS_SENT) {
            echo '<div class="form-group">';
            /* scans */
            if ($conf_row['type'] == $conf::TYPE_NEW) {
                $_filter = 'application_confirm';
            } else {
                $_filter = 'app_confirm_recall';
            }

            echo Form_Helper::setFormHeaderSub('Скан-копии');
            echo Form_Helper::setFormFileListDB([
                'required' => 'required',
                'required_style' => 'StarUp',
                'model_class' => Model_DictScans::class,
                'model_method' => 'getByDocument',
                'model_filter' => 'doc_code',
                'model_filter_var' => $_filter,
                'model_field' => 'scan_code',
                'model_field_name' => 'scan_name',
                'data' => $data,
                'home_ctr' => 'ApplicationConfirm',
                'home_hdr' => 'Направления подготовки',
                'home_act' => 'Edit/?id=' . $data['id'],
                'ext' => FILES_EXT_SCANS,
                'editable' => FALSE
            ]);
            echo '</div>';
        } else {
            if ($conf_row['type'] == $conf::TYPE_NEW) {
                $_filter = 'application_confirm';
            } else {
                $_filter = 'app_confirm_recall';
            }

            if (in_array($conf_row['id_status'], [$conf::STATUS_SENT, $conf::STATUS_APPROVED, $conf::STATUS_REJECTED, $conf::STATUS_RECALLED])) {
                $editable = FALSE;
            } else {
                $editable = TRUE;
            }

            echo '<form enctype="multipart/form-data" action="' . Basic_Helper::appUrl(APP_CONFIRM['ctr'], 'Send') . '" method="post" id="form_app_spec" novalidate>';
            echo '<div class="form-group">';
            /* scans */
            echo '<input type="hidden" name="id" value="' . $data['id'] . '" />';
            echo Form_Helper::setFormHeaderSub('Скан-копии');
            echo Form_Helper::setFormFileListDB([
                'required' => 'required',
                'required_style' => 'StarUp',
                'model_class' => Model_DictScans::class,
                'model_method' => 'getByDocument',
                'model_filter' => 'doc_code',
                'model_filter_var' => $_filter,
                'model_field' => 'scan_code',
                'model_field_name' => 'scan_name',
                'data' => $data,
                'home_ctr' => 'ApplicationConfirm',
                'home_hdr' => 'Направления подготовки',
                'home_act' => 'Edit/?id=' . $data['id'],
                'ext' => FILES_EXT_SCANS,
                'editable' => $editable
            ]);
            echo '</div>';
            /* controls */
            echo '<div class="form-group">';
            echo '<div class="col">';
            if ($editable) {
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save', 'Отправить', 'Сохраняет данные заявления');
            }
            echo '</div></div></form>';
        }
    }
    ?>
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
