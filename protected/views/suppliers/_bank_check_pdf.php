<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>SNS...</title>
            <style>
               .cheque{
			   width: 661px;
			   margin:0px;
			   margin-left:110px;
			   padding-top:19px;
			    font-size: 15px;
                    font-family: Arial,Verdana,sans-serif;
                    font-weight: bold; 
			   } 
            </style>
            <!--<style>
                html {
                    font-size: 100%; /* 1 */
                    -webkit-text-size-adjust: 100%; /* 2 */
                }
 
                body {
                    margin:0;
                    background: url('back.png') #fff no-repeat left top;
                    background-size:100% auto;
                    font-size: 15px;
                    font-family: Arial,Verdana,sans-serif;
                    font-weight: bold;
                }
 
                
                .amount {
                    margin-left: 75%;
                    margin-top: 5%;
                    width: 23.3%;
                    font-size: 1.5em;
                }
                .supplier {
                    margin-left: 16.2%;
                    margin-top: 7.5%;
                    width: 75.3%;
                    font-size: 1.5em;
                }
                .amountLetters {
                    margin-left: 16.2%;
                    margin-top: 1.5%;
                    width: 75.3%;
                    font-size: 1.5em;
                }
                .place {
                    margin-left: 39.5%;
                    margin-top: 5%;
                    width: 11%;
                    font-size: 1.3em;
                    float: left;
                    clear: left;
                }
                .date {
                    margin-left: 4.6%;
                    margin-top: 5%;
                    width: 11%;
                    float: left;
                    font-size: 1.3em;
                }
            </style>-->
    </head>
    <body>
		<table class="cheque" border="0">
		<tr>
		<td style="width:97px;"><?php echo " " ;?></td>
		<td></td>
		<td></td>
		<td></td>

		<td><?php echo Utils::formatNumber($model->amount);?></td>
		</tr>
		<tr><td colspan="5" style="color:white;"><?php echo "space " ;?></td></tr>
				
		<tr><td colspan="5" style="color:white;"><?php echo "space " ;?></td></tr>

		<tr>
		<td></td>
		<td colspan="3" > <?php if($model->support_user == null ) { echo $model->idSupplier->name;}else{ echo Users::getNameById($model->support_user); }?></td>		
		
		<td></td>
		</tr>
		
		
		<tr>
		<td></td>
		<td colspan="3" > <?php if(substr($model->amount,-2)=='00'){ echo Utils::convert_number_to_words((int)($model->amount));} 
        else{
            echo Utils::convert_number_to_words($model->amount);
            }?></td>		
		
		<td></td>
		</tr>
		<tr><td colspan="5" style="color:white; font-size:12px;"><?php  echo "space " ;?></td></tr>
		<tr>
		<td></td>
		<td style="width:162px; color:white;"><?php  echo "space "  ;?></td>
		<td  style="width:113px; padding-left:5px; padding-bottom:5px;"><?php echo $model->idSupplier->city;?></td>
		<td style="padding-bottom:5px;"><?php echo date('d/m/Y',strtotime($model->date));?></td>
		<td></td>
		</tr>
	</table>	
		
       
    </body>
</html>