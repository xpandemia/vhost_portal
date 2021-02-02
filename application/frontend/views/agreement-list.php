<?php

use common\models\Model_Agreement;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

// check login
if (!isset($_SESSION[APP_CODE]['user_name'])) {
    Basic_Helper::redirectHome();
}

if (isset($data['success_msg'])) {
    echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
}

if (isset($data['error_msg'])) {
    echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
}
?>
<div class="container-fluid rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
    <div class="row">
        <div class="">
            <h2>Договоры</h2>
        </div>
        <div class="">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpApp">Инструкция</button>
        </div>
        <div class="col text-left">
            <?php echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную'); ?>
        </div>
    </div>
    <?php
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
    echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
    echo HTML_Helper::setGridDB(['model_class' => Model_Agreement::class,
        'model_method' => 'getByUserGrid',
        'model_filter' => 'id_user',
        'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
        'grid' => 'grid',
        'controller' => APP_CONFIRM['ctr'],
        'home_hdr' => 'Договоры']);
    ?>
</div>

<div class="modal fade" id="helpApp" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Договоры (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::agreement_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
