<?php

use common\models\Model_ApplicationConfirm;
use common\models\Model_Features;
use common\models\Model_PrivillegeAdvanced;
use common\models\Model_PrivillegeQuota;
use common\models\Model_TargetQuota;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use common\models\Model_Personal as Personal;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Ege as Ege;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_Application as Application;
use frontend\models\Model_Resume as Model_Resume;

// check login
if (!isset($_SESSION[APP_CODE]['user_name'])) {
    Basic_Helper::redirectHome();
}
?>
<div class="row">
    <?php
    $personal = new Personal();
    $personal_row = $personal->getFioByUser();
    if ($personal_row) {
        $welcome = 'Добро пожаловать, ' . $personal_row['name_last'] . ' ' . $personal_row['name_first'] . ' ' . $personal_row['name_middle'] . '!';
    } else {
        $welcome = 'Добро пожаловать!';
    }
    ?>
    <div class="col text-primary"><h3><?php echo $welcome; ?></h3></div>
</div>
<?php
$personal_row = $personal->getCode1sByUser();
if (!empty($personal_row['code1s'])) {
    echo '<div class="row">';
    echo '<div class="col"><h5>Ваш идентификатор абитуриента <a href="http://abitur.bsu.edu.ru/abitur/exam/sched/?request=personal_schedule&AID=\'' . $personal_row['code1s'] . '\'" target="_blank">для просмотра расписания вступительных испытаний на сайте</a>: <strong>' . $personal_row['code1s'] . '</strong></h5></div>';
    echo '</div>';
}
?>
<div class="row">
    <div class="col">
        <strong>Для получения дополнительной информации Вы можете обратиться в <a
                    href="http://abitur.bsu.edu.ru/abitur/help/contacts/" target="_blank">Приёмную комиссию</a></strong><br>
    </div>
</div>
<?php
echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
?>
<div class="row">
    <div class="col text-center text-primary">
        <h3>Схема работы (* - обязательные шаги)</h3>
    </div>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpResume">Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl('Main', RESUME['ctr']); ?>">Шаг 1*: Анкета <i
                        class="fas fa-id-card"></i></a>
        </h4>
    </div>
    <?php
    $resume = new Resume();
    $resume->id_user = $_SESSION[APP_CODE]['user_id'];
    $resume_row = $resume->checkByUser();
    if (!$resume_row) {
        echo '<div class="col col-sm-3 alert alert-danger"><h5>Состояние шага - не пройден</h5></div>';
        echo '<div class="col col-sm-3"></div>';
        echo '<div class="col col-sm-1"></div>';
    } elseif ($resume_row['status'] == $resume::STATUS_CREATED) {
        echo '<div class="col col-sm-3 alert alert-danger"><h5>Состояние шага - не пройден</h5></div>';
        //echo Model_Resume::showStatus($resume_row['status'], 3);
        echo '<div class="col col-sm-1"></div>';
    } else {
        echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
        //echo Model_Resume::showStatus($resume_row['status'], 3);
        echo '<div class="col col-sm-1"></div>';
    }
    ?>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpDocsEduc">
                Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl('Main', DOCS_EDUC['ctr']); ?>">Шаг 2*: Документы об образовании <i
                        class="fas fa-graduation-cap"></i></a>
        </h4>
    </div>
    <?php
    $docs = new DocsEduc();
    $docs->id_user = $_SESSION[APP_CODE]['user_id'];
    $docs_arr = $docs->getByUser();
    if ($docs_arr) {
        stepSuccess('документов', count($docs_arr));
    } else {
        stepError(1);
    }
    ?>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpEge">Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl('Main', EGE['ctr']); ?>">Шаг 3: Результаты ЕГЭ <i
                        class="fas fa-table"></i></a>
        </h4>
    </div>
    <?php
    $ege = new Ege();
    $ege->id_user = $_SESSION[APP_CODE]['user_id'];
    $ege_arr = $ege->getByUser();
    if ($ege_arr) {
        stepSuccess('результатов ЕГЭ', count($ege_arr));
    } else {
        stepError(0);
    }
    ?>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpFeatures">
                Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl(FEATURES['ctr'], 'Index'); ?>">Шаг 4: Отличительные признаки <i
                        class="fab fa-creative-commons-by"></i></a>
        </h4>
    </div>
    <?php
    $feature = new Model_Features();
    $feature->id_user = $_SESSION[APP_CODE]['user_id'];
    $feature_arr = $feature->getByUser();

    $priv_quota = new Model_PrivillegeQuota();
    $priv_quota->id_user = $_SESSION[APP_CODE]['user_id'];
    $priv_quota_arr = $priv_quota->getByUser();

    $priv_adv = new Model_PrivillegeAdvanced();
    $priv_adv->id_user = $_SESSION[APP_CODE]['user_id'];
    $priv_adv_arr = $priv_adv->getByUser();
    if ((is_array($feature_arr) && count($feature_arr) > 0) || (is_array($priv_quota_arr) && count($priv_quota_arr) > 0) || (is_array($priv_adv_arr) && count($priv_adv_arr) > 0)) {
        echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
    } else {
        stepError(0);
    }
    ?>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpTargetQuota">
                Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl(TARGET_QUOTA['ctr'], 'Index'); ?>">Шаг 5: Целевая квота <i
                        class="fa fa-address-book"></i></a>
        </h4>
    </div>
    <?php
    $target_quota = new Model_TargetQuota();
    $target_quota->id_user = $_SESSION[APP_CODE]['user_id'];
    $target_quota_arr = $target_quota->getByUser();

    if (is_array($target_quota_arr) && count($target_quota_arr) > 0) {
        echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
    } else {
        stepError(0);
    }
    ?>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpIndAchievs">
                Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl('Main', IND_ACHIEVS['ctr']); ?>">Шаг 6: Индивидуальные достижения
                <i class="fas fa-trophy"></i></a>
        </h4>
    </div>
    <?php
    $ia = new IndAchievs();
    $ia->id_user = $_SESSION[APP_CODE]['user_id'];
    $ia_arr = $ia->getByUser();
    if ($ia_arr) {
        stepSuccess('индивидуальных достижений', count($ia_arr));
    } else {
        stepError(0);
    }
    ?>
