<?php //phpinfo();
if($mes != null)
	echo $mes;
else
foreach($expenses as $key=>$expens){?>
	<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
		<?php //echo Customers::getNameById($key);?>
		<?php $projects = $expens['projects'];//print_r($projects);?>
		<?php foreach($projects as $key_project=>$project){?>
		<?php $types = $project['type'];//print_r($types);
			$start_date = new DateTime('0000-00-00');
			$end_datee = strtotime('2050-10-10');
		?>
							<?php foreach ($types as $key_type =>$type) {?>
								<?php foreach ($type as $val){?>
								<?php if($start_date > $val['startDate'])
									$start_date = $val['startDate'];?>
								<?php if($end_datee < $val['endDate'])
									$end_datee = $val['endDate'];
							}	}
							?>
		<div class="table_rep">
			<table class="first">
				<tr>
					<td class="h3" style="padding-top:20px;padding-left:20px;border-top:1.5px solid #567885;font-family:arial;"><b>Customer:</b> <?php echo Customers::getNameById($key);?></td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;font-family:arial;"><b>From Date:</b> <?php echo date('d/m/Y',strtotime($start_date)); ?></td>
				</tr>
				<tr>
					<td class="h3" style="padding-bottom:20px;padding-left:20px;border-bottom:1.5px solid #567885;font-family:arial;"><b>Project:</b> <?php echo Projects::getNameById($key_project);?></td>
					<td class="h3" style="padding-bottom:20px;border-bottom:1.5px solid #567885;font-family:arial;"><b>To Date:</b> <?php echo date('d/m/Y',strtotime($end_datee)); ?></td>
				</tr>
			</table>
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">Item</th>
					<th class="h2" style="text-align:left;width:37%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">Expense Type</th>
					<th class="h2" style="text-align:left;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">Amount USD (Billable)</th>
					<th class="h2" style="text-align:left;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;;padding-left:20px;font-family:arial;">Amount USD (Not Billable)</th>
				</tr>
				<?php 
					$index = 0;
					$total_bill = 0;
					$total_not_bill = 0;
					$total_amount = 0;
					$chart = array();
				?>
							<?php foreach ($types as $key_type =>$type) {
								$bill = 0;
					$not_bill = 0;
					$total_amount_type = 0;
					foreach ($type as $val){
						if($val['billable'] == "Yes")
							$bill += $val['amount'];
						else 
							$not_bill += $val['amount']; 
							
						$total_amount += $val['amount'];
						$total_amount_type += $val['amount'];
					}
					$chart[$key_type]=$total_amount_type;
				?>
					<tr>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px;font-family:arial;"><?php echo ++$index;?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px;font-family:arial;"><?php echo Codelkups::getCodelkup($key_type);?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px;font-family:arial;"><?php echo Utils::formatNumber($bill);?></td>
						<td class="h3" style="text-align:left;padding-left:20px;font-family:arial;"><?php echo Utils::formatNumber($not_bill);?></td>
					</tr>
					<?php 
						$total_bill += $bill;
						$total_not_bill += $not_bill;
					?>
				<?php }?>
				<tr>
					<td style="border-top:1px solid #567885;font-family:arial;"></td>
					<td class="h2" style="padding:10px 5px;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px;font-family:arial;">
						Total Amount: <?php echo Utils::formatNumber($total_amount);?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px;font-family:arial;">
						<?php echo Utils::formatNumber($total_bill);?>
					</td>
					<td class="h2" style="font-weight:normal;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;padding-left:20px;font-family:arial;">
						<?php echo Utils::formatNumber($total_not_bill);?>
					</td>
				</tr>
			</table>
			<div class="graph" id="graph-<?php echo $key_project?>" ></div>
			
			
		</div>
		 <script type="text/javascript">
			//Callback handler method which is invoked after the chart has saved image on server.
		 	function FC_Exported(objRtn){ console.log(objRtn);      
	            if (objRtn.statusCode=="1"){
	               alert("The chart was successfully saved on server. The file can be accessed from " + objRtn.fileName);
	            }else{
	               alert("The chart could not be saved on server. There was an error. Description : " + objRtn.statusMessage);
	            }
           	}
		 	function ExportMyChart(id) {console.log(id);
		         var chartObject = getChartFromId('myChartId2'+id);
		         if( chartObject.hasRendered() ) chartObject.exportChart(); 
		   }
		           	
         </script>
		<script type="text/javascript">$(document).ready(function() {
			
					      var myChart = new FusionCharts( "../../FusionCharts/Pie3D.swf", 
					                   "myChartId2<?php echo $key_project?>", "400", "300", "0" );
					      myChart.setJSONData( {
					    	    "chart":{
					    	    	"exportEnabled":'1',
					    	    	"exportHandler":'<?php echo Yii::app()->getBaseUrl(true)?>/FusionCharts/FCExporter.php',
					    	    	"exportAtClient":'0',
					    	    	"exportAction":'save',
					    	    	"callback":'ExportMyChart',
					    	        "caption":"<?php echo Customers::getNameById($key)."/".Projects::getNameById($key_project);?>"   
					    	     },   
					    	     "data":[
									<?php foreach ($chart as $type_exp=>$value){?>
										{"label":"<?php echo Codelkups::getCodelkup($type_exp);?>",
						    	        	"value":"<?php echo $value;?>" 
							    	    },
						    	        <?php }?>
					    	       ] 
					    	    }
					    	 );
					     
						     myChart.render("graph-"+<?php echo $key_project?>);
						}); 
		</script>
		
		<?php }?>
		<input type="button" onclick="ExportMyChart()" value="Quick Export" >
	</div>
<?php }?>
