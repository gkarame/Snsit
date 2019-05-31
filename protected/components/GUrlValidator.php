<?php
class GUrlValidator extends CValidator
{
  public $protocolPattern='/^(http|https)$/i';
  public $uriPattern='/^(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
  public $defaultProtocol='http://';
  public $allowEmpty=true;
  protected function validateAttribute($object,$attribute)
  {
    $value=$object->$attribute;
    if($this->allowEmpty && $this->isEmpty($value))
      return;
    if($validated=$this->validateValue($value))
    {
      $object->$attribute=$validated;
    }
    else
    {
      $message=$this->message!==null?$this->message:Yii::t('yii','{attribute} is not a valid URL.');
      $this->addError($object,$attribute,$message);
    }
  }
  public function validateValue($value)
  {
    if (!is_string($value))
      return false;   
    $parts=explode('://',$value,2);    
    if (count($parts)==1 && $this->defaultProtocol!==null)
      return preg_match($this->uriPattern,$parts[0]) ? $this->defaultProtocol.$value : null;
    else
      return count($parts)==2 && preg_match($this->protocolPattern,$parts[0]) && preg_match($this->uriPattern,$parts[1]) ? $value : null;
  }
}