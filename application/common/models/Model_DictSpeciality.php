<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use \Datetime;

class Model_DictSpeciality extends Db_Helper
{
    /*
        Dictionary speciality processing
    */

	const INPUT_FORMAT = 'd.m.Y H:i:s';
	const OUTPUT_FORMAT = 'Y-m-d H:i:s';
    const TABLE_NAME = 'dict_speciality';

    public $id;
    public $campaign_code;
    public $campaign_dt;
    public $stage_numb;
    public $stage_dt_begin;
    public $stage_dt_end;
    public $stage_dt_order;
    public $curriculum_code;
    public $curriculum_name;    
    public $faculty_code;
    public $faculty_name;
    public $speciality_code;
    public $speciality_name;
    public $speciality_human_code;
    public $profil_code;
    public $profil_name;
    public $edulevel_code;
    public $edulevel_name;
    public $eduform_code;
    public $eduform_name;
    public $eduprogram_code;
    public $eduprogram_name;
    public $finance_code;
    public $finance_name;
    public $group_code;
    public $group_name;
    public $group_beneficiary;
    public $detail_group_code;
    public $detail_group_name;    
    public $receipt_allowed;
    public $archive;

    public $db;

    public function __construct()
    {
        $this->db = Db_Helper::getInstance();
    }

    /**
     * Dictionary speciality rules.
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
            'campaign_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->campaign_code            
            ],
            'campaign_dt' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->campaign_dt            
            ],
            'stage_numb' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->stage_numb            
            ],                                          
            'stage_dt_begin' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->stage_dt_begin            
            ],
            'stage_dt_end' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->stage_dt_end            
            ],
            'stage_dt_order' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->stage_dt_order            
            ],                                    
            'curriculum_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->curriculum_code            
            ],
            'curriculum_name' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->curriculum_name            
            ],            
            'faculty_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->faculty_code
            ],
            'faculty_name' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->faculty_name
            ],
            'speciality_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->speciality_code
            ],
            'speciality_name' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->speciality_name
            ],
            'speciality_human_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->speciality_human_code
            ],            
            'profil_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->profil_code
            ],
            'profil_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->profil_name
            ],
            'edulevel_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->edulevel_code
            ],
            'edulevel_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->edulevel_name
            ],
            'eduform_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->eduform_code
            ],
            'eduform_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->eduform_name
            ],
            'eduprogram_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->eduprogram_code
            ],
            'eduprogram_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->eduprogram_name
            ],
            'finance_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->finance_code
            ],
            'finance_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->finance_name
            ],
            'group_code' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->group_code
            ],
            'group_name' => [
                'required' => 1,
                'insert' => 1,
                'update' => 1,
                'value' => $this->group_name
            ],
            'group_beneficiary' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->group_beneficiary
            ],
            'detail_group_name' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->detail_group_name
            ],
            'detail_group_code' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->detail_group_code
            ],
            'receipt_allowed' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->receipt_allowed
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
     * Gets all specialities.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->rowSelectAll('*',
            self::TABLE_NAME,
            null,
            null,
            'speciality_name, profil_name');
    }

    /**
     * Gets all specialities of the first education.
     *
     * @return array
     */
    public function getAllFirst()
    {
        return $this->rowSelectAll('*',
            self::TABLE_NAME,
            'eduprogram_name is null',
            null,
            'speciality_name, profil_name');
    }

    /**
     * Gets all specialities of the second education.
     *
     * @return array
     */
    public function getAllSecond()
    {
        return $this->rowSelectAll('*',
            self::TABLE_NAME,
            'eduprogram_name = :eduprogram_name',
            [':eduprogram_name' => 'Высшее'],
            'speciality_name, profil_name');
    }

    /**
     * Gets one speciality.
     *
     * @return array
     */
    public function getOne()
    {
        return $this->rowSelectOne('*',
            self::TABLE_NAME,
            'group_code = :group_code AND campaign_code = :campaign_code AND IFNULL(stage_numb, 0) = :stage_numb',
            [':group_code' => $this->group_code,
            	':campaign_code' => $this->campaign_code,
            	':stage_numb' => $this->stage_numb]);                
    }

    /**
     * Gets speciality by ID.
     *
     * @return array
     */
    public function getById($debug = false)
    {
        return $this->rowSelectOne('*',
            self::TABLE_NAME,
            'id = :id',
            [':id' => $this->id], $debug);
    }

    /**
     * Saves speciality data to database.
     *
     * @return integer
     */
    public function save()
    {
        $prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        
        return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
    }

    /**
     * Changes all speciality data.
     *
     * @return boolean
     */
    public function changeAll()
    {
        $prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
        return $this->rowUpdate(self::TABLE_NAME,
            $prepare['fields'],
            $prepare['params'],
            ['group_code' => $this->group_code,
                'stage_numb' => $this->stage_numb,
                'campaign_code' => $this->campaign_code]);
    }

