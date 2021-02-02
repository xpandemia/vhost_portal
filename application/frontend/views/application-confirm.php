<?php

use common\models\Model_ApplicationConfirm;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

// check login
if (!isset($_SESSION[APP_CODE]['user_name'])) {
    Basic_Helper::redirectHome();
}

?>
<div class="container-fluid rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <div class="row">
        <div class="">
            <h2>Согласия на зачисление</h2>
        </div>
        <div class="">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpApp">Инструкция</button>
        </div>
        <div class="col text-left">
            <?php echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную'); ?>
        </div>
    </div>
    <?php
    $_conf = new Model_ApplicationConfirm();
    $valid_count = $_conf->getValidCount();

    foreach($valid_count as $valid_by_campaign) {
        if(isset($valid_by_campaign['desc'])) {
            $text = '<h4> Приемная компания: ' . $valid_by_campaign['desc'] . '</h4><br/>';

            if ((count($valid_by_campaign['ready']) + count($valid_by_campaign['recalled'])) > 1) {
                $text .= '<p><strong>ВЫ УЖЕ ПОДАЛИ ДВА СОГЛАСИЯ НА ЗАЧИСЛЕНИЕ НА ЭТУ ПРИЕМНУЮ КОМПАНИЮ И БОЛЬШЕ НЕ МОЖЕТЕ ПОДАВАТЬ НОВЫЕ ЭКЗЕМПЛЯРЫ! ВСЕ ПОСЛЕДУЮЩИЕ ПОДАЧИ СОГЛАСИЯ НА ЭТУ ПРИЕМНУЮ КОМПАНИЮ БУДУТ АВТОМАТИЧЕСКИ ОТКЛОНЯТЬСЯ СИСТЕМОЙ!</strong></p>';
                echo HTML_Helper::setAlert($text, 'alert-danger');
            } elseif ((count($valid_by_campaign['ready']) + count($valid_by_campaign['recalled'])) == 0) {
                $text .= '<p>Вы можете подать <strong>ПЕРВОЕ</strong> из доступных вам <strong>ДВУХ</strong> согласие на зачисление на эту приемную компанию. Если со временем вы захотите изменить свое решение вы сможете <strong>ЕДИНОЖДЫ</strong> отозвать отправленное согласие и подать новый экземпляр на эту приемную компанию.</p>';
                echo HTML_Helper::setAlert($text, 'alert-success');
            } else {
                $text .= '<p>Вы можете подать <strong>ВТОРОЕ</strong> и <strong>ПОСЛЕДНЕЕ</strong> согласие на зачисление на эту приемную компанию. <strong>ВЫ БОЛЬШЕ НЕ СМОЖЕТЕ ИЗМЕНИТЬ НАПРАВЛЕНИЕ ПОДГОТОВКИ ПОСЛЕ ОТПРАВКИ ЗАЯВЛЕНИЯ ДЛЯ ЭТОЙ ПРИЕМНОЙ КОМПАНИИ!</strong></p>';
                echo HTML_Helper::setAlert($text, 'alert-warning');
            }
        }
    }


    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    echo HTML_Helper::setGridDB(['model_class' => Model_ApplicationConfirm::class,
        'model_method' => 'getByUserGrid',
        'model_filter' => 'id_user',
        'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
        'grid' => 'grid',
        'controller' => APP_CONFIRM['ctr'],
        'home_hdr' => 'Согласия']);
    ?>
</div>

<div class="modal fade" id="helpApp" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Заявления о согласие на зачисление (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::confirm_help();?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
