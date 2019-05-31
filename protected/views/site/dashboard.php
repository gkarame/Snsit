<?php	$this->widget('WidgetAddForm', array('id_dashboard' => $id));	$widgets =  Widgets::getWidgetsOn($id); ?>
<div class="sortableDash optionsDiv ui-sortable" data-dashboard="<?php echo $id;?>">
	<?php foreach ($widgets as $row) {	?>
	<div class="board inline-block ui-state-default <?php echo Dashboards::getBigSizeWidget($row['uid']); ?>" id="<?php echo $row['uid'];?>" data-id="<?php echo $row['uid'];?>">
		<div class="bhead">	<div class="title"><?php echo  $row['name'];?></div>
			<div class="drag" onclick="mareste(<?php echo $row['id']?>);" data-id = <?php echo $row['id']?>></div>
			<div class="dragg"></div><div class="close <?php echo ($row['name'] == "Oldest Invoices" || $row['name'] == "Project Summary" || $row['name'] == "ACTIVE PROJECT BUDGET RUNOUT" || $row['name'] == "Revenues Pipeline" || $row['name'] == "Project Financial Outlook")?"left874":""?>" onclick="deletewid(<?php echo $row['uid'];?>)"></div>
		</div>	<?php	$this->widget($row['model'], array('widget'=>$row)); ?>	<div class="ftr"></div></div>	<?php }	?></div> 