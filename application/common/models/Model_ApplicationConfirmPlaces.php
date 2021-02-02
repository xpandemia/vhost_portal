<?php /** @noinspection PhpUnused */

namespace common\models;

use tinyframe\core\helpers\Db_Helper;

class Model_ApplicationConfirmPlaces
    extends Db_Helper
{
    const TABLE_NAME = 'application_confirm_places';
    
    public $id;
    public $id_application_confirm;
    public $id_application_place;
    public $selected;
    
    public $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = Db_Helper::getInstance();
    }
    
    /**
     * Application places rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required' => 1,
                'insert' => 0,
                'update' => 0,
                'value' => $this->id
            ],
            'id_application_confirm' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_application_confirm
            ],
            'id_application_place' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_application_place
            ],
            'selected' => [
                'required' => 1,
                'insert' => 0,
                'update' => 1,
                'value' => $this->selected
            ]
        ];
    }
    
    /**
     * Application places grid.
     *
     * @return array
     */
    public function grid(): array
    {
        return [
            'spec' => [
                'name' => 'Направление подготовки',
                'type' => 'string'
            ],
            'finance_name' => [
                'name' => 'Основание поступления',
                'type' => 'string'
            ],
            'eduform_name' => [
                'name' => 'Основание поступления',
                'type' => 'string'
            ],
            'group_name' => [
                'name' => 'Категория приема',
                'type' => 'string'
            ],
            'actions' => [
                'name' => 'Действия',
                'type' => 'string'
            ]
        ];
    }
    
    /**
     * Gets application places for GRID.
     *
     * @param bool $debug
     *
     * @return array|NULL|FALSE
     */
    public function getGrid( $debug = FALSE )
    {
        if( $debug ) {
            echo 'GetGridCall<br/>';
        }
        
        $req = $this->rowSelectAll("application_confirm_places.id id, application_confirm_places.selected selected, concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, '')) AS spec, dict_speciality.finance_name, dict_speciality.eduform_name, dict_spec_groups.name as group_name",
                                   'application_confirm_places INNER JOIN application_places ON application_confirm_places.id_application_place = application_places.id INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id INNER JOIN dict_spec_groups ON dict_spec_groups.code = application_places.group_code',
                                   'id_application_confirm = :id_application_confirm',
                                   [ ':id_application_confirm' => $this->id_application_confirm ], NULL, NULL, $debug);
        
        $ret = [];
        
        $selected = FALSE;
        foreach( $req as $rec ) {
            if( isset($rec['selected']) && $rec['selected'] == 1 ) {
                //debug_print_object($rec);
                $selected = TRUE;
            }
        }
        
        $flagged = FALSE;
        foreach( $req as $rec ) {
            $checked = '';
            
            if( $selected ) {
                if( $rec['selected'] == 1 && !$flagged ) {
                    $checked = 'checked';
                    $flagged = TRUE;
                }
                $action_string = '<p><input disabled type="radio" name="spec" value="'.$rec['id'].'" '.$checked.' ></p>';
            } else {
                if( !$flagged ) {
                    $checked = 'checked';
                    $flagged = TRUE;
                }
                $action_string = '<p><input type="radio" name="spec" value="'.$rec['id'].'" '.$checked.' ></p>';
            }
            
            $ret[] = [
                'spec' => $rec['spec'],
                'finance_name' => $rec['finance_name'],
                'eduform_name' => $rec['eduform_name'],
                'group_name' => $rec['group_name'],
                'actions' => $action_string
            ];
        }
        
        return $ret;
    }
    
    /**
     * Gets application places for GRID.
     *
     * @param bool $debug
     *
     * @return array|NULL|FALSE
     */
    public function getGridRecall( $debug = FALSE )
    {
        if( $debug ) {
            echo 'getGridRecallCall<br/>';
        }
        
        $req = $this->rowSelectAll("application_confirm_places.id id, application_confirm_places.selected selected, concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, '')) AS spec, dict_speciality.finance_name, dict_speciality.eduform_name, dict_spec_groups.name as group_name",
                                   'application_confirm_places
                                   INNER JOIN  application_confirm ON application_confirm_places.id_application_confirm = application_confirm.parent_id
                                   INNER JOIN application_places ON application_confirm_places.id_application_place = application_places.id
                                   INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id
                                   INNER JOIN dict_spec_groups ON dict_spec_groups.code = application_places.group_code',
                                   'application_confirm.id = :id_application_confirm AND application_confirm_places.selected = 1',
                                   [ ':id_application_confirm' => $this->id_application_confirm ], NULL, NULL, $debug);
        
        $ret = [];
        
        $selected = FALSE;
        foreach( $req as $rec ) {
            if( isset($rec['selected']) && $rec['selected'] == 1 ) {
                $selected = TRUE;
            }
        }
        
        $flagged = FALSE;
        foreach( $req as $rec ) {
            $checked = '';
            
            if( $selected ) {
                if( $rec['selected'] == 1 && !$flagged ) {
                    $checked = 'checked';
                    $flagged = TRUE;
                }
                $action_string = '<p><input disabled type="radio" name="spec" value="'.$rec['id'].'" '.$checked.' ></p>';
            } else {
                if( !$flagged ) {
                    $checked = 'checked';
                    $flagged = TRUE;
                }
                $action_string = '<p><input type="radio" name="spec" value="'.$rec['id'].'" '.$checked.' ></p>';
            }
            
            $ret[] = [
                'spec' => $rec['spec'],
                'finance_name' => $rec['finance_name'],
                'eduform_name' => $rec['eduform_name'],
                'group_name' => $rec['group_name'],
                'actions' => $action_string
            ];
        }
        
        return $ret;
    }
    
    /**
     * Gets specialities by application.
     *
     * @return array|NULL|FALSE
     */
    public function getSpecsByApp()
    {
        return $this->rowSelectAll('*',
                                   self::TABLE_NAME,
                                   'id_application_confirm = :id_application_confirm',
                                   [ ':id_application_confirm' => $this->id_application_confirm ]);
    }
    
    /**
     * Gets specialities by application for PDF.
     *
     * @return array|NULL|FALSE
     */
    public function getSpecsByAppPdf()
    {
        return $this->rowSelectAll("concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, '')) as place, dict_speciality.edulevel_name as edulevel, dict_speciality.eduform_name as eduform, dict_finances.abbr as finance",
                                   self::TABLE_NAME.' INNER JOIN dict_speciality ON '.self::TABLE_NAME.'.id_spec = dict_speciality.id'.
                                   ' INNER JOIN dict_finances ON dict_speciality.finance_code = dict_finances.code',
                                   'id_application_confirm = :id_application_confirm',
                                   [ ':id_application_confirm' => $this->id_application_confirm ]);
    }
    
    public function save($debug = FALSE): int
    {
        $prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        
        return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params'], $debug);
    }
    
    
    /**
     * Changes all application places data.
     *
     * @return boolean
     */
    public function changeAll(): bool
    {
        $prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
        
        return $this->rowUpdate(self::TABLE_NAME,
                                $prepare['fields'],
                                $prepare['params'],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Removes application places by application.
     *
     * @return integer
     */
    public function clearByApplication(): int
    {
        return $this->rowDelete(self::TABLE_NAME,
                                'id_application_confirm = :id_application_confirm',
                                [ ':id_application_confirm' => $this->id_application_confirm ]);
    }
    
    /**
     * Returns tables for specs.
     *
     * @return string
     */
    public function TablesSpecs(): string
    {
        return 'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
               ' INNER JOIN application ON admission_campaign.id = application.id_campaign'.
               ' INNER JOIN dict_finances ON dict_speciality.finance_code = dict_finances.code';
    }
    
    public function __destruct()
    {
        $this->db = NULL;
    }
    
    public function getByApp( $app_id )
    {
        return $this->rowSelectAll('application_confirm_places.id, id_application_confirm, id_application_place, selected',
                                   'application_confirm_places
                                           INNER JOIN application_confirm
                                               ON application_confirm.id = application_confirm_places.id_application_confirm
                                           INNER JOIN application
                                               ON application.id = application_confirm.id_application',
                                   'id_user = :id_user AND application_confirm.id = :app_id',
                                   [
                                       ':id_user' => $_SESSION[APP_CODE]['user_id'],
                                       ':app_id' => $app_id
                                   ]
        );
    }
}
