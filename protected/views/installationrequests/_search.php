<div class="wide search" id="search-ir" style="overflow:inherit;">
	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',)); ?>
		<div class="row customer width230">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'Customer', array('class'=>"width70")); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name',		
						'source'=>InstallationRequests::getCustomersAutocomplete(),
						'options'=>array(
							'minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ $('#InstallationRequests_customer').val(ui.item.id); }",
							'change'=>"js:function(event, ui) { if (!ui.item) { $('#InstallationRequests_customer').val(''); } }", ),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'	  => "width131",), )); ?>
			</div>	
			<?php echo $form->hiddenField($model, 'customer'); ?>
		</div>
		<div class="row assigned_to width260">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'assigned_to', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'assigned_to','source'=>InstallationRequests::getAssignedUsersAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ 
								$('#InstallationRequests_assigned_to').val(ui.item.id); }" ),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'	  => "width141",), )); ?>
			</div>
		</div>
		<div class="row requested_by width260">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'requested_by', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'requested_by',		
						'source'=>InstallationRequests::getRequestUsersAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold',
							'select'	=>"js: function(event, ui){ $('#InstallationRequests_requested_by').val(ui.item.id); }" ),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');", 'class'	  => "width141", ), )); ?>
			</div>
		</div>
		<div class="row status margint10 width230">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'status', array('class'=>"width70")); ?>
				<span class="spliter"></span>
				<div class="select_container width131"><?php echo $form->dropDownList($model, 'status', InstallationRequests::getStatusList(""), array('prompt'=>'')); ?></div>
			</div>
		</div>
		<div class="row project margint10 width260">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'project', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'project_name','source'=>InstallationRequests::getProjectsAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ 
								$('#InstallationRequests_project').val(ui.item.id);}",
							'change'=>"js:function(event, ui) { if (!ui.item) { $('#InstallationRequests_project').val(''); } }", ),
						'htmlOptions'	=>array( 'onfocus' 	=> "javascript:$(this).autocomplete('search','');", 'class'	  => "width141", ), )); ?>
			</div>
			<?php echo $form->hiddenField($model, 'project'); ?>
		</div>
		<div class="row product margint10 width260">
			<div class="inputBg_txt">				
				<?php echo CHtml::label(Yii::t('demo', 'Product'), 'Product', array('class'=>"width89")); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('name'=>'Product',	
						'source'=>InstallationrequestsProducts::getProductsAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ 
								$('#id_product').val(ui.item.id); }" ),
						'htmlOptions'	=>array( 'onfocus' 	=> "javascript:$(this).autocomplete('search','');", 'class'	  => "width141", ), )); ?>
			</div>
			<?php echo CHtml::hiddenField('id_product'); ?>
		</div>
		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			<div class="wrapper_action" id="action_tabs_right">
			<div onclick="chooseActions();" class="action triggerAction" ><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel">
			    	<div class="headli"></div>
					<div class="contentli" >
						<?php if(GroupPermissions::checkPermissions('ir-general-installationrequests')){ ?>
						<div class="cover">
							<div class="li noborder"><a href="create">NEW IR</a></div>
						</div> <?php } ?>
					<div class="cover">
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