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
     * @return boolean
     */
	public function rowInsert($fields, $tables, $conds, $params)
	{
		try {
			self::$pdo->beginTransaction();
			$sql = self::$pdo->prepare('INSERT INTO '.$tables.' ( '.$fields.' ) VALUES ( '.$conds.' )');
		    $sql->execute($params);
		    self::$pdo->commit();
			$sql = null;
			return TRUE;
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
		    return FALSE;
		}
	}

	/**
     * Updates table row.
     *
     * @return boolean
     */
	public function rowUpdate($tables, $fields, $params)
	{
		try {
			self::$pdo->beginTransaction();
			$sql = self::$pdo->prepare('UPDATE '.$tables.' SET '.$fields);
		    $sql->execute($params);
		    self::$pdo->commit();
			$sql = null;
		    return TRUE;
		} catch(\PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
			return FALSE;
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
