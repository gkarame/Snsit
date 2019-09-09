<?php

/**
 * XUploadAction
 * =============
 * Basic upload functionality for an action used by the xupload extension.
 *
 * XUploadAction is used together with XUpload and XUploadForm to provide file upload funcionality to any application
 *
 * You must configure properties of XUploadAction to customize the folders of the uploaded files.
 *
 * Using XUploadAction involves the following steps:
 *
 * 1. Override CController::actions() and register an action of class XUploadAction with ID 'upload', and configure its
 * properties:
 * ~~~
 * [php]
 * class MyController extends CController
 * {
 *     public function actions()
 *     {
 *         return array(
 *             'upload'=>array(
 *                 'class'=>'xupload.actions.XUploadAction',
 *                 'path' =>Yii::app() -> getBasePath() . "/../uploads",
 *                 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads",
 *                 'subfolderVar' => "parent_id",
 *             ),
 *         );
 *     }
 * }
 *
 * 2. In the form model, declare an attribute to store the uploaded file data, and declare the attribute to be validated
 * by the 'file' validator.
 * 3. In the controller view, insert a XUpload widget.
 *
 * ###Resources
 * - [xupload](http://www.yiiframework.com/extension/xupload)
 *
 * @version 0.3
 * @author Asgaroth (http://www.yiiframework.com/user/1883/)
 */
class CustomXUploadAction extends CAction {

    /**
     * XUploadForm (or subclass of it) to be used.  Defaults to XUploadForm
     * @see XUploadAction::init()
     * @var string
     * @since 0.5
     */
    public $formClass = 'xupload.models.XUploadForm';

    /**
     * Name of the model attribute referring to the uploaded file.
     * Defaults to 'file', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $fileAttribute = 'file';

    /**
     * Name of the model attribute used to store mimeType information.
     * Defaults to 'mime_type', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $mimeTypeAttribute = 'mime_type';

    /**
     * Name of the model attribute used to store file size.
     * Defaults to 'size', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $sizeAttribute = 'size';

    /**
     * Name of the model attribute used to store the file's display name.
     * Defaults to 'name', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $displayNameAttribute = 'name';

    /**
     * Name of the model attribute used to store the file filesystem name.
     * Defaults to 'filename', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $fileNameAttribute = 'filename';

    /**
     * The query string variable name where the subfolder name will be taken from.
     * If false, no subfolder will be used.
     * Defaults to null meaning the subfolder to be used will be the result of date("mdY").
     *
     * @see XUploadAction::init().
     * @var string
     * @since 0.2
     */
    public $subfolderVar;

    /**
     * Path of the main uploading folder.
     * @see XUploadAction::init()
     * @var string
     * @since 0.1
     */
    public $path;

    /**
     * Public path of the main uploading folder.
     * @see XUploadAction::init()
     * @var string
     * @since 0.1
     */
    public $publicPath;

    /**
     * @var boolean dictates whether to use sha1 to hash the file names
     * along with time and the user id to make it much harder for malicious users
     * to attempt to delete another user's file
     */
    public $secureFileNames = false;

    /**
     * Name of the state variable the file array is stored in
     * @see XUploadAction::init()
     * @var string
     * @since 0.5
     */
    public $stateVariable = 'xuploadFiles';

    /**
     * The resolved subfolder to upload the file to
     * @var string
     * @since 0.2
     */
    private $_subfolder = "";

    /**
     * The form model we'll be saving our files to
     * @var CModel (or subclass)
     * @since 0.5
     */
    private $_formModel;

