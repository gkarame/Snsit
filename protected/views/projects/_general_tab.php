<div id="popupps" class="hidden"> <div class='titre'>Project Survey</div> <div class='closefq'> </div> <div class='surveyscontainer'></div> </div>
<?php if ($edit){?> <div id="popupProjClose"> 
			<div class='titre red-bold'>Please enter the lessons learned :</div><div class='closereason'> </div>
			<div class='reasoncontainer'><textarea id="lessons_message" style="width:270px;height:120px;resize:none;" name="lessons_message"></textarea></div> 
			<div class='submitreason'><?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'Setlessons();return false;','id'=>'createbut')); ?>
	</div></div><div id="project_header" class="edit_header">
		<form id="projects-form" action='<?php echo Yii::app()->createAbsoluteUrl('projects/update', array('id'=> ($model->id ? $model->id : null)));?>' 
		method="post" enctype='multipart/form-data' class="ajax_submit" autocomplete="off">
			<fieldset id="projects_fields" class="create"><div class="formColumn" style="margin-top: -2px">
				<div class="row"><?php echo CHtml::activeLabelEx($model,'name'); ?>						
				<div class="inputBg_create"><?php echo CHtml::activeTextField($model,'name'); ?></div>						
			<?php echo CCustomHtml::error($model,'name', array('id'=>"Projects_name_em_")); ?></div>

			<div class="row"><?php echo CHtml::activeLabelEx($model,'status'); ?>						
			<div class="selectBg_create" ><?php echo CHtml::activeDropDownList($model, 'status', Projects::getStatusList(), array('prompt'=>Yii::t('translations', ''),'id'=>'status_dropdown')); ?>
						</div><?php echo CCustomHtml::error($model,'status'); ?></div>


<?php if($model->id_type=='27' && ($model->template==1 || $model->template==4 || $model->template==6 )){ ?> 
<div class="row">
						<?php echo CHtml::activeLabelEx($model,'product_edit'); ?>						
						<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'product', Codelkups::getCodelkupsDropDown('product'), array('prompt'=>Yii::t('translations', 'Choose Product'),'id'=>'product' )); ?>
						</div><?php echo CCustomHtml::error($model,'product', array('id'=>"Projects_product_em_")); ?>
			</div><?php }   ?>

			</div><div class="formColumn"><div class="row"><?php echo CHtml::activeLabelEx($model,'project_manager_edit'); ?><div class="selectBg_create">
					<?php echo CHtml::activeDropDownList($model, 'project_manager', Projects::getAllActiveUsers(),array('id'=>'PM' , 'onChange' => 'javascript:checkBMPM()' ), array('prompt'=>Yii::t('translations', ''))); ?>
				</div><?php echo CCustomHtml::error($model,'project_manager', array('id'=>"Projects_project_manager_em_")); ?>
			</div>

<?php if($model->id_type=='27'){ ?> 

				<div class="row">
					<?php echo CHtml::activeLabelEx($model,'Complex Modules to Implement *'); ?>						
						<div class="selectBg_create" ><?php echo CHtml::activeDropDownList($model, 'complexmodule', Projects::getcomplexModule(), array('prompt'=>Yii::t('translations', ''),'id'=>'complexmodule_dropdown','onchange' => 'changeCategory(this)')); ?>
						</div><?php echo CCustomHtml::error($model,'complexmodule', array('id'=>"Projects_complexmodule_em_")); ?></div>
<?php if($model->template==1 || $model->template==4 || $model->template==6 ){ ?> 
				<div class="row">
						<?php echo CHtml::activeLabelEx($model,'version_edit'); ?>						
						<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'version', Codelkups::getCodelkupsDropDown('soft_version'), array( 'prompt'=>Yii::t('translations', 'Choose Version'),'id'=>'version' )); ?>
						</div><?php echo CCustomHtml::error($model,'version', array('id'=>"Projects_version_em_")); ?>
			</div>
			<?php } ?>
