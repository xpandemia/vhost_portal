<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\PDF_Helper as PDF_Helper;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_Scans as Scans;
use common\models\Model_Docs as Model_Docs;
use common\models\Model_AdmissionCampaign as Model_AdmissionCampaign;
use common\models\Model_DictSpeciality as Model_DictSpeciality;
use common\models\Model_DictEntranceExams as Model_DictEntranceExams;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_DictDiscipline as Model_DictDiscipline;
use common\models\Model_EgeDisciplines as Model_EgeDisciplines;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use common\models\Model_Resume as Resume;

include ROOT_DIR.'/application/frontend/models/Model_Scans.php';

class Model_ApplicationSpec extends Model
{
	/*
		Application spec processing
	*/

	/**
     * Application spec rules.
     *
     * @return array
     */
	public function rules()
	{
		$rules = [
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
		$scans = Model_Scans::createRules('application');
		return array_merge($rules, $scans);
	}

	/**
     * Validates resume advanced.
     *
     * @return array
     */
	public function validateFormAdvanced($form, $id)
	{
		$place = new ApplicationPlaces();
		$place->pid = $id;
		// photo3x4
		if ($place->getByAppForBachelorSpec()) {
			if (empty($form['photo3x4_name'])) {
				$form = $this->setFormErrorFile($form, 'photo3x4', 'Скан-копия "Фотография 3х4" обязательна для заполнения!');
			}
		}
		// medical_certificate
		if ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1()) {
			if (empty($form['medical_certificate_face_name'])) {
				$form = $this->setFormErrorFile($form, 'medical_certificate_face', 'Скан-копия "Медицинская справка (лицевая сторона)" обязательна для заполнения!');
			}
			if (empty($form['medical_certificate_back_name'])) {
				$form = $this->setFormErrorFile($form, 'medical_certificate_back', 'Скан-копия "Медицинская справка (оборотная сторона)" обязательна для заполнения!');
			}
		}
		return $form;
	}

	/**
     * Gets application spec data from database.
     *
     * @return array
     */
	public function get($id)
	{
		$app = new Application();
		$app->id = $id;
		return $app->getSpec();
	}

