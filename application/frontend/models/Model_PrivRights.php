<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Application as Application;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_DictIndAchievs as Model_DictIndAchievs;
use common\models\Model_DictScans as Model_DictScans;

include ROOT_DIR.'/application/frontend/models/Model_Scans.php';

class Model_PrivRights extends Model
{
	/*
		Individual achievments processing
	*/

	/**
     * Individual achievments rules.
     *
     * @return array
     */
	public function rules()
	{
		$rules = [
                'achiev_type' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Вид индивидуального достижения обязателен для заполнения!'],
								'success' => 'Вид индивидуального достижения заполнен верно.'
                               ],
                'series' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB_ALL, 'msg' => 'Для серии можно использовать '.MSG_ALPHA_NUMB_ALL.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!'],
                            'success' => 'Серия заполнена верно.'
                           ],
                'numb' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для номера можно использовать '.MSG_INFO_RUS.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 20, 'msg' => 'Слишком длинный номер!'],
                            'success' => 'Номер заполнен верно.'
                           ],
                'company' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования организации можно использовать '.MSG_INFO_RUS.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 150, 'msg' => 'Слишком длинное наименование организации!'],
                            'success' => 'Наименование учебного заведения заполнено верно.'
                           ],
				'dt_issue' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть '.MSG_DATE_STRONG.'!'],
                                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!'],
                                'success' => 'Дата выдачи заполнена верно.'
                               ]
	            ];
		$scans = Model_Scans::createRules('ind_achievs');
		return array_merge($rules, $scans);
	}

	/**
     * Gets individual achievment from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ia = new IndAchievs();
		$ia->id = $id;
		return $ia->get();
	}

	/**
     * Deletes individual achievment from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$ia = new IndAchievs();
		$ia->id = $form['id'];
		if ($ia->existsAppGo()) {
			$app = new Application();
			$form['error_msg'] = 'Удалять индивидуальные достижения, которые используются в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong> нельзя!';
		} else {
			if ($ia->clear() > 0) {
				$form['success_msg'] = 'Индивидуальное достижение № '.$ia->id.' удалено.';
			} else {
				$form['error_msg'] = 'Ошибка удаления индивидуального достижения № '.$ia->id.'! Свяжитесь с администратором.';
			}
		}
		return $form;
	}

	/**
     * Unsets individual achievment files.
     *
     * @return array
     */
	public function unsetScans($form)
	{
		return Model_Scans::unsets('ind_achievs', $form);
	}

	/**
     * Checks education document data.
     *
     * @return array
     */
	public function check($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$ia = new IndAchievs();
		$ia->id_user = $_SESSION[APP_CODE]['user_id'];
			$achievtype = new Model_DictIndAchievs();
			$achievtype->code = $form['achiev_type'];
			$row_achievtype = $achievtype->getByCode();
		$ia->id_achiev = $row_achievtype['id'];
		$ia->series = (empty($form['series'])) ? null : $form['series'];
		$ia->numb = (empty($form['numb'])) ? null : $form['numb'];
		$ia->company = (empty($form['company'])) ? null : $form['company'];
		$ia->dt_issue = (empty($form['dt_issue']) ? null : date('Y-m-d', strtotime($form['dt_issue'])));
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$ia->id = $form['id'];
			$ia_row = $ia->getByNumbExcept();
			if ($ia_row) {
				$form['error_msg'] = 'Индивидуальное достижение "'.$row_achievtype['description'].'" с серией/номером '.((!empty($docs->series)) ? $docs->series : '').'/'.$docs->numb.' уже есть!';
				return $form;
			} elseif ($ia->existsAppGo()) {
				$app = new Application();
				$form['error_msg'] = 'Изменять индивидуальные достижения, которые используются в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong> нельзя!';
				return $form;
			} else {
				if ($ia->changeAll()) {
					$form['success_msg'] = 'Изменено индивидуальное достижение № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении индивидульного достижения!';
					return $form;
				}
			}
		} else {
			// insert
			$ia_row = $ia->getByNumb();
			if ($ia_row) {
				$form['error_msg'] = 'Индивидуальное достижение "'.$row_achievtype['description'].'" с серией/номером '.((!empty($docs->series)) ? $docs->series : '').'/'.$docs->numb.' уже есть!';
				return $form;
			} else {
				$form['id'] = $ia->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создано новое индивидуальное достижение № '.$form['id'].'.';
				} else {
					unset($form['id']);
					$form['error_msg'] = 'Ошибка при создании индивидуального достижения!';
					return $form;
				}
			}
		}
		/* scans */
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = 'ind_achievs';
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			foreach ($dict_scans_arr as $dict_scans_row) {
				$form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
				if (!empty($form['error_msg'])) {
					return $form;
				}
			}
		}
		return $form;
	}
}