<?php } ?>
			</div><div class="formColumn last"><div class="row">
						<?php echo CHtml::activeLabelEx($model,'business_manager_edit'); ?>						
						<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'business_manager', Projects::getAllActiveUsers(), array( 'id'=>'BM' , 'onChange' => 'javascript:checkBMPM()' ), array('prompt'=>Yii::t('translations', ''))); ?>
						</div><?php echo CCustomHtml::error($model,'business_manager', array('id'=>"Projects_business_manager_em_")); ?>
			</div>

<?php if($model->complexmodule=='Yes'){	?><div  id="complexnotesdiv" ><div class="row formColumn  ">
				<?php echo CHtml::activeLabelEx($model,'complexnotes'); ?>						
				<div class="selectBg_create" style="height:60px !important;" ><?php echo CHtml::activeDropDownList($model, 'complexnotes', Projects::getcomplexnotes(), array('id'=>'complexnotes','multiple' => 'multiple', 'style'=>'height:60px !important')); ?>
				</div><?php echo CCustomHtml::error($model,'complexnotes'); ?></div></div>
		<?php } else {?><div  id="complexnotesdiv" class="hidden"><div class="row formColumn  "><?php echo CHtml::activeLabelEx($model,'complexnotes'); ?>
				<div class="selectBg_create" style="height:60px !important;" ><?php echo CHtml::activeDropDownList($model, 'complexnotes', Projects::getcomplexnotes(), array('id'=>'complexnotes','multiple' => 'multiple', 'style'=>'height:60px !important')); ?>
				</div><?php echo CCustomHtml::error($model,'complexnotes'); ?></div></div>
		
		<?php }?>

			</div>
				
		
		<?php if( ($model->id_type=='27' || $model->id_type=='26')  && $model->status=="2" ) { ?> 			
			<div class="formColumn"><div class="row"><?php echo CHtml::activeLabelEx($model,'surveystatus'); ?>						
			<div class="selectBg_create" >	<?php echo CHtml::activeDropDownList($model, 'surveystatus', Projects::getSurveyStatusList(), array('prompt'=>Yii::t('translations', $model->surveystatus),'id'=>'status_survey_dropdown')); ?>
			</div><?php echo CCustomHtml::error($model,'status'); ?></div></div><?php } ?>
			
			<div class="formColumn"><div class="row"><?php echo CHtml::activeLabelEx($model,'deactivate_alerts');  ?><div class="inline-block bigger_amt general_col2"  >
			<div class="o_clasa" onclick="CheckOrUncheckInputAlert(this)" style="display:block;with:25px;height:25px;position:relative">
			<div class="repeat_inp input <?php echo ($model->deactivate_alerts == "Yes")?"checked":""?>" style="margin-left:50px;margin-top:-2px;" id="checkboxdiv">
			<?php echo CHtml::CheckBox('deactivate_alerts',($model->deactivate_alerts == "Yes")?true:false , array (
			        'value'=>'Yes','style'=>'width:10px;margin-left: 17px;margin-top: 8px;display:none' ,'class'=>'status_alerts','id'=>'alerts_button')); ?>
			</div></div></div></div><?php echo CCustomHtml::error($model,'deactivate_alerts', array('id'=>"Projects_deactivate_alerts_em_")); ?></div>
			<?php if($model->id_type=='27') {	?><div class="formColumn"><div class="row"><?php echo CHtml::activeLabelEx($model,'under_support'); ?>
						<div class="inline-block bigger_amt general_col2"  >
							<div class="o_clasa" onclick="CheckOrUncheckInputUS(this)" style="display:block;with:25px;height:25px;position:relative">
							<div class="repeat_inp input <?php echo ($model->under_support == "Yes")?"checked":""?>" style="margin-left:50px;margin-top:-2px;" id="checkboxdiv">
									 <?php echo CHtml::CheckBox('under_support',($model->under_support == "Yes")?true:false , array (
			                                        'value'=>'Yes','style'=>'width:10px;margin-left: 17px;margin-top: 8px;display:none','class'=>'under_support','id'=>'under_support')); ?>
</div></div></div></div></div><?php } ?></fieldset>	</form><div class="horizontalLine smaller_margin"></div></div><?php } else {?>
<div class="wrapper_action" id="action_tabs_right">	<div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
	<div class="action_list actionPanel"><div class="headli"></div>	<div class="contentli">
			<?php  $surv_type=''; if(GroupPermissions::checkPermissions('general-projects','write')){ ?>
				<div class="cover"><div class="li noborder" onclick="editProject(this);">Edit Project</div></div>
				<div class="cover"><div class="li noborder" onclick="createStatusReport();">Create Status Report</div></div>				
				<?php $checkprojectstatus = Projects::getProjectStatus($model->id);
			if($checkprojectstatus=="2"){	$surv_type='close';	}
 		 if( ($model->id_type=='27' || $model->id_type=='26') && Projects::getProjectStatus($model->id) &&  Projects::checkSurveyStatus($model->id , $surv_type)<1 && ((Projects::getBudgetedMD($model->id)>150 && $surv_type=='intermediate') ||  $surv_type=='close' ) ) { ?> 
			<div class="cover">	<div class="li noborder delete" onclick="sendSurvey(this);return false;" >Send Survey</div>	</div>	<?php } }	?>	</div>
		<div class="ftrli"></div></div>
	<?php if( ($model->id_type=='27' || $model->id_type=='26') && Projects::getProjectStatus($model->id) &&  Projects::checkSurveyStatus($model->id , $surv_type)<1 && ((Projects::getBudgetedMD($model->id)>150 && $surv_type=='intermediate') ||  $surv_type=='close' ) ) { ?> 
	<div id="offset-reas" style="display:none;"></div>	<?php } ?></div>
	<div id="project_header" class="edit_header"><div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('name')); ?></div>
			<div class="general_col2 "><?php echo CHtml::encode(($model->name)); ?></div>
			<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('customer_id')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode($model->customer->name); ?></div>
		</div>	<div class="view_row">
			<div class="general_col1"><?php echo Yii::t('translations','Primary Contact Name'); ?></div>
			<div class="general_col2 "><?php echo CHtml::encode($model->customer->primary_contact_name); ?></div>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Primary Phone Number')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode($model->customer->main_phone); ?></div>
		</div>	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('type')); ?></div>
			<div class="general_col2 "><?php echo CHtml::encode(($model->type->codelkup)); ?></div>

			<?php if(((($model->id_type) ) == '28')){?>
				<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('id_parent')); ?></div>
				<div class="general_col4 "><?php echo Projects::getParentProject($model->id_parent); ?></div>
			<?php }else{?>
			<?php if(((($model->id_type) ) == '27')){?>

			<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('template')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode(Eas::getTemplateLabel($model->template)); ?></div>

			<?php }else{?>	
				<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('is_billable')); ?></div>
				<div class="general_col4 "><?php echo (Projects::getExpensesType($model->customer_id, $model->id) == 'Actuals') ? 'Yes' : 'No';?></div>	
			<?php } }?>	
			</div>		
		<div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Project Manager')); ?></div>
			<div class="general_col2 "><?php echo isset($model->projectManager) ? ucwords($model->projectManager->fullname) : ' '; ?></div>
			<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('business_manager')); ?></div>
			<div class="general_col4 "><?php echo isset($model->businessManager) ? ucwords($model->businessManager->fullname) : ' '; ?></div>
		</div><div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Current Milestone')); ?></div>
			<div class="general_col2 "><?php echo CHtml::encode($model->getCurrentMilestone($model->id)); ?></div>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Alerts')); ?></div>
			<div class="general_col4 "><?php if($model->status!=2){echo CHtml::encode((ProjectsAlerts::getAlertsCount($model->id)));}else{echo "0";} ?></div>
		</div><?php if(((($model->id_type) ) == '28')) { ?>	<div class="view_row">
			<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('is_billable')); ?></div>
			<div class="general_col2 "><?php echo (Projects::getExpensesType($model->customer_id, $model->id) == 'Actuals') ? 'Yes' : 'No'; ?></div>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Country')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->customer->country)); ?></div>
		</div>	<?php } else {?><div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Country')); ?></div>
			<div class="general_col2 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->customer->country)); ?></div>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Status')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode((Projects::getStatusLabel($model->status))); ?></div>
		</div>	<?php } if($model->id_type== '27' && $model->under_support=='Yes' ) { ?>
		<div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Under Support')); ?></div>
			<div class="general_col2 "><?php echo CHtml::encode($model->under_support); ?></div>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Transition Date')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode($model->transition_to_support_date); ?></div></div> <?php } ?>
		<div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','deactivate alerts')); ?></div>
			<div class="general_col2 "><?php  echo CHtml::encode($model->deactivate_alerts); ?></div>
			<?php if($model->id_type== '28') { ?>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Status')); ?></div>
			<div class="general_col4 "><?php echo CHtml::encode((Projects::getStatusLabel($model->status))); ?></div>
			<?php } else{ ?>
			<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','t&m')); ?></div>
            <!--
                /*
                 * Author: Mike
                 * Date: 19.06.19
                 * MDs display it on status report
                 *
                 * Date: 25.06.19
                 * Hide survey fields on the projects
                 */
            -->
            <div class="general_col4 "><?php echo Projects::checktandm($model->id); ?></div><?php }  ?></div>
		<?php  if($model->id_type== '28' ) { ?>	<div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','t&m')); ?></div>
			<div class="general_col2 "><?php echo Projects::checktandm($model->id); ?></div>
	<?php }?>

		<?php if($model->id_type=='27' && ($model->template==1 || $model->template==4 || $model->template==6 )){ ?> 
<div class="view_row">
			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Product')); ?></div>
			<div class="general_col2 "><?php echo Codelkups::getCodelkup($model->product); ?></div>
			<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Version')); ?></div>
			<div class="general_col4 "><?php echo  Codelkups::getCodelkup($model->version); ?></div>
</div>
			<?php }?>
		<?php  if($model->id_type== '27' ) { ?>

		<div class="view_row">

			<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Complex Modules to Implement')); ?></div>
			<?php  if($model->complexmodule == 'Yes' ) { ?>
				<div class="general_col2 "><?php echo CHtml::encode($model->complexnotes ); ?></div>
			<?php } else   {?>
				<div class="general_col2 "><?php echo CHtml::encode($model->complexmodule); ?></div>
			<?php } ?>
		</div>	<?php }?>
       <div class="view_row">
            <div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','mds')); ?></div>
            <div class="general_col4 "><?php echo Projects::getMDS($model->id); ?></div>
       </div>

		<div class="horizontalLine smaller_margin"></div></div><?php }?><div id="budget_record"  class="grid border-grid">
