<style>
    div, span {
        font-family: Helvetica;
        font-size: 13px;
    }
    table {
        border-collapse:collapse;
        width:100%;
        font-family: Helvetica;
        font-size: 13px;
        color:#000000;
    }
    .second {
        min-height:100px;
        border:none;
    }
    
    .second td{
        padding:0px 10px 24px 3px;
        text-align:left;
        font-family:Calibri;
    }
    .second .th td{
        padding:10px 10px 12px 3px;
        text-align:left;
        font-family:Calibri;
    }
    .first td {
        padding:5px;
    }
    .first tr td:first-child{
        text-align:right !important;
    }
    .second tr td h2 {
        color:black;
        font:bold 11px Calibri;
    }
    table.second tr td h3{
        font:14px Calibri !important;
        color:#000 !important;
    }
    table.first tr td.h3,table.firstt tr td.h3{
        font:16px Calibri !important;
    }
    table.first{
        position:absolute;
        top:0px;
        left:0px;
    }
    tabel.firstt tr td.right{
        text-align:right;
    }
</style>
<table class="firstt">
    
    <tr>
        <td colspan="2"><img style="width:250px;margin-top:-10px" src="<?php echo Yii::app()->getBaseUrl().'/images/snsi_logo.png';?>" /></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td colspan="3"></td>
    </tr>
    
    <tr>
        <td style="vertical-align:top;width:50%;" rowspan="8" ></td>
        <td colspan="2"></td>
    </tr>
    
</table>    

<table class="second">
    <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri"> </td>
    </tr>
    <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri;text-decoration: underline;" >Beirut: <?php echo date('M d, Y');?></td>
    </tr>
    <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri"> </td>
    </tr>
    
     <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri;" ><b>Messrs. Audi Bank - Zouk Branch</b> <br/>Fax: 9/225505 </td>
    </tr>
     
     <tr>
        <td colspan="4" class="h3" style="padding:2px;font-size:16px;font-family:Calibri;" ><b><span style="text-decoration: underline;">Subject:</span> Transfer to NECB SNSAPJ US-dollar Account - Transfer <?php echo $Transfernb;?></b> </td>
    </tr>
      <tr>
        <td colspan="4" class="h3" style="padding:2px;font-size:16px;font-family:Calibri;" ><b><span style="text-decoration: underline;">Reason:</span> Commission Payment</b> </td>
    </tr>
     
       <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri"> </td>
    </tr>
      <tr> 
    </tr>
      <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri">Kindly transfer from SNSI US-dollar current account number 901966/461/002/012/02 the amount of <b> $ <?php echo Utils::formatNumber($total,2);?> (<?php echo $word;?>)</b>  to the following Bank Account. Transfer to be conducted today: </td>
    </tr>
    
</table>
<table class="second" >
    <tr>
        <td colspan="5" class="h3" style="padding:2px;padding-left:70px;font-size:16px;font-family:Calibri; text-align:right !important;"></td>
        
        <td colspan="2" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri; text-align:right !important;">Bank: </td>
        <td colspan="2" class="h3" style="padding:2px;padding-top:15px;font-size:16px;font-family:Calibri;text-align:right !important;">SNS APJ PTE. LTD.</td>
   
    </tr>
   
     <tr>
        <td colspan="5" class="h3" style="padding:2px;padding-left:70px;font-size:16px;font-family:Calibri; text-align:right !important;"></td>
        
        <td colspan="2" class="h3" style="padding:2px;padding-top:8px;font-size:16px;font-family:Calibri; text-align:right !important;">Beneficiary: </td>
        <td colspan="2" class="h3" style="padding:2px;padding-top:8px;font-size:16px;font-family:Calibri;text-align:right !important;">NEAR EAST COMMERCIAL BANK SAL</td>
   
    </tr>
    
      <tr>
        <td colspan="5" class="h3" style="padding:2px;padding-left:70px;font-size:16px;font-family:Calibri; text-align:right !important;"></td>
        
        <td colspan="2" class="h3" style="padding:2px;padding-top:8px;font-size:16px;font-family:Calibri; text-align:right !important;">Swift Code: </td>
        <td colspan="2" class="h3" style="padding:2px;padding-top:8px;font-size:16px;font-family:Calibri;text-align:right !important;">NECBLBBE </td>
   
    </tr>


     <tr>
        <td colspan="5" class="h3" style="padding:2px;padding-left:70px;font-size:16px;font-family:Calibri; text-align:right !important;"></td>
        
        <td colspan="2" class="h3" style="padding:2px;padding-top:8px;font-size:16px;font-family:Calibri; text-align:right !important;">IBAN Number: </td>
        <td colspan="2" class="h3" style="padding:2px;padding-top:8px;font-size:16px;font-family:Calibri;text-align:right !important;">LB08004820500265210024602000</td>
   
    </tr>
</table>
<br />
<table class="second">
    
    <tr>
        <td colspan="4" class="h3" style="padding:2px;font-size:16px;font-family:Calibri" >Thanking you in advance for your swift action.</td>
    </tr>
    <tr>
        <td colspan="4" class="h3" style="padding:2px;padding-top:25px;font-size:16px;font-family:Calibri">Best regards,</td>
    </tr>
    <tr>
        <td colspan="4" class="h3" style="padding:1px;font-size:16px;font-family:Calibri"></td>
    </tr>
    <tr>
        <td colspan="4" class="h3" style="padding:0px 1px;font-size:16px;font-family:Calibri;padding-top:50px;">Mario Ghosn</td>
    </tr>
    <tr>
        <td colspan="4" class="h3" style="padding:0px 1px;font-size:16px;font-family:Calibri">General Manager</td>
    </tr>
</table>
<div style="padding-top:20px;"> </div>