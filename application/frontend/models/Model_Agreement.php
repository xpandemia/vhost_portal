<?php /** @noinspection TypeUnsafeComparisonInspection */

namespace frontend\models;

use common\models\Model_Agreement as DB_Agreement;
use common\models\Model_DictCountries as Model_DictCountries;
use common\models\Model_DictScans;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\SOAP_Helper;
use tinyframe\core\Model as Model;

include ROOT_DIR . '/application/frontend/models/Model_Scans.php';

class Model_Agreement
    extends Model
{
    /**
     * Application rules.
     *
     * @return array
     */
    public function rules($type)
    {
        $root_rule = [];

        switch ($type) {
            case 'person':
                $root_rule = $this->rulesForSubmitPerson();
                break;
            case 'legal':
                $root_rule = $this->rulesForSubmitLegal();
        }

        return array_merge($root_rule, Model_Scans::createRules('agreement'));
    }

    public function getRulesForScan()
    {
        $res = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Номер договора получен успешно'
            ],
            'conf_id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код заявления успешно получен'
            ],
            'payer_type' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Тип оплаты не предоставлен!'],
                'success' => 'Тип оплаты получен успешно'
            ],
            'status' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Статус договора получен успешно'
            ],
            'comment' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Замечание модератора'
            ]];

        return array_merge($res, Model_Scans::createRules('agreement'));
    }

    public function rulesForSubmitMeWithScans() {
        return array_merge($this->rulesForSubmitMe(), Model_Scans::createRules('agreement'));
    }

    public function rulesForSubmitMe()
    {
        return [
            'id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Номер договора получен успешно'
            ],
            'conf_id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код заявления успешно получен'
            ],
            'payer_type' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Тип оплаты не предоставлен!'],
                'success' => 'Тип оплаты получен успешно'
            ],
            'status' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Статус договора получен успешно'
            ],
            'comment' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Замечание модератора'
            ]];
    }

    public function rulesForSubmitPersonWithScans() {
        return array_merge($this->rulesForSubmitPerson(), Model_Scans::createRules('agreement'));
    }

    public function rulesForSubmitPerson()
    {
        $rules = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Номер договора получен успешно'
            ],
            'conf_id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код заявления успешно получен'
            ],
            'payer_type' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Тип оплаты не предоставлен!'],
                'success' => 'Тип оплаты получен успешно'
            ],
            'status' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Статус договора получен успешно'
            ],
            'name_last' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Фамилия обязательна для заполнения!'],
                'pattern' => ['value' => PATTERN_FAMILY_RUS, 'msg' => 'Для фамилии можно использовать ' . MSG_FAMILY_RUS . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинная фамилия!'],
                'success' => 'Фамилия заполнена верно.'
            ],
            'name_first' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Имя обязательно для заполнения!'],
                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для имени можно использовать ' . MSG_ALPHA_RUS . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное имя!'],
                'success' => 'Имя заполнено верно.'
            ],
            'name_middle' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для отчества можно использовать ' . MSG_ALPHA_RUS . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное отчество!'],
                'success' => 'Отчество заполнено верно.'
            ],
            'phone_number' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Контактный телефон обязателен для заполнения'],
                'success' => 'Номер мобильного телефона заполнен верно.'
            ],
            'id_doctype' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код вида Док. удост. личн. получен успешно.'
            ],
            'passport_type' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Тип документа обязателен для заполнения!'],
                'success' => 'Тип документа заполнен верно.'
            ],
            'series' => [
                'type' => 'text',
                'class' => 'form-control',
                'width' => ['format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!'],
                'success' => 'Серия заполнена верно.'
            ],
            'numb' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Номер обязателен для заполнения!'],
                'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для номера можно использовать ' . MSG_NUMB . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 15, 'msg' => 'Слишком длинный номер!'],
                'success' => 'Номер заполнен верно.'
            ],
            'sex' => [
                'type' => 'radio',
                'class' => 'form-check-input',
                'required' => [ 'default' => '', 'msg' => 'Пол обязателен для заполнения!' ],
                'success' => 'Пол заполнен верно.'
            ],
            'birth_dt' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Дата рождения обязательна для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_DATE_STRONG, 'msg' => 'Дата рождения должна быть '.MSG_DATE_STRONG.'!' ],
                'compared' => [ 'value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата рождения больше текущей даты или равна ей!' ],
                'success' => 'Дата рождения заполнена верно.'
            ],
            'birth_place' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Место рождения обязательно для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_TEXT_RUS, 'msg' => 'Для места рождения можно использовать '.MSG_TEXT_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 240, 'msg' => 'Слишком длинное место рождения!' ],
                'success' => 'Место рождения заполнено верно.'
            ],
            'citizenship' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Гражданство обязательно для заполнения!' ],
                'success' => 'Гражданство заполнено верно.'
            ],
            'dt_issue' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Дата выдачи обязательна для заполнения!'],
                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть ' . MSG_DATE_STRONG . '!'],
                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!'],
                'success' => 'Дата выдачи заполнена верно.'
            ],
            'unit_name' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования подразделения можно использовать ' . MSG_INFO_RUS . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование подразделения!'],
                'success' => 'Наименование подразделения заполнено верно.'
            ],
            'unit_code' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код подразделения заполнен верно.'
            ],
            'address_reg' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Адрес регистрации обязателен для заполнения!'],
                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для адреса регистрации можно использовать ' . MSG_INFO_RUS . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 4096, 'msg' => 'Слишком длинный адрес регистрации!'],
                'success' => 'Адрес регистрации заполнен верно.'
            ],
            'address_res' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Адрес проживания обязателен для заполнения!'],
                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для адреса проживания можно использовать ' . MSG_INFO_RUS . '!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 4096, 'msg' => 'Слишком длинный адрес проживания!'],
                'success' => 'Адрес проживания заполнен верно.'
            ],
            'has_mat_capital' => [
                'type' => 'checkbox',
                'class' => 'form-check-input',
                'success' => 'Получена информация о материнском капитале.'
            ],
            'comment' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Замечание модератора'
            ]
        ];

        return $rules;
    }

    public function rulesForSubmitLegalWithScans() {
        return array_merge($this->rulesForSubmitLegal(), Model_Scans::createRules('agreement'));
    }

    public function rulesForSubmitLegal()
    {
        $rules = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Номер договора получен успешно'
            ],
            'conf_id' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код заявления успешно получен'
            ],
            'payer_type' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Тип оплаты не предоставлен!'],
                'success' => 'Тип оплаты получен успешно'
            ],
            'status' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Статус договора получен успешно'
            ],
            'org_name' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Название организации для заполнения!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 1024, 'msg' => 'Слишком длинное название!'],
                'success' => 'Название организации заполнена верно.'
            ],
            'comment' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Замечание модератора'
            ]
        ];

        return $rules;
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
        return '<div class="alert alert-info">Тип: <strong>' . mb_convert_case(DB_Agreement::TYPES[$type], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
    }

    /**
     * Shows status.
     *
     * @param $status
     *
     * @return string
     */
    public static function showStatus($data)
    {
        if (isset($data['id']) && $data['id'] > 0) {
            return '<div class="alert alert-danger">Состояние: <strong>' . mb_convert_case(DB_Agreement::STATUSES[$data['status']], MB_CASE_UPPER, 'UTF-8') . '</strong></div>';
        }

        return '';
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
        $agreement = new DB_Agreement();
        $agreement->id = $form['id'];
        $agreement_row = $agreement->get();

        if (is_array($agreement_row) && count($agreement_row) > 0 && in_array($agreement_row['status'], [
                DB_Agreement::STATUS_CREATED, DB_Agreement::STATUS_ALLOWED, DB_Agreement::STATUS_DISALLOWED, DB_Agreement::STATUS_SAVED_PAYER_DATA, DB_Agreement::STATUS_SAVED_SCANS, DB_Agreement::STATUS_REJECTED])) {
            if ($agreement->clear() > 0) {
                $_SESSION[APP_CODE]['success_msg'] = 'Договор с кодом ' . $form['id'] . ' удалено.';

                return TRUE;
            }
            $_SESSION[APP_CODE]['error_msg'] = 'Ошибка удаления договора с кодом ' . $form['id'] . '! Свяжитесь с администратором.';

            return FALSE;
        }

        $_SESSION[APP_CODE]['error_msg'] = 'Удалять договора можно только с состоянием: ' .
            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_PAYER_DATA], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_SCANS], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_ALLOWED], MB_CASE_UPPER, 'UTF-8') . '</strong>, '.
            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_DISALLOWED], MB_CASE_UPPER, 'UTF-8') . '</strong>, '.
            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_REJECTED], MB_CASE_UPPER, 'UTF-8') . '</strong>!';

        return FALSE;
    }

    /**
     * Checks application spec data.
     *
     * @return array
     */
    public function checkSavePersonal($form)
    {
        if (!isset($form['error_msg'])) {
            if(!isset($form['birth_dt']) || time() - strtotime($form['birth_dt']) < 18 * 31536000) {
                $form['error_msg'] = 'Возраст оплачивающего физлица должен быть не менее 18 лет!';
                return $form;
            }
            if (isset($form['id']) && $form['id'] > 0) {
                $agreement = new DB_Agreement();
                $agreement->id = $form['id'];
                $agreement_row = $agreement->get();

                if (is_array($agreement_row) && count($agreement_row) > 0) {
                    if ($agreement_row['active'] != 1) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у активных договоров!';
                        return $form;
                    }

                    if (!in_array($agreement_row['status'], [DB_Agreement::STATUS_CREATED, DB_Agreement::STATUS_SAVED_PAYER_DATA])) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у договоров с состоянием: ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_PAYER_DATA], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
                        return $form;
                    }
                }
            } else {
                if (!isset($form['conf_id']) || $form['conf_id'] <= 0) {
                    $form['error_msg'] = 'Нет согласия, на основании которого можно заключать договоор';
                    return $form;
                }

                $conf = new \common\models\Model_ApplicationConfirm();
                $conf->id = $form['conf_id'];
                $place = $conf->getSelectedPlace();

                if (!is_array($place) || count($place) < 1) {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор нет выбраных направлений подготовки';
                    return $form;
                }

                if ($place['finance_code'] != '000000002') {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор выбрано не платное направлений подготовки';
                    return $form;
                }
            }
        }

        return $form;
    }

    public function checkSaveLegal(array $form)
    {
        if (!isset($form['error_msg'])) {
            if (isset($form['id']) && $form['id'] > 0) {
                $agreement = new DB_Agreement();
                $agreement->id = $form['id'];
                $agreement_row = $agreement->get();

                if (is_array($agreement_row) && count($agreement_row) > 0) {
                    if ($agreement_row['active'] != 1) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у активных договоров!';
                        return $form;
                    }

                    if (!in_array($agreement_row['status'], [DB_Agreement::STATUS_CREATED, DB_Agreement::STATUS_SAVED_PAYER_DATA])) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у договоров с состоянием: ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_PAYER_DATA], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
                        return $form;
                    }
                }
            } else {
                if (!isset($form['conf_id']) || $form['conf_id'] <= 0) {
                    $form['error_msg'] = 'Нет согласия, на основании которого можно заключать договоор';
                    return $form;
                }

                $conf = new \common\models\Model_ApplicationConfirm();
                $conf->id = $form['conf_id'];
                $place = $conf->getSelectedPlace();

                if (!is_array($place) || count($place) < 1) {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор нет выбраных направлений подготовки';
                    return $form;
                }

                if ($place['finance_code'] != '000000002') {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор выбрано не платное направлений подготовки';
                    return $form;
                }
            }
        }

        return $form;
    }

    public function checkSaveMe(array $form)
    {
        if (!isset($form['error_msg'])) {
            if (isset($form['id']) && $form['id'] > 0) {
                $agreement = new DB_Agreement();
                $agreement->id = $form['id'];
                $agreement_row = $agreement->get();

                if (is_array($agreement_row) && count($agreement_row) > 0) {
                    if ($agreement_row['active'] != 1) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у активных договоров!';
                        return $form;
                    }

                    if (!in_array($agreement_row['status'], [DB_Agreement::STATUS_CREATED, DB_Agreement::STATUS_SAVED_PAYER_DATA])) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у договоров с состоянием: ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_PAYER_DATA], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
                        return $form;
                    }
                }
            } else {
                if (!isset($form['conf_id']) || $form['conf_id'] <= 0) {
                    $form['error_msg'] = 'Нет согласия, на основании которого можно заключать договоор';
                    return $form;
                }

                $conf = new \common\models\Model_ApplicationConfirm();
                $conf->id = $form['conf_id'];
                $place = $conf->getSelectedPlace();

                if (!is_array($place) || count($place) < 1) {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор нет выбраных направлений подготовки';
                    return $form;
                }

                if ($place['finance_code'] != '000000002') {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор выбрано не платное направлений подготовки';
                    return $form;
                }
            }
        }

        return $form;
    }


    public function savePayer($form)
    {
        if (isset($form['id']) && $form['id'] > 0) {
            $agreement = new DB_Agreement();
            $agreement->load($form['id']);

            if ($agreement->id_user == $_SESSION[APP_CODE]['user_id']) {
                switch ($form['payer_type']) {
                    case \common\models\Model_Agreement::PAYER_SELF:
                        $agreement->payer_type = $agreement::PAYER_SELF;
                        $agreement->is_self_payer = 1;

                        $agreement->org_name = NULL;

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;
                        $agreement->unit_code = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_PERSON:
                        $agreement->payer_type = $agreement::PAYER_PERSON;
                        $agreement->is_self_payer = 0;
                        $agreement->has_mat_capital = isset($form['has_mat_capital']) ? 1 : 0;

                        $agreement->org_name = NULL;

                        $agreement->name_first = $form['name_first'];
                        $agreement->name_last = $form['name_last'];
                        $agreement->name_middle = $form['name_middle'];

                        $dict = new \common\models\Model_DictDoctypes();
                        $dict->code = $form['passport_type'];

                        $agreement->sex         = $form['sex'];
                        $agreement->id_doctype = $dict->getByCode()['id'];
                        $agreement->series = $form['series'];
                        $agreement->numb = $form['numb'];
                        $agreement->dt_issue = (empty($form['dt_issue']) ? null : date('Y-m-d', strtotime($form['dt_issue'])));;
                        $agreement->unit_name = $form['unit_name'];
                        $agreement->unit_code  = $form['unit_code'];

                        $agreement->address_reg = $form['address_reg'];
                        $agreement->address_res = $form['address_res'];

                        $agreement->phone_number = $form['phone_number'];

                        $agreement->status = $agreement::STATUS_SAVED_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_LEGAL_AGENT:
                        $agreement->payer_type = $agreement::PAYER_LEGAL_AGENT;

                        $agreement->org_name = $form['org_name'];

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;
                        $agreement->unit_code = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SAVED_PAYER_DATA;
                        break;
                    default:
                        return FALSE;
                        break;
                }

                $agreement->dt_updated = date('Y-m-d H:i:s');
                $agreement->active = 1;

                $agreement->changeAll();
            }

            return FALSE;
        } elseif (isset($form['conf_id']) && count($form['conf_id']) > 0) {
            $agreement = new DB_Agreement();
            $agreement->id_confirm = $form['conf_id'];
            $agreement->id_user = $_SESSION[APP_CODE]['user_id'];

            if($agreement->available()) {
                switch ($form['payer_type']) {
                    case \common\models\Model_Agreement::PAYER_SELF:
                        $agreement->payer_type = $agreement::PAYER_SELF;
                        $agreement->is_self_payer = 1;

                        $agreement->org_name = NULL;

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;
                        $agreement->unit_code = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_PERSON:
                        $agreement->payer_type = $agreement::PAYER_PERSON;
                        $agreement->is_self_payer = 0;
                        $agreement->has_mat_capital = isset($form['has_mat_capital']) ? 1 : 0;

                        $agreement->org_name = NULL;

                        $agreement->name_first = $form['name_first'];
                        $agreement->name_last = $form['name_last'];
                        $agreement->name_middle = $form['name_middle'];

                        $agreement->sex         = $form['sex'];
                        $agreement->birth_dt    = date('Y-m-d', strtotime($form['birth_dt']));
                        $agreement->birth_place = $form['birth_place'];

                        $citizenship           = new Model_DictCountries();
                        $citizenship->code     = $form['citizenship'];
                        $row_citizenship       = $citizenship->getByCode();
                        $agreement->citizenship = $row_citizenship['id'];

                        $dict = new \common\models\Model_DictDoctypes();
                        $dict->code = $form['passport_type'];

                        $agreement->id_doctype = $dict->getByCode()['id'];
                        $agreement->series = $form['series'];
                        $agreement->numb = $form['numb'];
                        $agreement->dt_issue = (empty($form['dt_issue']) ? null : date('Y-m-d', strtotime($form['dt_issue'])));;
                        $agreement->unit_name = $form['unit_name'];
                        $agreement->unit_code  = $form['unit_code'];

                        $agreement->address_reg = $form['address_reg'];
                        $agreement->address_res = $form['address_res'];

                        $agreement->phone_number = $form['phone_number'];

                        $agreement->status = $agreement::STATUS_SAVED_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_LEGAL_AGENT:
                        $agreement->payer_type = $agreement::PAYER_LEGAL_AGENT;

                        $agreement->org_name = $form['org_name'];

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SAVED_PAYER_DATA;
                        break;
                    default:
                        return FALSE;
                        break;
                }

                $agreement->dt_created = date('Y-m-d H:i:s');
                $agreement->active = 1;

                $agreement->save();
            }
        }

        return FALSE;
    }

    public function checkSendPersonal(array $form)
    {
        if (!isset($form['error_msg'])) {
            if (isset($form['id']) && $form['id'] > 0) {
                $agreement = new DB_Agreement();
                $agreement->id = $form['id'];
                $agreement_row = $agreement->get();

                if (is_array($agreement_row) && count($agreement_row) > 0) {
                    if ($agreement_row['active'] != 1) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у активных договоров!';
                        return $form;
                    }

                    if (!in_array($agreement_row['status'], [DB_Agreement::STATUS_CREATED, DB_Agreement::STATUS_SAVED_PAYER_DATA])) {
                        $form['error_msg'] = 'Отправлять информацию о плательщике договора можно у договоров с состоянием: ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_PAYER_DATA], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
                        return $form;
                    }
                }
            } else {
                if (!isset($form['conf_id']) || $form['conf_id'] <= 0) {
                    $form['error_msg'] = 'Нет согласия, на основании которого можно заключать договоор';
                    return $form;
                }

                $conf = new \common\models\Model_ApplicationConfirm();
                $conf->id = $form['conf_id'];
                $place = $conf->getSelectedPlace();

                if (!is_array($place) || count($place) < 1) {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор нет выбраных направлений подготовки';
                    return $form;
                }

                if ($place['finance_code'] != '000000002') {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор выбрано не платное направлений подготовки';
                    return $form;
                }
            }
        }

        return $form;
    }

    public function sendPayer($form)
    {
        if (isset($form['id']) && $form['id'] > 0) {
            $agreement = new DB_Agreement();
            $agreement->load($form['id']);

            if ($agreement->id_user == $_SESSION[APP_CODE]['user_id']) {
                switch ($form['payer_type']) {
                    case \common\models\Model_Agreement::PAYER_SELF:
                        $agreement->payer_type = $agreement::PAYER_SELF;
                        $agreement->is_self_payer = 1;

                        $agreement->org_name = NULL;

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_PERSON:
                        $agreement->payer_type = $agreement::PAYER_PERSON;
                        $agreement->is_self_payer = 0;
                        $agreement->has_mat_capital = isset($form['has_mat_capital']) ? 1 : 0;

                        $agreement->org_name = NULL;

                        $agreement->name_first = $form['name_first'];
                        $agreement->name_last = $form['name_last'];
                        $agreement->name_middle = $form['name_middle'];

                        $agreement->sex         = $form['sex'];
                        $agreement->birth_dt    = date('Y-m-d', strtotime($form['birth_dt']));
                        $agreement->birth_place = $form['birth_place'];

                        $citizenship           = new Model_DictCountries();
                        $citizenship->code     = $form['citizenship'];
                        $row_citizenship       = $citizenship->getByCode();
                        $agreement->citizenship = $row_citizenship['id'];

                        $dict = new \common\models\Model_DictDoctypes();
                        $dict->code = $form['passport_type'];

                        $agreement->id_doctype = $dict->getByCode()['id'];
                        $agreement->series = $form['series'];
                        $agreement->numb = $form['numb'];
                        $agreement->dt_issue = (empty($form['dt_issue']) ? null : date('Y-m-d', strtotime($form['dt_issue'])));;
                        $agreement->unit_name = $form['unit_name'];

                        $agreement->address_reg = $form['address_reg'];
                        $agreement->address_res = $form['address_res'];

                        $agreement->phone_number = $form['phone_number'];

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_LEGAL_AGENT:
                        $agreement->payer_type = $agreement::PAYER_LEGAL_AGENT;

                        $agreement->org_name = $form['org_name'];

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    default:
                        return FALSE;
                        break;
                }

                $agreement->dt_updated = date('Y-m-d H:i:s');
                $agreement->active = 1;

                $agreement->changeAll();
            }

            return FALSE;
        } elseif (isset($form['conf_id']) && count($form['conf_id']) > 0) {
            $agreement = new DB_Agreement();
            $agreement->id_confirm = $form['conf_id'];
            $agreement->id_user = $_SESSION[APP_CODE]['user_id'];

            if($agreement->available()) {
                switch ($form['payer_type']) {
                    case \common\models\Model_Agreement::PAYER_SELF:
                        $agreement->payer_type = $agreement::PAYER_SELF;
                        $agreement->is_self_payer = 1;

                        $agreement->org_name = NULL;

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_PERSON:
                        $agreement->payer_type = $agreement::PAYER_PERSON;
                        $agreement->is_self_payer = 0;
                        $agreement->has_mat_capital = isset($form['has_mat_capital']) ? 1 : 0;

                        $agreement->org_name = NULL;

                        $agreement->name_first = $form['name_first'];
                        $agreement->name_last = $form['name_last'];
                        $agreement->name_middle = $form['name_middle'];

                        $agreement->sex         = $form['sex'];
                        $agreement->birth_dt    = date('Y-m-d', strtotime($form['birth_dt']));
                        $agreement->birth_place = $form['birth_place'];

                        $citizenship           = new Model_DictCountries();
                        $citizenship->code     = $form['citizenship'];
                        $row_citizenship       = $citizenship->getByCode();
                        $agreement->citizenship = $row_citizenship['id'];

                        $dict = new \common\models\Model_DictDoctypes();
                        $dict->code = $form['passport_type'];

                        $agreement->id_doctype = $dict->getByCode()['id'];
                        $agreement->series = $form['series'];
                        $agreement->numb = $form['numb'];
                        $agreement->dt_issue = (empty($form['dt_issue']) ? null : date('Y-m-d', strtotime($form['dt_issue'])));;
                        $agreement->unit_name = $form['unit_name'];

                        $agreement->address_reg = $form['address_reg'];
                        $agreement->address_res = $form['address_res'];

                        $agreement->phone_number = $form['phone_number'];

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    case \common\models\Model_Agreement::PAYER_LEGAL_AGENT:
                        $agreement->payer_type = $agreement::PAYER_LEGAL_AGENT;

                        $agreement->org_name = $form['org_name'];

                        $agreement->name_first = NULL;
                        $agreement->name_last = NULL;
                        $agreement->name_middle = NULL;

                        $agreement->sex = NULL;
                        $agreement->birth_dt = NULL;
                        $agreement->birth_place = NULL;
                        $agreement->citizenship = NULL;

                        $agreement->id_doctype = NULL;
                        $agreement->series = NULL;
                        $agreement->numb = NULL;
                        $agreement->dt_issue = NULL;
                        $agreement->unit_name = NULL;

                        $agreement->address_reg = NULL;
                        $agreement->address_res = NULL;

                        $agreement->phone_number = NULL;

                        $agreement->status = $agreement::STATUS_SENT_PAYER_DATA;
                        break;
                    default:
                        return FALSE;
                        break;
                }

                $agreement->dt_created = date('Y-m-d H:i:s');
                $agreement->active = 1;

                $agreement->save();
            }
        }

        return FALSE;
    }

    public function checkSendLegal(array $form)
    {
        if (!isset($form['error_msg'])) {
            if (isset($form['id']) && $form['id'] > 0) {
                $agreement = new DB_Agreement();
                $agreement->id = $form['id'];
                $agreement_row = $agreement->get();

                if (is_array($agreement_row) && count($agreement_row) > 0) {
                    if ($agreement_row['active'] != 1) {
                        $form['error_msg'] = 'Сохранять информацию о плательщике договора можно у активных договоров!';
                        return $form;
                    }

                    if (!in_array($agreement_row['status'], [DB_Agreement::STATUS_CREATED, DB_Agreement::STATUS_SAVED_PAYER_DATA])) {
                        $form['error_msg'] = 'Отправлять на модерацию информацию о плательщике договора можно у договоров с состоянием: ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_CREATED], MB_CASE_UPPER, 'UTF-8') . '</strong>, ' .
                            '<strong>' . mb_convert_case(DB_Agreement::STATUSES[DB_Agreement::STATUS_SAVED_PAYER_DATA], MB_CASE_UPPER, 'UTF-8') . '</strong>!';
                        return $form;
                    }
                }
            } else {
                if (!isset($form['conf_id']) || $form['conf_id'] <= 0) {
                    $form['error_msg'] = 'Нет согласия, на основании которого можно заключать договоор';
                    return $form;
                }

                $conf = new \common\models\Model_ApplicationConfirm();
                $conf->id = $form['conf_id'];
                $place = $conf->getSelectedPlace();

                if (!is_array($place) || count($place) < 1) {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор нет выбраных направлений подготовки';
                    return $form;
                }

                if ($place['finance_code'] != '000000002') {
                    $form['error_msg'] = 'В согласии, на основании которого формируется договор выбрано не платное направлений подготовки';
                    return $form;
                }
            }
        }

        return $form;
    }

    public function saveScans($form)
    {
        /* scans */
        $dict_scans = new Model_DictScans();
        $dict_scans->doc_code = 'agreement';


        $dict_scans_arr = $dict_scans->getByDocument();
        if ($dict_scans_arr) {
            foreach ($dict_scans_arr as $dict_scans_row) {
                $form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
                if (!empty($form['error_msg'])) {
                    return $form;
                }
            }
        }

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
        $app = new DB_Agreement();
        $app->id = $id;

        return $app->getWithDocs();
    }

    /**
     * Gets application spec data from database.
     *
     * @param $conf_id
     *
     * @return array|NULL|false
     */
    public function getByApp($conf_id)
    {
        $app = new DB_Agreement();
        $app->id_confirm = $conf_id;

        return $app->getByConfirmId();
    }

    public function formPost($conf_id)
    {
        if (!is_array($this->getByApp($conf_id))) {
            return (new DB_Agreement())->buildFromApplication($conf_id);
        }

        return NULL;
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
}