<?php $provider = $model->getEasProvider(); $eas = $provider->getData();
$this->widget('zii.widgets.grid.CGridView', array('id'=>'budget-record-grid','dataProvider'=>$provider,'summaryText' => '','pager'=> Utils::getPagerArray(),
	'template'=>'{items}{pager}',
	'columns'=>array(
		array('header'=>Yii::t('translations', 'EA #'),'value'=>'$data->renderEANumber()','name' => 'ea_number','htmlOptions' => array('class' => 'column50'),
			'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'Net Amount','value' => 'Utils::formatNumber($data->getNetAmount())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'currency','value' => 'Codelkups::getCodelkup($data->currency)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('header' => 'TOTAL MDs','value' => 'Utils::formatNumber($data->getTotalManDays())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65', 'style'=>'text-transform:none !important;'),),
		array('header' => 'ACTUAL MDs','value' => 'Utils::formatNumber($data->getActualMD())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65', 'style'=>'text-transform:none !important;'),),
		array('header' => 'REMAINING MDs','value' => 'Utils::formatNumber($data->getTotalManDays() - $data->getActualMD())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65', 'style'=>'text-transform:none !important;'),),
		array('name' => 'Budgeted Rate','value' => 'Utils::formatNumber($data->getNetManDayRate())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'Actual Rate','value' => 'Utils::formatNumber($data->getActualRate())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
	),)); ?><div class="total_amounts totalrow"><div class="column inline-block"><span class="title"><?php echo Yii::t('translations', 'TOTAL NET AMOUNT'); 	?></span>
			<br /><br /><span id="total_net_amount" class="value"><?php echo Utils::formatNumber(Projects::getProjectNetAmountWithoutExpenses($eas));?></span>
		</div><div class="column inline-block middleitem"><span class="title"><?php echo Yii::t('translations', 'TOTAL MDs');?></span>
			<br /><br /><span id="total_man_days" class="value"><?php echo Utils::formatNumber(Projects::getProjectTotalManDays($eas));?></span>
		</div><div class="column inline-block middleitem"><span class="title"><?php echo Yii::t('translations', 'TOTAL ACTUAL MDs');?></span>
			<br /><br /><span id="total_actual_man_day" class="value"><?php echo Utils::formatNumber(Projects::getProjectActualManDays($model->id,$eas)); ?></span>
		</div><div class="column inline-block middleitem "><span class="title"><?php echo Yii::t('translations', 'REMAINING MDs');?></span><br /><br />
			<span id="remaining_days" class="value"><?php echo Utils::formatNumber(Projects::getProjectRemainingManDays($eas)); ?></span>
		</div><div class="column inline-block middleitem nobackground"><span class="title"><?php echo Yii::t('translations', 'ACTUAL RATE');?></span>
			<br /><br /><span id="totactualrate" class="value"><?php echo Utils::formatNumber(Projects::getProjectActualRate($model->id,$eas)); ?></span>
		</div></div></div><div id="expenses_record"  class="grid border-grid">
