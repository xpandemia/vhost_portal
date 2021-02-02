<?php

namespace tinyframe\core\helpers;

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

class HTML_Helper
{
    /*
        HTML processing
    */
    
    /**
     * Creates label.
     *
     * @param $class
     * @param $control
     * @param $label
     *
     * @return string
     */
    public static function setLabel( $class, $control, $label ): string
    {
        return '<label class="'.$class.'" for="'.$control.'">'.$label.'</label>';
    }

    /**
     * Creates input field.
     *
     * @param $type
     * @param $class
     * @param $control
     * @param $help
     * @param $placeholder
     * @param $value
     *
     * @param null $events
     * @param bool $isDisabled
     * @return string
     */
    public static function setInput( $type, $class, $control, $help, $placeholder, $value, $events = NULL, $isDisabled = FALSE): string
    {
        if( !empty($type) && !empty($class) && !empty($control) ) {
            $event_text = '';
            if(is_array($events) && count($events) > 0) {
                $event_text = '<script>';
                foreach ($events as $event_name => $event_code) {
                    $event_text .= '$("#'.$control.'").on("'.$event_name.'", function(){'.$event_code.'});';
                    $event_text .= '$("#'.$control.'").trigger("'.$event_name.'");';
                }
                $event_text .= '</script>';
            }

            if($isDisabled === TRUE) {
                $disabled = ' readonly ';
            } else {
                $disabled = '';
            }

            return '<input '.$disabled.' type="'.$type.'" class="'.$class.'"'.
                   ( ( isset($help) ) ? ' aria-describedby="'.$control.'HelpBlock"' : '' ).
                   ' id="'.$control.'" name="'.$control.'"'.
                   ( ( isset($placeholder) ) ? ' placeholder="'.$placeholder.'"' : '' ).
                   ' value="'.$value.'">'.$event_text;
        }
        
        return '<p class="text-danger">HTML_Helper.setInput - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates submit button.
     *
     * @param      $class
     * @param      $id
     * @param      $text
     * @param null $tooltip
     *
     * @return string
     */
    public static function setSubmit( $class, $id, $text, $tooltip = NULL ): string
    {
        if( !empty($class) && !empty($id) && !empty($text) ) {
            if( empty($tooltip) ) {
                return '<button type="submit" class="'.$class.'" id="'.$id.'" name="'.$id.'">'.$text.'</button> ';
            }
            
            return '<button type="submit" class="'.$class.'" id="'.$id.'" name="'.$id.'" data-toggle="tooltip" title="'.$tooltip.'">'.$text.'</button> ';
        }
        
        return '<p class="text-danger">HTML_Helper.setSubmit - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates button.
     *
     * @param      $class
     * @param      $id
     * @param      $text
     * @param null $tooltip
     *
     * @return string
     */
    public static function setButton( $class, $id, $text, $tooltip = NULL ): string
    {
        if( !empty($id) && !empty($text) ) {
            if( empty($tooltip) ) {
                return '<button type="button" class="'.$class.'" id="'.$id.'" name="'.$id.'">'.$text.'</button> ';
            }
            
            return '<button type="button" class="'.$class.'" id="'.$id.'" name="'.$id.'" data-toggle="tooltip" title="'.$tooltip.'">'.$text.'</button> ';
        }
        
        return '<p class="text-danger">HTML_Helper.setButton - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates HREF as text.
     *
     * @param      $controller
     * @param      $action
     * @param      $text
     * @param null $tooltip
     *
     * @return string
     */
    public static function setHrefText( $controller, $action, $text, $tooltip = NULL ): string
    {
        if( !empty($controller) && !empty($action) && !empty($text) ) {
            if( empty($tooltip) ) {
                return '<p><a href="'.Basic_Helper::appUrl($controller, $action).'" class="font-weight-bold text-secondary">'.$text.'</a></p> ';
            }
            
            return '<p><a data-toggle="tooltip" title="'.$tooltip.'" href="'.Basic_Helper::appUrl($controller, $action).'" class="font-weight-bold text-secondary">'.$text
                   .'</a></p> ';
        }
        
        return '<p class="text-danger">HTML_Helper.setHrefText - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates HREF as button.
     *
     * @param      $controller
     * @param      $action
     * @param      $class
     * @param      $text
     * @param null $tooltip
     *
     * @return string
     */
    public static function setHrefButton( $controller, $action, $class, $text, $tooltip = NULL ): string
    {
        if( !empty($controller) && !empty($action) && !empty($class) && !empty($text) ) {
            if( empty($tooltip) ) {
                return '<a href="'.Basic_Helper::appUrl($controller, $action).'" class="'.$class.'">'.$text.'</a> ';
            }
            
            return '<a data-toggle="tooltip" title="'.$tooltip.'" href="'.Basic_Helper::appUrl($controller, $action).'" class="'.$class.'">'.$text.'</a> ';
        }
        
        return '<p class="text-danger">HTML_Helper.setHrefButton - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates HREF as icon button.
     *
     * @param      $controller
     * @param      $action
     * @param      $class
     * @param      $icon
     * @param null $tooltip
     * @param int  $new_page
     *
     * @return string
     */
    public static function setHrefButtonIcon( $controller, $action, $class, $icon, $tooltip = NULL, $new_page = 0 ): string
    {
        if( !empty($controller) && !empty($action) && !empty($class) && !empty($icon) ) {
            if( $new_page === 0 ) {
                return '<a data-toggle="tooltip" title="'.$tooltip.'" href="'.Basic_Helper::appUrl($controller, $action).'" class="'.$class.'"><i class="'.$icon.'"></i></a> ';
            }
            
            return '<a data-toggle="tooltip" title="'.$tooltip.'" href="'.Basic_Helper::appUrl($controller, $action).'" class="'.$class.'" target="_blank"><i class="'.$icon
                   .'"></i></a> ';
        }
        
        return '<p class="text-danger">HTML_Helper.setHrefButtonIcon - На входе недостаточно данных!</p>';
    }

    public static function setUrlHrefButtonIcon( $url, $class, $icon, $tooltip = NULL, $new_page = 0 ): string
    {
        if( !empty($url) && !empty($class) && !empty($icon) ) {
            if( $new_page === 0 ) {
                return '<a data-toggle="tooltip" title="'.$tooltip.'" href="'.$url.'" class="'.$class.'"><i class="'.$icon.'"></i></a> ';
            }

            return '<a data-toggle="tooltip" title="'.$tooltip.'" href="'.$url.'" class="'.$class.'" target="_blank"><i class="'.$icon
                .'"></i></a>';
        }

        return '<p class="text-danger">HTML_Helper.setHrefButtonIcon - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates image.
     *
     * @param      $type
     * @param      $lob
     * @param null $width
     * @param null $height
     *
     * @return string
     */
    public static function setImageLOB( $type, $lob, $width = NULL, $height = NULL ): string
    {
        if( !empty($type) && !empty($lob) ) {
            return '<br><img class="img-fluid" src="data:'.$type.';base64,'.base64_encode($lob).'" width="'.( ( empty($width) ) ? 460 : $width ).'" height="'.( ( empty($height) )
                    ? 345 : $height ).'">';
        }
        
        return '<p class="text-danger">HTML_Helper.setImageLOB - На входе недостаточно данных!</p>';
    }
    
    /**
     * Creates GRID from database.
     *
     * @param $rules
     *
     * @return string
     */
    /* RULES (+ required)
       'id' => {TABLE_ID},
       + 'model_class' => {MODEL_CLASS},
       + 'model_method' => {MODEL_METHOD},
       'model_filter' => {MODEL_FILTER},
       'model_filter_var' => {MODEL_FILTER_VAR},
       + 'grid' => {GRID},
       + 'controller' => {CONTROLLER},
       + 'action_add' => {ACTION_ADD},
       'action_edit' => {ACTION_EDIT},
       'action_delete' => {ACTION_DELETE},
       + 'home_hdr' => {HOME_HEADER}
   */
    public static function setGridDB( $rules ): string
    {
        if( isset($rules) && is_array($rules) ) {
            // action create
            if( isset($rules['action_add']) ) {
                $button_label = $rules['button_label'] ?? 'Добавить';
                $result = self::setHrefButton($rules['controller'], $rules['action_add'], 'btn btn-success', $button_label, $button_label);
            } else {
                $result = '';
            }
            if( isset($rules['id']) && !empty($rules['id']) ) {
                $result .= '<table class="table table-bordered table-hover" id="'.$rules['id'].'" name="'.$rules['id'].'">';
            } else {
                $result .= '<table class="table table-bordered table-hover" id="gridDb" name="gridDb">';
            }
            // using model
            $model = new $rules['model_class']();
            // using model method
            $method = $rules['model_method'];
            /* header */
            $result .= '<thead class="thead-dark">';
            $result .= '<tr>';
            $grid   = $rules['grid'];
            foreach( $model->$grid() as $key => $value ) {
                $result .= '<th class="align-text-top">'.$value['name'].'</th>';
            }
            $result .= '</tr>';
            $result .= '</thead>';
            /* data */
            // using model filter
            if( isset($rules['model_filter'], $rules['model_filter_var']) ) {
                $filter         = $rules['model_filter'];
                $model->$filter = $rules['model_filter_var'];
            }
            // fetching data
            $table = $model->$method();
            if( $table ) {
                foreach( $table as $table_row ) {
                    $result .= '<tr>';
                    foreach( $model->$grid() as $key => $value ) {
                        switch ( $value['type'] ) {
                            case 'lob':
                                if( !empty($table_row[$key]) ) {
                                    $result .= '<td><img class="img-fluid" src="data:'.( $table_row['file_type'] ?? '' ).';base64,'.base64_encode($table_row[$key])
                                               .'" width="80" height="100"></td>';
                                } else {
                                    $result .= '<td>Файл не загружен</td>';
                                }
                                break;
                            case 'date':
                                if( isset($value['format']) && !empty($value['format']) ) {
                                    $result .= '<td>'.date($value['format'], strtotime($table_row[$key])).'</td>';
                                } else {
                                    $result .= '<td>'.$table_row[$key].'</td>';
                                }
                                break;
                            default:
                                $style = '';
                                if( $key == 'status' ) {
                                    $style = 'style ="color:#ba0000; font-weight:bold;"';
                                }
                                $result .= '<td '.$style.'>'.$table_row[$key].'</td>';
                        }
                    }
                    // actions
                    if( isset($table_row['id']) && ( isset($rules['action_edit']) || isset($rules['action_delete']) ) ) {
                        $result .= '<td>';
                        // action edit
                        if( isset($rules['action_edit']) && HTML_Helper::canBeDisplayed($table_row, $rules, 'action_edit') ) {
                            $result .= HTML_Helper::setHrefButtonIcon($rules['controller'],
                                                                      $rules['action_edit'].'/?id='.$table_row['id'].( ( isset($table_row['pid']) ) ? '&pid='.$table_row['pid']
                                                                          : '' ), 'font-weight-bold', 'far fa-edit fa-2x', 'Редактировать');
                        }
                        // action delete
                        if( isset($rules['action_delete']) && HTML_Helper::canBeDisplayed($table_row, $rules, 'action_delete') ) {
                            $result .= HTML_Helper::setHrefButtonIcon($rules['controller'],
                                                                      $rules['action_delete'].'/?id='.$table_row['id'].( ( isset($table_row['pid']) ) ? '&pid='.$table_row['pid']
                                                                          : '' ).'&hdr='.$rules['home_hdr'].'&ctr='.$rules['controller'], 'text-danger font-weight-bold',
                                                                      'fas fa-times fa-2x', 'Удалить');
                        }
                        $result .= '</td>';
                    }
                    $result .= '</tr>';
                }
            }
            $result .= '</table>';
            
            return $result;
        }
        
        return '<p class="text-danger">HTML_Helper.setGridDB - На входе не массив!</p>';
    }
    
    /**
     * @param $row
     * @param $rules
     * @param $action
     *
     * @return bool
     */
    public static function canBeDisplayed( $row, $rules, $action ): bool
    {
        if( $rules['model_class'] == 'common\\models\\Model_Application' ) {
            switch ( $action ) {
                case 'action_delete':
                    if( in_array($row['status'], [ 'Новое', 'Принято', 'Отклонено', 'Сохранено', 'Изменено', 'Отозвано' ]) ) {
                        if( ( $row['type'] != 'Заявление на приём документов' && $row['status'] == 'Принято' )
                            || ( $row['type'] == 'Заявление на приём документов'
                                 && in_array($row['status'],
                                             [ 'Принято' ]) ) ) {
                            return FALSE;
                        }
                        
                        return TRUE;
                    }
                    
                    return FALSE;
                    break;
                default:
                    return TRUE;
                    break;
            }
        } else {
            return TRUE;
        }
    }
    
    /**
     * Creates table begin.
     *
     * @param null $id
     *
     * @return string
     */
    public static function setTableBegin( $id = NULL ): string
    {
        if( !empty($id) ) {
            return '<table class="table table-bordered table-hover" id="'.$id.'" name="'.$id.'">';
        }
        
        return '<table class="table table-bordered table-hover">';
    }
    
    /**
     * Creates table header.
     *
     * @param $rules
     *
     * @return string
     */
    /* RULES (+ required)
       'class' => {HEADER_CLASS},
       + 'grid' => {GRID}
   */
    public static function setTableHeader( $rules ): string
    {
        if( isset($rules) && is_array($rules) ) {
            if( isset($rules['grid']) && !empty($rules['grid']) && is_array($rules['grid']) ) {
                if( isset($rules['class']) && !empty($rules['class']) ) {
                    $result = '<thead class="'.$rules['class'].'">';
                } else {
                    $result = '<thead>';
                }
                $result .= '<tr>';
                foreach( $rules['grid'] as $key => $value ) {
                    $result .= '<th class="align-text-top">'.$value['name'].'</th>';
                }
                $result .= '</tr>';
                $result .= '</thead>';
                
                return $result;
            }
            
            return '<p class="text-danger">HTML_Helper.setTableHeader - Нет данных для создания заголовка!</p>';
        }
        
        return '<p class="text-danger">HTML_Helper.setTableHeader - На входе не массив!</p>';
    }
    
    /**
     * Creates table row.
     *
     * @param $rules
     *
     * @return string
     */
    /* RULES (+ required)
       + 'grid' => {GRID},
       + 'row' => {ROW}
   */
    public static function setTableRow( $rules ): string
    {
        if( isset($rules) && is_array($rules) ) {
            if( ( isset($rules['grid']) && !empty($rules['grid']) && is_array($rules['grid']) ) || ( isset($rules['row']) && !empty($rules['row']) && is_array($rules['row']) ) ) {
                $result = '<tr>';
                foreach( $rules['grid'] as $key => $value ) {
                    switch ( $value['type'] ) {
                        case 'date':
                            if( isset($value['format']) && !empty($value['format']) ) {
                                $result .= '<td>'.date($value['format'], strtotime($rules['row'][$key])).'</td>';
                            } else {
                                $result .= '<td>'.$rules['row'][$key].'</td>';
                            }
                            break;
                        default:
                            $result .= '<td>'.$rules['row'][$key].'</td>';
                    }
                }
                if( isset($rules['controls']) && !empty($rules['controls']) && is_array($rules['controls']) ) {
                    $result .= '<td>';
                    foreach( $rules['controls'] as $control ) {
                        $result .= HTML_Helper::setHrefButtonIcon($control['controller'], $control['action'], $control['class'], $control['icon'], $control['tooltip']);
                    }
                    $result .= '</td>';
                }
                $result .= '</tr>';
                
                return $result;
            }
            
            return '<p class="text-danger">HTML_Helper.setTableRow - Нет данных для создания строки!</p>';
        }
        
        return '<p class="text-danger">HTML_Helper.setTableRow - На входе не массив!</p>';
    }
    
    /**
     * Creates table end.
     *
     * @return string
     */
    public static function setTableEnd(): string
    {
        return '</table>';
    }
    
    /**
     * Creates pagination.
     *
     * @param $rules
     *
     * @return string
     */
    /* RULES (+ required)
       + 'model_class' => {MODEL_CLASS},
       + 'model_data_method' => {MODEL_DATA_METHOD},
       + 'model_page_method' => {MODEL_PAGE_METHOD},
       + 'model_count_method' => {MODEL_COUNT_METHOD},
       'model_rowsless_method' => {MODEL_ROWSLESS_METHOD},
       'model_rows_method' => {MODEL_ROWS_METHOD},
       'model_filter' => {MODEL_FILTER},
       'model_filter_var' => {MODEL_FILTER_VAR},
       + 'id' => {ID_CURRENT},
       + 'rows' => {ROWS_LIMIT}
   */
    public static function setPagination( $rules ): string
    {
        if( isset($rules) && is_array($rules) ) {
            // using model
            $model = new $rules['model_class']();
            // using model data method
            $method_data = $rules['model_data_method'];
            /* data */
            // using model filter
            if( isset($rules['model_filter']) && isset($rules['model_filter_var']) ) {
                $filter         = $rules['model_filter'];
                $model->$filter = $rules['model_filter_var'];
            }
            // fetching data
            $table = $model->$method_data();
            if( $table ) {
                if( !isset($rules['model_page_method']) || empty($rules['model_page_method']) ) {
                    return '<p class="text-danger">HTML_Helper.setPagination - Метод определения страницы не указан!</p>';
                }
                if( isset($rules['id']) ) {
                    $method_page = $rules['model_page_method'];
                    if( isset($filter) && !empty($filter) ) {
                        $model->$filter = $rules['id'];
                    }
                    $page_current = $model->$method_page();
                } else {
                    return '<p class="text-danger">HTML_Helper.setPagination - Текущий идентификатор не указан!</p>';
                }
                $rows         = '';
                $pages        = '<ul class="pagination justify-content-center">';
                $method_rows  = $rules['model_rows_method'];
                $max_id_value = $model->$method_rows();
                
                $i = $max_id_value;//$i = 0;
                foreach( $table as $table_row ) {
                    // start page
                    if( $i === $max_id_value /*$i === 0*/ ) {
                        $id_min = $table_row['id'];
                        if( isset($filter) && !empty($filter) ) {
                            $model->$filter = $table_row['id'];
                        }
                        $page = $model->$method_page();
                        if( $page !== 1 ) {
                            $pages .= '<li class="page-item"><a class="page-link" href="Index/?id='.$id_min.'&step=prev">Previous</a></li>';
                        }
                    }
                    // current page
                    if( $i % $rules['rows'] === 0 ) {
                        if( $page === $page_current ) {
                            // mark page active
                            $pages .= '<li class="page-item active"><a class="page-link" href="Index/?id='.$table_row['id'].'&step=next">'.$page.'</a></li>';
                            // create rows marker start
                            if( isset($rules['model_rowsless_method']) && !empty($rules['model_rowsless_method']) && isset($rules['model_rows_method'])
                                && !empty($rules['model_rows_method']) ) {
                                if( isset($filter) && !empty($filter) ) {
                                    $model->$filter = $rules['id'];
                                }
                                $method_rowsless = $rules['model_rowsless_method'];
                                $rows            .= '<strong>'.$model->$method_rowsless().' - ';
                            }
                        } else {
                            // mark page inactive
                            $pages .= '<li class="page-item"><a class="page-link" href="Index/?id='.$table_row['id'].'&step=next">'.$page.'</a></li>';
                        }
                        $page++;
                    }
                    if( $i === $method_rows ) {
                        //$id_min = $table_row['id'];
                        $id_max = $table_row['id'];
                    } else {
                        $id_max = $table_row['id'];
                    }
                    $i--;
                }
                // create rows marker end
                $rows .= $i.' из '.$model->$method_rows().'</strong>';
                // using model count method
                $method_count = $rules['model_count_method'];
                if( $page !== $model->$method_count() && ( $page - 1 ) !== 1 ) {
                    $pages .= '<li class="page-item"><a class="page-link" href="Index/?id='.$id_max.'&step=next">Next</a></li>';
                }
                $pages .= '</ul>';
                
                return $rows.$pages;
            }
            
            return '<p class="text-danger">HTML_Helper.setPagination - Нет данных для пагинации!</p>';
        }
        
        return '<p class="text-danger">HTML_Helper.setPagination - На входе не массив!</p>';
    }
    
    /**
     * Creates alert.
     *
     * @param $msg
     * @param $class
     *
     * @return mixed
     */
    public static function setAlert( $msg, $class )
    {
        if( !empty($msg) && !empty($class) ) {
            return '<div class="alert '.$class.'">'.$msg.'</div>';
        }
        
        return NULL;
    }
    
    /**
     * Creates invalid feedback.
     *
     * @param $err
     *
     * @return mixed
     */
    public static function setInvalidFeedback( $err )
    {
        if( $err ) {
            return '<div class="invalid-feedback">'.$err.'</div>';
        }
        
        return NULL;
    }
    
    /**
     * Creates valid feedback.
     *
     * @param $err
     * @param $msg
     *
     * @return mixed
     */
    public static function setValidFeedback( $err, $msg )
    {
        if( !$err ) {
            return '<div class="valid-feedback">'.$msg.'</div>';
        }
        
        return NULL;
    }
}
