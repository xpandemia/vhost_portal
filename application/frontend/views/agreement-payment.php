<?php

use common\models\Model_Agreement as DB_Agreement;
use common\models\Model_DictDoctypes as Model_DictDoctypesAlias;
use frontend\models\Model_Agreement;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

// check resume
if (!isset($data) || !isset($_SESSION[APP_CODE]['user_name'])) {
    Basic_Helper::redirectHome();
}
echo '<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">';
if (isset($data['success_msg'])) {
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
}

if (isset($data['error_msg'])) {
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
}

if (isset($data['id']) && $data['id'] > 0) {
    $agreement = new DB_Agreement();
    $agreement->load($data['id']);
    if ($agreement->id_user == $_SESSION[APP_CODE]['user_id']) {
        switch ($agreement->status) {
            case $agreement::STATUS_CREATED:
            case $agreement::STATUS_SAVED_PAYER_DATA:
                $disabled_details = FALSE;
                $disabled_scans = TRUE;
                break;
            case $agreement::STATUS_ALLOWED:
            case $agreement::STATUS_SAVED_SCANS:
                $disabled_details = TRUE;
                $disabled_scans = FALSE;
                break;
            default:
                $disabled_details = TRUE;
                $disabled_scans = TRUE;
        }
    } else {
        echo HTML_Helper::setAlert('<p>Вы пытаетесть получить доступ к чужому документу</p>', 'alert-danger');
        echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную');
    }
} else {
    $agreement = NULL;
    $disabled_details = FALSE;
    $disabled_scans = TRUE;
}

