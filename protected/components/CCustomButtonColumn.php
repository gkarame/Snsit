<?php 
class CCustomButtonColumn extends CButtonColumn
{
	public $deleteConfirmationTitle;
	protected function initDefaultButtons()
	{
		if($this->viewButtonLabel===null)
			$this->viewButtonLabel=Yii::t('zii','View');
		if($this->updateButtonLabel===null)
			$this->updateButtonLabel=Yii::t('zii','Update');
		if($this->deleteButtonLabel===null)
			$this->deleteButtonLabel=Yii::t('zii','Delete');
		if($this->viewButtonImageUrl===null)
			$this->viewButtonImageUrl=$this->grid->baseScriptUrl.'/view.png';
		if($this->updateButtonImageUrl===null)
			$this->updateButtonImageUrl=$this->grid->baseScriptUrl.'/update.png';
		if($this->deleteButtonImageUrl===null)
			$this->deleteButtonImageUrl=$this->grid->baseScriptUrl.'/delete.png';
		if($this->deleteConfirmation===null)
			$this->deleteConfirmation=Yii::t('zii','Are you sure you want to delete this item?');
		if($this->deleteConfirmationTitle===null)
			$this->deleteConfirmationTitle=Yii::t('zii','DELETE MESSAGE');		
		foreach(array('view','update','delete') as $id)
		{
			$button=array(
				'label'=>$this->{$id.'ButtonLabel'},
				'url'=>$this->{$id.'ButtonUrl'},
				'imageUrl'=>$this->{$id.'ButtonImageUrl'},
				'options'=>$this->{$id.'ButtonOptions'},	);
			if(isset($this->buttons[$id]))
				$this->buttons[$id]=array_merge($button,$this->buttons[$id]);
			else
				$this->buttons[$id]=$button;
		}
		if(!isset($this->buttons['delete']['click']))
		{
			if(Yii::app()->request->enableCsrfValidation)
			{
				$csrfTokenName = Yii::app()->request->csrfTokenName;
				$csrfToken = Yii::app()->request->csrfToken;
				$csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
			}
			else
				$csrf = '';
			
			if($this->afterDelete===null)
				$this->afterDelete='function(){}';

$this->buttons['delete']['click']=<<<EOD
function() {
	var th = this,
	afterDelete = $this->afterDelete;
	$("#confirm_dialog .confirm_title").html("$this->deleteConfirmationTitle");
	$("#confirm_dialog .confirm_content").html("$this->deleteConfirmation");
	
	$("#confirm_dialog").dialog({
        resizable: false,
        draggable: true,
        closeOnEscape: true,
        width: 'auto',
        buttons: {
	        "Yes": {
	        	class: "yes_button",
		        click: function() 
		        {
		            $( this ).dialog( "close" );
					jQuery('#{$this->grid->id}').yiiGridView('update', {
						type: 'POST',
						url: jQuery(th).attr('href'),$csrf
						success: function(data) {
							jQuery('#{$this->grid->id}').yiiGridView('update');
							afterDelete(th, true, data);
						},
						error: function(XHR) {
							return afterDelete(th, false, XHR);
						}
					});
					return false;
				}
	        },
	        "No": {
				class: "no_button",
				click:function() 
			        {
			            $( this ).dialog( "close" );
			            return false;
					}
	        },
		},
        modal: true,
        width: 379,
        height: 235,
        dialogClass: 'confirm_dialog'
    });
    return false;
}
EOD;
		}
	}
	
}