    /**
     * Initialize the propeties of pthis action, if they are not set.
     *
     * @since 0.1
     */
    public function init( ) {
        if( !isset( $this->path ) ) {
            $this->path = realpath( Yii::app( )->getBasePath( )."/../uploads" );
        }
        if( !is_dir( $this->path ) ) {
            mkdir( $this->path, 0777, true );
            chmod ( $this->path , 0777 );
            //throw new CHttpException(500, "{$this->path} does not exists.");
        } else if( !is_writable( $this->path ) ) {
            chmod( $this->path, 0777 );
            //throw new CHttpException(500, "{$this->path} is not writable.");
        }
        
        /*if( $this->subfolderVar === null ) {
            $this->_subfolder = Yii::app( )->request->getQuery( $this->subfolderVar, date( "mdY" ) );
        } else if($this->subfolderVar !== false ) {
            $this->_subfolder = date( "mdY" );
        }*/
		
        if( !isset($this->_formModel)) {
            $this->formModel = Yii::createComponent(array('class'=>$this->formClass));
        }

        if($this->secureFileNames) {
            $this->formModel->secureFileNames = true;
        }
    }

    /**
     * The main action that handles the file upload request.
     * @since 0.1
     * @author Asgaroth
     */
    public function run( ) 
    {
        $this->sendHeaders();
        $this->handleDeleting() or $this->handleUploading();
    }
    
