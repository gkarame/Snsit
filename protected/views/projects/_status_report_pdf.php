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
      
            <div  style="width:100%;">  
               <!--  <tr> -->
                <!-- <td style="width:10%;"> -->
                <div style="float:left;width:10%;">
                     <img style="width:150%;height:150%;" src="<?php echo Yii::app()->getBaseUrl().'/images/logo_status_report.png';?>" />
               </div>
                <?php
                	$file=Customers::getLogo($model->customer_id);
                	if( $file != null){ 	
                 	$actp= "/uploads/customers/".$model->customer_id."/documents/".$file."/".Customers::getFName($file);   ?> 
				   <div style="float:right;width:10%;">
	                     <img style="display: block; " height="48" width="180" src="<?php echo Yii::app()->getBaseUrl().$actp;?>" />
	               </div>
                 <?php }  ?> 
			      <br clear="all">   <br clear="all">  <br clear="all">
               <!--  </td>  -->
                <!-- <td style="padding-left:2em;width:400px;" valign="top"> -->  <!-- width:40%; -->
                <div style="float:left;width:25%;">
                    <table class= "inner-table fullwidth" > 
                        <tr>
                            <th style="width:40%;text-align:left;"> <!--  -->
                               <font color=white> Customer </font>
                            </th>
                            <td>
                                <?php 
                                    echo Customers::getNameByID(($model->customer_id));
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- </td> -->
                <!-- <td style="padding-left:2em;" valign="top"> --> <!-- width:29%; -->
                <div style="padding-left:3em;float:left;width:21%;">
                     <table class="inner-table" style="width:100%;">
                        <tr>
                            <th style="width:50%;text-align:left;"> <!-- style="width:60%;" -->
                                <font color="white">   Project Manager </font>
                            </th>
                            <td>
                                <?php echo Users::getNameById($model->project_manager);?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width:50%;text-align:left;"> <!-- style="width:60%;" -->
                               <font color="white"> Business Manager </font>
                            </th>
                            <td>
                               <?php echo Users::getNameById($model->business_manager);?> 
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- </td> -->
                <!-- <td style="padding-left:2em;" valign="top"> --> <!-- width:20%; -->
                <div style="padding-left:3em;float:left;width:45.8%;">
                    <table class="inner-table" style="width:100%;">
					<tr>
                            <th style="width:22%;text-align:left;"> <!-- style="width:50%;" -->
                               <font color="white"> Project Name</font>
                            </th>
                            <td>
                                <?php echo $model->name; ?>
                            </td>
                        </tr>
						
                        <tr>
                            <th style="text-align:left;">
                                <font color="white"> Report Date </font>
                            </th>
                            <td>
                               <?php echo date('d/m/Y',strtotime("now"));?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- </td> -->
                <!-- </tr> -->
            </div>
            <br clear="all">

            <div style="width:100%;">
                <!-- <tr> -->
                    <div style="width:50%;float:left;" > <!-- valign="top" -->
						
                        <table class="inner-table" style="width:100%;">
                        <tr>
                            <th colspan="5"  style="text-align:left;"><font color="white">Project Milestones</font></th>
                        </tr>
                            <tr>
                                <td colspan="2" style="background-color:#CCCCCC">Milestone</td>
                                <td style="background-color:#CCCCCC">Status</td> 
                                <td style="background-color:#CCCCCC">Start Date</td>
                                <td style="background-color:#CCCCCC">End Date</td>
                            </tr>
                            <?php foreach ($project_phases as $key => $phase) { ?>
                            
                            <tr style="<?php if ($phase['status'] == "Closed") {echo "background-color:#CCCCCC;";} else if ($phase['status'] == "In Progress"){echo "background-color:#FFC000;"; } ?>" >
                                <td style="width:3%;">
                                   <?php echo $key+1;?>
                                </td>
                                <td style="width:45%;">
                                    <?php echo $phase['description'];?>
                                </td>
                                <td>
                                     <?php echo $phase['status'];?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($phase['estimated_date_of_start']));?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($phase['estimated_date_of_completion']));?>
                                </td>
                            </tr>
                                   
                                <?php  } ?>      
                        
                        </table>
                    </div>
                <?php if (!empty($project_highlights)){ ?>
                    <div style="width:46%;padding-left:2em;float:right;"  > <!-- valign="top" -->
						
					
                        <table class="inner-table" style="width:100%;">
                            <tr>
                                <th style="text-align:left;">
                                    <font color="white"> Highlight(s)</font>
                                </th>
                            </tr>
                            <?php foreach ($project_highlights as $highlight) { ?>
                                <tr style="width:100%">
                                    <td>
                                            <?php echo $highlight['description'];?>
                                    </td>
                                </tr>
                               <?php }?> 
                        </table>
					
                    </div><?php }   ?>
                <!-- </tr> -->
            </div>
            <br clear="all">
            <div style="width:100%;">
            
            <table  class ="inner-table " style="align:center;">
                <tr>
                    <th colspan="3" style="width:35%;font-size:100%;text-align:left;">
                        <font color="white">Project Health Indicators</font>
                    </th>
                    <th colspan="<?php echo sizeof($project_health_indicators);?>"  style="width:65%;font-size:100%;text-align:left;">
                        <font color="white">Period Ending</font>
                    </th>
                </tr>
                <tr>
                    <td style="background-color:#F2DCDB;">
                        On Track &emsp;&emsp; 
                    </td>
                    <td colspan="2" style="background-color:green;text-align:center;">
                        G &emsp;&emsp;
                    </td>
                    <!--  here is to iterate -->
                    <?php foreach ($project_health_indicators as $key => $indicator) {
                        ?>
                    
                    <td rowspan="3" style="<?php if( $key % 2 == 0) { echo "background-color:#F2DCDB;";}?>;text-align:center;width:<?php echo (65/sizeof($project_health_indicators)); ?>%;">
                        <div  style="text-align: center;font-size:150%;">
                            <p style="text-align: center;"> <?php echo date('d',strtotime($indicator['indicators_date'])).'/'.date('m',strtotime($indicator['indicators_date'])).'/'.date('y',strtotime($indicator['indicators_date']));?></p>
                        </div>
                    </td>
                    <?php }?>

                   
                <!--  here is to iterate -->
                    
                </tr>
                <tr>
                    <td style="background-color:#F2DCDB;">
                    Manageable Issues / Risks &emsp;&emsp; 
                    </td>
                    <td colspan="2" style="background-color:yellow;text-align:center;">
                    Y &emsp;&emsp;
                    </td>
                </tr>
                <tr>
                    <td style="background-color:#F2DCDB;">
                    Major Problems &emsp;&emsp;&emsp;&emsp;
                    </td>
                    <td colspan="2" style="background-color:red;text-align:center;">
                    R &emsp;&emsp; 
                    </td>
                </tr>
                <tr>
                    <td>
                    Project Scope
                    </td>
                    <td colspan="2" style="background-color:#F2DCDB;">
                    </td>
                    <!--  here is to iterate -->
                    <?php foreach ($project_health_indicators as $indicator) { ?>
                       <td
                         style="text-align:center;margin-left:-3em;margin-right:-3em; <?php switch ($indicator['project_scope']) {
                            case '1':
                                echo "background-color:green;";
                                break;
                            case '2':
                                echo "background-color:yellow;";
                                break;
                            case '3':
                                echo "background-color:red;";
                                break;
                            default:
                                # code...
                                break;
                        }?>"
                       >
                        <?php if ($indicator['project_scope']!='0')
                        { echo StatusReportHealthIndicators::getIndicatorLabel($indicator['project_scope']);}
                        else 
                            { echo '';}?>
                       </td>
                   <?php }?>
                    <!--  here is to iterate -->
                </tr>
                
                <tr>
                    <td>
                        Resources
                    </td>
                    <td colspan="2" style="background-color:#F2DCDB;">
                    </td>
                    <!--  here is to iterate -->
                     <?php foreach ($project_health_indicators as $indicator) { ?>
                       <td
                        style="text-align:center;margin-left:-3em;margin-right:-3em; <?php switch ($indicator['resources']) {
                            case '1':
                                echo "background-color:green;";
                                break;
                            case '2':
                                echo "background-color:yellow;";
                                break;
                            case '3':
                                echo "background-color:red;";
                                break;
                            default:
                                # code...
                                break;
                        }?>"
                       >
                          <?php if ($indicator['resources']!='0')
                        { echo StatusReportHealthIndicators::getIndicatorLabel($indicator['resources']);}
                        else 
                            { echo '';}?>
                       </td>
                   <?php }?>
                    <!--  here is to iterate -->
                </tr>
                
                <tr>
                    <td>
                        Timeline
                    </td>
                    <td colspan="2" style="background-color:#F2DCDB;">
                    </td>
                    <!--  here is to iterate -->
                    <?php foreach ($project_health_indicators as $indicator) { ?>
                       <td
                        style="text-align:center;margin-left:-3em;margin-right:-3em; <?php switch ($indicator['timeline']) {
                            case '1':
                                echo "background-color:green;";
                                break;
                            case '2':
                                echo "background-color:yellow;";
                                break;
                            case '3':
                                echo "background-color:red;";
                                break;
                            default:
                                # code...
                                break;
                        }?>"
                       >
                         <?php if ($indicator['timeline']!='0')
                        { echo StatusReportHealthIndicators::getIndicatorLabel($indicator['timeline']);}
                        else 
                            { echo '';}?>
                       </td>
                   <?php }?>
                    <!--  here is to iterate -->
                </tr>
                
                <tr>
                    <td>
                    Project Finance
                    </td>
                    <td colspan="2" style="background-color:#F2DCDB;">
                    </td>
                    <!--  here is to iterate -->
                    <?php foreach ($project_health_indicators as $indicator) { ?>
                       <td
                        style="text-align:center;margin-left:-3em;margin-right:-3em; <?php switch ($indicator['project_finance']) {
                            case '1':
                                echo "background-color:green;";
                                break;
                            case '2':
                                echo "background-color:yellow;";
                                break;
                            case '3':
                                echo "background-color:red;";
                                break;
                            default:
                                # code...
                                break;
                        }?>"
                       >
                       <?php if ($indicator['project_finance']!='0')
                        { echo StatusReportHealthIndicators::getIndicatorLabel($indicator['project_finance']);}
                        else 
                            { echo '';}?>
                       </td>
                   <?php }?>
                    <!--  here is to iterate -->
                </tr>
                
                <tr>
                    <td>
                    Risks &amp; Issues
                    </td>
                    <td colspan="2" style="background-color:#F2DCDB;">
                    </td>
                    <!--  here is to iterate -->
                     <?php foreach ($project_health_indicators as $indicator) { ?>
                       <td
                        style="text-align:center;margin-left:-3em;margin-right:-3em; <?php switch ($indicator['risks_issues']) {
                            case '1':
                                echo "background-color:green;";
                                break;
                            case '2':
                                echo "background-color:yellow;";
                                break;
                            case '3':
                                echo "background-color:red;";
                                break;
                            default:
                                # code...
                                break;
                        }?>"
                       >
                         <?php if ($indicator['risks_issues']!='0')
                        { echo StatusReportHealthIndicators::getIndicatorLabel($indicator['risks_issues']);}
                        else 
                            { echo '';}?>
                       </td>
                   <?php }?>
                    <!--  here is to iterate -->
                </tr>
                
                <tr>
                    <td >
                    Overalll Project Health
                    </td>
                    <td colspan="2" style="background-color:#F2DCDB;">
                    </td>
                    <!--  here is to iterate -->
                    <?php foreach ($project_health_indicators as $indicator) { ?>
                       <td
                        style="text-align:center;margin-left:-3em;margin-right:-3em; <?php switch ($indicator['overall_project_health']) {
                            case '1':
                                echo "background-color:green;";
                                break;
                            case '2':
                                echo "background-color:yellow;";
                                break;
                            case '3':
                                echo "background-color:red;";
                                break;
                            default:
                                # code...
                                break;
                        }?>"
                       >
                        <?php if ($indicator['overall_project_health']!='0')
                        { echo StatusReportHealthIndicators::getIndicatorLabel($indicator['overall_project_health']);}
                        else 
                            { echo '';}?>
                       </td>
                   <?php }?>
                    <!--  here is to iterate -->
                </tr>
                
            </table>
            </div> 
        </div>
    </body>
</html>
