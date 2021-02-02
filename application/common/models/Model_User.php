<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('USERNAME_HELP', 'Логин должен содержать <strong>'.MSG_ALPHA.'</strong>, и быть не более <strong>45</strong> символов длиной.');
define('EMAIL_HELP',
       'Адрес электронной почты должен быть <strong>'.MSG_EMAIL_LIGHT.'</strong>, содержать <strong>'.MSG_ALPHA.'</strong> и не более <strong>45</strong> символов длиной.');
define('PWD_HELP', 'Пароль должен содержать <strong>'.MSG_ALPHA_NUMB.'</strong>, и быть <strong>6-10</strong> символов длиной.');
define('PWD_CONFIRM_HELP', 'Пароль должен содержать <strong>'.MSG_ALPHA_NUMB.'</strong>, и быть <strong>6-10</strong> символов длиной.');

define('USERNAME_PLC', 'Логин');
define('EMAIL_PLC', 'user@domain');
define('PWD_PLC', 'Пароль');
define('PWD_CONFIRM_PLC', 'Повторите пароль');

class Model_User
    extends Db_Helper
{
    /*
        Users processing
    */
    
    const TABLE_NAME            = 'user';
    
    const ROLE_GUEST            = 0;
    
    const ROLE_GUEST_NAME       = 'Гость';
    
    const ROLE_MANAGER          = 1;
    
    const ROLE_MANAGER_NAME     = 'Модератор';
    
    const ROLE_ADMIN            = 2;
    
    const ROLE_ADMIN_NAME       = 'Администратор';
    
    const ROLE_LIST             = [
        [ 'code' => 0, 'description' => 'Гость' ],
        [ 'code' => 1, 'description' => 'Модератор' ],
        [ 'code' => 2, 'description' => 'Администратор' ]
    ];
    
    const STATUS_NOTACTIVE      = 0;
    
    const STATUS_NOTACTIVE_NAME = 'Не активен';
    
    const STATUS_ACTIVE         = 1;
    
    const STATUS_ACTIVE_NAME    = 'Активен';
    
    const STATUS_DELETED        = 2;
    
    const STATUS_DELETED_NAME   = 'Удалён';
    
    const LIMIT_ROWS            = 10;
    
    const LIMIT_PAGES           = 20;
    
    public $id;
    public $username;
    public $email;
    public $pwd_hash;
    public $activation;
    public $pwd_token;
    public $role;
    public $status;
    public $dt_created;
    public $dt_updated;
    
    public $db;
    
    public function __construct()
    {
        $this->db = Db_Helper::getInstance();
    }
    
    /**
     * User rules.
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
            'username' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->username
            ],
            'email' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->email
            ],
            'pwd_hash' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->pwd_hash
            ],
            'activation' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->activation
            ],
            'pwd_token' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->pwd_token
            ],
            'role' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->role
            ],
            'status' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->status
            ],
            'dt_created' => [
                'required' => 1,
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
     * User grid.
     *
     * @return array
     */
    public function grid()
    {
        return [
            'id' => [
                'name' => '№',
                'type' => 'int'
            ],
            'username' => [
                'name' => 'Логин',
                'type' => 'string'
            ],
            'email' => [
                'name' => 'Эл. почта',
                'type' => 'string'
            ],
            'role' => [
                'name' => 'Роль',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Состояние',
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
     * Gets users for GRID.
     *
     * @return array
     */
    public function getGrid()
    {
        return $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, dt_created",
                                   self::TABLE_NAME);
    }
    
    /**
     * Gets users rows less count.
     *
     * @return int
     */
    public function getRowsCountLess(): int
    {
        $count = $this->rowSelectOne('count(*) as rows',
                                     self::TABLE_NAME,
                                     'id <= :id',
                                     [ ':id' => $this->id ]);
        
        return $count['rows'];
    }
    
    /**
     * Gets users rows count.
     *
     * @return int
     */
    public function getRowsCount(): int
    {
        $count = $this->rowSelectOne('count(*) as rows', self::TABLE_NAME);
        
        return $count['rows'];
    }
    
    /**
     * Gets users pages.
     *
     * @return mixed
     */
    public function getPages()
    {
        $next = $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                    self::TABLE_NAME,
                                    'id < :id',
                                    [ ':id' => $this->id ],
                                    'id DESC',
                                    ( $this::LIMIT_ROWS * $this::LIMIT_PAGES ) / 2);
        $prev = $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                    self::TABLE_NAME,
                                    'id >= :id',
                                    [ ':id' => $this->id ],
                                    'id ASC',
                                    ( $this::LIMIT_ROWS * $this::LIMIT_PAGES ) / 2);
        if( $prev && $next ) {
            $prev = array_reverse($prev);
            
            return array_merge($prev, $next);
        }
        
        if( $next ) {
            return array_reverse($this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                                     self::TABLE_NAME,
                                                     'id < :id',
                                                     [ ':id' => $this->id ],
                                                     'id DESC',
                                                     $this::LIMIT_ROWS * $this::LIMIT_PAGES));
        }
        
        if( $prev ) {
            return $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                       self::TABLE_NAME,
                                       'id >= :id',
                                       [ ':id' => $this->id ],
                                       'id ASC',
                                       $this::LIMIT_ROWS * $this::LIMIT_PAGES);
        }
        
        return NULL;
    }
    
    /**
     * Gets users pages count.
     *
     * @return int
     */
    public function getPagesCount(): int
    {
        $count = $this->rowSelectOne('count(*) as rows', self::TABLE_NAME);
        
        return $count['rows'] / self::LIMIT_ROWS;
    }
    
    /**
     * Gets users page number.
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        $count = $this->rowSelectOne('count(*) as rows',
                                     self::TABLE_NAME,
                                     'id >= :id',
                                     [ ':id' => $this->id]);
        
        return ( $count['rows'] / self::LIMIT_ROWS ) + 1;
    }
    
    /**
     * Gets users previous page.
     *
     * @return array
     */
    public function getPagePrev()
    {
        return $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                   self::TABLE_NAME,
                                   'id >= :id',
                                   [ ':id' => $this->id + self::LIMIT_ROWS ],
                                   'id',
                                   $this::LIMIT_ROWS);
    }
    
    /**
     * Gets users next page.
     *
     * @return array
     */
    public function getPageNext()
    {
        return $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                   self::TABLE_NAME,
                                   'id <= :id',
                                   [ ':id' => $this->id ],
                                   'id DESC',
                                   $this::LIMIT_ROWS);
    }
    
    /**
     * Gets user by ID.
     *
     * @return array
     */
    public function get()
    {
        return $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [ ':id' => $this->id ]);
    }
    
    /**
     * Gets min user.
     *
     * @return array
     */
    public function getMin()
    {
        return $this->rowSelectOne('min(id) as id', self::TABLE_NAME);
    }
    
    public function getMax()
    {
        return $this->rowSelectOne('max(id) as id', self::TABLE_NAME);
    }
    
    /**
     * Gets user by name.
     *
     * @return array
     */
    public function getByUsername()
    {
        return $this->rowSelectOne('*', self::TABLE_NAME, 'username = :username', [ ':username' => $this->username ]);
    }
    
    /**
     * Gets user by email.
     *
     * @return array
     */
    public function getByEmail()
    {
        return $this->rowSelectOne('*', self::TABLE_NAME, 'email = :email', [ ':email' => $this->email ]);
    }
    
    /**
     * Checks if username exists.
     *
     * @return boolean
     */
    public function existsUsername()
    {
        $row = $this->rowSelectOne('id', self::TABLE_NAME, 'username = :username', [ ':username' => $this->username ]);
        if( !empty($row) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Checks if username exists except this ID.
     *
     * @return boolean
     */
    public function existsUsernameExcept()
    {
        $row = $this->rowSelectOne('id',
                                   self::TABLE_NAME,
                                   'username = :username AND id <> :id',
                                   [
                                       ':username' => $this->username,
                                       ':id' => $this->id
                                   ]);
        if( !empty($row) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Checks if email exists.
     *
     * @return boolean
     */
    public function existsEmail()
    {
        $row = $this->rowSelectOne('id', self::TABLE_NAME, 'email = :email', [ ':email' => $this->email ]);
        if( !empty($row) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Checks if email exists except this ID.
     *
     * @return boolean
     */
    public function existsEmailExcept()
    {
        $row = $this->rowSelectOne('id',
                                   self::TABLE_NAME,
                                   'email = :email AND id <> :id',
                                   [
                                       ':email' => $this->email,
                                       ':id' => $this->id
                                   ]);
        if( !empty($row) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Searches users by username.
     *
     * @return array
     */
    public function search( $conds, $params )
    {
        return $this->rowSelectAll("id, username, email, getUserRoleName(role) as role, getUserStatusName(status) as status, date_format(dt_created, '%d.%m.%Y %H:%i:%s') as dt_created",
                                   self::TABLE_NAME,
                                   $conds,
                                   $params,
                                   'id ASC');
    }
    
    /**
     * Saves user data to database.
     *
     * @return integer
     */
    public function save()
    {
        $this->dt_created = date('Y-m-d H:i:s');
        $this->dt_updated = NULL;
        $prepare          = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        
        return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
    }
    
    /**
     * Changes all user data.
     *
     * @return boolean
     */
    public function changeAll()
    {
        $this->dt_updated = date('Y-m-d H:i:s');
        $prepare          = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
        
        return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], [ 'id' => $this->id ]);
    }
    
    /**
     * Changes user status.
     *
     * @return boolean
     */
    public function changeStatus()
    {
        $this->dt_updated = date('Y-m-d H:i:s');
        
        return $this->rowUpdate(self::TABLE_NAME,
                                'status = :status, dt_updated = :dt_updated',
                                [ ':status' => $this->status, ':dt_updated' => $this->dt_updated ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes user password token.
     *
     * @return boolean
     */
    public function changePwdToken()
    {
        $this->dt_updated = date('Y-m-d H:i:s');
        
        return $this->rowUpdate(self::TABLE_NAME,
                                'pwd_token = :pwd_token, dt_updated = :dt_updated',
                                [ ':pwd_token' => $this->pwd_token, ':dt_updated' => $this->dt_updated ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Changes user password.
     *
     * @return boolean
     */
    public function changePwd()
    {
        $this->dt_updated = date('Y-m-d H:i:s');
        
        return $this->rowUpdate(self::TABLE_NAME,
                                'pwd_hash = :pwd_hash, dt_updated = :dt_updated',
                                [ ':pwd_hash' => $this->pwd_hash, ':dt_updated' => $this->dt_updated ],
                                [ 'id' => $this->id ]);
    }
    
    /**
     * Sets user session.
     *
     * @return void
     */
    public function setUser($is_admin = FALSE)
    {
        switch ( LOGON ) {
            case 'login':
                $_SESSION[APP_CODE][self::TABLE_NAME.'_id']     = $this->id;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_name']   = $this->username;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_email']  = $this->email;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_role']   = $this->role;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
                break;
            case 'cas':
                $_SESSION[APP_CODE][self::TABLE_NAME.'_id']     = $this->id;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_email']  = $this->email;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_role']   = $this->role;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
                break;
            default:
                $_SESSION[APP_CODE][self::TABLE_NAME.'_id']     = $this->id;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_name']   = $this->username;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_email']  = $this->email;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_role']   = $this->role;
                $_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
        }
        
        if($is_admin || $_SESSION[APP_CODE][self::TABLE_NAME.'_role'] == 2) {
            $_SESSION[APP_CODE]['is_admin'] = FALSE;
        } else{
            $_SESSION[APP_CODE]['is_admin'] = FALSE;
        }
    }
    
    /**
     * Unsets user session.
     *
     * @return void
     */
    public function unsetUser()
    {
        unset($_SESSION[APP_CODE][self::TABLE_NAME.'_id']);
        unset($_SESSION[APP_CODE][self::TABLE_NAME.'_name']);
        unset($_SESSION[APP_CODE][self::TABLE_NAME.'_email']);
        unset($_SESSION[APP_CODE][self::TABLE_NAME.'_role']);
        unset($_SESSION[APP_CODE][self::TABLE_NAME.'_status']);
    }
    
    public function __destruct()
    {
        $this->db = NULL;
    }
}
