<style>
    div, span, table {
        font:11pt Calibri;
        color:#000;
    }
    table {
        border-collapse:collapse;
        width:100%;
    }
    .first td {
        padding-bottom:5px;
        padding-left:7px;
        padding-right:0;
        padding-top:0;
    }
    .second {
        min-height:300px;
        border:none;
        margin-top:60px;
    }
    .second td, .second th {
        padding:10px 5px;
    }
    .h2 {
        font:bold 12pt Calibri;
    }
    .h3_bold {
        font:bold 11pt Calibri;
    }
    .h3 {
        font:11pt Calibri;
    }
</style>

<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;">
    <div class="table_rep">
        <h3 style="margin: 10px">Travel Report</h3>
        <table class="second">
            <tr>
                <th class="h2" style="text-align:left;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">Traveler</th>
                <th class="h2" style="text-align:left;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Departure Date</th>
                <th class="h2" style="text-align:left;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> Return Date</th>
                <th class="h2" style="text-align:left;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Period (days)</th>
                <th class="h2" style="text-align:left;width:34%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Project</th>
                <th class="h2" style="text-align:left;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Customer</th>
                <th class="h2" style="text-align:left;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Destination</th>
                <th class="h2" style="text-align:left;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Status</th>
            </tr>
            <?foreach ($data as $item):?>
                <?php
                $datetime1 = new DateTime(str_ireplace('/','-',$item['departure_date']));
                $datetime2 = new DateTime(str_ireplace('/','-',$item['return_date']));
                $period = $datetime1->diff($datetime2);
                ?>
            <tr>
                <td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px"><?=Users::getNameById((int)$item['traveler']);?></td>
                <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$item['departure_date'];?></td>
                <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$item['return_date'];?></td>
                <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$period->format('%a');?></td>
                <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$item['purpose'];?></td>
                <td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$item['id_customer'];?></td>
                <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=Codelkups::getCodelkup((int)$item['destination']);?></td>
                <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=Booking::getStatusLabel((int)$item['status']);?></td>
            </tr>
            <?endforeach;?>
            <tr>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">  <?php echo " " ; ?>
                </td>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
                </td>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
                </td>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
                </td>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
                </td>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
                </td>
                <td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
                </td>
                <td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:20px">
                </td>
            </tr>
        </table>
    </div>
</div>