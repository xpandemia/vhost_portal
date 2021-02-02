<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictIndAchievs extends Db_Helper
{
	/*
		Dictionary individual achievments processing
	*/

	const TABLE_NAME = 'dict_ind_achievs';

	public $id;
	public $code;
	public $description;
	public $numb;
	public $abbr;
	public $confirm;
	public $guid;
	public $archive;

	const IA_BSU = [
        '000000002',
        '000000003',
        '000000004',
        '000000005',
        '000000010',
        '000000017',
        '000000022',
        '000000023',
        '000000028',
        '000000030',
        '000000031',
        '000000032',
        '000000035',
        '000000036',
        '000000037',
        '000000038',
        '000000045',
        '000000050',
        '000000057',
        '000000058',
        '000000059',
        '000000060',
        '000000061',
        '000000063',
        '000000064',
        '000000065',
        '000000066',
        '000000067',
        '000000068',
        '000000069',
        '000000070',
        '000000071',
        '000000072',
        '000000073',
        '000000074',
        '000000075',
        '000000076',
        '000000077',
        '000000078',
        '000000079',
        '000000080',
        '000000081',
        '000000082',
        '000000083',
        '000000084',
        '000000085',
        '000000086',
        '000000087',
        '000000088',
        '000000089',
        '000000090',
        '000000091',
        '000000092',
        '000000093',
        '000000094',
        '000000095',
        '000000096',
        '000000097',
        '000000098',
        '000000099',
        '000000100',
        '000000101',
        '000000102',
        '000000103',
        '000000104',
        '000000105',
        '000000106',
        '000000107',
        '000000108',
        '000000109',
        '000000110',
        '000000111',
        '000000112',
        '000000113',
        '000000114',
        '000000115',
        '000000116',
        '000000117',
        '000000118',
        '000000119',
        '000000120',
        '000000121',
        '000000122',
        '000000123',
        '000000124',
        '000000125',
        '000000126',
        '000000127',
        '000000128',
        '000000129',
        '000000130',
        '000000131',
        '000000132',
        '000000133',
        '000000134',
        '000000135',
        '000000136',
        '000000137',
        '000000137',
        '000000138',
        '000000139',
        '000000140',
        '000000141',
        '000000142',
        '000000143',
        '000000144',
        '000000145',
        '000000146',
        '000000147',
        '000000148',
        '000000149',
        '000000150',
        '000000151',
        '000000152',
        '000000153',
        '000000154',
        '000000155',
        '000000156',
        '000000157',
        '000000158',
        '000000159',
        '000000160',
        '000000161',
        '000000162',
        '000000163',
        '000000164',
        '000000165',
        '000000166',
        '000000167',
        '000000168',
        '000000169',
        '000000170',
        '000000171',
        '000000172',
        '000000173',
        '000000174',
        '000000175',
        '000000176',
        '000000177',
        '000000178',
        '000000179',
        '000000180',
        '000000181',
        '000000182',
        '000000183',
        '000000184',
        '000000185',
        '000000186',
        '000000187',
        '000000188',
        '000000189',
        '000000190',
        '000000191',
        '000000192',
        '000000193',
        '000000194',
        '000000195',
        '000000196',
        '000000197',
        '000000198',
        '000000199',
        '000000200',
        '000000201',
        '000000202',
        '000000203',
        '000000204',
        '000000205',
        '000000206',
        '000000207',
        '000000208',
        '000000209',
        '000000210',
        '000000211',
        '000000212',
        '000000213',
        '000000214',
        '000000215',
        '000000216',
        '000000217',
        '000000221',
        '000000222',
        '000000223',
        '000000224',
        '000000225',
        '000000226',
        '000000227',
        '000000228',
        '000000229',
        '000000230',
        '000000231',
        '000000232',
        '000000233',
        '000000234',
        '000000235',
        '000000236',
        '000000237',
        '000000238',
        '000000239',
        '000000240',
        '000000241',
        '000000242',
        '000000243',
        '000000244',
        '000000245',
        '000000246',
        '000001063',
        '000001064',
        '000001065',
        '000001066',
        '000001067',
        '000001068',
        '000001069',
        '000001070',
        '000001071',
        '000001072',
        '000001073',
        '000001074',
        '000001075',
        '000001076',
        '000001077',
        '000001078',
        '000001079',
        '000001080',
        '000001081',
        '000001082',
        '000001083',
        '000001084',
        '000001085',
        '000001086',
        '000001087',
        '000001088',
        '000001089',
        '000001090',
        '000001091',
        '000001092',
        '000001093',
        '000001094',
        '000001095',
        '000001096',
        '000001097',
        '000001098',
        '000001099',
        '000001100',
        '000001101',
        '000001102',
        '000001103',
        '000001104',
        '000001105',
        '000001106',
        '000001107',
        '000001108',
        '000001109',
        '000001110',
        '000001111',
        '000001112',
        '000001113',
        '000001114',
        '000001115',
        '000001116',
        '000001117',
        '000001118',
        '000001119',
        '000001120',
        '000001121',
        '000001122',
        '000001123',
        '000001124',
        '000001125',
        '000001126',
        '000001127',
        '000001128',
        '000001129',
        '000001130',
        '000001131',
        '000001132',
        '000001133',
        '000001134',
        '000001135',
        '000001136'
    ];

	const IA_CATEGORIES = [
	    "Все" => self::IA_BSU,
    ];

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}
    
    public function getMagPubAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'МАГ_Публикации_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspPerfectDiplomaAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_ДО_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspDOAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_ДО_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspMaterialAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_Материалы_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspWOSorScopusAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_ Web of Science или Scopus_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspWAKAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_ ВАК_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspRINCAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_ РИНЦ_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspTrainerNoteAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_Отзыв НР_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspGEKAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_ГЭК_%' and :t = :t", [':t' => 0]);
    }
    
    public function getAspGrantAchives()
    {
        return $this->rowSelectAll('code, abbr', self::TABLE_NAME, "abbr like 'АСП_Грант, патент, свидетельство_%' and :t = :t", [':t' => 0]);
    }
    
    /**
     * Individual achievments rules.
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
				'description' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->description
								],
				'numb' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'abbr' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->abbr
							],
				'confirm' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->confirm
							],
				'guid' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->guid
							],
				'archive' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->archive
							]
				];
	}

	/**
     * Gets all individual achievments.
     *
     * @return array
     */
	public function getAll()
	{
	    $records = $this->rowSelectAll('*',
            self::TABLE_NAME,
            'archive = :archive',
            [':archive' => 0],
            'description');
	    $resoult = [];
	    foreach (self::IA_CATEGORIES as $IA_CATEGORY_NAME => $IA_CATEGORY_CODES) {
	        $resoult[$IA_CATEGORY_NAME] = [];
	        foreach ($records as $record) {
	            if(in_array($record['code'], $IA_CATEGORY_CODES)) {
                    $resoult[$IA_CATEGORY_NAME][] = $record;
                }
            }
        }
		return $resoult;
	}
	
	public function getAllFilteredByUser()
    {
        $records = $this->rowSelectAll('*',
                                       self::TABLE_NAME,
                                       'archive = :archive',
                                       [':archive' => 0],
                                       'description');
        
        $user_ias = $this->rowSelectAll('DISTINCT id_achiev', Model_IndAchievs::TABLE_NAME, 'id_user = :id_user', [':id_user' => $_SESSION[APP_CODE]['user_id']]);
        
        $user_ias_ids = [];
        
        foreach($user_ias as $user_ia) {
            $user_ias_ids[] = $user_ia['id_achiev'];
        }
        
        $unset_indexes = [];
        foreach($records as $index=>$record) {
            if(in_array($record['id'], $user_ias_ids)) {
                $unset_indexes[] = $index;
            }
        }
        
        foreach($unset_indexes as $unsetIndex) {
            unset($records[$unsetIndex]);
        }
        
        $resoult = [];
        foreach (self::IA_CATEGORIES as $IA_CATEGORY_NAME => $IA_CATEGORY_CODES) {
            $resoult[$IA_CATEGORY_NAME] = [];
            foreach ($records as $record) {
                if(in_array($record['code'], $IA_CATEGORY_CODES)) {
                    $resoult[$IA_CATEGORY_NAME][] = $record;
                }
            }
        }
        return $resoult;
    }

	/**
     * Gets individual achievment by GUID.
     *
     * @return array
     */
	public function getByGuid()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'guid = :guid',
									[':guid' => $this->guid]);
	}

	/**
     * Gets individual achievment by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'code = :code',
									[':code' => $this->code]);
	}

	/**
     * Saves individual achievment data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes individual achievment code.
     *
     * @return boolean
     */
	public function changeCode()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'code = :code',
								[':code' => $this->code],
								['id' => $this->id]);
	}

	/**
     * Changes individual achievment description.
     *
     * @return boolean
     */
	public function changeDescription()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'description = :description',
								[':description' => $this->description],
								['id' => $this->id]);
	}

	/**
     * Changes individual achievment numb.
     *
     * @return boolean
     */
	public function changeNumb()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'numb = :numb',
								[':numb' => $this->numb],
								['id' => $this->id]);
	}

	/**
     * Changes individual achievment abbr.
     *
     * @return boolean
     */
	public function changeAbbr()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'abbr = :abbr',
								[':abbr' => $this->abbr],
								['id' => $this->id]);
	}

	/**
     * Changes individual achievment confirm.
     *
     * @return boolean
     */
	public function changeConfirm()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'confirm = :confirm',
								[':confirm' => $this->confirm],
								['id' => $this->id]);
	}

	/**
     * Removes all individual achievments.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads individual achievments.
     *
     * @return array
     */
	public function load($properties, $id_dict, $dict_name, $clear_load)
	{
		$result['success_msg'] = null;
		$result['error_msg'] = null;
		$log = new Model_DictionaryManagerLog();
		$log->id_dict = $id_dict;
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено индивидуальных достижений - '.$rows_del.'.';
				$log->value_old = null;
				$log->value_new = null;
				$log->save();
			} else {
				$rows_del = 0;
			}
		if(sizeof($properties) == 0) {
			$result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
		$rows_ins = 0;
		$rows_upd = 0;
		foreach($properties as $property) {
			$this->guid = (string)$property->Ref_Key;
			$ia = $this->getByGuid();

            if($property->DeletionMark == 'false') {
				$this->code = (string)$property->Code;
				$this->description = (string)$property->Description;
				$this->numb = (string)$property->НомерИД;
				$this->abbr = (string)$property->СокращенноеНаименование;
				$this->confirm = ((string)$property->ТребуетсяПодтверждающийДокумент == 'false') ? 0 : 1;
				$this->archive = (in_array($this->code, self::IA_BSU)) ? 0 : 1;
					if ($ia == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создано новое индивидуальное достижение с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении индивидуального достижения с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $ia['id'];
						// code
						if ($ia['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($ia['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// numb
						if ($ia['numb'] != $this->numb) {
							if ($this->changeNumb()) {
								$log->msg = 'Изменён номер ИД индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['numb'];
								$log->value_new = $this->numb;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении номера ИД индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// abbr
						if ($ia['abbr'] != $this->abbr) {
							if ($this->changeAbbr()) {
								$log->msg = 'Изменено сокращённое наименование индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['abbr'];
								$log->value_new = $this->abbr;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении сокращённого наименования индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// confirm
						if ($ia['confirm'] != $this->confirm) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак подтверждающего документа индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['confirm'];
								$log->value_new = $this->confirm;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака подтверждающего документа индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// counter
						if ($upd == 1) {
							$rows_upd++;
						}
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
