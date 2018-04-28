<?php

namespace tinyframe\core\helpers;

use PDO;

class Db_Helper
{
	// Database processing *Singleton*

	public static $pdo;

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
								array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	    } catch(\PDOException $pdo_err) {
	        echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
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
        static $instance = null;
        if (null === $instance) {
            $instance = new static;
        }
        return $instance;
    }

    /**
     * Prepares insert query.
     *
     * @return array
     */
	public function prepareInsert($table_name, $rules)
	{
		$result['fields'] = '';
		$result['conds'] = '';
		$result['params'] = [];
		$i = 1;
		foreach ($rules as $field_name => $rule_name_arr) {
			foreach ($rule_name_arr as $rule_name => $rule_var) {
				switch ($rule_name) {
					case 'insert':
						if ($rule_var == 1) {
							if ($rules[$field_name]['required'] == 1) {
								if (!empty($rules[$field_name]['value']) || $rules[$field_name]['value'] == 0) {
									if ($i == 1) {
										$result['fields'] = $field_name;
										$result['conds'] = ':'.$field_name;
									} else {
										$result['fields'] .= ', '.$field_name;
										$result['conds'] .= ', :'.$field_name;
									}
									$result['params'][':'.$field_name] = $rules[$field_name]['value'];
								} else {
									throw new \InvalidArgumentException('Поле '.$field_name.' таблицы '.$table_name.' обязательно для заполнения!');
								}
							} else {
								if (!empty($rules[$field_name]['value'])) {
									if ($i == 1) {
										$result['fields'] = $field_name;
										$result['conds'] = ':'.$field_name;
									} else {
										$result['fields'] .= ', '.$field_name;
										$result['conds'] .= ', :'.$field_name;
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
     * @return array
     */
	public function prepareUpdate($table_name, $rules)
	{
		$result['fields'] = '';
		$result['params'] = [];
		$i = 1;
		foreach ($rules as $field_name => $rule_name_arr) {
			foreach ($rule_name_arr as $rule_name => $rule_var) {
				switch ($rule_name) {
					case 'update':
						if ($rule_var == 1) {
							if ($rules[$field_name]['required'] == 1 && empty($rules[$field_name]['value'])) {
								throw new \InvalidArgumentException('Поле '.$field_name.' таблицы '.$table_name.' обязательно для заполнения!');
							} else {
								if ($i == 1) {
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
     * @return array
     */
	public function rowSelectOne($fields, $tables, $conds = null, $params = null)
	{
		try {
			self::$pdo->beginTransaction();
			if (!empty($conds) && (!empty($params))) {
				$sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds);
				$sql->execute($params);
			} else {
				$sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables);
				$sql->execute();
			}
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			self::$pdo->commit();
		    $sql = null;
		    return $row;
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
			return null;
		}
	}

	/**
     * Gets table row.
     *
     * @return array
     */
	public function rowSelectAll($fields, $tables, $conds = null, $params = null)
	{
		try {
			self::$pdo->beginTransaction();
			if (!empty($conds) && (!empty($params))) {
				$sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds);
				$sql->execute($params);
			} else {
				$sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables);
				$sql->execute();
			}
			$row = $sql->fetchAll();
			self::$pdo->commit();
		    $sql = null;
		    return $row;
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
			return null;
		}
	}

	/**
     * Inserts table row.
     *
     * @return integer
     */
	public function rowInsert($fields, $tables, $conds, $params)
	{
		try {
			self::$pdo->beginTransaction();
			$sql = self::$pdo->prepare('INSERT INTO '.$tables.' ( '.$fields.' ) VALUES ( '.$conds.' )');
			foreach ($params as $param => &$value) {
				switch (gettype($value)) {
					case 'integer':
						$sql->bindParam($param, $value, PDO::PARAM_INT);
						break;
					case 'string':
						$sql->bindParam($param, $value, PDO::PARAM_STR);
						break;
					case 'resource':
						$sql->bindParam($param, $value, PDO::PARAM_LOB);
						break;
				}
			}
			$sql->execute();
			$id = self::$pdo->lastInsertId();
			self::$pdo->commit();
			$sql = null;
			return $id;
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
		    return 0;
		}
	}

	/**
     * Updates table row.
     *
     * @return boolean
     */
	public function rowUpdate($tables, $fields, $params, $conds = null)
	{
		try {
			self::$pdo->beginTransaction();
			$whereSql = '';
			if (!empty($conds) && is_array($conds)) {
				$whereSql .= ' WHERE ';
                $i = 0;
                foreach($conds as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    if (is_numeric($value)) {
						$whereSql .= $pre.$key." = ".$value;
					} else {
						$whereSql .= $pre.$key." = '".$value."'";
					}
                    $i++;
                }
			}
			$sql = self::$pdo->prepare('UPDATE '.$tables.' SET '.$fields.$whereSql);
			$sql->execute($params);
		    self::$pdo->commit();
			$sql = null;
		    return true;
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
			return false;
		}
	}

	/**
     * Deletes table row.
     *
     * @return integer
     */
	public function rowDelete($tables, $conds = null, $params = null)
	{
		try {
			self::$pdo->beginTransaction();
			if (!empty($conds) && (!empty($params))) {
				$sql = self::$pdo->prepare('DELETE FROM '.$tables.' WHERE '.$conds);
				$sql->execute($params);
			} else {
				$sql = self::$pdo->prepare('DELETE FROM '.$tables);
				$sql->execute();
			}
		    self::$pdo->commit();
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
		}
		$rows = $sql->rowCount();
		$sql = null;
		return $rows;
	}

	/**
     * Gets string HASH.
     *
     * @return string
     */
	public function getHash($str)
	{
		return password_hash($str, PASSWORD_DEFAULT);
	}

	/**
     * Verifies string to HASH.
     *
     * @return boolean
     */
	public function checkHash($pwd, $hash)
	{
		return password_verify($pwd, $hash);
	}
}
