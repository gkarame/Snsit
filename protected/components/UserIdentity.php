<?php
class UserIdentity extends CUserIdentity
{  
    private $_id;
    public function authenticate()
    {
		$record=Users::model()->findByAttributes(array('username'=>$this->username, 'active' => 1));
		if ($record === null)
   		{
   			$customer = CustomersContacts::model()->findByAttributes(array('username'=>$this->username, 'access' => 'Yes'));
   			if ($customer === null)
   			{
				$this->errorCode=self::ERROR_USERNAME_INVALID;
   			}
	        elseif ($customer->password !== ($this->password))
	        {
	            $this->errorCode=self::ERROR_PASSWORD_INVALID;
	        }
	        else
	        {
	            $this->_id = $customer->id;
	            $this->setState('username', $customer->username);
				$this->setState('name', $customer->name);
				$this->setState('isAdmin', false);	
				$this->setState('customer_id', $customer->id_customer);	
	            $this->errorCode=self::ERROR_NONE;	           
	            Yii::app()->user->returnUrl = Yii::app()->createUrl('supportDesk/Index');
	        }
   		}
        else
       {
			if ($record->password !== sha1($this->password))
			{
            	$this->errorCode=self::ERROR_PASSWORD_INVALID;
			}
        	else
        	{ 
			    $sql = "select max(ut.date) as maxdate,p.id from projects_tasks pt , projects_phases pp , projects p , user_time ut where ut.default=0 and ut.amount>0 and pt.id=ut.id_task and  pt.id_project_phase=pp.id and pp.id_project=p.id and p.project_manager=".$record->id." and p.status=1 group by p.id order by ut.date desc";
				$command =Yii::app()->db->createCommand($sql);	     
				$lwd = $command->query();				
				foreach ($lwd as $lw)
	        	{	$twomonths= Date('Y-m-d', strtotime('2 month ago'));	}					
	            $this->_id = $record->id;   $this->setState('username', $record->username); $this->setState('name', $record->fullname);
				$this->setState('isAdmin', true);	 $this->errorCode=self::ERROR_NONE; Yii::app()->user->returnUrl = Yii::app()->user->returnUrl;
	        }
        }
        return !$this->errorCode;
    } 
    public function getId()
    {  return $this->_id; }
}