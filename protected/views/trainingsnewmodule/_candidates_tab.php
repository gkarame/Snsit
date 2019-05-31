   <div> <div class="header_title">  <div class="wrapper_action" id="action_tabs_right">
    <div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
    <div class="action_list actionPanel">
        <div class="headli"></div>
        <div class="contentli"> <?php if(GroupPermissions::checkPermissions('general-trainings','write')){   ?>
                <div class="cover"><div class="li noborder" onclick="sendEmail(this);return false;">Send Invitation</div></div>
                <div class="cover"><div class="li noborder delete" onclick="deleteCandidates(this);">Delete Candidate(s)</div></div>  <?php } ?>  
        </div>   <div class="ftrli"></div>   </div></div>    </div></div>
<?php 
$buttons = array();   $tmp = '';
    if (GroupPermissions::checkPermissions('general-trainings', 'write')){     $tmp = '{update}';	
	$buttons =  array(
                 'update' => array(
                    'label' => Yii::t('translations', 'Edit'), 
                    'imageUrl' => null,
                    'url' => 'Yii::app()->createUrl("trainingsnewmodule/submitFreeInvitation", array("id"=>$data->id))',
                    'options' => array(
                        'onclick' => 'showCandidatesForm(this);return false;'      ),       ),        );  }?>
<div id="candidates_fields">
<?php	$provider = TrainingFreeCandidates::getCandidatesProvider($model->idTrainings);
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'candidates-grid',
    'dataProvider'=>$provider,
    'summaryText' => '',
    'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',
    'columns'=>array(
        array('class'=>'CCheckBoxColumn','id'=>'checkcandidate','htmlOptions' => array('class' => 'item checkbox_grid_candidates'),'selectableRows'=>2,),
       array('name' => 'Name','value' => '$data->eCustomer->name','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'Country','value' => '$data->eCustomer->cCountry->codelkup','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
         array('name' => 'City','value' => '$data->eCustomer->city','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'Contact Name','value' => '$data->contact_name','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'Contact Email','value' => '$data->contact_email','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'Mobile Number','value' => '$data->eCustomer->mobile_number','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'Invitation Sent','value' => '($data->email_sent == 1)? "Yes" : "No"','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),        ),
          array('class'=>'CCustomButtonColumn','template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons,),  ),)); ?>
<?php if(GroupPermissions::checkPermissions("general-trainings","write")) { ?>
<div class="tache">  <div onclick="showCandidatesForm(this,true);" class="new_can margint10"><u><b>+ <?php echo Yii::t('translations', 'NEW Candidate');?></b></u></div>
</div><?php   }   ?></div>
<script>
    function showCandidatesForm(element,NewCan) {
          var url, data;
            if (NewCan) {
                url = "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/submitFreeInvitation');?>";
            } else {
                url = $(element).attr('href');
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {'train_id':<?php echo $model->idTrainings?>}, 
                dataType: "json",
                success: function(data) {
                    if (data) {
                        if (data.status == 'success') {
                            if(NewCan){
                                $('.new_can').hide();
                                $('.new_can').after(data.form);
                            }else{
                                 $(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');
                            }
                            
                        }
                    }
                }
            });
        }
       function saveCandidate(element, id) {
        var url = "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/submitFreeInvitation');?>";
        if (id != 'new') {
            url += '/'+parseInt(id);
        }
        $.ajax({
            type: "POST",
           data: $(element).parents('.new_candidate').serialize() + '&TrainingFreeCandidates['+id+'][id_training]=<?php echo $model->idTrainings;?>' + '&train_id=<?php echo $model->idTrainings;?>',
            url: url, 
            dataType: "json",
            success: function(data) {
                if (data) {
                    if (data.status == 'saved') {
                            $(element).parents('.tache2.new').remove();
                            $('.new_can').show();
                             
                        $.fn.yiiGridView.update('candidates-grid');       
                    } else if (data.status == 'success') {
                          $(element).parents('.tache2.new').replaceWith(data.form);                          
                        }
                }
            }
        });
    }
    function deleteCandidates(element){
        $.ajax({
            type: "POST",
            data: $('.checkbox_grid_candidates input').serialize(),                 
            url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/deleteFreeCandidate', array('id_training' => $model->idTrainings));?>", 
            dataType: "json",
            success: function(data) {
                if (data) {
                    if (data.status == 'sent') {
                         var action_buttons = {
                                "Ok": {
                                    click: function() 
                                    {
                                        $( this ).dialog( "close" );
                                    },
                                    class : 'ok_button'
                                }
                            }
                        $.fn.yiiGridView.update('candidates-grid');
                    }else if (data.status == 'fail') {
                        var action_buttons = {
                                "Ok": {
                                    click: function() 
                                    {
                                        $( this ).dialog( "close" );
                                    },
                                    class : 'ok_button'
                                }
                            }
                        custom_alert('ERROR MESSAGE', data.message, action_buttons);
                        }                   
                }
            }
        });
    }
    function sendEmail(element){
            $.ajax({
            type: "POST",
            data: $('.checkbox_grid_candidates input').serialize(),                 
            url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/SendFreeInviteEmail', array('id_training' => $model->idTrainings));?>", 
            dataType: "json",
            success: function(data) {
                if (data) {
                    if (data.status == 'sent') {
                         var action_buttons = {
                                "Ok": {
                                    click: function() 
                                    {
                                        $( this ).dialog( "close" );
                                    },
                                    class : 'ok_button'
                                }
                            }
                        custom_alert('SUCCESS', "Emails are sent successfully", action_buttons);
                        $.fn.yiiGridView.update('candidates-grid');
                    }else if (data.status == 'fail') {
                        var action_buttons = {
                                "Ok": {
                                    click: function() 
                                    {
                                        $( this ).dialog( "close" );
                                    },
                                    class : 'ok_button'
                                }
                            }
                        custom_alert('ERROR MESSAGE', data.message, action_buttons);
                        }
                        $.fn.yiiGridView.update('candidates-grid');
                }
            }
        });
}
    function disableCheckBoxes(){
            $.ajax({
            type: "POST",
            data: $('.checkbox_grid_candidates input').serialize(),                 
            url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/SendFreeInviteEmail', array('id_training' => $model->idTrainings));?>", 
            dataType: "json",
            success: function(data) {
                if (data) {
                    if (data.status == 'sent') {
                    } else {
                        if (data.status == 'fail') {
                            showErrors(data.message);
                        }
                    }
                    
                }
            }
        });
    }
</script>