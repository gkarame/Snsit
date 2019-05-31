<form id="offset-reason-form" method="post" ><div class="listofactions unassigned" style="width:300px;"><div class="headlioff"></div>
<div class="contentli scroll_div"><div class="title_offset">REQUEST OFFSET *</div><div class="itemlabel red" style="margin-left:20px">
<b><?php echo "Offset*" ;?></b></div><div class="row column3 item rammini" ><select id="<?php echo $id_phase;?>OffSign" class="input_text_value" name="ProjectsPhases[offset_sign]">
<option value=""></option><option value="Plus">+</option><option value="Minus">-</option></select></div>			
<div class="row column3 item rammini">
<input id="<?php echo $id_phase;?>Off" value="0" style="width:35px;  text-align:center; font-color:#fff" name="ProjectsPhases[offset]" type="text">
</div><br/><br/><br/><div class="row"><div class="itemlabel red" style="margin-left:20px; margin-bottom:5px;"><b><?php echo "Reason*" ;?></b>
</div><textarea id="offset-text" name="offset-text" class="offsetreason"></textarea></div></div>
<ul class="act"><li class="customBtn">
<a href="javascript:void(0);" class="save customSaveBtn ua" onclick="setreason(<?php echo $id_phase ;?>);"><?php echo Yii::t('translation', 'Send');?></a>
<a href="javascript:void(0);" class="customCancelBtn ua" onclick="cancelreason(<?php echo $id_phase ;?>)"><?php echo Yii::t('translation', 'Cancel');?></a>
</li></ul><div class="ftrli"></div></div></form>