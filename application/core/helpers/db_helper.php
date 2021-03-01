<?php

namespace tinyframe\core\helpers;

use PDO;

class Db_Helper
{
    // Database processing *Singleton*
    
    public static $pdo;
    
    private static $_instance = NULL;
    
    /**
     * Protected constructor to prevent creating a new instance of the
     * Db_Helper via the 'new' operator from outside of this class.
     *
     * @return void
     */
    protected function __construct()
    {
        try {
            self::$pdo = new PDO('mysql:host='.DB_HOST.';charset=utf8;dbname='.DB_NAME,
                                 DB_USER,
                                 DB_PASSWORD,
                                 [
                                     PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                                     PDO::ATTR_PERSISTENT => TRUE,
                                     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                 ]);
        } catch ( \PDOException $pdo_err ) {
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
            exit;
        }
    }
    
    /**
     * Private clone method to prevent cloning of the instance of the
     * Db_Helper instance.
     *
     * @return void
     */
    private function __clone()
    {
    }
    
    /**
     * Private unserialize method to prevent unserializing of the Db_Helper
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
    
    /**
     * Returns the Db_Helper instance.
     *
     * @staticvar Db_Helper $instance.
     *
     * @return Db_Helper instance.
     */
    public static function getInstance()
    {
        if( self::$_instance != NULL ) {
            return self::$_instance;
        }
        
        return new self();
    }
    
