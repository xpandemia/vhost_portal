<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;

class Model_ApplicationPlacesExams extends Db_Helper
{
    /*
        Application places exams processing
    */

    const TABLE_NAME = 'application_places_exams';
    
    public const EXAM_NO_SELECTION = -1;
    public const EXAM_SELECTION = 1;
    public const EXAM_REQUIRED = 2;

    public $id;
    public $pid;
    public $id_user;
    public $id_test;
    public $id_discipline;
    public $points;
    public $reg_year;
    public $dt_created;
    public $selected;

    public $db;

    public function __construct()
    {
        $this->db = Db_Helper::getInstance();
    }

    /**
     * Application places exams rules.
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
            'pid' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->pid
            ],
            'id_user' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_user
            ],
            'id_test' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_test
            ],
            'id_discipline' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->id_discipline
            ],
            'points' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->points
            ],
            'reg_year' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->reg_year
            ],
            'dt_created' => [
                'required' => 1,
                'insert' => 1,
                'update' => 0,
                'value' => $this->dt_created
            ],
            'selected' => [
                'required' => 0,
                'insert' => 1,
                'update' => 1,
                'value' => $this->selected
            ],            
        ];
    }

    /**
     * Gets exams by place.
     *
     * @return array
     */
    public function getExamsByPlace()
    {
        return $this->rowSelectAll('application_places_exams.id, dict_testing_scopes.code as test_code, dict_discipline.code as discipline_code',
            'application_places_exams INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id' .
            ' INNER JOIN dict_discipline ON application_places_exams.id_discipline = dict_discipline.id',
            'application_places_exams.pid = :pid',
            [':pid' => $this->pid]);
    }

    /**
     * Gets full exams by place.
     *
     * @return array
     */
    public function getExamsByPlaceFull()
    {
        return $this->rowSelectAll('*',
            self::TABLE_NAME,
            'pid = :pid',
            [':pid' => $this->pid]);
    }

    public function getExamsForChange($disc_code)
    {
        return $this->rowSelectAll('ape.id, ape.id_user user_id, dd.code dics_code',
            'application_places_exams ape
INNER JOIN dict_discipline dd on ape.id_discipline = dd.id',
            'ape.pid = :pid AND ape.id_user = :id_user AND dd.code = :disc_code',
            [
                ':pid' => $this->pid,
                ':id_user' => $_SESSION[APP_CODE]['user_id'],
                ':disc_code' => $disc_code
            ]);
    }

    /**
     * Gets exams by application.
     *
     * @return array
     */
    public function getExamsByApplication()
    {
        return $this->rowSelectAll('DISTINCT dict_testing_scopes.code, dict_testing_scopes.description, dict_discipline.code as discipline_code, dict_discipline.discipline_name, application_places_exams.points, application_places_exams.reg_year',
            'application_places_exams INNER JOIN application_places ON application_places_exams.pid = application_places.id' .
            ' INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id' .
            ' INNER JOIN dict_discipline ON application_places_exams.id_discipline = dict_discipline.id',
            'application_places.pid = :pid and (application_places_exams.selected = :selected1 or application_places_exams.selected = :selected2)',
            [':pid' => $this->pid, 'selected1' => Model_ApplicationPlacesExams::EXAM_REQUIRED, 'selected2' => Model_ApplicationPlacesExams::EXAM_SELECTION],
            'dict_discipline.discipline_name ASC');
    }
    
    public function getExamsByApplicationWithSelection()
    {
        return $this->rowSelectAll('dict_testing_scopes.code, dict_testing_scopes.description, dict_discipline.code as discipline_code, dict_discipline.discipline_name, application_places_exams.points, application_places_exams.reg_year, application_places_exams.pid as application_place_id, application_places_exams.selected, dict_speciality.speciality_name, dict_speciality.finance_name, dict_speciality.eduform_name, dict_spec_groups.name as spec_group_name',
            'application_places_exams INNER JOIN application_places ON application_places_exams.pid = application_places.id' .
            ' INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id' .
            ' INNER JOIN dict_discipline ON application_places_exams.id_discipline = dict_discipline.id' . 
            ' INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id' .
            ' INNER JOIN dict_spec_groups ON dict_spec_groups.code = application_places.group_code',
            'application_places.pid = :pid',
            [':pid' => $this->pid],
            'application_places_exams.pid, CASE application_places_exams.selected WHEN 2 THEN 0 ELSE 1 END ASC, dict_discipline.discipline_name ASC');
            // вначале сортируем по конкурсным группам, далее - основные предметы, среди основных предметов или тех, которые на выбор - по наименованию
    }

    /**
     * Checks if exams exists.
     *
     * @return boolean
     */
    public function existsExams()
    {
        $arr = $this->rowSelectAll('application_places_exams.*',
            'application_places_exams INNER JOIN application_places ON application_places_exams.pid = application_places.id' .
            ' INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id',
            'application_places.pid = :pid AND dict_testing_scopes.description = :description',
            [':pid' => $this->pid,
                ':description' => 'Экзамен']);
        if (!empty($arr)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Saves application places exams data to database.
     *
     * @return integer
     */
    public function save()
    {
        $this->id_user = $_SESSION[APP_CODE]['user_id'];
        $this->dt_created = date('Y-m-d H:i:s');
        $prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
        return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
    }

    /**
     * Changes testing scope.
     *
     * @return boolean
     */
    public function changeTest()
    {
        return $this->rowUpdate(self::TABLE_NAME,
            'id_test = :id_test',
            [':id_test' => $this->id_test],
            ['id' => $this->id]);
    }
    
    public function changeSelectiveExam(){
        $result = $this->rowSelectAll('*',
        	self::TABLE_NAME,
            'pid = :pid and id_user = :id_user and id_discipline = :id_discipline',
            [':pid' => $this->pid, ':id_user' => $this->id_user, ':id_discipline' => $this->id_discipline]);
        if ($result){
        	// Сбрасываем выбор c текущей выбранной дисциплины 
        	$result = $this->rowUpdate(self::TABLE_NAME,
            	'selected = :selected',
            	[':selected' => -1],
            	['pid' => $this->pid, 'id_user' => $this->id_user, 'selected' => 1]);
            if ($result){
            	// устанавливаем выбор на новой выбранной дисциплине
	        	return $this->rowUpdate(self::TABLE_NAME,
	            	'selected = :selected',
	            	[':selected' => 1],
	            	['pid' => $this->pid, 'id_user' => $this->id_user, 'id_discipline' => $this->id_discipline]);            	
            }
        }
    	return FALSE;      
    }    

    public function __destruct()
    {
        $this->db = null;
    }
}
