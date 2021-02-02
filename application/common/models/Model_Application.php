<?php

namespace common\models;

use common\models\Model_ApplicationAchievs as ApplicationAchievs;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_Scans as Scans;
use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Application
    extends Db_Helper
{
    /*
        Application processing
    */
    
    const TABLE_NAME       = 'application';
    
    const TYPE_NEW         = 1;
    
    const TYPE_NEW_NAME    = 'Заявление на приём документов';
    
    const TYPE_CHANGE      = 2;
    
    const TYPE_CHANGE_NAME = 'Заявление на изменение документов';
    
    const TYPE_RECALL      = 3;
    
    const TYPE_RECALL_NAME = 'Заявление на отзыв документов';
    
    /*
        "GO" - SENDED, APPROVED
    */
    const STATUS_CREATED       = 0;
    
    const STATUS_CREATED_NAME  = 'Новое';
    
    const STATUS_SENDED        = 1;
    
    const STATUS_SENDED_NAME   = 'Отправлено';
    
    const STATUS_APPROVED      = 2;
    
    const STATUS_APPROVED_NAME = 'Принято';
    
    const STATUS_REJECTED      = 3;
    
    const STATUS_REJECTED_NAME = 'Отклонено';
    
    const STATUS_SAVED         = 4;
    
    const STATUS_SAVED_NAME    = 'Сохранено';
    
    const STATUS_CHANGED       = 5;
    
    const STATUS_CHANGED_NAME  = 'Изменено';
    
    const STATUS_RECALLED      = 6;
    
    const STATUS_RECALLED_NAME = 'Отозвано';
    
    public $id;
    public $id_user;
    public $id_university;
    public $id_campaign;
    public $id_docseduc;
    public $id_docship;
    public $id_lang;
    public $id_app;
    public $type;
    public $status;
    public $numb;
    public $numb1s;
    public $inila;
    public $campus;
    public $conds;
    public $remote;
    public $pay;
    public $active;
    public $dt_created;
    
    public $db;
    
    public function __construct()
    {
        $this->db = Db_Helper::getInstance();
    }
    
    /**
     * Application rules.
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
            'id_user' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_user
            ],
            'id_university' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_university
            ],
            'id_campaign' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_campaign
            ],
            'id_docseduc' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_docseduc
            ],
            'id_docship' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_docship
            ],
            'id_lang' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_lang
            ],
            'id_app' => [
                'required' => 0,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_app
            ],
            'type' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->type
            ],
            'status' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->status
            ],
            'numb' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->numb
            ],
            'numb1s' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->numb1s
            ],
            'inila' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->inila
            ],
            'campus' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->campus
            ],
            'conds' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->conds
            ],
            'remote' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->remote
            ],
            'pay' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->pay
            ],
            'active' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->active
            ],
            'dt_created' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->dt_created
            ]
        ];
    }
    
    /**
     * Applications grid.
     *
     * @return array
     */
    public function grid()
    {
        return [
            'numb' => [
                'name' => 'Номер',
                'type' => 'int'
            ],
            'status' => [
                'name' => 'Статус заявления',
                'type' => 'string'
            ],
            'dt_status' => [
                'name' => 'Дата изменения статуса',
                'type' => 'date',
                'format' => 'd.m.Y H:i:s'
            ],
            'reason' => [
                'name' => 'Основание',
                'type' => 'string'
            ],
            'type' => [
                'name' => 'Тип',
                'type' => 'string'
            ],
            
            'university' => [
                'name' => 'Место поступления',
                'type' => 'string'
            ],
            'campaign' => [
                'name' => 'Приёмная кампания',
                'type' => 'string'
            ],
            'docs_educ' => [
                'name' => 'Документ об образовании',
                'type' => 'string'
            ],
            'dt_created' => [
                'name' => 'Дата создания',
                'type' => 'date',
                'format' => 'd.m.Y H:i:s'
            ]
        ];
    }
    
    /**
     * Generates application numb.
     *
     * @return string
     */
    public function generateNumb()
    {
        $NULLIFIER_OFFSET = 0;
        
        if( isset($this->id) && !empty($this->id) ) {
            return str_pad('', 11 - strlen($this->id - $NULLIFIER_OFFSET), '0').( $this->id - $NULLIFIER_OFFSET );
        } else {
            return str_pad('', 11, '0');
        }
    }
    
    /**
     * Gets applications by user for GRID.
     *
     * @return array
     */
    public function getByUserGrid()
    {
        $app_arr = $this->rowSelectAll("application.id,".
                                       " dict_university.code as university,".
                                       " admission_campaign.description as campaign,".
                                       " concat(dict_doctypes.description, ' № ', ifnull(concat(docs_educ.series, '-'), ''), docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as docs_educ,"
                                       .
                                       " reason.numb as reason,".
                                       " getAppTypeName(application.type) as type,".
                                       " getAppStatusName(application.status) as status,".
                                       " application.numb,".
                                       " application.dt_created",
                                       'application INNER JOIN dict_university ON application.id_university = dict_university.id'.
                                       ' INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id'.
                                       ' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
                                       ' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id'.
                                       ' LEFT OUTER JOIN application reason ON application.id_app = reason.id',
                                       'application.id_user = :id_user AND application.active = :active',
                                       [
                                           ':id_user' => $_SESSION[APP_CODE]['user_id'],
                                           ':active' => 1
                                       ],
                                       'admission_campaign.description ASC, application.numb ASC');
        if( $app_arr ) {
            $result = [];
            foreach( $app_arr as $app_row ) {
                $applog                 = new ApplicationStatus();
                $applog->id_application = $app_row['id'];
                $applog_row             = $applog->getLast();
                array_push($result, [
                    'id' => $app_row['id'],
                    'university' => $app_row['university'],
                    'campaign' => $app_row['campaign'],
                    'docs_educ' => $app_row['docs_educ'],
                    'reason' => $app_row['reason'],
                    'type' => $app_row['type'],
                    'status' => $app_row['status'],
                    'dt_status' => $applog_row['dt_created'],
                    'numb' => $app_row['numb'],
                    'dt_created' => $app_row['dt_created']
                ]);
            }
            
            return $result;
        } else {
            return NULL;
        }
    }
    
    /**
     * Gets application by ID.
     *
     * @return array
     */
    public function get()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id = :id',
                                   [ ':id' => $this->id ]);
    }
    
    /**
     * Gets applications by user.
     *
     * @return array
     */
    public function getByUser()
    {
        return $this->rowSelectAll('*',
                                   self::TABLE_NAME,
                                   'id_user = :id_user',
                                   [ ':id_user' => $_SESSION[APP_CODE]['user_id'] ]);
    }
    
    /**
     * Gets active applications by user.
     *
     * @return array
     */
    public function getActiveByUser()
    {
        return $this->rowSelectAll('*',
                                   self::TABLE_NAME,
                                   'id_user = :id_user and active = :active',
                                   [
                                       ':id_user' => $_SESSION[APP_CODE]['user_id'],
                                       ':active' => 1
                                   ]);
    }
    
    /**
     * Gets application spec.
     *
     * @return array
     */
    public function getSpec()
    {
        $result = [];
        $app    = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
        if( $app ) {
            // docs shipment
            $docs_ship = $this->rowSelectOne('code as docs_ship',
                                             'dict_docships',
                                             'id = :id',
                                             [ ':id' => $app['id_docship'] ]);
            if( !is_array($docs_ship) ) {
                $docs_ship = [ 'docs_ship' => NULL ];
            }
            // scans
            $scan            = new Model_Scans();
            $scan_arr        = $scan->getByDocrowFull('application', $this->id);
            $scan_recall     = new Model_Scans();
            $scan_recall_arr = $scan_recall->getByDocrowFull('application_recall', $this->id);
            $result          = array_merge($app, $docs_ship, $scan_arr, $scan_recall_arr);
        }
        
        return $result;
    }
    
    /**
     * Checks if campaign exists for user.
     *
     * @return boolean
     */
    public function existsUserCampaign( $debug = FALSE ): bool
    {
        $apps = $this->rowSelectAll('*',
                                    self::TABLE_NAME,
                                    'id_user = :id_user AND id_campaign = :id_campaign AND active = :active',
                                    [
                                        ':id_user' => $_SESSION[APP_CODE]['user_id'],
                                        ':id_campaign' => $this->id_campaign,
                                        ':active' => 1
                                    ],
                                    'dt_created', NULL, $debug);
        
        if( is_array($apps) && count($apps) > 0 ) {
            if( !isset($apps[0]) || !is_array($apps[0]) ) {
                $t_apps = $apps;
                $apps   = [];
                $apps[] = $t_apps;
            }
            
            if( $debug ) {
                echo '<pre>';
                var_dump([ 'result' => $apps ]);
                echo '</pre>';
            }
            
            $allowed = TRUE;
            foreach( $apps as $app ) {
                switch ( $app['type'] ) {
                    case self::TYPE_NEW:
                        if( $debug ) {
                            echo 'self::TYPE_NEW<br>';
                            
                            echo '<pre>';
                            var_dump(!( $app['status'] == self::STATUS_REJECTED ));
                            echo '</pre>';
                        }
                        
                        if( !( $app['status'] == self::STATUS_REJECTED ) ) {
                            $allowed = FALSE;
                        }
                        break;
                    case self::TYPE_RECALL:
                        if( $debug ) {
                            echo 'self::TYPE_RECALL<br>';
                            
                            echo '<pre>';
                            var_dump(!( $app['status'] == self::STATUS_APPROVED ));
                            echo '</pre>';
                        }
                        
                        if( !( $app['status'] == self::STATUS_APPROVED ) ) {
                            $allowed = FALSE;
                        }
                        break;
                    case self::TYPE_CHANGE:
                    default:
                        if( $debug ) {
                            echo 'self::TYPE_CHANGE<br>';
                        }

                        if(  $app['status'] != self::STATUS_REJECTED ) {
                            $allowed = FALSE;
                        }
                        break;
                }
            }
            
            if($debug) {
                echo '<pre>';
                var_dump(['allowed' => $allowed]);
                echo '</pre>';
            }
            
            return !$allowed;
        }
    
        if( $debug ) {
            echo 'FLOP<br>';
        }
    
        return FALSE;
    }
    
    /**
     * Checks if education document used in applications "GO".
     *
     * @return boolean
     */
    public function existsAppGo(): bool
    {
        $app_arr = $this->rowSelectAll('application.id',
                                       self::TABLE_NAME,
                                       'id_user = :id_user AND ((status IN (1, 2) and type IN (1,2)) OR (type = 3 AND status = 1)) AND active = 1',
                                       [
                                           ':id_user' => $_SESSION[APP_CODE]['user_id'],
                                       ]);
        if( $app_arr ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Saves application data to database.
     *
     * @return integer
     */
    public function save()
    {
        $this->id_user    = $_SESSION[APP_CODE]['user_id'];
        $this->status     = self::STATUS_CREATED;
        $this->active     = 1;
        $this->dt_created = date('Y-m-d H:i:s');
        $prepare          = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        $id               = $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
        if( $id > 0 ) {
            $this->id   = $id;
            $this->numb = $this->generateNumb();
            $this->changeNumb();
        }
        
        return $id;
    }
    
    /**
     * Changes all application data.
     *
     * @return boolean
     */
    public function changeAll()
    {
        $this->numb = $this->generateNumb();
        $prepare    = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
        
        return $this->rowUpdate(self::TABLE_NAME,
                                $prepare['fields'],
                                $prepare['params'],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes application foreign language.
     *
     * @return boolean
     */
    public function changeLang()
    {
        return $this->rowUpdate(self::TABLE_NAME,
                                'id_lang = :id_lang',
                                [ ':id_lang' => $this->id_lang ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes application type.
     *
     * @return boolean
     */
    public function changeType()
    {
        return $this->rowUpdate(self::TABLE_NAME,
                                'type = :type',
                                [ ':type' => $this->type ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes application status.
     *
     * @return boolean
     */
    public function changeStatus()
    {
        return $this->rowUpdate(self::TABLE_NAME,
                                'status = :status',
                                [ ':status' => $this->status ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes application numb.
     *
     * @return boolean
     */
    public function changeNumb()
    {
        return $this->rowUpdate(self::TABLE_NAME,
                                'numb = :numb',
                                [ ':numb' => $this->numb ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes application activity.
     *
     * @return boolean
     */
    public function changeActive()
    {
        return $this->rowUpdate(self::TABLE_NAME,
                                'active = :active',
                                [ ':active' => $this->active ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Removes application.
     *
     * @return integer
     */
    public function clear()
    {
        // clear scans
        $scans         = new Model_Scans();
        $scans->id_row = $this->id;
        $scans->clearbyDoc('application');
        
        $_t = $this->rowSelectOne('id_app', self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
        if(is_array($_t) && count($_t) > 0) {
            $this->rowUpdate(self::TABLE_NAME, 'active = :active', [':active' => 1], ['id' => $_t['id_app']]);
        }
        
        // clear app
        return $this->rowUpdate(self::TABLE_NAME, 'active = :active', [':active' => -1], [ 'id' => $this->id ]);
        //return $this->rowDelete(self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
    }
    
    /**
     * Copies application.
     *
     * @return integer
     */
    public function copy( $type = NULL )
    {
        $app_old = $this->get();
        // new application
        $this->id_university = $app_old['id_university'];
        $this->id_campaign   = $app_old['id_campaign'];
        $this->id_docseduc   = $app_old['id_docseduc'];
        $this->id_docship    = $app_old['id_docship'];
        $this->id_lang       = $app_old['id_lang'];
        $this->id_app        = $app_old['id'];
        $this->active        = 0;
        $this->changeActive();
        if( empty($type) ) {
            $this->type = self::TYPE_NEW;
        } else {
            $this->type = $type;
        }
        $this->campus = $app_old['campus'];
        $this->conds  = $app_old['conds'];
        $this->remote = $app_old['remote'];
        $this->pay    = $app_old['pay'];
        $id_old       = $this->id;
        $this->save();
        if( $this->id > 0 ) {
            // log
            $applog                 = new ApplicationStatus();
            $applog->id_application = $this->id;
            $applog->create();
            // places
            $places      = new ApplicationPlaces();
            $places->pid = $id_old;
            $places_arr  = $places->getSpecsByApp();
            if( $places_arr ) {
                foreach( $places_arr as $places_row ) {
                    $places->pid        = $this->id;
                    $places->id_spec    = $places_row['id_spec'];
                    $places->curriculum = $places_row['curriculum'];
                    $places->group_code = $places_row['group_code'];
                    $place              = $places->save();
                    // exams
                    $exams      = new ApplicationPlacesExams();
                    $exams->pid = $places_row['id'];
                    $exams_arr  = $exams->getExamsByPlaceFull();
                    if( $exams_arr ) {
                        foreach( $exams_arr as $exams_row ) {
                            $exams->pid           = $place;
                            $exams->id_test       = $exams_row['id_test'];
                            $exams->id_discipline = $exams_row['id_discipline'];
                            $exams->save();
                        }
                    }
                }
            }
            // achievs
            $ia      = new ApplicationAchievs();
            $ia->pid = $id_old;
            $ia_arr  = $ia->getByApp();
            if( $ia_arr ) {
                foreach( $ia_arr as $ia_row ) {
                    $ia->pid       = $this->id;
                    $ia->id_achiev = $ia_row['id_achiev'];
                    $ia->save();
                }
            }
            // scans
            $scans         = new Scans();
            $scans->id_row = $id_old;
            $scans_arr     = $scans->getByDocrow('application');
            if( $scans_arr ) {
                foreach( $scans_arr as $scans_row ) {
                    $scans->id_doc    = $scans_row['id_doc'];
                    $scans->id_row    = $this->id;
                    $scans->id_scans  = $scans_row['id_scans'];
                    $scans->file_data = $scans_row['file_data'];
                    $scans->file_name = $scans_row['file_name'];
                    $scans->file_type = $scans_row['file_type'];
                    $scans->file_size = $scans_row['file_size'];
                    $scans->save();
                }
            }
        }
        
        return $this->id;
    }
    
    /**
     * Checks bachelor.
     *
     * @param bool $debug
     *
     * @return boolean
     */
    public function checkBachelor( $debug = FALSE )
    {
        $row = $this->rowSelectOne('application.*',
                                   'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id'.
                                   ' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
                                   ' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
                                   'application.id = :id AND left(admission_campaign.description, 23) = :description',
                                   [
                                       ':id' => $this->id,
                                       ':description' => 'Бакалавриат/специалитет'
                                   ], $debug);
        if( !empty($row) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Checks magistrature.
     *
     * @return boolean
     */
    public function checkMagistrature()
    {
        $row = $this->rowSelectOne('application.*',
                                   'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id'.
                                   ' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
                                   ' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
                                   'application.id = :id AND left(admission_campaign.description, 12) = :description AND dict_doctypes.code in (:doc_type1, :doc_type2, :doc_type3, :doc_type4)',
                                   [
                                       ':id' => $this->id,
                                       ':description' => 'Магистратура',
                                       ':doc_type1' => '000000022',
                                       ':doc_type2' => '000000023',
                                       ':doc_type3' => '000000024',
                                       ':doc_type4' => '000000025'
                                   ]);
        if( !empty($row) ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Checks high after.
     *
     * @return boolean
     */
    public function checkHighAfter()
    {
        $row = $this->rowSelectOne('application.*',
                                   'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id',
                                   'application.id = :id AND (left(admission_campaign.description, 10) = :description1 OR left(admission_campaign.description, 11) = :description2)',
                                   [
                                       ':id' => $this->id,
                                       ':description1' => 'Ординатура',
                                       ':description2' => 'Аспирантура'
                                   ]);
        if( !empty($row) ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Checks certificate.
     *
     * @return boolean
     */
    public function checkCertificate()
    {
        $row = $this->rowSelectOne('application.*',
                                   'application INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
                                   ' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
                                   'application.id = :id AND dict_doctypes.code in (:code1, :code2)',
                                   [
                                       ':id' => $this->id,
                                       ':code1' => '000000026',
                                       ':code2' => '000000088'
                                   ]);
        if( !empty($row) ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function __destruct()
    {
        $this->db = NULL;
    }
    
    public function getThisUserActive()
    {
        return $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_user = :id_user and active = :active and id = :id',
                                   [
                                       ':id' => $this->id,
                                       ':id_user' => $_SESSION[APP_CODE]['user_id'],
                                       ':active' => 1
                                   ]);
    }

    public function checkCanSend()
    {
        $places = $this->rowSelectOne('COUNT(dict_speciality.id) count', 'dict_speciality
INNER JOIN application_places ap on dict_speciality.id = ap.id_spec
INNER JOIN application a on ap.pid = a.id', 'NOW() NOT between dict_speciality.stage_dt_begin and dict_speciality.stage_dt_end AND a.id = :id', [':id' => $this->id]);
        if(isset($places['count']) && $places['count'] > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