echo HTML_Helper::setAlert(
    '<p>В качестве Заказчика по договору об оказании платных образовательных услуг указывается лицо, которое фактически будет производить оплату за обучение.</p>
<p>При оплате образовательных услуг путем внесения наличных денежных средств в документе, подтверждающем оплату обучения, указывается лицо, являющееся заказчиком по Договору об оказании платных образовательных услуг.</p>
<p>Оплата за обучение, осуществляемая путем безналичного перечисления денежных средств на расчетный счет НИУ «БелГУ», производится с банковского счета лица, указанного в Договоре об оказании платных образовательных услуг в качестве Заказчика.</p>', 'alert-warning');

if ($agreement !== NULL) {
    switch ($agreement->status) {
        case $agreement::STATUS_ALLOWED:
        case $agreement::STATUS_SAVED_SCANS:
        case $agreement::STATUS_SENT_SCANS:
            $act = 'SaveScans';
            break;
        case $agreement::STATUS_CREATED:
        case $agreement::STATUS_SAVED_PAYER_DATA:
        case $agreement::STATUS_SENT_PAYER_DATA:
        case $agreement::STATUS_DISALLOWED:
        case $agreement::STATUS_APPROVED:
        case $agreement::STATUS_REJECTED:
        default:
            $act = AGREEMENT['act'];
            break;
    }
} else {
    $act = AGREEMENT['act'];
}

echo Form_Helper::setFormBegin(AGREEMENT['ctr'], $act, AGREEMENT['id'], 'Данные о плательщике', 2);

echo Form_Helper::setFormInput(['label' => 'Договор',
    'control' => 'id',
    'type' => 'hidden',
    'class' => $data['id_cls'],
    'required' => 'yes',
    'required_style' => 'StarUp',
    'value' => $data['id'],
    'success' => $data['id_scs'],
    'error' => $data['id_err']]);
echo Form_Helper::setFormInput(['label' => 'Согласие-основание',
    'control' => 'conf_id',
    'type' => 'hidden',
    'class' => $data['conf_id_cls'],
    'required' => 'yes',
    'required_style' => 'StarUp',
    'value' => $data['conf_id'],
    'success' => $data['conf_id_scs'],
    'error' => $data['conf_id_err']]);
echo Form_Helper::setFormInput(['label' => 'Тип плательщика',
    'control' => 'payer_type',
    'type' => 'hidden',
    'class' => $data['payer_type_cls'],
    'required' => 'yes',
    'required_style' => 'StarUp',
    'value' => $data['payer_type'],
    'success' => $data['payer_type_scs'],
    'error' => $data['payer_type_err']]);
/* status */
echo Model_Agreement::showStatus($data);
/* comment */
if ($data['status'] == DB_Agreement::STATUS_DISALLOWED || $data['status'] == DB_Agreement::STATUS_REJECTED) {
    echo HTML_Helper::setAlert('Причины отклонения: <strong>' . $data['comment'] . '</strong>', 'alert-danger');
}
switch ($data['payer_type']) {
    case DB_Agreement::PAYER_PERSON:
        /* personal data */
        echo Form_Helper::setFormHeaderSub('Личные данные');
        // name_last
        echo Form_Helper::setFormInput(['label' => LASTNAME_PLC,
            'control' => 'name_last',
            'type' => 'text',
            'class' => $data['name_last_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'placeholder' => LASTNAME_PLC,
            'value' => $data['name_last'],
            'success' => $data['name_last_scs'],
            'error' => $data['name_last_err'],
            'help' => LASTNAME_HELP,
            'disabled' => $disabled_details]);
        // name_first
        echo Form_Helper::setFormInput(['label' => FIRSTNAME_PLC,
            'control' => 'name_first',
            'type' => 'text',
            'class' => $data['name_first_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'placeholder' => FIRSTNAME_PLC,
            'value' => $data['name_first'],
            'success' => $data['name_first_scs'],
            'error' => $data['name_first_err'],
            'help' => FIRSTNAME_HELP,
            'disabled' => $disabled_details]);
        // name_middle
        echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЕсли у Вас есть отчество, то обязательно укажите его."), 'alert-warning');
        echo Form_Helper::setFormInput(['label' => MIDDLENAME_PLC,
            'control' => 'name_middle',
            'type' => 'text',
            'class' => $data['name_middle_cls'],
            'required' => 'no',
            'placeholder' => MIDDLENAME_PLC,
            'value' => $data['name_middle'],
            'success' => $data['name_middle_scs'],
            'error' => $data['name_middle_err'],
            'help' => MIDDLENAME_HELP,
            'disabled' => $disabled_details]);
        // sex
        echo Form_Helper::setFormRadio(['label' => 'Пол',
            'control' => 'sex',
            'required' => 'yes',
            'required_style' => 'StarUp',
            'radio' => [
                'male' => ['1' => 'Мужской'],
                'female' => ['0' => 'Женский'],
            ],
            'value' => $data['sex'],
            'error' => $data['sex_err'],
            'disabled' => $disabled_details]);
        // birth_dt
        echo Form_Helper::setFormInput(['label' => 'Дата рождения',
            'control' => 'birth_dt',
            'type' => 'text',
            'class' => $data['birth_dt_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'value' => $data['birth_dt'],
            'success' => $data['birth_dt_scs'],
            'error' => $data['birth_dt_err'],
            'disabled' => $disabled_details]);
        echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nВозраст оплачивающего физлица должен быть не менее 18 лет."), 'alert-warning');
        // birth_place
        echo Form_Helper::setFormInput(['label' => BIRTHPLACE_PLC,
            'control' => 'birth_place',
            'type' => 'text',
            'class' => $data['birth_place_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'placeholder' => BIRTHPLACE_PLC,
            'value' => $data['birth_place'],
            'success' => $data['birth_place_scs'],
            'error' => $data['birth_place_err'],
            'help' => BIRTHPLACE_HELP,
            'disabled' => $disabled_details]);
        // citizenship
        echo Form_Helper::setFormSelectListDB(['label' => 'Гражданство',
            'control' => 'citizenship',
            'class' => $data['citizenship_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'model_class' => 'common\\models\\Model_DictCountries',
            'model_method' => 'getAll',
            'model_field' => 'code',
            'model_field_name' => 'description',
            'value' => $data['citizenship'],
            'success' => $data['citizenship_scs'],
            'error' => $data['citizenship_err'],
            'disabled' => $disabled_details]);
        /* contacts */
        echo Form_Helper::setFormHeaderSub('Контактная информация');
        // phone mobile
        echo Form_Helper::setFormInput(['label' => 'Номер мобильного телефона',
            'control' => 'phone_number',
            'type' => 'text',
            'class' => $data['phone_number_cls'],
            'required' => 'no',
            'value' => $data['phone_number'],
            'success' => $data['phone_number_scs'],
            'error' => $data['phone_number_err'],
            'disabled' => $disabled_details]);
        /* passport */
        $code = null;
        if (isset($data['id_doctype']) && $data['id_doctype'] > 0) {
            $dict = new Model_DictDoctypesAlias();
            $dict->id = $data['id_doctype'];
            $code = $dict->get()['code'];
        }

        echo Form_Helper::setFormHeaderSub('Документ, удостоверяющий личность');
        echo Form_Helper::setFormSelectListDB(['label' => 'Тип документа',
            'control' => 'passport_type',
            'class' => $data['passport_type_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'model_class' => 'common\\models\\Model_DictDoctypes',
            'model_method' => 'getPassportsBsu',
            'model_field' => 'code',
            'model_field_name' => 'description',
            'value' => $code,
            'success' => $data['passport_type_scs'],
            'error' => $data['passport_type_err'],
            'disabled' => $disabled_details]);
        // series
        echo Form_Helper::setFormInput(['label' => 'Серия',
            'control' => 'series',
            'type' => 'text',
            'class' => $data['series_cls'],
            'required' => 'no',
            'value' => $data['series'],
            'success' => $data['series_scs'],
            'error' => $data['series_err'],
            'disabled' => $disabled_details]);
        // numb
        echo Form_Helper::setFormInput(['label' => 'Номер',
            'control' => 'numb',
            'type' => 'text',
            'class' => $data['numb_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'value' => $data['numb'],
            'success' => $data['numb_scs'],
            'error' => $data['numb_err'],
            'disabled' => $disabled_details]);
        // dt_issue
        echo Form_Helper::setFormInput(['label' => 'Дата выдачи',
            'control' => 'dt_issue',
            'type' => 'text',
            'class' => $data['dt_issue_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'value' => $data['dt_issue'],
            'success' => $data['dt_issue_scs'],
            'error' => $data['dt_issue_err'],
            'disabled' => $disabled_details]);
        // unit_name
        echo Form_Helper::setFormInput(['label' => UNITNAME_PLC,
            'control' => 'unit_name',
            'type' => 'text',
            'class' => $data['unit_name_cls'],
            'required' => 'no',
            'placeholder' => UNITNAME_PLC,
            'value' => $data['unit_name'],
            'success' => $data['unit_name_scs'],
            'error' => $data['unit_name_err'],
            'help' => UNITNAME_HELP,
            'disabled' => $disabled_details]);
        // unit_code
        echo Form_Helper::setFormInput(['label' => 'Код подразделения',
            'control' => 'unit_code',
            'type' => 'text',
            'class' => $data['unit_code_cls'],
            'required' => 'no',
            'value' => $data['unit_code'],
            'success' => $data['unit_code_scs'],
            'error' => $data['unit_code_err'],
            'disabled' => $disabled_details]);
        // unit_code
        echo Form_Helper::setFormHeaderSub('Адрес регистрации');
        echo '<br>';
        // address string (registration)
        echo Form_Helper::setFormInput(['label' => ADRREG_PAYER['name'],
            'control' => 'address_reg',
            'type' => 'text',
            'class' => $data['address_reg_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'placeholder' => ADRREG_PAYER['plc'],
            'value' => $data['address_reg'],
            'success' => $data['address_reg_scs'],
            'error' => $data['address_reg_err'],
            'help' => ADRREG_PAYER['help'],
            'disabled' => $disabled_details]);

        /* residential address */
        echo Form_Helper::setFormHeaderSub('Адрес проживания');
        echo '<br>';
        // address string (residential)
        echo Form_Helper::setFormInput(['label' => ADRESS_PAYER['name'],
            'control' => 'address_res',
            'type' => 'text',
            'class' => $data['address_res_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'placeholder' => ADRESS_PAYER['plc'],
            'value' => $data['address_res'],
            'success' => $data['address_res_scs'],
            'error' => $data['address_res_err'],
            'help' => ADRESS_PAYER['help'],
            'disabled' => $disabled_details]);
        echo HTML_Helper::setAlert('<strong>Внимание!</strong><br>
<p>В случае оплаты образовательных услуг из средств материнского (семейного) капитала в качестве Заказчика по договору
    должно быть указано лицо, являющееся распорядителем указанных средств.
    Порядок действия при оплате образовательных услуг из средств материнского (семейного) капитала:</p>
<ol>
    <li>договор на оказание платных образовательных услуг</li>
    <li>Обратиться в многофункциональный центр НИУ «БелГУ» по электронной почте <strong>mfc-mc@bsu.edu.ru</strong> или
        тел. 4722 73-25-63, 24-56-23 для заключения дополнительного соглашения и получения комплекта документов для
        пенсионного фонда
    </li>
</ol>', 'alert-warning');
        echo Form_Helper::setFormCheckbox(['label' => 'Оплата производится за счет материнского капитала',
            'control' => 'has_mat_capital',
            'class' => $data['has_mat_capital_cls'],
            'value' => $data['has_mat_capital'],
            'success' => $data['has_mat_capital_scs'],
            'error' => $data['has_mat_capital_err'],
            'disabled' => $disabled_details]);
        echo '<br>
<div class="form-group">
    <div class="col">';
        switch ($data['status']) {
            case DB_Agreement::STATUS_SENT_PAYER_DATA:
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит введенные вами данные</h5>';
                break;
            case DB_Agreement::STATUS_DISALLOWED:
                echo '<h5>Модератор отказал вам в праве заключения договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
            case DB_Agreement::STATUS_ALLOWED:
            case DB_Agreement::STATUS_SAVED_SCANS:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS]);
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_scans', 'Сохранить', 'Сохраняет прикрепленные скан-коппии в базу данных');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_scans', 'Отправить', 'Сохраняет и отправляет прикрепленные скан-коппии на проверку модератору');
                break;
            case DB_Agreement::STATUS_SENT_SCANS:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит отправленные вами документы</h5>';
                break;
            case DB_Agreement::STATUS_APPROVED:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Модератор одобрил и подтвердил заключение договора.<h5>';
                break;
            case DB_Agreement::STATUS_REJECTED:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Модератор отказал вам в заключении договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
            case DB_Agreement::STATUS_CREATED:
            case DB_Agreement::STATUS_SAVED_PAYER_DATA:
            default:
                echo HTML_Helper::setAlert('<h5>Осуществляя сохранение или отправку данных заказчика в личном кабинете, вы
            гарантируете, что действуете от своего имени и в своих интересах. В случае указания вами в качестве
            заказчика третьего лица, вы гарантируете, что имете соответствующие полномочия на указание его данных и
            указываете соответствующие данные от его имени.</h5>', 'alert-danger');
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_payer', 'Сохранить', 'Сохраняет данные плательщика');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_payer', 'Отправить', 'Отправить данные плательщика на рассмотрение модератору');
                break;
        }
        echo '</div></div>';
        break;
    case DB_Agreement::PAYER_LEGAL_AGENT:
        echo Form_Helper::setFormHeaderSub('Детали юридического лица');
        echo Form_Helper::setFormInput(['label' => 'Название организации',
            'control' => 'org_name',
            'type' => 'text',
            'class' => $data['org_name_cls'],
            'required' => 'yes',
            'required_style' => 'StarUp',
            'placeholder' => 'Название организации',
            'value' => $data['org_name'],
            'success' => $data['org_name_scs'],
            'error' => $data['org_name_err'],
            'help' => MSG_INFO,
            'disabled' => $disabled_details]);
        echo '<br><div class="form-group"><div class="col">';
        /*
        switch ($data['status']) {
            case DB_Agreement::STATUS_CREATED:
            case DB_Agreement::STATUS_SAVED_PAYER_DATA:
                echo HTML_Helper::setAlert('Осуществляя сохранение или отправку данных заказчика в личном кабинете, вы
        гарантируете, что действуете от своего имени и в своих интересах. В случае указания вами в качестве заказчика
        третьего лица, вы гарантируете, что имете соответствующие полномочия на указание его данных и указываете
        соответствующие данные от его имени.', 'alert-error');
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_payer', 'Сохранить', 'Сохраняет данные плательщика');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_payer', 'Отправить', 'Отправить данные плательщика на рассмотрение модератору');
                break;
            case DB_Agreement::STATUS_SENT_PAYER_DATA:
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит введенные вами данные</h5>';
                break;
            case DB_Agreement::STATUS_DISALLOWED:
                echo '<h5>Модератор отказал вам в праве заключения договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
        }
        */
        switch ($data['status']) {
            case DB_Agreement::STATUS_SENT_PAYER_DATA:
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит введенные вами данные</h5>';
                break;
            case DB_Agreement::STATUS_DISALLOWED:
                echo '<h5>Модератор отказал вам в праве заключения договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
            case DB_Agreement::STATUS_ALLOWED:
            case DB_Agreement::STATUS_SAVED_SCANS:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS]);
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_scans', 'Сохранить', 'Сохраняет прикрепленные скан-коппии в базу данных');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_scans', 'Отправить', 'Сохраняет и отправляет прикрепленные скан-коппии на проверку модератору');
                break;
            case DB_Agreement::STATUS_SENT_SCANS:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит отправленные вами документы</h5>';
                break;
            case DB_Agreement::STATUS_APPROVED:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Модератор одобрил и подтвердил заключение договора.<h5>';
                break;
            case DB_Agreement::STATUS_REJECTED:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Модератор отказал вам в заключении договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
            case DB_Agreement::STATUS_CREATED:
            case DB_Agreement::STATUS_SAVED_PAYER_DATA:
            default:
                echo HTML_Helper::setAlert('<h5>Осуществляя сохранение или отправку данных заказчика в личном кабинете, вы
            гарантируете, что действуете от своего имени и в своих интересах. В случае указания вами в качестве
            заказчика третьего лица, вы гарантируете, что имете соответствующие полномочия на указание его данных и
            указываете соответствующие данные от его имени.</h5>', 'alert-danger');
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_payer', 'Сохранить', 'Сохраняет данные плательщика');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_payer', 'Отправить', 'Отправить данные плательщика на рассмотрение модератору');
                break;
        }
        echo '</div></div>';
        break;
    case DB_Agreement::PAYER_SELF:
        echo 'Модератор принимает решение на основе данных, введенных вами в Анкете (Шаг 1)<br>';
        /*
        switch ($data['status']) {
            case DB_Agreement::STATUS_CREATED:
            case DB_Agreement::STATUS_SAVED_PAYER_DATA:
                break;
            case DB_Agreement::STATUS_SENT_PAYER_DATA:
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит введенные вами данные</h5>';
                break;
            case DB_Agreement::STATUS_DISALLOWED:
                echo '<h5>Модератор отказал вам в праве заключения договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
        }
        */
        switch ($data['status']) {
            case DB_Agreement::STATUS_SENT_PAYER_DATA:
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит введенные вами данные</h5>';
                break;
            case DB_Agreement::STATUS_DISALLOWED:
                echo '<h5>Модератор отказал вам в праве заключения договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
            case DB_Agreement::STATUS_ALLOWED:
            case DB_Agreement::STATUS_SAVED_SCANS:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS]);
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_scans', 'Сохранить', 'Сохраняет прикрепленные скан-коппии в базу данных');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_scans', 'Отправить', 'Сохраняет и отправляет прикрепленные скан-коппии на проверку модератору');
                break;
            case DB_Agreement::STATUS_SENT_SCANS:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Пожалуйста, ожидайте, пока модератор проверит отправленные вами документы</h5>';
                break;
            case DB_Agreement::STATUS_APPROVED:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Модератор одобрил и подтвердил заключение договора.<h5>';
                break;
            case DB_Agreement::STATUS_REJECTED:
                echo Form_Helper::setFormHeaderSub('Загрузка документов');

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAgreement/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить договор', 1);
                echo "<span style='color:red'> <strong>Загрузить договор</strong></span>";
                echo '</div></div>';

                if ($agreement->has_supply_agreement == 1) {
                    echo '<div class="row"><div class="col">';
                    echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadAddition/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить доп. соглашение', 1);
                    echo "<span style='color:red'> <strong>Загрузить доп. соглашение</strong></span>";
                    echo '</div></div>';
                }

                echo '<div class="row"><div class="col">';
                echo HTML_Helper::setHrefButtonIcon(AGREEMENT['ctr'], 'DownloadBill/?id=' . $data['id'], 'font-weight-bold', 'fas fa-print fa-3x', 'Загрузить квитанцию', 1);
                echo "<span style='color:red'> <strong>Загрузить квитанцию</strong></span>";
                echo '</div></div>';

                echo Form_Helper::setFormHeaderSub('Скан-копии');
                echo Form_Helper::setFormFileListDB(['required' => 'required',
                    'required_style' => 'StarUp',
                    'model_class' => 'common\\models\\Model_DictScans',
                    'model_method' => 'getByDocument',
                    'model_filter' => 'doc_code',
                    'model_filter_var' => 'agreement',
                    'model_field' => 'scan_code',
                    'model_field_name' => 'scan_name',
                    'data' => $data,
                    'home_ctr' => AGREEMENT['ctr'],
                    'home_hdr' => AGREEMENT['hdr'],
                    'home_act' => 'Index',
                    'ext' => FILES_EXT_SCANS,
                    'editable' => FALSE]);
                echo '<h5>Модератор отказал вам в заключении договора. Детали указаны в комментарии в верхней части этой страницы<h5>';
                break;
            case DB_Agreement::STATUS_CREATED:
            case DB_Agreement::STATUS_SAVED_PAYER_DATA:
            default:
                echo HTML_Helper::setAlert('<h5>Осуществляя сохранение или отправку данных заказчика в личном кабинете, вы
            гарантируете, что действуете от своего имени и в своих интересах. В случае указания вами в качестве
            заказчика третьего лица, вы гарантируете, что имете соответствующие полномочия на указание его данных и
            указываете соответствующие данные от его имени.</h5>', 'alert-danger');
                echo HTML_Helper::setSubmit('btn btn-info', 'btn_save_payer', 'Сохранить', 'Сохраняет данные плательщика');
                echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_payer', 'Отправить', 'Отправить данные плательщика на рассмотрение модератору');
                break;
        }
        break;
    default:
        break;
}
echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную');
echo Form_Helper::setFormEnd();
echo '</div>';
?>

<div class="modal fade" id="helpResume" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Информация о плательщике (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-justify">
                <?php /*echo Help_Helper::resume_help();*/ ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        formInit();
        formEvents();
    });
</script>

<script>
    // form init
    var lang_count = 0;

    function formInit() {
        lang_count = 0;
        // agreement
        if (getAge($('#birth_dt').val()) < 18) {
            $('#agreement_div').show();
        } else {
            $('#agreement_div').hide();
        }
        // citizenship
        if ($('#citizenship').val() == '000') {
            $('#citizenship').prop('disabled', true);
        } else {
            $('#citizenship').prop('disabled', false);
        }
        setCitizenship($('#citizenship').val());
        // passport
        setPassport();
        // old passport yes
        if ($('#passport_old_yes').prop('checked')) {
            $('#passport_old_div').show();
        } else {
            $('#passport_old_div').hide();
        }
        // old passport
        setPassportOld();
        // address registration
        var country_reg = $('#country_reg').val();
        if (country_reg == '') {
            $('#kladr_reg').hide();
            $('#address_reg').prop('disabled', true);
            $('#address_reg_clone').hide();
        } else {
            switch (country_reg) {
                case '000':
                    $('#kladr_reg').hide();
                    $('#homeless_reg').prop('checked', true);
                    $('#address_reg').prop('disabled', true);
                    break;
                case '643':
                    $('#kladr_reg').show();
                    $('#homeless_reg').prop('checked', false);
                    $('#address_reg').prop('disabled', true);
                    break;
                default:
                    $('#kladr_reg').hide();
                    $('#homeless_reg').prop('checked', false);
                    $('#address_reg').prop('disabled', false);
                    $('#address_reg_clone').show();
            }
        }
        if ($('#region_reg').val() == '' && jQuery.isEmptyObject($('#area_reg').val()) && jQuery.isEmptyObject($('#city_reg').val()) && jQuery.isEmptyObject($('#location_reg').val()) && jQuery.isEmptyObject($('#street_reg').val()) && $('#house_reg').val() == '' && $('#building_reg').val() == '' && $('#flat_reg').val() == '' && $('#postcode_reg').val() == '' && $('#address_reg').val() != '') {
            $('#kladr_reg_not').prop('checked', true);
        } else {
            $('#kladr_reg_not').prop('checked', false);
        }
        if ($('#kladr_reg_not').prop('checked')) {
            $('#kladr_reg').hide();
            $('#address_reg').prop('disabled', false);
        } else {
            $('#kladr_reg').show();
            $('#address_reg').prop('disabled', true);
        }
        // address residential
        var country_res = $('#country_res').val();
        if (country_res == '') {
            $('#kladr_res').hide();
            $('#address_res').prop('disabled', true);
        } else {
            switch (country_res) {
                case '000':
                    $('#kladr_res').hide();
                    $('#homeless_res').prop('checked', true);
                    $('#address_res').prop('disabled', true);
                    break;
                case '643':
                    $('#kladr_res').show();
                    $('#homeless_res').prop('checked', false);
                    $('#address_res').prop('disabled', true);
                    break;
                default:
                    $('#kladr_res').hide();
                    $('#homeless_res').prop('checked', false);
                    $('#address_res').prop('disabled', false);
                    $('#address_res_clone').show();
            }
        }
        if ($('#region_res').val() == '' && jQuery.isEmptyObject($('#area_res').val()) && jQuery.isEmptyObject($('#city_res').val()) && jQuery.isEmptyObject($('#location_res').val()) && jQuery.isEmptyObject($('#street_res').val()) && $('#house_res').val() == '' && $('#building_res').val() == '' && $('#flat_res').val() == '' && $('#postcode_res').val() == '' && $('#address_res').val() != '') {
            $('#kladr_res_not').prop('checked', true);
        } else {
            $('#kladr_res_not').prop('checked', false);
        }
        if ($('#kladr_res_not').prop('checked')) {
            $('#kladr_res').hide();
            $('#address_res').prop('disabled', false);
        } else {
            $('#kladr_res').show();
            $('#address_res').prop('disabled', true);
        }

        // KLADR res
        if ($('#address_reg').val() != '' && $('#address_res').val() == '' && !$('#address_reg_clone_flag').prop('checked')) {
            cloneAddressRegistration();
        }

        if ($('#address_reg').val() != '' && $('#address_reg').val() == $('#address_res').val()) {
            $('#address_reg_clone_flag').prop('checked', true);
            CountryResHide();
            AddressResHide();
        } else {
            $('#address_reg_clone_flag').prop('checked', false);
            CountryResShow();
            if ($('#address_res').val() == '') {
                AddressResHide();
            } else {
                AddressResShow();
            }
        }
    }
</script>

<script>
    // form events
    function formEvents() {
        // passport
        $('#passport_type').change(function () {
            $('#series').val('');
            $('#numb').val('');
            $('#dt_issue').val('');
            $('#unit_name').val('');
            $('#unit_code').val('');
            $('#dt_end').val('');
            unsetPassport();
            setPassport();
        });

        // kladr_reg not found
        $('#kladr_reg_not').change(function () {
            $('#homeless_reg').prop('checked', false)
            if ($('#kladr_reg_not').prop('checked')) {
                $('#kladr_reg').hide();
                $('#address_reg').val('');
                $('#address_reg').prop('disabled', false);
            } else {
                $('#kladr_reg').show();
                $('#address_reg').val('');
                $('#address_reg').prop('disabled', true);
            }
        });

        // address_reg_clone
        $('#address_reg_clone_flag').change(function () {
            if ($('#address_reg_clone_flag').prop('checked')) {
                // clone registration address
                cloneAddressRegistration();
            } else {
                // clear residential address
                CountryResShow();
                AddressResShow();
                $('#country_res').val('');
                $('#region_res').empty();
                $('#area_res').empty();
                $('#city_res').empty();
                $('#location_res').empty();
                $('#street_res').empty();
                $('#house_res').val('');
                $('#building_res').val('');
                $('#flat_res').val('');
                $('#postcode_res').val('');
                $('#address_res').val('');
                $('#address_res').prop('disabled', false);
            }
        });

        // kladr_res not found
        $('#kladr_res_not').change(function () {
            $('#homeless_res').prop('checked', false)
            if ($('#kladr_res_not').prop('checked')) {
                $('#country_res').val('');
                $('#kladr_res').hide();
                $('#address_res').val('');
                $('#address_res').prop('disabled', false);
            } else {
                $('#kladr_res').show();
                $('#address_res').val('');
                $('#address_res').prop('disabled', true);
            }
        });

        // homeless_res
        $('#homeless_res').change(function () {
            $('#kladr_res_not').prop('checked', false)
            $('#address_res').prop('disabled', true);
            if ($('#homeless_res').prop('checked')) {
                $('#country_res').val('000');
                $('#kladr_res').hide();
                $('#address_res').val('Не имею адреса проживания.');
            } else {
                $('#country_res').val('');
                $('#kladr_res').hide();
                $('#address_res').val('');
            }
        });

        // submit click
        $('#btn_save_payer').click(function () {
            $('#citizenship').prop('disabled', false);
            $('#postcode_reg').prop('disabled', false);
            $('#address_reg').prop('disabled', false);
            $('#postcode_res').prop('disabled', false);
            $('#address_res').prop('disabled', false);
        });
    }

    function setCitizenship(citizenship, renew) {
        var passport_type = $('#passport_type').val();
        switch (citizenship) {
            case '':
                $('#citizenship_not').prop('checked', false);
                disablePassport(true);
                break;
            case '000':
                $('#citizenship_not').prop('checked', true);
                disablePassport(false);
                getPassportAJAX('/frontend/DictDoctypes/PassportsBsuJSON', '#passport_type', passport_type);
                break;
            case '643':
                $('#citizenship_not').prop('checked', false);
                disablePassport(false);
                getPassportAJAX('/frontend/DictDoctypes/PassportsRussianJSON', '#passport_type', passport_type);
                break;
            default:
                $('#citizenship_not').prop('checked', false);
                disablePassport(false);
                getPassportAJAX('/frontend/DictDoctypes/PassportsForeignJSON', '#passport_type', passport_type);
                break;
        }
    }

    function setPassport() {
        if ($('#passport_type').val() != '') {
            switch ($('#passport_type').val()) {
                // Паспорт РФ
                case '000000047':
                    $("label[for='series']").html('Серия*');
                    $('#series').mask('9999');
                    $("label[for='numb']").html('Номер*');
                    $('#numb').mask('999999');
                    $("label[for='dt_issue']").html('Дата выдачи*');
                    $("label[for='unit_name']").html('Наименование подразделения*');
                    $("label[for='unit_code']").html('Код подразделения*');
                    $('#unit_code').mask('999-999');
                    $("label[for='dt_end']").html('Дата окончания действия');
                    //TODO паша добавил скрытие поля
                    $("label[for='dt_end']").hide();
                    $("#dt_end").hide();
                    $("label[for='passport_face']").html('Первая страница паспорта*');
                    $('#passport_face_div').show();
                    $("label[for='passport_reg']").html('Страница паспорта с регистрацией*');
                    $('#passport_reg_div').show();
                    $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
                    $('#passport_foreign_face_div').hide();
                    $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
                    $('#passport_foreign_reg_div').hide();
                    $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
                    $('#passport_foreign_rus_div').hide();
                    $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
                    $('#passport_pforeign_face_div').hide();
                    $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
                    $('#passport_pforeign_rus_div').hide();
                    $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
                    $('#residency_foreign_face_div').hide();
                    $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
                    $('#residency_foreign_reg_div').hide();
                    $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
                    $('#id_russian_div').hide();
                    $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_face_div').hide();
                    $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_back_div').hide();
                    $("label[for='certificate_birth']").html('Свидетельство о рождении');
                    $('#certificate_birth_div').hide();
                    $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
                    $('#certificate_pbirth_div').hide();
                    break;
                // Паспорт иностранного гражданина
                case '000000049':
                    $("label[for='series']").html('Серия');
                    $('#series').unmask();
                    $("label[for='numb']").html('Номер*');
                    $('#numb').unmask();
                    $("label[for='dt_issue']").html('Дата выдачи*');
                    $("label[for='unit_name']").html('Наименование подразделения*');
                    $("label[for='unit_code']").html('Код подразделения');
                    $('#unit_code').unmask();
                    $("label[for='dt_end']").html('Дата окончания действия');
                    //TODO паша добавил скрытие поля
                    $("label[for='dt_end']").hide();
                    $("#dt_end").hide();
                    $("label[for='passport_face']").html('Первая страница паспорта');
                    $('#passport_face_div').hide();
                    $("label[for='passport_reg']").html('Страница паспорта с регистрацией');
                    $('#passport_reg_div').hide();
                    $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина*');
                    $('#passport_foreign_face_div').show();
                    $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
                    $('#passport_foreign_reg_div').show();
                    $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
                    $('#passport_foreign_rus_div').show();
                    $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
                    $('#passport_pforeign_face_div').show();
                    $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
                    $('#passport_pforeign_rus_div').show();
                    $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
                    $('#residency_foreign_face_div').hide();
                    $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
                    $('#residency_foreign_reg_div').hide();
                    $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
                    $('#id_russian_div').hide();
                    $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_face_div').hide();
                    $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_back_div').hide();
                    $("label[for='certificate_birth']").html('Свидетельство о рождении');
                    $('#certificate_birth_div').show();
                    $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
                    $('#certificate_pbirth_div').show();
                    break;
                // Вид на жительство иностранного гражданина
                case '000000075':
                    $("label[for='series']").html('Серия*');
                    $('#series').unmask();
                    $("label[for='numb']").html('Номер*');
                    $('#numb').unmask();
                    $("label[for='dt_issue']").html('Дата выдачи*');
                    $("label[for='unit_name']").html('Наименование подразделения*');
                    $("label[for='unit_code']").html('Код подразделения');
                    $('#unit_code').unmask();
                    $("label[for='dt_end']").html('Дата окончания действия*');
                    //TODO паша добавил отображение поля
                    $("label[for='dt_end']").show();
                    $("#dt_end").show();
                    $('#unit_code').unmask();
                    $("label[for='passport_face']").html('Первая страница паспорта');
                    $('#passport_face_div').hide();
                    $("label[for='passport_reg']").html('Страница паспорта с регистрацией');
                    $('#passport_reg_div').hide();
                    $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
                    $('#passport_foreign_face_div').hide();
                    $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
                    $('#passport_foreign_reg_div').hide();
                    $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
                    $('#passport_foreign_rus_div').hide();
                    $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
                    $('#passport_pforeign_face_div').show();
                    $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
                    $('#passport_pforeign_rus_div').show();
                    $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина*');
                    $('#residency_foreign_face_div').show();
                    $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина*');
                    $('#residency_foreign_reg_div').show();
                    $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
                    $('#id_russian_div').hide();
                    $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_face_div').hide();
                    $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_back_div').hide();
                    $("label[for='certificate_birth']").html('Свидетельство о рождении');
                    $('#certificate_birth_div').show();
                    $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
                    $('#certificate_pbirth_div').show();
                    break;
                // Временное удостоверение личности гражданина РФ
                case '000000202':
                    $("label[for='series']").html('Серия');
                    $('#series').unmask();
                    $("label[for='numb']").html('Номер*');
                    $('#numb').unmask();
                    $("label[for='dt_issue']").html('Дата выдачи*');
                    $("label[for='unit_name']").html('Наименование подразделения*');
                    $("label[for='unit_code']").html('Код подразделения');
                    $('#unit_code').unmask();
                    $("label[for='dt_end']").html('Дата окончания действия*');
                    //TODO паша добавил отображение поля
                    $("label[for='dt_end']").show();
                    $("#dt_end").show();
                    $("label[for='passport_face']").html('Первая страница паспорта');
                    $('#passport_face_div').hide();
                    $("label[for='passport_reg']").html('Страница паспорта с регистрацией');
                    $('#passport_reg_div').hide();
                    $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
                    $('#passport_foreign_face_div').hide();
                    $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
                    $('#passport_foreign_reg_div').hide();
                    $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
                    $('#passport_foreign_rus_div').hide();
                    $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
                    $('#passport_pforeign_face_div').hide();
                    $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
                    $('#passport_pforeign_rus_div').hide();
                    $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
                    $('#residency_foreign_face_div').hide();
                    $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
                    $('#residency_foreign_reg_div').hide();
                    $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ*');
                    $('#id_russian_div').show();
                    $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_face_div').hide();
                    $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_back_div').hide();
                    $("label[for='certificate_birth']").html('Свидетельство о рождении');
                    $('#certificate_birth_div').hide();
                    $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
                    $('#certificate_pbirth_div').hide();
                    break;
                // Удостоверение личности иностранного гражданина
                case '000000223':
                    $("label[for='series']").html('Серия');
                    $('#series').unmask();
                    $("label[for='numb']").html('Номер*');
                    $('#numb').unmask();
                    $("label[for='dt_issue']").html('Дата выдачи*');
                    $("label[for='unit_name']").html('Наименование подразделения');
                    $("label[for='unit_code']").html('Код подразделения');
                    $('#unit_code').unmask();
                    $("label[for='dt_end']").html('Дата окончания действия*');
                    //TODO паша добавил отображение поля
                    $("label[for='dt_end']").show();
                    $("#dt_end").show();
                    $("label[for='passport_face']").html('Первая страница паспорта');
                    $('#passport_face_div').hide();
                    $("label[for='passport_reg']").html('Страница паспорта с регистрацией');
                    $('#passport_reg_div').hide();
                    $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
                    $('#passport_foreign_face_div').hide();
                    $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
                    $('#passport_foreign_reg_div').hide();
                    $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
                    $('#passport_foreign_rus_div').hide();
                    $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
                    $('#passport_pforeign_face_div').show();
                    $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
                    $('#passport_pforeign_rus_div').show();
                    $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
                    $('#residency_foreign_face_div').hide();
                    $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
                    $('#residency_foreign_reg_div').hide();
                    $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
                    $('#id_russian_div').hide();
                    $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина*');
                    $('#id_foreign_face_div').show();
                    $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина*');
                    $('#id_foreign_back_div').show();
                    $("label[for='certificate_birth']").html('Свидетельство о рождении');
                    $('#certificate_birth_div').hide();
                    $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
                    $('#certificate_pbirth_div').show();
                    break;
                // Свидетельство о рождении, выданное уполномоченным органом иностранного государства
                case '000000226':
                    $("label[for='series']").html('Серия*');
                    $('#series').unmask();
                    $("label[for='numb']").html('Номер*');
                    $('#numb').unmask();
                    $("label[for='dt_issue']").html('Дата выдачи*');
                    $("label[for='unit_name']").html('Наименование подразделения*');
                    $("label[for='unit_code']").html('Код подразделения');
                    $('#unit_code').unmask();
                    $("label[for='dt_end']").html('Дата окончания действия');
                    //TODO паша добавил скрытие поля
                    $("label[for='dt_end']").hide();
                    $("#dt_end").hide();
                    $("label[for='passport_face']").html('Первая страница паспорта');
                    $('#passport_face_div').hide();
                    $("label[for='passport_reg']").html('Страница паспорта с регистрацией');
                    $('#passport_reg_div').hide();
                    $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
                    $('#passport_foreign_face_div').hide();
                    $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
                    $('#passport_foreign_reg_div').hide();
                    $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
                    $('#passport_foreign_rus_div').hide();
                    $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
                    $('#passport_pforeign_face_div').show();
                    $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
                    $('#passport_pforeign_rus_div').show();
                    $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
                    $('#residency_foreign_face_div').hide();
                    $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
                    $('#residency_foreign_reg_div').hide();
                    $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
                    $('#id_russian_div').hide();
                    $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_face_div').hide();
                    $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
                    $('#id_foreign_back_div').hide();
                    $("label[for='certificate_birth']").html('Свидетельство о рождении*');
                    $('#certificate_birth_div').show();
                    $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
                    $('#certificate_pbirth_div').show();
                    break;
            }
        } else {
            $("label[for='series']").html('Серия');
            $('#series').unmask();
            $("label[for='numb']").html('Номер');
            $('#numb').unmask();
            $("label[for='dt_issue']").html('Дата выдачи');
            $("label[for='unit_name']").html('Наименование подразделения');
            $("label[for='unit_code']").html('Код подразделения');
            $('#unit_code').unmask();
            $("label[for='dt_end']").html('Дата окончания действия');
            //TODO паша добавил скрытие поля
            $("label[for='dt_end']").hide();
            $("#dt_end").hide();
            $("label[for='passport_face']").html('Первая страница паспорта');
            $('#passport_face_div').hide();
            $("label[for='passport_reg']").html('Страница паспорта с регистрацией');
            $('#passport_reg_div').hide();
            $("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
            $('#passport_foreign_face_div').hide();
            $("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
            $('#passport_foreign_reg_div').hide();
            $("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
            $('#passport_foreign_rus_div').hide();
            $("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
            $('#passport_pforeign_face_div').hide();
            $("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
            $('#passport_pforeign_rus_div').hide();
            $("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
            $('#residency_foreign_face_div').hide();
            $("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
            $('#residency_foreign_reg_div').hide();
            $("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
            $('#id_russian_div').hide();
            $("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
            $('#id_foreign_face_div').hide();
            $("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
            $('#id_foreign_back_div').hide();
            $("label[for='certificate_birth']").html('Свидетельство о рождении');
            $('#certificate_birth_div').hide();
            $("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
            $('#certificate_pbirth_div').hide();
        }
    }

    function getPassportAJAX(url, select, val) {
        startLoadingAnimation();
        $.ajax({
            url: url,
            type: 'POST',
            data: {format: 'json'},
            dataType: 'json',
            success: function (result) {
                $(select).empty();
                $(select).append('<option></option>');
                $.each(result, function (key, value) {
                    if (val == value.code) {
                        $(select).append('<option value="' + value.code + '" selected>' + value.description + '</option>');
                    } else {
                        $(select).append('<option value="' + value.code + '">' + value.description + '</option>');
                    }
                });
            },
            error: function (xhr, status, error) {
                console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
            }
        });
        stopLoadingAnimation();
    }

    function cloneAddressRegistration() {
        var country_reg = $('#country_reg').val();
        var address_reg = $('#address_reg').val();
        if (country_reg != '' && address_reg != '') {
            $('#country_res').val(country_reg);
            if (country_reg == '643') {
                $('#kladr_res').show();
                $('#address_res').prop('disabled', true);
                $('#address_res').val(address_reg);
                // renew kladr_res
                var region_reg = $('#region_reg').val();
                var area_reg = $('#area_reg').val();
                var city_reg = $('#city_reg').val();
                var location_reg = $('#location_reg').val();
                var street_reg = $('#street_reg').val();
                if (region_reg != '' && !jQuery.isEmptyObject(region_reg)) {
                    getKladrAJAX('/frontend/Kladr/RegionAllJSON', null, '#region_res', region_reg);
                    // area
                    if (area_reg != '' && !jQuery.isEmptyObject(area_reg)) {
                        getKladrAJAX('/frontend/Kladr/AreaByRegionJSON', region_reg, '#area_res', area_reg);
                        if (city_reg != '' && !jQuery.isEmptyObject(city_reg)) {
                            getKladrAJAX('/frontend/Kladr/CityByAreaJSON', area_reg, '#city_res', city_reg);
                            if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
                                getKladrAJAX('/frontend/Kladr/LocationByCityJSON', city_reg, '#location_res', city_reg);
                            } else {
                                $('#location_res').empty();
                                if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
                                    getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city_reg, '#street_res', street_reg);
                                } else {
                                    $('#street_res').empty();
                                }
                            }
                        } else {
                            $('#city_res').empty();
                            if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
                                getKladrAJAX('/frontend/Kladr/LocationByAreaJSON', area_reg, '#location_res', location_reg);
                                if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
                                    getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', location_reg, '#street_res', street_reg);
                                } else {
                                    $('#street_res').empty();
                                }
                            } else {
                                $('#location_res').empty();
                            }
                        }
                    } else {
                        $('#area_res').empty();
                        // city
                        if (city_reg != '' && !jQuery.isEmptyObject(city_reg)) {
                            getKladrAJAX('/frontend/Kladr/CityByRegionJSON', region_reg, '#city_res', city_reg);
                            if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
                                getKladrAJAX('/frontend/Kladr/LocationByCityJSON', city_reg, '#location_res', city_reg);
                            } else {
                                $('#location_res').empty();
                                if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
                                    getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city_reg, '#street_res', street_reg);
                                } else {
                                    $('#street_res').empty();
                                }
                            }
                        } else {
                            $('#city_res').empty();
                        }
                        // location
                        if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
                            getKladrAJAX('/frontend/Kladr/LocationByRegionJSON', region_reg, '#location_res', location_reg);
                            if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
                                getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', street_reg, '#street_res', street_reg);
                            } else {
                                $('#street_res').empty();
                            }
                        } else {
                            $('#location_res').empty();
                            if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
                                getKladrAJAX('/frontend/Kladr/StreetByRegionJSON', region_reg, '#street_res', street_reg);
                            } else {
                                $('#street_res').empty();
                            }
                        }
                    }
                } else {
                    $('#region_res').empty();
                    $('#area_res').empty();
                    $('#city_res').empty();
                    $('#location_res').empty();
                    $('#street_res').empty();
                }
                $('#house_res').val($('#house_reg').val());
                $('#building_res').val($('#building_reg').val());
                $('#flat_res').val($('#flat_reg').val());
                $('#postcode_res').val($('#postcode_reg').val());
                CountryResHide();
                AddressResHide();
            } else {
                $('#kladr_res').hide();
                $('#address_res').prop('disabled', false);
                $('#address_res').val(address_reg);
            }
        }
    }

    function CountryResShow() {
        $("label[for='country_res']").show();
        $('#country_res').show();
    }

    function CountryResHide() {
        $("label[for='country_res']").hide();
        $('#country_res').hide();
    }

    function ChangeCountry(adr) {
        var country = $('#country' + adr).val();
        if (country == '643') {
            // MOTHER LAND
            // prepare
            $('#kladr' + adr).show();
            getKladrAJAX('/frontend/Kladr/RegionAllJSON', null, '#region' + adr);
            $('#house' + adr).prop('disabled', true);
            $('#building' + adr).prop('disabled', true);
            $('#flat' + adr).prop('disabled', true);
            $('#postcode' + adr).prop('disabled', true);
            $('#address' + adr).prop('disabled', true);
            // clear
            $('#area' + adr).empty();
            $('#city' + adr).empty();
            $('#location' + adr).empty();
            $('#street' + adr).empty();
            $('#house' + adr).val('');
            $('#building' + adr).val('');
            $('#flat' + adr).val('');
            $('#postcode' + adr).val('');
            $('#address' + adr).val('');
        } else {
            // FOREIGN LAND
            $('#kladr' + adr).hide();
            $('#address' + adr).prop('disabled', false);
            $('#address' + adr).val('');
        }
        AddressClone(adr);
    }

    function ChangeRegion(adr) {
        var region = $('#region' + adr).val();
        // prepare
        getKladrAJAX('/frontend/Kladr/AreaByRegionJSON', region, '#area' + adr);
        getKladrAJAX('/frontend/Kladr/CityByRegionJSON', region, '#city' + adr);
        getKladrAJAX('/frontend/Kladr/LocationByRegionJSON', region, '#location' + adr);
        getKladrAJAX('/frontend/Kladr/StreetByRegionJSON', region, '#street' + adr);
        // clear
        $('#location' + adr).empty();
        $('#street' + adr).empty();
        $('#house' + adr).val('');
        $('#building' + adr).val('');
        $('#flat' + adr).val('');
        // get postcode
        getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region, '#postcode' + adr);
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function ChangeArea(adr) {
        var region = $('#region' + adr).val();
        var area = $('#area' + adr).val();
        // prepare
        getKladrAJAX('/frontend/Kladr/CityByAreaJSON', area, '#city' + adr);
        getKladrAJAX('/frontend/Kladr/LocationByAreaJSON', area, '#location' + adr);
        // clear
        $('#street' + adr).empty();
        $('#house' + adr).val('');
        $('#building' + adr).val('');
        $('#flat' + adr).val('');
        // get postcode
        if (area != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area, '#postcode' + adr);
        } else {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region, '#postcode' + adr);
        }
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function ChangeCity(adr) {
        var region = $('#region' + adr).val();
        var area = $('#area' + adr).val();
        var city = $('#city' + adr).val();
        // prepare
        $('#location' + adr).empty();
        getKladrAJAX('/frontend/Kladr/LocationByCityJSON', city, '#location' + adr);
        getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city, '#street' + adr);
        // clear
        $('#house' + adr).val('');
        $('#building' + adr).val('');
        $('#flat' + adr).val('');
        // get postcode
        if (city != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', city, '#postcode' + adr);
        } else if (area != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area, '#postcode' + adr);
        } else {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region, '#postcode' + adr);
        }
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function ChangeLocation(adr) {
        var region = $('#region' + adr).val();
        var area = $('#area' + adr).val();
        var city = $('#city' + adr).val();
        var location = $('#location' + adr).val();
        // prepare
        getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', location, '#street' + adr);
        // clear
        $('#house' + adr).val('');
        $('#building' + adr).val('');
        $('#flat' + adr).val('');
        // get postcode
        if (location != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', location, '#postcode' + adr);
        }
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function ChangeStreet(adr) {
        var region = $('#region' + adr).val();
        var area = $('#area' + adr).val();
        var city = $('#city' + adr).val();
        var location = $('#location' + adr).val();
        var street = $('#street' + adr).val();
        // prepare
        $('#house' + adr).prop('disabled', false);
        $('#building' + adr).prop('disabled', false);
        // clear
        $('#house' + adr).val('');
        $('#building' + adr).val('');
        $('#flat' + adr).val('');
        // get postcode
        if (street != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', street, '#postcode' + adr);
        } else if (location != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', location, '#postcode' + adr);
        } else if (city != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', city, '#postcode' + adr);
        } else if (area != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area, '#postcode' + adr);
        } else {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region, '#postcode' + adr);
        }
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function ChangeHouse(adr) {
        var region = $('#region' + adr).val();
        var area = $('#area' + adr).val();
        var city = $('#city' + adr).val();
        var location = $('#location' + adr).val();
        var street = $('#street' + adr).val();
        var house = $('#house' + adr).val();
        // prepare
        $('#building' + adr).prop('disabled', false);
        $('#flat' + adr).prop('disabled', false);
        // clear
        $('#building' + adr).val('');
        $('#flat' + adr).val('');
        // get postcode
        if (house != '') {
            getHousePostcode('/frontend/Kladr/HouseByStreetJSON', street, house, '#postcode' + adr);
        } else if (street != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', street, '#postcode' + adr);
        } else if (location != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', location, '#postcode' + adr);
        } else if (city != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', city, '#postcode' + adr);
        } else if (area != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area, '#postcode' + adr);
        } else {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region, '#postcode' + adr);
        }
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function ChangeBuilding(adr) {
        var region = $('#region' + adr).val();
        var area = $('#area' + adr).val();
        var city = $('#city' + adr).val();
        var location = $('#location' + adr).val();
        var street = $('#street' + adr).val();
        var house = $('#house' + adr).val();
        var building = $('#building' + adr).val();
        // prepare
        $('#flat_reg').prop('disabled', false);
        // clear
        $('#flat_reg').val('');
        // get postcode
        if (building != '') {
            if (house != '') {
                getHousePostcode('/frontend/Kladr/HouseByStreetJSON', street, house + 'к' + building, '#postcode' + adr);
            }
        } else if (house != '') {
            getHousePostcode('/frontend/Kladr/HouseByStreetJSON', street, house, '#postcode' + adr);
        } else if (street != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', street, '#postcode' + adr);
        } else if (location != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', location, '#postcode' + adr);
        } else if (city != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', city, '#postcode' + adr);
        } else if (area != '') {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area, '#postcode' + adr);
        } else {
            getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region, '#postcode' + adr);
        }
        // address
        AddressCreate(adr);
        AddressClone(adr);
    }

    function AddressCreate(adr) {
        var address;
        var region_name = $('#region' + adr + ' :selected').text();
        var area_name = $('#area' + adr + ' :selected').text();
        var city_name = $('#city' + adr + ' :selected').text();
        var location_name = $('#location' + adr + ' :selected').text();
        var street_name = $('#street' + adr + ' :selected').text();
        var house = $('#house' + adr).val();
        var building = $('#building' + adr).val();
        var flat = $('#flat' + adr).val();
        var postcode = $('#postcode' + adr).val();
        // region
        if (region_name != '') {
            address = region_name;
        }
        // area
        if (area_name != '') {
            address = address + ', ' + area_name;
        }
        // city
        if (city_name != '') {
            address = address + ', ' + city_name;
        }
        // location
        if (location_name != '') {
            address = address + ', ' + location_name;
        }
        // street
        if (street_name != '') {
            address = address + ', ' + street_name;
        }
        // house
        if (house != '') {
            address = address + ', дом ' + house;
        }
        // building
        if (building != '') {
            address = address + ', корпус ' + building;
        }
        // flat
        if (flat != '') {
            address = address + ', квартира ' + flat;
        }
        // postcode
        if (postcode != '') {
            address = postcode + ', ' + address;
        }
        // address
        $('#address' + adr).val(address);
    }

    function AddressClone(adr) {
        if (adr == '_reg') {
            $('#address_reg_clone').show();
            AddressResShow();
            if ($('#address_res').val() != '') {
                $('#address_reg_clone_flag').prop('checked', false);
            }
            if ($('#address_reg_clone_flag').prop('checked')) {
                cloneAddressRegistration();
            }
        }
        if (adr == '_res') {
            $('#address_reg_clone').show();
            $('#address_reg_clone_flag').prop('checked', false);
        }
    }

    function AddressResShow() {
        $('#kladr_res').show();
        $('#kladr_res_not').show();
        $("label[for='kladr_res_not']").show();
        $('#homeless_res').show();
        $("label[for='homeless_res']").show();
    }

    function AddressResHide() {
        $('#kladr_res').hide();
        $('#kladr_res_not').hide();
        $("label[for='kladr_res_not']").hide();
        $('#homeless_res').hide();
        $("label[for='homeless_res']").hide();
    }

    function getAge(dateString) {
        var day = parseInt(dateString.substring(0, 2));
        var month = parseInt(dateString.substring(3, 5));
        var year = parseInt(dateString.substring(6, 10));
        var today = new Date();
        var birthDate = new Date(year, month - 1, day);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }
</script>

<script>
    $(function () {
        $("#birth_dt").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ"});
        $("#phone_number").mask("+7(999) 999-99-99");
        $("#dt_issue").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ"});
        $("#dt_end").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ"});
        $("#dt_issue_old").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ"});
        $("#dt_end_old").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ"});
        $("#postcode_reg").mask("999999");
        $("#postcode_res").mask("999999");
    });
</script>
