<?php

namespace mikehaertl\pdftk;

use mikehaertl\shellcommand\Command as BaseCommand;

/**
 * Command
 *
 * This class represents an pdftk shell command. It extends a standard shellcommand
 * and adds pdftk specific features to add options and operations.
 *
 * @author  Michael Härtl <haertl.mike@gmail.com>
 * @license http://www.opensource.org/licenses/MIT
 */
class Command
    extends BaseCommand
{
	// begin Ильяшенко 12.02.2021
	// добавлено для возможности формирования pdf на машине разработчика на Windows
	function __construct() {
	   parent::__construct();
	   if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	   {
		   $this->_command = 'java -jar D:/work/vhost_portal/pdftk-all.jar';
	   }
	}
	// end Ильяшенко 12.02.2021
	
    /**
     * @var string the pdftk binary
     */
    //protected $_command = 'pdftk';
    protected $_command = 'java -jar /var/www/html/vhost_portal/pdftk-all.jar';
    /**
     * @var array list of input files to process as array('name' => $filename, 'password' => $pw) indexed by handle
     */
    protected $_files = [];
    
    /**
     * @var array list of command options, either strings or array with arguments to addArg()
     */
    protected $_options = [];
    
    /**
     * @var string the operation to perform
     */
    protected $_operation;
    
    /**
     * @var string|array operation arguments, e.g. a list of page ranges or a filename or tmp file instance
     */
    protected $_operationArgument = [];
    
    /**
     * @var bool whether to force escaping of the operation argument e.g. for filenames
     */
    protected $_escapeOperationArgument = FALSE;
    
    /**
     * @param string      $name     the PDF file to add for processing
     * @param string      $handle   one or more uppercase letters A..Z to reference this file later.
     * @param string|null $password the owner (or user) password if any
     *
     * @throws \Exception
     * @return Command the command instance for method chaining
     */
    public function addFile( $name, $handle, $password = NULL )
    {
        $this->checkExecutionStatus();
        $file                  = [
            'name' => $name,
            'password' => $password,
        ];
        $this->_files[$handle] = $file;
        
        return $this;
    }
    
    /**
     * @param string           $option   the pdftk option to add
     * @param string|File|null $argument the argument to add, either string, File instance or null if none
     * @param null|bool whether to escape the option. Default is null meaning use Command default setting.
     *
     * @return Command the command instance for method chaining
     */
    public function addOption( $option, $argument = NULL, $escape = NULL )
    {
        $this->_options[] = $argument === NULL ? $option : [ $option, $argument, $escape ];
        
        return $this;
    }
    
    /**
     * @param string $operation the operation to perform
     *
     * @return Command the command instance for method chaining
     */
    public function setOperation( $operation )
    {
        $this->checkExecutionStatus();
        //if($operation == 'fill_form') {
        //    $this->_command = "java -jar /var/www/html/vhost_portal/mcpdf.jar";
        //}
        $this->_operation = $operation;
        
        return $this;
    }
    
    /**
     * @return string|null the current operation or null if none set
     */
    public function getOperation()
    {
        return $this->_operation;
    }
    
    /**
     * @param string $value  the operation argument
     * @param bool   $escape whether to escape the operation argument
     *
     * @return Command the command instance for method chaining
     */
    public function setOperationArgument( $value, $escape = FALSE )
    {
        $this->checkExecutionStatus();
        $this->_operationArgument       = $value;
        $this->_escapeOperationArgument = $escape;
        
        return $this;
    }
    
    /**
     * @return string|array|null the current operation argument as string or array or null if none set
     */
    public function getOperationArgument()
    {
        // Typecast to string in case we have a File instance as argument
        return is_array($this->_operationArgument) ? $this->_operationArgument : (string) $this->_operationArgument;
    }
    
    /**
     * @return int the number of files added to the command
     */
    public function getFileCount()
    {
        return count($this->_files);
    }
    
    /**
     * Add a page range as used by some operations
     *
     * @param int|string|array $start     the start page number or an array of page numbers. If an array, the other
     *                                    arguments will be ignored. $start can also be bigger than $end for pages in reverse order.
     * @param int|string|null  $end       the end page number or null for single page (or list if $start is an array)
     * @param string|null      $handle    the handle of the file to use. Can be null if only a single file was added.
     * @param string|null      $qualifier the page number qualifier, either 'even' or 'odd' or null for none
     * @param string           $rotation  the rotation to apply to the pages.
     *
     * @return Command the command instance for method chaining
     */
    public function addPageRange( $start, $end = NULL, $handle = NULL, $qualifier = NULL, $rotation = NULL )
    {
        $this->checkExecutionStatus();
        if( is_array($start) ) {
            if( $handle !== NULL ) {
                $start = array_map(function( $p ) use ( $handle ) {
                    return $handle.$p;
                }, $start);
            }
            $range = implode(' ', $start);
        } else {
            $range = $handle.$start;
            if( $end ) {
                $range .= '-'.$end;
            }
            $range .= $qualifier.$rotation;
        }
        $this->_operationArgument[] = $range;
        
        return $this;
    }
    
    /**
     * @param string|null $filename the filename to add as 'output' option or null if none
     *
     * @return bool whether the command was executed successfully
     */
    public function execute( $filename = NULL )
    {
        $this->checkExecutionStatus();
        
        if( FALSE /*$this->_operation == 'fill_form'*/ ) {
            $this->_command = 'java -jar /var/www/html/vhost_portal/mcpdf.jar';
            
            $value = $this->_operationArgument ? $this->_operationArgument : NULL;
            if( $value instanceof TmpFile ) {
                $value = (string) $value;
            }
            
            $this->addArg($this->_files['A']['name']);
            $this->addArg($this->_operation);
            $this->addArg('- output - < '.$value .' >');
            $this->addArg($filename);
        } else {
            $value = $this->_operationArgument;
            if (false) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($value).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($value));
                readfile($value);
                exit;
            }
            
            
            $this->processInputFiles();
            $this->processOperation();
            $this->processOptions($filename);
        }
        
        return parent::execute();
    }
    
    /**
     * Process input PDF files and create respective command arguments
     */
    protected function processInputFiles()
    {
        $passwords = [];
        foreach( $this->_files as $handle => $file ) {
            $this->addArg($handle.'=', $file['name']);
            if( $file['password'] !== NULL ) {
                $passwords[$handle] = $file['password'];
            }
        }
        if( $passwords !== [] ) {
            $this->addArg('input_pw');
            foreach( $passwords as $handle => $password ) {
                $this->addArg($handle.'=', $password);
            }
        }
    }
    
    /**
     * Process options and create respective command arguments
     *
     * @param string|null $filename if provided an 'output' option will be added
     */
    protected function processOptions( $filename = NULL )
    {
        // output must be first option after operation
        if( $filename !== NULL ) {
            $this->addArg('output', $filename, TRUE);
        }
        foreach( $this->_options as $option ) {
            if( is_array($option) ) {
                $this->addArg($option[0], $option[1], $option[2]);
            } else {
                $this->addArg($option);
            }
        }
    }
    
    /**
     * Process opearation and create respective command arguments
     */
    protected function processOperation()
    {
        if( $this->_operation !== NULL ) {
            $value = $this->_operationArgument ? $this->_operationArgument : NULL;
            if( $value instanceof TmpFile ) {
                $value = (string) $value;
            }
            $this->addArg($this->_operation, $value, $this->_escapeOperationArgument);
        }
    }
    
    /**
     * Ensure that the command was not exectued yet. Throws exception otherwise.
     */
    protected function checkExecutionStatus()
    {
        if( $this->getExecuted() ) {
            throw new \Exception('Operation was already executed');
        }
    }
}
