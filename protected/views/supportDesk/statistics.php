<div class="dashboard mytabs"><div id="yw0" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
<div id="yw0_tab_0" aria-labelledby="ui-id-10" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false">
<?php	$this->widget('WidgetAddForm', array('id_dashboard' => '1'));	$widgets =  Widgets::getCustomerWidgetsOn(1); ?>
<div class="sortableDash optionsDiv ui-sortable" data-dashboard="1"><?php foreach ($widgets as $row) {	?>
	<div class="board inline-block ui-state-default <?php echo Dashboards::getBigSizeWidget($row['uid']); ?>" id="<?php echo $row['uid'];?>" data-id="<?php echo $row['uid'];?>">
		<div class="bhead">	<div class="title"><?php echo  $row['name'];?></div><div class="drag" onclick="mareste(<?php echo $row['id']?>);" data-id = <?php echo $row['id']?>></div>
			<div class="dragg"></div>	<div class="close <?php echo ($row['name'] == "Project Summary" || $row['name'] == "Revenues Pipeline" || $row['name'] == "Project Financial Outlook")?"left874":""?>" onclick="deletewid(<?php echo $row['uid'];?>)"></div>
		</div>	<?php	$this->widget($row['model'], array('widget'=>$row));		?>	<div class="ftr"></div>	</div>	<?php }	 ?></div> </div></div></div>