<?php

namespace frontend\models;

use common\models\Model_Address as Model_Address;
use common\models\Model_Application as Application;
use common\models\Model_Contacts as Model_Contacts;
use common\models\Model_DictCountries as Model_DictCountries;
use common\models\Model_DictDoctypes as Model_DictDoctypes;
use common\models\Model_DictForeignLangs as DictForeignLangs;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_ForeignLangs as ForeignLangs;
use common\models\Model_Kladr as Kladr;
use common\models\Model_Passport as Model_Passport;
use common\models\Model_Personal as Personal;
use common\models\Model_Resume as Resume;
use tinyframe\core\helpers\Calc_Helper as Calc_Helper;
use tinyframe\core\Model as Model;

include ROOT_DIR.'/application/frontend/models/Model_Scans.php';

class Model_Resume
    extends Model
{
    /*
        Resume processing
    */
    
    /**
     * Resume rules.
     *
     * @return array
     */
    public function rules()
    {
        $rules             = [
            'name_last' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Фамилия обязательна для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_FAMILY_RUS, 'msg' => 'Для фамилии можно использовать '.MSG_FAMILY_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинная фамилия!' ],
                'success' => 'Фамилия заполнена верно.'
            ],
            'name_first' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Имя обязательно для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_ALPHA_RUS, 'msg' => 'Для имени можно использовать '.MSG_ALPHA_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное имя!' ],
                'success' => 'Имя заполнено верно.'
            ],
            'name_middle' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_ALPHA_RUS, 'msg' => 'Для отчества можно использовать '.MSG_ALPHA_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное отчество!' ],
                'success' => 'Отчество заполнено верно.'
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
            'agreement' => [
                'type' => 'file',
                'class' => 'form-control',
                'name' => [ 'value' => FILES_NAME, 'msg' => 'Имя файла скан-копии "Согласие родителей/опекунов" превышает '.FILES_NAME.' знаков!' ],
                'size' => [ 'value' => FILES_SIZE['value'], 'msg' => 'Размер скан-копии "Согласие родителей/опекунов" превышает '.FILES_SIZE['value'].' '.FILES_SIZE['size'].' !' ],
                'ext' => [ 'value' => FILES_EXT_SCANS, 'msg' => 'Недопустимый тип скан-копии "Согласие родителей/опекунов"!' ],
                'success' => 'Скан-копия "Согласие родителей/опекунов" заполнена верно.'
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
            'beneficiary' => [
                'type' => 'checkbox',
                'class' => 'form-check-input',
                'success' => ''
            ],
            'email' => [
                'type' => 'email',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть '.MSG_EMAIL_LIGHT ],
                'width' => [ 'format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!' ],
                'success' => 'Адрес эл. почты заполнен верно.'
            ],
            'phone_mobile' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Номер мобильного телефона заполнен верно.'
            ],
            'phone_home' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_NUMB, 'msg' => 'Номер домашнего телефона должен содержать '.MSG_NUMB.'!' ],
                'success' => 'Номер домашнего телефона заполнен верно.'
            ],
            'phone_add' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_PHONE_ADD, 'msg' => 'Номер дополнительного телефона должен содержать '.MSG_PHONE_ADD.'!' ],
                'success' => 'Номер дополнительного телефона заполнен верно.'
            ],
            'passport_type' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Тип документа обязателен для заполнения!' ],
                'success' => 'Тип документа заполнен верно.'
            ],
            'series' => [
                'type' => 'text',
                'class' => 'form-control',
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!' ],
                'success' => 'Серия заполнена верно.'
            ],
            'numb' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Номер обязателен для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_NUMB, 'msg' => 'Для номера можно использовать '.MSG_NUMB.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 15, 'msg' => 'Слишком длинный номер!' ],
                'success' => 'Номер заполнен верно.'
            ],
            'dt_issue' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Дата выдачи обязательна для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть '.MSG_DATE_STRONG.'!' ],
                'compared' => [ 'value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!' ],
                'success' => 'Дата выдачи заполнена верно.'
            ],
            'unit_name' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования подразделения можно использовать '.MSG_INFO_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование подразделения!' ],
                'success' => 'Наименование подразделения заполнено верно.'
            ],
            'unit_code' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код подразделения заполнен верно.'
            ],
            'dt_end' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_DATE_STRONG, 'msg' => 'Дата окончания действия должна быть '.MSG_DATE_STRONG.'!' ],
                'compared' => [ 'value' => date('d.m.Y'), 'type' => '>', 'msg' => 'Дата окончания действия меньше текущей даты или равна ей!' ],
                'success' => 'Дата окончания действия заполнена верно.'
            ],
            'passport_old_yes' => [
                'type' => 'checkbox',
                'class' => 'form-check-input',
                'success' => ''
            ],
            'passport_type_old' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'success' => 'Тип документа заполнен верно.'
            ],
            'series_old' => [
                'type' => 'text',
                'class' => 'form-control',
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!' ],
                'success' => 'Серия заполнена верно.'
            ],
            'numb_old' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_NUMB, 'msg' => 'Для номера можно использовать '.MSG_NUMB.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 15, 'msg' => 'Слишком длинный номер!' ],
                'success' => 'Номер заполнен верно.'
            ],
            'dt_issue_old' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть '.MSG_DATE_STRONG.'!' ],
                'compared' => [ 'value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!' ],
                'success' => 'Дата выдачи заполнена верно.'
            ],
            'unit_name_old' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования подразделения можно использовать '.MSG_INFO_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование подразделения!' ],
                'success' => 'Наименование подразделения заполнено верно.'
            ],
            'unit_code_old' => [
                'type' => 'text',
                'class' => 'form-control',
                'success' => 'Код подразделения заполнен верно.'
            ],
            'dt_end_old' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_DATE_STRONG, 'msg' => 'Дата окончания действия должна быть '.MSG_DATE_STRONG.'!' ],
                'compared' => [ 'value' => date('d.m.Y'), 'type' => '>', 'msg' => 'Дата окончания действия меньше текущей даты или равна ей!' ],
                'success' => 'Дата окончания действия заполнена верно.'
            ],
            'passport_old' => [
                'type' => 'file',
                'class' => 'form-control',
                'name' => [ 'value' => FILES_NAME, 'msg' => 'Имя файла скан-копии "Сведения о ранее выданных паспортах" превышает '.FILES_NAME.' знаков!' ],
                'size' => [
                    'value' => FILES_SIZE['value'],
                    'msg' => 'Размер скан-копии "Сведения о ранее выданных паспортах" превышает '.FILES_SIZE['value'].' '.FILES_SIZE['size'].' !'
                ],
                'ext' => [ 'value' => FILES_EXT_SCANS, 'msg' => 'Недопустимый тип скан-копии "Сведения о ранее выданных паспортах"!' ],
                'success' => 'Скан-копия "Сведения о ранее выданных паспортах" заполнена верно.'
            ],
            'country_reg' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Страна регистрации обязательна для заполнения!' ],
                'success' => 'Страна регистрации заполнена верно.'
            ],
            'address_reg' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Адрес регистрации обязателен для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_INFO_RUS, 'msg' => 'Для адреса регистрации можно использовать '.MSG_INFO_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 255, 'msg' => 'Слишком длинный адрес регистрации!' ],
                'success' => 'Адрес регистрации заполнен верно.'
            ],
            'country_res' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Страна проживания обязательна для заполнения!' ],
                'success' => 'Страна проживания заполнена верно.'
            ],
            'address_res' => [
                'type' => 'text',
                'class' => 'form-control',
                'required' => [ 'default' => '', 'msg' => 'Адрес проживания обязателен для заполнения!' ],
                'pattern' => [ 'value' => PATTERN_INFO_RUS, 'msg' => 'Для адреса проживания можно использовать '.MSG_INFO_RUS.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 255, 'msg' => 'Слишком длинный адрес проживания!' ],
                'success' => 'Адрес проживания заполнен верно.'
            ],
            'personal' => [
                'type' => 'checkbox',
                'class' => 'form-check-input',
                'required' => [ 'default' => '', 'msg' => 'Необходимо согласие на обработку персональных данных!' ],
                'success' => 'Получено согласие на обработку персональных данных.'
            ]
        ];
        $scans             = Model_Scans::createRules('resume');
        $rules             = array_merge($rules, $scans);
        $rules['personal'] = [
            'type' => 'checkbox',
            'class' => 'form-check-input',
            'required' => [ 'default' => '', 'msg' => 'Необходимо согласие на обработку персональных данных!' ],
            'success' => 'Получено согласие на обработку персональных данных.'
        ];
        
        return $rules;
    }
    
    /**
     * Shows status.
     *
     * @return string
     */
    public static function showStatus( $status, $width = NULL )
    {
        // width
        if( !empty($width) && $width > 0 && $width < 12 ) {
            $div = '<div class="col col-sm-'.$width.' ';
        } else {
            $div = '<div class="';
        }
        // status
        switch ( $status ) {
            case Resume::STATUS_CREATED:
                return $div.'alert alert-info">Состояние: <strong>'.mb_convert_case(Resume::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
            case Resume::STATUS_SAVED:
                return $div.'alert alert-info">Состояние: <strong>'.mb_convert_case(Resume::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
            case Resume::STATUS_SENDED:
                return $div.'alert alert-primary">Состояние: <strong>'.mb_convert_case(Resume::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
            case Resume::STATUS_APPROVED:
                return $div.'alert alert-success">Состояние: <strong>'.mb_convert_case(Resume::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
            case Resume::STATUS_REJECTED:
                return $div.'alert alert-danger">Состояние: <strong>'.mb_convert_case(Resume::STATUS_REJECTED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
            default:
                return $div.'alert alert-warning">Состояние: <strong>НЕИЗВЕСТНО</strong></div>';
        }
    }
    
    /**
     * Validates resume advanced.
     *
     * @return array
     */
    public function validateFormAdvanced( $form )
    {
        // birth_dt
        if( !empty($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') <= 12 ) {
            $form = $this->setFormErrorField($form, 'birth_dt', 'Ваш возраст меньше или равен 12 лет!');
            
            return $form;
        }
        // dt_issue
        if( !empty($form['dt_issue']) && date('Y-m-d', strtotime($form['dt_issue'])) <= date('Y-m-d', strtotime($form['birth_dt'])) ) {
            $form = $this->setFormErrorField($form, 'dt_issue', 'Дата выдачи документа, удостоверяющего личность, меньше или равна дате рождения!');
            
            return $form;
        }
        // phones
        if( empty($form['phone_mobile']) && empty($form['phone_home']) ) {
            $form                     = $this->setFormError($form, 'Необходимо заполнить мобильный или домашний номер телефона в контактной информации!');
            $form['phone_mobile_err'] = 'Ни мобильный, ни домашний номера телефонов не заполнены!';
            $form['phone_home_err']   = 'Ни мобильный, ни домашний номера телефонов не заполнены!';
            
            return $form;
        }
        
        $form = $this->checkLangCount($form);
        
        return $form;
    }
    
    /**
     * Validates agreement.
     *
     * @return array
     */
    public function validateAgreement( $form )
    {
        if( !empty($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') < 18 && empty($form['agreement']) ) {
            $form = $this->setFormErrorFile($form, 'agreement', 'Скан-копия "Согласие родителей/опекунов" обязательна для заполнения!');
        }
        
        return $form;
    }
    
    /**
     * Validates passport.
     *
     * @return array
     */
    public function validatePassport( $form )
    {
        if( !empty($form['passport_type']) ) {
            switch ( $form['passport_type'] ) {
                // Паспорт РФ
                case '000000047':
                    // series
                    if( empty($form['series']) ) {
                        $form = $this->setFormErrorField($form, 'series', 'Серия обязательна для заполнения!');
                    }
                    // unit_name
                    if( empty($form['unit_name']) ) {
                        $form = $this->setFormErrorField($form, 'unit_name', 'Наименование подразделения обязательно для заполнения!');
                    }
                    // unit_code
                    if( empty($form['unit_code']) ) {
                        $form = $this->setFormErrorField($form, 'unit_code', 'Код подразделения обязателен для заполнения!');
                    }
                    $form = $this->checkPassportRussian($form);
                    break;
                // Паспорт иностранного гражданина
                case '000000049':
                    // series
                    //if( empty($form['series']) ) {
                    //    $form = $this->setFormErrorField($form, 'series', 'Серия обязательна для заполнения!');
                    //}
                    // unit_name
                    if( empty($form['unit_name']) ) {
                        $form = $this->setFormErrorField($form, 'unit_name', 'Наименование подразделения обязательно для заполнения!');
                    }
                    $form = $this->checkPassportForeign($form);
                    break;
                // Вид на жительство иностранного гражданина
                case '000000075':
                    // series
                    if( empty($form['series']) ) {
                        $form = $this->setFormErrorField($form, 'series', 'Серия обязательна для заполнения!');
                    }
                    // unit_name
                    if( empty($form['unit_name']) ) {
                        $form = $this->setFormErrorField($form, 'unit_name', 'Наименование подразделения обязательно для заполнения!');
                    }
                    // dt_end
                    if( empty($form['dt_end']) ) {
                        $form = $this->setFormErrorField($form, 'dt_end', 'Дата окончания действия обязательна для заполнения!');
                    }
                    $form = $this->checkResidencyForeign($form);
                    break;
                // Временное удостоверение личности гражданина РФ
                case '000000202':
                    // unit_name
                    if( empty($form['unit_name']) ) {
                        $form = $this->setFormErrorField($form, 'unit_name', 'Наименование подразделения обязательно для заполнения!');
                    }
                    // dt_end
                    if( empty($form['dt_end']) ) {
                        $form = $this->setFormErrorField($form, 'dt_end', 'Дата окончания действия обязательна для заполнения!');
                    }
                    $form = $this->checkIdRussian($form);
                    break;
                // Удостоверение личности иностранного гражданина
                case '000000223':
                    // dt_end
                    if( empty($form['dt_end']) ) {
                        $form = $this->setFormErrorField($form, 'dt_end', 'Дата окончания действия обязательна для заполнения!');
                    }
                    $form = $this->checkIdForeign($form);
                    break;
                // Свидетельство о рождении, выданное уполномоченным органом иностранного государства
                case '000000226':
                    // series
                    if( empty($form['series']) ) {
                        $form = $this->setFormErrorField($form, 'series', 'Серия обязательна для заполнения!');
                    }
                    // unit_name
                    if( empty($form['unit_name']) ) {
                        $form = $this->setFormErrorField($form, 'unit_name', 'Наименование подразделения обязательно для заполнения!');
                    }
                    $form = $this->checkCertificateBirth($form);
                    break;
            }
        }
        
        return $form;
    }
    
    /**
     * Validates old passport.
     *
     * @return array
     */
    public function validatePassportOld( $form )
    {
        if( $form['passport_old_yes'] == 'checked' ) {
            // type
            if( empty($form['passport_type_old']) ) {
                $form = $this->setFormErrorField($form, 'passport_type_old', 'Тип документа обязателен для заполнения!');
            } else {
                if( $form['passport_type_old'] == '000000047' ) {
                    // series
                    if( empty($form['series_old']) ) {
                        $form = $this->setFormErrorField($form, 'series_old', 'Серия обязательна для заполнения!');
                    }
                    // unit_code
                    if( empty($form['unit_code_old']) ) {
                        $form = $this->setFormErrorField($form, 'unit_code_old', 'Код подразделения обязателен для заполнения!');
                    }
                    // passport_old
                    if( empty($form['passport_old']) ) {
                        $form = $this->setFormErrorFile($form, 'passport_old', 'Скан-копия "Сведения о ранее выданных паспортах" обязательна для заполнения!');
                    }
                }
                // numb
                if( empty($form['numb_old']) ) {
                    $form = $this->setFormErrorField($form, 'numb_old', 'Номер обязателен для заполнения!');
                }
                // dt_issue
                if( empty($form['dt_issue_old']) ) {
                    $form = $this->setFormErrorField($form, 'dt_issue_old', 'Дата выдачи обязательна для заполнения!');
                } elseif( date('Y-m-d', strtotime($form['dt_issue_old'])) <= date('Y-m-d', strtotime($form['birth_dt'])) ) {
                    $form = $this->setFormErrorField($form, 'dt_issue_old', 'Дата выдачи старого документа, удостоверяющего личность, меньше или равна дате рождения!', 1);
                } elseif( date('Y-m-d', strtotime($form['dt_issue'])) <= date('Y-m-d', strtotime($form['dt_issue_old'])) ) {
                    $form = $this->setFormErrorField($form, 'dt_issue_old',
                                                     'Дата выдачи документа, удостоверяющего личность, меньше или равна дате выдачи старого документа, удостоверяющего личность!',
                                                     1);
                }
            }
        }
        
        return $form;
    }
    
    /**
     * Sets registration address.
     *
     * @return array
     */
    public function setAddressReg( $form )
    {
        $address            = new Model_Address();
        $address->id_resume = $form['id'];
        $address->type      = $address::TYPE_REG;
        $row                = $address->getByResumeType();
        if( $row ) {
            $form['kladr_reg']    = $row['kladr'];
            $form['region_reg']   = $row['region_code'];
            $form['area_reg']     = $row['area_code'];
            $form['city_reg']     = $row['city_code'];
            $form['location_reg'] = $row['location_code'];
            $form['street_reg']   = $row['street_code'];
            $form['house_reg']    = $row['house'];
            $form['building_reg'] = $row['building'];
            $form['flat_reg']     = $row['flat'];
            $form['postcode_reg'] = $row['postcode'];
        } else {
            $form['kladr_reg']    = NULL;
            $form['region_reg']   = NULL;
            $form['area_reg']     = NULL;
            $form['city_reg']     = NULL;
            $form['location_reg'] = NULL;
            $form['street_reg']   = NULL;
            $form['house_reg']    = NULL;
            $form['building_reg'] = NULL;
            $form['flat_reg']     = NULL;
            $form['postcode_reg'] = NULL;
        }
        
        return $form;
    }
    
    /**
     * Sets residential address.
     *
     * @return array
     */
    public function setAddressRes( $form )
    {
        $address            = new Model_Address();
        $address->id_resume = $form['id'];
        $address->type      = $address::TYPE_RES;
        $row                = $address->getByResumeType();
        if( $row ) {
            $form['kladr_res']    = $row['kladr'];
            $form['region_res']   = $row['region_code'];
            $form['area_res']     = $row['area_code'];
            $form['city_res']     = $row['city_code'];
            $form['location_res'] = $row['location_code'];
            $form['street_res']   = $row['street_code'];
            $form['house_res']    = $row['house'];
            $form['building_res'] = $row['building'];
            $form['flat_res']     = $row['flat'];
            $form['postcode_res'] = $row['postcode'];
        } else {
            $form['kladr_res']    = NULL;
            $form['region_res']   = NULL;
            $form['area_res']     = NULL;
            $form['city_res']     = NULL;
            $form['location_res'] = NULL;
            $form['street_res']   = NULL;
            $form['house_res']    = NULL;
            $form['building_res'] = NULL;
            $form['flat_res']     = NULL;
            $form['postcode_res'] = NULL;
        }
        
        return $form;
    }
    
    /**
     * Resets registration address.
     *
     * @return array
     */
    public function resetAddressReg( $form )
    {
        $form['kladr_reg']    = NULL;
        $form['region_reg']   = NULL;
        $form['area_reg']     = NULL;
        $form['city_reg']     = NULL;
        $form['location_reg'] = NULL;
        $form['street_reg']   = NULL;
        $form['house_reg']    = NULL;
        $form['building_reg'] = NULL;
        $form['flat_reg']     = NULL;
        $form['postcode_reg'] = NULL;
        
        return $form;
    }
    
    /**
     * Resets residential address.
     *
     * @return array
     */
    public function resetAddressRes( $form )
    {
        $form['kladr_res']    = NULL;
        $form['region_res']   = NULL;
        $form['area_res']     = NULL;
        $form['city_res']     = NULL;
        $form['location_res'] = NULL;
        $form['street_res']   = NULL;
        $form['house_res']    = NULL;
        $form['building_res'] = NULL;
        $form['flat_res']     = NULL;
        $form['postcode_res'] = NULL;
        
        return $form;
    }
    
    /**
     * Gets registration address.
     *
     * @return array
     */
    public function getAddressReg( $form )
    {
        if( isset($_POST['homeless_reg']) || isset($_POST['kladr_reg_not']) ) {
            $form['kladr_reg']    = 0;
            $form['region_reg']   = NULL;
            $form['area_reg']     = NULL;
            $form['city_reg']     = NULL;
            $form['location_reg'] = NULL;
            $form['street_reg']   = NULL;
            $form['house_reg']    = NULL;
            $form['building_reg'] = NULL;
            $form['flat_reg']     = NULL;
            $form['postcode_reg'] = NULL;
        } else {
            $form['kladr_reg']    = 1;
            $form['region_reg']   = ( isset($_POST['region_reg']) ) ? htmlspecialchars($_POST['region_reg']) : NULL;
            $form['area_reg']     = ( isset($_POST['area_reg']) ) ? htmlspecialchars($_POST['area_reg']) : NULL;
            $form['city_reg']     = ( isset($_POST['city_reg']) ) ? htmlspecialchars($_POST['city_reg']) : NULL;
            $form['location_reg'] = ( isset($_POST['location_reg']) ) ? htmlspecialchars($_POST['location_reg']) : NULL;
            $form['street_reg']   = ( isset($_POST['street_reg']) ) ? htmlspecialchars($_POST['street_reg']) : NULL;
            $form['house_reg']    = ( isset($_POST['house_reg']) ) ? htmlspecialchars($_POST['house_reg']) : NULL;
            $form['building_reg'] = ( isset($_POST['building_reg']) ) ? htmlspecialchars($_POST['building_reg']) : NULL;
            $form['flat_reg']     = ( isset($_POST['flat_reg']) ) ? htmlspecialchars($_POST['flat_reg']) : NULL;
            $form['postcode_reg'] = ( isset($_POST['postcode_reg']) ) ? htmlspecialchars($_POST['postcode_reg']) : NULL;
        }
        
        return $form;
    }
    
    /**
     * Gets residential address.
     *
     * @return array
     */
    public function getAddressRes( $form )
    {
        if( isset($_POST['homeless_res']) || isset($_POST['kladr_res_not']) ) {
            $form['kladr_res']    = 0;
            $form['region_res']   = NULL;
            $form['area_res']     = NULL;
            $form['city_res']     = NULL;
            $form['location_res'] = NULL;
            $form['street_res']   = NULL;
            $form['house_res']    = NULL;
            $form['building_res'] = NULL;
            $form['flat_res']     = NULL;
            $form['postcode_res'] = NULL;
        } else {
            $form['kladr_res']    = 1;
            $form['region_res']   = ( isset($_POST['region_res']) ) ? htmlspecialchars($_POST['region_res']) : NULL;
            $form['area_res']     = ( isset($_POST['area_res']) ) ? htmlspecialchars($_POST['area_res']) : NULL;
            $form['city_res']     = ( isset($_POST['city_res']) ) ? htmlspecialchars($_POST['city_res']) : NULL;
            $form['location_res'] = ( isset($_POST['location_res']) ) ? htmlspecialchars($_POST['location_res']) : NULL;
            $form['street_res']   = ( isset($_POST['street_res']) ) ? htmlspecialchars($_POST['street_res']) : NULL;
            $form['house_res']    = ( isset($_POST['house_res']) ) ? htmlspecialchars($_POST['house_res']) : NULL;
            $form['building_res'] = ( isset($_POST['building_res']) ) ? htmlspecialchars($_POST['building_res']) : NULL;
            $form['flat_res']     = ( isset($_POST['flat_res']) ) ? htmlspecialchars($_POST['flat_res']) : NULL;
            $form['postcode_res'] = ( isset($_POST['postcode_res']) ) ? htmlspecialchars($_POST['postcode_res']) : NULL;
        }
        
        return $form;
    }
    
    /**
     * Sets foreign languages.
     *
     * @return array
     */
    public function setForeignLangs( $form )
    {
        $langs            = new ForeignLangs();
        $langs->id_resume = $form['id'];
        $langs_arr        = $langs->getByResumeList();
        if( $langs_arr ) {
            foreach( $langs_arr as $langs_row ) {
                $form['lang'.$langs_row['numb']] = $langs_row['code'];
            }
        }
        
        return $form;
    }
    
    /**
     * Resets foreign languages.
     *
     * @return array
     */
    public function resetForeignLangs( $form )
    {
        foreach( array_filter($form, function( $var ) {
            return ( substr($var, 0, 4) == 'lang' );
        }, ARRAY_FILTER_USE_KEY) as $key => $value ) {
            unset($form[$key]);
        }
        
        return $form;
    }
    
    /**
     * Gets foreign languages.
     *
     * @return array
     */
    public function getForeignLangs( $form )
    {
        foreach( array_filter($_POST, function( $var ) {
            return ( substr($var, 0, 4) == 'lang' );
        }, ARRAY_FILTER_USE_KEY) as $key => $value ) {
            $form[$key] = $value;
        }
        
        return $form;
    }
    
    /**
     * Unsets resume files.
     *
     * @return array
     */
    public function unsetScans( $form )
    {
        // agreement
        if( isset($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') < 18 ) {
            $form = $this->setFormErrorFile($form, 'agreement', 'Скан-копия "Согласие родителей/опекунов" обязательна для заполнения!');
        }
        // passport_old
        if( $form['passport_old_yes'] == 'checked' && $form['passport_type_old'] == '000000047' ) {
            $form = $this->setFormErrorFile($form, 'passport_old', 'Скан-копия "Сведения о ранее выданных паспортах" обязательна для заполнения!');
        }
        // main
        $form = Model_Scans::unsets('resume', $form);
        // passports
        switch ( $form['passport_type'] ) {
            // Паспорт РФ
            case '000000047':
                $form = $this->checkPassportRussian($form);
                break;
            // Паспорт иностранного гражданина
            case '000000049':
                $form = $this->checkPassportForeign($form);
                break;
            // Вид на жительство иностранного гражданина
            case '000000075':
                $form = $this->checkResidencyForeign($form);
                break;
            // Временное удостоверение личности гражданина РФ
            case '000000202':
                $form = $this->checkIdRussian($form);
                break;
            // Удостоверение личности иностранного гражданина
            case '000000223':
                $form = $this->checkIdForeign($form);
                break;
            // Свидетельство о рождении, выданное уполномоченным органом иностранного государства
            case '000000226':
                $form = $this->checkCertificateBirth($form);
                break;
        }
        
        return $form;
    }
    
    public function checkLangCount( $form )
    {
        $valid = false;
        
        foreach($form as $key => $value) {
            if (substr($key, 0, 4) == 'lang') {
                $valid = true;
            }
        }
        
        if(!$valid) {
            $form['error_msg'] = 'Необходимо выбрать не менее одонго иностранного языка';
            $form['validate'] = false;
        }
        
        return $form;
    }
    
    /**
     * Checks passport russian files.
     *
     * @return array
     */
    public function checkPassportRussian( $form )
    {
        // passport_face
        $form = $this->setFormErrorFile($form, 'passport_face', 'Скан-копия "Первая страница паспорта" обязательна для заполнения!');
        // passport_reg
        $form = $this->setFormErrorFile($form, 'passport_reg', 'Скан-копия "Страница паспорта с регистрацией по месту жительства" обязательна для заполнения!');
        
        return $form;
    }
    
    /**
     * Checks id russian files.
     *
     * @return array
     */
    public function checkIdRussian( $form )
    {
        // id_russian
        $form = $this->setFormErrorFile($form, 'id_russian', 'Скан-копия "Временное удостоверение личности гражданина РФ" обязательна для заполнения!');
        
        return $form;
    }
    
    /**
     * Checks passport foreign files.
     *
     * @return array
     */
    public function checkPassportForeign( $form )
    {
        $form = $this->setFormErrorFile($form, 'passport_foreign_face', 'Скан-копия "Первая страница паспорта иностранного гражданина" обязательна для заполнения!');
        
        return $form;
    }
    
    /**
     * Checks id foreign files.
     *
     * @return array
     */
    public function checkIdForeign( $form )
    {
        // id_foreign_face
        $form = $this->setFormErrorFile($form, 'id_foreign_face', 'Скан-копия "Лицевая сторона удостоверения личности иностранного гражданина" обязательна для заполнения!');
        // id_foreign_back
        $form = $this->setFormErrorFile($form, 'id_foreign_back', 'Скан-копия "Оборотная сторона удостоверения личности иностранного гражданина" обязательна для заполнения!');
        
        return $form;
    }
    
    /**
     * Checks residency foreign files.
     *
     * @return array
     */
    public function checkResidencyForeign( $form )
    {
        // residency_foreign_face
        $form = $this->setFormErrorFile($form, 'residency_foreign_face', 'Скан-копия "Первая страница вида на жительство иностранного гражданина" обязательна для заполнения!');
        // residency_foreign_reg
        $form = $this->setFormErrorFile($form, 'residency_foreign_reg',
                                        'Скан-копия "Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина" обязательна для заполнения!');
        
        return $form;
    }
    
    /**
     * Checks birth certificate files.
     *
     * @return array
     */
    public function checkCertificateBirth( $form )
    {
        // certificate_birth
        $form = $this->setFormErrorFile($form, 'certificate_birth', 'Скан-копия "Свидетельство о рождении" обязательна для заполнения!');
        
        return $form;
    }
    
    /**
     * Checks resume data.
     *
     * @return array
     */
    public function check( $form )
    {
        $resume              = new Resume();
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        /* personal */
        $personal              = new Personal();
        $personal->id_user     = $_SESSION[APP_CODE]['user_id'];
        $personal->id_resume   = $form['id'];
        $personal->name_first  = $form['name_first'];
        $personal->name_middle = $form['name_middle'];
        $personal->name_last   = $form['name_last'];
        $personal->sex         = $form['sex'];
        $personal->birth_dt    = date('Y-m-d', strtotime($form['birth_dt']));
        $personal->birth_place = $form['birth_place'];
        $citizenship           = new Model_DictCountries();
        $citizenship->code     = $form['citizenship'];
        $row_citizenship       = $citizenship->getByCode();
        $personal->citizenship = $row_citizenship['id'];
        if( $form['beneficiary'] == 'checked' ) {
            $personal->beneficiary = 1;
            $resume->app           = 0;
        } else {
            $personal->beneficiary = 0;
            $resume->app           = 1;
        }
        $row_personal = $personal->getByResume();
        if( $row_personal ) {
            $personal->id = $row_personal['id'];
            if( !$personal->changeAll() ) {
                $form['error_msg'] = 'Ошибка при изменении личных данных!';
                
                return $form;
            }
        } else {
            if( $personal->save() == 0 ) {
                $form['error_msg'] = 'Ошибка при создании личных данных!';
                
                return $form;
            }
        }
        /* agreement */
        if( !empty($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') < 18 ) {
            $form = Model_Scans::push('resume', 'agreement', $form);
        }
        /* contacts */
        $contacts            = new Model_Contacts();
        $contacts->id_user   = $_SESSION[APP_CODE]['user_id'];
        $contacts->id_resume = $form['id'];
        // email
        $contacts->type    = (int) $contacts::TYPE_EMAIL;
        $contacts->contact = $form['email'];
        $row_contacts      = $contacts->getEmailByResume();
        if( $row_contacts ) {
            $contacts->id = $row_contacts['id'];
            if( !$contacts->changeAll() ) {
                $form['error_msg'] = 'Ошибка при изменении адреса эл. почты!';
                
                return $form;
            }
        } else {
            if( $contacts->save() == 0 ) {
                $form['error_msg'] = 'Ошибка при создании адреса эл. почты!';
                
                return $form;
            }
        }
        // phone mobile
        $contacts->type = $contacts::TYPE_PHONE_MOBILE;
        if( !empty($form['phone_mobile']) ) {
            $contacts->contact = $form['phone_mobile'];
            $row_contacts      = $contacts->getPhoneMobileByResume();
            if( $row_contacts ) {
                $contacts->id = $row_contacts['id'];
                if( !$contacts->changeAll() ) {
                    $form['error_msg'] = 'Ошибка при изменении номера мобильного телефона!';
                    
                    return $form;
                }
            } else {
                if( $contacts->save() == 0 ) {
                    $form['error_msg'] = 'Ошибка при создании номера мобильного телефона!';
                    
                    return $form;
                }
            }
        } else {
            $row_contacts = $contacts->getPhoneMobileByResume();
            if( $row_contacts ) {
                $contacts->id = $row_contacts['id'];
                $contacts->clear();
            }
        }
        // phone home
        $contacts->type = $contacts::TYPE_PHONE_HOME;
        if( !empty($form['phone_home']) ) {
            $contacts->contact = $form['phone_home'];
            $row_contacts      = $contacts->getPhoneHomeByResume();
            if( $row_contacts ) {
                $contacts->id = $row_contacts['id'];
                if( !$contacts->changeAll() ) {
                    $form['error_msg'] = 'Ошибка при изменении номера домашнего телефона!';
                    
                    return $form;
                }
            } else {
                if( $contacts->save() == 0 ) {
                    $form['error_msg'] = 'Ошибка при создании номера домашнего телефона!';
                    
                    return $form;
                }
            }
        } else {
            $row_contacts = $contacts->getPhoneHomeByResume();
            if( $row_contacts ) {
                $contacts->id = $row_contacts['id'];
                $contacts->clear();
            }
        }
        // phone add
        $contacts->type = $contacts::TYPE_PHONE_ADD;
        if( !empty($form['phone_add']) ) {
            $contacts->contact = $form['phone_add'];
            $row_contacts      = $contacts->getPhoneAddByResume();
            if( $row_contacts ) {
                $contacts->id = $row_contacts['id'];
                if( !$contacts->changeAll() ) {
                    $form['error_msg'] = 'Ошибка при изменении номера дополнительного телефона!';
                    
                    return $form;
                }
            } else {
                if( $contacts->save() == 0 ) {
                    $form['error_msg'] = 'Ошибка при создании номера дополнительного телефона!';
                    
                    return $form;
                }
            }
        } else {
            $row_contacts = $contacts->getPhoneAddByResume();
            if( $row_contacts ) {
                $contacts->id = $row_contacts['id'];
                $contacts->clear();
            }
        }
        /* passports */
        // new passport
        $passport             = new Model_Passport();
        $passport->id_user    = $_SESSION[APP_CODE]['user_id'];
        $passport->id_resume  = $form['id'];
        $passport_type        = new Model_DictDoctypes();
        $passport_type->code  = $form['passport_type'];
        $passport_row_type    = $passport_type->getByCode();
        $passport->id_doctype = $passport_row_type['id'];
        $passport->main       = 1;
        $passport->series     = ( empty($form['series']) ) ? NULL : $form['series'];
        $passport->numb       = $form['numb'];
        $passport->dt_issue   = date('Y-m-d', strtotime($form['dt_issue']));
        $passport->unit_name  = $form['unit_name'];
        $passport->unit_code  = ( empty($form['unit_code']) ) ? NULL : $form['unit_code'];
        $passport->dt_end     = ( empty($form['dt_end']) ) ? NULL : date('Y-m-d', strtotime($form['dt_end']));
        $passport_row         = $passport->getByResume();
        if( $passport_row ) {
            $passport->id = $passport_row['id'];
            if( !$passport->changeAll() ) {
                $form['error_msg'] = 'Ошибка при изменении данных документа, удостоверяющего личность!';
                
                return $form;
            }
        } else {
            if( $passport->save() == 0 ) {
                $form['error_msg'] = 'Ошибка при создании данных документа, удостоверяющего личность!';
                
                return $form;
            }
        }
        // old passport
        if( $form['passport_old_yes'] == 'checked' ) {
            $passport_type        = new Model_DictDoctypes();
            $passport_type->code  = $form['passport_type_old'];
            $passport_row_type    = $passport_type->getByCode();
            $passport->id_doctype = $passport_row_type['id'];
            $passport->main       = 0;
            $passport->series     = ( empty($form['series_old']) ) ? NULL : $form['series_old'];
            $passport->numb       = $form['numb_old'];
            $passport->dt_issue   = date('Y-m-d', strtotime($form['dt_issue_old']));
            $passport->unit_name  = ( empty($form['unit_name_old']) ) ? NULL : $form['unit_name_old'];
            $passport->unit_code  = ( empty($form['unit_code_old']) ) ? NULL : $form['unit_code_old'];
            $passport->dt_end     = ( empty($form['dt_end_old']) ) ? NULL : date('Y-m-d', strtotime($form['dt_end_old']));
            $passport_row         = $passport->getByResume();
            if( $passport_row ) {
                $passport->id = $passport_row['id'];
                if( !$passport->changeAll() ) {
                    $form['error_msg'] = 'Ошибка при изменении данных старого документа, удостоверяющего личность!';
                    
                    return $form;
                }
            } else {
                if( $passport->save() == 0 ) {
                    $form['error_msg'] = 'Ошибка при создании данных старого документа, удостоверяющего личность!';
                    
                    return $form;
                }
            }
            $form = Model_Scans::push('resume', 'passport_old', $form);
        }
        /* addresses */
        $kladr = new Kladr();
        // address registration
        $address_reg              = new Model_Address();
        $address_reg->id_user     = $_SESSION[APP_CODE]['user_id'];
        $address_reg->id_resume   = $form['id'];
        $country_reg              = new Model_DictCountries();
        $country_reg->code        = $form['country_reg'];
        $row_country_reg          = $country_reg->getByCode();
        $address_reg->id_country  = $row_country_reg['id'];
        $address_reg->type        = $address_reg::TYPE_REG;
        $address_reg->kladr       = $form['kladr_reg'];
        $address_reg->region_code = ( empty($form['region_reg']) ) ? NULL : $form['region_reg'];
        if( !empty($form['region_reg']) ) {
            $kladr_row           = $kladr->getByCode($form['region_reg']);
            $address_reg->region = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_reg->region = NULL;
        }
        $address_reg->area_code = ( empty($form['area_reg']) ) ? NULL : $form['area_reg'];
        if( !empty($form['area_reg']) ) {
            $kladr_row         = $kladr->getByCode($form['area_reg']);
            $address_reg->area = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_reg->area = NULL;
        }
        $address_reg->city_code = ( empty($form['city_reg']) ) ? NULL : $form['city_reg'];
        if( !empty($form['city_reg']) ) {
            $kladr_row         = $kladr->getByCode($form['city_reg']);
            $address_reg->city = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_reg->city = NULL;
        }
        $address_reg->location_code = ( empty($form['location_reg']) ) ? NULL : $form['location_reg'];
        if( !empty($form['location_reg']) ) {
            $kladr_row             = $kladr->getByCode($form['location_reg']);
            $address_reg->location = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_reg->location = NULL;
        }
        $address_reg->street_code = ( empty($form['street_reg']) ) ? NULL : $form['street_reg'];
        if( !empty($form['street_reg']) ) {
            $kladr_row           = $kladr->getByCode($form['street_reg']);
            $address_reg->street = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_reg->street = NULL;
        }
        $address_reg->house    = ( empty($form['house_reg']) ) ? NULL : $form['house_reg'];
        $address_reg->building = ( empty($form['building_reg']) ) ? NULL : $form['building_reg'];
        $address_reg->flat     = ( empty($form['flat_reg']) ) ? NULL : $form['flat_reg'];
        $address_reg->postcode = ( empty($form['postcode_reg']) ) ? NULL : $form['postcode_reg'];
        $address_reg->adr      = $form['address_reg'];
        $row_address_reg       = $address_reg->getByResumeType();
        if( $row_address_reg ) {
            if( $row_address_reg['id_country'] != $address_reg->id_country || $row_address_reg['adr'] != $address_reg->adr ) {
                $address_reg->id = $row_address_reg['id'];
                if( !$address_reg->changeAll() ) {
                    $form['error_msg'] = 'Ошибка при изменении адреса регистрации!';
                    
                    return $form;
                }
            }
        } else {
            if( $address_reg->save() == 0 ) {
                $form['error_msg'] = 'Ошибка при создании адреса регистрации!';
                
                return $form;
            }
        }
        // address residential
        $address_res              = new Model_Address();
        $address_res->id_user     = $_SESSION[APP_CODE]['user_id'];
        $address_res->id_resume   = $form['id'];
        $country_res              = new Model_DictCountries();
        $country_res->code        = $form['country_res'];
        $row_country_res          = $country_res->getByCode();
        $address_res->id_country  = $row_country_res['id'];
        $address_res->type        = $address_res::TYPE_RES;
        $address_res->kladr       = $form['kladr_res'];
        $address_res->region_code = ( empty($form['region_res']) ) ? NULL : $form['region_res'];
        if( !empty($form['region_res']) ) {
            $kladr_row           = $kladr->getByCode($form['region_res']);
            $address_res->region = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_res->region = NULL;
        }
        $address_res->area_code = ( empty($form['area_res']) ) ? NULL : $form['area_res'];
        if( !empty($form['area_res']) ) {
            $kladr_row         = $kladr->getByCode($form['area_res']);
            $address_res->area = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_res->area = NULL;
        }
        $address_res->city_code = ( empty($form['city_res']) ) ? NULL : $form['city_res'];
        if( !empty($form['city_res']) ) {
            $kladr_row         = $kladr->getByCode($form['city_res']);
            $address_res->city = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_res->city = NULL;
        }
        $address_res->location_code = ( empty($form['location_res']) ) ? NULL : $form['location_res'];
        if( !empty($form['location_res']) ) {
            $kladr_row             = $kladr->getByCode($form['location_res']);
            $address_res->location = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_res->location = NULL;
        }
        $address_res->street_code = ( empty($form['street_res']) ) ? NULL : $form['street_res'];
        if( !empty($form['street_res']) ) {
            $kladr_row           = $kladr->getByCode($form['street_res']);
            $address_res->street = $kladr_row['kladr_name'].' '.$kladr_row['kladr_abbr'];
        } else {
            $address_res->street = NULL;
        }
        $address_res->house    = ( empty($form['house_res']) ) ? NULL : $form['house_res'];
        $address_res->building = ( empty($form['building_res']) ) ? NULL : $form['building_res'];
        $address_res->flat     = ( empty($form['flat_res']) ) ? NULL : $form['flat_res'];
        $address_res->postcode = ( empty($form['postcode_res']) ) ? NULL : $form['postcode_res'];
        $address_res->adr      = $form['address_res'];
        $row_address_res       = $address_res->getByResumeType();
        if( $row_address_res ) {
            if( $row_address_res['id_country'] != $address_res->id_country || $row_address_res['adr'] != $address_res->adr ) {
                $address_res->id = $row_address_res['id'];
                if( !$address_res->changeAll() ) {
                    $form['error_msg'] = 'Ошибка при изменении адреса проживания!';
                    
                    return $form;
                }
            }
        } else {
            if( $address_res->save() == 0 ) {
                $form['error_msg'] = 'Ошибка при создании адреса проживания!';
                
                return $form;
            }
        }
        /* foreign languages */
        $langs            = new ForeignLangs();
        $lang             = new DictForeignLangs();
        $langs->id_user   = $_SESSION[APP_CODE]['user_id'];
        $langs->id_resume = $form['id'];
        $langs_arr        = $langs->getByResume();
        $i                = 1;
        if( $langs_arr ) {
            foreach( $langs_arr as $langs_row ) {
                $langs->id = $langs_row['id'];
                if( array_key_exists('lang'.$langs_row['numb'], $form) ) {
                    // update
                    $lang->code = $form['lang'.$langs_row['numb']];
                    $lang_row   = $lang->getByCode();
                    if( $lang_row ) {
                        $langs->id_lang = $lang_row['id'];
                        if( !$langs->changeAll() ) {
                            $form['error_msg'] = 'Ошибка при изменении иностранного языка!';
                            
                            return $form;
                        }
                    } else {
                        $form['error_msg'] = 'Ошибка при поиске иностранного языка!';
                        
                        return $form;
                    }
                } else {
                    // delete
                    $langs->clear();
                }
                unset($form['lang'.$langs_row['numb']]);
                $i++;
            }
        }
        // insert
        foreach( array_filter($form, function( $var ) {
            return ( substr($var, 0, 4) == 'lang' );
        }, ARRAY_FILTER_USE_KEY) as $key => $value ) {
            $lang->code = $value;
            $lang_row   = $lang->getByCode();
            if( $lang_row ) {
                $langs->numb    = $i;
                $langs->id_lang = $lang_row['id'];
                if( $langs->save() == 0 ) {
                    $form['error_msg'] = 'Ошибка при создании иностранного языка!';
                    
                    return $form;
                }
            } else {
                $form['error_msg'] = 'Ошибка при поиске иностранного языка!';
                
                return $form;
            }
            $i++;
        }
        /* scans */
        $dict_scans           = new Model_DictScans();
        $dict_scans->doc_code = 'resume';
        // clear
        switch ( $form['passport_type'] ) {
            // Паспорт РФ
            case '000000047':
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_russian', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_back', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'certificate_birth', $form);
                break;
            // Паспорт иностранного гражданина
            case '000000049':
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_russian', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_back', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'certificate_birth', $form);
                break;
            // Вид на жительство иностранного гражданина
            case '000000075':
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_russian', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_back', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'certificate_birth', $form);
                break;
            // Временное удостоверение личности гражданина РФ
            case '000000202':
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_back', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'certificate_birth', $form);
                break;
            // Удостоверение личности иностранного гражданина
            case '000000223':
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_russian', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'certificate_birth', $form);
                break;
            // Свидетельство о рождении, выданное уполномоченным органом иностранного государства
            case '000000226':
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'passport_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'residency_foreign_reg', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_russian', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_face', $form);
                $form = Model_Scans::unpush($dict_scans->doc_code, 'id_foreign_back', $form);
                break;
        }
        // save
        $dict_scans_arr = $dict_scans->getByDocument();
        if( $dict_scans_arr ) {
            foreach( $dict_scans_arr as $dict_scans_row ) {
                $form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
                if( !empty($form['error_msg']) ) {
                    return $form;
                }
            }
        }
        /* resume status */
        $resume->id     = $form['id'];
        $resume->status = $resume::STATUS_SAVED;
        $resume->changeStatus();
        $form['status'] = $resume->status;
        /* resume app */
        if( $form['citizenship'] == '000' || !empty($form['beneficiary']) || $form['passport_type'] == '000000087' ) {
            $resume->app = 0;
        }
        $resume->changeApp();
        $form['success_msg'] = 'Анкета сохранена.';
        $form['error_msg']   = NULL;
        
        return $form;
    }
    
    /**
     * Sends resume data.
     *
     * @return array
     */
    public function send( $form )
    {
        $resume              = new Resume();
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        /* check status */
        if( $form['status'] != $resume::STATUS_SAVED ) {
            $form['error_msg'] = 'Отправлять анкеты можно только с состоянием <strong>'.mb_convert_case($resume::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        } elseif( $form['citizenship'] == '000' ) {
            $form['error_msg'] = nl2br("Лицам без гражданства не разрешена подача документов через веб!\nПожалуйста, обратитесть в приёмную комиссию лично.");
            
            return $form;
        } elseif( !empty($form['beneficiary']) ) {
            $form['error_msg'] = nl2br("Лицам, имеющим льготы, не разрешена подача документов через веб!\nПожалуйста, обратитесть в приёмную комиссию лично.");
            
            return $form;
        } elseif( $form['passport_type'] == '000000087' ) {
            $form['error_msg'] = nl2br("По \"Свидетельству о предоставлении временного убежища на территории РФ\" не разрешена подача документов через веб!\nПожалуйста, обратитесть в приёмную комиссию лично.");
            
            return $form;
        }
        /* send */
        $resume->id     = $form['id'];
        $resume->status = $resume::STATUS_SENDED;
        $resume->changeStatus();
        $form['status']      = $resume->status;
        $form['success_msg'] = 'Анкета отправлена.';
        $form['error_msg']   = NULL;
        
        return $form;
    }
    
    /**
     * Recalls resume data.
     *
     * @return array
     */
    public function recall( $form )
    {
        $app                 = new Application();
        $resume              = new Resume();
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        /* check status */
        $personal            = new Personal();
        $personal->id_resume = $form['id'];
        $personal_row        = $personal->getByResume();
        if( !in_array($form['status'], [$resume::STATUS_SENDED, $resume::STATUS_APPROVED]) ) {
            $form['error_msg'] = 'Отзывать анкеты можно только с состоянием <strong>'.mb_convert_case($resume::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>! Актуальное состояние: '. $form['status'];
            
            return $form;
        } elseif( !empty($personal_row['guid']) ) {
            $form['error_msg'] = 'Отзывать анкету нельзя, так как приёмная комиссия уже создала для Вас запись физ. лица в учётной системе!';
            
            return $form;
        } elseif( $app->existsAppGo() == TRUE ) {
            $form['error_msg'] = 'Отзывать анкеты нельзя, если есть заявления с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8')
                                 .'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        /* recall */
        $resume->id     = $form['id'];
        $resume->status = $resume::STATUS_SAVED;
        $resume->changeStatus();
        $form['status']      = $resume->status;
        $form['success_msg'] = 'Анкета отозвана.';
        $form['error_msg']   = NULL;
        
        return $form;
    }
}
