<?php /** @noinspection TypeUnsafeComparisonInspection */

namespace frontend\models;

use common\models\Model_ApplicationConfirm as ApplicationConfirm;
use common\models\Model_DictScans;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\Model as Model;

include ROOT_DIR . '/application/frontend/models/Model_Scans.php';

class Model_ApplicationConfirm
    extends Model
{
    /*
        Application processing
    */

    /**
     * Application rules.
     *
     * @return array
     */
    public function rules($filter)
    {
        return array_merge([
            'id' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Код заявления не передан!'],
                'success' => 'Код заявления успешно получен'
            ]
        ], Model_Scans::createRules($filter));
    }

    /**
     * Shows type.
     *
     * @param $type
     *
     * @return string
     */
    public static function showType($type)
    {
        switch ($type) {
            case ApplicationConfirm::TYPE_NEW:
                return '<div class="alert alert-info">Тип: <strong>' . mb_convert_case(ApplicationConfirm::TYPE_NAMES[ApplicationConfirm::TYPE_NEW], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            case ApplicationConfirm::TYPE_RECALL:
                return '<div class="alert alert-info">Тип: <strong>' . mb_convert_case(ApplicationConfirm::TYPE_NAMES[ApplicationConfirm::TYPE_RECALL], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            default:
                return '<div class="alert alert-info">Тип: <strong>НЕИЗВЕСТНО</strong></div>';
        }
    }

    /**
     * Shows status.
     *
     * @param $status
     *
     * @return string
     */
    public static function showStatus($status)
    {
        switch ($status) {
            case ApplicationConfirm::STATUS_CREATED:
                return '<div class="alert alert-info">Состояние: <strong>' . mb_convert_case(ApplicationConfirm::STATUSES[ApplicationConfirm::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            case ApplicationConfirm::STATUS_SAVED:
                return '<div class="alert alert-info">Состояние: <strong>' . mb_convert_case(ApplicationConfirm::STATUSES[ApplicationConfirm::STATUS_SAVED], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            case ApplicationConfirm::STATUS_SENT:
                return '<div class="alert alert-primary">Состояние: <strong>' . mb_convert_case(ApplicationConfirm::STATUSES[ApplicationConfirm::STATUS_SENT], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            case ApplicationConfirm::STATUS_APPROVED:
                return '<div class="alert alert-success">Состояние: <strong>' . mb_convert_case(ApplicationConfirm::STATUSES[ApplicationConfirm::STATUS_APPROVED], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            case ApplicationConfirm::STATUS_REJECTED:
                return '<div class="alert alert-danger">Состояние: <strong>' . mb_convert_case(ApplicationConfirm::STATUSES[ApplicationConfirm::STATUS_REJECTED], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            case ApplicationConfirm::STATUS_RECALLED:
                return '<div class="alert alert-danger">Состояние: <strong>' . mb_convert_case(ApplicationConfirm::STATUSES[ApplicationConfirm::STATUS_RECALLED], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
            default:
                return '<div class="alert alert-warning">Состояние: <strong>НЕИЗВЕСТНО</strong></div>';
        }
    }

    /**
     * Deletes application from database.
     *
     * @param $form
     *
     * @return bool
     */
    public function delete($form)
    {
        $app = new ApplicationConfirm();
        $app->id = $form['id'];
        $app_row = $app->getByUserAndId();
        $app->type = $app_row['type'];
        $app->id_status = $app_row['id_status'];
        $app->parent_id = $app_row['parent_id'];


        if (is_array($app_row) && count($app_row) > 0) {
            if ($app->clear() > 0) {
                $_SESSION[APP_CODE]['success_msg'] = 'Согласие № ' . $form['id'] . ' удалено.';

                return TRUE;
            }
            $_SESSION[APP_CODE]['error_msg'] = 'Ошибка удаления согласия № ' . $form['id'] . '! Свяжитесь с администратором.';

            return FALSE;
        }

        $_SESSION[APP_CODE]['error_msg'] = 'Удалять заявления можно только с состоянием: '.
            '<strong>' . mb_convert_case($app::STATUSES[$app::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8').'</strong>, '.
            '<strong>' . mb_convert_case($app::STATUSES[$app::STATUS_SAVED], MB_CASE_UPPER, 'UTF-8') . '</strong>!';

        return FALSE;
    }

    /**
     * Checks application spec data.
     *
     * @return array
     */
    public function check($form)
    {
        $form['success_msg'] = NULL;
        $form['error_msg'] = NULL;
        $app = new ApplicationConfirm();
        $app->id = $form['id'];
        $app_row = $app->get();
        /* check type */
        if ($app_row['type'] == $app::TYPE_RECALL) {
            $form['error_msg'] = 'Нельзя сохранять заявления с типом <strong>' . mb_convert_case($app::TYPE_NAMES[$app::TYPE_RECALL], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
            return $form;
        }
        /* check status */
        if ($app_row['id_status'] != $app::STATUS_SAVED) {
            $form['error_msg'] = 'Сохранять можно только заявления с состоянием: <strong>' . mb_convert_case($app::STATUSES[$app::STATUS_SAVED], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
            return $form;
        }

        /* scans */
        $dict_scans = new Model_DictScans();
        if ($app->type == $app::TYPE_NEW) {
            $dict_scans->doc_code = 'application_confirm';
        } else {
            $dict_scans->doc_code = 'app_confirm_recall';
        }


        $dict_scans_arr = $dict_scans->getByDocument();
        if ($dict_scans_arr) {
            foreach ($dict_scans_arr as $dict_scans_row) {
                $form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
                if (!empty($form['error_msg'])) {
                    return $form;
                }
            }
        }

        Basic_Helper::msgReset();
        $form['success_msg'] = 'Заявление сохранено.';
        $form['error_msg'] = NULL;

        return $form;
    }

    /**
     * Gets application spec data from database.
     *
     * @param $id
     *
     * @return array|NULL|false
     */
    public function get($id)
    {
        $app = new ApplicationConfirm();
        $app->id = $id;

        return $app->getWithDocs();
    }

    public function getByUserAndId(string $id)
    {
        $app = new ApplicationConfirm();
        $app->id = $id;

        return $app->getByUserAndId();
    }

    /**
     * Gets application spec data from database.
     *
     * @param $app_id
     *
     * @return array|NULL|false
     */
    public function getByApp($app_id)
    {
        $app = new ApplicationConfirm();
        $app->id_application = $app_id;

        return $app->getByAppAndActive();
    }

    public function formPost($app_id)
    {
        $new_confirm = new ApplicationConfirm();
        return $new_confirm->buildFromApplication($app_id);
    }

    /**
     * Unsets application spec files.
     *
     * @return array
     */
    public function unsetScans($form)
    {
        $form = Model_Scans::unsets('application_confirm', $form);

        return $form;
    }

    public function send($form)
    {
        $form['success_msg'] = NULL;
        $form['error_msg'] = NULL;
        $app = new ApplicationConfirm();
        $app->id = $form['id'];
        $app_row = $app->get();
        /* check status */
        if (in_array($app_row['id_status'], [$app::STATUS_SENT, $app::STATUS_APPROVED, $app::STATUS_REJECTED, $app::STATUS_RECALLED])) {
            $form['error_msg'] = 'Отправлять можно только согласия с состоянием <strong>НОВОЕ</strong> и <strong>СОХРАНЕНО</strong>';
            return $form;
        }

        if ($app->isSelected() === FALSE) {
            $form['error_msg'] = 'Отправлять можно только согласия с <strong>выбранным направлением подготовки</strong>!';
            return $form;
        }

        if ($app_row['id_status'] == $app::STATUS_SENT) {
            $form['error_msg'] = 'Заявление уже отправлено!';

            return $form;
        }

        /* scans */
        $dict_scans = new Model_DictScans();
        if ($app_row['type'] == $app::TYPE_NEW) {
            $dict_scans->doc_code = 'application_confirm';
        } else {
            $dict_scans->doc_code = 'app_confirm_recall';
        }

        $dict_scans_arr = $dict_scans->getByDocument();

        if ($dict_scans_arr) {
            foreach ($dict_scans_arr as $dict_scans_row) {
                $form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
                if (!empty($form['error_msg'])) {
                    return $form;
                }
            }
        }

        /* send */
        $app->changeSingle('id_status', ApplicationConfirm::STATUS_SENT);
        $form['status'] = $app::STATUS_SENT;
        Basic_Helper::msgReset();
        $form['success_msg'] = 'Заявление отправлено.';
        $form['error_msg'] = NULL;

        return $form;
    }

    public function hasActiveConfirmByApp($app_id)
    {
        $current_actives = (new ApplicationConfirm())->getValidConfsByAppId($app_id);
        if(is_array($current_actives) && count($current_actives) > 0) {
            return true;
        }

        return false;
    }
}
