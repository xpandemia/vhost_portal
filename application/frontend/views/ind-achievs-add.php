<?php

use common\models\Model_DictIndAchievs;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

// check data
if( !isset($data) ) {
    Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', NULL, nl2br("Ошибка добавления индивидуального достижения!\nСвяжитесь с администратором."));
}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <?php
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
    echo Form_Helper::setFormBegin(IND_ACHIEVS['ctr'], IND_ACHIEVS['act'], IND_ACHIEVS['id'], IND_ACHIEVS['hdr'], 0, '/images/logo_bsu_transparent.gif');
    ?>
    <div class="form-group">
        <input type="hidden" id="id" name="id" value="<?php echo ( isset($data['id']) ) ? $data['id'] : NULL; ?>"/>
    </div>
    <?php
    // achiev type
    if( isset($data['id']) ) {
        echo Form_Helper::setFormSelectListDB([
                                                  'label' => 'Вид индивидуального достижения',
                                                  'control' => 'achiev_type',
                                                  'class' => $data['achiev_type_cls'],
                                                  'required' => 'yes',
                                                  'required_style' => 'StarUp',
                                                  'model_class' => Model_DictIndAchievs::class,
                                                  'model_method' => 'getAll',
                                                  'model_field' => 'code',
                                                  'model_field_name' => 'description',
                                                  'value' => $data['achiev_type'],
                                                  'success' => $data['achiev_type_scs'],
                                                  'error' => $data['achiev_type_err'],
                                                  'grouped' => TRUE
                                              ]);
    } else {
        echo Form_Helper::setFormSelectListDB([
                                                  'label' => 'Вид индивидуального достижения',
                                                  'control' => 'achiev_type',
                                                  'class' => $data['achiev_type_cls'],
                                                  'required' => 'yes',
                                                  'required_style' => 'StarUp',
                                                  'model_class' => Model_DictIndAchievs::class,
                                                  'model_method' => 'getAllFilteredByUser',
                                                  'model_field' => 'code',
                                                  'model_field_name' => 'description',
                                                  'value' => $data['achiev_type'],
                                                  'success' => $data['achiev_type_scs'],
                                                  'error' => $data['achiev_type_err'],
                                                  'grouped' => TRUE
                                              ]);
    }
    // series
    echo Form_Helper::setFormInput([
                                       'label' => 'Серия',
                                       'control' => 'series',
                                       'type' => 'text',
                                       'class' => $data['series_cls'],
                                       'required' => 'no',
                                       'value' => $data['series'],
                                       'success' => $data['series_scs'],
                                       'error' => $data['series_err']
                                   ]);
    // numb
    echo Form_Helper::setFormInput([
                                       'label' => 'Номер',
                                       'control' => 'numb',
                                       'type' => 'text',
                                       'class' => $data['numb_cls'],
                                       'required' => 'no',
                                       'value' => $data['numb'],
                                       'success' => $data['numb_scs'],
                                       'error' => $data['numb_err']
                                   ]);
    // company
    echo Form_Helper::setFormInput([
                                       'label' => 'Наименование организации',
                                       'control' => 'company',
                                       'type' => 'text',
                                       'class' => $data['company_cls'],
                                       'required' => 'no',
                                       'value' => $data['company'],
                                       'success' => $data['company_scs'],
                                       'error' => $data['company_err'],
                                       'help' => "Это поле может содержать только <b>".MSG_INFO_RUS."</b>" //  Добавил Паша
                                   ]);
    // dt_issue
    echo Form_Helper::setFormInput([
                                       'label' => 'Дата выдачи',
                                       'control' => 'dt_issue',
                                       'type' => 'text',
                                       'class' => $data['dt_issue_cls'],
                                       'required' => 'no',
                                       'value' => $data['dt_issue'],
                                       'success' => $data['dt_issue_scs'],
                                       'error' => $data['dt_issue_err']
                                   ]);
    /* scans */
    echo Form_Helper::setFormHeaderSub('Скан-копии');
    echo Form_Helper::setFormFileListDB([
                                            'required' => 'required',
                                            'required_style' => 'StarUp',
                                            'model_class' => 'common\\models\\Model_DictScans',
                                            'model_method' => 'getByDocument',
                                            'model_filter' => 'doc_code',
                                            'model_filter_var' => 'ind_achievs',
                                            'model_field' => 'scan_code',
                                            'model_field_name' => 'scan_name',
                                            'data' => $data,
                                            'home_id' => ( isset($data['id']) ) ? $data['id'] : NULL,
                                            'home_ctr' => IND_ACHIEVS['ctr'],
                                            'home_hdr' => IND_ACHIEVS['hdr'],
                                            'home_act' => 'Edit',
                                            'ext' => FILES_EXT_SCANS
                                        ]);
    ?>
    <!-- controls -->
    <div class="form-group">
        <div class="col">
            <?php
            echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить', 'Сохраняет данные');
            if( isset($data['id']) ) {
                echo HTML_Helper::setHrefButton(IND_ACHIEVS['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Сбрасывает данные');
            } else {
                echo HTML_Helper::setHrefButton(IND_ACHIEVS['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Очищает форму ввода');
            }
            echo HTML_Helper::setHrefButton(IND_ACHIEVS['ctr'], 'Cancel', 'btn btn-warning', 'Отмена');
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
