<?php /** @noinspection TypeUnsafeComparisonInspection */

/** @noinspection PhpUnused */

namespace common\models;

require_once ROOT_DIR . '/application/common/models/Model_ApplicationConfirmPlaces.php';

use common\models\Model_ApplicationConfirmPlaces as ApplicationConfirmPlaces;
use tinyframe\core\helpers\Db_Helper;
use tinyframe\core\helpers\HTML_Helper;

class Model_ApplicationConfirm
    extends Db_Helper
{

    const TYPE_NONE = NULL;

    const TYPE_NEW = 0;

    const TYPE_RECALL = 1;

    const TYPE_NAMES = [
        self::TYPE_NONE => 'Нет',
        self::TYPE_NEW => 'Согласие',
        self::TYPE_RECALL => 'Отзыв согласия',
    ];

    /*
        "GO" - SENDED, APPROVED
    */
    const STATUS_UNSET = NULL;

    const STATUS_CREATED = 0;

    const STATUS_SAVED = 1;

    const STATUS_SENT = 2;

    const STATUS_APPROVED = 3;

    const STATUS_REJECTED = 4;

    const STATUS_RECALLED = 5;

    const STATUSES = [
        self::STATUS_UNSET => 'Отсутствует',
        self::STATUS_CREATED => 'Новое',
        self::STATUS_SENT => 'Отправлено',
        self::STATUS_APPROVED => 'Принято',
        self::STATUS_REJECTED => 'Отклонено',
        self::STATUS_SAVED => 'Сохранено',
        self::STATUS_RECALLED => 'Отозвано'
    ];

    const TABLE_NAME = 'application_confirm';

    public $id;
    public $id_application;
    public $id_status;
    public $type;
    public $parent_id;
    public $active;
    public $dt_created;
    public $dt_update;
    public $comment;

    public $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Db_Helper::getInstance();
    }

    /**
     * Application rules.
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
            'id_application' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_application
            ],
            'id_status' => [
                'required' => 1,
                'insert' => 0,
                'update' => 1,
                'value' => $this->id_status
            ],
            'type' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->type
            ],
            'parent_id' => [
                'required' => 0,
                'insert' => 0,
                'update' => 0,
                'value' => $this->parent_id
            ],
            'active' => [
                'required' => 1,
                'insert' => 0,
                'update' => 1,
                'value' => $this->active
            ],
            'dt_created' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->dt_created
            ],
            'dt_update' => [
                'required' => 1,
                'insert' => 0,
                'update' => 1,
                'value' => $this->dt_update
            ],
            'comment' => [
                'required' => 0,
                'insert' => 0,
                'update' => 0,
                'value' => $this->comment
            ]
        ];
    }

    /**
     * Applications grid.
     *
     * @return array
     */
    public function grid(): array
    {
        return [
            'numb' => [
                'name' => 'Номер подтвержденного заявления',
                'type' => 'int'
            ],
            'dt_created' => [
                'name' => 'Дата создания',
                'type' => 'string',
            ],
            'type' => [
                'name' => 'Тип заявления',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус заявления',
                'type' => 'string'
            ],
            'dt_updated' => [
                'name' => 'Дата изменения статуса',
                'type' => 'string',
            ],
            'actions' => [
                'name' => 'действия',
                'type' => 'string',
            ]
        ];
    }

    public function getByUserGridUnfiltered()
    {
        return $this->rowSelectAll('application.id, ' .
            'reason.numb AS reason, ' .
            'application.type AS type, ' .
            'application.numb, ' .
            'application.dt_created, ' .
            'application_confirm.id AS confirm_id, ' .
            'application_confirm.id_status AS status, ' .
            'application_confirm.dt_update dt_updated, ' .
            'application_confirm.type AS type ',
            'application INNER JOIN dict_university ON application.id_university = dict_university.id' .
            ' LEFT JOIN application_confirm ON application_confirm.id_application = application.id' .
            ' LEFT OUTER JOIN application reason ON application.id_app = reason.id',
            'application.id_user = :id_user
                                       AND (application_confirm.active = :active OR application_confirm.active IS NULL)
                                       AND application.active = :active
                                       AND application.status = :app_status
                                       AND application.type IN (:app_type)',
            [
                ':id_user' => $_SESSION[APP_CODE]['user_id'],
                ':active' => 1,
                ':app_status' => 2,
                ':app_type' => '1, 2'
            ],
            'application.numb');
    }

    /**
     * Gets applications by user for GRID.
     */
    public function getByUserGrid()
    {
        /*AND application_confirm.active = 1*/
        $app_arr = $this->rowSelectAll('application.id id, ' .
            'reason.numb AS reason, ' .
            'application.type AS type, ' .
            'application.id_campaign, ' .
            'application.numb, ' .
            'application_confirm.dt_created, ' .
            'application_confirm.id AS confirm_id, ' .
            'application_confirm.id_status AS status, ' .
            'application_confirm.active AS confirm_active, ' .
            'application_confirm.parent_id AS parent_id, ' .
            'application_confirm.dt_update dt_updated, ' .
            'application_confirm.type AS type',
            'application INNER JOIN dict_university ON application.id_university = dict_university.id' .
            ' LEFT JOIN application_confirm ON application_confirm.id_application = application.id AND application_confirm.active = 1' .
            ' LEFT OUTER JOIN application reason ON application.id_app = reason.id',
            'application.id_user = :id_user
                                       AND application.active = 1
                                       AND application.status = 2
                                       AND application.type IN (1, 2)',
            [
                ':id_user' => $_SESSION[APP_CODE]['user_id']
            ],
            'application.numb');

        if ($app_arr) {
            $_app_arr = [];

            foreach ($app_arr as $app_row) {
                unset($app_row[0], $app_row[1], $app_row[2], $app_row[3], $app_row[4], $app_row[5], $app_row[6], $app_row[7], $app_row[8], $app_row[9], $app_row[10], $app_row[11]);

                if (!empty($app_row['confirm_id']) && ($app_row['confirm_active'] != 1 || $app_row['status'] == self::STATUS_RECALLED)) {
                    unset($app_row['confirm_id'], $app_row['status'], $app_row['confirm_active'], $app_row['parent_id'], $app_row['dt_created'], $app_row['dt_updated'], $app_row['type']);
                }

                if (!empty($app_row['confirm_id'])) {
                    if ($app_row['type'] == self::TYPE_RECALL) {
                        if ($app_row['status'] == self::STATUS_APPROVED) {
                            $_t = $app_row;
                            unset($_t['confirm_id'], $_t['status'], $_t['confirm_active'], $_t['parent_id'], $_t['dt_created'], $_t['dt_updated'], $_t['type']);
                            $_app_arr[] = $_t;
                            $_app_arr[] = $app_row;
                            foreach ($app_arr as $_app_row) {
                                if ($app_row['parent_id'] == $_app_row['confirm_id']) {
                                    $_app_arr[] = $_app_row;
                                    break;
                                }
                            }
                        } else {
                            $_app_arr[] = $app_row;
                        }
                    } else {
                        $_app_arr[] = $app_row;
                    }
                } else {
                    $_app_arr[] = $app_row;
                }
            }

            $to_unset = [];
            foreach ($_app_arr as $index => $item) {
                if(!isset($_item['confirm_id'])) {
                    foreach ($_app_arr as $_index => $_item) {
                        if($item['id'] == $_item['id'] && !isset($_item['confirm_id']) && $index != $_index) {
                            $to_unset[] = $_index;
                        }
                    }
                }
            }

            foreach ($to_unset as $unset_target) {
                unset($_app_arr[$unset_target]);
            }

            $to_unset = [];
            foreach ($_app_arr as $index => $item) {
                if(isset($item['confirm_id']) && isset($item['type']) && $item['type'] == 0) {
                    foreach ($_app_arr as $_index => $_item) {
                        if($item['id'] == $_item['id'] && !isset($_item['confirm_id'])) {
                            $to_unset[] = $_index;
                        }
                    }
                }
            }

            foreach ($to_unset as $unset_target) {
                unset($_app_arr[$unset_target]);
            }

            $app_arr = $_app_arr;

            $result = [];
            foreach ($app_arr as $app_row) {
                $action = '-';
                if (!empty($app_row['confirm_id'])) {
                    switch ($app_row['status']) {
                        case self::STATUS_UNSET:
                            $action = HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Add/?app_id=' . $app_row['id'], 'btn btn-success',
                                'Подать');
                            break;
                        case
                        self::STATUS_CREATED:
                        case self::STATUS_SAVED:
                            $action = HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Edit/?id=' . $app_row['confirm_id'], 'btn btn-primary',
                                'Редактировать');
                            $action .= HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Delete/?id=' . $app_row['confirm_id'], 'btn btn-danger',
                                'Удалить');
                            break;
                        case self::STATUS_RECALLED:
                        case self::STATUS_SENT:
                            $action = HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Edit/?id=' . $app_row['confirm_id'], 'btn btn-primary',
                                'Просмотреть');
                            break;
                        case self::STATUS_APPROVED:
                            $action = HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Edit/?id=' . $app_row['confirm_id'], 'btn btn-primary',
                                'Просмотреть');
                            if ($app_row['type'] == 0) {
                                $action .= HTML_Helper::setHrefButton('ApplicationConfirm',
                                    'Recall/?id=' . $app_row['confirm_id'], 'btn btn-danger',
                                    'Отозвать');
                            }
                            break;
                        case self::STATUS_REJECTED:
                            $action = HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Edit/?id=' . $app_row['confirm_id'], 'btn btn-primary',
                                'Просмотреть');
                            $action .= HTML_Helper::setHrefButton('ApplicationConfirm',
                                'Delete/?id=' . $app_row['confirm_id'], 'btn btn-danger',
                                'Удалить');
                            break;
                        default:
                            break;
                    }
                } else {
                    $action = HTML_Helper::setHrefButton('ApplicationConfirm',
                        'Add/?app_id=' . $app_row['id'], 'btn btn-success',
                        'Подать');
                }
                if (isset($app_row['id'])) {
                    $result[] = [
                        'id' => $app_row['id'],
                        'reason' => $app_row['reason'],
                        'type' => self::TYPE_NAMES[$app_row['type'] ?? NULL],
                        'status_id' => $app_row['status'] ?? NULL,
                        'status' => self::STATUSES[$app_row['status'] ?? NULL],
                        'numb' => $app_row['numb'],
                        'dt_created' => isset($app_row['dt_created']) && $app_row['dt_created'] > 0 ? date('d.m.Y H:i:s', strtotime($app_row['dt_created'])) : '-',
                        'dt_updated' => isset($app_row['dt_updated']) && $app_row['dt_updated'] > 0 ? date('d.m.Y H:i:s', strtotime($app_row['dt_updated'])) : '-',
                        'actions' => $action
                    ];
                }
            }

            return $result;
        }

        return NULL;
    }

    /**
     * Gets application by ID.
     *
     * @return array|NULL|bool
     */
    public function get($debug = false)
    {
        return $this->rowSelectOne('application_confirm.id,
    id_application,
    id_status,
    application_confirm.type,
    parent_id,
    application_confirm.active,
    application_confirm.dt_created,
    dt_update,
    comment,
    application_confirm.numb1s,
    application.numb', 'application_confirm
    INNER JOIN application
                   ON application.id = application_confirm.id_application'
            ,
            'application_confirm.id = :id AND application.id_user = :user_id',
            [':id' => $this->id,
                ':user_id' => $_SESSION[APP_CODE]['user_id']], $debug);
    }

    public function getWithDocs()
    {
        $result = [];
        $app = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
        if ($app) {
            if ($app['type'] == 0) {
                $_filter = 'application_confirm';
            } else {
                $_filter = 'app_confirm_recall';
            }
            // scans
            $scan = new Model_Scans();
            $scan_arr = $scan->getByDocrowFull($_filter, $this->id);
            $result = array_merge($app, $scan_arr);
        }

        return $result;
    }

    public function getByApp()
    {
        return $this->rowSelectOne('*',
            self::TABLE_NAME,
            'id_application = :id_application',
            [':id_application' => $this->id_application]);
    }

    public function getByAppAndActive()
    {
        return $this->rowSelectOne('*',
            self::TABLE_NAME,
            'id_application = :id_application AND active = 1',
            [':id_application' => $this->id_application]);
    }

    /**
     * Gets applications by user.
     *
     * @return array|NULL|bool
     */
    public function getByUser($debug = FALSE)
    {
        return $this->rowSelectAll('
        application_confirm.id,
       application_confirm.id_application,
       application_confirm.id_status,
       application_confirm.type,
       application_confirm.parent_id,
       application_confirm.active,
       application_confirm.dt_created,
       application_confirm.dt_update,
       application_confirm.comment,
       application_confirm.numb1s',
            'application_confirm
         INNER JOIN application ON application_confirm.id_application = application.id',
            'id_user = :id_user',
            [':id_user' => $_SESSION[APP_CODE]['user_id']], NULL, NULL, $debug);
    }

    public function getByUserAndId()
    {
        return $this->rowSelectOne('
        application_confirm.id,
        application_confirm.id_application,
        application_confirm.id_status,
        application_confirm.type,
        application_confirm.parent_id,
        application_confirm.active,
        application_confirm.dt_created,
        application_confirm.dt_update,
        application.id_campaign',
            '
        application_confirm INNER JOIN application
        ON application_confirm.id_application = application.id',
            'application.active = 1 AND
        application_confirm.active = 1 AND
        application.id_user = :id_user AND
        application_confirm.id = :id',
            [
                ':id' => $this->id,
                ':id_user' => $_SESSION[APP_CODE]['user_id']
            ]);
    }

    /**
     * Gets active applications by user.
     *
     * @return array|NULL|bool
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
     * Checks if education document used in applications "GO".
     *
     * @return boolean
     */
    public function existsAppGo(): bool
    {
        $app_arr = $this->rowSelectAll('application.id',
            self::TABLE_NAME,
            'id_user = :id_user AND ((application.status in (1,2) and application.type in (1,2)) OR application.status = 1 and application.type = 3) AND application.active = 1',
            [
                ':id_user' => $_SESSION[APP_CODE]['user_id']
            ]);
        if ($app_arr) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Saves application data to database.
     *
     * @return integer
     */
    public function save(): int
    {
        $prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());

        $new_id = $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);

        if (is_numeric($new_id)) {
            $this->id = $new_id;
        } else {
            return FALSE;
        }

        return $new_id;
    }

    /**
     * Changes all application data.
     *
     * @return boolean
     */
    public function changeAll(): bool
    {
        $prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());

        return $this->rowUpdate(self::TABLE_NAME,
            $prepare['fields'],
            $prepare['params'],
            ['id' => $this->id]);
    }

    /**
     * Changes application status.
     *
     * @return boolean
     */
    public function changeStatus(): bool
    {
        return $this->rowUpdate(self::TABLE_NAME,
            'id_status = :status',
            [':status' => $this->id_status],
            ['id' => $this->id]);
    }

    /**
     * Changes application status.
     *
     * @return boolean
     */
    public function changeSingle($field, $new_value = NULL): bool
    {
        if ($new_value === NULL) {
            $new_value = $this->$field;
        }

        return $this->rowUpdate(self::TABLE_NAME,
            $field . ' = :val',
            [':val' => $new_value],
            ['id' => $this->id]);
    }

    /**
     * Changes application activity.
     *
     * @return boolean
     */
    public function changeActive(): bool
    {
        return $this->rowUpdate(self::TABLE_NAME,
            'active = :active',
            [':active' => $this->active],
            ['id' => $this->id]);
    }

    /**
     * Removes application.
     *
     * @return integer
     */
    public function clear($debug = FALSE): int
    {
        // clear scans
        $scans = new Model_Scans();
        $scans->id_row = $this->id;
        $scans->clearbyDoc('application_confirm');

        // clear app
        $return = $this->rowUpdate(self::TABLE_NAME, 'active = :active', [':active' => -1], ['id' => $this->id]);
        if ($this->type != 0) {
            $this->rowUpdate(self::TABLE_NAME, 'active = :active', [':active' => 1], ['id' => $this->parent_id]);
        }

        return $return;
    }

    /**
     * Copies application.
     *
     * @param null $type
     *
     * @return integer
     */
    public function copy($type = NULL): int
    {
        $app_old = $this->get();

        // new confirm
        $this->id = $app_old['id'];
        $this->id_application = $app_old['id_application'];
        $this->id_status = $app_old['id_status'];
        $this->active = $app_old['active'];
        $this->dt_created = $app_old['dt_created'];
        $this->dt_update = $app_old['dt_update'];

        $this->active = 0;
        $this->changeActive();
        if (empty($type)) {
            $this->type = self::TYPE_NEW;
        } else {
            $this->type = $type;
        }
        $id_old = $this->id;
        $this->save();
        if ($this->id > 0) {
            // places
            $places = new ApplicationConfirmPlaces();
            $places->id_application_confirm = $id_old;
            $places_arr = $places->getSpecsByApp();
            if ($places_arr) {
                foreach ($places_arr as $places_row) {
                    $places->id_application_confirm = $this->id;
                    $places->id_application_place = $places_row['id_application_place'];
                    $places->save();
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
    public function checkBachelor($debug = FALSE): bool
    {
        $row = $this->rowSelectOne('application.*',
            'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id' .
            ' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id' .
            ' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
            'application.id = :id AND left(admission_campaign.description, 23) = :description',
            [
                ':id' => $this->id,
                ':description' => 'Бакалавриат/специалитет'
            ], $debug);
        if (!empty($row)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Checks magistrature.
     *
     * @return boolean
     */
    public function checkMagistrature(): bool
    {
        $row = $this->rowSelectOne('application.*',
            'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id' .
            ' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id' .
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
        if (!empty($row)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Checks high after.
     *
     * @return boolean
     */
    public function checkHighAfter(): bool
    {
        $row = $this->rowSelectOne('application.*',
            'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id',
            'application.id = :id AND (left(admission_campaign.description, 10) = :description1 OR left(admission_campaign.description, 11) = :description2)',
            [
                ':id' => $this->id,
                ':description1' => 'Ординатура',
                ':description2' => 'Аспирантура'
            ]);
        if (!empty($row)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Checks certificate.
     *
     * @return boolean
     */
    public function checkCertificate(): bool
    {
        $row = $this->rowSelectOne('application.*',
            'application INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id' .
            ' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
            'application.id = :id AND dict_doctypes.code in (:code1, :code2)',
            [
                ':id' => $this->id,
                ':code1' => '000000026',
                ':code2' => '000000088'
            ]);
        if (!empty($row)) {
            return TRUE;
        }

        return FALSE;
    }

    public function __destruct()
    {
        $this->db = NULL;
    }

    public function buildFromApplication($app_id)
    {
        $application = new Model_Application();
        $application->id = $app_id;
        $application_row = $application->get();

        if (is_array($application_row) && $application_row['id_user'] == $_SESSION[APP_CODE]['user_id'] && $application_row['status'] == 2) {
            $this->id_application = $app_id;
            $this->type = 0;
            $this->dt_created = date('Y-m-d H:i:s');

            $this->id = $this->save();

            $application_place = new Model_ApplicationPlaces();
            $application_place->pid = $app_id;
            $application_place_rows = $application_place->getSpecsByApp();

            foreach ($application_place_rows as $application_place_row) {
                $application_confirm_place = new Model_ApplicationConfirmPlaces();

                $application_confirm_place->id_application_confirm = $this->id;
                $application_confirm_place->id_application_place = $application_place_row['id'];

                $application_confirm_place->save();
            }

            return $this->id;
        }

        return NULL;
    }

    public function isSelected(): bool
    {
        $_row = $this->get();

        if ($_row['type'] == self::TYPE_NEW) {
            $req = $this->rowSelectOne('id',
                Model_ApplicationConfirmPlaces::TABLE_NAME,
                'id_application_confirm = :app_id AND selected=1',
                [':app_id' => $this->id]
            );
        } else {
            $req = $this->rowSelectOne('application_confirm_places.id',
                'application_confirm_places
                                                INNER JOIN application_confirm
                                                    ON application_confirm_places.id_application_confirm = application_confirm.parent_id',
                'application_confirm.id = :app_id AND selected=1',
                [':app_id' => $this->id]
            );
        }

        return $req !== NULL && $req !== FALSE && (is_array($req) && count($req) > 0);
    }

    public function updateStatus($status)
    {
        return $this->rowUpdate(self::TABLE_NAME, 'id_status = :id_status', [':id_status' => $status], ['id' => $this->id]);
    }

    public function recall()
    {
        $app_array = $this->get();
        $this->changeSingle('active', 0);

        $new_app = new self();
        $new_app->id_application = $app_array['id_application'];
        $new_app->dt_created = date('Y-m-d H:i:s');

        $new_app->parent_id = $app_array['id'];
        $new_app->id_status = self::STATUS_CREATED;
        $new_app->type = self::TYPE_RECALL;
        $new_app->active = 1;

        $new_id = $new_app->save();

        $new_app->changeSingle('parent_id', $app_array['id']);

        return $new_id;
    }

    public function getRootNumb($debug = false)
    {
        $row = $this->get($debug);

        if (is_array($row) && isset($row['numb'])) {
            return $row['numb'];
        }

        return null;
    }

    public function getSelectedPlace($debug = false)
    {

        if ($this->get()['type'] == $this::TYPE_NEW) {
            return $this->rowSelectOne(
                'dict_spec.id,
                        dict_spec.speciality_code,
                        dict_spec.speciality_name,
                        dict_spec.profil_code,
                        dict_spec.profil_name,
                        dict_spec.finance_code,
                        dict_spec.finance_name,
                        dict_spec.eduform_code,
                        dict_spec.eduform_name,
                        dict_spec.edulevel_code,
                        dict_spec.edulevel_name,
                        app_place.group_code',
                'application_confirm_places    app_conf_place
                            INNER JOIN application_confirm app_conf
                                ON app_conf_place.id_application_confirm = app_conf.id
                            INNER JOIN application_places  app_place
                                ON app_conf_place.id_application_place = app_place.id
                            INNER JOIN dict_speciality     dict_spec
                                ON app_place.id_spec = dict_spec.id',
                'app_conf_place.selected = 1 AND
                       app_conf.id = :id',
                [':id' => $this->id], $debug);
        }

        return $this->rowSelectOne(
            'dict_spec.id,
                    dict_spec.speciality_code,
                    dict_spec.speciality_name,
                    dict_spec.finance_code,
                    dict_spec.finance_name,
                    dict_spec.eduform_code,
                    dict_spec.eduform_name,
                    dict_spec.edulevel_code,
                    dict_spec.edulevel_name,
                    app_place.group_code',
            'application_confirm_places    app_conf_place
                        INNER JOIN application_confirm app_conf
                            ON app_conf_place.id_application_confirm = app_conf.parent_id
                        INNER JOIN application_places  app_place
                            ON app_conf_place.id_application_place = app_place.id
                        INNER JOIN dict_speciality     dict_spec
                            ON app_place.id_spec = dict_spec.id',
            'app_conf_place.selected = 1 AND
                   app_conf.id = :id',
            [':id' => $this->id], $debug);
    }

    public function getValidCount()
    {
        $picks = $this->rowSelectAll('
        application_confirm.id,
       application_confirm.id_application,
       application.id_user,
       application.id_campaign,
       application_confirm.id_status,
       application_confirm.type,
       application_confirm.parent_id,
       application_confirm.active,
       application_confirm.dt_created,
       application_confirm.dt_update,
       application_confirm.comment,
       application_confirm.numb1s', '
        application_confirm
         INNER JOIN application ON application_confirm.id_application = application.id',
            'application_confirm.active NOT IN (-1, -2) AND application.id_campaign NOT IN (63, 67, 68) AND application.id_user = :user_id',
            [':user_id' => $_SESSION[APP_CODE]['user_id']]);



        $campains = $this->rowSelectAll('admission_campaign.id, admission_campaign.description',
            'admission_campaign INNER JOIN application ON application.id_campaign = admission_campaign.id',
            'application.active = 1 AND application.status = 2 AND application.type != 3
             AND application.id_user = :user',
            [':user' => $_SESSION[APP_CODE]['user_id']], NULL, NULL);

        if (is_array($campains)) {
            $res = [];
            foreach ($campains as $campaign) {
                $res[$campaign['id']] = ['desc' => $campaign['description'], 'idle' => [], 'ready' => [], 'recalled' => []];
            }

            if (is_array($picks)) {
                foreach ($picks as $pick) {
                    $pick_selected_place = $this->rowSelectOne(
                        'application_confirm_places.id id',
                        'application_confirm_places
INNER JOIN application_places ON application_places.id = application_confirm_places.id_application_place
INNER JOIN dict_speciality ds on application_places.id_spec = ds.id',
                        'selected = 1 AND application_confirm_places.id_application_confirm = :id_conf AND ds.finance_code != "000000003"',
                        [':id_conf' => $pick['id']]
                    );

                    $accounted = TRUE;
                    if(is_array($pick_selected_place) && count($pick_selected_place) > 0 && $pick_selected_place['id'] > 0) {
                        $accounted = FALSE;
                    }

                    if ($pick['type'] == self::TYPE_NEW && $accounted) {
                        switch ($pick['id_status']) {
                            case self::STATUS_CREATED:
                            case self::STATUS_SAVED:
                            case self::STATUS_REJECTED;
                                $res[$pick['id_campaign']]['idle'][] = $pick['id'];
                                break;
                            case self::STATUS_SENT:
                            case self::STATUS_APPROVED:
                            case self::STATUS_RECALLED:
                                $_type = 'ready';
                                foreach ($picks as $_pick) {
                                    if ($_pick['type'] == self::TYPE_RECALL && $_pick['parent_id'] == $pick['id']) {
                                        switch ($_pick['id_status']) {
                                            case self::STATUS_CREATED:
                                            case self::STATUS_SAVED:
                                            case self::STATUS_SENT:
                                                $_type = 'ready';
                                                break;
                                            case self::STATUS_APPROVED:
                                            case self::STATUS_RECALLED:
                                                $_type = 'recalled';
                                                break;
                                            case self::STATUS_REJECTED:
                                            default:
                                                break;
                                        }
                                    }
                                }
                                $res[$pick['id_campaign']][$_type][] = $pick['id'];
                                break;
                        }
                    }
                }

                return $res;
            }
        }

        return [];
    }

    public function getValidConfsByAppId($app_id)
    {
        return $this->rowSelectAll('*', self::TABLE_NAME, 'id_application = :app_id AND type = 0 AND active = 1', [':app_id' => $app_id]);
    }
}
