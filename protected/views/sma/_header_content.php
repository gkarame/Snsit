<div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_no')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode(($model->id_no)); ?></div>
				<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('id_customer')); ?></div>
				<div class="general_col4s "><?php echo CHtml::encode(Customers::getNameById($model->id_customer)); ?></div>
			</div><div class="view_row"><div class="general_col1"><?php  echo CHtml::encode($model->getAttributeLabel('instance')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode($model->instance); ?></div>
				<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','Month/Year')); ?></div>
				<div class="general_col4s "><?php echo CHtml::encode(Sma::getMonthName($model->sma_month).' '.$model->sma_year); ?></div>
			</div>	<div class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
				<div class="general_col2 "><?php  echo CHtml::encode(Sma::getStatusLabel($model->status));?></div>
				<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('assigned_to')); ?></div>
				<div class="general_col4s "><?php echo CHtml::encode(Users::getNameById($model->assigned_to)); ?></div>
			</div><div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('instructions')); ?></div>
				<div class="general_col2 "><?php  echo CHtml::encode($model->instructions);?></div>
				<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
				<div class="general_col4s "><?php echo CHtml::encode(($model->notes)); ?></div>
			</div><div class="view_row"><div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','DBMS')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode(Maintenance::getDBMS($model->id_maintenance)); ?></div>
				<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations','NB# of Pending Actions')); ?></div>
				<div class="general_col4s "><?php echo CHtml::encode(SmaActions::getPendingPerCustomer($model->id_customer, $model->instance)); ?></div>
			</div><div class="horizontalLine smaller_margin"></div>