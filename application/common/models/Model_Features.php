<?php

namespace common\models;

use common\models\Model_Application as Application;
use tinyframe\core\helpers\Db_Helper;

class Model_Features
    extends Db_Helper
{
    const TABLE_NAME = 'features';
    
    public $id;
    public $id_feature;
    public $id_user;
    public $id_dict_discipline;
    public $doc_number;
    public $doc_date;
    
    public $db;
    
    public function __construct()
    {
        $this->db = Db_Helper::getInstance();
    }
    
    public function rules()
    {
        return [
            'id' => [
                'required' => 1,
                'insert' => 0,
                'update' => 0,
                'value' => $this->id
            ],
            'id_feature' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_feature
            ],
            'id_user' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_user
            ],
            'id_dict_discipline' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_dict_discipline
            ],
            'doc_number' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->doc_number
            ],
            'doc_date' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->doc_date
            ]
        ];
    }
    
    public function grid()
    {
        return [
            'id' => [
                'name' => '№',
                'type' => 'int'
            ],
            'name' => [
                'name' => 'Тип',
                'type' => 'string'
            ],
            'discipline' => [
                'name' => 'Предмет',
                'type' => 'string'
            ],
            'doc_number' => [
                'name' => 'Номер документа',
                'type' => 'string'
            ]
        ];
    }
    
    public function get()
    {
        $feature_record = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
        if( $feature_record ) {
            $feature_code = $this->rowSelectOne('code as feature_type',
                                                'dict_features',
                                                'id = :id',
                                                [ ':id' => $feature_record['id_feature'] ]);
            if( !is_array($feature_code) ) {
                $feature_code = [ 'feature_type' => NULL ];
            }
            $result   = array_merge($feature_record, $feature_code);
    
            $discipline_code = $this->rowSelectOne('code as discipline',
                                                'dict_discipline',
                                                'id = :id',
                                                [ ':id' => $feature_record['id_dict_discipline'] ]);
            if( !is_array($discipline_code) ) {
                $discipline_code = [ 'discipline' => NULL ];
            }
            $result   = array_merge($result, $discipline_code);
            
            $scan     = new Model_Scans();
            $scan_arr = $scan->getByDocrowFull('features', $feature_record['id']);
            $result   = array_merge($result, $scan_arr);
            
            return $result;
        }
        
        return NULL;
    }
    
    /**
     * @return array|bool
     */
    public function getByNumb()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_feature = :id_feature AND id_dict_discipline = :id_dict_discipline AND doc_number = :doc_number',
                                   [
                                       ':id_feature' => $this->id_feature,
                                       ':id_dict_discipline' => $this->id_dict_discipline,
                                       ':doc_number' => $this->doc_number
                                   ]);
    }
    
    public function getByNumbExcept()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_feature = :id_feature AND id_dict_discipline = :id_dict_discipline AND doc_number = :doc_number AND id <> :id',
                                   [
                                       ':id_feature' => $this->id_feature,
                                       ':id_dict_discipline' => $this->id_dict_discipline,
                                       ':doc_number' => $this->doc_number,
                                       ':id' => $this->id
                                   ]);
    }
    
    public function existsAppGo(): bool
    {
        $app_arr = $this->rowSelectAll('application.id',
                                       'application INNER JOIN application_achievs ON application_achievs.pid = application.id'.
                                       ' INNER JOIN ind_achievs ON application_achievs.id_achiev = ind_achievs.id',
                                       'ind_achievs.id = :id AND ((application.status in (1,2) and application.type in (1,2)) OR application.status = 1 and application.type = 3) AND application.active = 1',
                                       [
                                           ':id' => $this->id
                                       ]);
        if( $app_arr ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function getByUser()
    {
        return $this->rowSelectAll('*',
                                   self::TABLE_NAME,
                                   'id_user = :id_user',
                                   [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
    }
    
    public function getFirstByUser()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_user = :id_user',
                                   [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
    }
    
    public function getByUserGrid()
    {
        return $this->rowSelectAll('a.id, b.name name, a.doc_number doc_number, c.discipline_name discipline',
                                   self::TABLE_NAME.' a '.
                                   'INNER JOIN dict_features b ON a.id_feature = b.id '.
                                   'INNER JOIN dict_discipline c ON c.id = a.id_dict_discipline',
                                   'id_user = :id_user',
                                   [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
    }
    
    public function clear()
    {
        return $this->rowDelete(self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
    }
    
    public function changeAll()
    {
        $prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
        
        return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], [ 'id' => $this->id ]);
    }
    
    public function changeField( $field ): bool
    {
        return $this->rowUpdate(self::TABLE_NAME,
                                "$field = :$field",
                                [ ":$field" => $this->$field ],
                                [ 'id' => $this->id ]);
    }
    
    public function save()
    {
        $prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        
        return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
    }
}