    /**
     * Prepares insert query.
     *
     * @param $table_name
     * @param $rules
     *
     * @return array
     */
    public function prepareInsert( $table_name, $rules )
    {
        $result['fields'] = '';
        $result['conds']  = '';
        $result['params'] = [];
        $i                = 1;
        
        foreach( $rules as $field_name => $rule_name_arr ) {
            foreach( $rule_name_arr as $rule_name => $rule_var ) {
                switch ( $rule_name ) {
                    case 'insert':
                        if( $rule_var == 1 ) {
                            if( $rules[$field_name]['required'] == 1 ) {
                                if( !empty($rules[$field_name]['value']) || $rules[$field_name]['value'] == '0' ) {
                                    if( $i == 1 ) {
                                        $result['fields'] = $field_name;
                                        $result['conds']  = ':'.$field_name;
                                    } else {
                                        $result['fields'] .= ', '.$field_name;
                                        $result['conds']  .= ', :'.$field_name;
                                    }
                                    $result['params'][':'.$field_name] = $rules[$field_name]['value'];
                                } else {
                                    throw new \InvalidArgumentException('Поле '.$field_name.' таблицы '.$table_name.' обязательно для заполнения!');
                                }
                            } else {
                                if( !empty($rules[$field_name]['value']) ) {
                                    if( $i == 1 ) {
                                        $result['fields'] = $field_name;
                                        $result['conds']  = ':'.$field_name;
                                    } else {
                                        $result['fields'] .= ', '.$field_name;
                                        $result['conds']  .= ', :'.$field_name;
                                    }
                                    $result['params'][':'.$field_name] = $rules[$field_name]['value'];
                                }
                            }
                            $i++;
                        }
                        break;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Prepares update query.
     *
     * @param $table_name
     * @param $rules
     * @return array
     */
    public function prepareUpdate( $table_name, $rules )
    {
        $result['fields'] = '';
        $result['params'] = [];
        $i                = 1;
        foreach( $rules as $field_name => $rule_name_arr ) {
            foreach( $rule_name_arr as $rule_name => $rule_var ) {
                switch ( $rule_name ) {
                    case 'update':
                        if( $rule_var == 1 ) {
                            if( $rules[$field_name]['required'] == 1 && empty($rules[$field_name]['value']) && $rules[$field_name]['value'] != '0' ) {
                                throw new \InvalidArgumentException('Поле '.$field_name.' таблицы '.$table_name.' обязательно для заполнения!');
                            } else {
                                if( $i == 1 ) {
                                    $result['fields'] = $field_name.' = :'.$field_name;
                                } else {
                                    $result['fields'] .= ', '.$field_name.' = :'.$field_name;
                                }
                                $result['params'][':'.$field_name] = $rules[$field_name]['value'];
                                $i++;
                            }
                        }
                        break;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets table row.
     *
     * @param      $fields
     * @param      $tables
     * @param null $conds
     * @param null $params
     * @param bool $debug
     * @return array
     */
    public function rowSelectOne( $fields, $tables, $conds = NULL, $params = NULL, $debug = FALSE )
    {
        try {
            self::$pdo->beginTransaction();
            if( !empty($conds) && ( !empty($params) ) ) {
                if($debug) {
                    echo '<pre>';
                    var_dump(['F1','SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds]);
                    var_dump($params);
                    echo '</pre>';
                }
                
                $sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds);
                $sql->execute($params);
                if($debug) {
                    echo '<pre>';
                    var_dump(['X2',$sql]);
                    echo '</pre>';
                }
            } else {
                if($debug) {
                    echo '<pre>';
                    var_dump('SELECT '.$fields.' FROM '.$tables);
                    echo '</pre>';
                }
                
                $sql = self::$pdo->query('SELECT '.$fields.' FROM '.$tables);
                if($debug) {
                    echo '<pre>';
                    var_dump($sql);
                    echo '</pre>';
                }
            }
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            
            if($debug) {
                echo '<pre>';
                var_dump($row);
                echo '</pre>';
            }
            self::$pdo->commit();
            $sql = NULL;
            
            return $row;
        } catch ( \PDOException $pdo_err ) {
            self::$pdo->rollBack();
            $sql = NULL;
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
            
            return NULL;
        }
    }
    
    /**
     * Gets table row.
     *
     * @param      $fields
     * @param      $tables
     * @param null $conds
     * @param null $params
     * @param null $order
     * @param int  $limit
     * @param bool $debug
     *
     * @return array
     */
    public function rowSelectAll( $fields, $tables, $conds = NULL, $params = NULL, $order = NULL, $limit = 0, $debug = FALSE )
    {
        if($debug) {
            echo 'L3 debug: rowSelectAll<br>';
        }
        
        try {
            self::$pdo->beginTransaction();
            if( !empty($conds) && ( !empty($params) ) ) {
                $sql = self::$pdo->prepare(
                    'SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds.( ( !empty($order) ) ? ' ORDER BY '.$order : '' ).( ( $limit > 0 ) ? ' LIMIT '.$limit : '' ));
                if( $debug ) {
                    echo '<pre>';
                    echo 'SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds.( ( !empty($order) ) ? ' ORDER BY '.$order : '' ).( ( $limit > 0 ) ? ' LIMIT '.$limit : '' ).'<br>';
                    var_dump($params);
                    echo '</pre>';
    
                    echo '<pre>';
                    var_dump($params);
                    echo '</pre>';
                }
                $sql->execute($params);
            } else {
                $sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables.( ( !empty($order) ) ? ' ORDER BY '.$order : '' ).( ( $limit > 0 ) ? ' LIMIT '.$limit : '' ));
                if( $debug ) {
                    echo '<pre>';
                    echo 'SELECT '.$fields.' FROM '.$tables.( ( !empty($order) ) ? ' ORDER BY '.$order : '' ).( ( $limit > 0 ) ? ' LIMIT '.$limit : '' );
                    echo '</pre>';
                }
                $sql->execute();
            }
            
            if( $limit == 1 ) {
                $row = $sql->fetch(PDO::FETCH_ASSOC);
            } else {
                $row = $sql->fetchAll();
            }
            self::$pdo->commit();
            $sql = NULL;
            
            return $row;
        } catch ( \PDOException $pdo_err ) {
            self::$pdo->rollBack();
            $sql = NULL;
    
            if( $debug ) {
                echo '<pre>';
                echo 'SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds.( ( !empty($order) ) ? ' ORDER BY '.$order : '' ).( ( $limit > 0 ) ? ' LIMIT '.$limit : '' );
                echo '</pre>';
            }
            
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
            
            return FALSE;
        }
    }
    
    /**
     * Inserts table row.
     *
     * @param      $fields
     * @param      $tables
     * @param      $conds
     * @param      $params
     * @param bool $debug
     * @return integer
     */
    public function rowInsert( $fields, $tables, $conds, $params, $debug = FALSE )
    {
        try {
            self::$pdo->beginTransaction();
            if($debug) {
                echo 'INSERT INTO '.$tables.' ( '.$fields.' ) VALUES ( '.$conds.' )<br>';
            }
            $sql = self::$pdo->prepare('INSERT INTO '.$tables.' ( '.$fields.' ) VALUES ( '.$conds.' )');
            
            foreach( $params as $param => &$value ) {
                switch ( gettype($value) ) {
                    case 'integer':
                        $sql->bindParam($param, $value, PDO::PARAM_INT);
                        break;
                    case 'string':
                        $sql->bindParam($param, $value, PDO::PARAM_STR);
                        break;
                    case 'resource':
                        $sql->bindParam($param, $value, PDO::PARAM_LOB);
                        break;
                    default:
                        break;
                }
            }
            
            $sql->execute();
            $id = self::$pdo->lastInsertId();
            self::$pdo->commit();
            $sql = NULL;
            
            return $id;
        } catch ( \PDOException $pdo_err ) {
            self::$pdo->rollBack();
            $sql = NULL;
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
            
            return 0;
        }
    }
    
    /**
     * Updates table row.
     *
     * @param      $tables
     * @param      $fields
     * @param      $params
     * @param null $conds
     * @param bool $debug
     * @return boolean
     */
    public function rowUpdate( $tables, $fields, $params, $conds = NULL, $debug = FALSE )
    {
        if($debug) {
            echo '<pre>';
            echo '</pre>';
        }
        
        try {
            self::$pdo->beginTransaction();
            $whereSql = '';
            if( !empty($conds) && is_array($conds) ) {
                $whereSql .= ' WHERE ';
                $i        = 0;
                foreach( $conds as $key => $value ) {
                    $pre = ( $i > 0 ) ? ' AND ' : '';
                    if( is_numeric($value) ) {
                        $whereSql .= $pre.$key.' = '.$value;
                    } else {
                        $whereSql .= $pre.$key." = '".$value."'";
                    }
                    $i++;
                }
            }
            
            if($debug) {
                echo '<pre>';
                var_dump('UPDATE '.$tables.' SET '.$fields.$whereSql);
                echo '</pre>';
            }
            
            $sql = self::$pdo->prepare('UPDATE '.$tables.' SET '.$fields.$whereSql);
            foreach( $params as $param => &$value ) {
                switch ( gettype($value) ) {
                    case 'integer':
                        $sql->bindParam($param, $value, PDO::PARAM_INT);
                        break;
                    case 'string':
                        $sql->bindParam($param, $value, PDO::PARAM_STR);
                        break;
                    case 'resource':
                        $sql->bindParam($param, $value, PDO::PARAM_LOB);
                        break;
                    default:
                        break;
                }
            }
            $sql->execute($params);
            self::$pdo->commit();
            $sql = NULL;
            
            return TRUE;
        } catch ( \PDOException $pdo_err ) {
            self::$pdo->rollBack();
            $sql = NULL;
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
            
            return FALSE;
        }
    }
    
    public function rowUpdateUnsafe( $tables, $fields, $conds = NULL, $debug = FALSE )
    {
        if($debug) {
            echo '<pre>';
            echo '</pre>';
        }
        
        try {
            self::$pdo->beginTransaction();
            $whereSql = '';
            if( !empty($conds) ) {
                $whereSql .= ' WHERE ' . $conds;
            }
            
            if($debug) {
                echo '<pre>';
                var_dump('UPDATE '.$tables.' SET '.$fields.$whereSql);
                echo '</pre>';              
            }
            
            $sql = self::$pdo->prepare('UPDATE '.$tables.' SET '.$fields.$whereSql);

            $sql->execute();
            self::$pdo->commit();
            $sql = NULL;
            
            return TRUE;
        } catch ( \PDOException $pdo_err ) {
            self::$pdo->rollBack();
            $sql = NULL;
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
            
            return FALSE;
        }
    }    
    
    /**
     * Deletes table row.
     *
     * @param      $tables
     * @param null $conds
     * @param null $params
     * @return integer
     */
    public function rowDelete( $tables, $conds = NULL, $params = NULL, $debug = FALSE )
    {
        try {
            self::$pdo->beginTransaction();
            if( !empty($conds) && ( !empty($params) ) ) {
                if($debug) {
                    echo 'Cond+Params '.'DELETE FROM '.$tables.' WHERE '.$conds.'<br/>';
                    echo '<pre>';
                    var_dump($params);
                    echo '</pre>';
                }
                $sql = self::$pdo->prepare('DELETE FROM '.$tables.' WHERE '.$conds);
                $sql->execute($params);
            } else {
                if($debug) {
                    echo 'Default '.'DELETE FROM '.$tables.'<br>';
                }
                $sql = self::$pdo->prepare('DELETE FROM '.$tables);
                $sql->execute();
            }
            self::$pdo->commit();
        } catch ( \PDOException $pdo_err ) {
            self::$pdo->rollBack();
            echo nl2br('Error MySQL: '.$pdo_err->getMessage()."\n");
        }
        $rows = $sql->rowCount();
        $sql  = NULL;
        
        return $rows;
    }
    
    /**
     * Gets string HASH.
     *
     * @param $str
     * @return string
     */
    public function getHash( $str )
    {
        return password_hash($str, PASSWORD_DEFAULT);
    }
    
    /**
     * Verifies string to HASH.
     *
     * @param $pwd
     * @param $hash
     * @return boolean
     */
    public function checkHash( $pwd, $hash )
    {
        return password_verify($pwd, $hash);
    }
}
