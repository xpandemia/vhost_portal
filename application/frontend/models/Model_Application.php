<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_DictUniversity as Model_DictUniversity;
use common\models\Model_AdmissionCampaign as Model_AdmissionCampaign;

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
		$app->type = $app::TYPE_NEW;
		$app->id = $app->save();
		if ($app->id > 0) {
			$app->numb = $app->generateNumb();
			$app->changeNumb();
				$applog = new ApplicationStatus();
				$applog->id_application = $app->id;
				$applog->numb = $app->numb;
				$applog->status = $app::STATUS_CREATED;
				$applog->save();
			$_SESSION[APP_CODE]['error_msg'] = null;
			$_SESSION[APP_CODE]['success_msg'] = 'Создано новое заявление.';
		} else {
			$form['error_msg'] = 'Ошибка при создании заявления!';
		}
		return $form;
	}
}
