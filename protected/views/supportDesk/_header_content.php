<div class="view_row">
    <div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('SR#')); ?></div>
    <div class="general_col2 "><?php echo CHtml::encode($model->sd_no); ?></div>
    <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('short_description')); ?></div>
    <div class="general_col4 "><?php echo CHtml::encode($model->short_description); ?></div>
</div>
<div class="view_row">
    <div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('product')); ?></div>
    <div class="general_col2 "><?php echo CHtml::encode($model->product0->codelkup); ?></div>
    <div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('environment')); ?></div>
    <div class="general_col4 "><?php echo CHtml::encode($model->environment); ?></div>
</div>
<div class="view_row">
    <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('schema')); ?></div>
    <div class="general_col2 "><?php echo CHtml::encode($model->schema); ?></div>
    <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('severity')); ?></div>
    <div class="general_col4 "><?php echo CHtml::encode($model->severity); ?></div>
</div>
<div class="view_row">
    <div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
    <div class="general_col2 "
         id="status-support"><?php echo CHtml::encode(SupportDesk::getStatusLabel($model->status)); ?></div>
    <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('system_down')); ?></div>
    <div class="general_col4 "><?php echo CHtml::encode($model->system_down); ?></div>
</div>
<div class="view_row">
    <div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('due_date')); ?></div>
    <?php if (Yii::app()->user->isAdmin) { ?>
        <div class="general_col2 "><?php echo CHtml::textField("change_" . $model->id, ($model->due_date != null) ? date("d/m/Y", strtotime($model->due_date)) : "", array("style" => "width:70px;text-align:left;border:none;background:#F0F0F0;color:#555555;font-family:Arial;font-size:12px", "onClick" => "changeDate($model->id)", "onchange" => "changeInput(value,$model->id,'1')")) ?></div>
    <?php } else { ?>
        <div class="general_col2 "><?php echo CHtml::encode($model->due_date); ?></div><?php } ?>
    <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('assigned_to')); ?></div>
    <?php if ((Users::checkCSManagers(Yii::app()->user->id)) > 0) { ?>
        <div class="general_col4 "><?php echo SupportDesk::getAllUsers($model->id, $model->assigned_to) ?></div>
    <?php } else { ?>
        <div class="general_col4 "
             id="assigned_to_txt"><?php echo CHtml::encode(Users::getUsername($model->assigned_to)); ?></div><?php } ?>
</div>
<div class="view_row">
    <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
    <div class="general_col2 "><?php echo(SupportDesk::getDescriptionGrid($model->description)); ?></div>
    <div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('SR Occured Previously')); ?></div>
    <div class="general_col4 "><?php echo CHtml::encode($model->issue_incurred_previously); ?></div>
</div>
<div class="view_row">
    <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('SR Related to Customization')); ?></div>
    <div class="general_col2 "><?php echo CHtml::encode($model->issue); ?></div>
    <?php if (Yii::app()->user->isAdmin) { ?>
        <div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('reason')); ?></div>
        <div class="general_col4 "><?php echo CHtml::activeDropDownList($model, "reason", Codelkups::getCodelkupsDropDown('reason'), array('class' => 'input_text_value', 'prompt' => " ", 'style' => 'width:190px;border:none;', "onchange" => "changeInput(value,$model->id,3)")); ?></div>
    <?php } else { ?>
        <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Submitted By')); ?></div>
        <div class="general_col4 "><?php echo CHtml::encode($model->submitter_name); ?></div><?php } ?></div>
