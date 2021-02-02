<?php

namespace frontend\controllers;

use common\models\Model_Application;
use common\models\Model_ApplicationConfirmPlaces;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_Features;
use common\models\Model_PrivillegeQuota;
use common\models\Model_Resume as Resume;
use frontend\models\Model_ApplicationConfirm;
use tinyframe\core\Controller;
use tinyframe\core\exceptions\UploadException;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\helpers\PDF_Helper;
use tinyframe\core\View;

include ROOT_DIR.'/application/frontend/models/Model_ApplicationSpec.php';

class Controller_ApplicationConfirm
    extends Controller
{
    public $form;
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_ApplicationConfirm();
        $this->view  = new View();
        // check resume
        $resume     = new Resume();
        $resume_row = $resume->getStatusByUser();
        if( $resume_row ) {
            if( $resume_row['status'] == $resume::STATUS_CREATED || $resume_row['status'] == $resume::STATUS_SAVED ) {
                Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', NULL, 'Анкета ещё не отправлена!');
            }
        } else {
            Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', NULL, 'Анкета ещё не создана!');
        }
        // check education documents
        $docs     = new DocsEduc();
        $docs_row = $docs->getByUser();
        if( !$docs_row ) {
            Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', NULL, 'Нет ни одного документа об образовании!');
        }
    }
    
    /**
     * Displays application page.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->view->generate('application-confirm.php', 'main.php', 'Заявления');
    }
    
    /**
     * Resets application page.
     *
     * @return mixed
     */
    public function actionReset()
    {
        $this->form = $this->model->resetForm(TRUE, $this->form, $this->model->rules('application_confirm'));
        
        return $this->view->generate('application-confirm-add.php', 'form.php', APP_CONFIRM['hdr'], $this->form);
    }
    
    /**
     * Displays application add page.
     *
     * @return mixed
     */
    public function actionAdd()
    {

        if( isset($_GET['app_id']) && !empty($_GET['app_id']) ) {
            $app = new Model_Application();
            $app->id = $_GET['app_id'];
            $data = $app->get();

            if(!is_array($data) || count($data) == 0) {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отправка согласия на зачисление: Такого заявления-основания не существует!');
                return NULL;
            }

            $_conf = new \common\models\Model_ApplicationConfirm();
            $valid_count = $_conf->getValidCount()[$data['id_campaign']];

            if ((count($valid_count['ready']) + count($valid_count['recalled'])) > 1) {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отправка согласия на зачисление: Вы уже подали два согласия на зачисление на эту приемную компанию и больше не можете подавать новые!');
                return NULL;
            }

            if($data['id_user'] != $_SESSION[APP_CODE]['user_id']) {
                if(user_can_debug()) {
                    die();
                }
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отправка согласия на зачисление: Вы пытаетесь подать согласие на заявление, которое было подано другим человеком!');
                return NULL;
            }

            if($data['type'] == 3) {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отправка согласия на зачисление: Нельзя подать согласие на зачисление на основе отзыва заявления!');
                return NULL;
            }

            if($data['status'] != 2) {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отправка согласия на зачисление: согласие можно подавать только на основе принятого заявления!');
                return NULL;
            }

            if($this->model->hasActiveConfirmByApp($_GET['app_id'])) {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Создание согласия на зачисление: Согласие на зачисление по этому заявлению уже создано!');
                return NULL;
            }

            $id = $this->model->formPost($_GET['app_id']);
            
            if( $id !== NULL ) {
                $spec      = new Model_ApplicationConfirm();
                $spec_row         = $spec->get($id);

                if($spec_row['type'] == \common\models\Model_ApplicationConfirm::TYPE_NEW) {
                    $_filter = 'application_confirm';
                } else {
                    $_filter = 'app_confirm_recall';
                }
    
                $this->form       = $this->model->setForm($spec->rules($_filter), $spec_row);
                $this->form['id'] = $id;
                
                return $this->view->generate('application-confirm-add.php', 'main.php', 'Заявление', $this->form);
            } else {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Создание согласия на зачисление: Системная ошибка при создании заявления!');
                return NULL;
            }
        }

        Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Создание согласия на зачисление: Отсутствует идент-р заявления!');
        return NULL;
    }

    public function actionAddConfirm() {
        //application-confirm-add-agree
    }
    
    /**
     * Shows application specialities.
     *
     * @return mixed
     */
    public function actionEdit()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id               = htmlspecialchars($_GET['id']);
            $app_conf             = new Model_ApplicationConfirm();
            $app_conf_row         = $app_conf->get($id);
            
            if($app_conf_row['type'] == \common\models\Model_ApplicationConfirm::TYPE_NEW) {
                $_filter = 'application_confirm';
            } else {
                $_filter = 'app_confirm_recall';
            }

            $this->form       = $this->model->setForm($app_conf->rules($_filter), $app_conf_row);
            $this->form['id'] = $id;
            
            return $this->view->generate('application-confirm-add.php', 'main.php', 'Заявление', $this->form);
        }
        Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Редактирование: Отсутствует идент-р заявления!');
        
        return NULL;
    }
    
    /**
     * Deletes application.
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $this->form['id']  = htmlspecialchars($_GET['id']);
        $this->form['hdr'] = htmlspecialchars(APP_CONFIRM['hdr']);
        $this->form['ctr'] = htmlspecialchars(APP_CONFIRM['ctr']);
        
        if( $this->model->delete($this->form) ) {
            Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', $_SESSION[APP_CODE]['success_msg']);
        } else {
            Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', NULL, $_SESSION[APP_CODE]['error_msg']);
        }
        
        return NULL;
    }
    
    /**
     * Deletes application.
     *
     * @return mixed
     */
    public function actionRecall()
    {
        if( ( isset($_GET['id']) && !empty($_GET['id']) ) ) {
            $id       = htmlspecialchars($_GET['id']);
            $spec_row = $this->model->getByUserAndId($id);
            if( is_array($spec_row) && count($spec_row) > 0 && $spec_row['type'] == 0 && $spec_row['active'] == 1) {
                $app     = new \common\models\Model_ApplicationConfirm();
                $app->id = $_GET['id'];
                $recall_id = $app->recall();
                if( $recall_id ) {
                    $_GET['id'] = $recall_id;
                    return $this->actionEdit();
                }
                
                return $this->view->generate('application-confirm.php', 'main.php', 'Заявление', $this->form);
            }
        }
        
        return FALSE;
    }
    
    public function actionFreeze()
    {
        $app_confirm_places            = new Model_ApplicationConfirmPlaces();
        $user_app_confirm_places_array = $app_confirm_places->getByApp($_POST['app_id']);
        
        $is_frozen = FALSE;
        foreach($user_app_confirm_places_array as $user_app_confirm_place) {
            if($user_app_confirm_place['selected'] == 1) {
                $is_frozen = TRUE;
            }
        }
        
        if( !$is_frozen ) {
            foreach( $user_app_confirm_places_array as $user_app_confirm_place ) {
                if( $user_app_confirm_place['id'] == $_POST['spec'] ) {
                    $_t                         = new Model_ApplicationConfirmPlaces();
                    $_t->id                     = $user_app_confirm_place['id'];
                    $_t->id_application_place   = $user_app_confirm_place['id_application_place'];
                    $_t->id_application_confirm = $user_app_confirm_place['id_application_confirm'];
                    $_t->selected               = 1;
                    $_t->changeAll();
                    
                    $app_conf = new \common\models\Model_ApplicationConfirm();
                    $app_conf->id = $user_app_confirm_place['id_application_confirm'];
                    $app_conf->changeSingle('id_status', $app_conf::STATUS_SAVED);
                }
            }
        }
        
        $_GET['id'] = $_POST['app_id'];
        
        return $this->actionEdit();
    }
    
    /**
     * Sends application.
     *
     * @return mixed
     * @throws UploadException
     */
    public function actionSend()
    {
        if( isset($_POST['id']) && !empty($_POST['id']) ) {
            $id         = htmlspecialchars($_POST['id']);
            $spec_row   = $this->model->getByUserAndId($id);

            $_conf = new \common\models\Model_ApplicationConfirm();
            $valid_count = $_conf->getValidCount()[$spec_row['id_campaign']];

            if ($spec_row['type'] == 0 && (count($valid_count['ready']) + count($valid_count['recalled'])) > 1) {
                Basic_Helper::redirect(APP_NAME, 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отправка согласия на зачисление: Вы уже подали два согласия на зачисление на эту приемную компанию и больше не можете подавать новые!');
                return NULL;
            }
    
            if($spec_row['type'] == \common\models\Model_ApplicationConfirm::TYPE_NEW) {
                $_filter = 'application_confirm';
            } else {
                $_filter = 'app_confirm_recall';
            }

            $rules = $this->model->rules($_filter);
            
            $this->form = $this->model->getForm($rules, $spec_row, $_FILES);
            $this->form = $this->model->validateForm($this->form, $rules);
            
            if( $this->form['validate'] ) {
                $this->form['id'] = $id;
                $this->form       = $this->model->send($this->form);
                if( !$this->form['error_msg'] ) {
                    return $this->view->generate('application-confirm.php', 'main.php', 'Заявление', $this->form);
                }
            } elseif( empty($this->form['error_msg']) ) {
                $this->form['error_msg'] = '<strong>Ошибка при проверке данных заявления!</strong> Пожалуйста, проверьте все поля ввода.';
            }
            
            return $this->view->generate('application-confirm.php', 'main.php', 'Заявление', $this->form);
        }
        
        Basic_Helper::redirect('Заявления', 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Отсутствует идент-р заявления!');
        //Basic_Helper::redirect('Заявления', 202, APP_CONFIRM['ctr'], 'Index', NULL, 'Сроки подачи согласий закончились!');

        return NULL;
    }
    
    /**
     * Saves application spec data as PDF.
     *
     * @return mixed
     */
    public function actionSavePdf()
    {
        $pdf         = new PDF_Helper();
        $confirm     = new \common\models\Model_ApplicationConfirm();
        $confirm->id = $_GET['id'];
        $confirm_row = $confirm->get();

        if(is_array($confirm_row) && count($confirm_row)) {
            $selected = $confirm->getSelectedPlace();

            if ($confirm_row['type'] == 1) {
                $isRecall = TRUE;
            } else {
                $isRecall = FALSE;
            }

            $app = new Model_Application();
            $app->id = $confirm_row['id_application'];
            $app_row = $app->get();

            $data = [];

            $data = $this->setAppForPdf($data, $app_row);
            $data = $this->setResumeForPdf($data);
            $data = $this->setBodyForPdf($data, $selected);

            if (!$isRecall) {
                $template_name = $this->getTemplateName($selected);

                $pdf->create($data, $template_name, 'Согласие');
            } else {
                $template_name = $this->getTemplateRecallName($selected);
                $pdf->create($data, $template_name, 'Отзыв_согласия');
            }
        }
        
        return NULL;
    }
    
    /**
     * Sets application data for PDF.
     *
     * @return array
     */
    public function setAppForPdf( $data, $app_row ): array
    {
        $app              = new Model_Application();
        $data['app_numb'] = $app_row['numb'];
        if( $app_row['type'] == $app::TYPE_RECALL ) {
            $resume                 = new Resume();
            $resume_row             = $resume->getByUser();
            $data['recall_fio']     = $resume_row['name_last'].' '.$resume_row['name_first'].$resume_row['name_middle'];
            $data['recall_dt']      = date('d.m.Y');
            $data['recall_dt_day']  = date('d');
            $data['recall_dt_body'] = $this->getMothName(date('m'));
        } else {
            $data['dt']      = date('d.m.Y');
        }
        
        return $data;
    }
    
    /**
     * Sets resume data for PDF.
     *
     * @return array
     */
    public function setResumeForPdf( $data ): array
    {
        $resume     = new Resume();
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
            'phone_main' => ( ( !empty($resume_row['phone_mobile']) ) ? $resume_row['phone_mobile'] : $resume_row['phone_home'] ),
            'phone_add' => $resume_row['phone_add'],
            'email' => $resume_row['email'],
            'address_res' => $resume_row['address_res']
        ];
        
        return array_merge($data, $resume_arr);
    }
    
    public function setBodyForPdf( $data, $row )
    {
        $data['eduform'] = $row['eduform_name'];
        $data['place']   = $row['speciality_name'].(isset($row['profil_name']) ? ' '.$row['profil_name'] : '');
        $data['app_dt']  = date('d.m.Y');
        
        return $data;
    }
    
    private function getMothName( $date )
    {
        switch ( $date ) {
            case 1:
                return "января";
                break;
            case 2:
                return "февраля";
                break;
            case 3:
                return "марта";
                break;
            case 4:
                return "апреля";
                break;
            case 5:
                return "мая";
                break;
            case 6:
                return "июня";
                break;
            case 7:
                return "июля";
                break;
            case 8:
                return "августа";
                break;
            case 9:
                return "сентября";
                break;
            case 10:
                return "октября";
                break;
            case 11:
                return "ноября";
                break;
            case 12:
                return "декабря";
                break;
            default:
                return "ньяль";
                break;
        }
    }
    
    public function __destruct()
    {
        $this->model = NULL;
        $this->view  = NULL;
        parent::__destruct();
    }
    
    private function getTemplateName( $selected )
    {
        $root = 'confirm/confirm_';
        
        switch ( $selected['finance_code'] ) {
            case '000000001':
                $root .= 'target';
                break;
            case '000000002':
                $root .= 'pay';
                break;
            case '000000003':
                $root .= 'budget';
                
                $quota_priv     = new Model_PrivillegeQuota();
                $quota_priv_row = $quota_priv->getFirstByUser();
                
                if( is_array($quota_priv_row) && count($quota_priv_row) > 0 ) {
                    $root .= '_quota';
                } else {
                    $feature     = new Model_Features();
                    $feature_row = $feature->getFirstByUser();
                    
                    if( is_array($feature_row) && count($feature_row) > 0 ) {
                        $root .= '_no_exam';
                    }
                }
                break;
        }
        
        $root .= $this->checkMed($selected);
        
        return $root;
    }
    
    
    private function getTemplateRecallName( array $selected )
    {
        $root = 'confirm/recall/confirm_recall_';
        
        switch ( $selected['finance_code'] ) {
            case '000000002':
                $root .= 'pay';
                break;
            case '000000001':
            case '000000003':
                $root .= 'budget';
                break;
        }
        
        return $root;
    }
    
    private function checkMed( $selected )
    {
        $spec_code = mb_substr($selected['speciality_name'], 0, 8);
        
        if( in_array($spec_code, [ '31.05.01', '31.05.02', '32.05.01', '31.05.03', '33.05.01', '21.05.02', '21.05.04', '44.03.01', '44.03.03', '19.03.04' ]) ) {
            return '_med';
        }
        
        return '';
    }
}
