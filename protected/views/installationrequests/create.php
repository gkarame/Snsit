<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'irs-header-form','enableAjaxValidation'=>false,)); ?>
<table><tr><td>
	<div class="row customerRow">
	<div>
		<?php echo $form->labelEx($model,'customer'); ?>
		<div class="inputBg_create">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name','source'=>Customers::getAllAutocomplete(),
				'options'=>array('minLength'=>'0','showAnim'=>'fold',
					'select'=>"js:function(event, ui) { $('#InstallationRequests_customer').val(ui.item.id);
                    				getCustomerProjects('#InstallationRequests_customer'); }",
					'change'=>"js:function(event, ui) { if (!ui.item) { $('#InstallationRequests_customer').val(''); }
									getCustomerProjects('#InstallationRequests_customer'); }", ),
				'htmlOptions'=>array( 'onfocus' => "javascript:$(this).autocomplete('search','');",
					'onblur' => 'blurAutocomplete(event, this, "#InstallationRequests_customer");' ), )); ?>		 
		</div>
	</div>
	<?php echo $form->hiddenField($model, 'customer'); ?>
	<?php echo $form->error($model,'customer'); ?>
	</div>
</td>
<td><div class="row StartDateRow ">
		<div>
		<?php echo $form->labelEx($model,'expected_starting_date'); ?>
			<div class="inputBg_create ">
			<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'expected_starting_date','cssFile' => false,
			        'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width111'), )); ?>
			</div>
			<?php echo $form->error($model,'expected_starting_date'); ?>
		</div>
	</div>
</td>
<td><div class="row DeadlineRow ">
		<div>
		<?php echo $form->labelEx($model,'deadline_date'); ?>
			<div class="inputBg_create ">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'deadline_date','cssFile' => false,
			        'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width111'),));?>
			</div>
			<?php echo $form->error($model,'deadline_date'); ?>
		</div>
	</div>