<?php $provider2 = $model->getEasProvider2();
$this->widget('zii.widgets.grid.CGridView', array('id'=>'expenses-record-grid',	'dataProvider'=>$provider2,	'summaryText' => '','pager'=> Utils::getPagerArray(),
	'template'=>'{items}{pager}','columns'=>array(
		array('header'=>Yii::t('translations', 'EA #'),'value'=>'$data->renderEANumber()','name' => 'ea_number','htmlOptions' => array('class' => 'column50'),'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'Expenses Budget','value' => '($data->getFormatExpense() == "N/A")?0:$data->getFormatExpenseUSD()','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'Expenses Spent','value' => 'Utils::formatNumber(Projects::getExpensesAmount($data->id_project))',
        	'htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'Balance','value' => '($data->getFormatExpense() == "Actuals")?"Actuals":Utils::formatNumber((Eas::getAmountinUSD($data->currency,$data->expense)) - Projects::getExpensesAmount($data->id_project))',
        	'htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'currency','value' => 'Codelkups::getCodelkup(9)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),
        ),	),)); ?><div class="total_amounts totalrow">
		<div class="column inline-block"><span class="title"><?php echo Yii::t('translations', 'EXPENSES BUDGET')?></span><br /><br /><span id="expenses_budget" class="value"><?php echo is_numeric(Projects::getSumBudgetUSD($model->eas)) ? Utils::formatNumber(Projects::getSumBudgetUSD($model->eas)) : Projects::getSumBudgetUSD($model->eas);?></span>
		</div><div class="column inline-block middleitem"><span class="title"><?php echo Yii::t('translations', 'EXPENSES SPENT');?></span><br /><br />			<span id="expenses_spent" class="value"><?php echo Utils::formatNumber(Projects::getSumSpent($model->eas));?></span>
		</div><div class="column inline-block middleitem nobackground"><span class="title"><?php echo Yii::t('translations', 'BALANCE');?></span><br /><br /><span id="balance" class="value"><?php echo is_numeric(Projects::getSumBudgetUSD($model->eas)) ? Utils::formatNumber(Projects::getSumBudgetUSD($model->eas) - Projects::getSumSpent($model->eas)) : Projects::getSumBudgetUSD($model->eas); ?></span>
		</div></div></div>
