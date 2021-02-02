<?php

namespace frontend\controllers;

use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use frontend\models\Model_ApplicationSpec as Model_ApplicationSpec;
use tinyframe\core\Controller as Controller;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\View as View;

include ROOT_DIR.'/application/frontend/models/Model_Application.php';

class Controller_ApplicationSpec
    extends Controller
{
    /*
        Application spec actions
    */
    
    public $form;
    
    public function __construct()
    {
        $this->model = new Model_ApplicationSpec();
        $this->view  = new View();
    }
    
    /**
     * Synchronizes individual achievments for application.
     *
     * @return mixed
     */
    public function actionSyncIa()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id               = htmlspecialchars($_GET['id']);
            $spec_row         = $this->model->get($id);
            $this->form       = $this->model->setForm($this->model->rules(), $spec_row);
            $this->form['id'] = $id;
            $this->form       = $this->model->syncIa($this->form);
            
            return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
        }
    
        Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', NULL, 'Синхронизация ИД: Отсутствует идент-р заявления!');
        return NULL;
    }
    
    /**
     * Cancels application spec page.
     *
     * @return mixed
     */
    public function actionCancel()
    {
        return Basic_Helper::redirect('Заявления', 200, APP['ctr'], 'Index');
    }
    
    /**
     * Displays application places add page.
     *
     * @return mixed
     */
    public function actionAddPlaces()
    {
        if( isset($_GET['pid']) && !empty($_GET['pid']) ) {
            $this->form['pid']         = htmlspecialchars($_GET['pid']);
            $this->form['error_msg']   = NULL;
            $this->form['success_msg'] = NULL;
            
            return $this->view->generate('application-places-add.php', 'form.php', 'Выбор направлений подготовки', $this->form);
        } else {
            return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', NULL, 'Выбор направления подготовки: Отсутствует идент-р заявления!');
        }
    }
    
    /**
     * Cancels application places add page.
     *
     * @return mixed
     */
    public function actionCancelPlaces()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id = htmlspecialchars($_GET['id']);
            
            return Basic_Helper::redirect('Заявления', 200, APP['ctr'], 'Edit/?id='.$id);
        } else {
            return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', NULL, 'Отмена редактирования направлений подготовки заявления: Отсутствует идент-р заявления!');
        }
    }
    
    /**
     * Saves application spec places.
     *
     * @return mixed
     */
    public function actionSavePlaces()
    {
        $this->form = $this->model->checkPlaces($_POST);
        if( !$this->form['error_msg'] ) {
            return Basic_Helper::redirect(APP['hdr'], 200, APP['ctr'], 'Edit/?id='.$this->form['pid'], 'Направления подготовки выбраны.');
        }
        
        return $this->view->generate('application-places-add.php', 'form.php', 'Выбор направлений подготовки', $this->form);
    }
    
    /**
     * Saves application spec.
     *
     * @return mixed
     * @throws \tinyframe\core\exceptions\UploadException
     */
    public function actionSave()
    {
        $id               = htmlspecialchars($_POST['id']);
        $this->form       = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
        $this->form['id'] = $id;
        $this->form = $this->model->getExams($this->form);
        $this->form = $this->model->saveExams($this->form);
        $this->form = $this->model->validateForm($this->form, $this->model->rules());
        $this->form = $this->model->validateFormAdvanced($this->form);
        if( $this->form['validate'] ) {
            $this->form = $this->model->check($this->form);
            if( !$this->form['error_msg'] ) {
                return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
            }
        } else {
            if( empty($this->form['error_msg']) ) {
                $this->form['error_msg'] = '<strong>Ошибка при проверке данных заявления!</strong> Пожалуйста, проверьте все поля ввода.';
            }
        }
        $this->form = $this->model->unsetScans($this->form);
        
        return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
    }
    
    /**
     * Sends application.
     */
    public function actionSend()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id               = htmlspecialchars($_GET['id']);
            $spec_row         = $this->model->get($id);
            $this->form       = $this->model->setForm($this->model->rules(), $spec_row);
            $this->form['id'] = $id;
            $this->form       = $this->model->send($this->form);
            
            return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
        }
    
        Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', NULL, 'Отправка заявления: Отсутствует идент-р заявления!');
        
        return NULL;
    }
    
    /**
     * Changes application.
     *
     * @return mixed
     */
    public function actionChange()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id               = htmlspecialchars($_GET['id']);
            $spec_row         = $this->model->get($id);
            $this->form       = $this->model->setForm($this->model->rules(), $spec_row);
            $this->form['id'] = $id;
            $this->form       = $this->model->change($this->form);
            
            return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
        }
    
        return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', NULL, 'Изменение завяления: Отсутствует идент-р заявления!');
    }
    
    /**
     * Recalls application.
     *
     * @return mixed
     */
    public function actionRecall()
    {
        if( isset($_GET['id']) && !empty($_GET['id']) ) {
            $id               = htmlspecialchars($_GET['id']);
            $spec_row         = $this->model->get($id);
            $this->form       = $this->model->setForm($this->model->rulesExtra(), $spec_row);
            $this->form['id'] = $id;
            $this->form       = $this->model->recall($this->form);
            
            return $this->view->generate('application.php', 'main.php', 'Заявление', $this->form);
        }
    
        if( isset($_POST['id']) && !empty($_POST['id']) ) {
            $id       = htmlspecialchars($_POST['id']);
            $spec_row = $this->model->get($id);
            
            $this->form       = $this->model->setForm($this->model->rulesExtra(), $spec_row);
            $this->form['id'] = $id;
            
            $this->form = $this->model->recall($this->form, $_FILES);
            
            return Basic_Helper::redirect(APP_NAME, 200, APP['ctr'], 'Index', "Успешно отозвано");
        }
    
        return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', NULL, 'Отзыв заявления: Отсутствует идент-р заявления!');
    }
    
    /**
     * Saves application spec as PDF.
     *
     * @return mixed
     */
    public function actionSavePdf()
    {
        if( isset($_GET['pid']) && !empty($_GET['pid']) ) {
            $id         = htmlspecialchars($_GET['pid']);
            $place      = new ApplicationPlaces();
            $place->pid = $id;
            if( $place->getSpecsByApp() ) {
                $this->model->savePdf(htmlspecialchars($id));
            } else {
                $spec_row                = $this->model->get($id);
                $this->form              = $this->model->setForm($this->model->rules(), $spec_row);
                $this->form['id']        = $id;
                $this->form['error_msg'] = 'Направления подготовки не выбраны!';
                
                return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
            }
        } else {
            return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', NULL, 'Сохранение файла заявления: Отсутствует идент-р заявления!');
        }
    }
    
    public function __destruct()
    {
        $this->model = NULL;
        $this->view  = NULL;
    }
}
