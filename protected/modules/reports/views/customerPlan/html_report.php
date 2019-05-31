<?php //phpinfo();

if($message != null)
	echo $message;
else
foreach($CustomerPlan as $CustomerP){?>
	<div class="project_thead project" > 
		
		<?php   $customer = $CustomerP['id'];
				$title= $CustomerP['contract_description'];
				$support= Codelkups::getCodelkup($CustomerP['support_service']);
		?>
		<div class="table_rep">
			<table class="first">
				<tr>
					<td class="h3" style="padding-top:20px;padding-left:20px;border-top:1.5px solid #567885;font-family:arial;"><b>Customer:</b> <?php echo Customers::getNameById($customer);?></td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;font-family:arial;"><b>Contract Description:</b> <?php echo $title; ?></td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;font-family:arial;"><b>Support Service:</b> <?php echo $support; ?></td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;font-family:arial;"><b>Renewal Date:</b> <?php echo date("d/m/Y", strtotime(MaintenanceServices::getNextRenovationDatePerContract($CustomerP['id_maintenance']))); ?></td>
				</tr>
			</table>
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">#</th>
					<th class="h2" style="text-align:left;width:37%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">Service</th>
					<th class="h2" style="text-align:left;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">Quota</th>
					<th class="h2" style="text-align:left;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px;font-family:arial;">Actuals</th>
				</tr>
				<?php 
				$index = 0;
				$services= MaintenanceServices::getSupportServicesPerMaint($CustomerP['id_maintenance']);
				foreach($services as $service){
					if ($service['id_service'] == 7 || $service['id_service'] == 19) 
	    			{
	    				$quota= 'Unlimited';	
	    			}else
	    			{
	    				$quota= Utils::formatNumber($service['quota']);
	    			}
					?>
					<tr>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px;font-family:arial;"><?php echo ++$index;?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px;font-family:arial;"><?php echo MaintenanceServices::getNameById($service['id_service']);?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px;font-family:arial;"><?php echo $quota;?></td>
						<td class="h3" style="text-align:left;padding-left:20px;font-family:arial;"><?php echo Utils::formatNumber(MaintenanceServices::getActualExcel($CustomerP['id_maintenance'],$service['id_service'],$service['field_type']));?></td>
					</tr>
				<?php }?>
				
			</table>
			<div class="graph" ></div>
			
			
		</div>
			<?php }?>

</div>
				
