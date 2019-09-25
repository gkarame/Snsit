<div class=" training_bg"><?php $can_modify=true; ?> </div>
<fieldset id="header_fieldset">
<tabel><tr>
<?php if(GroupPermissions::checkPermissions('ir-general-installationrequests', 'write')){ ?>
<td>
    <div class="textBox inline-block">
        <div class="input_text_desc padding_smaller"> <?= CHtml::label('Source','source_type') ?>
            <div class="" style="margin-top: 10px;">
                <?php  ?>
                <?=CHtml::radioButtonList('source_type',$model->maintenance,['Project','Support Plan'],array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline-block;margin-left: 20px;margin-top: 3px;'),'onchange' => 'changeSource(this)'))?>
            </div>
            <?php echo CCustomHtml::error($model,'project'); ?>
        </div>
    </div>
</td>
        <td><div class="textBox inline-block " id="project_block" style="width:200px;">
                <?php
                    $type_project = ((int)$model->maintenance === 1)?'maintenance':'project';
                    $projects = Projects::getAllParentProjectsMaintSelect($model->customer,$type_project);
                ?>
                <div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,((int)$model->maintenance === 1)?'Support plans':'Projects'); ?></div>
                <div class="input_text">
                    <div class="hdselect">
                        <select style="width:190px;" class="" value="<?=$model->project?>" name="InstallationRequests[project]" id="InstallationRequests_project">
                            <?php foreach ($projects as $project):?>
                                <option <?if((int)$project['id'] === (int)$model->project):?> selected="selected" <?php endif ?> value="<?=$project['id']?>"><?=$project['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php echo CCustomHtml::error($model, "project", array('id'=>"Installationrequests_project_em_")); ?>
            </div></td>
<td><div class="textBox inline-block bigger_amt2">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"expected_starting_date"); ?></div>
		<div class="input_text">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'expected_starting_date', 
			        'value' => $model->expected_starting_date,'cssFile' => false,'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width111'),)); ?>
		</div>
		<?php echo CCustomHtml::error($model, "expected_starting_date", array('id'=>"Installationrequests_start_date_em_")); ?>
	</div>
</td></tr>
<tr><td>
	<div class="textBox inline-block ">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"deadline_date"); ?></div>
		<div class="input_text">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'deadline_date','value' => $model->deadline_date,
			    	'cssFile' => false,'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width81','readonly'=>true),)); ?>
		</div>
		<?php echo CCustomHtml::error($model, "deadline_date", array('id'=>"Installationrequests_end_date_em_")); ?>
	</div>
</td>
<td><div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"installation_locally"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "installation_locally", InstallationRequests::installation_locally(), array('onchange' => 'environment_change(this);','class'=>'input_text_value','value'=>$model->installation_locally)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "installation_locally", array('id'=>"Installationrequests_installation_locally_em_")); ?>
</div></td>
<td>
<div class="textBox cc inline-block">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"installation_location"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "installation_location", InstallationRequests::getInstallLocationList(), array('prompt' => ' ','style'=>'width:93px;','class'=>'','value'=>$model->installation_location)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "installation_location", array('id'=>"Installationrequests_installation_location_em_")); ?>
</div></td></tr>
<tr><td>
<div class="textBox inline-block ">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"disaster_recovery"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "disaster_recovery", InstallationRequests::getDisasterList(), array('style'=>'width:158px;','class'=>'input_text_value','value'=>$model->disaster_recovery)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "disaster_recovery", array('id'=>"Installationrequests_disaster_recovery_em_")); ?>
</div></td><br>
<td>
<div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"assigned_to"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "assigned_to", InstallationRequests::getAssignedToListEdit(), array('class'=>'input_text_value','prompt'=>'','value'=>($model->assigned_to !=null)?$model->assigned_to : '' , 'disabled' => ($model->status == InstallationRequests::STATUS_COMPLETED)?true:false)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "assigned_to", array('id'=>"Installationrequests_assigned_to_em_")); ?>
</div></td><td>
<div class="textBox  inline-block " style="width:440px;">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"notes"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextField($model, 'notes',array('autocomplete'=>'off','style'=>' width:440px;resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "notes", array('id'=>"Installationrequests_notes_em_")); ?>
</div>
</td>
</tr>
<?php } ?>
</tabel>
<div style="right:70px;top:75%;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
<div style="color:#333;top:75%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
<div class="horizontalLine smaller_margin"></div>
<script>
$(function() {
	if(<?php echo $model->installation_locally;?> == 0 ){	$('#InstallationRequests_installation_location').parents('.cc').addClass('hidden');	}	}); 
function environment_change(element){
$this = $(element);
if($this.val() == 0){	$('#InstallationRequests_installation_location').parents('.cc').addClass('hidden');
	}else{	$('#InstallationRequests_installation_location').parents('.cc').removeClass('hidden');	} }

function changeSource(obj){
    const user = '<?=$model->customer?>';
    if(parseInt($(obj).val()) === 0){
        getCustomerProgOrContr(user,'project');
        $("#project_block .input_text_desc").text('Projects')
    }else{
        getCustomerProgOrContr(user,'maintenance');
        $("#project_block .input_text_desc").text('Support plans')
    }
}

function getCustomerProgOrContr(val,type) {
    $.ajax({ type: "GET",url: '<?php echo Yii::app()->createAbsoluteUrl('projects/GetParentProjectsMaintByClient');?>',data: { id: val,type:type},dataType: "json",
        success: function(data) {
            if (data) {
                let html = '';
                data.forEach(function (item) {
                    html += `<option value="${item.id}">${item.name}</option>`
                });
                $('#InstallationRequests_project').html(html);
            }
        }
    });
}
</script>