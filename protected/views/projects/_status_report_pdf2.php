<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <style>

        .fullwidth{
            width: 100% !important;
        }

        th{
            background-color: #B10634;
            font-color: white;
        }
        
        .inner-table{ 
        text-align:left;
        border-collapse:collapse;
        font-family: Calibri;
        font-size: 0.85em;
        color:#000000;
    }
    
    .inner-table td,th{
        border-style: solid;
         border-color: black;
        border-width: 0.3pt;
    }

    table {
        width:100%;
    }
    
  .rotate {
    /* FF3.5+ */
    -moz-transform: rotate(-90.0deg);
    /* Opera 10.5 */
    -o-transform: rotate(-90.0deg);
    /* Saf3.1+, Chrome */
    -webkit-transform: rotate(-90.0deg);
    /* IE6,IE7 */
    filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=0.083);
    /* IE8 */
    -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
    /* Standard */
    transform: rotate(-90.0deg);
  }

p {
    font-size: 70%;
   
}
    </style>
    </head>
    <body>
        <div class=" fullwidth">
        
        <br clear="all"><?php if (!empty($project_risks)){ ?>
            <div style="width:100%;">
			
            <table class = " inner-table" style="width:100%;">
                <tr>
                    <th colspan = "4" style="text-align:left;">
                        <font color=white>Open Risks</font>
                    </th>
                </tr>
                <tr>
                    <td style="width:35%;background-color:#CCCCBB;">
                        Description
                    </td>
                    <td style="width:10%;background-color:#CCCCBB;">
                        Rating
                    </td>
                    <td style="width:15%;background-color:#CCCCBB;">
                        Responsibility
                    </td>
                    <td style="width:40%;background-color:#CCCCBB;">
                        Planned Actions
                    </td>
                </tr>
            <?php foreach ($project_risks as  $risk_open) { ?>  
                <tr>
                    <td>
                        <?php echo $risk_open['risk'];?> 
                    </td>
                    <td style="<?php if($risk_open['priority'] == 3){
                        echo "background-color:green;";
                    }else if($risk_open['priority'] == 2){
                        echo "background-color:yellow;";
                    }else if($risk_open['priority'] == 1){
                        echo "background-color:red;";
                    }
                    ?>">
                        <?php echo Projects::getPriorityLabel($risk_open['priority']);?>
                    </td>
                    <td>
                        <?php 
                        	if($risk_open['responsibility'] == 'CLIENT')
                        	{
								echo Customers::getNameByID(Projects::getCustomerByProjectName($model->name));
                        	}
                        	else if($risk_open['responsibility'] == 'SNS/CLIENT'){
                        		echo 'SNS/'.Customers::getNameByID(Projects::getCustomerByProjectName($model->name));
                        	}else{
                        		echo $risk_open['responsibility'];
                        	}
                        ?>
                    </td>
                    <td>
                        <?php echo $risk_open['planned_actions'];?>
                    </td>
                </tr>
            <?php }?>
			
            </table>
		
            </div>    <?php }?>
           <br clear="all">
            <div style="width:100%;">
               <!--  <tr> --><?php if (!empty($project_risks_closed)){ ?>
                    <div style="width:50%;float:left;"> <!--  valign="top";  -->
					
					
                        <table class="inner-table " style="width:100%;">
                            <tr class="fullwidth">
                                <th colspan="3" style="text-align:left;">
                                    <font color=white>Closed Risks </font>
                                </th>
                            </tr>
                            <tr>
                                <td style="width:100%;background-color:#CCCCBB;">
                                    Description
                                </td>
                            </tr>
                            <?php foreach ($project_risks_closed as $risk_closed) { ?>
                            <tr>
                                <td>
                                    <?php echo $risk_closed['risk'];?>
                                </td>
                            </tr>
                            <?php }?>
                        </table>
						
						    
                    </div>
                     <?php } else  if (!empty($project_invoices)){ ?>
                <div style="width:50%;float:left;"  > <!-- valign="top"; -->
                
                    <table class="inner-table " style="width:100%;">
                        <tr>
                            <th colspan="5" style="text-align:left;">
                                <font color=white>Invoices Due </font>
                            </th>
                        </tr>
                        <tr>
                            <td style="width:25%;background-color:#CCCCBB;">
                                Invoice#
                            </td>
                            <td style="width:25%;background-color:#CCCCBB;">
                                Amount
                            </td>
                            <td style="width:10%;background-color:#CCCCBB;">
                                Currency
                            </td>
                           <td style="width:25%;background-color:#CCCCBB;">
                                Due Date
                            </td>
                             <td style="width:25%;background-color:#CCCCBB;">
                                Age
                            </td>
                        </tr>
                        <?php foreach ($project_invoices as $key => $invoice) { ?>
                        <tr>
                            <td>
                                <?php echo $invoice['final_invoice_number'];?>
                            </td>
                            <td>
                                <?php echo $invoice['gross_amount'];?>
                            </td>
                            <td>
                                <?php echo Codelkups::getCodelkup($invoice['default_currency']);?>
                            </td>
                            <td>
                                <?php echo date('d/m/Y',strtotime($invoice['due_date']));?>
                            </td>
                            <td>
                                <?php echo $invoice['age'];?>
                            </td>
                            
                        </tr>
                        <?php }?>
                    </table>
                </div>
                 <?php }?>

                     <?php if (!empty($project_milestones)){ ?>
                   
					<?php if( empty($project_invoices) && empty($project_risks_closed)){?>
                <div style="width:45%;float:left;" > <!-- valign="top"; -->
                <?php }else{?>
  <div style="width:46%;padding-left:2em;float:right;" > <!-- valign="top"; -->
                <?php } ?>
                        <table class="inner-table " style="width:100%;">
                            <tr>
                                <th style="text-align:left;">
                                    <font color=white>Milestones For The Next 6 Months</font>
                                </th>
                            </tr>
                            <tr>
                                <td style="background-color:#CCCCBB;">
                                    Description
                                </td>
                            </tr>
                            <?php foreach ($project_milestones as $milestone) { ?>
                            <tr>
                                <td>
                                    <?php echo $milestone['milestone'];?>
                                </td>
                            </tr>
                            <?php }?>
                        </table>
						
                    </div>  <?php } else {?>
                     <?php if(empty($project_milestones) && empty($project_invoices) && empty($project_risks_closed)){?>
               <div  style="width:45%;float:left;" > <!--valign="top";  width:40%; -->
                <?php }else{?>
  <div  style="width:46%;padding-left:2em;float:right;" > <!--valign="top";  width:40%; -->
                <?php } ?>
                   
                    <!-- if isset -->
                    <?php if (isset($uat_open_checklist)  && !empty($uat_open_checklist )) { ?>
                    <table class=" inner-table" style="width:100%;">
                        <tr>
                            <th colspan="2" style="text-align:left;">
                                <font color="white">Pending Checklist Item(s)</font>
                            </th>
                        </tr>

                        <tr>
                            <td style="background-color:#CCCCBB;"> <!-- width:70%; -->
                                Item
                            </td>
                            <td style="background-color:#CCCCBB;">
                                Category
                            </td>
                        </tr>

                        <?php foreach ($uat_open_checklist as $key => $uat_check) { ?>
                        <tr>
                                <td>
                                    <?php echo  $uat_check['descr'];?>
                                </td>
                                <td>
                                    <?php echo $uat_check['category'];?>
                                </td>
                            </tr>
                        <?php }?>
                    </table>
                    <?php } else if (isset($uat_golive_pending_checklist) && !empty($uat_golive_pending_checklist )) { ?>

                        <table class=" inner-table" style="width:100%;">
                            <tr>
                                <th colspan="2" style="text-align:left;">
                                    <font color="white">Pending Checklist Item(s)</font>
                                </th>
                            </tr>

                            <tr>
                                <td style="background-color:#CCCCBB;"> <!-- width:70%; -->
                                    Item
                                </td>
                                <td style="background-color:#CCCCBB;">
                                    Category
                                </td>
                            </tr>

                        <?php foreach ($uat_golive_pending_checklist as $key => $uat_golive_check) { ?>
                            <tr>
                                <td>
                                    <?php echo  $uat_golive_check['descr'];?>
                                </td>
                                <td>
                                    <?php echo $uat_golive_check['category'];?>
                                </td>
                            </tr>
                        <?php }?>
                        
                    </table>
                        
                   <?php }?>
                </div>
 <?php }?>



                <!-- </tr> -->
            </div>
            
          
            <br clear="all">

            <div style="width:100%;">
              <!--   <tr> --><?php if (!empty($project_invoices) && !empty($project_risks_closed)){ ?>
                <div style="width:50%;float:left;"  > <!-- valign="top"; -->
				
                    <table class="inner-table " style="width:100%;">
                        <tr>
                            <th colspan="5" style="text-align:left;">
                                <font color=white>Invoices Due </font>
                            </th>
                        </tr>
                        <tr>
                            <td style="width:25%;background-color:#CCCCBB;">
                                Invoice#
                            </td>
                            <td style="width:25%;background-color:#CCCCBB;">
                                Amount
                            </td>
                            <td style="width:10%;background-color:#CCCCBB;">
                                Currency
                            </td>
                           <td style="width:25%;background-color:#CCCCBB;">
                                Due Date
                            </td>
							 <td style="width:25%;background-color:#CCCCBB;">
                                Age
                            </td>
                        </tr>
                        <?php foreach ($project_invoices as $key => $invoice) { ?>
                        <tr>
                            <td>
                                <?php echo $invoice['final_invoice_number'];?>
                            </td>
                            <td>
                                <?php echo $invoice['gross_amount'];?>
                            </td>
                            <td>
                                <?php echo Codelkups::getCodelkup($invoice['default_currency']);?>
                            </td>
                            <td>
                                <?php echo date('d/m/Y',strtotime($invoice['due_date']));?>
                            </td>
							<td>
                                <?php echo $invoice['age'];?>
                            </td>
							
                        </tr>
                        <?php }?>
                    </table>
                </div>
                 <?php }?>

                   <?php if (!empty($project_milestones)){ ?>

                   <?php if(empty($project_risks_closed)){
                   	if (!empty($project_invoices)){?>
                <div  style="width:50%;float:left; " > <!--valign="top";  width:40%; -->
                <?php }else{?> <div  style="width:45%;float:left;" > <?php }}else{?>
 <div  style="width:46%;padding-left:2em;float:right;" > <!--valign="top";  width:40%; -->
                <?php } ?>
                    <!-- if isset -->
                    <?php if (isset($uat_open_checklist)  && !empty($uat_open_checklist )) { ?>
                    <table class=" inner-table" style="width:100%;">
                        <tr>
                            <th colspan="2" style="text-align:left;">
                                <font color="white">Pending Checklist Item(s)</font>
                            </th>
                        </tr>

                        <tr>
                            <td style="background-color:#CCCCBB;"> <!-- width:70%; -->
                                Item
                            </td>
                            <td style="background-color:#CCCCBB;">
                                Category
                            </td>
                        </tr>

                        <?php foreach ($uat_open_checklist as $key => $uat_check) { ?>
                        <tr>
                                <td>
                                    <?php echo  $uat_check['descr'];?>
                                </td>
                                <td>
                                    <?php echo $uat_check['category'];?>
                                </td>
                            </tr>
                        <?php }?>
                    </table>
                    <?php } else if (isset($uat_golive_pending_checklist) && !empty($uat_golive_pending_checklist )) { ?>

                        <table class=" inner-table" style="width:100%;">
                            <tr>
                                <th colspan="2" style="text-align:left;">
                                    <font color="white">Pending Checklist Item(s)</font>
                                </th>
                            </tr>

                            <tr>
                                <td style="background-color:#CCCCBB;"> <!-- width:70%; -->
                                    Item
                                </td>
                                <td style="background-color:#CCCCBB;">
                                    Category
                                </td>
                            </tr>

                        <?php foreach ($uat_golive_pending_checklist as $key => $uat_golive_check) { ?>
                            <tr>
                                <td>
                                    <?php echo  $uat_golive_check['descr'];?>
                                </td>
                                <td>
                                    <?php echo $uat_golive_check['category'];?>
                                </td>
                            </tr>
                        <?php }?>
                        
                    </table>
                        
                   <?php }?>
                </div>
            <!-- </tr> --><?php }?>
                   <?php if (!empty($project_time)){ 
                   	 if (!empty($project_invoices) && !empty($project_risks_closed) && !empty($project_milestones) && (!empty($uat_golive_pending_checklist) || !empty($uat_open_checklist))) {?>
	<div  style="width:50%;float:left;margin-top: 20px;" > 
<?php }else if ((!empty($project_invoices) || !empty($project_risks_closed)) && !empty($project_milestones) && (!empty($uat_golive_pending_checklist) || !empty($uat_open_checklist))) { ?>
<div  style="width:50%;float:left;margin-top: 20px;" >
<?php }else if (empty($project_invoices) && empty($project_risks_closed) && empty($project_milestones) && !empty($uat_golive_pending_checklist)){ ?>
<div  style="width:46%;padding-left:2em;float:right;margin-top:-80px;" > 
<?php }else  if (empty($project_invoices) && empty($project_risks_closed) && !empty($project_milestones) && !empty($uat_golive_pending_checklist)){ ?>
<div  style="width:46%;padding-left:2em;float:right;margin-top:-68px;" > 
<?php }else  if (!empty($project_invoices) && !empty($project_risks_closed)){?>
	<div  style="width:46%;padding-left:2em;float:right;" > 
 <?php }else { ?>
 <div  style="width:50%;float:left;" > <!--valign="top";  width:40%; -->
 <?php 	} ?>
                <table class=" inner-table" style="width:100%;">
                        <tr>
                            <th colspan="2" style="text-align:left;">
                                <font color="white">Time Spent Per Phase</font>
                            </th>
                        </tr>

                        <tr>
                            <td style="background-color:#CCCCBB;"> <!-- width:70%; -->
                                Phase
                            </td>
                            <td style="background-color:#CCCCBB;">
                                Time in  MDs
                            </td>
                        </tr>

                        <?php foreach ($project_time as $key => $uat_check) { ?>
                        <tr>
                                <td>
                                    <?php echo  $uat_check['description'];?>
                                </td>
                                <td>
                                    <?php echo Utils::formatNumber($uat_check['time'],2);?>
                                </td>
                            </tr>
                        <?php }?>
                    </table>
                </div>
            <!-- </tr> --><?php }?>
            </div>
        </div>
    </body>
</html>
