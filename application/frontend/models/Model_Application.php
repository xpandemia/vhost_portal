<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_DictUniversity as Model_DictUniversity;
use common\models\Model_AdmissionCampaign as Model_AdmissionCampaign;
use common\models\Model_DictDocships as Model_DictDocships;
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
                'docs_ship' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Тип возврата документов обязателен для заполнения!'],
								'success' => 'Тип возврата документов заполнен верно.'
                               ],
                'campus' => [
							'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'success' => 'Получена потребность в общежитии.'
                           ],
                'conds' => [
							'type' => 'checkbox',
	                        'class' => 'form-check-input',
	                        'success' => 'Получена просьба о создании специальных условий.'
	                       ],
	            'remote' => [
							'type' => 'checkbox',
	                        'class' => 'form-check-input',
	                        'success' => 'Получена просьба о сдаче вступительных испытаний с использованием дистанционных технологий.'
	                       ]
	            ];
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
				return '<div class="alert alert-info">Состояние заявления: СОЗДАНО</div>';
			case Application::STATUS_SENDED:
				return '<div class="alert alert-primary">Состояние заявления: ОТПРАВЛЕНО</div>';
			case Application::STATUS_APPROVED:
				return '<div class="alert alert-success">Состояние заявления: ОДОБРЕНО</div>';
			case Application::STATUS_REJECTED:
				return '<div class="alert alert-danger">Состояние заявления: ОТКЛОНЕНО</div>';
			case Application::STATUS_RECALLED:
				return '<div class="alert alert-danger">Состояние заявления: ОТОЗВАНО</div>';
			case Application::STATUS_CHANGED:
				return '<div class="alert alert-warning">Состояние заявления: ИЗМЕНЕНО</div>';
			default:
				return '<div class="alert alert-warning">Состояние заявления: НЕИЗВЕСТНО</div>';
		}
	}

	/**
     * Deletes application from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$app = new Application();
		$app->id = $form['id'];
		if ($app->clear() > 0) {
			return true;
		} else {
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
		$app->id_user = $_SESSION[APP_CODE]['user_id'];
			$university = new Model_DictUniversity();
			$university->code = $form['university'];
			$row_university = $university->getByCode();
		$app->id_university = $row_university['id'];
			$campaign = new Model_AdmissionCampaign();
			$campaign->code = $form['campaign'];
			$row_campaign = $campaign->getByCode();
		$app->id_campaign = $row_campaign['id'];
		$app->id_docseduc = $form['docs_educ'];
			$docship = new Model_DictDocships();
			$docship->code = $form['docs_ship'];
			$row_docship = $docship->getByCode();
		$app->id_docship = $row_docship['id'];
		$app->type = $app::TYPE_NEW;
		$app->campus = (($form['campus'] == 'checked') ? 1 : 0);
		$app->conds = (($form['conds'] == 'checked') ? 1 : 0);
		$app->remote = (($form['remote'] == 'checked') ? 1 : 0);
		$app->id = $app->save();
		if ($app->id > 0) {
			$app->numb = $app->generateNumb();
			$app->changeNumb();
				$applog = new ApplicationStatus();
				$applog->id_application = $app->id;
				$applog->numb = $app->numb;
				$applog->status = $app::STATUS_CREATED;
				$applog->save();
			// set individual achievments
			$ia = new Model_IndAchievs();
			$ia->id_user = $_SESSION[APP_CODE]['user_id'];
			$ia->campaign_code = $form['campaign'];
			$ia_arr = $ia->getByUserCampaign();
			if ($ia_arr) {
				$appia = new Model_ApplicationAchievs();
				$appia->pid = $app->id;
				$appia->id_user = $_SESSION[APP_CODE]['user_id'];
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
