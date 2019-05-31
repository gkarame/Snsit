<div class="bcontenu">
 	<div class="stat_years">
 		<span class="status year_maintp 100" id="alltypes" onClick="changeYearMaintP(<?php echo "100";?>)"><i>Current Year /</i></span>
 		<span class="status year_maintp 1" data-id="1" onClick="changeYearMaintP(1)"><i>Last Year/ </i></span>
 		<span class="status year_maintp 2" data-id="2" onClick="changeYearMaintP(2)"><i>2 Years Ago</i></span>
 	
	    <span class="status filter_maintp 100" data-id="100" style="text-align:center;padding-left :4px;" onClick="changecolorMaint(100)"><i>All / </i></span>
	    <span class="status filter_maintp 80" data-id="80" style="text-align:center;"   onClick="changecolorMaint(80)"><i>Red/</i></span>        
	   <span class="status filter_maintp 40" data-id="40"  style="text-align:center;" onClick="changecolorMaint(40)"><i>Green/</i></span>
		<span class="status filter_maintp 60" data-id="60"  style="text-align:center;" onClick="changecolorMaint(60)"><i>Blue(<10,000)</i></span>

 	 </div>
 	<div class="style_chart700 graph" id="graph-maintprofit" ></div>
</div>
<?php $id = WidgetMaintenanceProfit::getId();?>
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>1006,
        'height'=>810,
        'resizable'=>false,
        'closeOnEscape' => true,
    ),
));
?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetMaintenanceProfit::getName(); ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 		<span class="status year_maintp 100" id="alltypes" onClick="changeYearMaintP(<?php echo "100";?>)"><i>Current Year /</i></span>
 		<span class="status year_maintp 1" data-id="1" onClick="changeYearMaintP(1)"><i>Last Year/ </i></span>
 		<span class="status year_maintp 2" data-id="2" onClick="changeYearMaintP(2)"><i>2 Years Ago</i></span>
 	
 		<span class="spliter status" style="margin-left:150px !important;"> Customers</span>
        <span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Customers::getAllAutocomplete(),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold', ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width161",
               'id'   => "custsearchprofit",
              'style'=> "margin-left:10px; ",), ));?>
        </span>
		<span class="spliter status" onclick="searchcustomerProfit()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>

	    <span class="status filter_maintp 100" data-id="100" style="text-align:center;padding-left :150px;" onClick="changecolorMaint(100)"><i>All / </i></span>
	    <span class="status filter_maintp 80" data-id="80" style="text-align:center;"   onClick="changecolorMaint(80)"><i>Red /</i></span>        
	    <span class="status filter_maintp 40" data-id="40"  style="text-align:center;" onClick="changecolorMaint(40)"><i>Green /</i></span>
		<span class="status filter_maintp 60" data-id="60"  style="text-align:center;" onClick="changecolorMaint(60)"><i>Blue (<10,000)</i></span>


 	 	</div>
 	<div class="graph style_chart1000" id="graph-maintprofit2" ></div>
</div>
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<script type="text/javascript">
$(document).ready(function() {

	var id_type = 100;
	var id_status=100;
	$('.year_maintp.'+'100').addClass("colorRed");
	$('.filter_maintp.'+'100').addClass("colorRed");

		if($('#graph-maintprofit2').is(':visible')){ //if the container is visible on the page
		 createGridMaintProfit();   
		
	}
	

});
function getProjectInfo(element){

                var url="<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectInfo'); ?>";
                  $.ajax({
                         type: "POST",
                         url: url,
                         dataType: "json",
                         data: {'id_project':element},
                            success: function(data) {
                               if (data) {
                                    if (data.status == 'success') {                                  
                                         return data.name;
                                     
                                  }
                             }
                               
                          }

                      });
                  
}
function createGridMaintProfit() {

	val = <?php echo WidgetMaintenanceProfit::CharChart();?>;
    ChartMaintP(val,"graph-maintprofit");
    ChartMaintP(val,"graph-maintprofit2");
   
 
};

