<?php

namespace tinyframe\core\helpers;

use PDO;

class Db_Helper
{
	/*
		Database processing
	*/

	public static $pdo;

	public function __construct()
	{
		try {
	        self::$pdo = new PDO('mysql:host='.DB_HOST.';charset=utf8;dbname='.DB_NAME, DB_USER, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
	    } catch(PDOException $pdo_err) {
	        echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
	        exit;
	    }
	}

	/**
     * Gets table row.
     *
     * @return array
     */
	public function rowSelect($fields, $tables, $conds, $params)
	{
		try {
			self::$pdo->beginTransaction();
			$sql = self::$pdo->prepare('SELECT '.$fields.' FROM '.$tables.' WHERE '.$conds);
		    $sql->execute($params);
		    $row = $sql->fetch(PDO::FETCH_ASSOC);
		    self::$pdo->commit();
		    $sql = null;
		    return $row;
		} catch(PDOException $pdo_err) {
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
		} catch(PDOException $pdo_err) {
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
		} catch(PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
			return FALSE;
		}
	}

	/**
     * Deletes table row.
     *
     * @return boolean
     */
	public function rowDelete($tables, $conds, $params)
	{
		try {
			self::$pdo->beginTransaction();
			$sql = self::$pdo->prepare('DELETE FROM '.$tables.' WHERE '.$conds);
		    $sql->execute($params);
		    self::$pdo->commit();
			$sql = null;
		    return TRUE;
		} catch(PDOException $pdo_err) {
			self::$pdo->rollBack();
			$sql = null;
			echo nl2br("Error MySQL: ".$pdo_err->getMessage()."\n");
			return FALSE;
		}
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

	public function __destruct()
	{
		//self::$pdo = null;
	}
}
