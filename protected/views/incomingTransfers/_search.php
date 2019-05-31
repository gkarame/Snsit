<div class="wide search" id="search-ir" style="overflow:inherit;">
	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',)); ?>

	<div class="row status  width260" >	<div class="inputBg_txt" >	
	<?php echo $form->label($model,'it_no', array('class'=>"width89")); ?>	<span class="spliter"></span>
	<?php echo $form->textField($model,'it_no',array('class'=>'width141')); ?>	
	</div>	</div>
	
		<div class="row customer marginl20 width260">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'Customer', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name',		
						'source'=>IncomingTransfers::getCustomersAutocomplete(),
						'options'=>array(
							'minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ $('#IncomingTransfers_customer').val(ui.item.id); }",
							'change'=>"js:function(event, ui) { if (!ui.item) { $('#IncomingTransfers_customer').val(''); } }", ),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'	  => "width141",), )); ?>
			</div>	
			<?php echo $form->hiddenField($model, 'id_customer'); ?>
		</div>
		

		<div class="row status marginl20  width260">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'partner', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<div class="select_container width141"><?php echo $form->dropDownList($model, 'partner',  Codelkups::getCodelkupsDropDown('partner'), array('prompt'=>'')); ?></div>
			</div>
		</div>

		<div class="row status margint10 width260">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'status', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<div class="select_container width141"><?php echo $form->dropDownList($model, 'status', IncomingTransfers::getStatusList(), array('prompt'=>'')); ?></div>
			</div>
		</div>
		<div class="row status marginl20 margint10 width260">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'offsetting', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<div class="select_container width141"><?php echo $form->dropDownList($model, 'offsetting',  IncomingTransfers::getOffsettingList(), array('prompt'=>'')); ?></div>
			</div>
		</div>
		<div class="row assigned_to marginl20 margint10 width260">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'id_user', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_user','source'=>IncomingTransfers::getCreatedUsersAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ 
								$('#IncomingTransfers_id_user').val(ui.item.id); }" ),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'	  => "width141",), )); ?>
			</div>
		</div>
		<div class="row status margint10 width260">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'currency', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<div class="select_container width141"><?php echo $form->dropDownList($model, 'currency',  Codelkups::getCodelkupsDropDown('currency'), array('prompt'=>'')); ?></div>
			</div>
		</div>

		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			<div class="wrapper_action" id="action_tabs_right">
			<div onclick="chooseActions();" class="action triggerAction" ><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel" style="    margin-top: -20px;">
			    	<div class="headli"></div>
					<div class="contentli" >
						<div class="cover">
						<?php if(GroupPermissions::checkPermissions('financial-incomingTransfers','write')){ ?>
						<!--	<div class="li noborder" ><a class="special_edit_header" href="<?php //echo Yii::app()->createAbsoluteUrl('incomingTransfers/create');?>"><?php //echo Yii::t('translations', 'NEW TR');?></a></div>-->
							
							 <div class="li noborder" onclick="getRecipients();">ADD INVOICES</div>
						<?php } ?>
							 <div class="li noborder" onclick="getExcel();">EXPORT TO EXCEL</div>
					</div>
					</div>
					<div class="ftrli"></div>
			    </div>
			    <div id="users-list" style="display:none;"></div>
		</div>	
		</div>
		<div class="horizontalLine search-margin"></div>
<?php $this->endWidget(); ?>
</div>