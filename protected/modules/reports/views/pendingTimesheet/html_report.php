<?php 

	 $results =Yii::app()->db->createCommand("SELECT substring(week_start,'1','10') as week_start, substring(week_end,'1','10') as week_end  FROM timesheets WHERE status='NEW' and id_user in (select id from users where active='1') and id_user not in ('11','19','23','40','27') group by week_start order by week_start desc ")->queryAll();
					foreach ($results as $result)
						{
						
							$res= Yii::app()->db->createCommand("select t.id_user , t.timesheet_cod ,upd.email from timesheets t , user_personal_details upd where t.id_user=upd.id_user and t.week_start='".$result['week_start']."' and t.status='NEW' and t.id_user in (select id from users where active='1') and t.id_user not in ('11','19','23','40','27')")->queryAll();
							
		
		
?> <table class="second">  <br> <br>
					<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-left:1px solid #567885;padding-left:20px;font-size:17px;">Pending Week</th>
					<th class="h2" style="text-align:left;font-family:arial;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px; font-size:17px;"> <?php echo $result['week_start'].'  -  '.$result['week_end'] ; ?></th> 
					</tr>
	<?php

							
						
		?> <?php ?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
			
			   
				<tr>
				
					<th class="h2" style="text-align:left;font-family:arial;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-left:1px solid #567885;padding-left:20px">Resource Name</th>
					<th class="h2" style="text-align:left;font-family:arial;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> Timesheet ID</th>
					
					
				</tr>
				<?php foreach ($res as $r) {
				?>
			
					<tr>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px" ><a style="color:#567885;" href="mailto:<?php echo $r['email'];?>?subject='Need to fill your timesheet'"><?php echo Users::getUsername($r['id_user']);  ?> </a></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php  echo $r['timesheet_cod']; ?></td>
					
					</tr>
				 <?php  } ?>
							
									

				<tr>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-right:1px solid #567885;border-bottom:1px solid #B20533;border-left:1px solid #567885;padding-left:20px">
					</td>
					<td class="h2" style="border-bottom:1px solid #B20533;font-family:arial;text-align:left;border-right:1px solid #567885;padding-left:20px">
					</td>
					
				</tr> 
				 <?php  } ?>
			</table>
		</div>
		
		
	</div>
	