function ChartMaintP(val,id){

	var dataSource = val;   

           
    $("#"+id).dxChart({

	dataSource: dataSource,
	commonSeriesSettings: {type: 'scatter'},
    customizePoint: function() {
                                     if(this.value > 0 && this.value>= 10000) { 
//alert(this.value);
                                                return { color: '#33CC33', hoverStyle: { color: '#33CC33' } };
                                    
                                             } else if(this.value > 0 && this.value< 10000) {

                                                 return { color: '#7fbffe', hoverStyle: { color: '#7fbffe' } }; 
                                             }else if(this.value < 0) {

                                                 return { color: '#FF6565', hoverStyle: { color: '#FF6565' } }; 
                                             }else if(this.value = 0) {

                                                 return { color: '#FF6565', hoverStyle: { color: '#FF6565' } }; 
                                             }
    },

    tooltip: {
               enabled: true,
               background:'grey' ,
               font: { color: 'black',                       
                        size:15,
                        

                    },
              
                customizeText: function (arg) { 
                   
                   return arg.point.tag;

                }
    },

    argumentAxis: {
                label: {
                            customizeText: function () {return this.value;}
                        },
                title: 'Revenues'
    },

    valueAxis: {
                label: {
                            customizeText: function () {return this.value ;}
                        },
                constantLines: [{
                                    label: {  text: '' },
                                    width: 2,       
                                    value: 0,
                                    color: '#E60000',
                                    dashStyle: 'dash'
                                }],
                title: 'Profit'
    },

    legend: {
       
                verticalAlignment: 'bottom',
                horizontalAlignment: 'center',
                visible: false
        
    },
     
    palette: ["#2E5CB8"],

    series: [{
                name: 'name',
                argumentField: 'total1',
                valueField: 'older1',
                tagField:'tag1'
            }]
});


}

function addCommas(n){
  var s = "",
      r;

  while (n) {
    r = n % 1000;
    s = r + s;
    n = (n - r)/1000;
    s = (n ? "," : "") + s;
  }

  return s;
}

 


function searchcustomerProfit(colorfilterMaint){
	custProfit=$('#custsearchprofit').val();
  $.ajax({
    type: "POST",
    data: {'custProfit':custProfit},          
      url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/MaintProfit');?>", 
      dataType: "json",
      success: function(data) {
        if (data) { 
                $('.filter_maintp').removeClass("colorRed");
          var pieChartDataSource = data;
          ChartMaintP(pieChartDataSource,"graph-maintprofit");
          ChartMaintP(pieChartDataSource,"graph-maintprofit2"); 
                $('.filter_maintp.100').addClass("colorRed");          
      }
     }
});}


function changecolorMaint(colorfilterMaint){
	custProfit=$('#custsearchprofit').val();
  $.ajax({
    type: "POST",
    data: {'colorfilterMaint':colorfilterMaint,'custProfit':custProfit},          
      url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/MaintProfit');?>", 
      dataType: "json",
      success: function(data) {
        if (data) {
          $('.filter_maintp').removeClass("colorRed");
          var pieChartDataSource = data;
          ChartMaintP(pieChartDataSource,"graph-maintprofit");
            ChartMaintP(pieChartDataSource,"graph-maintprofit2");
            $('.filter_maintp.'+colorfilterMaint).addClass("colorRed");
          
      }
     }
});}
 


function changeYearMaintP(year){
	custProfit=$('#custsearchprofit').val();
	$.ajax({
 		type: "POST",
 		data: {'yearprofit':year,'custProfit':custProfit},					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/MaintProfit');?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		$('.year_maintp').removeClass("colorRed");
                $('.filter_maintp').removeClass("colorRed");
		  		var pieChartDataSource = data;
		  		ChartMaintP(pieChartDataSource,"graph-maintprofit");
		  	    ChartMaintP(pieChartDataSource,"graph-maintprofit2");
  			  	$('.year_maintp.'+year).addClass("colorRed");
                $('.filter_maintp.100').addClass("colorRed");
  			 
			}
		 }
});}
</script>	