<?php if (Yii::app()->user->isAdmin) { ?>
    <div class="view_row">
        <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('repeat')); ?></div>
        <div class="inline-block bigger_amt general_col2">
            <div class="o_clasa" onclick="CheckOrUncheckInput(this)"
                 style="display:block;with:25px;height:25px;position:relative">
                <div class="repeat_inp input <?php echo ($model->repeat == "Yes") ? "checked" : "" ?>"
                     style="margin-left:0px;">
                    <?php echo CHtml::CheckBox('repeat', ($model->repeat == "Yes") ? true : false, array('value' => 'Yes', 'style' => 'width:10px;margin-left: 17px;margin-top: 8px;')); ?>
                </div>
            </div>
        </div>
        <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('CA')); ?></div>
        <div class="general_col4 "><?php echo CHtml::encode(Users::getUsername(Customers::getCAbyCustomer($model->id_customer))); ?></div>
    </div>
    <div class="view_row">
        <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Logged By')); ?></div>
        <div class="general_col2 "><?php echo CHtml::encode($model->submitter_name); ?></div>
        <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Customer Name')); ?></div>
        <div class="general_col4 "><?php echo CHtml::encode(Customers::getNameById($model->id_customer)); ?></div>
    </div>
    <div class="view_row">
        <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('num_of_followups')); ?></div>
        <div class="general_col2 num_of_followups"><?php echo CHtml::encode($model->num_of_followups); ?></div>
        <div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('last_followup_date')); ?></div>
        <div class="general_col4 last_followup_date"><?php echo $model->last_followup_date != null ? date("d/m/Y H:i:s", strtotime($model->last_followup_date)) : ""; ?></div>
    </div>
    <div class="view_row">
    <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Submitted By')); ?></div>
    <div class="general_col2 "><?php echo CHtml::encode($model->submitter_name); ?></div>
    <div class="general_col3"><?php echo "Connections Link"; ?></div>
    <div class="general_col4 "><a
                href="<?php echo Yii::app()->createAbsoluteUrl("customers/view/" . $model->id_customer); ?>"> Click
            Here </a></div>
    </div><?php if ($model->status == 5) { ?>
        <div class="view_row">
            <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('exclude')); ?></div>
            <div class="inline-block bigger_amt general_col2">
                <div class="o_clasa" onclick="CheckOrUncheckInputexclude(this)"
                     style="display:block;with:25px;height:25px;position:relative">
                    <div class="repeat_inp input <?php echo ($model->exclude == "Yes") ? "checked" : "" ?>"
                         style="margin-left:0px;">
                        <?php echo CHtml::CheckBox('exclude', ($model->exclude == "Yes") ? true : false, array('value' => 'Yes', 'style' => 'width:10px;margin-left: 17px;margin-top: 8px; ', 'class' => 'hidden')); ?>
                    </div>
                </div>
            </div>
            <div class="general_col3 " id="rating"><?php echo CHtml::encode($model->getAttributeLabel('Rate')); ?></div>
            <?php $x = $model->rate; ?>
            <div class="general_col4 " id="rating"><img src="../../images/<?php echo $x; ?>stars.png" width="80px;">
            </div>
        </div>
    <?php } ?>

    <div class="view_row">
        <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('deployment')); ?></div>
        <div class="general_col2 "><?php echo SupportDesk::getdeploymentdd($model->id, $model->deployment) ?></div>
        <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
        <?php if ((Users::checkCSManagers(Yii::app()->user->id)) > 0 && $model->status != 5 && $model->status != 3) { ?>
            <div class="general_col4 "><?php echo CHtml::activeDropDownList($model, "category", supportDesk::getCategoryList(), array('class' => 'input_text_value', 'prompt' => " ", 'style' => 'width:190px;border:none;', "onchange" => "changeInput(value,$model->id,8)")); ?></div>
        <?php } else { ?>
            <div class="general_col4 "><?php echo CHtml::encode(supportDesk::getCategoryLabel($model->category)); ?></div>
        <?php } ?>
    </div>
