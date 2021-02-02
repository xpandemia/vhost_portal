<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictFeatures
    extends Db_Helper
{
    const TABLE_NAME = 'dict_features';
    
    public $id;
    public $code;
    public $guid;
    public $name;
    public $parent_guid;
    public $is_group;
    
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
            'guid' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->guid
            ],
            'code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->code
            ],
            'name' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->name
            ],
            'parent_guid' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->parent_guid
            ],
            'is_group' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->is_group
            ]
        ];
    }
    
    public function getAll()
    {
        $records = $this->rowSelectAll('a.id id, a.code code, c.name group_name, a.name name',
                                       self::TABLE_NAME.' a '.
                                       'INNER JOIN dict_features_types_links b ON a.code = b.feature_code '.
                                       'INNER JOIN dict_feature_types c ON c.code = b.feature_type_code', NULL, NULL,
                                       'name');
        
        $result = [];
        foreach ($records as $record) {
            if(!isset($result[$record['group_name']])) {
                $result[$record['group_name']] = [];
            }
    
            $result[$record['group_name']][] = $record;
        }
        return $result;
    }
    
    public function getByCode()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'code = :code',
                                   [ ':code' => $this->code ]);
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
    
    public function load( $properties, $id_dict, $dict_name, $clear_load )
    {
        $result['success_msg'] = NULL;
        $result['error_msg']   = NULL;
        $log                   = new Model_DictionaryManagerLog();
        $log->id_dict          = $id_dict;
        if( $clear_load == 1 ) {
            // clear
            $rows_del       = $this->$clear_load();
            $log->msg       = 'Удалено особых отметок - '.$rows_del.'.';
            $log->value_old = NULL;
            $log->value_new = NULL;
            $log->save();
        } else {
            $rows_del = 0;
        }
        if( sizeof($properties) == 0 ) {
            $result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
            
            return $result;
        }
        $rows_ins = 0;
        $rows_upd = 0;
        foreach( $properties as $property ) {
            echo 'Код записи: '.$this->code.'<br/>';
            $this->code = (string) $property->Code;
            $feature    = $this->getByCode();
            
            if( $property->DeletionMark == 'false' ) {
                echo 'Валидная записть 1С<br/>';
                
                $this->guid        = (string) $property->Ref_Key;
                $this->name        = (string) $property->Description;
                $this->parent_guid = (string) $property->Parent_Key;
                $this->is_group    = ( $property->IsFolder == 'false' ? 0 : 1 );
                if( $feature == NULL ) {
                    echo 'Такойй записи нет в БД<br/>';
                    // insert
                    if( $this->save() ) {
                        $log->msg       = 'Создана новая особенность с GUID ['.$this->guid.'].';
                        $log->value_old = NULL;
                        $log->value_new = NULL;
                        $log->save();
                        $rows_ins++;
                    } else {
                        $result['error_msg'] = 'Ошибка при сохранении особенности с GUID ['.$this->guid.']!';
                        
                        return $result;
                    }
                } else {
                    // update
                    $upd      = 0;
                    $this->id = $feature['id'];
                    // code
                    if( $feature['code'] != $this->code ) {
                        if( $this->changeField('code') ) {
                            $log->msg       = 'Изменён код особенности с GUID ['.$this->guid.'].';
                            $log->value_old = $feature['code'];
                            $log->value_new = $this->code;
                            $log->save();
                            $upd = 1;
                        } else {
                            $result['error_msg'] = 'Ошибка при изменении кода особенности с GUID ['.$this->guid.']!';
                            
                            return $result;
                        }
                    }
                    // description
                    if( $feature['name'] != $this->name ) {
                        if( $this->changeField('name') ) {
                            $log->msg       = 'Изменено наименование особенности с GUID ['.$this->guid.'].';
                            $log->value_old = $feature['name'];
                            $log->value_new = $this->name;
                            $log->save();
                            $upd = 1;
                        } else {
                            $result['error_msg'] = 'Ошибка при изменении наименования особенности с GUID ['.$this->guid.']!';
                            
                            return $result;
                        }
                    }
                    // full name
                    if( $feature['parent_guid'] != $this->parent_guid ) {
                        if( $this->changeField('parent_guid') ) {
                            $log->msg       = 'Изменен родительский гуид особенности с GUID ['.$this->guid.'].';
                            $log->value_old = $feature['parent_guid'];
                            $log->value_new = $this->parent_guid;
                            $log->save();
                            $upd = 1;
                        } else {
                            $result['error_msg'] = 'Ошибка при изменении родительский гуид особенности с GUID ['.$this->guid.']!';
                            
                            return $result;
                        }
                    }
                    if( $feature['is_group'] != $this->is_group ) {
                        if( $this->changeField('is_group') ) {
                            $log->msg       = 'Изменено груп_флаг особенности с GUID ['.$this->guid.'].';
                            $log->value_old = $feature['is_group'];
                            $log->value_new = $this->is_group;
                            $log->save();
                            $upd = 1;
                        } else {
                            $result['error_msg'] = 'Ошибка при изменении груп_флаг особенности с GUID ['.$this->guid.']!';
                            
                            return $result;
                        }
                    }
                    // counter
                    if( $upd == 1 ) {
                        $rows_upd++;
                    }
                }
            }
        }
        if( $rows_del == 0 && $rows_ins == 0 && $rows_upd == 0 ) {
            $result['success_msg'] = 'Справочник "'.$dict_name.'" не нуждается в обновлении.';
        } else {
            $result['success_msg'] = nl2br("В справочнике \"$dict_name\":\n----- удалено записей - $rows_del\n----- добавлено записей - $rows_ins\n----- обновлено записей - $rows_upd");
        }
        
        return $result;
    }
}