<?php if ($edit) { ?><div class="row buttons saveDiv noimg">
	<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('onclick' => 'js:submitForm();return false;')); ?></div>
	<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div></div><?php } ?>
<script>
function checkBMPM(){ var PM = document.getElementById('PM').value; var BM = document.getElementById('BM').value;
if(BM==PM){
	var action_but = {
					"Ok": {
						click: function() 
						{
							$(this).dialog('close');
						},
						class: 'ok_button'	
					} 
			};
custom_alert('ERROR MESSAGE', 'Project Manager and Business Manager could not be the same', action_but);
 document.getElementById('projects-form').reset(); } }
function sendSurvey(element) {			
			$.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('projects/sendSurvey');?>",dataType: "json",data: {'id_project':<?php echo $model->id; ?>},
			  	success: function(data) { if (data) { $('#offset-reas').html(data.div); $('#offset-reas').show(); } }, });	} 
function CheckOrUncheckInputAlert(obj){
	var checkBoxDiv = $(obj).find('.input');	var input = $(obj).find('input[type="checkbox"]');
	if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')){
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	input.prop('checked', false);
		}else{	checkBoxDiv.addClass('checked');	input.prop('checked', true); } } }
function CheckOrUncheckInput(obj){
	var checkBoxDiv = $(obj).find('.input');	var input = $(obj).find('input[type="checkbox"]');
	if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')){
		if (checkBoxDiv.hasClass('checked')) { checkBoxDiv.removeClass('checked'); input.prop('checked', false);
		}else{	checkBoxDiv.addClass('checked'); input.prop('checked', true); }	} }
