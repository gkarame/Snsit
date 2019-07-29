<?php //echo $user."ddd";
if (!empty($mes))
{
    echo $mes;
}else {?>
    <div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;">
        <div class="table_rep">

            <table class="second">
                <tr>
                    <th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">TR#</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Traveler</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Destination</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">Customer</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Purpose</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Departing</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">Returning</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Billable</th>
                    <th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Status</th>
                </tr>



                <?php foreach($travels as $key => $travel) {?>

                    <tr>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$travel['id'];?></td>
                        <td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px"><?=Users::getNameByid($travel['traveler']);?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=Codelkups::getCodelkup($travel['destination']);?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$travel['id_customer'];?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$travel['purpose'];?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$travel['departure_date'];?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$travel['return_date'];?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=$travel['billable'];?></td>
                        <td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?=Booking::getStatusLabel((int)$travel['status']);?></td>
                    </tr>

                <?php }?>
            </table>
        </div>


    </div>
<?php }?>