	/**
     * Gets application places exams.
     *
     * @return array
     */
	public function getExams($form)
	{
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, 4) == 'exam') {
				$form[$key] = $value;
			}
		}
		return $form;
	}

	/**
     * Unsets application spec files.
     *
     * @return array
     */
	public function unsetScans($form)
	{
		$place = new ApplicationPlaces();
		$place->pid = $form['id'];
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = 'application';
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			$docs = new Model_Docs();
			$docs->doc_code = 'application';
			$docs_row = $docs->getByCode();
			$scans = new Scans();
			foreach ($dict_scans_arr as $dict_scans_row) {
				// check
				$unset = 0;
				if ($dict_scans_row['required'] == 1) {
					$unset = 1;
				} elseif ($dict_scans_row['scan_code'] == 'photo3x4' && $place->getByAppForBachelorSpec()) {
					$unset = 1;
				} elseif ($dict_scans_row['scan_code'] == 'medical_certificate_face' && ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1())) {
					$unset = 1;
				} elseif ($dict_scans_row['scan_code'] == 'medical_certificate_back' && ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1())) {
					$unset = 1;
				}
				// unset
				if ($unset == 1) {
					$scans->id_doc = $docs_row['id'];
					$scans->id_scans = $dict_scans_row['id'];
					if (!$scans->getByDoc()) {
						$form[$dict_scans_row['scan_code'].'_id'] = null;
						$form[$dict_scans_row['scan_code']] = null;
						$form[$dict_scans_row['scan_code'].'_id'] = null;
						$form[$dict_scans_row['scan_code'].'_name'] = null;
						$form[$dict_scans_row['scan_code'].'_type'] = null;
						$form[$dict_scans_row['scan_code'].'_size'] = null;
						$form[$dict_scans_row['scan_code'].'_scs'] = null;
						$form[$dict_scans_row['scan_code'].'_err'] = 'Скан-копия "'.ucfirst($dict_scans_row['scan_name']).'" обязательна для заполнения!';
					}
				}
			}
		}
		return $form;
	}

	/**
     * Checks application places data.
     *
     * @return array
     */
	public function checkPlaces($post)
	{
		$form['pid'] = htmlspecialchars($post['pid']);
		$form['error_msg'] = null;
		$form['success_msg'] = null;
		// get app
		$app = new Application();
		$app->id = $form['pid'];
		$app_row = $app->get();
		// get max_spec
		$adm = new Model_AdmissionCampaign();
		$adm->id = $app_row['id_campaign'];
		$adm_row = $adm->getById();
		if ($adm_row) {
			// get specs
			$spec_unique_arr = [];
			$spec_arr = [];
			$exams_arr = [];
			foreach ($post as $key => $value) {
				if (substr($key, 0, 4) == 'spec') {
					$spec = new Model_DictSpeciality();
					$spec->id = $value;
					$spec_row = $spec->getById();
					if ($spec_row) {
						$place = $spec_row['speciality_code'].((!empty($spec_row['profil_code'])) ? $spec_row['profil_code'] : '');
						array_push($spec_arr, [$spec_row['id'], $spec_row['campaign_code'], $spec_row['curriculum_code'], $spec_row['group_code'], $spec_row['edulevel_code']]);
						if (array_search($place, $spec_unique_arr) === false) {
							array_push($spec_unique_arr, $place);
						}
					} else {
						$form['error_msg'] = 'Ошибка при получении данных направления подготовки с ID '.$value.'!';
						return $form;
					}
				}
			}
			// check max_spec
			if (count($spec_unique_arr) <= $adm_row['max_spec']) {
				$places = new ApplicationPlaces();
				$places->pid = $form['pid'];
				// clear specs
				$places->clearByApplication();
				// set specs
				foreach ($spec_arr as $spec_row) {
					$places->id_spec = $spec_row[0];
					$places->curriculum = $spec_row[2];
					$id = $places->save();
					if ($id > 0) {
						// get entrance exams
						// bachelor and specialist only
						if ($spec_row[4] == '000000001' || $spec_row[4] == '000000002') {
							$exams = new Model_DictEntranceExams();
							$exams->campaign_code = $spec_row[1];
							$exams->group_code = $spec_row[3];
							$exams_arr = $exams->getByCampaignGroup();
							if ($exams_arr) {
								// set entrance exams
								$enter = new ApplicationPlacesExams();
								$enter->pid = $id;
								foreach ($exams_arr as $exams_row) {
									$disc = new Model_DictDiscipline();
									$disc->code = $exams_row['exam_code'];
									$disc->campaign_code = $spec_row[1];
									$disc_row = $disc->getOne();
										$enter->id_discipline = $disc_row['id'];
									$ege = new Model_EgeDisciplines();
									$ege->code_discipline = $exams_row['exam_code'];
									$ege_row = $ege->checkDiscipline();
										$test = new Model_DictTestingScopes();
									if ($ege_row) {
										// ege
										$test_row = $test->getEge();
									} else {
										// exam
										$test_row = $test->getExam();
									}
									$enter->id_test = $test_row['id'];
									if ($enter->save() == 0) {
										$form['error_msg'] = 'Ошибка сохранения вступительного испытания с ID '.$enter->id_discipline.' для направления подготовки с ID '.$id.'!';
										return $form;
									}
								}
							} else {
								$form['error_msg'] = 'Ошибка при получении вступительных испытаний направления подготовки с ID '.$value.'!';
								return $form;
							}
						}
					} else {
						$form['error_msg'] = 'Ошибка при сохранении направления подготовки с ID '.$spec_row[0].'!';
						return $form;
					}
				}
			} else {
				$form['error_msg'] = 'Превышено кол-во направлений подготовки: выбрано '.count($spec_unique_arr).' при разрешённых '.$adm_row['max_spec'].'!';
			}
		} else {
			$form['error_msg'] = 'Ошибка при получении максимального числа направлений подготовки приёмной кампании с ID '.$form['pid'].'!';
		}
		return $form;
	}

	/**
     * Checks application spec data.
     *
     * @return array
     */
	public function check($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$app = new Application();
		/* check type */
		if ($form['type'] == $app::TYPE_RECALL) {
			$form['error_msg'] = 'Нельзя сохранять заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* check status */
		if ($form['status'] != $app::STATUS_CREATED && $form['status'] != $app::STATUS_SAVED && $form['status'] != $app::STATUS_CHANGED) {
			$form['error_msg'] = 'Сохранять можно только заявления с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_CHANGED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* exams */
		$places = new ApplicationPlaces();
		$places->pid = $form['id'];
		$places_arr = $places->getSpecsByApp();
		if ($places_arr) {
			foreach ($places_arr as $places_row) {
				$exams = new ApplicationPlacesExams();
				$exams->pid = $places_row['id'];
				$exams_arr = $exams->getExamsByPlace();
				if ($exams_arr) {
					foreach ($exams_arr as $exams_row) {
						if ($exams_row['test_code'] != $form['exam'.$exams_row['discipline_code']]) {
							$exams->id = $exams_row['id'];
								$test = new Model_DictTestingScopes();
								$test->code = $form['exam'.$exams_row['discipline_code']];
								$test_row = $test->getByCode();
							$exams->id_test = $test_row['id'];
							if (!$exams->changeTest()) {
								$form['error_msg'] = 'Ошибка при изменении типа вступительного испытания с ID '.$exams_row['id'].'!';
								return $form;
							}
						}
					}
				}
			}
		} else {
			$form['error_msg'] = 'Сохранение невозможно - направления подготовки не выбраны!';
			return $form;
		}
		/* application */
		$app->id = $form['id'];
		$app_row = $app->get();
		$app->id_docseduc = $app_row['id_docseduc'];
		$app->id_docship = $app_row['id_docship'];
		$app->id_lang = $app_row['id_lang'];
		$app->status = $app::STATUS_SAVED;
		// additional info
		$app->campus = (($form['campus'] == 'checked') ? 1 : 0);
		$app->conds = (($form['conds'] == 'checked') ? 1 : 0);
		// check remote
		if (count($places->getByAppForPayedOnline()) == 0 || count($places->getByAppForPayedOnline()) == count($places->getSpecsByApp())) {
			$app->remote = (($form['remote'] == 'checked') ? 1 : 0);
		} else {
			$app->remote = 0;
		}
		$app->active = $app_row['active'];
		$app->changeAll();
		$form['status'] = $app->status;
			if ($app_row['status'] != $app::STATUS_SAVED) {
				$applog = new ApplicationStatus();
				$applog->id_application = $app->id;
				$applog->create();
			}
		/* scans */
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = 'application';
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			foreach ($dict_scans_arr as $dict_scans_row) {
				$form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
				if (!empty($form['error_msg'])) {
					return $form;
				}
			}
		}
		Basic_Helper::msgReset();
		$this->form['success_msg'] = 'Заявление успешно сохранено.';
		$form['error_msg'] = null;
		return $form;
	}

	/**
     * Sends application spec data.
     *
     * @return array
     */
	public function send($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$app = new Application();
		/* check status */
		if ($form['status'] != $app::STATUS_SAVED) {
			$form['error_msg'] = 'Отправлять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		if ($form['status'] == $app::STATUS_SENDED) {
			$form['error_msg'] = 'Заявление уже отправлено!';
			return $form;
		}
		/* send */
		$app->id = $form['id'];
		$app->status = $app::STATUS_SENDED;
		$app->changeStatus();
		$form['status'] = $app->status;
			$applog = new ApplicationStatus();
			$applog->id_application = $app->id;
			$applog->create();
		Basic_Helper::msgReset();
		$form['success_msg'] = 'Заявление успешно отправлено.';
		$form['error_msg'] = null;
		return $form;
	}

	/**
     * Changes application spec data.
     *
     * @return array
     */
	public function change($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$app = new Application();
		/* check type */
		if ($form['type'] == $app::TYPE_RECALL) {
			$form['error_msg'] = 'Нельзя изменять заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* check status */
		if ($form['status'] != $app::STATUS_APPROVED && $form['status'] != $app::STATUS_REJECTED) {
			$form['error_msg'] = 'Изменять можно только заявления с состоянием: <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_REJECTED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* change */
		$app->id = $form['id'];
		$app->status = $app::STATUS_CHANGED;
		$app->changeStatus();
			$applog = new ApplicationStatus();
			$applog->id_application = $app->id;
			$applog->create();
		$form['status'] = $app->status;
		$id = $app->copy($app::TYPE_CHANGE);
		if ($id > 0) {
			$spec_row = $this->get($id);
			$form = $this->setForm($this->rules(), $spec_row);
			$form['id'] = $id;
			$form['status'] = $spec_row['status'];
			$form['numb'] = $spec_row['numb'];
			Basic_Helper::msgReset();
			$form['success_msg'] = 'Заявление успешно изменено.';
			$form['error_msg'] = null;
		} else {
			Basic_Helper::msgReset();
			$form['success_msg'] = null;
			$form['error_msg'] = 'Ошибка при изменении заявления.';
		}
		return $form;
	}

	/**
     * Recalls application spec data.
     *
     * @return array
     */
	public function recall($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$app = new Application();
		/* check type */
		if ($form['type'] == $app::TYPE_RECALL) {
			$form['error_msg'] = 'Нельзя отзывать заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* check status */
		if ($form['status'] != $app::STATUS_APPROVED) {
			$form['error_msg'] = 'Изменять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* recall */
		$app->id = $form['id'];
		$app->status = $app::STATUS_RECALLED;
		$app->changeStatus();
			$applog = new ApplicationStatus();
			$applog->id_application = $app->id;
			$applog->create();
		$form['status'] = $app->status;
		$id = $app->copy($app::TYPE_RECALL);
		if ($id > 0) {
			$spec_row = $this->get($id);
			$form = $this->setForm($this->rules(), $spec_row);
			$form['id'] = $id;
			$form['status'] = $spec_row['status'];
			$form['numb'] = $spec_row['numb'];
			Basic_Helper::msgReset();
			$form['success_msg'] = 'Заявление успешно отозвано.';
			$form['error_msg'] = null;
		} else {
			Basic_Helper::msgReset();
			$form['success_msg'] = null;
			$form['error_msg'] = 'Ошибка при отзыве заявления.';
		}
		return $form;
	}

	/**
     * Saves application spec data as PDF.
     *
     * @return array
     */
	public function savePdf($id)
	{
		$pdf = new PDF_Helper();
		$app = new Application();
		$app->id = $id;
		$app_row = $app->get();
		$place = new ApplicationPlaces();
		$place->pid = $id;
		if ($place->getByAppForBachelorSpec()) {
			$resume = new Resume();
			$resume_row = $resume->getByUser();
			$data = [
	                'name_last' => $resume_row['name_last'],
	                'name_first' => $resume_row['name_first'],
	                'name_middle' => $resume_row['name_middle'],
	                'birth_dt' => date('d.m.Y', strtotime($resume_row['birth_dt'])),
	                'citizenship' => mb_convert_case(mb_convert_case($resume_row['citizenship_name'], MB_CASE_LOWER, 'UTF-8'), MB_CASE_TITLE, 'UTF-8'),
	                'passport_type' => $resume_row['passport_type_name'],
	                'series' => $resume_row['series'],
	                'numb' => $resume_row['numb'],
	                'unit_code' => $resume_row['unit_code'],
	                'when_where' => $resume_row['unit_name'].' '.date('d.m.Y', strtotime($resume_row['dt_issue'])),
	                'address_reg' => $resume_row['address_reg'],
	                'phone_main' => ((!empty($resume_row['phone_mobile'])) ? $resume_row['phone_mobile'] : $resume_row['phone_home']),
	                'phone_add' => $resume_row['phone_add'],
	                'email' => $resume_row['email'],
	                'address_res' => $resume_row['address_res'],
	                'app_numb' => '№ '.$app_row['numb'],
	                'campus_yes' => 'On'
	                ];
			$pdf->create($data, 'application_2018', 'заявление'.$app_row['numb']);
		} else {
			$resume = new Resume();
			$resume_row = $resume->getByUser();
			if ($resume_row['sex'] == 0) {
				$data = [
		                'header' => 'Уважаемая '.$resume_row['name_last'].' '.$resume_row['name_first'].' '.$resume_row['name_middle'].'!'
		                ];
			} else {
				$data = [
		                'header' => 'Уважаемый '.$resume_row['name_last'].' '.$resume_row['name_first'].' '.$resume_row['name_middle'].'!'
		                ];
			}
			$pdf->create($data, 'application_sorry', 'application_sorry'.$app_row['numb']);
		}
	}
}