</td>
<tr><td><div class="row rowDisasterRecovery margint10"><div>
		<?php echo $form->labelEx($model, 'disaster_recovery'); ?>
		<div class="">
			<?php echo $form->radioButtonList($model, 'disaster_recovery',InstallationRequests::getDisasterList(),array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
		</div>
		<?php echo $form->error($model,'disaster_recovery'); ?>
		</div>
</div></td>
<td><div class="row rowlocally margint10">
		<div>
		<?php echo $form->labelEx($model, 'installation_locally'); ?>
		<div class=" " >
			<?php echo $form->radioButtonList($model,'installation_locally',InstallationRequests::installation_locally() , array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'),'onchange' => 'changePlacement(this);')); ?> 
		</div>
		<?php echo $form->error($model,'installation_locally'); ?>
		</div>
</div></td>
    <!--
     /*
      * Author: Mike
      * Date: 12.07.19
      * Under Environment add a 3rd Radio Button: Hosted  -  When Clicked it shows in addition to Customer Contact Name and Email, Hosting Contact Name & Email   On different note, add inside the IR, under Authentication drop don list: Hybrid
      */
    -->
    <tr><td>
            <div class="row contactname" >
                <div>
                    <?php echo $form->labelEx($model,'hosting_contact_name'.'*'); ?>
                    <div class="inputBg_create">
                        <?php echo $form->textField($model, 'hosting_contact_name',array('autocomplete'=>'off')); ?>
                    </div>
                    <?php echo $form->error($model,'hosting_contact_name'); ?>
                </div>
            </div>
        </td>
        <td>
            <div class="row contactname" style="margin-right:45px;">
                <div>
                    <?php echo $form->labelEx($model,'hosting_contact_email'.'*'); ?>
                    <div class="inputBg_create">
                        <?php echo $form->textField($model, 'hosting_contact_email',array('autocomplete'=>'off')); ?>
                    </div>
                    <?php echo $form->error($model,'hosting_contact_email'); ?>
                </div>
            </div>
        </td>

    </tr>
<tr><td><div class="row InstallLocationRow margint10">
<div>
	<?php echo $form->label($model, 'installation_location'); ?>
	<div class="">
	<?php echo $form->radioButtonList($model, 'installation_location', InstallationRequests::getInstallLocationList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'),'onchange' => 'changePlacement2(this);')); ?>
	</div>
	<?php echo $form->error($model,'installation_location'); ?>
</div></div></td>
<td><div class="row contactname" >
	<div>
		<?php echo $form->labelEx($model,'customer_contact_name'.'*'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'customer_contact_name',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'customer_contact_name'); ?>
	</div>
</div></td>
<td><div class="row contactname" style="margin-right:45px;">
	<div>
		<?php echo $form->labelEx($model,'customer_contact_email'.'*'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'customer_contact_email',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'customer_contact_email'); ?>
	</div>
</div></td></tr></table>
<div class="row rowprerequisitesRecovery margint10" style="margin-right:127px;">
		<div>
		<?php echo $form->labelEx($model, 'prerequisites'); ?>
		<div class="">
			<?php echo $form->radioButtonList($model, 'prerequisites',InstallationRequests::getPrerequisitesList(),array('value' => InstallationRequests::prerequisite_NO,'separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
		</div>
		<?php echo $form->error($model,'prerequisites'); ?>
		</div>
</div>
<div class="row NotesRow " style="margin-right:40px;">
<div> <?php echo $form->labelEx($model,'notes'); ?>
	<div class="inputBg_create">
		<?php echo $form->textArea($model, 'notes',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
	</div>
	<?php echo $form->error($model,'notes'); ?>
</div> </div>

    <div class="row InstallLocationRow margint10 " style="margin-right:40px;">
        <div> <?= CHtml::label('Source','source_type') ?>
            <div class="">
                <?=CHtml::radioButtonList('source_type','',['Project','Support Plan'],array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'),'onchange' => 'changeSource(this)'))?>
            </div>
            <?php echo $form->error($model,'project'); ?>
         </div>
    </div>

    <div id="project_block"></div>

<br clear="all" /> <br clear="all" />
	<?php if($error1){?>
		<div> <span><b><font color="red">Note: Please Create a connection for this customer to proceed with IR creation</font></b></span> </div>		
		<?php } if($error2){?>
	<br clear="all" />
		<div> <span><b><font color="red">Note: Please Create a maintenance contract for this customer to proceed with IR creation</font></b></span> </div>		
		<?php }?>
	<div class="horizontalLine"></div>
	<div class="row buttons">		<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>	</div>
	<br clear="all" />
<?php $this->endWidget(); ?>
</div><br clear="all" />
<script>
	$(document).ready(function() {
	if($('#InstallationRequests_installation_locally_1').attr('checked') != 'checked' || $('#InstallationRequests_installation_location').val() ==null){
			$('#InstallationRequests_installation_location').parents('.row').addClass('hidden'); $('#InstallationRequests_prerequisites').parents('.row').addClass('hidden');
			$('#InstallationRequests_customer_contact_name').parents('.row').addClass('hidden'); $('#InstallationRequests_customer_contact_email').parents('.row').addClass('hidden');
            $('#InstallationRequests_hosting_contact_name').parents('.row').addClass('hidden');
            $('#InstallationRequests_hosting_contact_email').parents('.row').addClass('hidden');
		}
	});
    /*
 * Author: Mike
 * Date: 12.07.19
 * Under Environment add a 3rd Radio Button: Hosted  -  When Clicked it shows in addition to Customer Contact Name and Email, Hosting Contact Name & Email   On different note, add inside the IR, under Authentication drop don list: Hybrid
 */
	function changePlacement(element){
		$this = $(element);
		if($this.val() == 1) {
            $('#InstallationRequests_installation_location').parents('.row').removeClass('hidden');
            $('#InstallationRequests_prerequisites').parents('.row').removeClass('hidden');
            $('#InstallationRequests_customer_contact_name').parents('.row').removeClass('hidden');
            $('#InstallationRequests_customer_contact_email').parents('.row').removeClass('hidden');
            $('#InstallationRequests_hosting_contact_name').parents('.row').addClass('hidden');
            $('#InstallationRequests_hosting_contact_email').parents('.row').addClass('hidden');
        }
		else if($this.val() == 2){
                $('#InstallationRequests_installation_location').parents('.row').removeClass('hidden');
                $('#InstallationRequests_prerequisites').parents('.row').removeClass('hidden');
                $('#InstallationRequests_customer_contact_name').parents('.row').removeClass('hidden');
                $('#InstallationRequests_customer_contact_email').parents('.row').removeClass('hidden');
                $('#InstallationRequests_hosting_contact_name').parents('.row').removeClass('hidden');
                $('#InstallationRequests_hosting_contact_email').parents('.row').removeClass('hidden');
            }
		else{
		$('#InstallationRequests_installation_location').parents('.row').addClass('hidden');
		$('#InstallationRequests_prerequisites').parents('.row').addClass('hidden');
		$('#InstallationRequests_customer_contact_name').parents('.row').addClass('hidden');
		$('#InstallationRequests_customer_contact_email').parents('.row').addClass('hidden');
            $('#InstallationRequests_hosting_contact_name').parents('.row').addClass('hidden');
            $('#InstallationRequests_hosting_contact_email').parents('.row').addClass('hidden');
		}
	}
	function changePlacement2(element){
		$this = $(element);
		if($this.val() == 1){
		$('#InstallationRequests_customer_contact_name').parents('.row').removeClass('hidden');
		$('#InstallationRequests_customer_contact_email').parents('.row').removeClass('hidden');
		}else{
		$('#InstallationRequests_customer_contact_name').parents('.row').addClass('hidden');
		$('#InstallationRequests_customer_contact_email').parents('.row').addClass('hidden');
		}
	}
function getCustomerProjects(element) {
		$this = $(element);
		var val = $this.val();
        getCustomerProgOrContr(val,'project','Projects');
		$('#source_type_0').attr('checked','checked')
	}

	function getCustomerProgOrContr(val,type,title) {
        let html = `<div class="row projectRow">
                        <label for="InstallationRequests_project" class="required">${title} <span class="required">*</span></label>
                            <div class="selectBg_create">
                                <select name="InstallationRequests[project]" id="InstallationRequests_project">`;

        if (val) {
            $.ajax({ type: "GET",url: '<?php echo Yii::app()->createAbsoluteUrl('projects/GetParentProjectsMaintByClient');?>',data: { id: val,type:type},dataType: "json",
                success: function(data) {
                    if (data) {
                        data.forEach(function (item) {
                            html += `<option value="${item.id}">${item.name}</option>`
                        });

                        $('#project_block').html(html + `</select></div></div>`);
                    } } });
        } else { $('#project_block').html(html + `<option value=""></option></select></div></div>`);}
    }

    function changeSource(obj){
	    const user = $('#InstallationRequests_customer').val();
        if(parseInt($(obj).val()) === 0){
            getCustomerProgOrContr(user,'project','Projects');
        }else{
            getCustomerProgOrContr(user,'maintenance','Support plans');
        }
    }
</script>