function editProject(obj){ window.location = '<?php echo $this->createUrl('projects/update', array('id' => $model->id, 'view'=> 1)); ?>'; }
function createStatusReport(obj){ window.location = '<?php echo $this->createUrl('projects/createStatusReport', array('id' => $model->id)); ?>'; }
function CheckOrUncheckInputUS(obj){
	var checkBoxDiv = $(obj).find('.input');	var input = $(obj).find('input[type="checkbox"]');
	if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')) {
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	document.getElementById("under_support").value="No";
		}else{
			var url="<?php echo Yii::app()->createAbsoluteUrl('projects/validateTransitionSupport');?>"; var customer_id="<?php echo $model->customer_id; ?>";
			var id_project ="<?php echo $model->id;?>" ;	
			$.ajax({ type: "POST", data: {selected:customer_id, id_project:id_project}, url: url, dataType: "json",
	  					success: function(data) {
		  					if (data) { 
		  							var action_but = {
											"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
											} 
									};
		  						if (data.valid == '1') {
										custom_alert('ERROR MESSAGE', 'Cannot transit the project to support without having at least 1 active maintenance contract', action_but);	
										checkBoxDiv.removeClass('checked');	input.prop('checked', false); }
								if (data.valid == '2') {							
										custom_alert('ERROR MESSAGE', 'Customer contacts does not have SD access', action_but);	
										checkBoxDiv.removeClass('checked'); input.prop('checked', false); }
								if (data.valid == '3') {							
										custom_alert('ERROR MESSAGE', 'CS representative is not defined for this customer', action_but);	
										checkBoxDiv.removeClass('checked'); input.prop('checked', false); }
								if (data.valid == '4') {							
										custom_alert('ERROR MESSAGE', 'Checklist items are not Completed', action_but);	
										checkBoxDiv.removeClass('checked'); input.prop('checked', false); }  } }	}); 
			checkBoxDiv.addClass('checked');	input.prop('checked', true);	document.getElementById("under_support").value="Yes";			
		}	} }
		function CheckOrUncheckInputQA(obj){
	var checkBoxDiv = $(obj).find('.input');	var input = $(obj).find('input[type="checkbox"]');
	if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')) {
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	document.getElementById("qa").value="No";
		}else{
			checkBoxDiv.addClass('checked');	input.prop('checked', true);	document.getElementById("qa").value="Yes";			
		}	} }
