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
<table cellspacing="0" border="0">
    <colgroup width="109"></colgroup>
    <colgroup width="135"></colgroup>
    <colgroup width="118"></colgroup>
    <colgroup width="162"></colgroup>
    <colgroup width="156"></colgroup>
    <tr>
        <td style="border-top: 2px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="22" align="center" bgcolor="#C0C0C0"><b><font size=3>Client Code</font></b></td>
        <td style="border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" bgcolor="#C0C0C0"><b><font size=3>Description</font></b></td>
        <td style="border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" bgcolor="#C0C0C0"><b><font size=3>SNSAPJ Invoices</font></b></td>
         <td style="border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" bgcolor="#C0C0C0"><b><font size=3>SNS Invoices</font></b></td>
        <td style="border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" bgcolor="#C0C0C0"><b><font size=3>SNSAPJ Amount USD</font></b></td>
        <td style="border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" bgcolor="#C0C0C0"><b><font size=3>SNS Amount USD</font></b></td>
    </tr>
    <?php
            foreach ($inv as $invoice) {
              // if($invoice['type'] !='Expenses' && $invoice['type'] !='Travel Expenses'  ){
            ?>
    <tr>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="20" align="left"><font face="Calibri"><?php echo Customers::GetNameById($invoice['id_customer']);?></font></td>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="20" align="left"><font face="Calibri"><?php echo $invoice['description'];?></font></td>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left"><font face="Calibri">SNSAPJ-<?php echo $invoice['snsapj_partner_inv']; ?></font></td>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left"><font face="Calibri">SNS-<?php echo $invoice['final_invoice_number']; ?></font></td>
        <td style="align-text:right;border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="right" sdnum="1033;0;[$$-409]#,##0.00"><font face="Calibri"><?php echo $invoice['gross_amount']; ?></font></td>
        <td style="align-text:right;border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="right" sdnum="1033;0;&quot;$&quot;#,##0.00"><font face="Calibri"><?php echo $invoice['usd_amount']; ?></font></td>
    </tr>
       <?php  // }else {
        ?>
   <!--     <tr>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="20" align="left"><font face="Calibri">Expenses</font></td>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left"><font face="Calibri"></font></td>
        <td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left"><font face="Calibri">SNS-<?php echo $invoice['final_invoice_number']; ?></font></td>
        <td style="align-text:right;border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="right" sdnum="1033;0;[$$-409]#,##0.00"><font face="Calibri"><?php echo $invoice['gross_amount']; ?></font></td>
        <td style="align-text:right;border-top: 2px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="right" sdnum="1033;0;&quot;$&quot;#,##0.00"><font face="Calibri"><?php echo $invoice['usd_amount']; ?></font></td>
    </tr>-->
           
       <?php // }
       }?>
    <tr>
        <td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="18" align="left"><b>TOTAL</b></td>
        <td style="border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left"><b><br></b></td>
         <td style="border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left"><b><br></b></td>
        <td style="border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left" sdnum="1033;0;#,##0.00"><b><br></b></td>
        <td style="border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="right" sdval="0" sdnum="1033;0;&quot;$&quot;#,##0.00"><b>$<?php echo number_format($totalpartner, 2, '.', ''); ?></b></td>
        <td style="border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="right" sdval="0" sdnum="1033;0;&quot;$&quot;#,##0.00"><b>$<?php echo number_format($total, 2, '.', '');; ?></b></td>
    </tr>
</table>
<br />