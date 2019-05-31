<div class="mytabs training_edit">
	<div id="ir_header" class="edit_header">
	<div class="header_title">	
	<span class="red_title"><?php echo Yii::t('translations', 'IR HEADER');?></span>
	<?php if(GroupPermissions::checkPermissions('ir-general-installationrequests')){?>
    <a class="tabs_extra extra_edit2" id="extra_edit_training" href="<?php echo Yii::app()->createAbsoluteUrl('installationrequests/update', array('id' => $model->id)).'"  title="'.Yii::t('translations', 'Edit IR')?>">Edit IR</a>
	<?php  }?>
    </div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>
		<br clear="all" />
	</div>
<div id="budget_record"  class="grid border-grid">
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid','dataProvider'=>InstallationrequestsProducts::getProductsProvider($model->id),
                'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
                'afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
        'columns' => array(
        array('name' => 'product_name','value' => '$data->eProduct->codelkup','type'=>'raw',
            'htmlOptions' => array('class' => 'column160','onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"),'headerHtmlOptions' => array('class' => 'column160'),),
        array('name' => 'software_version','value' => '$data->software_version','htmlOptions' => array('class' => 'column160'),'headerHtmlOptions' => array('class' => 'column160'),),
        array('name' => 'number_of_nodes','value' => '$data->number_of_nodes','htmlOptions' => array('class' => 'column150'),'headerHtmlOptions' => array('class' => 'column150'),),
        array('name' => 'db_type','value' => '$data->getDBTypeLabel($data->db_type)','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'db_collation','value' => '$data->getDBCollationLabel($data->db_collation)','htmlOptions' => array('class' => 'column120'),'headerHtmlOptions' => array('class' => 'column120'),),
        array('name' => 'number_of_schemas','value' => '$data->number_of_schemas','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'authentication','value' => '$data->getAuthenticationLabel($data->authentication)','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'reporting_type','value' => '$data->getReportingTypeLabel($data->reporting_type)','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
       ), )); ?>
</div>
<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery">
    <div class="attachments_pic exppage margint20" ></div>
    <?php $files = $model->getFiles($model->id);
        foreach ($files as $file){ $path_parts = pathinfo($file['path']); ?>
        <div class="box template-download fade" style="padding:9px;">
            <div class="title">
                <a href="<?php echo $this->createUrl('site/download', array('file' => $file['path']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
            </div>                      
            <div class="size">
                <span><?php echo Utils::getReadableFileSize(filesize($file['path']));?></span>
            </div>
        </div>    <?php } ?>  
</div> </div>
<br clear="all" />