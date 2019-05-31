<div class="bcontenu">
 	<?php foreach($this->links as $row){
 		if(GroupPermissions::checkPermissions('quicklinks-id'.$row['id']))
 		{echo '<div class="stat" style="height:16px !important;"><a href="'. Yii::app()->getBaseUrl(true).$row->url.'">'.$row->name.'</a></div>';}
 	}	echo '<div class="stat" style="height:16px !important;" onClick="showServ();">Update Customer Licenses</div>'; 	?>
</div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1006,
	    'height'=>810,
		'resizable'=>false,
		'closeOnEscape' => true,
    ),));?>
<div class="bcontenu z-index" id="links">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetQuickLinks::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div> 	
<?php foreach($this->links as $row)
 	{ if(GroupPermissions::checkPermissions('quicklinks-id'.$row['id']))
 		{
 			echo '<div class="stat"><a href="'. Yii::app()->getBaseUrl(true).$row->url.'">'.$row->name.'</a></div>';
 		}
 	} echo '<div class="stat" style=" font-size:14px !important;"  onClick="showServ();">Update Customer Licenses</div>';
 	?>
</div><?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function showServ(){	$('#popupLicenses').removeClass('hidden');}
</script>