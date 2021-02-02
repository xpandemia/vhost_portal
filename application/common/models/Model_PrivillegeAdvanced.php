<?php

namespace common\models;

use common\models\Model_Application as Application;
use tinyframe\core\helpers\Db_Helper;

class Model_PrivillegeAdvanced
    extends Db_Helper
{
    const TABLE_NAME = 'privilleges_advanced';
    
    public $id;
    public $id_privillege;
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
            'id_privillege' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_privillege
            ],
            'id_user' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_user
            ],
            'doc_number' => [
                'required' => 1,
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
            'name' => [
                'name' => 'Тип',
                'type' => 'string'
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
            $privillege_code = $this->rowSelectOne('code as privillege_type',
                                                   'dict_privilleges',
                                                   'id = :id',
                                                   [ ':id' => $privillege_quota_record['id_privillege'] ]);
            if( !is_array($privillege_code) ) {
                $privillege_code = [ 'privillege_type' => NULL ];
            }
        
            $result   = array_merge($privillege_quota_record, $privillege_code);
            $scan     = new Model_Scans();
            $scan_arr = $scan->getByDocrowFull('priv_adv', $privillege_quota_record['id']);
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
                                       ':id_privillege' => $this->id_privillege,
                                       ':doc_issuer' => $this->doc_issuer,
                                       ':doc_number' => $this->doc_number
                                   ]);
    }
    
    public function getByNumbExcept()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_privillege = :id_privillege AND doc_issuer = :doc_issuer AND doc_number = :doc_number AND id <> :id',
                                   [
                                       ':id_privillege' => $this->id_privillege,
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
        return $this->rowSelectOne('privilleges_advanced.id,
    id_privillege,
    id_user,
    doc_number,
    doc_issuer,
    doc_date,
    doc_type',
                                   'privilleges_advanced
        INNER JOIN dict_privilleges
                       ON dict_privilleges.id = privilleges_advanced.id_privillege',
                                   'id_user = :id_user',
                                   [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
    }
    
    public function getByUserGrid()
    {
        return $this->rowSelectAll('a.id, b.name name, a.doc_number doc_number, a.doc_issuer',
                                   self::TABLE_NAME.' a '.
                                   'INNER JOIN dict_privilleges b ON a.id_privillege = b.id ',
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
