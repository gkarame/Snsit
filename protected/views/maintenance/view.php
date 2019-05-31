<div class="maintenance-view mytabs hidden size12">
	<?php $tabs = array();
		if(GroupPermissions::checkPermissions('financial-maintenance','write'))		{
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('update', array('model'=>$model,'edit'=>false), true);
		}	
		$tabs[Yii::t('translations', 'Performance')] = $this->renderPartial('_performance', array('model'=>$model), true);
if($model->support_service=='501' || $model->support_service=='502'){
			$tabs[Yii::t('translations', 'Services')] = $this->renderPartial('_services', array('model'=>$model), true);
				}
	$this->widget('CCustomJuiTabs', array('tabs'=>$tabs,'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab',  ),
	    'headerTemplate'=> '<li><a href="{url}" >{title}</a></li>',	));	?></div>
  <script type="text/javascript">
  function updateaItem(element){
		var val = parseFloat($(element).val());
		$.ajax({type: "POST",url: '<?php echo Yii::app()->createAbsoluteUrl('maintenance/updatefieldsItem');?>',
			data: { 'val' : val},dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
				  		$('#MaintenanceItems_amount').val(data.amount);
				  	} } } });
	}
  function m1(val){
	var dataSource = val;
              $("#chartContainerMaintenance").dxChart({
            	    dataSource: dataSource,
            	    commonSeriesSettings: { argumentField: "label",type: "bar"},
                  series: [ { valueField: "issues", name: "Issues" }, { valueField: "hours", name: "Hours" }, ],
                  legend: { verticalAlignment: "bottom", horizontalAlignment: "center" },
                  tooltip: {
                      enabled: true,
                      customizeText: function () { return this.seriesName + " : " + this.valueText; }
                  } }); }
  function m1NotStandard(val){
	var dataSource = val;
              $("#chartContainerMaintenance").dxChart({
            	    dataSource: dataSource,
            	    commonSeriesSettings: { argumentField: "label",type: "bar"},
                  series: [ { valueField: "hours", name: "Hours" }, ],
                  legend: { verticalAlignment: "bottom", horizontalAlignment: "center" },
                  tooltip: {
                      enabled: true,
                      customizeText: function () { return this.seriesName + " : " + this.valueText; }
                  } }); }
function m2(val){console.log("supp");
	var dataSource = val;
	              $("#chartContainerLine").dxChart({
	            	  dataSource: dataSource,
	            	    commonSeriesSettings: { type: "spline",argumentField: "label" },
	            	    commonAxisSettings: {
	            	        grid: { visible: true } },
	            	    series: [  { valueField: "revenues", name: "Revenues" },{ valueField: "cost", name: "Cost" },{ valueField: "profit", name: "Profit" }, ],
              		  tooltip:{ enabled: true },
              		    legend: { verticalAlignment: "bottom",horizontalAlignment: "center" },
              		    commonPaneSettings: {
              		        border:{ visible: true, bottom: false }}});}
function Maintenance1(val){
	$.ajax({type: "POST",	data: {'val':val}, 	url: "<?php echo Yii::app()->createAbsoluteUrl('Maintenance/barChart',array('id'=>$model->id_maintenance));?>", 
	  	dataType: "json",
		success: function(data) {
		  	if (data) { var pieChartDataSource = data; 
		  				if( <?php echo $model->support_service; ?>== '503'  ){	m1(pieChartDataSource); }else{ m1NotStandard(pieChartDataSource); }
		  				} }});}
function Maintenance2(val){
	$.ajax({ type: "POST",data: {'val':val},url: "<?php echo Yii::app()->createAbsoluteUrl('Maintenance/lineChart',array('id'=>$model->id_maintenance));?>", 
	  	dataType: "json",
	  	success: function(data) { if (data) { var pieChartDataSource = data; m2(pieChartDataSource); }	 } });}
    </script> 