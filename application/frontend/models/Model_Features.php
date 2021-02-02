<?php

namespace frontend\models;

require_once ROOT_DIR.'/application/frontend/models/Model_Scans.php';

use common\models\Model_DictDiscipline;
use common\models\Model_DictFeatures;
use common\models\Model_DictScans;
use tinyframe\core\Model;

use common\models\Model_Application as Application;
use common\models\Model_Features as Features;

class Model_Features
    extends Model
{
    public function rules()
    {
        $rules = [
            'feature_type' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Вид признака обязателен для заполнения!'],
                'success' => 'Вид признака заполнен верно.'
            ],
            'discipline' => [
                'type' => 'selectlist',
                'class' => 'form-control',
                'required' => ['default' => '', 'msg' => 'Предмет обязателен для заполнения!'],
                'success' => 'Предмет заполнен верно.'
            ],
            'doc_number' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для номера можно использовать '.MSG_INFO_RUS.'!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 20, 'msg' => 'Слишком длинный номер!'],
                'success' => 'Номер заполнен верно.'
            ],
            'doc_date' => [
                'type' => 'date',
                'format' => 'd.m.Y',
                'class' => 'form-control',
                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть '.MSG_DATE_STRONG.'!'],
                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!'],
                'success' => 'Дата выдачи заполнена верно.'
            ]
        ];
        $scans = Model_Scans::createRules('features');
        
        return array_merge($rules, $scans);
    }
    
    /**
     * Gets individual achievment from database.
     *
     * @param $id
     *
     * @return array
     */
    public function get($id): array
    {
        $feature_record = new Features();
        $feature_record->id = $id;
        return $feature_record->get();
    }
    
    /**
     * Deletes individual achievment from database.
     *
     * @param $form
     *
     * @return array
     */
    public function delete($form) : array
    {
        $form['success_msg'] = null;
        $form['error_msg'] = null;
        $feature_record = new Features();
        $feature_record->id = $form['id'];
        if ($feature_record->existsAppGo()) {
            $app = new Application();
            $form['error_msg'] = 'Удалять индивидуальные достижения, которые используются в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong> нельзя!';
        } elseif ( $feature_record->clear() > 0) {
            $form['success_msg'] = 'Индивидуальное достижение № '.$feature_record->id.' удалено.';
        } else {
            $form['error_msg'] = 'Ошибка удаления индивидуального достижения № '.$feature_record->id.'! Свяжитесь с администратором.';
        }
        return $form;
    }
    
    /**
     * Unsets individual achievment files.
     *
     * @param $form
     *
     * @return array
     */
    public function unsetScans($form): array
    {
        return Model_Scans::unsets('features', $form);
    }
    
    /**
     * Checks education document data.
     *
     * @param $form
     *
     * @return array
     */
    public function check($form): array
    {
        $form['success_msg'] = null;
        $form['error_msg'] = null;
        
        $feature_type = new Model_DictFeatures();
        $feature_type->code = $form['feature_type'];
        $row_feature = $feature_type->getByCode();
        
        $discipline = new Model_DictDiscipline();
        $discipline->code = $form['discipline'];
        $row_discipline = $discipline->getByCode();
        
        $feature_record = new Features();
        $feature_record->id_user = $_SESSION[APP_CODE]['user_id'];
        $feature_record->id_feature = $row_feature['id'];
        $feature_record->id_dict_discipline = $row_discipline['id'];
        $feature_record->doc_number = (empty($form['doc_number'])) ? null : $form['doc_number'];
        $feature_record->doc_date = (empty($form['doc_date']) ? null : date('Y-m-d', strtotime($form['doc_date'])));
        
        if (isset($form['id']) && !empty($form['id'])) {
            // update
            $feature_record->id = $form['id'];
            $feature_row = $feature_record->getByNumbExcept();
            if( $feature_row ) {
                $form['error_msg'] = 'Документ, подтверждающий отличительный признак с таким номером от этой организации уже есть в системе';//'Индивидуальное достижение "'.$row_feature['description'].'" с серией/номером '.((!empty($docs->series)) ? $docs->series : '').'/'.$docs->numb.' уже есть!';
                return $form;
            }
    
            if( $feature_record->existsAppGo() ) {
                $app = new Application();
                $form['error_msg'] = 'Изменять отличительные признаки, которые используются в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong> нельзя!';
                return $form;
            }
    
            if ( $feature_record->changeAll() ) {
                $form['success_msg'] = 'Изменено индивидуальное достижение № '.$form['id'].'.';
            } else {
                $form['error_msg'] = 'Ошибка при изменении индивидульного достижения!';
                return $form;
            }
        } else {
            // insert
            $feature_row = $feature_record->getByNumb();
            if ($feature_row) {
                $form['error_msg'] = 'Такой отличительный признак уже есть';//'Индивидуальное достижение "'.$row_feature['description'].'" с серией/номером '.((!empty($docs->series)) ? $docs->series : '').'/'.$docs->numb.' уже есть!';
                return $form;
            }
    
            $form['id'] = $feature_record->save();
            if ( $form['id'] > 0) {
                $form['success_msg'] = 'Создан отличительный признак достижение № '.$form['id'].'.';
            } else {
                unset($form['id']);
                $form['error_msg'] = 'Ошибка при создании отличительного признака!';
                return $form;
            }
        }
        /* scans */
        $dict_scans = new Model_DictScans();
        $dict_scans->doc_code = 'features';
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
}
