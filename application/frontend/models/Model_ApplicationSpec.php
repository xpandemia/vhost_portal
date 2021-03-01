<?php /** @noinspection TypeUnsafeArraySearchInspection */

namespace frontend\models;

use common\models\Model_AdmissionCampaign as Model_AdmissionCampaign;
use common\models\Model_Application as Application;
use common\models\Model_ApplicationAchievs as ApplicationAchievs;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_DictCountries as DictCountries;
use common\models\Model_DictDiscipline as Model_DictDiscipline;
use common\models\Model_DictEge as DictEge;
use common\models\Model_DictEntranceExams as Model_DictEntranceExams;
use common\models\Model_DictForeignLangs as DictForeignLangs;
use common\models\Model_DictIndAchievs;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_DictSpeciality as Model_DictSpeciality;
use common\models\Model_DictTestingScopes as Model_DictTestingScopes;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_EgeDisciplines as Model_EgeDisciplines;
use common\models\Model_IndAchievs as IndAchievs;
use common\models\Model_Personal as Personal;
use common\models\Model_Resume as Resume;
use common\models\Model_Scans as Scans;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Files_Helper;
use tinyframe\core\helpers\PDF_Helper as PDF_Helper;
use tinyframe\core\Model as Model;

class Model_ApplicationSpec
    extends Model
{
    /*
        Application spec processing
    */
    
    const ЗАЯВЛЕНИЕ_БАКАЛАВРА  = 0;
    
    const ЗАЯВЛЕНИЕ_МАГИСТРА   = 1;
    
    const ЗАЯВЛЕНИЕ_АСПИРАНТА  = 2;
    
    const ЗАЯВЛЕНИЕ_ОРДИНАТОРА = 3;
    
    const ЗАЯВЛЕНИЕ_СПО        = 4;
    
    /**
     * Application spec rules.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'inila' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_INILA, 'msg' => 'Для СНИЛС можно использовать '.MSG_INILA.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 14, 'msg' => 'Слишком длинный СНИЛС!' ],
                'success' => 'СНИЛС заполнен верно.'
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
        $scans = Model_Scans::createRules('application');
        
        return array_merge($rules, $scans);
    }
    
    public function rulesExtra()
    {
        $rules       = [
            'inila' => [
                'type' => 'text',
                'class' => 'form-control',
                'pattern' => [ 'value' => PATTERN_INILA, 'msg' => 'Для СНИЛС можно использовать '.MSG_INILA.'!' ],
                'width' => [ 'format' => 'string', 'min' => 1, 'max' => 14, 'msg' => 'Слишком длинный СНИЛС!' ],
                'success' => 'СНИЛС заполнен верно.'
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
        $scans       = Model_Scans::createRules('application');
        $scans_extra = Model_Scans::createRules('application_recall');
        
        return array_merge($rules, array_merge($scans, $scans_extra));
    }
    
    /**
     * Validates resume advanced.
     *
     * @return array
     */
    public function validateFormAdvanced( $form )
    {
        $place      = new ApplicationPlaces();
        $place->pid = $form['id'];
        $exam       = new ApplicationPlacesExams();
        $exam->pid  = $form['id'];
        
        $form = $this->setFormErrorFile($form, 'photo3x4', 'Скан-копия "Фотография 3х4" обязательна для заполнения!');
        // application_2
        if( !$place->getByAppForSpecial() ) {
            $form = $this->setFormErrorFile($form, 'application_2', 'Скан-копия "Заявление о приеме в БелГУ (второй лист)" обязательна для заполнения!');
        }
        /*
        // medical_certificate
        if( $place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1() ) {
            $form = $this->setFormErrorFile($form, 'medical_certificate_face', 'Скан-копия "Медицинская справка (лицевая сторона)" обязательна для заполнения!');
            $form = $this->setFormErrorFile($form, 'medical_certificate_back', 'Скан-копия "Медицинская справка (оборотная сторона)" обязательна для заполнения!');
        }
        */
        $personal    = new Personal();
        $citizenship = $personal->getCitizenshipByUser();
        // inila
        if( $place->getByAppForClinical() && $citizenship['abroad'] == 0) {
            if( empty($form['inila']) ) {
                $form = $this->setFormErrorField($form, 'inila', 'СНИЛС обязателен для заполнения!');
            }
            $form = $this->setFormErrorFile($form, 'inila_face', 'Скан-копия "СНИЛС (лицевая сторона)" обязательна для заполнения!');
        }
        
        return $form;
    }
    
    /**
     * Gets application spec data from database.
     *
     * @return array
     */
    public function get( $id )
    {
        $app     = new Application();
        $app->id = $id;
        
        return $app->getSpec();
    }
    
    /**
     * Gets application places exams.
     *
     * @return array
     */
    public function getExams( $form )
    {
        foreach( $_POST as $key => $value ) {
            if( substr($key, 0, 4) == 'exam' ) {
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
    public function saveExams( $form )
    {
        $places      = new ApplicationPlaces();
        $places->pid = $form['id'];
        $places_arr  = $places->getSpecsByApp();
        if( $places_arr ) {
            foreach( $places_arr as $places_row ) {
                $exams      = new ApplicationPlacesExams();
                $exams->pid = $places_row['id'];
                $exams_arr  = $exams->getExamsByPlace();
                if( $exams_arr ) {
                    foreach( $exams_arr as $exams_row ) {
                        if( isset($form['exam'.$exams_row['discipline_code']]) && $exams_row['test_code'] != $form['exam'.$exams_row['discipline_code']] ) {
                            $exams->id      = $exams_row['id'];
                            $test           = new Model_DictTestingScopes();
                            $test->code     = $form['exam'.$exams_row['discipline_code']];
                            $test_row       = $test->getByCode();
                            $exams->id_test = $test_row['id'];
                            if( !$exams->changeTest() ) {
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
    public function unsetScans( $form )
    {
        $place      = new ApplicationPlaces();
        $place->pid = $form['id'];
        $exam       = new ApplicationPlacesExams();
        $exam->pid  = $form['id'];
        // main
        $form = Model_Scans::unsets('application', $form);
        // application_2
        if( !$place->getByAppForSpecial() ) {
            $form = $this->setFormErrorFile($form, 'application_2', 'Скан-копия "Заявление о приеме в БелГУ (второй лист)" обязательна для заполнения!');
        }
        // photo3x4
        if( $place->getByAppForBachelorSpec() && $exam->existsExams() ) {
            $form = $this->setFormErrorFile($form, 'photo3x4', 'Скан-копия "Фотография 3х4" обязательна для заполнения!');
        }
        // medical_certificate
        if( $place->getByAppForMedicalA1() || $place->getByAppForMedicalA2() || $place->getByAppForMedicalB1() || $place->getByAppForMedicalC1() ) {
            $form = $this->setFormErrorFile($form, 'medical_certificate_face', 'Скан-копия "Медицинская справка (лицевая сторона)" обязательна для заполнения!');
            $form = $this->setFormErrorFile($form, 'medical_certificate_back', 'Скан-копия "Медицинская справка (оборотная сторона)" обязательна для заполнения!');
        }
        // inila
        if( $place->getByAppForClinical() ) {
            $form = $this->setFormErrorFile($form, 'inila_face', 'Скан-копия "СНИЛС (лицевая сторона)" обязательна для заполнения!');
        }
        
        return $form;
    }
    
    /**
     * Checks application places data.
     *
     * @return array
     */
    public function checkPlaces( $post )
    {
        $form['pid'] = htmlspecialchars($post['pid']);
        
        $debug = $form['pid'] == 28699;
        
        $form['error_msg']   = NULL;
        $form['success_msg'] = NULL;
        // get app
        $app     = new Application();
        $app->id = $form['pid'];
        $app_row = $app->get();
        // get citizenship
        $personal    = new Personal();
        $citizenship = $personal->getCitizenshipByUser();
        $country     = new DictCountries();
        // get max_spec
        $adm     = new Model_AdmissionCampaign();
        $adm->id = $app_row['id_campaign'];
        $adm_row = $adm->getById();
        if( $adm_row ) {
            // get specs
            $spec_unique_arr = [];
            $spec_arr        = [];
            $exams_arr       = [];
            if( $debug ) {
                debug_print_object([ "POST data " => $post ]);
            }
            foreach( $post as $key => $value ) {
                if( $debug ) {
                    debug_print_object([ "CID data ".$key => $value ]);
                }
                if( substr($key, 0, 4) == 'spec' ) {
                    $_t = explode('-', $key);
                    
                    $spec     = new Model_DictSpeciality();
                    $spec->id = $value;
                    if( $debug ) {
                        debug_print_object(["SPEC id" => $spec->id]);
                    }
                    $spec_row = $spec->getById($debug);
                    
                    if( $debug ) {
                        debug_print_object(["SPEC row" => $spec_row]);
                    }
                    
                    if( $spec_row ) {
                        $place      = $spec_row['speciality_code'].( ( !empty($spec_row['profil_code']) ) ? $spec_row['profil_code'] : '' );
                        $spec_arr[] = [
                            $spec_row['id'],
                            $spec_row['campaign_code'],
                            $spec_row['curriculum_code'],
                            $spec_row['group_code'],
                            $spec_row['edulevel_code'],
                            $spec_row['eduform_code'],
                            $_t[1]
                        ];
                        if( array_search($place, $spec_unique_arr) === FALSE ) {
                            $spec_unique_arr[] = $place;
                        }
                    } else {
                        $form['error_msg'] = 'X1: Ошибка при получении данных направления подготовки с ID '.$value.'!';

                        if($debug) {
                            debug_print_object(['error_msg' => $form['error_msg']]);
                            die();
                        }

                        return $form;
                    }
                }
            }
            // check max_spec
            if( count($spec_unique_arr) <= $adm_row['max_spec'] ) {
                $places      = new ApplicationPlaces();
                $places->pid = $form['pid'];
                // clear specs
                $places->clearByApplication();
                // set specs
                foreach( $spec_arr as $spec_row ) {
                    $places->id_spec    = $spec_row[0];
                    $places->group_code = $spec_row[6];
                    $places->curriculum = $spec_row[2];
                    
                    $id = $places->save();
                    if( $id > 0 ) {
                        // get entrance exams
                        // bachelors, specialists, magisters, attending physicians
                        if( $spec_row[4] == '000000008' || $spec_row[4] == '000000031' || $spec_row[4] == '000000001' || $spec_row[4] == '000000002' || $spec_row[4] == '000000003' || $spec_row[4] == '000000005' ) {
                            $exams                = new Model_DictEntranceExams();
                            $exams->campaign_code = $spec_row[1];
                            $exams->group_code    = $spec_row[3];
                            
                            $exams_arr = $exams->getByCampaignGroup($debug);
                            
                            if( $debug ) {
                                debug_print_object(['Exams' => $exams_arr]);
                            }
                            
                            if( $exams_arr ) {
                                // set entrance exams
                                $enter      = new ApplicationPlacesExams();
                                $enter->pid = $id;
                                
                                $is_bad_ege  = FALSE;
                                $bad_ege_arr = [];
                                foreach( $exams_arr as $exams_row ) {
                                    $disc                = new Model_DictDiscipline();
                                    $disc->code          = $exams_row['exam_code'];
                                    $disc->campaign_code = $spec_row[1];
                                    $disc_row            = $disc->getOne($debug);
                                    
                                    if( $debug ) {
                                        debug_print_object(['exam_name' => $exams_row['exam_name'], 'disc_row' => $disc_row]);
                                    }
                                    
                                    $enter->id_discipline = $disc_row['id'];
                                    //begin Ильяшенко 08.02.2021
                                    $enter->selected = ApplicationPlacesExams::EXAM_REQUIRED;		// испытание нельзя выбирать
                                    if ($exams_row['alt_exam_code'] != 'NULL'){
                                    	$enter->selected = ApplicationPlacesExams::EXAM_NO_SELECTION; // испытание можно выбирать, но оно не выбрано
                                    }
                                    else{
                                    	$filter_ar = array_filter($exams_arr, function($item) use ($exams_row){
                                    		return $item['alt_exam_code'] === $exams_row['exam_code'];
                                    	}, ARRAY_FILTER_USE_BOTH);
                                    	if ($filter_ar){
                                    		$enter->selected = ApplicationPlacesExams::EXAM_SELECTION; // испытание можно выбирать и оно сейчас выбрано
                                    	}
                                    }
                                    //end Ильяшенко 08.02.2021
                                    $test                 = new Model_DictTestingScopes();
                                    
                                    $test_flag = 'K';

                                    if( $exams_row['exam_name'] !=  'Служебная' &&
                                        strripos($exams_row['exam_name'], 'Профессиональное испытание') === FALSE
                                        && strripos($exams_row['exam_name'], 'Творческое испытание') === FALSE
                                        && strripos($exams_row['exam_name'], 'Теория физической культуры') === FALSE ) {
                                        switch ( $spec_row[4] ) {
                                            // bachelors
                                            case '000000001':
                                             // specialists
                                            case '000000002':
                                                $ege = new Model_EgeDisciplines();
                                                if( $exams_row['exam_code'] == '000000021' ) {
                                                    // foreign language
                                                    $langs = new DictForeignLangs();
                                                    if( !empty($app_row['id_lang']) ) {
                                                        $langs->id = $app_row['id_lang'];
                                                        $langs_row = $langs->get();
                                                        if( $langs_row['description'] == 'Не изучал(а)' ) {
                                                            $form['error_msg'] = 'Вы не изучали иностранный язык!';
                                                            $places->clearByApplication();
                                                            
                                                            return $form;
                                                        }
                                                        $egedict              = new DictEge();
                                                        $egedict->description = $langs_row['description'].' язык';
                                                        $egedict_row          = $egedict->getByDescription();
                                                        $ege->code_discipline = $egedict_row['code'];
                                                    } else {
                                                        $form['error_msg'] = 'Вы не изучали иностранный язык!';
                                                        $places->clearByApplication();

                                                        if($debug) {
                                                            debug_print_object(['error_msg' => $form['error_msg']]);
                                                            die();
                                                        }
                                                        
                                                        return $form;
                                                    }
                                                } else {
                                                    // others
                                                    $ege->code_discipline = $exams_row['exam_code'];
                                                }
                                                
                                                $ege_arr = $ege->checkDiscipline();
                                                
                                                if( $debug ) {
                                                    echo "<pre>";
                                                    var_dump($ege_arr);
                                                    echo "</pre>";
                                                    
                                                    echo "<pre>";
                                                    var_dump($citizenship['abroad']);
                                                    echo "</pre>";
                                                    
                                                    echo "<pre>";
                                                    var_dump($app->checkCertificate());
                                                    echo "</pre>";
                                                }
                                                switch ( $citizenship['abroad'] ) {
                                                    case $country::ABROAD_HOME:
                                                        if( $app->checkCertificate() ) {
                                                            // russia with certificate - ege only
                                                            if( $ege_arr ) {
                                                                $test_flag       = 'W';
                                                                $test_row        = $test->getEge();
                                                                $enter->points   = $ege_arr['points'];
                                                                $enter->reg_year = $ege_arr['reg_year'];
                                                            } else {
                                                                $is_bad_ege    = TRUE;
                                                                $bad_ege_arr[] = $exams_row['exam_name'];
                                                            }
                                                        } else {
                                                            // russia without certificate - ege or exam
                                                            if( $ege_arr ) {
                                                                // ege
                                                                $test_flag       = 'A';
                                                                $test_row        = $test->getEge();
                                                                $enter->points   = $ege_arr['points'];
                                                                $enter->reg_year = $ege_arr['reg_year'];
                                                            } else {
                                                                // exam
                                                                $test_flag       = 'B';
                                                                $test_row        = $test->getExam();
                                                                $enter->points   = NULL;
                                                                $enter->reg_year = NULL;
                                                            }
                                                        }
                                                        break;
                                                    case $country::ABROAD_NEAR:
                                                        if( $ege_arr ) {
                                                            // ege
                                                            $test_flag       = 'C';
                                                            $test_row        = $test->getEge();
                                                            $enter->points   = $ege_arr['points'];
                                                            $enter->reg_year = $ege_arr['reg_year'];
                                                        } else {
                                                            // exam
                                                            $test_flag       = 'D';
                                                            $test_row        = $test->getExam();
                                                            $enter->points   = NULL;
                                                            $enter->reg_year = NULL;
                                                        }
                                                        break;
                                                    /*
                                                    // close foreigners: full-time - ege only, not full-time - ege or exam
                                                    if( $ege_arr ) {
                                                        $test_row        = $test->getEge();
                                                        $enter->points   = $ege_arr['points'];
                                                        $enter->reg_year = $ege_arr['reg_year'];
                                                    } else {
                                                        if( $spec_row[5] == '000000001' ) {
                                                            $form['error_msg'] = 'Нет подходящих результатов ЕГЭ по дисциплине "'.$exams_row['exam_name'].'"!';
                                                            $places->clearByApplication();
                                                            
                                                            return $form;
                                                        } else {
                                                            $test_row        = $test->getExam();
                                                            $enter->points   = NULL;
                                                            $enter->reg_year = NULL;
                                                        }
                                                    }
                                                    break;
                                                    */
                                                    case $country::ABROAD_FAR:
                                                        // far foreigners: exam only
                                                        if( $ege_arr ) {
                                                            // ege
                                                            $test_flag       = 'F';
                                                            $test_row        = $test->getEge();
                                                            $enter->points   = $ege_arr['points'];
                                                            $enter->reg_year = $ege_arr['reg_year'];
                                                        } else {
                                                            // exam
                                                            $test_flag       = 'E';
                                                            $test_row        = $test->getExam();
                                                            $enter->points   = NULL;
                                                            $enter->reg_year = NULL;
                                                        }
                                                        break;
                                                    default:
                                                        $form['error_msg'] = 'Зарубежье '.$citizenship['abroad'].' не описано!';
                                                        $places->clearByApplication();

                                                        if($debug) {
                                                            debug_print_object(['error_msg' => $form['error_msg']]);
                                                            die();
                                                        }
                                                        
                                                        return $form;
                                                        break;
                                                }
                                                break;
                                            // magisters
                                            case '000000003':
                                                $test_flag       = 'Z';
                                                $test_row        = $test->getExam();
                                                $enter->points   = NULL;
                                                $enter->reg_year = NULL;
                                                break;
                                            // attending physicians
                                            case '000000005':
                                                $test_flag       = 'Y';
                                                $test_row        = $test->getTest();
                                                $enter->points   = NULL;
                                                $enter->reg_year = NULL;
                                                break;
                                            case '000000031':
                                            case '000000008':
                                                $test_flag       = 'AD';
                                                $_t = $test->getTest();
                                                if(is_array($_t) && count($_t) > 0) {
                                                    $test_row = $_t;
                                                } else {
                                                    $_t = $test->getExam();
                                                    if(is_array($_t) && count($_t) > 0) {
                                                        $test_row = $_t;
                                                    }
                                                }

                                                $enter->points   = NULL;
                                                $enter->reg_year = NULL;
                                                break;

                                        }
                                    } else {
                                        $test_flag       = 'X';
                                        $test_row        = $test->getExam();
                                        $enter->points   = NULL;
                                        $enter->reg_year = NULL;
                                    }
                                    $enter->id_test = ( isset($test_row['id']) ? $test_row['id'] : -1 );
                                    
                                    if( $enter->id_test == -1 ) {
                                        $places->clearByApplication();

                                        if($debug) {
                                            debug_print_object(['error_msg' => 'id_test плохой. Этап '.$test_flag]);
                                            die();
                                        }

                                        return $form;
                                    }
                                    if(/*!($test_flag === 'W' || $test_flag === 'A' || $test_flag === 'F' || $test_flag === 'C')*/ TRUE ) {
                                        try {
                                            if( $enter->save() == 0 ) {
                                                $form['error_msg'] = 'Ошибка сохранения вступительного испытания с ID '.$enter->id_discipline.' для направления подготовки с ID '
                                                                     .$id
                                                                     .'! Источник ошибки - Узел "'.$test_flag.'"';
                                                $places->clearByApplication();
                                                return $form;
                                            }
                                        } catch ( \InvalidArgumentException $e ) {
                                            $places->clearByApplication();

                                            if($debug) {
                                                debug_print_object(['error_msg' => $e->getMessage()]);
                                                die();
                                            }

                                            return $form;
                                        }
                                    }
                                }
                                
                                if( $is_bad_ege ) {
                                    $bad_ege_str = '';
                                    foreach( $bad_ege_arr as $bad_ege_item ) {
                                        $bad_ege_str .= '"'.$bad_ege_item.'", ';
                                    }
                                    
                                    $bad_ege_str = mb_substr($bad_ege_str, 0, -2);
                                    
                                    $form['error_msg'] = 'Нет подходящих результатов ЕГЭ по дисциплине(нам) '.$bad_ege_str.'!';
                                    $places->clearByApplication();

                                    if($debug) {
                                        debug_print_object(['error_msg' => $form['error_msg']]);
                                        die();
                                    }
                                    
                                    return $form;
                                }
                            } else {

                                if($spec_row[4] != '000000031') {
                                $form['error_msg'] = 'Ошибка при получении вступительных испытаний направления подготовки с ID '.$value.'!';
                                $places->clearByApplication();

                                if($debug) {
                                    debug_print_object(['error_msg' => $form['error_msg']]);
                                    die();
                                }

                                return $form;
                                }
                            }
                        }
                    } else {
                            $form['error_msg'] = 'Ошибка при сохранении направления подготовки с ID ' . $spec_row[0] . '!';
                            $places->clearByApplication();

                            if ($debug) {
                                debug_print_object(['error_msg' => $form['error_msg']]);
                                die();
                            }

                            return $form;

                    }
                }
                
                if($_SESSION[APP_CODE]['is_admin']) {
                    echo 'Отладочная приостановка. Если вы не администратор и видите это сообщение - сообщите об этом службе поддержки<br>';
                }
            } else {
                $form['error_msg'] = 'Превышено кол-во направлений подготовки: выбрано '.count($spec_unique_arr).' при разрешённых '.$adm_row['max_spec'].'!';
            }
        } else {
            $form['error_msg'] = 'Ошибка при получении максимального числа направлений подготовки приёмной кампании с ID '.$form['pid'].'!';
        }
        
        if( !$form['error_msg'] ) {
            // clear scans
            $scans         = new Scans();
            $scans->id_row = $app->id;
            $scans->clearbyDoc('application');
            // change status
            if( $app_row['status'] != $app::STATUS_CREATED ) {
                $app->status = $app::STATUS_CREATED;
                $app->changeStatus();
                $form['status']         = $app->status;
                $applog                 = new ApplicationStatus();
                $applog->id_application = $app->id;
                $applog->create();
            }
        } else {
            if($debug) {
                debug_print_object(['breaking_error' => $form['error_msg']]);
                die();
            }
        }
        
        return $form;
    }
    
    /**
     * Synchronizes individual achievments for application.
     *
     * @return array
     */
    public function syncIa( $form )
    {
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        $app                 = new Application();
        $app->id             = $form['id'];
        $app_row             = $app->get();
        /* check status */
        if( $app_row['status'] != $app::STATUS_CREATED && $app_row['status'] != $app::STATUS_SAVED ) {
            $form['error_msg'] = 'Обновлять индивидуальные достижения можно только в заявлениях с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER,
                                                                                                                                        'UTF-8').'</strong>, <strong>'
                                 .mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        /* sync ia */
        $campaign     = new Model_AdmissionCampaign();
        $campaign->id = $app_row['id_campaign'];
        $campaign_row = $campaign->getById();
        $appia        = new ApplicationAchievs();
        $appia->pid   = $app_row['id'];
        $appia->clearByApplication();
        $ia                = new IndAchievs();
        $ia->campaign_code = $campaign_row['code'];
        $ia_arr            = $ia->getByUserCampaign();
        if( $ia_arr ) {
            foreach( $ia_arr as $ia_row ) {
                $appia->id_achiev = $ia_row['id'];
                $appia->save();
            }
        }
        Basic_Helper::msgReset();
        $form['success_msg'] = 'Индивидуальные достижения обновлены.';
        $form['error_msg']   = NULL;
        
        return $form;
    }
    
    /**
     * Checks application spec data.
     *
     * @return array
     */
    public function check( $form )
    {
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        $app                 = new Application();
        $app->id             = $form['id'];
        $app_row             = $app->get();
        /* check type */
        if( $app_row['type'] == $app::TYPE_RECALL ) {
            $form['error_msg'] = 'Нельзя сохранять заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        /* check status */
        if( $app_row['status'] != $app::STATUS_CREATED && $app_row['status'] != $app::STATUS_SAVED && $app_row['status'] != $app::STATUS_CHANGED ) {
            $form['error_msg'] = 'Сохранять можно только заявления с состоянием: <strong>'.mb_convert_case($app::STATUS_CREATED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'
                                 .mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>, <strong>'.mb_convert_case($app::STATUS_CHANGED_NAME, MB_CASE_UPPER,
                                                                                                                                         'UTF-8').'</strong>!';
            
            return $form;
        }
        /* check education document scans */
        if( Model_Scans::existsRequired('docs_educ', $app_row['id_docseduc']) === FALSE ) {
            $form['error_msg'] = 'В документ об образовании загружены не все обязательные скан-копии!';
            
            return $form;
        }
        /* check individual achievments scans */
        $ia      = new ApplicationAchievs();
        $ia->pid = $app->id;
        $ia_arr  = $ia->getByApp();
        if( $ia_arr ) {
            foreach( $ia_arr as $ia_row ) {
                if( Model_Scans::existsRequired('ind_achievs', $ia_row['id_achiev']) === FALSE ) {
                    $form['error_msg'] = 'В индивидуальное достижение № '.$ia_row['id_achiev'].' загружены не все обязательные скан-копии!';
                    
                    return $form;
                }
            }
        }
        /* application */
        $app->id_docseduc = $app_row['id_docseduc'];
        $app->id_docship  = $app_row['id_docship'];
        $app->id_lang     = $app_row['id_lang'];
        $app->status      = $app::STATUS_SAVED;
        // additional info
        $app->inila  = ( ( empty($form['inila']) ) ? NULL : $form['inila'] );
        $app->campus = ( ( $form['campus'] == 'checked' ) ? 1 : 0 );
        $app->conds  = ( ( $form['conds'] == 'checked' ) ? 1 : 0 );
        // check remote
        $places      = new ApplicationPlaces();
        $places->pid = $form['id'];
        if( count($places->getByAppForPayedOnline()) != 0 && count($places->getByAppForPayedOnline()) == count($places->getSpecsByApp()) ) {
            $app->remote = ( ( $form['remote'] == 'checked' ) ? 1 : 0 );
        } else {
            $app->remote    = 0;
            $form['remote'] = NULL;
        }
        $app->pay    = $app_row['pay'];
        $app->active = $app_row['active'];
        $app->changeAll();
        $form['status'] = $app->status;
        if( $app_row['status'] != $app::STATUS_SAVED ) {
            $applog                 = new ApplicationStatus();
            $applog->id_application = $app->id;
            $applog->create();
        }
        /* scans */
        $dict_scans           = new Model_DictScans();
        $dict_scans->doc_code = 'application';
        $dict_scans_arr       = $dict_scans->getByDocument();
        if( $dict_scans_arr ) {
            foreach( $dict_scans_arr as $dict_scans_row ) {
                $form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
                if( !empty($form['error_msg']) ) {
                    return $form;
                }
            }
        }
        Basic_Helper::msgReset();
        $form['success_msg'] = 'Заявление сохранено.';
        $form['error_msg']   = NULL;
        
        return $form;
    }
    
    /**
     * Sends application spec data.
     *
     * @return array
     */
    public function send( $form )
    {
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        $app                 = new Application();
        $app->id             = $form['id'];
        $app_row             = $app->get();

        if($app_row['id_user'] !== $_SESSION[APP_CODE]['user_id']) {
            $form['error_msg'] = 'Отправлять можно только заявления которые были созданы вами!';

            return $form;
        }
        /* check status */
        if( $app_row['status'] != $app::STATUS_SAVED ) {
            $form['error_msg'] = 'Отправлять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_SAVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        if( $app_row['status'] == $app::STATUS_SENDED ) {
            $form['error_msg'] = 'Заявление уже отправлено!';
            
            return $form;
        }

        $_app = new \common\models\Model_Application();
        $_app->id = $app_row['id'];
        if(!$_app->checkCanSend()) {
            $form['error_msg'] = 'На одно из направлений подготовки, которое вы выбрали уже закончена подача заявлений!';

            return $form;
        }
        /* send */
        $app->status = $app::STATUS_SENDED;
        $app->changeStatus();
        $form['status']         = $app->status;
        $applog                 = new ApplicationStatus();
        $applog->id_application = $app->id;
        $applog->create();
        Basic_Helper::msgReset();
        $form['success_msg'] = 'Заявление отправлено.';
        $form['error_msg']   = NULL;
        
        return $form;
    }
    
    /**
     * Changes application spec data.
     *
     * @return array
     */
    public function change( $form )
    {
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        $app                 = new Application();
        $app->id             = $form['id'];
        $app_row             = $app->get();
        /* check type */
        if( $app_row['type'] == $app::TYPE_RECALL ) {
            $form['error_msg'] = 'Нельзя изменять заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        /* check status */
        if( $app_row['status'] != $app::STATUS_APPROVED ) {
            $form['error_msg'] = 'Изменять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        /* change */
        $id = $app->copy($app::TYPE_CHANGE);
        if( $id > 0 ) {
            $spec_row   = $this->get($id);
            $form       = $this->setForm($this->rules(), $spec_row);
            $form['id'] = $id;
            Basic_Helper::msgReset();
            $form['success_msg'] = 'Заявление на изменение сформировано.';
            $form['error_msg']   = NULL;
        } else {
            Basic_Helper::msgReset();
            $form['success_msg'] = NULL;
            $form['error_msg']   = 'Ошибка при изменении заявления.';
        }
        
        return $form;
    }
    
    /**
     * Recalls application spec data.
     *
     * @return array
     */
    public function recall( $form )
    {
        
        $form['success_msg'] = NULL;
        $form['error_msg']   = NULL;
        $app                 = new Application();
        $app->id             = $form['id'];
        $app_row             = $app->get();
        /* check type */
        if( $app_row['active'] == 0 ) {
            $form['error_msg'] = 'Нельзя отзывать заявления неактивные заявления!';
            
            return $form;
        }
        
        if( $app_row['type'] == $app::TYPE_RECALL ) {
            $form['error_msg'] = 'Нельзя отзывать заявления с типом <strong>'.mb_convert_case($app::TYPE_RECALL_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        /* check status */
        if( $app_row['status'] != $app::STATUS_APPROVED ) {
            $form['error_msg'] = 'Изменять можно только заявления с состоянием <strong>'.mb_convert_case($app::STATUS_APPROVED_NAME, MB_CASE_UPPER, 'UTF-8').'</strong>!';
            
            return $form;
        }
        
        $user_apps = $app->getByUser($app->id_user);
        
        if( $user_apps ) {
            foreach( $user_apps as $user_app ) {
                if( $user_app['id_app'] == $app->id
                    && $user_app['active'] = 1
                                           && $user_app['type'] = 3
                                                                && !in_array($user_app['status'], [ $app::STATUS_APPROVED, $app::STATUS_REJECTED ]) ) {
                    $form['error_msg'] = 'Заявление на отзыв этого заявления уже существет';
                    
                    return $form;
                }
            }
        }
        /* recall */
        $id = $app->copy($app::TYPE_RECALL);
        if( $id > 0 ) {
            $field_names = [ 'application_recall_1', "application_recall_2" ];
            
            foreach( $field_names as $field_name ) {
                if( is_array($_FILES) ) {
                    if( !empty($_FILES[$field_name]['name']) ) {
                        if( !is_array($_FILES[$field_name]['error']) ) {
                            if( $_FILES[$field_name]['error'] === UPLOAD_ERR_OK ) {
                                $uploadfile = FILES_TEMP.$field_name.'_'.session_id().'.'.Files_Helper::getExtension($_FILES[$field_name]['name']);
                                if( move_uploaded_file($_FILES[$field_name]['tmp_name'], $uploadfile) ) {
                                    $scan             = new Scans();
                                    $scan->id_doc     = 5;
                                    $scan->id_row     = $id;
                                    $scan->id_user    = $_SESSION[APP_CODE]['user_id'];
                                    $scan->id_scans   = ( ( $field_name == "application_recall_1" ) ? 36 : 37 );
                                    $scan->file_name  = htmlentities($_FILES[$field_name]['name']);
                                    $scan->file_type  = $_FILES[$field_name]['type'];
                                    $scan->file_size  = $_FILES[$field_name]['size'];
                                    $scan->dt_created = time();
                                    $scan->file_data  = fopen($uploadfile, 'rb');
                                    $scan->save();
                                    fclose($scan->file_data);
                                } else {
                                    throw new \RuntimeException('Возможная атака с помощью файловой загрузки!');
                                }
                            } else {
                                throw new UploadException($_FILES[$field_name]['error']);
                            }
                        } else {
                            throw new \RuntimeException('Множественная загрузка файлов!');
                        }
                    }
                } else {
                    throw new \InvalidArgumentException('На входе функции Model.getForm отсутствует массив файлов!');
                }
            }
            
            $spec_row   = $this->get($id);
            $form       = $this->setForm($this->rules(), $spec_row);
            $form['id'] = $id;
            /* save */
            $app->status = $app::STATUS_SENDED;
            $app->changeStatus();
            $form['status']         = $app->status;
            $applog                 = new ApplicationStatus();
            $applog->id_application = $form['id'];
            $applog->create();
            Basic_Helper::msgReset();
            $form['success_msg'] = 'Заявление на отзыв сформировано.';
            $form['error_msg']   = NULL;
        } else {
            Basic_Helper::msgReset();
            $form['success_msg'] = NULL;
            $form['error_msg']   = 'Ошибка при отзыве заявления.';
        }
        
        return $form;
    }
    
    /**
     * Saves application spec data as PDF.
     *
     * @return mixed
     */
    public function savePdf( $id )
    {
        $pdf        = new PDF_Helper();
        $app        = new Application();
        $app->id    = $id;
        $app_row    = $app->get();
        $place      = new ApplicationPlaces();
        $place->pid = $id;
        $data       = [];
        
        $debug = FALSE;
        
        if( $place->getByAppForBachelorSpec() ) {
            // bachelors and specialists
            $data                      = $this->setAppForPdf($data, $app_row);
            $data                      = $this->setResumeForPdf($data);
            $data                      = $this->setPlacesForPdf($data, $id, 6);
            $data                      = $this->setExamsForPdf($data, $id);
            $data                      = $this->setEducForPdf($data, $app_row['id_docseduc'], 'bachelor');
            $data                      = $this->setForeignLangForPdf($data, $app_row['id_lang']);
            $data['university5_yes']   = 'On';
            $data['specs3_yes']        = 'On';
            $data                      = $this->setCampusForPdf($data, $app_row['campus']);
            $data['docsship_personal'] = 'On';
            $data                      = $this->setIaForPdf($data, $id, self::ЗАЯВЛЕНИЕ_БАКАЛАВРА);
            
            $data = $this->setRemoteForPDF($data, $app_row);
            $data = $this->setPurposeForPDF($data, $app_row);
            
            if( isset($data['places']) ) {
                $pdf->create($data, 'application_bachelor_field', 'заявление'.$app_row['numb']);
            } else {
                $pdf->create($data, 'application_bachelor_table', 'заявление'.$app_row['numb']);
            }
        } elseif( $place->getByAppForMagister() ) {
            // magisters
            $data                      = $this->setAppForPdf($data, $app_row);
            $data                      = $this->setResumeForPdf($data);
            $data                      = $this->setPlacesForPdf($data, $id, 2);
            $data                      = $this->setExamsForPdf($data, $id);
            $data                      = $this->setEducForPdf($data, $app_row['id_docseduc'], 'magister');
            $data                      = $this->setForeignLangForPdf($data, $app_row['id_lang']);
            $data['specs2_yes']        = 'On';
            $data                      = $this->setCampusForPdf($data, $app_row['campus']);
            $data['docsship_personal'] = 'On';
            $data                      = $this->setIaForPdf($data, $id, self::ЗАЯВЛЕНИЕ_МАГИСТРА);
            
            if( isset($data["places"]) ) {
                $rows             = explode(';', $data["places"]);
                $_row             = explode(')', $rows[0], 2);
                $_row             = substr($_row[1], 2);
                $_trow            = explode(',', $_row);
                $data["finance1"] = array_pop($_trow);
                $data["eduform1"] = array_pop($_trow);
                $junk             = array_pop($_trow);
                $data["place1"]   = implode(',', $_trow);
                
                $_row             = explode(')', $rows[1], 2);
                $_row             = substr($_row[1], 2);
                $_trow            = explode(',', $_row);
                $data["finance2"] = array_pop($_trow);
                $data["eduform2"] = array_pop($_trow);
                $junk             = array_pop($_trow);
                $data["place2"]   = implode(',', $_trow);
                
                unset($data["places"]);
            }
            
            $data = $this->setRemoteForPDF($data, $app_row);
            $data = $this->setPurposeForPDF($data, $app_row);
            
            $pdf->create($data, 'application_magistrature', 'заявление'.$app_row['numb']);
        } elseif( $place->getByAppForSpecial() ) {
            // specials
            $data          = $this->setAppForPdf($data, $app_row);
            $data          = $this->setResumeForPdf($data);
            $data['exams'] = 'On';
            $data          = $this->setPlacesForPdf($data, $id, 1000000);
            $data          = $this->setExamsForPdf($data, $id);
            $data          = $this->setEducForPdf($data, $app_row['id_docseduc'], 'specialist');
            $data          = $this->setForeignLangForPdf($data, $app_row['id_lang']);
            if( $app_row['pay'] == 0 ) {
                $data['special_first_yes'] = 'On';
            } else {
                $data['special_first_no'] = 'On';
            }
            $data = $this->setCampusForPdf($data, $app_row['campus']);
            
            $data = $this->setRemoteForPDF($data, $app_row);
            $data = $this->setPurposeForPDF($data, $app_row);
            $data = $this->setIaForPdf($data, $id, self::ЗАЯВЛЕНИЕ_СПО);
            $pdf->create($data, 'application_special', 'заявление'.$app_row['numb']);
        } elseif( $place->getByAppForClinical() ) {
            // attending physicians
            $data                      = $this->setAppForPdf($data, $app_row);
            $data                      = $this->setResumeForPdf($data);
            $data                      = $this->setPlacesForPdf($data, $id, 10000000);
            $data                      = $this->setExamsForPdf($data, $id);
            $data                      = $this->setEducForPdf($data, $app_row['id_docseduc'], 'attending_physician');
            $data                      = $this->setForeignLangForPdf($data, $app_row['id_lang']);
            $data['inila']             = $app_row['inila'];
            $data                      = $this->setCampusForPdf($data, $app_row['campus']);
            $data['docsship_personal'] = 'On';
            $data                      = $this->setIaForPdf($data, $id, self::ЗАЯВЛЕНИЕ_ОРДИНАТОРА, $debug);
            
            if( $debug ) {
                echo "<pre>";
                var_dump($data);
                echo "</pre>";
                
                die();
            }
            
            $data = $this->setRemoteForPDF($data, $app_row);
            $data = $this->setPurposeForPDF($data, $app_row);
            
            $pdf->create($data, 'application_clinical', 'заявление'.$app_row['numb']);
        } elseif( $place->getByAppForTraineeship() ) {
            // trainees
            $data = $this->setAppForPdf($data, $app_row);
            $data = $this->setResumeForPdf($data);
            $data = $this->setPlacesForPdf($data, $id, 4);
            $data = $this->setExamsForPdf($data, $id);
            $data = $this->setEducForPdf($data, $app_row['id_docseduc'], 'trainee');
            $data = $this->setForeignLangForPdf($data, $app_row['id_lang']);
            
            $data = $this->setCampusForPdf($data, $app_row['campus']);
            $data = $this->setIaForPdf($data, $id, self::ЗАЯВЛЕНИЕ_АСПИРАНТА);
            
            if( $debug ) {
                echo "<pre>";
                var_dump($data);
                echo "</pre>";
                
                die();
            }
            
            $data = $this->setRemoteForPDF($data, $app_row);
            $data = $this->setPurposeForPDF($data, $app_row);
            
            $pdf->create($data, 'application_traineeship', 'заявление'.$app_row['numb']);
        } else {
            $resume     = new Resume();
            $resume_row = $resume->getByUser();
            if( $resume_row['sex'] == 0 ) {
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
    public function setAppForPdf( $data, $app_row ): array
    {
        $app              = new Application();
        $data['app_numb'] = $app_row['numb'];
        if( $app_row['type'] == $app::TYPE_RECALL ) {
            $resume                 = new Resume();
            $resume_row             = $resume->getByUser();
            $data['recall_fio']     = $resume_row['name_last'].' '.$resume_row['name_first'].$resume_row['name_middle'];
            $data['recall_dt']      = date('d.m.Y');
            $data['recall_dt_day']  = date('d');
            $data['recall_dt_body'] = $this->getMothName(date('m'));
        } else {
            $data['app_dt']      = date('d.m.Y');
            $data['app_dt_day']  = date('d');
            $data['app_dt_body'] = $this->getMothName(date('m'));
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
    
    /**
     * Sets places data for PDF.
     *
     * @return array
     */
    public function setPlacesForPdf( $data, $app, $limit ): array
    {
        $places      = new ApplicationPlaces();
        $places->pid = $app;
        $places_arr  = $places->getSpecsByAppPdf();


        $i           = 1;
        if( /*count($places_arr) <= $limit*/ TRUE ) {
            foreach( $places_arr as $places_row ) {
                $spec_arr['place'.$i]   = $places_row['place'].' ('.$places_row['edulevel'].')';
                $spec_arr['eduform'.$i] = $places_row['eduform'];
                $spec_arr['finance'.$i] = $places_row['finance'];
                $i++;
            }
        } else {
            $spec_arr['places'] = '';
            foreach( $places_arr as $places_row ) {
                $spec_arr['places'] .= $i.") ".$places_row['place'].", (".$places_row['edulevel']."), ".$places_row['eduform'].", ".$places_row['finance'].";\n";
                $i++;
            }
        }
        
        return array_merge($data, $spec_arr);
    }
    
    /**
     * Sets exams data for PDF.
     *
     * @return array
     */
    public function setExamsForPdf( $data, $app )
    {
        $exams      = new ApplicationPlacesExams();
        $exams->pid = $app;
        $exams_arr  = $exams->getExamsByApplication();

        if( $exams_arr ) {
            $data['exams_yes'] = 'On';
            $exams_disciplines = ' ';
            foreach( $exams_arr as $exams_row ) {
                switch ( $exams_row['description'] ) {
                    case 'ЕГЭ':
                        $data['exams_ege'] = 'On';
                        switch ( $exams_row['discipline_name'] ) {
                            case 'Русский язык':
                                $data['ege_rus_'.$exams_row['reg_year']] = $exams_row['points'];
                                break;
                            case 'Математика':
                                $data['ege_math_'.$exams_row['reg_year']] = $exams_row['points'];
                                break;
                            case 'Английский язык':
                            case 'Французский язык':
                            case 'Немецкий язык':
                            case 'Испанский язык':
                            case 'Иностранный язык':
                                if(!(isset($data['ege_lang_'.$exams_row['reg_year']])) || ($data['ege_lang_'.$exams_row['reg_year']] < $exams_row['points'])) {
                                    $data['ege_lang_' . $exams_row['reg_year']] = $exams_row['points'];
                                }
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
                    case 'Тестирование':
                    case 'Экзамен':
                        if( strripos($exams_row['discipline_name'], 'Профессиональное испытание') === FALSE
                            && strripos($exams_row['discipline_name'], 'Творческое испытание') === FALSE
                            && strripos($exams_row['discipline_name'], 'Теория физической культуры') === FALSE ) {
                            $data['exams_university'] = 'On';
                            $exams_disciplines        .= $exams_row['discipline_name'].', ';
                        }
                        break;
                }
            }
            $exams_disciplines         = mb_substr($exams_disciplines, 0, -2);
            $data['exams_disciplines'] = $exams_disciplines;
            // exams reason
            if( isset($data['exams_university']) ) {
                $personal    = new Personal();
                $citizenship = $personal->getCitizenshipByUser();
                if( $citizenship['code'] != '643' ) {
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
    public function setEducForPdf( $data, $id_docseduc, $edulevel ): array
    {
        $docs     = new DocsEduc();
        $docs->id = $id_docseduc;
        $docs_row = $docs->getForPdf();
        
        switch ( $docs_row['educ_type'] ) {
            case "Среднее общее":
                $data['educ_type_common'] = "On";
                break;
            case "среднее (основное,общее)":
                $data['educ_type_basic'] = 'On';
                break;
            case "Среднее специальное":
            case "Начальное профессиональное":
                $data['educ_type_prof'] = "On";
                break;
            case "Высшее":
            case "Аспирантура":
                $data['educ_type_high'] = "On";
                break;
        }
        $data['educ_type'] = $docs_row['educ_type'];
        $data['school']    = $docs_row['school'];
        switch ( $edulevel ) {
            case 'bachelor':
            case 'specialist':
                if( in_array($docs_row['doc_type'], $docs::CERTIFICATES) ) {
                    $data['certificate'] = 'On';
                } elseif( in_array($docs_row['doc_type'], $docs::DIPLOMAS) ) {
                    $data['diploma'] = 'On';
                }
                break;
            case 'magister':
            case 'trainee':
                switch ( $docs_row['doc_type'] ) {
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
        $data['docseduc_numb']   = $docs_row['numb'];
        $data['docseduc_dt']     = date('d.m.Y', strtotime($docs_row['dt_issue']));
        
        return $data;
    }
    
    /**
     * Sets foreign language for PDF.
     *
     * @return array
     */
    public function setForeignLangForPdf( $data, $id_lang ): array
    {
        $lang     = new DictForeignLangs();
        $lang->id = $id_lang;
        $lang_row = $lang->get();
        switch ( $lang->id ) {
            case 17:
                $data["foreign_lang_eng"] = "On";
                break;
            case 19:
                $data["foreign_lang_deuch"] = "On";
                break;
            case 20:
                $data["foreign_lang_fran"] = "On";
                break;
            default:
                if( isset($lang->id) && !empty($lang->id) && $lang->id > 0 && $lang->id < 25 ) {
                    $data['foreign_lang_other']   = "On";
                    $data['foreign_lang_caption'] = $lang_row['description'];
                } else {
                    $data["foreign_lang_none"] = "On";
                }
                break;
        }
        $data['foreign_lang'] = $lang_row['description'];
        
        return $data;
    }
    
    /**
     * Sets campus data for PDF.
     *
     * @return array
     */
    public function setCampusForPdf( $data, $campus )
    {
        if( $campus == 0 ) {
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
    public function setIaForPdf( $data, $app, $type, $debug = FALSE )
    {
        if($app == 7293) {
            $debug = TRUE;
        }

        if( $debug ) {
            echo "<pre>";
            var_dump([ "setIaForPdf_DEBUG", $data ]);
            echo "</pre>";
        }
        
        $ia      = new ApplicationAchievs();
        $ia->pid = $app;
        $ia_arr  = $ia->getByAppForPdf();
        
        $data['ia_no'] = 'On';
        
        if( $ia_arr ) {
            if($debug) {
                echo '<pre>';
                var_dump([$type => $ia_arr]);
                echo '</pre>';
            }

            switch ( $type ) {
                case self::ЗАЯВЛЕНИЕ_БАКАЛАВРА:
                    $ia_type_arr = [
                        'ia_olympic' => [ '000000004' ],
                        'ia_medal_certificate' => [ '000000003' ],
                        'ia_medal_diploma' => [ '000000022' ],
                        'ia_contest_rus' => [ '000000005' ],
                        'ia_sportmaster' => [ '000000002', '000000023' ]
                    ];
                    break;
                case self::ЗАЯВЛЕНИЕ_МАГИСТРА:
                    $ia_type_arr = [
                        'ia_science_profile' => [],
                        'ia_science' => [ '000000246' ],
                        'ia_contest_univer' => [ '000000038' ],
                        'ia_contest_russia' => [ '000000036' ],
                        'ia_contest_international' => [ '000000037' ],
                        'ia_grants_federal' => [],
                        'ia_grants_region' => [ '000000010' ],
                        'ia_grant_city' => [],
                        'ia_champion' => [ '000000031' ],
                        'ia_avg_diploma_score' => ['000000017']
                    ];
                    
                    $ia_mag_pubs = ( new Model_DictIndAchievs() )->getMagPubAchives();
                    $spec_codes  = [];
                    
                    for( $i = 1; $i < 6; $i++ ) {
                        $_t = 'place'.$i;
                        if( isset($data[$_t]) ) {
                            $spec_codes[] = mb_substr($data['place'.$i], 0, 8);
                        }
                    }
                    
                    foreach( $ia_mag_pubs as $ia_mag_pub ) {
                        if( in_array(mb_substr($ia_mag_pub['abbr'], -8), $spec_codes) ) {
                            $ia_type_arr['ia_science_profile'][] = $ia_mag_pub['code'];
                        } else {
                            $ia_type_arr['ia_science'][] = $ia_mag_pub['code'];
                        }
                    }
                    break;
                case self::ЗАЯВЛЕНИЕ_АСПИРАНТА:
                    $ia_type_arr = [
                        'ia_articles_vak_profile' => [],
                        'ia_articles_rus_profile' => [],
                        'ia_article_worldconf_profile' => [],
                        'ia_articles_rusconf_profile' => [],
                        'ia_grants_named' => [],
                        'ia_grants_profile' => [],
                        'ia_inventions_profile' => [],
                        'ia_soft' => [],
                        'ia_contest' => [ '000000045' ],
                        'ia_medal_profile' => [],
                        'ia_gek_letter' => [],
                        'ia_trainer_note' => [],
                        'ia_foreing_b1' => [ '000000058' ]
                    ];
                    
                    $spec_codes = [];
                    
                    for( $i = 1; $i < 10; $i++ ) {
                        $_t = 'place'.$i;
                        if( isset($data[$_t]) ) {
                            $spec_codes[] = mb_substr($data['place'.$i], 0, 2);
                        }
                    }

                    if($debug) {
                        echo '<pre>';
                        var_dump(['spec_code' => $spec_codes]);
                        echo '</pre>';
                    }

                    $_X = (new Model_DictIndAchievs())->getAspWAKAchives();

                    if($debug) {
                        echo '<pre>';
                        var_dump(['AspWAKAchives' => $_X]);
                        echo '</pre>';
                    }

                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_articles_vak_profile'][] = $_X_item['code'];
                        }
                    }

                    $_X = ( new Model_DictIndAchievs() )->getAspWOSorScopusAchives();

                    if($debug) {
                        echo '<pre>';
                        var_dump(['AspWOSorScopusAchives' => $_X]);
                        echo '</pre>';
                    }

                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_articles_wos_profile'][] = $_X_item['code'];
                        }
                    }
                    
                    $_X = ( new Model_DictIndAchievs() )->getAspRINCAchives();
                    
                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_article_worldconf_profile'][] = $_X_item['code'];
                        }
                    }
                    
                    $_X = ( new Model_DictIndAchievs() )->getAspMaterialAchives();
                    
                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_articles_rusconf_profile'][] = $_X_item['code'];
                        }
                    }
                    
                    $_X = ( new Model_DictIndAchievs() )->getAspGrantAchives();
                    
                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_grants_profile'][]     = $_X_item['code'];
                            $ia_type_arr['ia_inventions_profile'][] = $_X_item['code'];
                            $ia_type_arr['ia_soft'][]               = $_X_item['code'];
                        }
                    }
                    
                    $_X = ( new Model_DictIndAchievs() )->getAspDOAchives();
                    
                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_medal_profile'][] = $_X_item['code'];
                        }
                    }
                    
                    $_X = ( new Model_DictIndAchievs() )->getAspGEKAchives();
                    
                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_gek_letter'][] = $_X_item['code'];
                        }
                    }
                    
                    $_X = ( new Model_DictIndAchievs() )->getAspGEKAchives();
                    
                    foreach( $_X as $_X_item ) {
                        if( in_array(mb_substr($_X_item['abbr'], -8, 2), $spec_codes) ) {
                            $ia_type_arr['ia_trainer_note'][] = $_X_item['code'];
                        }
                    }
                    break;
                case self::ЗАЯВЛЕНИЕ_ОРДИНАТОРА:
                    $ia_type_arr = [
                        'ia_grants_president' => [ '000000032' ],
                        'ia_docseduc_medal' => [],
                        'ia_medic_mid' => [ '000000214' ],
                        'ia_medic_high' => [ '000000215' ],
                        'ia_medic_high_ext' => [  '000000216' ],
                        'ia_medic_locations' => [ '000000035' ],
                        'contest_region' => [],
                        'contest_country' => [],
                        'contest_world' => [],
                        'ia_inventions' => [ '000000217' ],
                        'ia_articles' => [ '000000213' ],
                        'ia_articles_conference' => [],
                        'ia_articles_vak_no' => [],
                        'ia_articles_vak_yes' => [ '000000213' ],
                        'ia_volunteer' => [ '000000050' ]
                    ];
                    break;
                case self::ЗАЯВЛЕНИЕ_СПО:
                    $ia_type_arr = [
                        'ia_spo_olimp' => [ '000000059' ],
                        'ia_abil' => [ '000000060' ],
                        'ia_young_prof' => [ '000000061' ],
                        'ia_diploma_avg_check' => [ '000000028' ]
                    ];
                    
                    break;
                default:
                    $ia_type_arr = [];
                    break;
            }
            
            foreach( $ia_arr as $ia_item ) {
                foreach( $ia_type_arr as $ia_type_item => $ia_type_codes ) {
                    if( in_array($ia_item['code'], $ia_type_codes) ) {
                        $data['ia_yes']      = 'On';
                        $data['ia_no']       = 'Off';
                        $data[$ia_type_item] = 'On';
                        break;
                    }
                }
            }
        }

        if($debug) {
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
            die();
        }
        
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
        }
    }
    
    public function setRemoteForPDF( array $data, array $app_row )
    {
        if($app_row['remote'] == 1) {
            if (!isset($data['exams_ege'])) {
                $data['exams_ege'] = 'Off';
            }

            if ($data['exams_ege'] != 'On') {
                $data['remote_exams_toggle'] = 'On';
                $data['remote_exams_discs'] = $data['exams_disciplines'] ?? '';
                $data['remote_exams_place'] = $data['address_res'];
            } else {
                $data['remote_exams_toggle'] = 'Off';
            }
        }
        
        return $data;
    }
    
    public function setPurposeForPDF( $data, array $app_row )
    {
        $feature     = new \common\models\Model_Features();
        $feature_row = $feature->getFirstByUser();
        
        if( is_array($feature_row) && count($feature_row) > 0 ) {
            $data['no_exams'] = 'On';
            //$data['contest_document'] = 'Заглушка';
        }
        
        $quota_priv     = new \common\models\Model_PrivillegeQuota();
        $quota_priv_row = $quota_priv->getFirstByUser();
        
        if( is_array($quota_priv_row) && count($quota_priv_row) > 0 ) {
            $data['quota']        = 'On';
            $data['quota_reason'] = $quota_priv_row['doc_type'];
        }
        
        $adv_priv     = new \common\models\Model_PrivillegeAdvanced();
        $adv_priv_row = $adv_priv->getFirstByUser();
        
        if( is_array($adv_priv_row) && count($adv_priv_row) > 0 ) {
            $data['privilege']        = 'On';
            $data['privilege_reason'] = $adv_priv_row['doc_type'];
        }
        
        $target     = new \common\models\Model_TargetQuota();
        $target_row = $target->getFirstByUser();
        
        if( is_array($target_row) && count($target_row) > 0 ) {
            $data['purpose']        = 'On';
            $data['purpose_reason'] = $target_row['doc_issuer'];
        }
        
        return $data;
    }
}
