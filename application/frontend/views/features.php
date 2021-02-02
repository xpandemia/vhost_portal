<?php

use common\models\Model_Features;
use common\models\Model_PrivillegeAdvanced;
use common\models\Model_PrivillegeQuota;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

// check login
if( !isset($_SESSION[APP_CODE]['user_name']) ) {
    Basic_Helper::redirectHome();
}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <div class="row">
        <div class="">
            <h2>Отличительные признаки </h2>
        </div>
        <div class="">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>
            <?php
            echo HTML_Helper::setUrlHrefButtonIcon('https://vk.com/video-102554211_456239630?list=c8d807c673fbf69bca', 'btn btn-primary', 'fab fa-youtube', 'Видеоинструкция', true);
            ?>
        </div>
        <div class="col text-left">
            <?php echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную'); ?>
        </div>
    </div>
    <div class="row">
        <div class="">
            <h2>Права на прием без вступительных испытаний </h2>
        </div>
        <div class="">
            <!--<button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>-->
        </div>
    </div>
    <?php
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    echo HTML_Helper::setGridDB([
                                    'model_class' => Model_Features::class,
                                    'model_method' => 'getByUserGrid',
                                    'model_filter' => 'id_user',
                                    'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
                                    'grid' => 'grid',
                                    'controller' => FEATURES['ctr'],
                                    'action_add' => 'Add',
                                    'action_edit' => 'Edit',
                                    'action_delete' => 'DeleteConfirm',
                                    'home_hdr' => 'Особые права'
                                ]);
    ?>
    <div class="row">
        <div class="">
            <h2>Право на прием на обучение за счет бюджетных ассигнований в пределах особой квоты </h2>
        </div>
        <div class="">
            <!--<button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>-->
        </div>
    </div>
    <?php
    /** @var Model_PrivillegeQuota $priv_quota_container */
    $_t = new Model_PrivillegeQuota();
    $priv_quota_container = $_t->getByUserGrid();
    if(is_array($priv_quota_container) && count($priv_quota_container) > 0) {
        $priv_quota_container = $priv_quota_container[0];
    } else {
        $priv_quota_container = NULL;
    }
    
    if( $priv_quota_container === FALSE || $priv_quota_container === NULL ) {
        echo HTML_Helper::setHrefButton('PrivillegeQuota', 'Add', 'btn btn-success', 'Добавить', 'Добавить');
    } else {
        echo '<table class="table table-bordered table-hover" id="gridDb_priv_qoute" name="gridDb">'.
             '<tr>'.
             '<td>'.$priv_quota_container['name'].'</td>'.
             '<td>'.$priv_quota_container['doc_number'].'</td>'.
             '<td>'.$priv_quota_container['doc_issuer'].'</td>'.
             '<td>'.HTML_Helper::setHrefButtonIcon(PRIV_QUOTA['ctr'],
                                                   'Edit/?id='.$priv_quota_container['id'], 'font-weight-bold', 'far fa-edit fa-2x', 'Редактировать').'</td>'.
             '<td>'.HTML_Helper::setHrefButtonIcon(PRIV_QUOTA['ctr'],
                                                   'Delete/?id='.$priv_quota_container['id'], 'font-weight-bold', 'fas fa-times fa-2x', 'Удалить').'</td>'.
             '</tr></table>';
    }
    ?>
    <div class="row">
        <div class="">
            <h2>Преимущественное право зачисления </h2>
        </div>
        <div class="">
            <!--<button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>-->
        </div>
    </div>
    <?php
    /** @var Model_PrivillegeQuota $priv_quota_container */
    $_t = new Model_PrivillegeAdvanced();
    $priv_adv_container = $_t->getByUserGrid();
    if(is_array($priv_adv_container) && count($priv_adv_container) > 0) {
        $priv_adv_container = $priv_adv_container[0];
    } else {
        $priv_adv_container = NULL;
    }
    if( $priv_adv_container === FALSE || $priv_adv_container === NULL) {
        echo HTML_Helper::setHrefButton('PrivillegeAdvanced', 'Add', 'btn btn-success', 'Добавить', 'Добавить');
    } else {
        echo '<table class="table table-bordered table-hover" id="gridDb_priv_qoute" name="gridDb">'.
             '<tr>'.
             '<td>'.$priv_adv_container['name'].'</td>'.
             '<td>'.$priv_adv_container['doc_number'].'</td>'.
             '<td>'.$priv_adv_container['doc_issuer'].'</td>'.
             '<td>'.HTML_Helper::setHrefButtonIcon(PRIV_ADV['ctr'],
                                                   'Edit/?id='.$priv_adv_container['id'], 'font-weight-bold', 'far fa-edit fa-2x', 'Редактировать').'</td>'.
             '<td>'.HTML_Helper::setHrefButtonIcon(PRIV_ADV['ctr'],
                                                   'Delete/?id='.$priv_adv_container['id'], 'font-weight-bold', 'fas fa-times fa-2x', 'Удалить').'</td>'.
             '</tr></table>';
    }
    
    
    ?>
</div>

<div class="modal fade" id="helpIndAchievs" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Отличительные признаки (инструкция)</h4>
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::features_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
