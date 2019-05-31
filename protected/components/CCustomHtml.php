<?php
Class CCustomHtml extends CHtml
{
	public static function error($model,$attribute,$htmlOptions=array())
	{
		self::resolveName($model,$attribute); // turn [a][b]attr into attr
		$error=$model->getError($attribute);
		if(!isset($htmlOptions['class']))
			$htmlOptions['class']=self::$errorMessageCss;
		return self::tag(self::$errorContainerTag,$htmlOptions,$error);
	}
}