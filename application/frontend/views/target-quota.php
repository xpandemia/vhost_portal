<?php

use common\models\Model_TargetQuota;
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
            <h2>Договор о целевом обучении </h2>
        </div>
        <div class="">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpIndAchievs">Инструкция</button>
            <?php
            echo HTML_Helper::setUrlHrefButtonIcon('https://vk.com/video-102554211_456239631?list=e317a315cb22e12630', 'btn btn-primary', 'fab fa-youtube', 'Видеоинструкция', true);
            ?>
        </div>
        <div class="col text-left">
            <?php echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную'); ?>
        </div>
    </div>
    <?php
    /** @var Model_TargetQuota $target_quota_container */
    $_t                     = new Model_TargetQuota();
    $target_quota_container = $_t->getByUserGrid();
    if( is_array($target_quota_container) && count($target_quota_container) > 0 ) {
        echo '<table class="table table-bordered table-hover" id="gridDb_priv_qoute" name="gridDb">'.
             '<tr>'.
             '<td>'.$target_quota_container['doc_number'].'</td>'.
             '<td>'.$target_quota_container['doc_issuer'].'</td>'.
             '<td>'.HTML_Helper::setHrefButtonIcon(TARGET_QUOTA['ctr'],
                                                   'Edit/?id='.$target_quota_container['id'], 'font-weight-bold', 'far fa-edit fa-2x', 'Редактировать').'</td>'.
             '<td>'.HTML_Helper::setHrefButtonIcon(TARGET_QUOTA['ctr'],
                                                   'Delete/?id='.$target_quota_container['id'], 'font-weight-bold', 'fas fa-times fa-2x', 'Удалить').'</td>'.
             '</tr></table>';
    } else {
        echo HTML_Helper::setHrefButton('TargetQuota', 'Add', 'btn btn-success', 'Добавить', 'Добавить');
    }
    ?>
</div>

<div class="modal fade" id="helpIndAchievs" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Договор о целевом обучении (инструкция)</h4>
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::target_quota_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
