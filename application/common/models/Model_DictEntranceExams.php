<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictEntranceExams extends Db_Helper
{
	/*
		Dictionary entrance exams processing
	*/

	const TABLE_NAME = 'dict_entrance_exams';

	public $campaign_code;
	public $group_code;
	public $exam_form_name;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets entrance exams by campaign/group.
     *
     * @return array
     */
	public function getByCampaignGroup()
	{
		return $this->rowSelectAll('DISTINCT exam_code, exam_name',
									self::TABLE_NAME,
									'campaign_code = :campaign_code AND group_code = :group_code AND exam_form_name in (:exam_form_name1, :exam_form_name2, :exam_form_name3) AND exam_code not in (:exam_code1, :exam_code2)',
									[':campaign_code' => $this->campaign_code,
									':group_code' => $this->group_code,
									':exam_form_name1' => 'ЕГЭ',
									':exam_form_name2' => 'Экзамен',
									':exam_form_name3' => 'Тестирование',
									':exam_code1' => '000000015',
									':exam_code2' => '000000021']);
	}
}
