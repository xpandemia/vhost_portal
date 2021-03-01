<?php

namespace common\models;

include ROOT_DIR.'/application/common/models/Model_DictSpecGroups.php';

use common\models\Model_DictCountries as DictCountries;
use common\models\Model_Personal as Personal;
use tinyframe\core\helpers\Db_Helper;

class Model_ApplicationPlaces
    extends Db_Helper
{
    /*
        Application places processing
    */
    
    const TABLE_NAME           = 'application_places';
    
    const FREE                 = 'Бесплатная';
    
    const PAY                  = 'Платная';
    
    const PURPOSE              = 'Целевой';
    
    const BACHELOR_LIMIT_START = '01.01.2021';
    
    const BACHELOR_LIMIT_END   = '31.12.2021';
    
    public $id;
    public $pid;
    public $id_user;
    public $id_spec;
    public $curriculum;
    public $dt_created;
    public $group_code;
    
    public $db;
    
    public function __construct()
    {
        $this->db = Db_Helper::getInstance();
    }
    
    /**
     * Application places rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => [
                'required' => 1,
                'insert' => 0,
                'update' => 0,
                'value' => $this->id
            ],
            'pid' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->pid
            ],
            'id_user' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_user
            ],
            'id_spec' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_spec
            ],
            'curriculum' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->curriculum
            ],
            'dt_created' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->dt_created
            ],
            'group_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->group_code
            ]
        ];
    }
    
    /**
     * Application places grid.
     *
     * @return array
     */
    public function grid()
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
            ]
        ];
    }
    
    /**
     * Gets application places for GRID.
     *
     * @param bool $debug
     *
     * @return array
     */
    public function getGrid( $debug = FALSE )
    {
        if( $debug ) {
            echo 'GetGridCall<br/>';
        }
        
        return $this->rowSelectAll("application_places.id, concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, '')) AS spec, dict_speciality.finance_name, dict_speciality.eduform_name, dict_spec_groups.name as group_name",
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id INNER JOIN dict_spec_groups ON dict_spec_groups.code = application_places.group_code',
                                   'pid = :pid',
                                   [ ':pid' => $this->pid ], NULL, NULL, $debug);
    }
    
    /**
     * Gets specialities by application.
     *
     * @return array
     */
    public function getSpecsByApp()
    {
        return $this->rowSelectAll('*',
                                   self::TABLE_NAME,
                                   'pid = :pid',
                                   [ ':pid' => $this->pid ]);
    }
    
    /**
     * Gets specialities by application for PDF.
     *
     * @return array
     */
    public function getSpecsByAppPdf()
    {
        return $this->rowSelectAll("concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, '')) as place, dict_speciality.edulevel_name as edulevel, dict_speciality.eduform_name as eduform, dict_finances.abbr as finance",
                                   self::TABLE_NAME.' INNER JOIN dict_speciality ON '.self::TABLE_NAME.'.id_spec = dict_speciality.id'.
                                   ' INNER JOIN dict_finances ON dict_speciality.finance_code = dict_finances.code',
                                   'pid = :pid',
                                   [ ':pid' => $this->pid ]);
    }
    
    public function hasTarget()
    {
        $hasTarget_record = $this->rowSelectOne('id', 'target_quota', 'id_user = :id_user', [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
        if( is_array($hasTarget_record) && count($hasTarget_record) > 0 ) {
            $hasTarget = TRUE;
        } else {
            $hasTarget = FALSE;
        }
        
        return $hasTarget;
    }
    
    public function filterSpec( $req, $pay, $debug = FALSE )
    {
        $hasTarget = $this->hasTarget();
        $hasQuota  = $this->hasPrivQuota();
        $ret       = [];
        
        $groups = $this->getGroupFirstBachelorForApp($pay);
    
        if($debug) {
            echo '<pre>';
            var_dump(['filterSpec' => ['hasTarget' => $hasTarget, 'hasQuota' => $hasQuota, 'groups' => $groups]]);
            echo '</pre>';
        }
        
        $_req = [];
        
        foreach($req as $req_item) {
            if($req_item['finance_code'] == '000000001') {
                if($hasTarget) {
                    $_req[] = $req_item;
                }
            } else {
                $_req[] = $req_item;
            }
        }
        $req = $_req;
        
        $rows = [];
        
        foreach( $req as $req_item ) {
            if( $hasTarget && $req_item['finance_code'] == '000000001' ) {
                $rows[] = $req_item;
            } elseif( !( ( in_array($req_item['edulevel_code'], [ '000000001', '000000031', '000000002' ]) && $req_item['finance_code'] == '000000003'
                           && $req_item['eduform_code'] == '000000001' ) ) ) {
                $rows[] = $req_item;
            } elseif( $req_item['edulevel_code'] != '000000005' ) {
                $rows[] = $req_item;
            }
        }
        
        foreach( $rows as $req_item ) {
            foreach( $groups as $group_item ) {
                if( $group_item['code'] == '000000002' && $hasQuota ) {
                    if( $req_item['finance_code'] == '000000003' ) {
                        $_t                    = $req_item;
                        $_t['spec_group_code'] = $group_item['code'];
                        $_t['spec_group_name'] = $group_item['name'];
                        
                        $ret[] = $_t;
                    }
                } else {
                    $_t                    = $req_item;
                    $_t['spec_group_code'] = $group_item['code'];
                    $_t['spec_group_name'] = $group_item['name'];
                    
                    $ret[] = $_t;
                }
            }
        }
        
        return $ret;
    }
    
    /**
     * Gets special specialities based on 9 classes for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getSpecsSpecial9ForApp( $pay, $debug = FALSE ): array
    {
        $conds  = $this->CondsSpecial9Educ($pay, $debug);
        $params = $this->ParamsSpecial9Educ($pay);
        
        if( $debug ) {
            echo 'Debug: getSpecsSpecial9ForApp<br>';
        }
        
        $req = $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_code, finance_name, eduform_code, eduform_name, edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name, profil_name', 0, $debug);
        
        return $this->filterSpec($req, $pay);
    }
    
    /**
     * Gets special specialities based on 9 classes UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getSpecialitySpecial9ForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo "In getSpecialitySpecial9ForApp<br>";
        }
        
        $conds  = $this->CondsSpecial9Educ($pay);
        $params = $this->ParamsSpecial9Educ($pay);
        
        return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name', 0, $debug);
    }
    
    /**
     * Gets special finances based on 9 classes UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getFinanceSpecial9ForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo 'getFinanceSpecial9ForApp<br>';
        }
        
        $conds  = $this->CondsSpecial9Educ($pay);
        $params = $this->ParamsSpecial9Educ($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT finance_code, finance_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params, 'finance_name', NULL, $debug);
    }
    
    /**
     * Gets special eduforms based on 9 classes UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEduformSpecial9ForApp( $pay )
    {
        $conds  = $this->CondsSpecial9Educ($pay);
        $params = $this->ParamsSpecial9Educ($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params);
    }
    
    /**
     * Gets special edulevels based on 9 classes UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEdulevelSpecial9ForApp( $pay )
    {
        $conds  = $this->CondsSpecial9Educ($pay);
        $params = $this->ParamsSpecial9Educ($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params);
    }
    
    /**
     * Gets first high bachelor specialities for application.
     *
     * @param $pay
     * @param $debug
     *
     * @return array
     * @throws \Exception
     */
    public function getSpecsFirstBachelorForApp( $pay, $debug ): array
    {
        if( $debug ) {
            echo 'Debug: getSpecsFirstBachelorForApp<br/>';
        }
        
        $conds  = $this->CondsHighEducFirstBachelor($pay);
        $params = $this->ParamsHighEducFirstBachelor($pay);
        
        if( $debug ) {
            echo '<pre>';
            var_dump(['Debug: getSpecsFirstBachelorForApp' => [ 'conds' => $conds, 'parans' => $params ]]);
            echo '</pre>';
        }
        
        $req = $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_code, finance_name, eduform_code, eduform_name, edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name, profil_name', 0, $debug);
        
        return $this->filterSpec($req, $pay, $debug);
    }
    
    /**
     * Gets first high bachelor specialities UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     * @throws \Exception
     */
    public function getSpecialityFirstBachelorForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo "In getSpecialitySpecial9ForApp<br>";
        }
        
        $conds  = $this->CondsHighEducFirstBachelor($pay);
        $params = $this->ParamsHighEducFirstBachelor($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name', 0, $debug);
    }
    
    /**
     * Gets first high bachelor finances UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     * @throws \Exception
     */
    public function getFinanceFirstBachelorForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo 'getFinanceFirstBachelorForApp<br>';
        }
        
        $conds  = $this->CondsHighEducFirstBachelor($pay);
        $params = $this->ParamsHighEducFirstBachelor($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT finance_code, finance_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'finance_name', NULL, $debug);
    }
    
    /**
     * Gets first high bachelor eduforms UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     * @throws \Exception
     */
    public function getEduformFirstBachelorForApp( $pay )
    {
        $conds  = $this->CondsHighEducFirstBachelor($pay);
        $params = $this->ParamsHighEducFirstBachelor($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'eduform_name');
    }
    
    /**
     * Gets first high bachelor edulevels UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     * @throws \Exception
     */
    public function getEdulevelFirstBachelorForApp( $pay )
    {
        $conds  = $this->CondsHighEducFirstBachelor($pay);
        $params = $this->ParamsHighEducFirstBachelor($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'edulevel_name');
    }
    
    /**
     * Gets magister specialities for application.
     *
     * @param $pay
     * @param $debug
     *
     * @return array
     */
    public function getSpecsMagisterForApp( $pay, $debug ): array
    {
        if( $debug ) {
            echo 'Debug: getSpecsMagisterForApp<br/>';
        }
        
        $conds  = $this->CondsHighEducMagister($pay);
        $params = $this->ParamsHighEducMagister($pay);
        
        $req = $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_code, finance_name, eduform_code, eduform_name, edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name, profil_name', 0, $debug);
        
        return $this->filterSpec($req, $pay);
    }
    
    /**
     * Gets magister specialities UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getSpecialityMagisterForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo "In getSpecialitySpecial9ForApp<br>";
        }
        
        $conds  = $this->CondsHighEducMagister($pay);
        $params = $this->ParamsHighEducMagister($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name', 0, $debug);
    }
    
    /**
     * Gets magister finances UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getFinanceMagisterForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo 'getFinanceMagisterForApp<br>';
        }
        
        $conds  = $this->CondsHighEducMagister($pay);
        $params = $this->ParamsHighEducMagister($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT finance_code, finance_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'finance_name', NULL, $debug);
    }
    
    /**
     * Gets magister eduforms UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEduformMagisterForApp( $pay )
    {
        $conds  = $this->CondsHighEducMagister($pay);
        $params = $this->ParamsHighEducMagister($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'eduform_name');
    }
    
    /**
     * Gets magister edulevels UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEdulevelMagisterForApp( $pay )
    {
        $conds  = $this->CondsHighEducMagister($pay);
        $params = $this->ParamsHighEducMagister($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'edulevel_name');
    }
    
    /**
     * Gets first high specialities for application.
     *
     * @param $pay
     * @param $debug
     *
     * @return array
     */
    public function getSpecsFirstForApp( $pay, $debug ): array
    {
        if( $debug ) {
            echo 'Debug: getSpecsFirstForApp<br/>';
        }
        
        $conds  = $this->CondsHighEducFirst($pay, $debug);
        $params = $this->ParamsHighEducFirst($pay);
        
        $req = $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_code, finance_name, eduform_code, eduform_name, edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name, profil_name', 0, $debug);
        
        return $this->filterSpec($req, $pay);
    }
    
    /**
     * Gets first high specialities UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getSpecialityFirstForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo "In getSpecialitySpecial9ForApp<br>";
        }
        
        $conds  = $this->CondsHighEducFirst($pay);
        $params = $this->ParamsHighEducFirst($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name', 0, $debug);
    }
    
    /**
     * Gets first high finances UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getFinanceFirstForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo 'getFinanceFirstForApp<br>';
        }
        
        $conds  = $this->CondsHighEducFirst($pay);
        $params = $this->ParamsHighEducFirst($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT finance_code, finance_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'finance_name', NULL, $debug);
    }
    
    /**
     * Gets first high eduforms UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEduformFirstForApp( $pay )
    {
        $conds  = $this->CondsHighEducFirst($pay);
        $params = $this->ParamsHighEducFirst($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'eduform_name');
    }
    
    /**
     * Gets first high edulevels UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEdulevelFirstForApp( $pay )
    {
        $conds  = $this->CondsHighEducFirst($pay);
        $params = $this->ParamsHighEducFirst($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'edulevel_name');
    }
    
    /**
     * Gets second high specialities for application.
     *
     * @param $pay
     * @param $debug
     *
     * @return array
     */
    public function getSpecsSecondForApp( $pay, $debug ): array
    {
        if( $debug ) {
            echo 'Debug: getSpecsSecondForApp<br/>';
        }
        
        $conds  = $this->CondsHighEducSecond($pay);
        $params = $this->ParamsHighEducSecond($pay);
        
        $req = $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_code, finance_name, eduform_code, eduform_name, edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name, profil_name', 0, $debug);
        
        return $this->filterSpec($req, $pay);
    }
    
    /**
     * Gets second high specialities UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getSpecialitySecondForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo "In getSpecialitySpecial9ForApp<br>";
        }
        
        $conds  = $this->CondsHighEducSecond($pay);
        $params = $this->ParamsHighEducSecond($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'speciality_name', 0, $debug);
    }
    
    /**
     * Gets second high finances UNIQUE for application.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return array
     */
    public function getFinanceSecondForApp( $pay, $debug = FALSE )
    {
        if( $debug ) {
            echo 'getFinanceSecondForApp<br>';
        }
        
        $conds  = $this->CondsHighEducSecond($pay);
        $params = $this->ParamsHighEducSecond($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT finance_code, finance_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'finance_name', NULL, $debug);
    }
    
    /**
     * Gets second high eduforms UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEduformSecondForApp( $pay )
    {
        $conds  = $this->CondsHighEducSecond($pay);
        $params = $this->ParamsHighEducSecond($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'eduform_name');
    }
    
    /**
     * Gets second high edulevels UNIQUE for application.
     *
     * @param $pay
     *
     * @return array
     */
    public function getEdulevelSecondForApp( $pay )
    {
        $conds  = $this->CondsHighEducSecond($pay);
        $params = $this->ParamsHighEducSecond($pay);
        
        //Тестовый костыль
        //$conds = substr($conds, 0, -47);
        //unset($params[":dt"]);
        
        return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
                                   $this->TablesSpecs(),
                                   $conds,
                                   $params,
                                   'edulevel_name');
    }
    
    /**
     * Gets specialities for bachelor and specialist.
     *
     * @return array
     */
    public function getByAppForBachelorSpec()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND edulevel_name in (:edulevel_name1, :edulevel_name2)',
                                   [
                                       ':pid' => $this->pid,
                                       ':edulevel_name1' => 'Бакалавр',
                                       ':edulevel_name2' => 'Специалист'
                                   ]);
    }
    
    /**
     * Gets specialities for magister.
     *
     * @return array
     */
    public function getByAppForMagister()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND edulevel_name = :edulevel_name',
                                   [
                                       ':pid' => $this->pid,
                                       ':edulevel_name' => 'Магистр'
                                   ]);
    }
    
    /**
     * Gets specialities for special.
     *
     * @return array
     */
    public function getByAppForSpecial()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND edulevel_name = :edulevel_name',
                                   [
                                       ':pid' => $this->pid,
                                       ':edulevel_name' => 'СПО'
                                   ]);
    }
    
    /**
     * Gets specialities for clinical.
     *
     * @return array
     */
    public function getByAppForClinical()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND edulevel_name = :edulevel_name',
                                   [
                                       ':pid' => $this->pid,
                                       ':edulevel_name' => 'Ординатура'
                                   ]);
    }
    
    /**
     * Gets specialities for traineeship.
     *
     * @return array
     */
    public function getByAppForTraineeship()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND edulevel_name = :edulevel_name',
                                   [
                                       ':pid' => $this->pid,
                                       ':edulevel_name' => 'Аспирантура'
                                   ]);
    }
    
    /**
     * Gets specialities for medical certificate (A1 group).
     *
     * @return array
     */
    public function getByAppForMedicalA1()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND speciality_name like :speciality_name AND profil_name = :profil_name',
                                   [
                                       ':pid' => $this->pid,
                                       'speciality_name' => '44.03.01 Педагогическое образование%',
                                       ':profil_name' => 'Физическая культура'
                                   ]);
    }
    
    /**
     * Gets specialities for medical certificate (A2 group).
     *
     * @return array
     */
    public function getByAppForMedicalA2()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND speciality_name in (:speciality_name1, :speciality_name2, :speciality_name3, :speciality_name4, :speciality_name5, :speciality_name6, :speciality_name7)',
                                   [
                                       ':pid' => $this->pid,
                                       ':speciality_name1' => '49.03.01 Физическая культура',
                                       ':speciality_name2' => '38.05.02 Таможенное дело',
                                       ':speciality_name3' => '31.05.01 Лечебное дело',
                                       ':speciality_name4' => '31.05.02 Педиатрия',
                                       ':speciality_name5' => '31.05.03 Стоматология',
                                       ':speciality_name6' => '33.05.01 Фармация',
                                       ':speciality_name7' => '32.05.01 Медико-профилактическое дело'
                                   ]);
    }
    
    /**
     * Gets specialities for medical certificate (B1 group).
     *
     * @return array
     */
    public function getByAppForMedicalB1()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND eduform_name = :eduform_name AND speciality_name in (:speciality_name1, :speciality_name2, :speciality_name3, :speciality_name4, :speciality_name5, :speciality_name6, :speciality_name7)',
                                   [
                                       ':pid' => $this->pid,
                                       ':eduform_name' => 'Очная',
                                       ':speciality_name1' => '21.05.02 Прикладная геология',
                                       ':speciality_name2' => '21.05.04 Горное дело и направлениям подготовки',
                                       ':speciality_name3' => '44.03.01 Педагогическое образование',
                                       ':speciality_name4' => '44.03.05 Педагогическое образование',
                                       ':speciality_name5' => '44.03.02 Психолого-педагогическое образование',
                                       ':speciality_name6' => '44.03.03 Специальное (дефектологическое) образование',
                                       ':speciality_name7' => '19.03.04 Технология продукции и организация общественного питания'
                                   ]);
    }
    
    /**
     * Gets specialities for medical certificate (C1 group).
     *
     * @return array
     */
    public function getByAppForMedicalC1()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
                                   'pid = :pid AND edulevel_name = :edulevel_name AND speciality_name in (:speciality_name1, :speciality_name2, :speciality_name3, :speciality_name4, :speciality_name5, :speciality_name6, :speciality_name7, :speciality_name8, :speciality_name9, :speciality_name10, :speciality_name11)',
                                   [
                                       ':pid' => $this->pid,
                                       ':edulevel_name' => 'СПО',
                                       ':speciality_name1' => '31.02.01 Лечебное дело',
                                       ':speciality_name2' => '31.02.02 Акушерское дело',
                                       ':speciality_name3' => '31.02.03 Лабораторная диагностика',
                                       ':speciality_name4' => '31.02.05 Стоматология ортопедическая',
                                       ':speciality_name5' => '31.02.06 Стоматология профилактическая',
                                       ':speciality_name6' => '32.02.02 Медико-профилактическое дело',
                                       ':speciality_name7' => '33.02.01 Фармация',
                                       ':speciality_name8' => '34.02.01 Сестринское дело',
                                       ':speciality_name9' => '34.02.02 Медицинский массаж (для обучения лиц с ограниченными возможностями здоровья по зрению)',
                                       ':speciality_name10' => '44.02.01 Дошкольное образование',
                                       ':speciality_name11' => '44.02.02 Преподавание в начальных классах'
                                   ]);
    }
    
    /**
     * Gets specialities for payed online education.
     *
     * @return array
     */
    public function getByAppForPayedOnline()
    {
        return $this->rowSelectAll('application_places.*',
                                   'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id'.
                                   ' INNER JOIN dict_finances ON dict_speciality.finance_code = dict_finances.code',
                                   'pid = :pid AND dict_finances.abbr = :finance AND eduform_name = :eduform_name',
                                   [
                                       ':pid' => $this->pid,
                                       ':finance' => self::PAY,
                                       ':eduform_name' => 'Заочная'
                                   ]);
    }
    
    /**
     * Saves application places data to database.
     *
     * @return integer
     */
    public function getUserIdBySession()
    {
        $login = strtoupper($_SESSION["phpCAS"]["user"]);
        
        if( !empty($login) ) {
            $sql = "SELECT id FROM user WHERE UPPER(username) = \"$login\"";
            
            return self::$pdo->query($sql)
                             ->fetch()["id"];
        }
        
        echo "Логина не оказалось в сессии";
        die();
    }
    
    public function save()
    {
        $this->id_user    = $this->getUserIdBySession();
        $this->dt_created = date('Y-m-d H:i:s');
        
        $prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        
        return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
    }
    
    
    /**
     * Changes all application places data.
     *
     * @return boolean
     */
    public function changeAll()
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
    public function clearByApplication()
    {
        return $this->rowDelete(self::TABLE_NAME,
                                'pid = :pid',
                                [ ':pid' => $this->pid ]);
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
    
    /**
     * Returns conditions for special education based on 9 classes.
     *
     * @param      $pay
     *
     * @param bool $debug
     *
     * @return string
     */
    public function CondsSpecial9Educ( $pay, $debug = FALSE ): string
    {
        if( $pay == 1 ) {
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND dict_finances.abbr = :finance AND (dict_speciality.eduprogram_name = :eduprogram_name OR dict_speciality.eduprogram_name IS NULL) AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND (dict_speciality.eduprogram_name = :eduprogram_name OR dict_speciality.eduprogram_name IS NULL) AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
        
        if( !$this->hasTargetQuota() ) {
            $ret .= ' AND dict_finances.abbr <> :finance ';
        }
        
        if( APP_DATA !== 'test' ) {
            $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
        } else {
            $ret .= 'AND :dt IS NOT NULL';
        }
        
        return $ret;
    }
    
    /**
     * Returns conditions for the first high bachelor education.
     *
     * @param      $pay
     *
     * @param bool $debug
     *
     * @return string
     * @throws \Exception
     */
    public function CondsHighEducFirstBachelor( $pay, $debug = FALSE ): string
    {   
        if( $pay === 1 ) {         
            return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND dict_finances.abbr = :finance AND (dict_speciality.eduprogram_name is null OR dict_speciality.eduprogram_name = \'\') AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) AND :dt between stage_dt_begin and stage_dt_end';
        }

        if( APP_DATA === 'test' ) {
            $now = new \DateTime('2020-07-07T15:03:01.012345Z');
        } else {
            $now = new \DateTime();
        }
        
        $now         = \DateTime::CreateFromFormat('d.m.Y', $now->format('d.m.Y'));
        $personal    = new Personal();
        $citizenship = $personal->getCitizenshipByUser();
        $country     = new DictCountries();
        
        if( $debug ) {
            echo 'Debug: CondsHighEducFirstBachelor<br/>';
        }
        
        if( $citizenship['abroad'] == $country::ABROAD_FAR ) {
            if( $debug ) {
                echo 'Abroad<br/>';
            }
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary  AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            
            if( !$this->hasTargetQuota() ) {
                $ret .= ' AND dict_finances.abbr <> :finance ';
            }
            
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        if( $now >= \DateTime::CreateFromFormat('d.m.Y', self::BACHELOR_LIMIT_START) && $now <= \DateTime::CreateFromFormat('d.m.Y', self::BACHELOR_LIMIT_END) ) {
            if( $debug ) {
                echo 'Between BACH<br/>';
            }
            //AND dict_finances.abbr = :finance
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND (dict_speciality.eduprogram_name is null OR dict_speciality.eduprogram_name = \'\')AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        if( $now > \DateTime::CreateFromFormat('d.m.Y', self::BACHELOR_LIMIT_END) ) {
            if( $debug ) {
                echo 'After BACH<br/>';
            }
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND eduform_name = :eduform  AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            
            if( !$this->hasTargetQuota() ) {
                $ret .= ' AND dict_finances.abbr <> :finance ';
            }
            
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        if( $debug ) {
            echo 'Default<br/>';
        }
        $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary  AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) AND :dt between stage_dt_begin and stage_dt_end';
        
        if( !$this->hasTargetQuota() ) {
            $ret .= ' AND dict_finances.abbr <> :finance ';
        }
        
        return $ret;
    }
    
    /**
     * Returns conditions for the magister education.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return string
     */
    public function CondsHighEducMagister( $pay, $debug = FALSE ): string
    {
        if( $pay == 1 ) {
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND dict_finances.abbr = :finance AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary  AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
        
        if( !$this->hasTargetQuota() ) {
            $ret .= ' AND dict_finances.abbr <> :finance ';
        }
        
        if( APP_DATA !== 'test' ) {
            $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
        } else {
            $ret .= 'AND :dt IS NOT NULL';
        }
        
        return $ret;
    }
    
    /**
     * Returns conditions for the first high education.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return string
     */
    public function CondsHighEducFirst( $pay, $debug = FALSE ): string
    {
        if( $pay == 1 ) {
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND dict_finances.abbr = :finance AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary  AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
        
        if( !$this->hasTargetQuota() ) {
            $ret .= ' AND dict_finances.abbr <> :finance ';
        }
        
        if( APP_DATA !== 'test' ) {
            $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
        } else {
            $ret .= 'AND :dt IS NOT NULL';
        }
        
        return $ret;
    }
    
    /**
     * Returns conditions for the second high education.
     *
     * @param      $pay
     * @param bool $debug
     *
     * @return string
     */
    public function CondsHighEducSecond( $pay, $debug = FALSE ): string
    {
        if( $pay == 1 ) {
            $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary AND dict_finances.abbr = :finance AND (dict_speciality.eduprogram_name = :eduprogram_name) AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
            
            if( APP_DATA !== 'test' ) {
                $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
            } else {
                $ret .= 'AND :dt IS NOT NULL';
            }
            
            return $ret;
        }
        
        $ret = 'application.id = :pid AND group_beneficiary = :group_beneficiary  AND (dict_speciality.eduprogram_name = :eduprogram_name) AND (stage_numb = :stage_numb OR stage_numb is null) AND group_name not like (:group_name) ';
        
        if( !$this->hasTargetQuota() ) {
            $ret .= ' AND dict_finances.abbr <> :finance ';
        }
        
        if( APP_DATA !== 'test' ) {
            $ret .= 'AND :dt between stage_dt_begin and stage_dt_end';
        } else {
            $ret .= 'AND :dt IS NOT NULL';
        }
        
        return $ret;
    }
    
    /**
     * Returns parameters for special education based on 9 classes.
     *
     * @param $pay
     *
     * @return array
     */
    public function ParamsSpecial9Educ( $pay ): array
    {
        if( $pay == 1 ) {
            return [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':finance' => self::PAY,
                ':eduprogram_name' => 'среднее (основное,общее)',
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d')
            ];
        }
        
        $ret = [
            ':pid' => $this->pid,
            ':group_beneficiary' => 0,
            ':eduprogram_name' => 'среднее (основное,общее)',
            ':stage_numb' => 1,
            ':group_name' => 'WAT',
            ':dt' => date('Y-m-d H:i:s')
        ];
        
        if( !$this->hasTargetQuota() ) {
            $ret[':finance'] = self::PURPOSE;
        }
        
        return $ret;
    }
    
    /**
     * Returns parameters for the first high bachelor education.
     *
     * @param $pay
     *
     * @return array
     * @throws \Exception
     */
    public function ParamsHighEducFirstBachelor( $pay ): array
    {
        if( $pay == 1 ) {
            return [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':finance' => self::PAY,
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
        }
        
        //$now         = new \DateTime('2020-07-07T15:03:01.012345Z');//new \DateTime;
        $now         = new \DateTime;
        $now         = \DateTime::CreateFromFormat('d.m.Y', $now->format('d.m.Y'));
        $personal    = new Personal();
        $citizenship = $personal->getCitizenshipByUser();
        $country     = new DictCountries();
        
        //$campaiign_year = "19"; //isv: to remove
        
        if( $citizenship['abroad'] == $country::ABROAD_FAR ) {
            $ret = [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
            
            if( !$this->hasTargetQuota() ) {
                $ret[':finance'] = self::PURPOSE;
            }
            
            return $ret;
        }
        
        if( $now >= \DateTime::CreateFromFormat('d.m.Y', self::BACHELOR_LIMIT_START) && $now <= \DateTime::CreateFromFormat('d.m.Y', self::BACHELOR_LIMIT_END) ) {
            return [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                /*':finance' => self::PAY,*/
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
        }
        
        if( $now > \DateTime::CreateFromFormat('d.m.Y', self::BACHELOR_LIMIT_END) ) {
            $ert = [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':eduform' => 'Заочная',
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
            
            if( !$this->hasTargetQuota() ) {
                $ret[':finance'] = self::PURPOSE;
            }
            
            return $ret;
        }
        
        $ret = [
            ':pid' => $this->pid,
            ':group_beneficiary' => 0,
            ':stage_numb' => 1,
            ':group_name' => 'WAT',
            ':dt' => date('Y-m-d H:i:s')
        ];
        
        if( !$this->hasTargetQuota() ) {
            $ret[':finance'] = self::PURPOSE;
        }
        
        return $ret;
    }
    
    /**
     * Returns parameters for the magister education.
     *
     * @param $pay
     *
     * @return array
     */
    public function ParamsHighEducMagister( $pay ): array
    {
        if( $pay == 1 ) {
            return [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':finance' => self::PAY,
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
        }
        
        $ret = [
            ':pid' => $this->pid,
            ':group_beneficiary' => 0,
            ':stage_numb' => 1,
            ':group_name' => 'WAT',
            ':dt' => date('Y-m-d H:i:s')
        ];
        
        if( !$this->hasTargetQuota() ) {
            $ret[':finance'] = self::PURPOSE;
        }
        
        return $ret;
    }
    
    /**
     * Returns parameters for the first high education.
     *
     * @param $pay
     *
     * @return array
     */
    public function ParamsHighEducFirst( $pay ): array
    {
        if( $pay == 1 ) {
            return [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':finance' => self::PAY,
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
        }
        
        $ret = [
            ':pid' => $this->pid,
            ':group_beneficiary' => 0,
            ':stage_numb' => 1,
            ':group_name' => 'WAT',
            ':dt' => date('Y-m-d H:i:s')
        ];
        
        if( !$this->hasTargetQuota() ) {
            $ret[':finance'] = self::PURPOSE;
        }
        
        return $ret;
    }
    
    /**
     * Returns parameters for the second high education.
     *
     * @param $pay
     *
     * @return array
     */
    public function ParamsHighEducSecond( $pay ): array
    {
        if( $pay == 1 ) {
            return [
                ':pid' => $this->pid,
                ':group_beneficiary' => 0,
                ':finance' => self::PAY,
                ':eduprogram_name' => 'Высшее',
                ':stage_numb' => 1,
                ':group_name' => 'WAT',
                ':dt' => date('Y-m-d H:i:s')
            ];
        }
        
        $ret = [
            ':pid' => $this->pid,
            ':group_beneficiary' => 0,
            ':eduprogram_name' => 'Высшее',
            ':stage_numb' => 1,
            ':group_name' => 'WAT',
            ':dt' => date('Y-m-d H:i:s')
        ];
        
        if( !$this->hasTargetQuota() ) {
            $ret[':finance'] = self::PURPOSE;
        }
        
        return $ret;
    }
    
    public function __destruct()
    {
        $this->db = NULL;
    }
    
    private function hasTargetQuota(): bool
    {
        $result = $this->rowSelectOne('id', 'target_quota', 'id_user = :id_user', [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
        
        return !( $result === NULL || $result === FALSE );
    }
    
    public function getGroupFirstBachelorForApp( $pay )
    {
        $groups = ( new Model_DictSpecGroups() )->getAll();
        
        $has_privillege_record = $this->rowSelectOne('id', 'privilleges_quota', 'id_user = :id_user', [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
        
        if( is_array($has_privillege_record) && count($has_privillege_record) > 0 ) {
            $privileged = TRUE;
        } else {
            $privileged = FALSE;
        }
        
        $ret = [];
        foreach( $groups as $group_key => $group ) {
            if( $group['code'] == '000000002' ) {
                if( $privileged ) {
                    $ret[$group_key] = $group;
                }
            } else {
                $ret[$group_key] = $group;
            }
        }
        
        return $ret;
    }
    
    public function get()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id = :id',
                                   [ ':id' => $this->id ]);
    }    
    
    private function hasPrivQuota()
    {
        $quota_priv     = new \common\models\Model_PrivillegeQuota();
        $quota_priv_row = $quota_priv->getFirstByUser();
        
        return is_array($quota_priv_row) && count($quota_priv_row) > 0;
    }
}