    /**
     * Removes all specialities.
     *
     * @return integer
     */
    public function clearAll()
    {
        return $this->rowDelete(self::TABLE_NAME);
    }


    public function _load($ignoreA, $igrnoreB, $ignoreC, $ignoreD)
    {

    }

    /**
     * Loads specialities.
     *
     * @return array
     */
    public function /*loadOld*/ load($xml, $id_dict, $dict_name, $clear_load)
    {    
        $result['success_msg'] = null;
        $result['error_msg'] = null;

        if ($xml === false || !isset($xml->return->StringsPlanNabora) || !isset($xml->return->StringsPlanNabora->StringPlanNabora)) {
            $result['error_msg'] = 'Не удалось получить данные справочника "' . $dict_name . '"!';
            return $result;
        }
        if (isset($result->return->UniversalResponse->Complete) && $result->return->UniversalResponse->Complete == "0") {
            $result['error_msg'] = 'Ошибка при выполнении метода GetStringsPriema: ' . $xml->return->UniversalResponse->Description;
            return $result;
        }
        $StringPlanPriema = $xml->return->StringsPlanNabora->StringPlanNabora;
        if (!is_array($StringPlanPriema)) {
            $StringPlanPriema = [$StringPlanPriema];
        }
        if (sizeof($StringPlanPriema) == 0) {
            $result['error_msg'] = 'Не удалось получить данные справочника "' . $dict_name . '"!';
            return $result;
        }
        
		$campaign_year = NULL;		
        foreach ($StringPlanPriema as $string_priema) {
            	$dtResult = $this->formatDateForSQL($string_priema->PKYear);
				if (!$dtResult){
                	$result['error_msg'] = 'Ошибка при парсинге даты приемной кампании ' . $string_priema->PKYear;
                	return $result;						
				}
				else{
					$string_priema->PKYear = $dtResult;
				} 
				
				$dtResult = $this->formatDateForSQL($string_priema->StepDateBegin);
				if (!$dtResult){
                	$result['error_msg'] = 'Ошибка при парсинге даты начала этапа приемной кампании ' . $string_priema->StepDateBegin;
                	return $result;						
				}
				else{
					$string_priema->StepDateBegin = $dtResult;
				}
				
				$dtResult = $this->formatDateForSQL($string_priema->StepDateEnd);
				if (!$dtResult){
                	$result['error_msg'] = 'Ошибка при парсинге даты окончания этапа приемной кампании ' . $string_priema->StepDateEnd;
                	return $result;						
				}
				else{
					$string_priema->StepDateEnd = $dtResult;
				}
				
				$dtResult = $this->formatDateForSQL($string_priema->OrderDate);
				if (!$dtResult){
                	$result['error_msg'] = 'Ошибка при парсинге даты приказа приемной кампании ' . $string_priema->OrderDate;
                	return $result;						
				}
				else{
					$string_priema->OrderDate = $dtResult;
				}
				
	        	if (!$campaign_year){
	        		$campaign_year = substr($string_priema->PKCode, 0, 2);
	        		if (strlen($campaign_year) !== 2 || !is_numeric($campaign_year)){
	        			$result['error_msg'] = 'Ошибка определение года приемной кампании ' . $string_priema->PKCode;
	        			return $result;
	        		}
	        	}				
				
				/*echo '<nobr>';
                echo
                    (string)$string_priema->GroupCode . ';
                ' . (string)$string_priema->GroupName . ';
                ' . (string)$string_priema->StepNumber;
                echo '</nobr><br />';*/
        }
        //die();

        $log = new Model_DictionaryManagerLog();
        $log->id_dict = $id_dict;
        $log->id_user = $_SESSION[APP_CODE]['user_id'];
        if ($clear_load == 1) {
            // clear
            $rows_del = $this->$clear_load();
            $log->msg = 'Удалено конкурсных групп - ' . $rows_del . '.';
            $log->value_old = null;
            $log->value_new = null;
            $log->save();
        } else {
            $rows_del = 0;
        }
        $rows_ins = 0;
        $rows_upd = 0;
        $rows_arch = 0;

		$res = $this->archiveCampaign($campaign_year);
		if (!$res["status"]){
	        $result['error_msg'] = 'Ошибка архивации конкурсных групп по номеру ПК ' . $campaign_code . '!';
            return $result;
		}
		$rows_arch = $res["count"];
        foreach ($StringPlanPriema as $string_priema) {
			$this->campaign_code = (string)$string_priema->PKCode;
			$this->campaign_dt = (string)$string_priema->PKYear;
			$this->stage_numb = (string)$string_priema->StepNumber;
			$this->stage_dt_begin = (string)$string_priema->StepDateBegin;
			$this->stage_dt_end = (string)$string_priema->StepDateEnd;
			$this->stage_dt_order = (string)$string_priema->OrderDate;
			$this->curriculum_code = (string)$string_priema->EduPlanCode;
			$this->curriculum_name = (string)$string_priema->EduPlanName;
			$this->faculty_code = (string)$string_priema->FakultetCode;
			$this->faculty_name = (string)$string_priema->FakultetName;
			$this->speciality_code = (string)$string_priema->SpecCode;
			$this->speciality_name = (string)$string_priema->SpecName;
			$this->speciality_human_code = (string)$string_priema->SpecCodeSpec;
			$this->profil_code = (string)$string_priema->ProfileCode;
			$this->profil_name = (string)$string_priema->ProfileName;
			$this->edulevel_code = (string)$string_priema->EduLevelCode;
			$this->edulevel_name = (string)$string_priema->EduLevelName;
			$this->eduform_code = (string)$string_priema->EduFormCode;
			$this->eduform_name = (string)$string_priema->EduFormName;
			$this->eduprogram_code = (string)$string_priema->EduProgCode;
			$this->eduprogram_name = (string)$string_priema->EduProgName;
			$this->finance_code = (string)$string_priema->EnterReasonCode;
			$this->finance_name = (string)$string_priema->EnterReasonName;
			$this->group_code = (string)$string_priema->GroupCode;
			$this->group_name = (string)$string_priema->GroupName;
			$this->group_beneficiary = (string)$string_priema->SpecialRight;
			$this->detail_group_code = (string)$string_priema->TakingFeaturesCode;
			$this->detail_group_name = (string)$string_priema->TakingFeaturesName;
			$this->receipt_allowed = (string)$string_priema->ReceiptAllowed;
			$this->archive = "0";

            $spec = $this->getOne();

            if ($spec == null) {
                // insert
                if ($this->save()) {
                    $log->msg = 'Создана конкурсная группа ' . $this->group_code . ' ПК ' . $this->campaign_code . ' этапа ' . $this->stage_numb . '.';
                    $log->value_old = null;
                    $log->value_new = null;
                    $log->save();
                    $rows_ins++;
                } else {
                    $result['error_msg'] = 'Ошибка при сохранении конкурсной группы ' . $this->group_code . ' ПК ' . $this->campaign_code . ' этапа ' . $this->stage_numb . '!';
                    return $result;
                }
            } else {
                // update
                if ($this->changeAll()) {
                    $log->msg = 'Изменена конкурсная группа ' . $this->group_code . ' ПК ' . $this->campaign_code . ' этапа ' . $this->stage_numb . '.';
                    $log->value_old = null;
                    $log->value_new = null;
                    $log->save();
                    $rows_upd++;
                } else {
                    $result['error_msg'] = 'Ошибка при изменении конкурсной группы ' . $this->group_code . ' ПК ' . $this->campaign_code . ' этапа ' . $this->stage_numb . '!';
                    return $result;
                }
                
            }
        }
        if ($rows_del == 0 && $rows_ins == 0 && $rows_upd == 0) {
            $result['success_msg'] = 'Справочник "' . $dict_name . '" не нуждается в обновлении.';
        } else {
            //$result['success_msg'] = nl2br("В справочнике \"$dict_name\":\n----- удалено записей - $rows_del\n----- добавлено записей - $rows_ins\n----- обновлено записей - $rows_upd");
            $result['success_msg'] = nl2br("В справочнике \"$dict_name\":\n----- удалено записей - $rows_del\n----- добавлено записей - $rows_ins\n----- обновлено записей - $rows_upd\n----- архивировано записей - $rows_arch");
        }
        return $result;
    }
    
    private function archiveCampaign($campaign_year) {
        $res = $this->rowSelectAll('COUNT(*) AS Count',
            self::TABLE_NAME,
            "campaign_code like '$campaign_year%' AND archive = :archive", ["archive" => 0]);    

        $status = $this->rowUpdateUnsafe(self::TABLE_NAME,
            'archive = 1',
            "campaign_code like '$campaign_year%' AND archive = 0");
        
        return array("status" => $status, "count" => $res[0]["Count"]);
	}
    
    private function formatDateForSQL($strDate) {
	    $strDate = str_replace('.00', '', (string)$strDate);
		$datetime = DateTime::createFromFormat(self::INPUT_FORMAT, $strDate);
		if (!$datetime){
			return FALSE;
		}
		else{
			return $datetime->format(self::OUTPUT_FORMAT);
		}
    }

    public
    function __destruct()
    {
        $this->db = null;
    }
}
