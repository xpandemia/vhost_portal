<?php /** @noinspection TypeUnsafeComparisonInspection */

/** @noinspection PhpUnused */

namespace common\models;

use tinyframe\core\helpers\Db_Helper;
use tinyframe\core\helpers\HTML_Helper;

class Model_Agreement
    extends Db_Helper
{
    const STATUS_UNSET = NULL;

    const STATUS_CREATED = 0;

    const STATUS_SAVED_PAYER_DATA = 8;

    const STATUS_SENT_PAYER_DATA = 1;

    const STATUS_ALLOWED = 2;

    const STATUS_DISALLOWED = 3;

    const STATUS_SAVED_SCANS = 9;

    const STATUS_SENT_SCANS = 4;

    const STATUS_REJECTED = 6;

    const STATUS_APPROVED = 5;

    const STATUSES = [
        self::STATUS_UNSET => 'Отсутствует',
        self::STATUS_CREATED => 'Новое',
        self::STATUS_SAVED_PAYER_DATA => 'Данные плательщика сохранены',
        self::STATUS_SENT_PAYER_DATA => 'Данные плательщика отправлены',
        self::STATUS_ALLOWED => 'Одобрено. Ожидает заполнения документов',
        self::STATUS_DISALLOWED => 'Отказано в заключении договора',
        self::STATUS_SAVED_SCANS => 'Скан-копии документов сохранены',
        self::STATUS_SENT_SCANS => 'Скан-копии документов отправлены',
        self::STATUS_REJECTED => 'Отклонено',
        self::STATUS_APPROVED => 'Принято'
    ];

    const PAYER_SELF = 0;
    const PAYER_PERSON = 1;
    const PAYER_LEGAL_AGENT = 2;

    const TYPES = [
        self::PAYER_SELF => 'Абитуриент',
        self::PAYER_PERSON => 'Физическое лицо',
        self::PAYER_LEGAL_AGENT => 'Юридическое лицо'
    ];

    const TABLE_NAME = 'edu_contracts';

    public $id;
    public $id_user;
    public $id_confirm;
    public $payer_type;
    public $status;
    public $name_last;
    public $name_first;
    public $name_middle;
    public $address_reg;
    public $address_res;
    public $id_doctype;
    public $series;
    public $sex;
    public $birth_dt;
    public $birth_place;
    public $citizenship;
    public $numb;
    public $dt_issue;
    public $unit_name;
    public $unit_code;
    public $phone_number;
    public $org_name;

    public $is_self_payer;
    public $contract_number;
    public $has_supply_agreement;
    public $has_mat_capital;

    public $active;
    public $comment;

    public $dt_created;
    public $dt_updated;

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
                'required' => 0,
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
            'id_confirm' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_confirm
            ],
            'payer_type' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->payer_type
            ],
            'has_mat_capital' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->has_mat_capital
            ],
            'org_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->org_name
            ],
            'name_last' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->name_last
            ],
            'name_first' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->name_first
            ],
            'name_middle' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->name_middle
            ],
            'address_reg' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->address_reg
            ],
            'address_res' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->address_res
            ],
            'id_doctype' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->id_doctype
            ],
            'series' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->series
            ],
            'numb' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->numb
            ],
            'unit_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->unit_name
            ],
            'unit_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->unit_code
            ],
            'sex' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->sex
            ],
            'birth_dt' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->birth_dt
            ],
            'birth_place' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->birth_place
            ],
            'citizenship' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->citizenship
            ],
            'dt_issue' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->dt_issue
            ],
            'phone_number' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->phone_number
            ],
            'is_self_payer' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->is_self_payer
            ],
            'contract_number' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->contract_number
            ],
            'has_supply_agreement' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->has_supply_agreement
            ],
            'status' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->status
            ],
            'active' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->active
            ],
            'comment' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->comment
            ],
            'dt_created' => [
                'required' => 0,
                'insert' => 1,
                'update' => 0,
                'value' => $this->dt_created
            ],
            'dt_updated' => [
                'required' => 0,
                'insert' => 0,
                'update' => 1,
                'value' => $this->dt_updated
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
            'type' => [
                'name' => 'плательщик',
                'type' => 'string'
            ],
            'name' => [
                'name' => 'Описание плательщика',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус',
                'type' => 'string'
            ],
            'actions' => [
                'name' => 'действия',
                'type' => 'string',
                'format' => 'd.m.Y H:i:s'
            ]
        ];
    }

    /**
     * Gets applications by user for GRID.
     */
    public function getByUserGrid()
    {
        $result = [];

        $debug = false;

        $conf = new Model_ApplicationConfirm();
        $conf_rows = $conf->getByUser($debug);


        if (is_array($conf_rows) && count($conf_rows) > 0) {
            foreach ($conf_rows as $conf_row_index => $conf_item) {
                if ($conf_item['active'] == 1) {
                    switch ($conf_item['type']) {
                        case Model_ApplicationConfirm::TYPE_NEW:
                            if ($conf_item['id_status'] == Model_ApplicationConfirm::STATUS_APPROVED) {
                                $_conf = new Model_ApplicationConfirm();
                                $_conf->id = $conf_item['id'];
                                $selected_row = $_conf->getSelectedPlace($debug);

                                if (is_array($selected_row) && count($selected_row) > 0) {
                                    $action = HTML_Helper::setHrefButton('Agreement',
                                        'Add/?conf_id=' . $_conf->id, 'btn btn-success',
                                        'Подать');

                                    $result[$_conf->getRootNumb($debug)] = ['numb' => $_conf->getRootNumb($debug), 'type' => NULL, 'name' => NULL, 'status_code' => NULL, 'status' => NULL, 'actions' => $action];
                                }
                            }
                            break;
                        case Model_ApplicationConfirm::TYPE_RECALL:
                            if ( in_array($conf_item['id_status'],
                                    [
                                        Model_ApplicationConfirm::STATUS_CREATED,
                                        Model_ApplicationConfirm::STATUS_SAVED,
                                        Model_ApplicationConfirm::STATUS_REJECTED,
                                        Model_ApplicationConfirm::STATUS_RECALLED
                                    ]
                                )) {
                                $_conf = new Model_ApplicationConfirm();
                                $_conf->id = $conf_item['id'];
                                $selected_row = $_conf->getSelectedPlace($debug);

                                if (is_array($selected_row) && count($selected_row) > 0) {
                                    $action = HTML_Helper::setHrefButton('Agreement',
                                        'Add/?conf_id=' . $_conf->id, 'btn btn-success',
                                        'Подать');

                                    $result[$_conf->getRootNumb($debug)] = ['numb' => $_conf->getRootNumb($debug), 'type' => NULL, 'name' => NULL, 'status_code' => NULL, 'status' => NULL, 'actions' => $action];
                                }
                            }
                            break;
                    }
                }
            }
        } elseif ($debug) {
            debug_print_object('Нет пригодных согласий');
        }

        $agreement = new self();
        $agreement_rows = $agreement->getActiveByUser($debug);

        if (is_array($agreement_rows) && count($agreement_rows) > 0) {
            foreach ($agreement_rows as $agreement_row) {
                $action = '';

                $edit = $action = HTML_Helper::setHrefButton(AGREEMENT['ctr'],
                    'Edit/?id=' . $agreement_row['id'], 'btn btn-primary',
                    'Детали');
                $delete = HTML_Helper::setHrefButton(AGREEMENT['ctr'],
                    'Delete/?id=' . $agreement_row['id'], 'btn btn-danger',
                    'Удалить');

                switch ($agreement_row['status']) {
                    case self::STATUS_SAVED_PAYER_DATA:
                    case self::STATUS_ALLOWED:
                    case self::STATUS_DISALLOWED:
                    case self::STATUS_SAVED_SCANS:
                    case self::STATUS_REJECTED:
                    case self::STATUS_CREATED:
                        $action = $edit . $delete;
                        break;
                    case self::STATUS_SENT_SCANS:
                    case self::STATUS_APPROVED:
                    case self::STATUS_SENT_PAYER_DATA:
                        $action = $edit;
                        break;
                }

                $_conf = new Model_ApplicationConfirm();
                $_conf->id = $agreement_row['id_confirm'];

                switch ($agreement_row['payer_type']) {
                    case self::PAYER_SELF:
                        $_name = '-';
                        break;
                    case self::PAYER_PERSON:
                        $_name = $agreement_row['name_last'] . ' ' . $agreement_row['name_first'] . ' ' . $agreement_row['name_middle'];
                        break;
                    case self::PAYER_LEGAL_AGENT:
                        $_name = $agreement_row['org_name'];
                        break;
                }

                unset($result[$_conf->getRootNumb($debug)]);
                $result[$_conf->getRootNumb($debug)] = ['numb' => $_conf->getRootNumb($debug), 'type' => self::TYPES[$agreement_row['payer_type']], 'name' => $_name, 'status_code' => $agreement_row['status'], 'status' => self::STATUSES[$agreement_row['status']], 'actions' => $action];
            }
        }

        return $result;
    }

    /**
     * Gets application by ID.
     *
     * @return array|NULL|bool
     */
    public function get()
    {
        return $this->rowSelectOne('*', self::TABLE_NAME,
            'id = :id AND id_user = :user_id',
            [':id' => $this->id,
                ':user_id' => $_SESSION[APP_CODE]['user_id']]);
    }

    public function getWithCountryFix() {
        $data = $this->get();

        if(!empty($data['citizenship'])) {
            $dict_citizenshop = new \common\models\Model_DictCountries();
            $dict_citizenshop->id = $data['citizenship'];
            $data['citizenship'] = $dict_citizenshop->get()['code'];
        }

        return $data;
    }

    public function load($id)
    {
        $this->id = $id;
        $row = $this->get();
        $rules = $this->rules();

        foreach ($rules as $name => $rule) {
            foreach ($row as $index => $item) {
                if ($index == $name) {
                    $this->$name = $item;
                }
            }
        }
    }

    public function getByConfirmId()
    {
        return $this->rowSelectOne('*', self::TABLE_NAME,
            'id_confirm = :id AND id_user = :user_id',
            [':id' => $this->id_confirm,
                ':user_id' => $_SESSION[APP_CODE]['user_id']]);
    }

    public function getAllByConfirmId()
    {
        return $this->rowSelectAll('*', self::TABLE_NAME,
            'id_confirm = :id AND id_user = :user_id',
            [':id' => $this->id_confirm,
                ':user_id' => $_SESSION[APP_CODE]['user_id']]);
    }

    public function getWithDocs()
    {
        $result = [];
        $app = $this->getWithCountryFix();
        if ($app) {
            // scans
            $scan = new Model_Scans();
            $scan_arr = $scan->getByDocrowFull('agreement', $this->id);
            $result = array_merge($app, $scan_arr);
        }

        return $result;
    }

    /**
     * Gets applications by user.
     *
     * @return array|NULL|bool
     */
    public function getByUser()
    {
        return $this->rowSelectAll('*',
            self::TABLE_NAME,
            'id_user = :id_user',
            [':id_user' => $_SESSION[APP_CODE]['user_id']]);
    }

    /**
     * Gets active applications by user.
     *
     * @return array|NULL|bool
     */
    public function getActiveByUser($debug = false)
    {
        return $this->rowSelectAll('*',
            self::TABLE_NAME,
            'id_user = :id_user and active = :active',
            [
                ':id_user' => $_SESSION[APP_CODE]['user_id'],
                ':active' => 1
            ], NULL, 0, $debug);
    }

    /**
     * Checks if education document used in applications "GO".
     *
     * @return boolean
     */
    public function existsGo(): bool
    {
        $app_arr = $this->rowSelectAll('application.id',
            self::TABLE_NAME,
            "id_user = :id_user AND active = 1 AND status != " . $this::STATUS_REJECTED,
            [
                ':id_user' => $_SESSION[APP_CODE]['user_id'],
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
            debug_print_object('Отскок при создании записи. Почему?..');
            die();
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
            [':status' => $this->status],
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

    public function available()
    {
        $res = $this->rowSelectOne(
            'id',
            self::TABLE_NAME,
            'id_confirm = :id_confirm AND active = 1 AND status not in (' . self::STATUS_DISALLOWED . ', ' . self::STATUS_REJECTED . ')',
            [':id_confirm' => $this->id_confirm]
        );

        if (is_array($res) && count($res) > 0) {
            return FALSE;
        }

        return TRUE;
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
        $scans->clearbyDoc('agreement');

        // clear app
        return $this->rowUpdate(self::TABLE_NAME, 'active = :active', [':active' => -1], ['id' => $this->id], $debug);
    }

    public function __destruct()
    {
        $this->db = NULL;
    }

    public function buildFromApplication($conf_id)
    {
        $confirm = new Model_ApplicationConfirm();
        $confirm->id = $conf_id;
        $confirm_row = $confirm->get();
        $place = $confirm->getSelectedPlace();

        if (is_array($confirm_row) && is_array($place) && $confirm_row['id_user'] == $_SESSION[APP_CODE]['user_id'] && $confirm_row['status'] == $confirm::STATUS_APPROVED && $place['finance_code'] == '000000002') {
            $this->id_confirm = $conf_id;
            $this->type = 0;
            $this->dt_created = date('Y-m-d H:i:s');

            $this->id = $this->save();

            $application_place = new Model_ApplicationPlaces();
            $application_place->pid = $conf_id;
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

    public function updateStatus($status)
    {
        return $this->rowUpdate(self::TABLE_NAME, 'id_status = :id_status', [':id_status' => $status], ['id' => $this->id]);
    }
}
