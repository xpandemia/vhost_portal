<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictIndAchievsAc extends Db_Helper
{
	/*
		Dictionary individual achievments by admission campaigns processing
	*/

	const TABLE_NAME = 'dict_ind_achievs_ac';

	public $id;
	public $achiev_code;
	public $achiev_name;
	public $campaign_code;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * IAbyAC rules.
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
				'achiev_code' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->achiev_code
								],
				'achiev_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->achiev_name
								],
				'campaign_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->campaign_code
									]
				];
	}

	/**
     * Gets all IAbyAC.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets one IAbyAC.
     *
     * @return array
     */
	public function getOne()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'achiev_code = :achiev_code AND campaign_code = :campaign_code',
								[':achiev_code' => $this->achiev_code,
								':campaign_code' => $this->campaign_code]);
	}

	/**
     * Gets individual achievments by admission campaign.
     *
     * @return array
     */
	public function getByCampaign()
	{
		return $this->rowSelectAll('achiev_code, achiev_name',
									self::TABLE_NAME,
									'campaign_code = :campaign_code',
									[':campaign_code' => $this->campaign_code]);
	}

	/**
     * Saves IAbyAC data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all IAbyAC data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME,
								$prepare['fields'],
								$prepare['params'],
								['code' => $this->code]);
	}

	/**
     * Removes all IAbyAC.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads IAbyAC.
     *
     * @return array
     */
	public function load($xml, $id_dict, $dict_name, $clear_load)
	{
		$result['success_msg'] = null;
		$result['error_msg'] = null;
		if ($xml === false || !isset($xml->return->Predmet)) {
            $result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
        $Predmet = $xml->return->Predmet;
        if (!is_array($Predmet)) {
            $Predmet = [$Predmet];
        }
		if(sizeof($Predmet) == 0) {
			$result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
        $log = new Model_DictionaryManagerLog();
		$log->id_dict = $id_dict;
		$log->id_user = $_SESSION[APP_CODE]['user_id'];
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено индивидуальных достижений по приёмным кампаниям - '.$rows_del.'.';
				$log->value_old = null;
				$log->value_new = null;
				$log->save();
			} else {
				$rows_del = 0;
			}
		$rows_ins = 0;
		$rows_upd = 0;
		foreach ($Predmet as $string_predmet) {
            $this->achiev_code = (string)$string_predmet->Code;
            $this->achiev_name = (string)$string_predmet->Name;
            $this->campaign_code = (string)$string_predmet->IdPK;
            $ia = $this->getOne();
            if ($ia == null) {
				if ($this->save()) {
					$log->msg = 'Создано индивидуальное достижение с кодом "'.$this->achiev_code.'" по приёмной кампании с кодом "'.$this->campaign_code.'".';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$rows_ins++;
				} else {
					$result['error_msg'] = 'Ошибка при создании индивидуального достижения с кодом "'.$this->achiev_code.'" по приёмной кампании с кодом "'.$this->campaign_code.'"!';
					return $result;
				}
			} else {
				// update
				$upd = 0;
				if ($this->changeAll()) {
					$log->msg = 'Изменено индивидуальное достижение с кодом "'.$this->achiev_code.'" по приёмной кампании с кодом "'.$this->campaign_code.'".';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$upd = 1;
				} else {
					$result['error_msg'] = 'Ошибка при изменении индивидуального достижения с кодом "'.$this->achiev_code.'" по приёмной кампании с кодом "'.$this->campaign_code.'"!';
					return $result;
				}
			}
        }
        if ($rows_del == 0 && $rows_ins == 0 && $rows_upd == 0) {
			$result['success_msg'] = 'Справочник "'.$dict_name.'" не нуждается в обновлении.';
		} else {
			$result['success_msg'] = nl2br("В справочнике \"$dict_name\":\n----- удалено записей - $rows_del\n----- добавлено записей - $rows_ins\n----- обновлено записей - $rows_upd");
		}
        return $result;
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