function Setlessons(){	var id_project ="<?php echo $model->id ;?>" ;	var msg2 = document.getElementById('lessons_message').value;
		$.ajax({ type: "POST",	data: {'lessons':msg2, 'project':id_project},url: "<?php echo Yii::app()->createAbsoluteUrl('projects/Updatelessons');?>", 
	  	dataType: "json",
	  	beforeSend: function() { $("#ajxLoader").fadeIn(); },
        complete: function() { $("#ajxLoader").fadeOut(); },
	  	success: function(data) {
		  	if (data) {	if (data.status == 'success' ) { $('#popupProjClose').hide(); } } } });}
$(".closereason").click(function() {	$('#popupProjClose').hide();	});
$(document).ready(function() {	$('#popupProjClose').hide(); });
var select= $("#status_dropdown option:selected").val();
$("#status_dropdown").change(function(){
   var selecteds = $("#status_dropdown option:selected").val();   var noerror=0;   if (selecteds=="2"){ 	$("#checkboxdiv").addClass("checked");
 	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateMilestones');?>";
 	var url2 = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateAlerts');?>"; 
 	var url3 = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateTimesheetGetNames');?>";
 	var url4 = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateOpenChecklist');?>";
 	var url5 = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateOpenIssues');?>";
	var id_project ="<?php echo $model->id ;?>" ;
	$.ajax({type: "POST",data: {selected:id_project},  	url: url, async : false, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.valid == '0') { noerror=1;
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with pending milestones.', action_but);				
				var ddl = document.getElementById('status_dropdown');	var opts = ddl.options.length;
				for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
								} } } } } });
	$.ajax({type: "POST",data: {selected:id_project},url: url2,async : false,dataType: "json",
	success: function(data) {
		  	if (data) { if(data.valid == '0'){ noerror=1;
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with pending User Controlled Alerts.', action_but);				
						var ddl = document.getElementById('status_dropdown'); var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
							} } } } } });	
	$.ajax({type: "POST",data: {selected:id_project}, 	url: url3,async : false, 	dataType: "json",	   
	  	success: function(data) {
		  	if (data) { if (data.valid == '0') { noerror=1;
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', data.message, action_but);	
				var ddl = document.getElementById('status_dropdown'); var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
						} } } } }	}); 
	$.ajax({type: "POST",data: {selected:id_project}, 	url: url4, async : false, 	dataType: "json",	   
	  	success: function(data) {
		  	if (data) { 		if (data.valid == '0') { noerror=1;
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with Open Checklist items.', action_but);	
				var ddl = document.getElementById('status_dropdown'); var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
						} } } } } }); 
	$.ajax({	type: "POST",data: {selected:id_project},  	url: url5, 	async : false, dataType: "json",	   
	  	success: function(data) {
		  	if (data) { if (data.valid == '0') { 	noerror=1;
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with Open Issues.', action_but);	
				var ddl = document.getElementById('status_dropdown'); var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
								} } } } }	}); }

if (selecteds=="2" && noerror == 0)	{	$('#popupProjClose').stop().show(); } });
function changeCategory(element) {		$this =  $(element);		
		switch ($this.val()) {
			case 'Yes':
				document.getElementById("complexnotesdiv").classList.remove("hidden");  
				break;
			case 'No':
				document.getElementById("complexnotesdiv").classList.add("hidden"); 
				break;	}	}

function sendSurveyEmail(element){ var toemail= document.getElementById('to').value; var name= document.getElementById('name').value; var fname= document.getElementById('fname').value;	

	if(toemail.search("<")!='-1'){ var tob = toemail.split('<')[1]; var to = tob.split('>')[0];	}else{ var to=toemail; }
	$.ajax({ type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/sendSurvey');?>",	dataType: "json",
			    data: {'id_project':<?php echo $model->id; ?> , 'send': <?php echo "1"; ?> , 'to':to , 'name':name , 'fname':fname },
			  	success: function(data) {
			  		if (data) {			  			
			  			if(data.status== 'success'){ $('#offset-reas').html(''); $('#offset-reas').hide();
			  			}else{
			  				var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					    	    }
							}
		  					custom_alert('ERROR MESSAGE', data.message, action_buttons); } } },});	}
</script>