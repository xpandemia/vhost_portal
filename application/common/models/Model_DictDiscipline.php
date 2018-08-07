<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictDiscipline extends Db_Helper
{
	/*
		Dictionary discipline processing
	*/

	const TABLE_NAME = 'dict_discipline';

	public $id;
	public $code;
	public $discipline_name;
	public $campaign_code;
	public $parent_code;
	public $archive;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary discipline rules.
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
				'code' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->code
							],
				'discipline_name' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->discipline_name
									],
				'campaign_code' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->campaign_code
									],
				'parent_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->parent_code
								],
				'archive' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->archive
							]
				];
	}

	/**
     * Gets all disciplines.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets unique disciplines.
     *
     * @return array
     */
	public function getUnique()
	{
		return $this->rowSelectAll('DISTINCT code, discipline_name', self::TABLE_NAME);
	}

	/**
     * Gets one discipline.
     *
     * @return array
     */
	public function getOne()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'code = :code AND campaign_code = :campaign_code',
								[':code' => $this->code,
								':campaign_code' => $this->campaign_code]);
	}

	/**
     * Gets discipline discipline_name by code.
     *
     * @return array
     */
	public function getDescriptionByCode()
	{
		return $this->rowSelectOne('DISTINCT discipline_name',
									self::TABLE_NAME,
									'code = :code',
									[':code' => $this->code]);
	}

	/**
     * Gets disciplines by admission campaign.
     *
     * @return array
     */
	public function getByCampaign()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'campaign_code = :campaign_code',
									[':campaign_code' => $this->campaign_code]);
	}

	/**
     * Saves discipline data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Removes all disciplines.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads disciplines.
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
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено направлений подготовки - '.$rows_del.'.';
				$log->value_old = null;
				$log->value_new = null;
				$log->save();
			} else {
				$rows_del = 0;
			}
		$rows_ins = 0;
		$rows_upd = 0;
		foreach ($Predmet as $string_predmet) {
            $this->code = (string)$string_predmet->Code;
            $this->discipline_name = (string)$string_predmet->Name;
            $this->campaign_code = (string)$string_predmet->IdPK;
            if (isset($string_predmet->ParentPredmet) && strlen((string)$string_predmet->ParentPredmet) > 0) {
                $this->parent_code = (string)$string_predmet->ParentPredmet;
            } else {
				$this->parent_code = null;
			}
            $disc = $this->getOne();
            if ($disc == null) {
				if ($this->save()) {
					$log->msg = 'Создана дисциплина с кодом "'.$this->code.'" для приемной кампании "'.$this->campaign_code.'".';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$rows_ins++;
				} else {
					$result['error_msg'] = 'Ошибка при сохранении дисциплины с кодом " '.$this->code.'" для приемной кампании "'.$this->campaign_code.'"!';
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