<?php } ?>
<div class="view_row">
    <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('escalate')); ?></div>
    <div class="inline-block bigger_amt general_col2">
        <div class="o_clasa" style="display:block;with:25px;height:25px;position:relative">
            <div id="escalatec" class="escalate_inp input <?php echo ($model->escalate == "Yes") ? "checked" : "" ?>"
                 style="margin-left:0px;">
                <?php echo CHtml::CheckBox('escalate', ($model->escalate == "Yes") ? true : false, array('value' => 'Yes', 'style' => 'width:10px;margin-left: 17px;margin-top: 8px;')); ?>
            </div>
        </div>
    </div>

    <?php if ($model->escalate == "Yes") { ?>
        <div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('escalate_date')); ?></div>
        <div class="general_col4 "><?php echo CHtml::encode(date("d/m/Y", strtotime($model->escalate_date))); ?></div>
    <?php } else { ?>


        <?php if (Yii::app()->user->isAdmin) { ?>
            <div id="rsrtitle" class="general_col3 <?php if (empty(SupportRequest::getRSR($model->id))) {
                echo 'hidden';
            } ?>"><?php echo CHtml::encode($model->getAttributeLabel('RSR#')); ?></div>
            <div id="rsrnum" class="general_col4 <?php if (empty(SupportRequest::getRSR($model->id))) {
                echo 'hidden';
            } ?>">
                <a href="<?php echo Yii::app()->createAbsoluteUrl("SupportRequest/update/" . SupportRequest::getRSR($model->id)); ?>"> <?php echo SupportRequest::getRSR($model->id); ?> </a>
            </div>

        <?php }
    } ?>
</div>
<?php if (Yii::app()->user->isAdmin && $model->escalate == "Yes") { ?>
    <div class="view_row">

        <div id="rsrtitle" class="general_col1 <?php if (empty(SupportRequest::getRSR($model->id))) {
            echo 'hidden';
        } ?>"><?php echo CHtml::encode($model->getAttributeLabel('RSR#')); ?></div>
        <div id="rsrnum" class="general_col2 <?php if (empty(SupportRequest::getRSR($model->id))) {
            echo 'hidden';
        } ?>">
            <a href="<?php echo Yii::app()->createAbsoluteUrl("SupportRequest/update/" . SupportRequest::getRSR($model->id)); ?>"> <?php echo SupportRequest::getRSR($model->id); ?> </a>
        </div>


    </div>
<?php } ?>
<script>
    function changeDate(id) {
        $("input#change_" + id).datepicker({dateFormat: 'dd/mm/yy'}).datepicker("show");
        $('#ui-datepicker-div').css('top', parseFloat($("input#change_" + id).offset().top) + 25.0);
        $('#ui-datepicker-div').css('left', parseFloat($("input#change_" + id).offset().left));
    }

    function changeInput(value, id_support_dask, type) {
        $.ajax({
            type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/assigned');?>", dataType: "json",
            data: {'value': value, 'id_support_desk': id_support_dask, 'type': type},
            success: function (data) {
                if (data) {
                    if (data.status == 'success') {
                        console.log('da');
                    }
                }
            }
        });
    }

    function CheckOrUncheckInput(obj) {
        var checkBoxDiv = $(obj).find('.input');
        var input = $(obj).find('input[type="checkbox"]');
        if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')) {
            if (checkBoxDiv.hasClass('checked')) {
                checkBoxDiv.removeClass('checked');
                input.prop('checked', false);
                changeInput('No',<?php echo $model->id; ?>, 5);
            } else {
                checkBoxDiv.addClass('checked');
                input.prop('checked', true);
                changeInput('Yes',<?php echo $model->id; ?>, 5);
            }
            if ($(obj).hasClass('read') || $(obj).hasClass('write')) {
                groupPermissions(obj);
            }
        }
    }

    function CheckOrUncheckInputexclude(obj) {
        var checkBoxDiv = $(obj).find('.input');
        var input = $(obj).find('input[type="checkbox"]');
        if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')) {
            if (checkBoxDiv.hasClass('checked')) {
                checkBoxDiv.removeClass('checked');
                input.prop('checked', false);
                changeInput('No',<?php echo $model->id; ?>, 6);
            } else {
                checkBoxDiv.addClass('checked');
                input.prop('checked', true);
                changeInput('Yes',<?php echo $model->id; ?>, 6);
            }
            if ($(obj).hasClass('read') || $(obj).hasClass('write')) {
                groupPermissions(obj);
            }
        }
    }
</script>