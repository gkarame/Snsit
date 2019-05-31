<?php  Yii::app()->clientScript->registerScript('search', "	$('.search-form form').submit(function(){		$.fn.yiiGridView.update('supportrequest-grid', {	data: $(this).serialize()	});		return false;	});");?>
	<div id="sp_whole_page"><div class="search-form"><?php $this->renderPartial('_search',array('model' => $model,	'provider' => $provider	)); ?></div>
<?php $searchArray = isset($_GET['SupportRequest']) ? $_GET['SupportRequest'] : Utils::getSearchSession();?>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'supportrequest-grid','afterAjaxUpdate' => "function(id, data){ $('#incidents').html($('#incidents', $.parseHTML(data)));}",
	'dataProvider' => $provider,'summaryText' => '','selectableRows'=>1,'pager'=> Utils::getPagerArray(),  'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','htmlOptions' => array('class' => 'item checkbox_grid_sd'),'selectableRows'=>2),
		array('name' => 'rsr_no','value'=>'$data->renderRSRNumber()','htmlOptions' => array('class' => 'column50'), 'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'id_customer', 'value' => '$data->idCustomer->name','htmlOptions'=>array('class'=>'width152', 'id'=>'cust_prod'),  ),
		array('name' => 'severity','value'=> '$data->severity'	),
        array('name' => 'status','value' => 'SupportRequest::getStatusLabel($data->status)'),
        array('name' => 'sr#','value' => 'SupportRequest::countSRs($data->sr)'),
        array('name' => 'eta','header'=>'Log Date','value' => '(empty($data->adddate)) ? "" : date("d/m/Y", strtotime($data->adddate)) ','htmlOptions'=>array('style'=>'    width: 90px;')),     
		array('name' => 'short_description','value' => '$data->short_description','htmlOptions'=>array('class'=>'width195'),),
		array('name' => 'version', 'header'=> 'WMS V.','value' => '$data->version0->codelkup','htmlOptions'=>array('class'=>'width100'),'headerHtmlOptions' => array('class' => 'width155')),
         array('header'=>'assigned','name' => 'assigned_to','type'=>'raw','htmlOptions'=>array('class'=>'width100'),'value' => '((Users::checkCSManagers(Yii::app()->user->id)) > 0)?  SupportRequest::getRSRUsers($data->id,$data->assigned_to) :  Users::getUsername($data->assigned_to) ',),
        array('name' => 'Hours','value' => 'SupportRequest::countTime($data->id, $data->id_customer, $data->sr)'),
           array( 'class'=>'CCustomButtonColumn',
			'template'=>'{cancel}',
			'htmlOptions'=>array('class' => 'button-column'), 'buttons'=> array(				
				'cancel' => array(	'label' => Yii::t('translations', 'Cancel'), 'imageUrl' => null,
					'click'=>"function(){
                                    $.fn.yiiGridView.update('supportrequest-grid', {
                                        type:'POST',
                                        url:$(this).attr('href'),
                                        success:function(data) {
                                              
                                             $('.search-btn').trigger('click');
                                        }
                                    })
                                    return false;
                              }
                     ",
					'url' => 'Yii::app()->createUrl("supportRequest/cancel", array("id"=>$data->id))',
					'visible' => '$data->status != 7 && $data->status != 6 && (Users::checkCSManagers(Yii::app()->user->id)) > 0', ),
				
				),	

         )
       ),)); ?></div>
<script type="text/javascript">
function triggerSDSearch(status) {	status = parseInt(status);	$('#inv_status').val(status); changestat($('#inv_status').val());	$('.search-btn').trigger('click'); }
function changeInput(value,id_rsr,type){
	$.ajax({ 
 		type: "POST",
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/assigned');?>",
	  	dataType: "json",
	  	data: {'value':value,'id_rsr':id_rsr,'type':type},
	  	success: function(data) {
		  	if (data) {
			  }	
			} 
	}); 
}
function showdropdown(){		document.getElementById('inv_status').style.visibility="visible";	}
function cancelRSR(id)
{
	alert("hi"+ id);
}
function changeDate(id){
	$("input#change_"+id).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker( "show" );
 	$('#ui-datepicker-div').css('top',parseFloat($("input#change_"+id).offset().top) + 25.0);
 	$('#ui-datepicker-div').css('left',parseFloat($("input#change_"+id).offset().left)); }
$(document).on('click','.toggle',function(){
    if ($(this).children().is('.icon-plus')) {
      $('#search_support').addClass('hide');
      $('#icon-hide-show').html('Show Search Bar');
      $(this).children().removeClass('icon-plus');
      $(this).children().addClass('icon-minus');
  }else{
     $(this).children().addClass('icon-plus');
     $(this).children().removeClass('icon-minus');
     $('#icon-hide-show').html('Hide Search Bar');
     $('#search_support').removeClass('hide');
  } });
$( "#export" ).click(function() {
	$.ajax({type: "POST",	data: $('#search_support').serialize()  + '&ajax=supportrequest-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/getExcelGrid');?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if(data.success == 'success')
		  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>";
		  	
  		}	}); });
</script>
