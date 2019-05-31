<div class="item  " style="float:left; margin-left:90px;margin-right:10px;"></div>
<div class="itemlabel red" style="margin-left:25px;" ><b><?php echo "Budgeted:";?></b></div>
<div class="column30 item ram2" style="text-align:center;" id="man_days_budgeted" ><span id= "total-man-days-budgeted" class="value" ><?php echo Projects::getEstimatedMD($model->id); ?></span></div>  
<div class="itemlabel red"  style="margin-left:35px;" ><b><?php echo "Total(with offset):";?></b></div>
<div class="column30 item" style="text-align:center;" id="total"><span id= "total-total-estimate" class="value" ><?php echo Projects::getIncludingOffsetMD($model->id); ?></span><div id="offset-reas" style="display:none;"></div>
</div><div class="itemlabel red"  style="margin-left:35px;"><b><?php echo  "Actuals:";?></b></div>
<div class="column30 item" style="text-align:center;"><span id="man_day_rate" class="value"><?php echo Utils::formatNumber(Projects::gettotActualMD($model->id)); ?></span></div>
<div class="itemlabel red" style="margin-left:35px;" ><b><?php echo  "Offsets (MDs):";?></b></div>
<div class="column30 item" style="text-align:center;"><span id="man_day_rate" class="value"><span id="total-off"><?php echo Utils::formatNumber(Projects::getTotalOffsetMD($model->id)); ?></span></span>
</div><div class="itemlabel red" style="margin-left:35px;"><b><?php echo  "# of Offsets:";?></b></div>
<div class="column30 item" style="text-align:center;"><span id="man_day_rate" class="value"><span id="total-offrequests"><?php echo Utils::formatNumber(Projects::getTotalOffset($model->id)); ?></span></span></div>
<div class="column6 item" style="margin-left:23px;" ><b><?php echo ""?></b></div>
<div class="column6 item" style="margin-right:7px;" ></div>