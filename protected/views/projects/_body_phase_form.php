<div cid="projects_items_content"  class="grid border-grid generalTasksTickets bordertop0"  id="phase_<?php echo $model->id;?>" data-id="<?php echo $model->id;?>">
<div class="total_amounts totalrow thead"><?php $this->renderPartial('_head_phase_form', array('model' => $model));?></div>
<form class="task" method="post">
<?php	$buttons = array();	$tmp = ''; if(GroupPermissions::checkPermissions('projects-tasks','write')){
				$tmp = '{update} {delete}';
				$buttons = array(
			           	'update' => array('label' => Yii::t('translations', ''),'imageUrl' => "../../images/EditPhase.png" ,
							'url' => 'Yii::app()->createUrl("projects/manageTasks", array("id"=>$data->id))',
							'htmlOptions' => array('style' => 'margin-top:4px;'),'options' => array('onclick' => 'showItemForm2(this);return false;','class' => 'taskbutton'),),
						'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => "../../images/DeletePhase.png" ,
							'url' => 'Yii::app()->createUrl("projects/deleteTask", array("id"=>$data->id))','htmlOptions' => array('style' => 'item checkbox_grid_task'),
			                'options' => array('class' => 'delete',),),); }
			$this->widget('zii.widgets.grid.CGridView', array('id'=>$model->id.'-grid','dataProvider'=>Projects::getTasks($model->id),'summaryText' => '',
			'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','hideHeader'=>'true',
			'columns'=>array(
				array('class'=>'CCheckBoxColumn','id'=>'checktask','htmlOptions' => array('class' => 'item checkbox_grid_task'),'selectableRows'=>2,),
		        array('value'=>'ProjectsTasks::getTitleTask($data->type, $data->description)','htmlOptions' => array('class' => 'column1 item paddigl0'),),
		        array('name' => 'users','type'=>'raw','value' => 'ProjectsTasks::getUsersGrid(ProjectsTasks::getAllUsersTask($data->id))','htmlOptions' => array('class' => 'profil'),),				
		        array('name' => 'complexity','value'=>'ProjectsTasks::getComplexityLabel($data->complexity)','htmlOptions' => array('class' => 'columnbillable'),),  
		        array('name' => 'billable','value'=>'$data->billable','htmlOptions' => array('class' => 'columnbillable'),),   
		        array('class'=>'CCustomButtonColumn','template'=>$tmp,'buttons'=>$buttons,),),)); 
if(GroupPermissions::checkPermissions('projects-tasks','write')){ ?><div class="tache new_item task_<?php echo $model->id;?>">
<div onclick="showItemForm2(this, true,<?php echo $model->id;?>);" class="newtask">	<u><b>+ <?php echo Yii::t('translations', 'NEW TASK');?></b></u></div></div><?php }?>	</form></div>