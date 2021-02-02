<?php

use common\models\Model_DictPrivilleges;
use common\models\Model_DictScans;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\Form_Helper;
use tinyframe\core\helpers\HTML_Helper;

// check data
if( !isset($data) ) {
    Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', NULL, nl2br("Ошибка добавления индивидуального достижения!\nСвяжитесь с администратором."));
}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <?php
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
    echo Form_Helper::setFormBegin(TARGET_QUOTA['ctr'], TARGET_QUOTA['act'], TARGET_QUOTA['id'], TARGET_QUOTA['hdr'], 0, '/images/logo_bsu_transparent.gif');
    ?>
    <div class="form-group">
        <input type="hidden" id="id" name="id" value="<?php echo $data['id'] ?? NULL; ?>"/>
    </div>
    <?php
    //issuer
    echo Form_Helper::setFormInput([
                                       'label' => 'Кем выдан',
                                       'control' => 'doc_issuer',
                                       'type' => 'text',
                                       'class' => $data['doc_issuer_cls'],
                                       'required' => 'yes',
                                       'value' => $data['doc_issuer'],
                                       'success' => $data['doc_issuer_scs'],
                                       'error' => $data['doc_issuer_err']
                                   ]);
    // numb
    echo Form_Helper::setFormInput([
                                       'label' => 'Номер подтверждающего документа',
                                       'control' => 'doc_number',
                                       'type' => 'text',
                                       'class' => $data['doc_number_cls'],
                                       'required' => 'yes',
                                       'value' => $data['doc_number'],
                                       'success' => $data['doc_number_scs'],
                                       'error' => $data['doc_number_err']
                                   ]);
    // dt_issue
    echo Form_Helper::setFormInput([
                                       'label' => 'Дата выдачи подтверждающего документа',
                                       'control' => 'doc_date',
                                       'type' => 'text',
                                       'class' => $data['doc_date_cls'],
                                       'required' => 'yes',
                                       'value' => $data['doc_date'],
                                       'success' => $data['doc_date_scs'],
                                       'error' => $data['doc_date_err']
                                   ]);
    /* scans */
    echo Form_Helper::setFormHeaderSub('Скан-копии');
    echo Form_Helper::setFormFileListDB([
                                            'required' => 'required',
                                            'required_style' => 'StarUp',
                                            'model_class' => Model_DictScans::class,
                                            'model_method' => 'getByDocument',
                                            'model_filter' => 'doc_code',
                                            'model_filter_var' => 'target_quota',
                                            'model_field' => 'scan_code',
                                            'model_field_name' => 'scan_name',
                                            'data' => $data,
                                            'home_id' => $data['id'] ?? NULL,
                                            'home_ctr' => TARGET_QUOTA['ctr'],
                                            'home_hdr' => TARGET_QUOTA['hdr'],
                                            'home_act' => 'Edit',
                                            'ext' => FILES_EXT_SCANS
                                        ]);
    ?>
    <!-- controls -->
    <div class="form-group">
        <div class="col">
            <?php
            echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить', 'Сохраняет данные индивидуального достижения');
            if( isset($data['id']) ) {
                echo HTML_Helper::setHrefButton(TARGET_QUOTA['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Сбрасывает данные индивидуального достижения');
            } else {
                echo HTML_Helper::setHrefButton(TARGET_QUOTA['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Обнуляет форму ввода');
            }
            echo HTML_Helper::setHrefButton(TARGET_QUOTA['ctr'], 'Cancel', 'btn btn-warning', 'Отмена');
            ?>
        </div>
    </div>
    <?php
    echo Form_Helper::setFormEnd();
    ?>
</div>

<script>
    $(function () {
        $("#dt_issue").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ"});
    });
</script>
