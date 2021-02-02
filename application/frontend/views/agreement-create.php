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
    Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', NULL, nl2br('У вас уже есть договор на основании заявления '.$data['id']));
}

$agree = new \common\models\Model_Agreement();
$agree->id_confirm = $data['id'];
$agree_rows = $agree->getAllByConfirmId();

$has_active = FALSE;
foreach ($agree_rows as $agree_row) {
    if($agree_row['active'] == 1) {
        $has_active = TRUE;
    }
}

if($has_active) {
    Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'У вас уже есть договор на основании заявления '.$data['id']);
}

$debug = FALSE;
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <?php
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    /* comment */
    echo HTML_Helper::setAlert(
        '<p>В качестве Заказчика по договору об оказании платных образовательных услуг указывается лицо, которое фактически будет производить оплату за обучение.</p>
<p>При оплате образовательных услуг путем внесения наличных денежных средств в документе, подтверждающем оплату обучения, указывается лицо, являющееся заказчиком по Договору об оказании платных образовательных услуг.</p>
<p>Оплата за обучение, осуществляемая путем безналичного перечисления денежных средств на расчетный счет НИУ «БелГУ», производится с банковского счета лица, указанного в Договоре об оказании платных образовательных услуг в качестве Заказчика.</p>', 'alert-warning');
    ?>
    <h3>Форма оплаты</h3><br>
    <?php
    $personal = new \common\models\Model_Personal();

    $personal->id_user = $_SESSION[APP_CODE]['user_id'];
    $personal_array = $personal->getByUser();

    echo '<div class="row"><div class="col">';
    if(time() - strtotime($personal_array['birth_dt']) < 18 * 31536000)  {
        echo 'Вам должнол быть 18 лет, чтобы вы могли самостоятельно оплатить собственное обучение<br>';
    } else {
        echo HTML_Helper::setHrefButton(AGREEMENT['ctr'], 'SubmitMe/?conf_id=' . $data['id'], 'btn btn-warning', 'Я оплачиваю сам', 'Я оплачиваю сам');
    }
    echo '</div><div class="col">';
    echo HTML_Helper::setHrefButton(AGREEMENT['ctr'], 'SubmitPerson/?conf_id=' . $data['id'], 'btn btn-warning', 'Оплачивает физическое лицо', 'Оплачивает физическое лицо');
    echo '</div><div class="col">';
    echo HTML_Helper::setHrefButton(AGREEMENT['ctr'], 'SubmitLegalEntity/?conf_id=' . $data['id'], 'btn btn-warning', 'Оплачивает юридическое лицо', 'Оплачивает юридическое лицо');
    echo '</div></div>';
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