</div>
<div class="row">
    <?php
    if ($resume_row && $resume_row['app'] == 1 && $resume_row['status'] != $resume::STATUS_CREATED && $resume_row['status'] != $resume::STATUS_REJECTED && $docs_arr) {
        echo '<div class="col col-sm-1"></div>';
        echo '<div class="col col-sm-10 text-center alert alert-success"><h5>Подача заявлений разрешена</h5></div>';
        echo '<div class="col col-sm-1"></div>';
    } else {
        echo '<div class="col col-sm-1"></div>';
        echo '<div class="col col-sm-10 text-center alert alert-danger"><h5>Подача заявлений запрещена</h5></div>';
        echo '<div class="col col-sm-1"></div>';
    }
    ?>
</div>
<div class="row">
    <div class="col col-sm-5 text-right text-primary">
        <h4>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpApp">Инструкция
            </button>
            <a href="<?php echo Basic_Helper::appUrl('Main', APP['ctr']); ?>">Шаг 7*: Заявление о приеме на обучение <i
                        class="fas fa-file-alt"></i></a>
        </h4>
    </div>
    <?php
    $app = new Application();
    $app->id_user = $_SESSION[APP_CODE]['user_id'];
    $app_arr = $app->getActiveByUser();
    if ($app_arr) {
        $created = 0;
        $saved = 0;
        $sent = 0;
        $approved = 0;
        $rejected = 0;
        foreach ($app_arr as $app_row) {
            switch ($app_row['status']) {
                case $app::STATUS_CREATED:
                    $created++;
                    break;
                case $app::STATUS_SAVED:
                    $saved++;
                    break;
                case $app::STATUS_SENDED:
                    $sent++;
                    break;
                case $app::STATUS_APPROVED:
                    $approved++;
                    break;
                case $app::STATUS_REJECTED:
                    $rejected++;
                    break;
            }
        }
        if ($rejected == count($app_arr)) {
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Все заявления отклонены!</h5></div>';
            echo '<div class="col col-sm-1"></div>';
        } elseif ($sent === 0 && $approved === 0) {
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Нет отправленных или принятых заявлений!</h5></div>';
            echo '<div class="col col-sm-1"></div>';
        } else {
            stepSuccess('заявлений', count($app_arr));
        }
    } else {
        stepError(1);
    }
    ?>
</div>
<?php
echo '<div class="row">';
echo '<div class="col col-sm-5 text-right text-primary">';
echo '<h4>';
echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#helpConf">Инструкция</button>';
echo '<a href="' . Basic_Helper::appUrl('Main', APP_CONFIRM['ctr']).'">Шаг 8*: Заявление о согласии на зачисление <i class="fas fa-check"></i></a>';
echo '</h4></div>';
$app = new Model_ApplicationConfirm();
$app->id_user = $_SESSION[APP_CODE]['user_id'];
$app_arr = $app->getByUserGrid();

if ($app_arr) {
    $created = 0;
    $saved = 0;
    $sent = 0;
    $approved = 0;
    $rejected = 0;
    foreach ($app_arr as $app_row) {
        switch ($app_row['status_id']) {
            case $app::STATUS_CREATED:
                $created++;
                break;
            case $app::STATUS_SAVED:
                $saved++;
                break;
            case $app::STATUS_SENT:
                $sent++;
                break;
            case $app::STATUS_APPROVED:
                $approved++;
                break;
            case $app::STATUS_REJECTED:
                $rejected++;
                break;
        }
    }

    if ($approved > 0) {
        stepSuccess('Согласий ', count($app_arr));
    } elseif ($rejected == count($app_arr)) {
        echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
        echo '<div class="col col-sm-3 alert alert-warning"><h5>Все согласия на зачисление отклонены!</h5></div>';
        echo '<div class="col col-sm-1"></div>';
    } elseif ($sent === 0) {
        echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
        echo '<div class="col col-sm-3 alert alert-warning"><h5>Нет отправленных согласий на зачисление!</h5></div>';
        echo '<div class="col col-sm-1"></div>';
    } elseif ($approved === 0) {
        echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
        echo '<div class="col col-sm-3 alert alert-warning"><h5>Нет принятых согласия на зачисление!</h5></div>';
        echo '<div class="col col-sm-1"></div>';
    }
} else {
    stepError(1);
}
echo '</div>';