    protected function sendHeaders()
    {
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) 
        {
            header('Content-type: application/json');
        } 
        else 
       {
            header('Content-type: text/plain');
        }
    }
    /**
     * Removes temporary file from its directory and from the session
     *
     * @return bool Whether deleting was meant by request
     */
    /*protected function handleDeleting()
    {	
        if (isset($_REQUEST["_method"]) && $_REQUEST["_method"] == "delete") 
        {
            $success = false;
            if ($_REQUEST["file"] !== '.') 
            {	
            	if (isset($_REQUEST['notSaved']) && $_REQUEST['notSaved']) 
            	{	
	            	if (Yii::app()->user->hasState($this->stateVariable)) 
	            	{
		                // pull our userFiles array out of state and only allow them to delete
		                // files from within that array
		                $userFiles = Yii::app()->user->getState($this->stateVariable, array());
		                if ($this->fileExists($userFiles[$_REQUEST["file"]])) 
		                {
		                    $success = $this->deleteFile($userFiles[$_REQUEST["file"]]);
		                    if ($success) 
		                    {
		                        unset($userFiles[$_REQUEST["file"]]); // remove it from our session and save that info
		                        Yii::app()->user->setState($this->stateVariable, $userFiles);
		                    }
		                }
	           		}
            	}	
            }
            
            echo json_encode($success);
            return true;
        }
        return false;
    }*/
	protected function handleDeleting()
    {	
        if (isset($_REQUEST["_method"]) && $_REQUEST["_method"] == "delete") 
        {
            $success = false;
            if ($_REQUEST["file"] !== '.') 
            {	
            	if (isset($_REQUEST['notSaved']) && $_REQUEST['notSaved']) 
            	{	
	            	if (Yii::app()->user->hasState($this->stateVariable)) 
	            	{
		                // pull our userFiles array out of state and only allow them to delete
		                // files from within that array
		                echo($_REQUEST["file"]);
		               	if ($this->fileExists($userFiles[$_REQUEST["file"]])) 
		                {
		                    $success = $this->deleteFile($_REQUEST["file"]);
		                    if ($success) 
		                    {
		                        unset($userFiles[$_REQUEST["file"]]); // remove it from our session and save that info
		                        Yii::app()->user->setState($this->stateVariable, $userFiles);
		                    }
		                }
	           		}
            	}else{
            		$path = $this->getPath();
            		$userFiles = array(
			            "path" => $path.$_REQUEST["file"],
			        );
			        print_r($userFiles);
			        if($this->fileExists($userFiles))
			        	echo ":D";
            	}	
            }
            
            echo json_encode($success);
            return true;
        }
        return false;
    }
    /**
     * Uploads file to temporary directory
     *
     * @throws CHttpException
     */
    protected function handleUploading()
    {
        $this->init();
        $model = $this->formModel;
        $model->{$this->fileAttribute} = CUploadedFile::getInstance($model, $this->fileAttribute);
		if ($model->{$this->fileAttribute} !== null) 
		{
            $model->{$this->mimeTypeAttribute} = $model->{$this->fileAttribute}->getType();
            //if (preg_match('/image/', $model->{$this->mimeTypeAttribute})) 
                            
            $model->{$this->sizeAttribute} = $model->{$this->fileAttribute}->getSize();
            $model->{$this->displayNameAttribute} = $model->{$this->fileAttribute}->getName();
            $model->{$this->fileNameAttribute} = $model->{$this->displayNameAttribute};

			if ($model->validate()) 
			{
            	// If we have to save the upload directly and link it to a model, not only on model save
				if (isset($_POST['modelId'], $_POST['path'], $_POST['publicPath'])) 
				{
					$_POST['modelId'] = (int)$_POST['modelId'];
	                $saved = true;
					$this->path = $_POST['path'];
					$this->publicPath = $_POST['publicPath'];
				}
				
				$path = $this->getPath();
				$publicPath = $this->getPublicPath();
                if (!is_dir($path)) 
                {
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                }
                
                switch ($this->stateVariable)
                {
                	case "expenses_uploads":
		                $query = "SELECT COUNT(*) FROM `expenses_uploads` WHERE expenses_id='{$_POST['modelId']}'";
						$number_file = Yii::app()->db->createCommand($query)->queryScalar();
						if ($number_file < 5) 
						{
							$expense_upload = true;
							$model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
		                	chmod($path . $model->{$this->fileNameAttribute}, 0777);
		                	$deleteUrl = Yii::app()->controller->createUrl("expenses/deleteUpload", array(
	                            "_method" => "delete",
	                            "file" => $model->{$this->fileNameAttribute},
		                		"model_id" => $_POST['modelId']
	                        ));
						}
						break;
                	case "supportdesk_uploads": 
	                	$query = "SELECT COUNT(*) FROM `support_desk` WHERE id='{$_POST['modelId']}'";
	                	$number_file = Yii::app()->db->createCommand($query)->queryScalar();
                		$model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
                		chmod($path . $model->{$this->fileNameAttribute}, 0777);
                		$deleteUrl = Yii::app()->controller->createUrl("supportDesk/deleteUpload", array(
                				"_method" => "delete",
                				"file" => $model->{$this->fileNameAttribute},
                				"model_id" => $_POST['modelId']
                		));
                		break;
                	case "supportdesk_uploads_comm":
	                	$query = "SELECT COUNT(*) FROM `support_desk_comments` WHERE id='{$_POST['modelId']}'";
	                	$number_file = Yii::app()->db->createCommand($query)->queryScalar();
                		$model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
                		chmod($path . $model->{$this->fileNameAttribute}, 0777);
                		$deleteUrl = Yii::app()->controller->createUrl("supportDesk/deleteUpload", array(
                				"_method" => "delete",
                				"file" => $model->{$this->fileNameAttribute},
                				"model_id" => $_POST['modelId'],
                				"comm" => "Yes"
                		));
                		break;
                	case "rsr_uploads": 
	                	$query = "SELECT COUNT(*) FROM `rsr` WHERE id='{$_POST['modelId']}'";
	                	$number_file = Yii::app()->db->createCommand($query)->queryScalar();
                		$model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
                		chmod($path . $model->{$this->fileNameAttribute}, 0777);
                		$deleteUrl = Yii::app()->controller->createUrl("supportRequest/deleteUpload", array(
                				"_method" => "delete",
                				"file" => $model->{$this->fileNameAttribute},
                				"model_id" => $_POST['modelId']
                		));
                		break;
                	case "rsr_uploads_comm":
	                	$query = "SELECT COUNT(*) FROM `rsr_comments` WHERE id='{$_POST['modelId']}'";
	                	$number_file = Yii::app()->db->createCommand($query)->queryScalar();
                		$model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
                		chmod($path . $model->{$this->fileNameAttribute}, 0777);
                		$deleteUrl = Yii::app()->controller->createUrl("supportRequest/deleteUpload", array(
                				"_method" => "delete",
                				"file" => $model->{$this->fileNameAttribute},
                				"model_id" => $_POST['modelId'],
                				"comm" => "Yes"
                		));
                		break;
                	default: 
		                $model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
		                chmod($path . $model->{$this->fileNameAttribute}, 0777);
	               		if ($this->stateVariable == 'eas')
	                	{
	                		$deleteUrl = Yii::app()->controller->createUrl("eas/deleteUpload", array(
	                            "_method" => "delete",
	                            "file" => $model->{$this->fileNameAttribute},
	                			"model_id" => $_POST['modelId']
	                        ));
	                	}else if($this->stateVariable == 'maintenance')
	                	{
	                		$deleteUrl = Yii::app()->controller->createUrl("maintenance/deleteUpload", array(
	                            "_method" => "delete",
	                            "file" => $model->{$this->fileNameAttribute},
	                			"model_id" => $_POST['modelId']
	                        ));
	                	}else if($this->stateVariable == 'customers_conn'){
	                		$deleteUrl = Yii::app()->controller->createUrl("customers/deleteUploadConnFile", array(
	                            "_method" => "delete",
	                            "file" => $model->{$this->fileNameAttribute},
	                			"model_id" => $_POST['modelId']
	                        ));
	                	}
	                	break;
                }
				
				if (!isset($saved)) 
				{
                	$returnValue = $this->beforeReturn();	
                }
                else
				{
	              	switch ($this->stateVariable)
	              	{
	              		case "expenses_uploads":
	              			if (isset($expense_upload))
		              		{
		              			try {
		              				$query = "INSERT INTO `{$this->stateVariable}` (expenses_id,file) VALUES ('{$_POST['modelId']}', '{$model->name}')";
									Yii::app()->db->createCommand($query)->execute();
									$query = "SELECT COUNT(*) FROM `expenses_uploads` WHERE expenses_id='{$_POST['modelId']}'";
									$number_file = Yii::app()->db->createCommand($query)->queryScalar();
									$query = "UPDATE `expenses` SET number_file=$number_file WHERE id='{$_POST['modelId']}'";
									Yii::app()->db->createCommand($query)->execute();
									$returnValue = true;
		              			}
		              			catch (Exception $e) {
									$returnValue = 'Problem saving file into database';
								}
		              		}
		              		else 
		              		{
		              			 $returnValue = 'full';
		              		}
	              			break;
	              		case "supportdesk_uploads":
	              			try {
		              			$query = "INSERT INTO `support_desk_files` (id_support_desk,filename) VALUES (NULL, '{$model->name}')";
		              			Yii::app()->db->createCommand($query)->execute();//die("dsf");
		              			$id = Yii::app()->db->createCommand("SELECT id FROM support_desk_files ORDER BY id desc LIMIT 1")->queryScalar();
			              		Yii::app()->session['id_files'] .= $id.",";
		              			$returnValue = true;
		              		}
		              		catch (Exception $e) {
		              			$returnValue = 'Problem saving file into database';
		              		}
	              			break;
	              		case "rsr_uploads":
	              			try {
		              			$query = "INSERT INTO `rsr_files` (id_rsr,filename) VALUES (NULL, '{$model->name}')";
		              			Yii::app()->db->createCommand($query)->execute();//die("dsf");
		              			$id = Yii::app()->db->createCommand("SELECT id FROM rsr_files ORDER BY id desc LIMIT 1")->queryScalar();
			              		Yii::app()->session['id_files'] .= $id.",";
		              			$returnValue = true;
		              		}
		              		catch (Exception $e) {
		              			$returnValue = 'Problem saving file into database';
		              		}
	              			break;
                        case "installationrequests_uploads":
                        try {
                                $query = "INSERT INTO `installation_requests_attachments` (id_ir,filename) VALUES ('{$_POST['modelId']}', '{$model->name}')";
                                $nr = Yii::app()->db->createCommand($query)->execute();
                                $id = Yii::app()->db->createCommand("SELECT id FROM installation_requests_attachments ORDER BY id desc LIMIT 1")->queryScalar();
                                Yii::app()->session['id'] .= $id.",";
                                $query = "UPDATE `installation_requests` SET files = '1' WHERE id='{$_POST['modelId']}'";
                                
                                Yii::app()->db->createCommand($query)->execute();
                                $returnValue = true;
                            }
                            catch (Exception $e) {
                                //print_r($e->getMessage());
                                $returnValue = 'Problem saving file into database  ';
                            }
                        break;
	              		case "supportdesk_uploads_comm":
	              			try {
			              		$query = "INSERT INTO `support_desk_comm_files` (id_support_desk,filename) VALUES ('{$_POST['modelId']}', '{$model->name}')";
			              		$nr = Yii::app()->db->createCommand($query)->execute();
			              		$id = Yii::app()->db->createCommand("SELECT id FROM support_desk_comm_files ORDER BY id desc LIMIT 1")->queryScalar();
			              		Yii::app()->session['id'] .= $id.",";
			              		$query = "UPDATE `support_desk_comments` SET files = '1' WHERE id='{$_POST['modelId']}'";
			              		
			              		Yii::app()->db->createCommand($query)->execute();
			              		$returnValue = true;
			              	}
			              	catch (Exception $e) {
			              		$returnValue = 'Problem saving file into database';
			              	}
	              			break;
	              		case "rsr_uploads_comm":
	              			try {
			              		$query = "INSERT INTO `rsr_comm_files` (id_rsr,filename) VALUES ('{$_POST['modelId']}', '{$model->name}')";
			              		$nr = Yii::app()->db->createCommand($query)->execute();
			              		$id = Yii::app()->db->createCommand("SELECT id FROM rsr_comm_files ORDER BY id desc LIMIT 1")->queryScalar();
			              		Yii::app()->session['id'] .= $id.",";
			              		$query = "UPDATE `rsr_comments` SET files = '1' WHERE id='{$_POST['modelId']}'";
			              		
			              		Yii::app()->db->createCommand($query)->execute();
			              		$returnValue = true;
			              	}
			              	catch (Exception $e) {
			              		$returnValue = 'Problem saving file into database';
			              	}
	              			break;
	              		case "customers_conn":
	              			try {
	              				Yii::app()->session['customers_conn'] = $model->name;
			              		$returnValue = true;
			              	}
			              	catch (Exception $e) {
			              		$returnValue = 'Problem saving file into database';
			              	}
	              			break;
	              		default:
	              			try 
		                	{
                                if ($this->stateVariable == "maintenance")
                                {
                                    $query = "SELECT file FROM `{$this->stateVariable}` WHERE id_maintenance='{$_POST['modelId']}'";
                                }
                                else
                                {
                                    $query = "SELECT file FROM `{$this->stateVariable}` WHERE id='{$_POST['modelId']}'";
                                }

                                $filename = Yii::app()->db->createCommand($query)->queryScalar();
                                if ($filename != $model->{$this->fileNameAttribute})
                                {
                                    is_file( $path.$filename ) && $path.$filename[0] !== '.' && unlink( $path.$filename );
                                }

                                if ($this->stateVariable == "maintenance")
                                {
                                    $query = "UPDATE `{$this->stateVariable}` SET file='{$model->name}' WHERE id_maintenance='{$_POST['modelId']}'";
                                }
                                else
                                {
                                    $query = "UPDATE `{$this->stateVariable}` SET file='{$model->name}' WHERE id='{$_POST['modelId']}'";
                                }
                                Yii::app()->db->createCommand($query)->execute();
                                $returnValue = true;
							}
							catch (Exception $e) 
							{
								$returnValue = 'Problem saving file into database';
							}
	              			break;
					}
				}
			
				if ($returnValue === true) 
				{
					echo json_encode(array(array(
						"name" => $model->{$this->displayNameAttribute},
						"type" => $model->{$this->mimeTypeAttribute},
						"size" => $model->{$this->sizeAttribute},
						"url" => $this->getFileUrl($model->{$this->fileNameAttribute}),
						"thumbnail_url" => $model->getThumbnailUrl($publicPath),
						"delete_url" => isset($deleteUrl) ? $deleteUrl : $this->getController()->createUrl($this->getId(), array(
								"_method" => "delete",
								"file" => $model->{$this->fileNameAttribute},
								"notSaved" => !isset($saved),  
							)),
	                        "delete_type" => "GET"
	                    )));
				} 
				else 
				{
					echo json_encode(array(array("error" => $returnValue)));
					Yii::log("XUploadAction: " . $returnValue, CLogger::LEVEL_ERROR, "xupload.actions.XUploadAction");
				}
			} 
			else 
			{
				echo json_encode(array(array("error" => $model->getErrors($this->fileAttribute),)));
				Yii::log("XUploadAction: " . CVarDumper::dumpAsString($model->getErrors()), CLogger::LEVEL_ERROR, "xupload.actions.XUploadAction");
			}
		} 
		else 
		{
			throw new CHttpException(500, "Could not upload file");
		}
	}

    /**
     * We store info in session to make sure we only delete files we intended to
     * Other code can override this though to do other things with state, thumbnail generation, etc.
     * @since 0.5
     * @author acorncom
     * @return boolean|string Returns a boolean unless there is an error, in which case it returns the error message
     */
    protected function beforeReturn() {
        $path = $this->getPath();
        
        // Now we need to save our file info to the user's session
        $userFiles = Yii::app( )->user->getState( $this->stateVariable, array());

        $userFiles[$this->formModel->{$this->fileNameAttribute}] = array(
            "path" => $path.$this->formModel->{$this->fileNameAttribute},
            //the same file or a thumb version that you generated
            "thumb" => $path.$this->formModel->{$this->fileNameAttribute},
            "filename" => $this->formModel->{$this->fileNameAttribute},
            'size' => $this->formModel->{$this->sizeAttribute},
            'mime' => $this->formModel->{$this->mimeTypeAttribute},
            'name' => $this->formModel->{$this->displayNameAttribute},
        );
        Yii::app( )->user->setState( $this->stateVariable, $userFiles );

        return true;
    }

    /**
     * Returns the file URL for our file
     * @param $fileName
     * @return string
     */
    protected function getFileUrl($fileName) {
        return $this->getPublicPath().$fileName;
    }

    /**
     * Returns the file's path on the filesystem
     * @return string
     */
    protected function getPath() {
        $path = ($this->_subfolder != "") ? "{$this->path}/{$this->_subfolder}/" : "{$this->path}";
        return $path;
    }

    /**
     * Returns the file's relative URL path
     * @return string
     */
    protected function getPublicPath() {
        return ($this->_subfolder != "") ? "{$this->publicPath}/{$this->_subfolder}/" : "{$this->publicPath}";
    }

    /**
     * Deletes our file.
     * @param $file
     * @since 0.5
     * @return bool
     */
    protected function deleteFile($file) {
        return unlink($file['path']);
    }

    /**
     * Our form model setter.  Allows us to pass in a instantiated form model with options set
     * @param $model
     */
    public function setFormModel($model) {
        $this->_formModel = $model;
    }

    public function getFormModel() {
        return $this->_formModel;
    }

    /**
     * Allows file existence checking prior to deleting
     * @param $file
     * @return bool
     */
    protected function fileExists($file) {
        return is_file( $file['path'] );
    }
}
