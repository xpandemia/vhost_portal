<?php

namespace frontend\controllers;

use common\models\Model_ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams;
use common\models\Model_DictDiscipline;
use common\models\Model_DictTestingScopes;
use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;
use frontend\models\Model_Application as Model_Application;
use frontend\models\Model_ApplicationSpec as Model_ApplicationSpec;
use common\models\Model_Application as Common_Model_Application;

include ROOT_DIR.'/application/frontend/models/Model_ApplicationSpec.php';

class Controller_Application extends Controller
{
	/*
		Application actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_Application();
		$this->app = new Common_Model_Application();
		$this->view = new View();
		// check resume
		$resume = new Resume();
		$resume_row = $resume->getStatusByUser();
		if ($resume_row) {
			if ($resume_row['status'] == $resume::STATUS_CREATED || $resume_row['status'] == $resume::STATUS_SAVED) {
				return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Анкета ещё не отправлена!');
			}
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Анкета ещё не создана!');
		}
		// check education documents
		$docs = new DocsEduc();
		$docs_row = $docs->getByUser();
		if (!$docs_row) {
			return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Нет ни одного документа об образовании!');
		}
	}

	/**
     * Displays application page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('application.php', 'main.php', 'Заявления');
	}

	/**
     * Resets application page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->view->generate('application-add.php', 'form.php', APP['hdr'], $this->form);
	}

	/**
     * Displays application add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('application-add.php', 'form.php', APP['hdr'], $this->form);
	}

	/**
     * Shows application specialities.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			$spec = new Model_ApplicationSpec();
			$spec_row = $spec->get($id);
			$this->form = $this->model->setForm($spec->rules(), $spec_row);
			$this->form['id'] = $id;
			return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Редактирование заявления: Отсутствует идент-р заявления!');
		}
	}

	public function actionChangeExam()
    {
    	$result = "fail";
        if(isset($_POST['app_place_id']) && isset($_POST['disc_code']) && isset($_POST['new_code'])
            && !empty($_POST['app_place_id']) && !empty($_POST['disc_code']) && !empty($_POST['new_code'])) 
        {
            
    		$app = new Common_Model_Application();
    		$app->id_user = $_SESSION[APP_CODE]['user_id'];
    		$app_row = $app->getByPlaceId($_POST['app_place_id']);

    		if ($app_row && ($app_row['status'] == $app::STATUS_CREATED || $app_row['status'] == $app::STATUS_SAVED || $app_row['status'] == $app::STATUS_CHANGED))
    		{    		     		
	            $app_place_id = $_POST['app_place_id'];
	            $disc_code = $_POST['disc_code'];
	            $new_code = $_POST['new_code'];

	            $dict_tests = new Model_DictTestingScopes();
	            $dict_tests->code = $new_code;
	            $dict_test_id = $dict_tests->getByCode();

	            $app_place_dao = new Model_ApplicationPlaces();
	            $app_place_dao->id = $app_place_id;
	            $app_place = $app_place_dao->get();

                $app_exams_dao = new Model_ApplicationPlacesExams();
                $app_exams_dao->pid = $app_place['id'];
                $app_exams = $app_exams_dao->getExamsForChange($disc_code);

                foreach ($app_exams as $app_exam) {
                    $app_exams_dao->id = $app_exam['id'];
                    $app_exams_dao->id_test = $dict_test_id['id'];
                    $app_exams_dao->changeTest();
                }
                $result = "ok";
			}
        }

        print($result);
        die();
    }
    
    // begin Ильяшенко 08.02.2021
    //ajax call
    public function actionChooseExam(){
    	$result = "fail";
    	if (isset($_POST['app_place_id']) && isset($_POST['disc_code']) && !empty($_POST['app_place_id']) && !empty($_POST['disc_code'])){
    		$app = new Common_Model_Application();
    		$app->id_user = $_SESSION[APP_CODE]['user_id'];
    		$app_row = $app->getByPlaceId($_POST['app_place_id']);
    		if ($app_row && ($app_row['status'] == $app::STATUS_CREATED || $app_row['status'] == $app::STATUS_SAVED || $app_row['status'] == $app::STATUS_CHANGED))
    		{
	    		$disc = new Model_DictDiscipline();
	    		$disc->code = $_POST['disc_code'];
	    		$disc_row = $disc->getByCode();
	    		if ($disc_row)
	    		{
	    			$app_place = new Model_ApplicationPlacesExams();
	    			$app_place->pid = $_POST['app_place_id'];
	    			$app_place->id_user = $_SESSION[APP_CODE]['user_id'];
	    			$app_place->id_discipline = $disc_row['id'];
	    			$result = $app_place->changeSelectiveExam();
	    			if ($result !== FALSE){
	    				$result = "ok";	
	    			}
	    		}
			}
    	}
    	print($result);
    	die();
    }
    // end Ильяшенко 08.02.2021
    
    public function actionRecall()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = htmlspecialchars($_GET['id']);
            $spec = new Model_ApplicationSpec();
            $spec_row = $spec->get($id);
            $this->form = $this->model->setForm($spec->rulesExtra(), $spec_row);
            $this->form['id'] = $id;
            return $this->view->generate('application-recall.php', 'main.php', 'Заявление', $this->form);
        }
    }

	/**
     * Calls to application delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
	    $this->app->id = htmlspecialchars($_GET['id']);
	    $app = $this->app->get();
	    
	    if($app["status"] == 2) {
	        return $this->actionRecall();
        }
	    
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

    /**
     * Displays document delete confirmation page.
     *
     * @return mixed
     */
    function actionDelDocConfirm($form, $get)
    {
        if (isset($get['id']) && !empty($get['id'])) {
            $form['id'] = htmlspecialchars($get['id']);
        } else {
            exit("<p><strong>Ошибка!</strong> Отсутствует идент-р документа!</p>");
        }
        if (isset($get['pid']) && !empty($get['pid'])) {
            $form['pid'] = htmlspecialchars($get['pid']);
        } else {
            $form['pid'] = null;
        }
        if (isset($get['hdr']) && !empty($get['hdr'])) {
            $form['hdr'] = htmlspecialchars($get['hdr']);
        } else {
            exit("<p><strong>Ошибка!</strong> Отсутствует заголовок документа!</p>");
        }
        if (isset($get['ctr']) && !empty($get['ctr'])) {
            $form['ctr'] = htmlspecialchars($get['ctr']);
        } else {
            exit("<p><strong>Ошибка!</strong> Отсутствует контроллер документа!</p>");
        }
        $form['error_msg'] = null;
        return $this->view->generate('delete-confirm.php', 'form.php', 'Удаление документа '.$form['ctr'], $form);
    }

	/**
     * Deletes application.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
		$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
		if ($this->model->delete($this->form)) {
			Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', $_SESSION[APP_CODE]['success_msg']);
		} else {
			Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', null, $_SESSION[APP_CODE]['error_msg']);
		}
	}

	/**
     * Saves application.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect('Заявления', 200, APP['ctr'], 'Index', 'Создано новое заявление.');
			}
		} else {
			if (empty($this->form['error_msg'])) {
				$this->form['error_msg'] = '<strong>Ошибка при проверке данных заявления!</strong> Пожалуйста, проверьте все поля ввода.';
			}
		}
		return $this->view->generate('application-add.php', 'form.php', APP['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