if (/*in_array($_SESSION[APP_CODE]['user_id'], [3, 4, 5, 7, 9, 166, 256, 3640, 3643, 3644, 3645, 7321])*/TRUE) {
    $href = Basic_Helper::appUrl('Main', AGREEMENT['ctr']);

    echo '<div class="row">
                <div class="col col-sm-5 text-right text-primary" >
                    <h4>
                        <button type = "button" class="btn btn-info btn-sm" data-toggle = "modal" data-target = "#helpAgree" > Инструкция</button >
                        <a href = "' . $href . '" > Договор о оплате обучения (только для платной основы обучения)* <i class="fas fa-balance-scale" ></i></a>
                    </h4>
                </div>';

    $agreement = new \common\models\Model_Agreement();
    $agreement->id_user = $_SESSION[APP_CODE]['user_id'];
    $aggr_arr = $agreement->getByUserGrid();
    if ($aggr_arr) {
        $created = 0;
        $saved = 0;
        $sent = 0;
        $allowed = 0;
        $approved = 0;
        $rejected = 0;
        foreach ($aggr_arr as $aggr_row) {
            switch ($aggr_row['status_code']) {
                case $agreement::STATUS_CREATED:
                    $created++;
                    break;
                case $agreement::STATUS_SAVED_PAYER_DATA:
                case $agreement::STATUS_SAVED_SCANS:
                    $saved++;
                    break;
                case $agreement::STATUS_SENT_PAYER_DATA:
                case $agreement::STATUS_SENT_SCANS:
                    $sent++;
                    break;
                case $agreement::STATUS_ALLOWED:
                    $allowed++;
                    break;
                case $agreement::STATUS_APPROVED:
                    $approved++;
                    break;
                case $agreement::STATUS_DISALLOWED:
                case $agreement::STATUS_REJECTED:
                    $rejected++;
                    break;
            }
        }

        if ($approved > 0) {
            stepSuccess('Договоров ', count($aggr_arr));
        } elseif ($rejected == count($aggr_arr)) {
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Все договора об оплате на зачисления отклонены!</h5></div>';
            echo '<div class="col col-sm-1"></div>';
        } elseif ($sent === 0) {
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Нет отправленных договоров об оплате!</h5></div>';
            echo '<div class="col col-sm-1"></div>';
        } elseif ($approved === 0) {
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Состояние шага - частично пройден</h5></div>';
            echo '<div class="col col-sm-3 alert alert-warning"><h5>Нет принятых договоров об оплате!</h5></div>';
            echo '<div class="col col-sm-1"></div>';
        }
    } else {
        stepError(1);
    }
    echo '</div>';
}
?>


<div class="modal fade" id="helpResume" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Анкета (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-justify">
                <?php echo Help_Helper::resume_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpDocsEduc" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Документы об образовании (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-justify">
                <?php echo Help_Helper::docs_educ_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpEge" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Результаты ЕГЭ (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-justify">
                <?php echo Help_Helper::ege_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpFeatures" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Отличительные признаки (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-justify">
                <?php echo Help_Helper::features_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpTargetQuota" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Целевая квота (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-justify">
                <?php echo Help_Helper::target_quota_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpIndAchievs" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Индивидуальные достижения (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::ind_achievs_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpApp" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Заявления (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::app_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpConf" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Заявления о согласие на зачисление (инструкция)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php echo Help_Helper::confirm_help(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpAgree" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Договора (инструкция)</h4>
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

<?php
function stepSuccess($count_msg = null, $count = null)
{
    echo '<div class="col col-sm-3 alert alert-success"><h5>Состояние шага - пройден</h5></div>';
    if (empty($count_msg) || empty($count)) {
        echo '<div class="col col-sm-3"></div>';
        echo '<div class="col col-sm-1"></div>';
    } else {
        echo '<div class="col col-sm-3 alert alert-info"><h5>Кол-во ' . $count_msg . ' - ' . $count . '</h5></div>';
        echo '<div class="col col-sm-1"></div>';
    }
}

function stepError($required = 0)
{
    echo '<div class="col col-sm-3 alert alert-' . (($required == 1) ? 'danger' : 'warning') . '"><h5>Состояние шага - не пройден</h5></div>';
    echo '<div class="col col-sm-3"></div>';
    echo '<div class="col col-sm-1"></div>';
}

?>
