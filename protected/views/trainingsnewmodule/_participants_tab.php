<?php $buttons = array();		$tmp = '';
    if (GroupPermissions::checkPermissions('general-trainings', 'write')) {
$tmp = '{update} {delete}';
$buttons =  array(
                'update' => array(
                    'label' => Yii::t('translations', 'Edit'), 
                    'imageUrl' => null,
                    'url' => 'Yii::app()->createUrl("trainingsnewmodule/ManageParticipant", array("id"=>$data->id))',
                    'options' => array(
                        'onclick' => 'showParticipantForm(this);return false;'
                    ),  ),
                'delete' => array(
                    'label' => Yii::t('translations', 'Delete'),
                    'imageUrl' => null,
                    'url' => 'Yii::app()->createUrl("trainingsnewmodule/deleteParticipant", array("id"=>$data->id))',  
                    'options' => array(
                        'class' => 'delete',
                    ) ),      );  }?>
<?php	$results=trainingsnewmodule::getSeatsCustomers();
if (!empty($results)) {?> 
<div class="header_title">  <span class="red_title"><?php echo Yii::t('translations', 'Suggested Customers');?></span></div>    
<div class="bcontenu">   <div id="widget_oldestInvoices">    <div class="boardrow color333">
            <div class="width203 inline-block"  style ="padding-bottom: 5px !important;">
                <span class="width203"><b>Customer</b></span>
            </div>            <div class="width165 inline-block "  style ="padding-bottom: 5px !important;">
                <span class="width165"><b>Support Plan</b></span>
            </div>            <div class="width165 inline-block"  style ="padding-bottom: 5px !important;">
                <span class="width165"><b># Free Seats</b></span>
            </div>         <div class="width165 inline-block"  style ="padding-bottom: 5px !important;">
                <span class="width165"><b>Remaining Free Seats</b></span>
            </div>        </div>      <?php foreach ($results as $result) { ?>     <div class="boardrow odd-even default" style ="padding-bottom: 1px !important;padding-top: 1px !important;" >    
            <div class="width203 inline-block" >    <span  class="width203"><?php echo Customers::getnamebyid($result['customer']); ?></span>
            </div><div class="width165 inline-block" style ="padding-bottom: 1px !important;padding-top: 1px !important;">
                <span  class="width165"><?php echo Codelkups::getCodelkup($result['support_service']); ?></span>    </div>           
            <div class="width165 inline-block" style ="padding-bottom: 1px !important;padding-top: 1px !important;">
                <span  class="width165"><?php echo $result['limit']; ?></span>   </div>         
            <div class="width165 inline-block" style ="padding-bottom: 1px !important;padding-top: 1px !important;">
                <span  class="width165"><?php echo ($result['limit']-MaintenanceServices::getActual($result['id'], 9, 1)); ?></span>
            </div>     </div>      <?php }?>   </div></div> <?php }?>
<div id="participant_fields"><?php   $this->widget('zii.widgets.grid.CGridView', array('id'=>'connections-grid',  'dataProvider'=>$model->getParticipantsProvider(),
    'summaryText' => '',  'pager'=> Utils::getPagerArray(),   'template'=>'{items}{pager}',    'columns'=>array(  
    	array(
            'header'=>'participant_number',
            'value'=>'$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
        ),  'firstname',   'lastname',
        'title',     'email',      array('header'=>Yii::t('translations', 'Customer'),'value'=>'isset($data->eCustomer)?$data->eCustomer->name : " "','name' => 'eCustomer.name','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),        ),  array(
                'class'=>'CCustomButtonColumn','template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons,),   ),)); ?>
<?php if(GroupPermissions::checkPermissions("general-trainings","write")) { ?>
<div class="tache new_cont">  <div onclick="showParticipantForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW PARTICIPANT');?></b></u></div>
</div><?php   }   ?></div>
<script>
    function showParticipantForm(element, newConn) {
        if (false) {   $(element).addClass('invalid');     } else {           $(element).removeClass('invalid');        }
        if (!$(element).hasClass('invalid')) {
            var url, data;
            if (newConn) {
                url = "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/ManageParticipant');?>";
                update = 1;
            } else {
                url = $(element).attr('href');
                update = 0;
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {'update':update,'train_id':<?php echo $model->idTrainings?>}, 
                dataType: "json",
                success: function(data) {
                    if (data) {
                        if (data.status == 'success') {
                            if (newConn) {
                                $('.new_cont').hide();
                                $('.new_cont').after(data.form);
                            } else {
                                $(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>');
                            }
                        }
                    }
                }
            });
        } else {
            alert('The form is not valid!');
        }
    }
     function saveParticipant(element, id) {
        var url = "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/ManageParticipant');?>";
        if (id != 'new') {
            url += '/'+parseInt(id);
        }        
        $.ajax({
            type: "POST",
            data: $(element).parents('.new_participant').serialize() + '&TrainingParticipants['+id+'][id_training]=<?php echo $model->idTrainings;?>&update=1',                 
            url: url, 
            dataType: "json",
            success: function(data) {
                if (data) {
                    if (data.status == 'saved') {
                            $(element).parents('.tache.new').remove();
                            $('.new_cont').show();
                             
                        $.fn.yiiGridView.update('connections-grid');       
                    } else if (data.status == 'success') {
                            $(element).parents('.tache.new').replaceWith(data.form);
                        }
                }
            }
        });
    }
</script>