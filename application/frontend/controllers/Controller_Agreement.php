<?php

namespace frontend\controllers;

use common\models\Model_Agreement as DB_Agreement;
use common\models\Model_Application;
use common\models\Model_ApplicationConfirmPlaces;
use common\models\Model_DictDoctypes;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Features;
use common\models\Model_PrivillegeQuota;
use common\models\Model_Resume as Resume;
use frontend\models\Model_Agreement;
use frontend\models\Model_ApplicationConfirm;
use frontend\models\Model_Scans;
use tinyframe\core\Controller;
use tinyframe\core\exceptions\UploadException;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\PDF_Helper;
use tinyframe\core\helpers\SOAP_Helper;
use tinyframe\core\View;

class Controller_Agreement
    extends Controller
{
    public $form;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_Agreement();
        $this->view = new View();
        // check resume
        $resume = new Resume();
        $resume_row = $resume->getStatusByUser();
        if ($resume_row) {
            if ($resume_row['status'] == $resume::STATUS_CREATED || $resume_row['status'] == $resume::STATUS_SAVED) {
                Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', NULL, 'Анкета ещё не отправлена!');
            }
        } else {
            Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', NULL, 'Анкета ещё не создана!');
        }
        // check education documents
        $docs = new DocsEduc();
        $docs_row = $docs->getByUser();
        if (!$docs_row) {
            Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', NULL, 'Нет ни одного документа об образовании!');
        }
    }

    /**
     * Displays application page.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->view->generate('agreement-list.php', 'main.php', 'Договора о зачислении');
    }

    /**
     * Displays application add page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        if (isset($_GET['conf_id']) && !empty($_GET['conf_id'])) {
            $id = $_GET['conf_id'];

            $conf = new \common\models\Model_ApplicationConfirm();
            $conf_rows = $conf->getByUser();

            $accepted = NULL;
            foreach ($conf_rows as $conf_row) {
                if($id == $conf_row['id']) {
                    $accepted = $id;
                }
            }

            $conf = new \common\models\Model_ApplicationConfirm();
            $conf->id = $accepted;
            $conf_row = $conf->get();
            if (is_array($conf_row) && count($conf_row) > 0 && $conf_row['id_status'] == 3) {
                $agree = new \common\models\Model_Agreement();
                $agree->id_confirm = $id;
                $agree_rows = $agree->getAllByConfirmId();

                $has_active = FALSE;
                foreach ($agree_rows as $agree_row) {
                    if($agree_row['active'] == 1) {
                        $has_active = TRUE;
                    }
                }

                if(!$has_active) {
                    $this->form['id'] = $id;
                    return $this->view->generate('agreement-create.php', 'main.php', 'Заявление', $this->form);
                } else {
                    Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'У вас уже есть договор на основании заявления '.$id);
                }
            }
            Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'На основе заявления ' . $id . ' нельзя создать договор');
        }

        return NULL;
    }

    public function actionSaveScans()
    {
        if (isset($_POST['id']) && $_POST['id'] > 0) {
            if (isset($_POST['btn_save_scans'])) {
                $new_status = DB_Agreement::STATUS_SAVED_SCANS;
            } elseif (isset($_POST['btn_send_scans'])) {
                $new_status = DB_Agreement::STATUS_SENT_SCANS;
            } else {
                die();
            }

            $agreement = new DB_Agreement();
            $agreement->load($_POST['id']);

            if ($agreement->id_user != $_SESSION[APP_CODE]['user_id']) {
                $this->form['error_msg'] = 'Вы пытались изменить запись, которая не сопоставлена с вашим пользователем!';
                return $this->view->generate('agreement-list.php', 'main.php', 'Договора', $this->form);
            }

            if ( in_array($agreement->status, [$agreement::STATUS_ALLOWED, $agreement::STATUS_SAVED_SCANS])) {
                switch ($agreement->payer_type) {
                    case $agreement::PAYER_SELF:
                        $rules = $this->model->rulesForSubmitMeWithScans();
                        break;
                    case $agreement::PAYER_PERSON:
                        $rules = $this->model->rulesForSubmitPersonWithScans();
                        break;
                    case $agreement::PAYER_LEGAL_AGENT:
                        $rules = $this->model->rulesForSubmitLegalWithScans();
                        break;
                }
                if ($agreement->has_supply_agreement == 1) {
                    $rules['agree_mod_page_1'][] = ['required' => ['default' => '', 'msg' => 'Скан-копия "Доп. Соглшашение" обязательна для заполнения!']];
                }

                $this->form = $this->model->getForm($rules, $_POST, $_FILES);
                $this->form = $this->model->validateForm($this->form, $rules);
                if (!empty($this->form['error_msg']) && $this->form['validate'] === TRUE) {
                    return $this->view->generate('agreement-list.php', 'main.php', 'Договора', $this->form);
                }


                if (in_array($agreement->status, [DB_Agreement::STATUS_ALLOWED, DB_Agreement::STATUS_SAVED_SCANS])) {
                    /* scans */
                    $dict_scans = new Model_DictScans();
                    $dict_scans->doc_code = 'agreement';
                    $dict_scans_arr = $dict_scans->getByDocument();
                    if ($dict_scans_arr) {
                        foreach ($dict_scans_arr as $dict_scans_row) {
                            $this->form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $this->form);
                            if (!empty($this->form['error_msg'])) {
                                return $this->view->generate('agreement-payment.php', 'form.php', 'Договора', $this->form);
                            }
                        }
                    }

                    $agreement->status = $new_status;
                    $agreement->changeAll();

                    Basic_Helper::msgReset();
                    $this->form['success_msg'] = 'Успешно выполнено!';
                    $_GET['id'] = $agreement->id;
                    return $this->actionEdit();
                } else {
                    $this->form['error_msg'] = 'Сохранять сканы документов договоров можно только для записей со статусами ' .
                        '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_ALLOWED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
                        '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_SCANS], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
                    return $this->view->generate('agreement-payment.php', 'form.php', 'Договора', $this->form);
                }
            } else {
                $this->form['error_msg'] = 'На данном этапе нельзя обновлять скан-копии документов в базе данных!';
                return $this->view->generate('agreement-list.php', 'main.php', 'Договора', $this->form);
            }
        } else {
            $this->form['error_msg'] = 'Не указана целевая запись!';
            return $this->view->generate('agreement-list.php', 'main.php', 'Договора', $this->form);
        }
    }

    public function actionSave()
    {
        if (isset($_POST['btn_save_payer'])) {
            if (isset($_POST['payer_type'])) {
                switch ($_POST['payer_type']) {
                    case \common\models\Model_Agreement::PAYER_SELF:
                        return $this->actionSaveAndSendMe();
                    case \common\models\Model_Agreement::PAYER_PERSON:
                        return $this->actionSavePayerPerson();
                        break;
                    case \common\models\Model_Agreement::PAYER_LEGAL_AGENT:
                        return $this->actionSavePayerLegalEnity();
                        break;
                }
            }
        } elseif (isset($_POST['btn_send_payer'])) {
            if (isset($_POST['payer_type'])) {
                switch ($_POST['payer_type']) {
                    case \common\models\Model_Agreement::PAYER_SELF:
                        return $this->actionSaveAndSendMe();
                    case \common\models\Model_Agreement::PAYER_PERSON:
                        return $this->actionSendPayerPerson();
                        break;
                    case \common\models\Model_Agreement::PAYER_LEGAL_AGENT:
                        return $this->actionSendPayerLegalEnity();
                        break;
                }
            }
        }

        return NULL;
    }

    public function actionSubmitMe()
    {
        if (isset($_GET['conf_id']) && !empty($_GET['conf_id'])) {
            $id = $_GET['conf_id'];
            $conf = new \common\models\Model_ApplicationConfirm();
            $conf->id = $id;
            $conf_row = $conf->get();
            if (is_array($conf_row) && count($conf_row) > 0 && $conf_row['id_status'] == 3) {
                $personal = new \common\models\Model_Personal();

                $personal->id_user = $_SESSION[APP_CODE]['user_id'];
                $personal_array = $personal->getByUser();

                echo '<div class="row"><div class="col">';
                if(time() - strtotime($personal_array['birth_dt']) < 18 * 31536000)  {
                    Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'Вам должнол быть 18 лет, чтобы вы могли самостоятельно оплатить собственное обучение');
                } else {
                    $agree = new \common\models\Model_Agreement();
                    $agree->id_confirm = $id;
                    $agree_rows = $agree->getAllByConfirmId();

                    $has_active = FALSE;
                    foreach ($agree_rows as $agree_row) {
                        if($agree_row['active'] == 1) {
                            $has_active = TRUE;
                        }
                    }

                    if($has_active){
                        Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'У вас уже есть договор на основании заявления '.$id);
                        return NULL;
                    }

                    $_POST['conf_id'] = $id;
                    $_POST['payer_type'] = \common\models\Model_Agreement::PAYER_SELF;
                    $_POST['btn_save_payer'] = 'selfpayed';
                    return $this->actionSave();
                }
            }

            Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'На основе заявления ' . $id . ' нельзя создать договор');
        }

        return NULL;
    }

    public function actionSaveAndSendMe()
    {
        $this->form = $this->model->getForm($this->model->rulesForSubmitMe(), $_POST);
        $this->form = $this->model->validateForm($this->form, $this->model->rulesForSubmitMe());
        $this->form = $this->model->checkSaveMe($this->form);
        if (!isset($this->form['error_msg']) && $this->form['validate'] === TRUE) {
            $this->model->savePayer($this->form);
        }

        return $this->view->generate('agreement-payment.php', 'main.php', 'Договора', $this->form);
    }

    public function actionSubmitPerson()
    {
        if (isset($_GET['conf_id']) && !empty($_GET['conf_id'])) {
            $id = $_GET['conf_id'];
            $conf = new \common\models\Model_ApplicationConfirm();
            $conf->id = $id;
            $conf_row = $conf->get();
            if (is_array($conf_row) && count($conf_row) > 0 && $conf_row['id_status'] == 3) {
                $agree = new \common\models\Model_Agreement();
                $agree->id_confirm = $id;
                $agree_rows = $agree->getAllByConfirmId();

                $has_active = FALSE;
                foreach ($agree_rows as $agree_row) {
                    if($agree_row['active'] == 1) {
                        $has_active = TRUE;
                    }
                }

                if($has_active){
                    Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'У вас уже есть договор на основании заявления '.$id);
                    return NULL;
                }

                $data['conf_id'] = $id;
                $data['payer_type'] = \common\models\Model_Agreement::PAYER_PERSON;
                $this->form = $this->model->setForm($this->model->rulesForSubmitPerson(), $data);

                return $this->view->generate('agreement-payment.php', 'form.php', 'Договора', $this->form);
            }

            Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'На основе согласия ' . $id . ' нельзя создать договор');
        }

        Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'Код согласия-основания не передан');
        return NULL;
    }

    public function actionSavePayerPerson()
    {
        $dict_doctype = new Model_DictDoctypes();
        $dict_doctype->code = $_POST['passport_type'];
        $dict_doctype->getByCode();
        if (!empty($dict_doctype) && isset($dict_doctype->id) && $dict_doctype->id > 0) {
            $_POST['id_doctype'] = $dict_doctype->id;
        }

        $this->form = $this->model->getForm($this->model->rulesForSubmitPerson(), $_POST);
        $this->form = $this->model->validateForm($this->form, $this->model->rulesForSubmitPerson());
        $this->form = $this->model->checkSavePersonal($this->form);
        if (!isset($this->form['error_msg']) && $this->form['validate'] === TRUE) {
            $this->model->savePayer($this->form);
        }

        return $this->view->generate('agreement-payment.php', 'form.php', AGREEMENT['hdr'], $this->form);
    }

    public function actionSendPayerPerson()
    {
        $this->form = $this->model->getForm($this->model->rulesForSubmitPerson(), $_POST);
        $this->form = $this->model->validateForm($this->form, $this->model->rulesForSubmitPerson());
        $this->form = $this->model->checkSendPersonal($this->form);
        if (!isset($this->form['error_msg']) && $this->form['validate'] == TRUE) {
            $this->model->sendPayer($this->form);
        }

        return $this->view->generate('agreement-payment.php', 'form.php', AGREEMENT['hdr'], $this->form);
    }

    public function actionSubmitLegalEntity()
    {
        if (isset($_GET['conf_id']) && !empty($_GET['conf_id'])) {
            $id = $_GET['conf_id'];
            $conf = new \common\models\Model_ApplicationConfirm();
            $conf->id = $id;
            $conf_row = $conf->get();
            if (is_array($conf_row) && count($conf_row) > 0 && $conf_row['id_status'] == 3) {
                $agree = new \common\models\Model_Agreement();
                $agree->id_confirm = $id;
                $agree_rows = $agree->getAllByConfirmId();

                $has_active = FALSE;
                foreach ($agree_rows as $agree_row) {
                    if($agree_row['active'] == 1) {
                        $has_active = TпRUE;
                    }
                }

                if($has_active){
                    Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'У вас уже есть договор на основании заявления '.$id);
                    return NULL;
                }

                $data['conf_id'] = $id;
                $data['payer_type'] = \common\models\Model_Agreement::PAYER_LEGAL_AGENT;
                $this->form = $this->model->setForm($this->model->rulesForSubmitLegal(), $data);

                return $this->view->generate('agreement-payment.php', 'form.php', 'Заявление', $this->form);
            }

            Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'На основе заявления ' . $id . ' нельзя создать договор');
        }

        return NULL;
    }

    public function actionSavePayerLegalEnity()
    {
        $this->form = $this->model->getForm($this->model->rulesForSubmitLegal(), $_POST);
        $this->form = $this->model->validateForm($this->form, $this->model->rulesForSubmitLegal());
        $this->form = $this->model->checkSaveLegal($this->form);
        if (!isset($this->form['error_msg']) && $this->form['validate'] === TRUE) {
            $this->model->savePayer($this->form);
        }

        return $this->view->generate('agreement-payment.php', 'main.php', AGREEMENT['hdr'], $this->form);
    }

    private function actionSendPayerLegalEnity()
    {
        $this->form = $this->model->getForm($this->model->rulesForSubmitLegal(), $_POST);
        $this->form = $this->model->validateForm($this->form, $this->model->rulesForSubmitLegal());
        $this->form = $this->model->checkSendLegal($this->form);
        if (!isset($this->form['error_msg']) && $this->form['validate'] === TRUE) {
            $this->model->sendPayer($this->form);
        }

        return $this->view->generate('agreement-payment.php', 'main.php', AGREEMENT['hdr'], $this->form);
    }

    /**
     * Shows application specialities.
     *
     * @return mixed
     */
    public function actionEdit()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = htmlspecialchars($_GET['id']);
            $agreement = new Model_Agreement();
            $agreement_db = new \common\models\Model_Agreement();
            $agreement_db->load($id);

            if ($agreement_db->active == 1) {
                switch ($agreement_db->status) {
                    case $agreement_db::STATUS_CREATED:
                    case $agreement_db::STATUS_SAVED_PAYER_DATA:
                    case $agreement_db::STATUS_SENT_PAYER_DATA:
                    case $agreement_db::STATUS_DISALLOWED:
                        $rules = [];
                        switch ($agreement_db->payer_type) {
                            case $agreement_db::PAYER_SELF:
                                $rules = $agreement->rulesForSubmitMe();
                                break;
                            case $agreement_db::PAYER_PERSON:
                                $rules = $agreement->rulesForSubmitPerson();
                                break;
                            case $agreement_db::PAYER_LEGAL_AGENT:
                                $rules = $agreement->rulesForSubmitLegal();
                                break;
                        }

                        $this->form = $this->model->setForm($rules, $agreement_db->getWithCountryFix());

                        return $this->view->generate('agreement-payment.php', 'form.php', 'плательщик договора', $this->form);
                        break;
                    case $agreement_db::STATUS_ALLOWED:
                    case $agreement_db::STATUS_SAVED_SCANS:
                    case $agreement_db::STATUS_SENT_SCANS:
                    case $agreement_db::STATUS_APPROVED:
                    case $agreement_db::STATUS_REJECTED:
                        $rules = [];
                        switch ($agreement_db->payer_type) {
                            case $agreement_db::PAYER_SELF:
                                $rules = $agreement->rulesForSubmitMeWithScans();
                                break;
                            case $agreement_db::PAYER_PERSON:
                                $rules = $agreement->rulesForSubmitPersonWithScans();
                                break;
                            case $agreement_db::PAYER_LEGAL_AGENT:
                                $rules = $agreement->rulesForSubmitLegalWithScans();
                                break;
                        }

                        $this->form = $this->model->setForm($rules, $agreement_db->getWithDocs());
                        $this->form['id'] = $id;

                        return $this->view->generate('agreement-payment.php', 'form.php', 'документы договора', $this->form);
                        break;
                    default:
                        Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'Редактирование договора: Неизвестный статус договора ' . $agreement_db->status . '. Обратитьесь к администратору!');
                        break;
                }
            } else {
                Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'Редактирование договора: нельзя редактировать неактивный договор!!');
            }
        }
        Basic_Helper::redirect(APP_NAME, 202, AGREEMENT['ctr'], 'Index', NULL, 'Редактирование договора: Отсутствует идент-р заявления!');

        return NULL;
    }

    public function actionDownloadAgreement()
    {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $agreement = new \common\models\Model_Agreement();
            $agreement->load($_GET['id']);
            if (isset($agreement->id) && $agreement->id > 0 && isset($agreement->id_user) && $agreement->id_user == $_SESSION[APP_CODE]['user_id']) {
                $params = [
                    'Ext' => 'PDF',
                    'ContractNumber' => $agreement->contract_number,
                    'ContractType' => 0
                ];
                $xml = SOAP_Helper::loadWsdl(WSDL_1C, 'GetEduContractFile', USER_1C, PASSWORD_1C, $params);
                if ($xml) {
                    $data = $xml->return->BinaryDataResponse->Data;
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Type: application/pdf');
                    header('Content-Transfer-Encoding: binary');

                    // #84: Content-Length leads to "network connection was lost" on iOS
                    $isIOS = preg_match('/i(phone|pad|pod)/i', $_SERVER['HTTP_USER_AGENT']);
                    if (!$isIOS) {
                        header('Content-Length: ' . strlen($data));
                    }
                    header("Content-Disposition: inline; filename=\"Договор.pdf\"");

                    echo $data;
                    die();
                } else {
                    echo 'Ошибка загрузки договора: системная ошибка при получении файла ' . $_GET['id'] . '<br>';
                }
            } else {
                echo 'Ошибка загрузки договора: Файла ' . $_GET['id'] . ' не сопоставлен вашему пользователю<br>';
            }
        } else {
            echo 'Ошибка загрузки договора: Файла ' . $_GET['id'] . ' не существует<br>';
        }
    }

    public function actionDownloadAddition()
    {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $agreement = new \common\models\Model_Agreement();
            $agreement->load($_GET['id']);
            if (isset($agreement->id) && $agreement->id > 0 && isset($agreement->id_user) && $agreement->id_user == $_SESSION[APP_CODE]['user_id']) {
                $params = [
                    'Ext' => 'PDF',
                    'ContractNumber' => $agreement->contract_number,
                    'ContractType' => 1
                ];
                $xml = SOAP_Helper::loadWsdl(WSDL_1C, 'GetEduContractFile', USER_1C, PASSWORD_1C, $params);
                if ($xml) {
                    $data = $xml->return->BinaryDataResponse->Data;
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Type: application/pdf');
                    header('Content-Transfer-Encoding: binary');

                    // #84: Content-Length leads to "network connection was lost" on iOS
                    $isIOS = preg_match('/i(phone|pad|pod)/i', $_SERVER['HTTP_USER_AGENT']);
                    if (!$isIOS) {
                        header('Content-Length: ' . strlen($data));
                    }
                    header("Content-Disposition: inline; filename=\"Дополнительно соглашение.pdf\"");

                    echo $data;
                    die();
                } else {
                    echo 'Ошибка загрузки договора: системная ошибка при получении файла ' . $_GET['id'] . '<br>';
                }
            } else {
                echo 'Ошибка загрузки договора: Файла ' . $_GET['id'] . ' не сопоставлен вашему пользователю<br>';
            }
        } else {
            echo 'Ошибка загрузки договора: Файла ' . $_GET['id'] . ' не существует<br>';
        }
    }

    public function actionDownloadBill()
    {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $agreement = new \common\models\Model_Agreement();
            $agreement->load($_GET['id']);
            if (isset($agreement->id) && $agreement->id > 0 && isset($agreement->id_user) && $agreement->id_user == $_SESSION[APP_CODE]['user_id']) {
                $params = [
                    'Ext' => 'PDF',
                    'ContractNumber' => $agreement->contract_number,
                    'ContractType' => $agreement->has_mat_capital ? 3 : 2
                ];
                $xml = SOAP_Helper::loadWsdl(WSDL_1C, 'GetEduContractFile', USER_1C, PASSWORD_1C, $params);
                if ($xml) {
                    $data = $xml->return->BinaryDataResponse->Data;
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Type: application/pdf');
                    header('Content-Transfer-Encoding: binary');

                    // #84: Content-Length leads to "network connection was lost" on iOS
                    $isIOS = preg_match('/i(phone|pad|pod)/i', $_SERVER['HTTP_USER_AGENT']);
                    if (!$isIOS) {
                        header('Content-Length: ' . strlen($data));
                    }
                    header("Content-Disposition: inline; filename=\"Квитанция.pdf\"");

                    echo $data;
                    die();
                } else {
                    echo 'Ошибка загрузки договора: системная ошибка при получении файла ' . $_GET['id'] . '<br>';
                }
            } else {
                echo 'Ошибка загрузки договора: Файла ' . $_GET['id'] . ' не сопоставлен вашему пользователю<br>';
            }
        } else {
            echo 'Ошибка загрузки договора: Файла ' . $_GET['id'] . ' не существует<br>';
        }
    }

    /**
     * Deletes application.
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $this->form['id'] = htmlspecialchars($_GET['id']);
        $this->form['hdr'] = htmlspecialchars(AGREEMENT['hdr']);
        $this->form['ctr'] = htmlspecialchars(AGREEMENT['ctr']);

        if ($this->model->delete($this->form)) {
            Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', $_SESSION[APP_CODE]['success_msg']);
        } else {
            Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', NULL, $_SESSION[APP_CODE]['error_msg']);
        }

        return NULL;
    }

    public function __destruct()
    {
        $this->model = NULL;
        $this->view = NULL;
        parent::__destruct();
    }
}
