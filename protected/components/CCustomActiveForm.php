<?php 
class CCustomActiveForm extends CWidget
{
	public static function validateTabular($models, $attributes=null, $loadInput=true, $json = true)
	{
		$result=array();
		if (!is_array($models))
			$models=array($models);
		foreach ($models as $i=>$model)
		{
			if ($loadInput && isset($_POST[get_class($model)][$i]))
				$model->attributes=$_POST[get_class($model)][$i];
			$model->validate($attributes);
			foreach ($model->getErrors() as $attribute=>$errors)
				$result[CHtml::activeId($model,'['.$i.']'.$attribute)]=$errors;
		}
		return $json ? (function_exists('json_encode') ? json_encode($result) : CJSON::encode($result)) : $result;
	}
	public static function validate($models, $attributes=null, $loadInput=true, $json = true)
	{
		$result=array();
		if(!is_array($models))
			$models=array($models);
		foreach($models as $model)
		{
			if($loadInput && isset($_POST[get_class($model)]))
				$model->attributes=$_POST[get_class($model)];
			$model->validate($attributes);
			foreach($model->getErrors() as $attribute=>$errors)
				$result[CHtml::activeId($model,$attribute)]=$errors;
		}
		return $json ? (function_exists('json_encode') ? json_encode($result) : CJSON::encode($result)) : $result;
	}
}