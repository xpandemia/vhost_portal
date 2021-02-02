<?php

namespace frontend\controllers;

require_once (ROOT_DIR.'/application/common/models/Model_Features.php');
require_once (ROOT_DIR.'/application/common/models/Model_PrivillegeQuota.php');
require_once (ROOT_DIR.'/application/common/models/Model_PrivillegeAdvanced.php');
require_once (ROOT_DIR.'/application/frontend/models/Model_Features.php');


use common\models\Model_Resume as Resume;
use frontend\models\Model_Features;
use tinyframe\core\Controller as parentAlias;
use tinyframe\core\exceptions\UploadException;
use tinyframe\core\helpers\Basic_Helper;
use tinyframe\core\View;

class Controller_Features
    extends parentAlias
{
    public $form;
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_Features();
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
    }
    
    /**
     * Displays individual achievments page.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->view->generate('features.php', 'main.php', 'Отличительные признаки');
    }
    
    /**
     * Resets individual achievments add page.
     *
     * @return mixed
     */
    public function actionReset()
    {
        $this->form = $this->model->resetForm(TRUE, $this->form, $this->model->rules());
        
        return $this->view->generate('features-add.php', 'form.php', FEATURES['hdr'], $this->form);
    }
    
    /**
     * Displays individual achievements add page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        if( !isset($this->form) ) {
            $this->form = $this->model->setForm($this->model->rules(), NULL);
        }
        
        return $this->view->generate('features-add.php', 'form.php', FEATURES['hdr'], $this->form);
    }
    
    /**
     * Shows individual achievement.
     *
     * @return mixed
     */
    public function actionEdit()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id = htmlspecialchars($_GET['id']);
        } else {
            exit('<p><strong>Ошибка!</strong> Отсутствует идент-р отличительного признака!</p>');
        }
        $this->form       = $this->model->setForm($this->model->rules(), $this->model->get($id));
        $this->form['id'] = $id;
        
        return $this->view->generate('features-add.php', 'form.php', 'Изменение отличительного признака', $this->form);
    }
    
    /**
     * Calls to individual achievement delete confirm.
     *
     * @return mixed
     */
    public function actionDeleteConfirm()
    {
        return $this->actionDelDocConfirm($this->form, $_GET);
    }
    
    /**
     * Deletes education document.
     *
     * @return mixed
     */
    public function actionDelete()
    {
        if( isset($_POST['id'], $_POST['hdr'], $_POST['ctr']) ) {
            $this->form['id']  = htmlspecialchars($_POST['id']);
            $this->form['hdr'] = htmlspecialchars($_POST['hdr']);
            $this->form['ctr'] = htmlspecialchars($_POST['ctr']);
            $this->form        = $this->model->delete($this->form);
            
            Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', $this->form['success_msg'], $this->form['error_msg']);
        } else {
            Basic_Helper::redirect('Индивидуальные достижения', 200, FEATURES['ctr'], 'Index', NULL, 'Ошибка удаления индивидуального достижения!');
        }
        
        return NULL;
    }
    
    /**
     * Saves individual achievement.
     *
     * @return mixed
     * @throws UploadException
     */
    public function actionSave()
    {
        $this->form       = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
        $this->form['id'] = htmlspecialchars($_POST['id']);
        
        $this->form       = $this->model->validateForm($this->form, $this->model->rules());
        if( $this->form['validate'] ) {
            $this->form = $this->model->check($this->form);
            if( !$this->form['error_msg'] ) {
                Basic_Helper::redirect('Индивидуальные достижения', 200, FEATURES['ctr'], 'Index', $this->form['success_msg']);
            }
        } elseif( empty($this->form['error_msg']) ) {
            $this->form['error_msg'] = '<strong>Ошибка при проверке данных индивидуального достижения!</strong> Пожалуйста, проверьте все поля ввода.';
        }
        
        $this->form = $this->model->unsetScans($this->form);
        
        return $this->view->generate('features-add.php', 'form.php', FEATURES['hdr'], $this->form);
    }
    
    /**
     * Cancels individual achievement.
     *
     * @return mixed
     */
    public function actionCancel()
    {
        Basic_Helper::redirect(FEATURES['hdr'], 200, FEATURES['ctr'], 'Index');
        
        return NULL;
    }
    
    public function __destruct()
    {
        parent::__destruct();
        $this->model = NULL;
        $this->view  = NULL;
    }
}
