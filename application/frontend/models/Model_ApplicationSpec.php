<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\PDF_Helper as PDF_Helper;
use common\models\Model_Personal as Personal;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationAchievs as ApplicationAchievs;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_Scans as Scans;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_Docs as Model_Docs;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_AdmissionCampaign as Model_AdmissionCampaign;
use common\models\Model_DictSpeciality as Model_DictSpeciality;
use common\models\Model_DictEntranceExams as Model_DictEntranceExams;
use common\models\Model_DictDiscipline as Model_DictDiscipline;
use common\models\Model_EgeDisciplines as Model_EgeDisciplines;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use common\models\Model_Resume as Resume;
use common\models\Model_DictForeignLangs as DictForeignLangs;

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
		$scans = Model_Scans::createRules('application');
		return array_merge($rules, $scans);
	}

	/**
     * Validates resume advanced.
     *
     * @return array
     */
	public function validateFormAdvanced($form)
	{
		$place = new ApplicationPlaces();
		$place->pid = $form['id'];
		$exam = new ApplicationPlacesExams();
		$exam->pid = $form['id'];
		// application_2
		if (!$place->getByAppForSpecial()) {
			$form = $this->setFormErrorFile($form, 'application_2', 'Скан-копия "Заявление о приеме в БелГУ (второй лист)" обязательна для заполнения!');
		}
		// photo3x4
		if ($place->getByAppForBachelorSpec() && $exam->existsExams()) {
			$form = $this->setFormErrorFile($form, 'photo3x4', 'Скан-копия "Фотография 3х4" обязательна для заполнения!');
		}
		// medical_certificate
		if ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1()) {
			$form = $this->setFormErrorFile($form, 'medical_certificate_face', 'Скан-копия "Медицинская справка (лицевая сторона)" обязательна для заполнения!');
			$form = $this->setFormErrorFile($form, 'medical_certificate_back', 'Скан-копия "Медицинская справка (оборотная сторона)" обязательна для заполнения!');
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
     * Saves application spec exams data.
     *
     * @return array
     */
	public function saveExams($form)
	{
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
							}
						}
					}
				}
			}
		} else {
			$form['error_msg'] = 'Сохранение невозможно - направления подготовки не выбраны!';
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
		$exam = new ApplicationPlacesExams();
		$exam->pid = $form['id'];
		// main
		$form = Model_Scans::unsets('application', $form);
		// application_2
		if (!$place->getByAppForSpecial()) {
			$form = $this->setFormErrorFile($form, 'application_2', 'Скан-копия "Заявление о приеме в БелГУ (второй лист)" обязательна для заполнения!');
		}
		// photo3x4
		if ($place->getByAppForBachelorSpec() && $exam->existsExams()) {
			$form = $this->setFormErrorFile($form, 'photo3x4', 'Скан-копия "Фотография 3х4" обязательна для заполнения!');
		}
		// medical_certificate
		if ($place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1()) {
			$form = $this->setFormErrorFile($form, 'medical_certificate_face', 'Скан-копия "Медицинская справка (лицевая сторона)" обязательна для заполнения!');
			$form = $this->setFormErrorFile($form, 'medical_certificate_back', 'Скан-копия "Медицинская справка (оборотная сторона)" обязательна для заполнения!');
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
		// get citizenship
		$personal = new Personal();
		$citizenship = $personal->getCitizenshipByUser();
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
						array_push($spec_arr, [$spec_row['id'], $spec_row['campaign_code'], $spec_row['curriculum_code'], $spec_row['group_code'], $spec_row['edulevel_code'], $spec_row['eduform_code']]);
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
						// bachelors, specialists, magisters, attending physicians
						if ($spec_row[4] == '000000001' || $spec_row[4] == '000000002' || $spec_row[4] == '000000003' || $spec_row[4] == '000000005') {
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
									$test = new Model_DictTestingScopes();
									if (strripos($exams_row['exam_name'], 'Профессиональное испытание') === false && strripos($exams_row['exam_name'], 'Творческое испытание') === false && strripos($exams_row['exam_name'], 'Теория физической культуры') === false) {
										switch ($spec_row[4]) {
											// bachelors
											case '000000001':
											// specialists
											case '000000002':
												$ege = new Model_EgeDisciplines();
												if ($exams_row['exam_code'] == '000000021') {
													// foreign language
													$langs = new DictForeignLangs();
													if (!empty($app_row['id_lang'])) {
														$langs->id = $app_row['id_lang'];
														$langs_row = $langs->get();
														$ege->code_discipline = $langs_row['code'];
													} else {
														$form['error_msg'] = 'Вы не изучали иностранный язык!';
														$places->clearByApplication();
														return $form;
													}
												} else {
													// others
													$ege->code_discipline = $exams_row['exam_code'];
												}
												$ege_arr = $ege->checkDiscipline();
												if ($citizenship['code'] != '643') {
													// foreigners - full-time ege only, not full-time - ege or exam
													if ($ege_arr) {
														$test_row = $test->getEge();
														$enter->points = $ege_arr['points'];
														$enter->reg_year = $ege_arr['reg_year'];
													} else {
														if ($spec_row[5] == '000000001') {
															$form['error_msg'] = 'Нет подходящих результатов ЕГЭ по дисциплине "'.$exams_row['exam_name'].'"!';
															$places->clearByApplication();
															return $form;
														} else {
															$test_row = $test->getExam();
															$enter->points = null;
															$enter->reg_year = null;
														}
													}
												} else {
													if ($citizenship['code'] == '643' && $app->checkCertificate()) {
														// russia with certificate - ege only
														if ($ege_arr) {
															$test_row = $test->getEge();
															$enter->points = $ege_arr['points'];
															$enter->reg_year = $ege_arr['reg_year'];
														} else {
															$form['error_msg'] = 'Нет подходящих результатов ЕГЭ по дисциплине "'.$exams_row['exam_name'].'"!';
															$places->clearByApplication();
															return $form;
														}
													} else {
														// russia without certificate - ege or exam
														if ($ege_arr) {
															// ege
															$test_row = $test->getEge();
															$enter->points = $ege_arr['points'];
															$enter->reg_year = $ege_arr['reg_year'];
														} else {
															// exam
															$test_row = $test->getExam();
															$enter->points = null;
															$enter->reg_year = null;
														}
													}
												}
												break;
											// magisters
											case '000000003':
												$test_row = $test->getExam();
												$enter->points = null;
												$enter->reg_year = null;
												break;
											// attending physicians
											case '000000005':
												$test_row = $test->getTest();
												$enter->points = null;
												$enter->reg_year = null;
												break;
										}
									} else {
										$test_row = $test->getExam();
									}
									$enter->id_test = $test_row['id'];
									if ($enter->save() == 0) {
										$form['error_msg'] = 'Ошибка сохранения вступительного испытания с ID '.$enter->id_discipline.' для направления подготовки с ID '.$id.'!';
										$places->clearByApplication();
										return $form;
									}
								}
							} else {
								$form['error_msg'] = 'Ошибка при получении вступительных испытаний направления подготовки с ID '.$value.'!';
								$places->clearByApplication();
								return $form;
							}
						}
					} else {
						$form['error_msg'] = 'Ошибка при сохранении направления подготовки с ID '.$spec_row[0].'!';
						$places->clearByApplication();
						return $form;
					}
				}
			} else {
				$form['error_msg'] = 'Превышено кол-во направлений подготовки: выбрано '.count($spec_unique_arr).' при разрешённых '.$adm_row['max_spec'].'!';
			}
		} else {
			$form['error_msg'] = 'Ошибка при получении максимального числа направлений подготовки приёмной кампании с ID '.$form['pid'].'!';
		}
		if (!$form['error_msg']) {
			// clear scans
			$scans = new Scans();
			$scans->id_row = $app->id;
			$scans->clearbyDoc('application');
			// change status
			if ($app_row['status'] != $app::STATUS_CREATED) {
				$app->status = $app::STATUS_CREATED;
				$app->changeStatus();
				$form['status'] = $app->status;
					$applog = new ApplicationStatus();
					$applog->id_application = $app->id;
					$applog->create();
			}
		}
		return $form;
	}

	/**
     * Synchronizes individual achievments for application.
     *
     * @return array
     */
	public function syncIa($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$app = new Application();
		$app->id = $form['id'];
		$app_row = $app->get();
		/* check status */
		if ($app_row['status'] != $app::STATUS_CREATED && $app_row['status'] != $app::STATUS_SAVED) {
			$form['error_msg'] = 'Обновлять индивидуальные достижения можно только в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* sync ia */
		$campaign = new Model_AdmissionCampaign();
		$campaign->id = $app_row['id_campaign'];
		$campaign_row = $campaign->getById();
			$appia = new ApplicationAchievs();
			$appia->pid = $app_row['id'];
			$appia->clearByApplication();
		$ia = new IndAchievs();
		$ia->campaign_code = $campaign_row['code'];
		$ia_arr = $ia->getByUserCampaign();
		if ($ia_arr) {
			foreach ($ia_arr as $ia_row) {
				$appia->id_achiev = $ia_row['id'];
				$appia->save();
			}
		}
		Basic_Helper::msgReset();
		$form['success_msg'] = 'Индивидуальные достижения обновлены.';
		$form['error_msg'] = null;
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
		$app->id = $form['id'];
		$app_row = $app->get();
		/* check type */
		if ($app_row['type'] == $app::TYPE_RECALL) {
			$form['error_msg'] = 'Нельзя сохранять заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* check status */
		if ($app_row['status'] != $app::STATUS_CREATED && $app_row['status'] != $app::STATUS_SAVED && $app_row['status'] != $app::STATUS_CHANGED) {
			$form['error_msg'] = 'Сохранять можно только заявления с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_CHANGED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* application */
		$app->id_docseduc = $app_row['id_docseduc'];
		$app->id_docship = $app_row['id_docship'];
		$app->id_lang = $app_row['id_lang'];
		$app->status = $app::STATUS_SAVED;
		// additional info
		$app->campus = (($form['campus'] == 'checked') ? 1 : 0);
		$app->conds = (($form['conds'] == 'checked') ? 1 : 0);
		// check remote
		$places = new ApplicationPlaces();
		$places->pid = $form['id'];
		if (count($places->getByAppForPayedOnline()) != 0 && count($places->getByAppForPayedOnline()) == count($places->getSpecsByApp())) {
			$app->remote = (($form['remote'] == 'checked') ? 1 : 0);
		} else {
			$app->remote = 0;
			$form['remote'] = null;
		}
		$app->pay = $app_row['pay'];
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
		$form['success_msg'] = 'Заявление сохранено.';
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
		$app->id = $form['id'];
		$app_row = $app->get();
		/* check status */
		if ($app_row['status'] != $app::STATUS_SAVED) {
			$form['error_msg'] = 'Отправлять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		if ($app_row['status'] == $app::STATUS_SENDED) {
			$form['error_msg'] = 'Заявление уже отправлено!';
			return $form;
		}
		/* send */
		$app->status = $app::STATUS_SENDED;
		$app->changeStatus();
		$form['status'] = $app->status;
			$applog = new ApplicationStatus();
			$applog->id_application = $app->id;
			$applog->create();
		Basic_Helper::msgReset();
		$form['success_msg'] = 'Заявление отправлено.';
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
		$app->id = $form['id'];
		$app_row = $app->get();
		/* check type */
		if ($app_row['type'] == $app::TYPE_RECALL) {
			$form['error_msg'] = 'Нельзя изменять заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* check status */
		if ($app_row['status'] != $app::STATUS_APPROVED) {
			$form['error_msg'] = 'Изменять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* change */
		$id = $app->copy($app::TYPE_CHANGE);
		if ($id > 0) {
			$spec_row = $this->get($id);
			$form = $this->setForm($this->rules(), $spec_row);
			$form['id'] = $id;
			Basic_Helper::msgReset();
			$form['success_msg'] = 'Заявление на изменение сформировано.';
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
		$app->id = $form['id'];
		$app_row = $app->get();
		/* check type */
		if ($app_row['type'] == $app::TYPE_RECALL) {
			$form['error_msg'] = 'Нельзя отзывать заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* check status */
		if ($app_row['status'] != $app::STATUS_APPROVED) {
			$form['error_msg'] = 'Изменять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
			return $form;
		}
		/* recall */
		$id = $app->copy($app::TYPE_RECALL);
		if ($id > 0) {
			$spec_row = $this->get($id);
			$form = $this->setForm($this->rules(), $spec_row);
			$form['id'] = $id;
			/* save */
			$app->status = $app::STATUS_SAVED;
			$app->changeStatus();
			$form['status'] = $app->status;
				$applog = new ApplicationStatus();
				$applog->id_application = $form['id'];
				$applog->create();
			Basic_Helper::msgReset();
			$form['success_msg'] = 'Заявление на отзыв сформировано.';
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
     * @return mixed
     */
	public function savePdf($id)
	{
		$pdf = new PDF_Helper();
		$app = new Application();
		$app->id = $id;
		$app_row = $app->get();
		$place = new ApplicationPlaces();
		$place->pid = $id;
		$data = [];
		if ($place->getByAppForBachelorSpec()) {
			// bachelors and specialists
			$data = $this->setAppForPdf($data, $app_row);
			$data = $this->setResumeForPdf($data);
	        $data = $this->setPlacesForPdf($data, $id, 5);
	        $data = $this->setExamsForPdf($data, $id);
	        $data = $this->setEducForPdf($data, $app_row['id_docseduc'], 'bachelor');
	        $data = $this->setForeignLangForPdf($data, $app_row['id_lang']);
			$data['university5_yes'] = 'On';
			$data['specs3_yes'] = 'On';
	        $data = $this->setCampusForPdf($data, $app_row['campus']);
			$data['docsship_personal'] = 'On';
	        $data = $this->setIaForPdf($data, $id);
			$pdf->create($data, 'application_bachelor', 'заявление'.$app_row['numb']);
		} elseif ($place->getByAppForMagister()) {
			// magisters
			$data = $this->setAppForPdf($data, $app_row);
			$data = $this->setResumeForPdf($data);
			$data = $this->setPlacesForPdf($data, $id, 2);
			$data = $this->setEducForPdf($data, $app_row['id_docseduc'], 'magister');
			$data = $this->setForeignLangForPdf($data, $app_row['id_lang']);
			$data['specs2_yes'] = 'On';
			$data = $this->setCampusForPdf($data, $app_row['campus']);
			$data['docsship_personal'] = 'On';
			$data = $this->setIaForPdf($data, $id);
			$pdf->create($data, 'application_magistrature', 'заявление'.$app_row['numb']);
		} elseif ($place->getByAppForSpecial()) {
			// specials
			$data = $this->setAppForPdf($data, $app_row);
			$data = $this->setResumeForPdf($data);
			$data['exams'] = 'On';
			$data = $this->setPlacesForPdf($data, $id, 3);
			$data = $this->setEducForPdf($data, $app_row['id_docseduc'], 'specialist');
			$data = $this->setForeignLangForPdf($data, $app_row['id_lang']);
			if ($app_row['pay'] == 0) {
				$data['special_first_yes'] = 'On';
			} else {
				$data['special_first_no'] = 'On';
			}
			$data = $this->setCampusForPdf($data, $app_row['campus']);
			$pdf->create($data, 'application_special', 'заявление'.$app_row['numb']);
		} elseif ($place->getByAppForClinical()) {
			// attending physicians
			$data = $this->setAppForPdf($data, $app_row);
			$data = $this->setResumeForPdf($data);
			$data = $this->setPlacesForPdf($data, $id, 4);
			$data = $this->setEducForPdf($data, $app_row['id_docseduc'], 'attending_physician');
			$data = $this->setForeignLangForPdf($data, $app_row['id_lang']);
			$data = $this->setCampusForPdf($data, $app_row['campus']);
			$data['docsship_personal'] = 'On';
			$data = $this->setIaForPdf($data, $id);
			$pdf->create($data, 'application_clinical', 'заявление'.$app_row['numb']);
		} elseif ($place->getByAppForTraineeship()) {
			// trainees
			$data = $this->setAppForPdf($data, $app_row);
			$data = $this->setResumeForPdf($data);
			$data = $this->setPlacesForPdf($data, $id, 4);
			$data = $this->setEducForPdf($data, $app_row['id_docseduc'], 'trainee');
			$data = $this->setForeignLangForPdf($data, $app_row['id_lang']);
			$pdf->create($data, 'application_traineeship', 'заявление'.$app_row['numb']);
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

	/**
     * Sets application data for PDF.
     *
     * @return array
     */
	public function setAppForPdf($data, $app_row) : array
	{
		$app = new Application();
		$data['app_numb'] = $app_row['numb'];
		if ($app_row['type'] == $app::TYPE_RECALL) {
			$resume = new Resume();
			$resume_row = $resume->getByUser();
			$data['recall_fio'] = $resume_row['name_last'].' '.$resume_row['name_first'].$resume_row['name_middle'];
			$data['recall_dt'] = date('d.m.Y');
		} else {
			$data['app_dt'] = date('d.m.Y');
		}
		return $data;
	}

	/**
     * Sets resume data for PDF.
     *
     * @return array
     */
	public function setResumeForPdf($data) : array
	{
		$resume = new Resume();
		$resume_row = $resume->getByUser();
		$resume_arr = [
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
		                'address_res' => $resume_row['address_res']
		                ];
		return array_merge($data, $resume_arr);
	}

	/**
     * Sets places data for PDF.
     *
     * @return array
     */
	public function setPlacesForPdf($data, $app, $limit) : array
	{
		$places = new ApplicationPlaces();
		$places->pid = $app;
		$places_arr = $places->getSpecsByAppPdf();
		$i = 1;
		foreach ($places_arr as $places_row) {
			if ($i <= $limit) {
				$spec_arr['place'.$i] = $places_row['place'].' ('.$places_row['edulevel'].')';
				$spec_arr['eduform'.$i] = $places_row['eduform'];
				$spec_arr['finance'.$i] = $places_row['finance'];
				$i++;
			} else {
				break;
			}
		}
		return array_merge($data, $spec_arr);
	}

	/**
     * Sets exams data for PDF.
     *
     * @return array
     */
	public function setExamsForPdf($data, $app) : array
	{
		$exams = new ApplicationPlacesExams();
		$exams->pid = $app;
		$exams_arr = $exams->getExamsByApplication();
		if ($exams_arr) {
			$data['exams_yes'] = 'On';
			$exams_disciplines = '';
			foreach ($exams_arr as $exams_row) {
				switch ($exams_row['description']) {
					case 'ЕГЭ':
						$data['exams_ege'] = 'On';
							switch ($exams_row['discipline_name']) {
								case 'Русский язык':
									$data['ege_rus_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Математика':
									$data['ege_math_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Иностранный язык':
									$data['ege_lang_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Обществознание':
									$data['ege_social_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'История':
									$data['ege_history_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Информатика и ИКТ':
									$data['ege_computer_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Физика':
									$data['ege_physics_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Химия':
									$data['ege_chemistry_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Биология':
									$data['ege_biology_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'Литература':
									$data['ege_literature_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
								case 'География':
									$data['ege_geography_'.$exams_row['reg_year']] = $exams_row['points'];
									break;
							}
						break;
					case 'Экзамен':
						if (strripos($exams_row['discipline_name'], 'Профессиональное испытание') === false && strripos($exams_row['discipline_name'], 'Творческое испытание') === false && strripos($exams_row['discipline_name'], 'Теория физической культуры') === false) {
							$data['exams_university'] = 'On';
							$exams_disciplines .= ' '.$exams_row['discipline_name'];
						}
						break;
				}
			}
			$data['exams_disciplines'] = $exams_disciplines;
			// exams reason
			if (isset($data['exams_university'])) {
				$personal = new Personal();
				$citizenship = $personal->getCitizenshipByUser();
				if ($citizenship['code'] != '643') {
					$data['exams_reason'] = 'иностранные граждане';
				} else {
					$data['exams_reason'] = 'лица, прошедшие государственную итоговую аттестацию по образовательным программам среднего общего образования не в форме ЕГЭ';
				}
			}
		}
		return $data;
	}

	/**
     * Sets education data for PDF.
     *
     * @return array
     */
	public function setEducForPdf($data, $id_docseduc, $edulevel) : array
	{
		$docs = new DocsEduc();
		$docs->id = $id_docseduc;
		$docs_row = $docs->getForPdf();
		$data['educ_type'] = $docs_row['educ_type'];
		$data['school'] = $docs_row['school'];
			switch ($edulevel) {
				case 'bachelor':
				case 'specialist':
					if (in_array($docs_row['doc_type'], $docs::CERTIFICATES)) {
						$data['certificate'] = 'On';
					} elseif (in_array($docs_row['doc_type'], $docs::DIPLOMAS)) {
						$data['diploma'] = 'On';
					}
					break;
				case 'magister':
				case 'trainee':
					switch ($docs_row['doc_type']) {
						case $docs::DIPLOMA_BACHELOR:
							$data['bachelor'] = 'On';
							break;
						case $docs::DIPLOMA_SPECIALIST:
							$data['specialist'] = 'On';
							break;
						case $docs::DIPLOMA_SPECIALIST_DIPLOMA:
							$data['specialist_diploma'] = 'On';
							break;
						case $docs::DIPLOMA_MAGISTER:
							$data['magister'] = 'On';
							break;
					}
			}
		$data['docseduc_series'] = $docs_row['series'];
		$data['docseduc_numb'] = $docs_row['numb'];
		$data['docseduc_dt'] = date('d.m.Y', strtotime($docs_row['dt_issue']));
		return $data;
	}

	/**
     * Sets foreign language for PDF.
     *
     * @return array
     */
	public function setForeignLangForPdf($data, $id_lang) : array
	{
		$lang = new DictForeignLangs();
		$lang->id = $id_lang;
		$lang_row = $lang->get();
		$data['foreign_lang'] = $lang_row['description'];
		return $data;
	}

	/**
     * Sets campus data for PDF.
     *
     * @return array
     */
	public function setCampusForPdf($data, $campus) : array
	{
		if ($campus == 0) {
			$data['campus_no'] = 'On';
		} else {
			$data['campus_yes'] = 'On';
		}
		return $data;
	}

	/**
     * Sets individual achievments data for PDF.
     *
     * @return array
     */
	public function setIaForPdf($data, $app) : array
	{
		$ia = new ApplicationAchievs();
		$ia->pid = $app;
		$ia_arr = $ia->getByAppForPdf();
		if ($ia_arr) {
			$data['ia_yes'] = 'On';
			foreach ($ia_arr as $ia_row) {
				switch ($ia_row['code']) {
					case $ia::IA_GTO:
						$data['ia_gto'] = 'On';
						break;
					case $ia::IA_MEDAL_CERTIFICATE:
						$data['ia_medal_certificate'] = 'On';
						break;
					case $ia::IA_MEDAL_DIPLOMA:
						$data['ia_medal_diploma'] = 'On';
						break;
					case $ia::IA_CONTEST_RUS:
						$data['ia_contest_rus'] = 'On';
						break;
					case $ia::IA_CONTEST_BSU:
						$data['ia_contest_bsu'] = 'On';
						break;
					case $ia::IA_SPORTMASTER:
						$data['ia_sportmaster'] = 'On';
						break;
					case $ia::IA_GRANTS_PRESIDENT:
						$data['ia_grants_president'] = 'On';
						break;
					case $ia::IA_GRANTS_NAMED:
						$data['ia_grants_named'] = 'On';
						break;
					case $ia::IA_DOCSEDUC_MEDAL:
						$data['ia_docseduc_medal'] = 'On';
						break;
					case $ia::IA_MEDIC_LOCATIONS:
						$data['ia_medic_locations'] = 'On';
						break;
					case $ia::IA_ARTICLES_WORLD:
					case $ia::IA_ARTICLES_RUS:
						$data['ia_articles'] = 'On';
						break;
					case $ia::IA_ARTICLES_VAK_NO:
						$data['ia_articles_vak_no'] = 'On';
						break;
					case $ia::IA_ARTICLES_VAK_YES:
						$data['ia_articles_vak_yes'] = 'On';
						break;
				}
			}
		} else {
			$data['ia_no'] = 'On';
		}
		return $data;
	}
}
