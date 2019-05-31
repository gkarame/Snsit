<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($CSD);
	$total_hours=0;
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885; ">Customer</th>
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">Industry</th>
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">Product</th>
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">Type of Items</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Brands</th>
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">Account Manager</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">CA</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">CS REP</th>
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">Support Plan</th>
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">WMS Version</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">ERP</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">WMS DB Type</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885; ">Strategic</th>
					
				</tr> 
				<?php foreach($CSD as $cs) { ?>
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885; "><?php echo $cs['name'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php echo Customers::getIndustry($cs['industry']); ?></td>
						

						<!-- Georgi on Jul 22 2016-->
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; ">
						
						<div class="inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
						<div class="first_it panel_container">
						<span class="clip"><?php echo substr(Codelkups::getCodelkupPerMultiple($cs['product']),0 ,5); ?></span><u class="red">+</u>
							 <div class="panelM" style = "left:80px">
								 <div style="  background-image: url('/images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
								<div style="  background-image: url('/images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
								 	<div class="coverM" style="background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Products: </u></b> ".Codelkups::getCodelkupPerMultiple($cs['product']); ?> </div>
								 </div>
								 <div  style="background-image: url('/images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
							 </div>
						 </div>
			 			</div>
						<?//php echo Codelkups::getCodelkup($cs['product']); ?>
						</td>
						<!-- Georgi on Jul 22 2016 End-->
						
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; ">
						<div class="inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
						<div class="first_it panel_container">
						<span class="clip"><?php echo substr($cs['product_type'],0 ,5); ?></span><u class="red">+</u>
							 <div class="panelM" style = "left:80px">
								 <div style="  background-image: url('/images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
								<div style="  background-image: url('/images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
								 	<div class="coverM" style="background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Types: </u></b> ".$cs['product_type']; ?> </div>
								 </div>
								 <div  style="background-image: url('/images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
							 </div>
						 </div>
			 			</div>
						<?//php  echo $cs['product_type']; ?>

						</td>
						<!-- Georgi on Jul 22 2016-->
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; ">
						<div class="inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
						<div class="first_it panel_container">
						<span class="clip"><?php echo substr($cs['brands'],0 ,5); ?></span><u class="red">+</u>
							 <div class="panelM" style = "left:80px">
								 <div style="  background-image: url('/images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
								<div style="  background-image: url('/images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
								 	<div class="coverM" style="background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Brands: </u></b> ".$cs['brands']; ?> </div>
								 </div>
								 <div  style="background-image: url('/images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
							 </div>
						 </div>
			 			</div>
						<?//php echo $cs['brands']; ?>
						</td>
						<!-- Georgi on Jul 22 2016 End-->
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php if($cs['account_manager']!= null){ echo Users::getNameById($cs['account_manager']); } ; ?> </td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php if($cs['ca']!= null){ echo Users::getNameById($cs['ca']); } ; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php if($cs['cs_representative']!= null){ echo Users::getNameById($cs['cs_representative']); } ; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php echo Maintenance::getMaint($cs['id']); ?></td>
						
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php echo  Codelkups::getCodelkupPerMultiple($cs['soft_version']); ?></td>					
						
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; ">
						<div class="inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
						<div class="first_it panel_container">
						<span class="clip"><?php echo substr($cs['erp'],0 ,5); ?></span><u class="red">+</u>
							 <div class="panelM" style = "left:80px">
								 <div style="  background-image: url('/images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
								<div style="  background-image: url('/images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
								 	<div class="coverM" style="background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u> </u></b> ".$cs['erp']; ?> </div>
								 </div>
								 <div  style="background-image: url('/images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
							 </div>
						 </div>
			 			</div>
						<?//php echo $cs['erp']; ?>
						</td>					
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php echo  Codelkups::getCodelkupPerMultiple($cs['wms_db_type']); ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885; "><?php echo $cs['strategic']; ?></td>
						</tr>
				 <?php  } ?>
				 <tr>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885; ">  <?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533; ;border-right:1px solid #567885;">
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					<td class="h2" style=" text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885; "><?php echo " " ; ?>
					</td>
					
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533; ;border-right:1px solid #567885;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533; ;border-right:1px solid #567885;">
					</td>
				</tr>
				
				
				
			</table>
		</div>
		
	
	</div>
		<?php }?>

		<script>
		function showdropdown()
	{
		document.getElementById('searchindustry').style.visibility="visible";
	}
		</script>

