<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_EduclevelsDoctypes as EduclevelsDoctypes;
use common\models\Model_DictUniversity as Model_DictUniversity;
use common\models\Model_AdmissionCampaign as Model_AdmissionCampaign;
use common\models\Model_DictDocships as Model_DictDocships;
use common\models\Model_DictForeignLangs as DictForeignLangs;
use common\models\Model_ForeignLangs as ForeignLangs;
use common\models\Model_IndAchievs as Model_IndAchievs;
use common\models\Model_ApplicationAchievs as Model_ApplicationAchievs;

class Model_Application extends Model
{
	/*
		Application processing
	*/

	/**
     * Application rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'university' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Место поступления обязательно для заполнения!'],
								'success' => 'Место поступления заполнено верно.'
                               ],
                'campaign' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Приёмная кампания обязательна для заполнения!'],
								'success' => 'Приёмная кампания заполнена верно.'
                               ],
                'docs_educ' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Документ об образовании обязателен для заполнения!'],
								'success' => 'Документ об образовании заполнен верно.'
                               ],
                'campus' => [
							'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'success' => 'Получена информация о потребности в общежитии.'
                           ],
                'conds' => [
							'type' => 'checkbox',
	                        'class' => 'form-check-input',
	                        'success' => 'Получена информация о просьбе в создании специальных условий.'
	                       ],
	            'remote' => [
							'type' => 'checkbox',
	                        'class' => 'form-check-input',
	                        'success' => 'Получена информация о просьбе в сдаче вступительных испытаний с использованием дистанционных технологий.'
	                       ]
	            ];
	}

	/**
     * Shows type.
     *
     * @return string
     */
	public static function showType($type)
	{
		switch ($type) {
			case Application::TYPE_NEW:
				return '<div class="alert alert-info">Тип: <strong>'.mb_convert_case(Application::TYPE_NEW_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::TYPE_CHANGE:
				return '<div class="alert alert-info">Тип: <strong>'.mb_convert_case(Application::TYPE_CHANGE_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::TYPE_RECALL:
				return '<div class="alert alert-info">Тип: <strong>'.mb_convert_case(Application::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			default:
				return '<div class="alert alert-info">Тип: <strong>НЕИЗВЕСТНО</strong></div>';
		}
	}

	/**
     * Shows status.
     *
     * @return string
     */
	public static function showStatus($status)
	{
		switch ($status) {
			case Application::STATUS_CREATED:
				return '<div class="alert alert-info">Состояние: <strong>'.mb_convert_case(Application::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::STATUS_SAVED:
				return '<div class="alert alert-info">Состояние: <strong>'.mb_convert_case(Application::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::STATUS_SENDED:
				return '<div class="alert alert-primary">Состояние: <strong>'.mb_convert_case(Application::STATUS_SENDED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::STATUS_APPROVED:
				return '<div class="alert alert-success">Состояние: <strong>'.mb_convert_case(Application::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::STATUS_REJECTED:
				return '<div class="alert alert-danger">Состояние: <strong>'.mb_convert_case(Application::STATUS_REJECTED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::STATUS_CHANGED:
				return '<div class="alert alert-warning">Состояние: <strong>'.mb_convert_case(Application::STATUS_CHANGED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			case Application::STATUS_RECALLED:
				return '<div class="alert alert-danger">Состояние: <strong>'.mb_convert_case(Application::STATUS_RECALLED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong></div>';
			default:
				return '<div class="alert alert-warning">Состояние: <strong>НЕИЗВЕСТНО</strong></div>';
		}
	}

	/**
     * Deletes application from database.
     *
     * @return array
     */
	public function delete($form)
	{
		$app = new Application();
		$app->id = $form['id'];
		$app_row = $app->get();
		if ($app_row['status'] == $app::STATUS_CREATED || $app_row['status'] == $app::STATUS_SAVED) {
			if ($app->clear() > 0) {
				$_SESSION[APP_CODE]['success_msg'] = 'Заявление № '.$form['id'].' успешно удалено.';
				return true;
			} else {
				$_SESSION[APP_CODE]['error_msg'] = 'Ошибка удаления заявления! Свяжитесь с администратором.';
				return false;
			}
		} else {
			$_SESSION[APP_CODE]['error_msg'] = 'Удалять заявления можно только с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return false;
		}
	}

	/**
     * Checks education document data.
     *
     * @return array
     */
	public function check($form)
	{
		$app = new Application();
			$university = new Model_DictUniversity();
			$university->code = $form['university'];
			$university_row = $university->getByCode();
		$app->id_university = $university_row['id'];
			$campaign = new Model_AdmissionCampaign();
			$campaign->code = $form['campaign'];
			$campaign_row = $campaign->getByCode();
		$app->id_campaign = $campaign_row['id'];
			// check campaign app
			if ($app->existsUserCampaign() === true) {
				$form['error_msg'] = 'Заявление на данную приёмную кампанию уже есть!';
				return $form;
			}
			// check campaign period
			$campaign_row = $campaign->getPeriod();
			if ($campaign_row) {
				if (date('d.m.Y') < $campaign_row['dt_begin'] || date('d.m.Y') > $campaign_row['dt_end']) {
					$form['error_msg'] = 'Сроки приёма выбранной кампании - с '.$campaign_row['dt_begin'].' по '.$campaign_row['dt_end'].' !';
					return $form;
				}
			} else {
				$form['error_msg'] = 'Ошибка при получении сроков приёма!';
				return $form;
			}
		$app->id_docseduc = $form['docs_educ'];
			$docship = new Model_DictDocships();
			$docship->code = '000000001';
			$docship_row = $docship->getByCode();
		$app->id_docship = $docship_row['id'];
			// foreign lang
			$lang = new ForeignLangs();
			$lang_row = $lang->getFirstByUser();
			if ($lang_row) {
				$lang = new DictForeignLangs();
				$lang->id = $lang_row['id_lang'];
				$lang_row = $lang->get();
				if ($lang_row) {
					$app->id_lang = $lang_row['id'];
				} else {
					$form['error_msg'] = 'Ошибка при создании отметки об изучаемом иностранном языке!';
					return $form;
				}
			} else {
				$lang = new DictForeignLangs();
				$lang_row = $lang->getBsuNot();
				if ($lang_row) {
					$app->id_lang = $lang_row['id'];
				} else {
					$form['error_msg'] = 'Ошибка при создании отметки о том, что иностранный язык не изучался!';
					return $form;
				}
			}
		$app->type = $app::TYPE_NEW;
		$app->campus = (($form['campus'] == 'checked') ? 1 : 0);
		$app->conds = (($form['conds'] == 'checked') ? 1 : 0);
		$app->remote = (($form['remote'] == 'checked') ? 1 : 0);
			$ed = new EduclevelsDoctypes();
			$ed_row = $ed->getPayByDocCampaign($app->id_campaign, $app->id_docseduc);
			if ($ed_row) {
				$app->pay = $ed_row['pay'];
			} else {
				$form['error_msg'] = 'Ошибка при получении отметки о возможности поступления на бесплатную форму обучения!';
				return $form;
			}
		$app->id = $app->save();
		if ($app->id > 0) {
			$applog = new ApplicationStatus();
			$applog->id_application = $app->id;
			$applog->create();
			// set individual achievments
			$ia = new Model_IndAchievs();
			$ia->campaign_code = $form['campaign'];
			$ia_arr = $ia->getByUserCampaign();
			if ($ia_arr) {
				$appia = new Model_ApplicationAchievs();
				$appia->pid = $app->id;
				foreach ($ia_arr as $ia_row) {
					$appia->id_achiev = $ia_row['id'];
					$appia->save();
				}
			}
		} else {
			$form['error_msg'] = 'Ошибка при создании заявления!';
		}
		return $form;
	}
}
