<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper;
use common\models\Model_Application as Application;

class Model_TargetQuota
    extends Db_Helper
{
    const TABLE_NAME = 'target_quota';
    
    public $id;
    public $id_user;
    public $doc_number;
    public $doc_issuer;
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
            'id_user' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_user
            ],
            'doc_number' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->doc_number
            ],
            'doc_issuer' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->doc_issuer
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
            'issuer' => [
                'name' => 'Место выдачи',
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
        $privillege_quota_record = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
        if( $privillege_quota_record ) {
            $result   = $privillege_quota_record;
            $scan     = new Model_Scans();
            $scan_arr = $scan->getByDocrowFull('target_quota', $privillege_quota_record['id']);
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
                                   'doc_number = :doc_number AND doc_number = :doc_number',
                                   [
                                       ':doc_issuer' => $this->doc_issuer,
                                       ':doc_number' => $this->doc_number
                                   ]);
    }
    
    public function getByNumbExcept()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'doc_issuer = :doc_issuer AND doc_number = :doc_number AND id <> :id',
                                   [
                                       ':doc_issuer' => $this->doc_issuer,
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
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_user = :id_user',
                                   [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
    }
    
    public function clear()
    {
        $my_id = $this->rowSelectOne('id', self::TABLE_NAME, 'id_user = :id_user', [':id_user' => $_SESSION[APP_CODE]['user_id']]);
        
        if(is_array($my_id)) {
            $my_id = $my_id['id'];
        }
        
        if($my_id == $this->id) {
            return $this->rowDelete(self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
        }
        
        return 10;
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
