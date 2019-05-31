<?php $buttons = array();		$tmp = '';
if (GroupPermissions::checkPermissions('general-trainings', 'write')){
$tmp = '{delete}';
$buttons =  array(
                'delete' => array(
                    'label' => Yii::t('translations', 'Delete'),
                    'imageUrl' => null,
                    'url' => 'Yii::app()->createUrl("trainingsnewmodule/deleteCost", array("id"=>$data->id))',  
                    'options' => array(
                        'class' => 'delete',
                    )
                ),
            ); }?>
<div id="costs_fields"> <?php   $this->widget('zii.widgets.grid.CGridView', array('id'=>'costs-grid',  'dataProvider'=>TrainingCosts::getCostsProvider($model->idTrainings),
    'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','columns'=>array(
        array('name' => 'Type','value' => 'TrainingCosts::getCostTypeLabel($data->cost_type)','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'Amount','value' => 'Yii::app()->format->formatNumber($data->amount)','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
          array('class'=>'CCustomButtonColumn','template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons,),  ),)); ?>
<?php if(GroupPermissions::checkPermissions("general-trainings","write")) { ?>
<div class="tache ">  <div onclick="showCostForm(this);" class="new_cost margint10"><u><b>+ <?php echo Yii::t('translations', 'ADD COST');?></b></u></div>
</div><?php   }   ?></div>
<script>
    function showCostForm(element) {
            $.ajax({
                type: "POST",
                url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/RenderNewCost');?>",
                data: {}, 
                dataType: "json",
                success: function(data) {
                    if (data) {
                        if (data.status == 'success') {
                                $('.new_cost').hide();
                                $('.new_cost').after(data.form);                            
                        }
                    }
                }
            });
        }
       function saveCost(element, type) {
        var url = "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/saveTrainingCost');?>";
        $.ajax({
            type: "POST",
            data:  $(element).parents('.new_cost').serialize()+ '&type='+type+'&id_training=<?php echo $model->idTrainings;?>',                 
            url: url, 
            dataType: "json",
            success: function(data) {
                if (data) {
                    if (data.status == 'saved') {
                            $(element).parents('.tache2.new').remove();
                            $('.new_cost').show(); $.fn.yiiGridView.update('costs-grid');       
                    } else if (data.status == 'fail') {
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
</script>