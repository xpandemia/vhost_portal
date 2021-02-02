<?php

namespace frontend\models;

use common\models\Model_DictScans;
use tinyframe\core\Model;

use common\models\Model_Application as Application;
use common\models\Model_TargetQuota as TargetQuota;

class Model_TargetQuota
    extends Model
{
    public function rules()
    {
        $rules = [
            'doc_number' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для номера можно использовать '.MSG_INFO_RUS.'!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 20, 'msg' => 'Слишком длинный номер!'],
                'success' => 'Номер заполнен верно.'
            ],
            'doc_issuer' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для номера можно использовать '.MSG_INFO_RUS.'!'],
                'width' => ['format' => 'string', 'min' => 1, 'max' => 1024, 'msg' => 'Слишком длинное название выпускающей организации!'],
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
        $scans = Model_Scans::createRules('target_quota');
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
        $priv_quota = new TargetQuota();
        $priv_quota->id = $id;
        return $priv_quota->get();
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
        $priv_quota_record = new TargetQuota();
        $priv_quota_record->id = $form['id'];

        if ($priv_quota_record->existsAppGo()) {
            $app = new Application();
            $form['error_msg'] = 'Удалять индивидуальные достижения, которые используются в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong> нельзя!';
        } elseif ( $priv_quota_record->clear() > 0) {
            $this->unsetScans($form);
            $form['success_msg'] = 'Индивидуальное достижение № '.$priv_quota_record->id.' удалено.';
        } else {
            $form['error_msg'] = 'Ошибка удаления индивидуального достижения № '.$priv_quota_record->id.'! Свяжитесь с администратором.';
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
        return Model_Scans::unsets('target_quota', $form);
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
        
        $targetQuota = new TargetQuota();
        $targetQuota->id_user = $_SESSION[APP_CODE]['user_id'];
        $targetQuota->doc_issuer = (empty($form['doc_issuer'])) ? null : $form['doc_issuer'];
        $targetQuota->doc_number = (empty($form['doc_number'])) ? null : $form['doc_number'];
        $targetQuota->doc_date = (empty($form['doc_date']) ? null : date('Y-m-d', strtotime($form['doc_date'])));
    
        if (isset($form['id']) && !empty($form['id'])) {
            // update
            $targetQuota->id = $form['id'];
            $feature_row = $targetQuota->getByNumbExcept();
            if( $feature_row ) {
                $form['error_msg'] = 'Отличительный признак с таким нормером от этой организации уже есть в системе';//'Индивидуальное достижение "'.$row_feature['description'].'" с серией/номером '.((!empty($docs->series)) ? $docs->series : '').'/'.$docs->numb.' уже есть!';
                return $form;
            }
    
            if( $targetQuota->existsAppGo() ) {
                $app = new Application();
                $form['error_msg'] = 'Изменять отличительные признаки, которые используются в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong> нельзя!';
                return $form;
            }
    
            if ( $targetQuota->changeAll() ) {
                $form['success_msg'] = 'Изменено индивидуальное достижение № '.$form['id'].'.';
            } else {
                $form['error_msg'] = 'Ошибка при изменении индивидульного достижения!';
                return $form;
            }
        } else {
            // insert
            $feature_row = $targetQuota->getByNumb();
            if ($feature_row) {
                $form['error_msg'] = 'Документ о целевой квоте с таким номером уже есть';//'Индивидуальное достижение "'.$row_feature['description'].'" с серией/номером '.((!empty($docs->series)) ? $docs->series : '').'/'.$docs->numb.' уже есть!';
                return $form;
            }
    
            $form['id'] = $targetQuota->save();
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
        $dict_scans->doc_code = 'target_quota';
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
