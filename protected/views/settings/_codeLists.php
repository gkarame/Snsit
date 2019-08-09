<style>
    .dashitem .textBox .input_text.input_text_two_col{width: 160px;float: left;background-size: cover;}
    .dashitem .textBox .input_text.input_text_two_col:after{content: '';position: absolute;width: 1.5px;background: #ccc;right: 5px;top: 2px;bottom: 3px;}
</style>

<div class="confgroup" id="sortable"><?php $visible = true;
    if (!GroupPermissions::checkPermissions('settings-codelists', 'write')) {
        $visible = false;
    }
    $len_countryes_perdier = count($all_countries_perdiem);
    foreach ($codelists_categories as $i => $category) {
        $codelists = $category->codelists;
        $len = count($codelists);
        if ($len) { ?>
            <div class="dashitem inline-block">
                <div class="cat_header head">
                    <div class="title"><?php echo $category->name; ?></div>
                    <div class="drag"></div>
                </div>
                <div id="codelists_<?=$category->id?>" class="cat_content contenu <?php if ($category->id === '13') {
                    echo 'template';
                } ?>">

                    <?php foreach ($codelists as $i => $codelist) { ?>
                        <div class="codelist textBox inline-block<?php echo ($i == $len - 1 && $len_countryes_perdier === 0) ? ' last' : ''; ?>"
                             id="codelist_<?php echo $codelist->id; ?>"
                             style="padding-bottom:<?php echo ($codelist->id === '8' || $codelist->id === '37') ? ' 30px' : ''; ?>">
                            <?php if($codelist->id === '39'):?>
                                <div class="normal_mode">
                                    <div class="codelist_name input_text_desc"><?php echo $codelist->label; ?></div>
                                    <div class="input_text input_text_two_col">
                                        <div class="arrow"></div>
                                        <div class="hdselect">
                                            <?php echo CHtml::dropDownList('country_id','', Country::getCountersDropDownOriginals(), array('prompt' => 'Choose country ', 'class' => 'codelist_dropdown', 'id' => 'country_id_'.$codelist->id,'style' => 'z-index: 1000;')); ?>
                                        </div>
                                    </div>
                                    <div class="input_text" style="width: 93px;float: left;margin-left: 0;background-position: right;">
                                        <?=CHtml::textField('perdier', '', array('id' => 'perdier_' . $codelist->id, 'class' => 'codelkup_input','style' => 'width: 100%;z-index: 1000;')); ?>
                                    </div>
                                        <div class="btnsList" style="top: 5px;">
                                        <a class="addCodelkup manageCodelkup" href="javascript:void(0);"
                                           onclick="saveCountryPerdier(<?=$codelist->id?>);return false;">ADD</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="normal_mode">
                                    <div class="codelist_name input_text_desc"><?php echo $codelist->label; ?></div>
                                    <div class="input_text">
                                        <div class="arrow"></div>
                                        <div class="hdselect">
                                            <?php echo CHtml::dropDownList($codelist->codelist, '', Codelkups::getCodelkupsDropDownOriginals($codelist->codelist), array('prompt' => 'Choose your ' . $codelist->label, 'class' => 'codelist_dropdown', $visible ? 'enabled' : 'disabled' => 'enabled')); ?>
                                        </div>
                                    </div><?php if ($visible) { ?>
                                        <div class="btnsList"><a class="editCodelkup manageCodelkup"
                                                                 href="javascript:void(0);"
                                                                 onclick="setEditMode(this, 'edit');return false;">EDIT</a>
                                        <a class="addCodelkup manageCodelkup" href="javascript:void(0);"
                                           onclick="setEditMode(this, 'add');return false;">ADD</a>
                                        <a class="deleteCodelkup red"
                                           href="<?php echo Yii::app()->createAbsoluteUrl('settings/deleteCodelkup'); ?>"
                                           onclick="deleteCodelkup(this);return false;">DELETE</a>
                                        </div><?php } ?></div>
                            <?php endif;?>
                            <div class="edit_mode"
                                 style="display:none"><?php if ($codelist->id === '8' && $codelist->id === '31' && $codelist->id === '22' && $codelist->id === '37') { ?>
                                    <div class="bg"></div><?php } else { ?>
                                    <div class="bg1"></div>    <?php } ?>

                                <div class="codelist_name input_text_desc">
                                    {Action} <?php echo $codelist->label; ?></div>
                                <div class="input_text"><?php echo CHtml::textField('codelkup', '', array('id' => 'codelkup_' . $codelist->id, 'class' => 'codelkup_input')); ?>
                                </div><?php if ($codelist->id === '8') { ?>
                                    <div class="input_text"><?php echo CHtml::textField('codelkup', '', array('class' => 'rate_input null', 'placeholder' => 'currency rate')); ?>
                                    </div><?php } ?>
                                <?php if ($codelist->id === '37') { ?>
                                    <div class="input_text"><?php echo CHtml::textField('codelkup', '', array('class' => 'rate_support null', 'placeholder' => 'Plan %')); ?>
                                    </div><?php } ?>
                                <?php if ($codelist->id === '31') { ?>
                                    <div class="input_text"><?php echo CHtml::textField('yearly_amount', '', array('class' => 'yearly_amount_input null', 'placeholder' => 'Yearly Amount')); ?>
                                    </div><?php } ?>
                                <?php if ($codelist->id === '22') { ?>
                                    <div class="input_textarea"><?php echo CHtml::textArea('template_message', '', array('class' => 'input_textarea_value')); ?>
                                    </div><?php } ?>
                                <div style="display:none;" class="error red"></div>
                                <?php if ($codelist->id === '8') { ?>
                                    <div class="btnsList">
                                        <a class="save red" href="javascript:void(0);"
                                           onclick="saveCodelkupCurrency(this);return false;">SAVE</a><a class="cancel"
                                                                                                         href="javascript:void(0);"
                                                                                                         onclick="setNormalMode(this);return false;">CANCEL</a>
                                    </div><?php } elseif ($codelist->id === '37') { ?>
                                    <div class="btnsList">
                                        <a class="save red" href="javascript:void(0);"
                                           onclick="saveCodelkupSupport(this);return false;">SAVE</a><a class="cancel"
                                                                                                        href="javascript:void(0);"
                                                                                                        onclick="setNormalMode(this);return false;">CANCEL</a>
                                    </div><?php } elseif ($codelist->id === '22') { ?>
                                    <div class="btnsList"><a class="save red" href="javascript:void(0);"
                                                             onclick="saveCodelkupTemplate(this);return false;">SAVE</a>
                                        <a class="cancel" href="javascript:void(0);"
                                           onclick="setNormalMode(this);return false;">CANCEL</a></div>
                                <?php } elseif ($codelist->id === '31') { ?>
                                    <div class="btnsList"><a class="save red" href="javascript:void(0);"
                                                             onclick="saveYearlySales(this);return false;">SAVE</a>
                                        <a class="cancel" href="javascript:void(0);"
                                           onclick="setNormalMode(this);return false;">CANCEL</a></div>
                                <?php } else {
                                    ?>
                                    <div class="btnsList">
                                        <a class="save red" href="javascript:void(0);"
                                           onclick="saveCodelkup(this);return false;">SAVE</a><a class="cancel"
                                                                                                 href="javascript:void(0);"
                                                                                                 onclick="setNormalMode(this);return false;">CANCEL</a>
                                    </div><?php } ?></div>
                        </div>
                    <?php } ?>    </div>
                <div class="ftr"></div>
            </div>    <?php } ?><?php } ?></div><br clear="all"/>
<script type="text/javascript">
    $(function () {
        $('.dashitem').find('.head').mousemove(
            function () {
                $("#sortable").sortable({cancel: ".dashitem>.contenu"});
                $('#sortable').sortable({
                    items: '.dashitem',
                    opacity: 0.5,
                    start: function (evt, ui) {
                        var link = ui.item.find('.drag');
                        link.data('click-event', link.attr('onclick'));
                        link.attr('onclick', '');
                        link.addClass('select');
                    },
                    stop: function (evt, ui) {
                        $('.drag').removeClass('select');
                        var link = ui.item.find('.drag');

                    }
                });
            });
        <?php if($len_countryes_perdier !== 0):?>
            $('#codelists_8').append(`
                            <div id="save_country_perdiem" class="codelist textBox inline-block last">
                                <div class="normal_mode">
                                    <div class="codelist_name input_text_desc">SAVE COUNTRY/PER DIEM</div>
                                    <div class="input_text">
                                        <div class="hdselect">
                                            <select data-country="" data-prediem="" class="codelist_dropdown" name="save_country_perdiem_select" id="save_country_perdiem_select">
                                                <option value=""></option>
                                                <?php foreach ($all_countries_perdiem as $item):?>
                                                    <option onclick="$('#save_country_perdiem_select').attr('data-country','<?=$item->getRelated('countryData')->country_name?>').attr('data-prediem','<?=$item->per_diem?>')" value="<?=$item->id?>"><?=$item->getRelated('countryData')->country_name . ' - ' . $item->per_diem?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="btnsList" style="top: 5px;">
                                        <a class="editCodelkup manageCodelkup"
                                            href="javascript:void(0);"
                                            onclick="editPrediem();return false;">EDIT</a>
                                        <a class="deleteCodelkup red"
                                       href="<?php echo Yii::app()->createAbsoluteUrl('settings/DeleteCountryPerdiem'); ?>"
                                       onclick="deleteCountryPrediem(this);return false;">DELETE</a>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_mode" style="display: none;" id="save_country_perdiem_edit">
                                <div class="bg1" style="display: block;"></div>
                                <div class="codelist_name input_text_desc" style="padding-top: 10px;">Edit COUNTRY/PER DIEM</div>
                                <div class="" style="display: flex;padding: 10px 0;margin-right: 27px;">
                                    <input data-prediem-id="" id="edit_country" readonly="readonly" style="padding: 1px 5px;width: 50%;height: 25px;border: 2px solid #ccc;border-radius: 5px 0 0 5px;"  class="codelkup_input edit_input" type="text" value="df" name="">
                                    <input id="edit_prediem" style="padding: 1px 5px;width: 50%;height: 25px;border: 2px solid #ccc;border-radius: 0 5px 5px 0;z-index: 1000;" class="codelkup_input edit_input" type="text" value="df" name="">
                                </div>                                                                                                                                <div style="display:none;" class="error red"></div>
                                <div class="btnsList">
                                     <a class="save red" href="javascript:void(0);" onclick="saveChangeCountryPerdier(this);return false;">SAVE</a>
                                     <a class="cancel" href="javascript:void(0);" onclick="$('#save_country_perdiem_edit').toggle();$('#save_country_perdiem').toggle();return false;">CANCEL</a>
                                </div>
                            </div>
            `);
        <?php endif;?>
    });

    function editPrediem() {
        const val = $('#save_country_perdiem_select').val();
        const country = $('#save_country_perdiem_select').attr('data-country');
        const prediem = $('#save_country_perdiem_select').attr('data-prediem');

        if (country.length > 0){
            $('#edit_country').val(country).attr('data-prediem-id',val);
            $('#edit_prediem').val(prediem);

            $('#save_country_perdiem_edit').toggle();
            $('#save_country_perdiem').toggle();
        }
    }

    function saveChangeCountryPerdier() {
        const id = $('#edit_country').attr('data-prediem-id');
        const val = $('#edit_prediem').val();

        $.post(`<?=Yii::app()->createAbsoluteUrl('settings/EditCountryPerdiem'); ?>`,{id,val},function (data) {
            if (data != 0){
                $('#save_country_perdiem_select').html(data)
                $('#save_country_perdiem_edit').toggle();
                $('#save_country_perdiem').toggle();
            } else{
                alert('Error!')
            }
        });
    }

    function saveCountryPerdier(id) {
        const country_id = $('#country_id_'+id).val();
        const perdiem = $('#perdier_'+id).val();
        if(country_id.length !== 0 && perdiem.length !== 0){
            $.post(configJs.urls.baseUrl + '/settings/SaveCountryPerdiem',{country_id, perdiem},function (data) {
                $('#save_country_perdiem_select').html(data)
            });
        }else {
            alert('Input don`t be blank')
        }
    }

    function deleteCountryPrediem(element) {
        const id = $('#save_country_perdiem_select').val();
        if (id.length > 0){
            var $this = $(element);
            var url = $this.attr('href');

            if (confirm('DELETE')){
                $.get(`${url}/${id}`,function (data) {
                    if (data != 0) $('#save_country_perdiem_select').html(data);
                    else alert('Error')
                });
            }
        }
    }

    function setNormalMode(element) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.codelist').find('.normal_mode');
        var editModeDiv = $this.parents('.edit_mode');
        editModeDiv.find('.codelist_name').html(editModeDiv.find('.codelist_name').html().replace('Add', '{Action}'));
        editModeDiv.find('.codelist_name').html(editModeDiv.find('.codelist_name').html().replace('Edit', '{Action}'));
        editModeDiv.hide();
        normalModeDiv.show();
    }

    function setEditMode(element, mode) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.normal_mode');
        var editModeDiv = $this.parents('.codelist').find('.edit_mode');
        var dropDown = normalModeDiv.find('.codelist_dropdown');
        var doEdit = false;
        if (mode == 'add') {
            doEdit = true;
            editModeDiv.find('.codelist_name').html(editModeDiv.find('.codelist_name').html().replace('{Action}', 'Add'));
            editModeDiv.find('input.codelkup_input').val('');
            editModeDiv.find('input.rate_input').val('');
            editModeDiv.find('input.yearly_amount_input').val('');
            editModeDiv.find('input.codelkup_input').addClass('add_input');
            editModeDiv.find('input.codelkup_input').removeClass('edit_input');
        } else {
            if (dropDown.val() != '') {
                doEdit = true;
                editModeDiv.find('.bg1').show();
                editModeDiv.find('.bg').show();
                editModeDiv.find('.codelist_name').html(editModeDiv.find('.codelist_name').html().replace('{Action}', 'Edit'));
                editModeDiv.find('input.codelkup_input').val($(dropDown).find(':selected').text());
                editModeDiv.find('input.codelkup_input').addClass('edit_input');
                editModeDiv.find('input.codelkup_input').removeClass('add_input');
                $.ajax({
                    type: "POST",
                    data: dropDown.val(),
                    dataType: "json",
                    url: configJs.urls.baseUrl + '/projects/GetCurrencyRate/' + dropDown.val(),
                    success: function (data) {
                        if (data) {
                            console.log(data);
                            editModeDiv.find('input.rate_input').val(data.rate);
                            editModeDiv.find('input.rate_support').val(data.supportrate);
                            editModeDiv.find('textarea.input_textarea_value').val(data.template);
                            editModeDiv.find('input.yearly_amount_input').val(data.yearlyamount);
                        }
                    }
                });
            }
        }
        if (doEdit) {
            normalModeDiv.hide();
            editModeDiv.show();
        } else {
            editModeDiv.find('.bg1').hide();
        }
    }

    function saveCodelkup(element) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.codelist').find('.normal_mode');
        var editModeDiv = $this.parents('.edit_mode');
        var dropDown = normalModeDiv.find('.codelist_dropdown');
        var input = editModeDiv.find('input.codelkup_input');
        if (input.val() != '') {
            var data;
            var codelistId = parseInt(input.attr('id').split('_').slice(-1)[0]);
            if (input.hasClass('add_input')) {
                data = {'Codelkups[id_codelist]': codelistId, 'Codelkups[codelkup]': input.val()};
            } else {
                data = {'Codelkups[id]': dropDown.val(), 'Codelkups[codelkup]': input.val()};
            }
            $.ajax({
                type: "POST", data: data, dataType: "json", url: configJs.urls.baseUrl + '/settings/manageCodelkup',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        if (data.status == "success") {
                            dropDown.replaceWith(data.dropDown);
                            editModeDiv.hide();
                            normalModeDiv.show();
                        } else {
                            if (data.status == "failure") {
                                if (data.errors) {
                                    var errors = '';
                                    $.each(data.errors, function (id, message) {
                                        console.log(id);
                                        console.log(message);
                                        errors += message + '<br/>';
                                    });
                                    $this.parent().siblings('.error').html(errors);
                                } else {
                                    if (data.error) {
                                        var action_buttons = {
                                            "Ok": {
                                                click: function () {
                                                    $(this).dialog("close");
                                                },
                                                class: 'ok_button'
                                            }
                                        }
                                        custom_alert('ERROR MESSAGE', data.error, action_buttons);
                                    }
                                }
                            }
                        }
                    }
                },
                error: function () {
                    var action_buttons = {
                        "Ok": {
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: 'ok_button'
                        }
                    }
                    custom_alert('ERROR MESSAGE', 'The action couldn\'t be completed due to an error.', action_buttons);
                }
            });
        }
    }

    function saveCodelkupSupport(element) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.codelist').find('.normal_mode');
        var editModeDiv = $this.parents('.edit_mode');
        var dropDown = normalModeDiv.find('.codelist_dropdown');
        var input = editModeDiv.find('input.codelkup_input');
        var inputRate = editModeDiv.find('input.rate_support');
        if (input.val() != '') {
            var data;
            var codelistId = parseInt(input.attr('id').split('_').slice(-1)[0]);
            if (input.hasClass('add_input')) {
                data = {
                    'Codelkups[id_codelist]': codelistId,
                    'Codelkups[codelkup]': input.val(),
                    'SupportRate[rate]': inputRate.val()
                };
            } else {
                data = {
                    'Codelkups[id]': dropDown.val(),
                    'Codelkups[codelkup]': input.val(),
                    'SupportRate[rate]': inputRate.val()
                };
            }
            $.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: configJs.urls.baseUrl + '/settings/manageCodelkupSupportPlan',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        if (data.status == "success") {
                            dropDown.replaceWith(data.dropDown);
                            editModeDiv.hide();
                            normalModeDiv.show();
                        } else {
                            if (data.status == "failure") {
                                if (data.errors) {
                                    var errors = '';
                                    $.each(data.errors, function (id, message) {
                                        console.log(id);
                                        console.log(message);
                                        errors += message + '<br/>';
                                    });
                                    $this.parent().siblings('.error').html(errors);
                                } else {
                                    if (data.error) {
                                        var action_buttons = {
                                            "Ok": {
                                                click: function () {
                                                    $(this).dialog("close");
                                                },
                                                class: 'ok_button'
                                            }
                                        }
                                        custom_alert('ERROR MESSAGE', data.error, action_buttons);
                                    }
                                }
                            }
                        }
                    }
                },
                error: function () {
                    var action_buttons = {
                        "Ok": {
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: 'ok_button'
                        }
                    }
                    custom_alert('ERROR MESSAGE', 'The action couldn\'t be completed due to an error.', action_buttons);
                }
            });
        }
    }

    function saveCodelkupCurrency(element) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.codelist').find('.normal_mode');
        var editModeDiv = $this.parents('.edit_mode');
        var dropDown = normalModeDiv.find('.codelist_dropdown');
        var input = editModeDiv.find('input.codelkup_input');
        var inputRate = editModeDiv.find('input.rate_input');
        if (input.val() != '') {
            var data;
            var codelistId = parseInt(input.attr('id').split('_').slice(-1)[0]);
            if (input.hasClass('add_input')) {
                data = {
                    'Codelkups[id_codelist]': codelistId,
                    'Codelkups[codelkup]': input.val(),
                    'CurrencyRate[rate]': inputRate.val()
                };
            } else {
                data = {
                    'Codelkups[id]': dropDown.val(),
                    'Codelkups[codelkup]': input.val(),
                    'CurrencyRate[rate]': inputRate.val()
                };
            }
            $.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: configJs.urls.baseUrl + '/settings/manageCodelkupCurrency',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        if (data.status == "success") {
                            dropDown.replaceWith(data.dropDown);
                            editModeDiv.hide();
                            normalModeDiv.show();
                        } else {
                            if (data.status == "failure") {
                                if (data.errors) {
                                    var errors = '';
                                    $.each(data.errors, function (id, message) {
                                        console.log(id);
                                        console.log(message);
                                        errors += message + '<br/>';
                                    });
                                    $this.parent().siblings('.error').html(errors);
                                } else {
                                    if (data.error) {
                                        var action_buttons = {
                                            "Ok": {
                                                click: function () {
                                                    $(this).dialog("close");
                                                },
                                                class: 'ok_button'
                                            }
                                        }
                                        custom_alert('ERROR MESSAGE', data.error, action_buttons);
                                    }
                                }
                            }
                        }
                    }
                },
                error: function () {
                    var action_buttons = {
                        "Ok": {
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: 'ok_button'
                        }
                    }
                    custom_alert('ERROR MESSAGE', 'The action couldn\'t be completed due to an error.', action_buttons);
                }
            });
        }
    }

    function saveYearlySales(element) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.codelist').find('.normal_mode');
        var editModeDiv = $this.parents('.edit_mode');
        var dropDown = normalModeDiv.find('.codelist_dropdown');
        var input = editModeDiv.find('input.codelkup_input');
        var inputAmount = editModeDiv.find('input.yearly_amount_input');
        if (input.val() != '') {
            var data;
            var codelistId = parseInt(input.attr('id').split('_').slice(-1)[0]);
            if (input.hasClass('add_input')) {
                data = {
                    'Codelkups[id_codelist]': codelistId,
                    'Codelkups[codelkup]': input.val(),
                    'Amount[yearly_amount]': inputAmount.val()
                };
            } else {
                data = {
                    'Codelkups[id_codelist]': '31',
                    'Codelkups[codelkup]': input.val(),
                    'Amount[yearly_amount]': inputAmount.val()
                };
            }
            $.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: configJs.urls.baseUrl + '/settings/manageCodelkupYearlySales',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        if (data.status == "success") {
                            dropDown.replaceWith(data.dropDown);
                            editModeDiv.hide();
                            normalModeDiv.show();
                        } else {
                            if (data.status == "failure") {
                                if (data.errors) {
                                    var errors = '';
                                    $.each(data.errors, function (id, message) {
                                        console.log(id);
                                        console.log(message);
                                        errors += message + '<br/>';
                                    });
                                    $this.parent().siblings('.error').html(errors);
                                } else {
                                    if (data.error) {
                                        var action_buttons = {
                                            "Ok": {
                                                click: function () {
                                                    $(this).dialog("close");
                                                },
                                                class: 'ok_button'
                                            }
                                        }
                                        custom_alert('ERROR MESSAGE', data.error, action_buttons);
                                    }
                                }
                            }
                        }
                    }
                },
                error: function () {
                    var action_buttons = {
                        "Ok": {
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: 'ok_button'
                        }
                    }
                    custom_alert('ERROR MESSAGE', 'The action couldn\'t be completed due to an error.', action_buttons);
                }
            });
        }
    }

    function saveCodelkupTemplate(element) {
        var $this = $(element);
        var normalModeDiv = $this.parents('.codelist').find('.normal_mode');
        var editModeDiv = $this.parents('.edit_mode');
        var dropDown = normalModeDiv.find('.codelist_dropdown');
        var input = editModeDiv.find('input.codelkup_input');
        var inputTemplate = editModeDiv.find('textarea.input_textarea_value');
        if (input.val() != '') {
            var data;
            var codelistId = parseInt(input.attr('id').split('_').slice(-1)[0]);
            if (input.hasClass('add_input')) {
                data = {
                    'Codelkups[id_codelist]': codelistId,
                    'Codelkups[codelkup]': input.val(),
                    'Template[template_message]': inputTemplate.val()
                };
            } else {
                data = {
                    'Codelkups[id_codelist]': '22',
                    'Codelkups[codelkup]': input.val(),
                    'Template[template_message]': inputTemplate.val()
                };
            }
            $.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: configJs.urls.baseUrl + '/settings/manageCodelkupTemplate',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        if (data.status == "success") {
                            dropDown.replaceWith(data.dropDown);
                            editModeDiv.hide();
                            normalModeDiv.show();
                        } else {
                            if (data.status == "failure") {
                                if (data.errors) {
                                    var errors = '';
                                    $.each(data.errors, function (id, message) {
                                        console.log(id);
                                        console.log(message);
                                        errors += message + '<br/>';
                                    });
                                    $this.parent().siblings('.error').html(errors);
                                } else {
                                    if (data.error) {
                                        var action_buttons = {
                                            "Ok": {
                                                click: function () {
                                                    $(this).dialog("close");
                                                },
                                                class: 'ok_button'
                                            }
                                        }
                                        custom_alert('ERROR MESSAGE', data.error, action_buttons);
                                    }
                                }
                            }
                        }
                    }
                },
                error: function () {
                    var action_buttons = {
                        "Ok": {
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: 'ok_button'
                        }
                    }
                    custom_alert('ERROR MESSAGE', 'The action couldn\'t be completed due to an error.', action_buttons);
                }
            });
        }
    }

    function deleteCodelkup(element) {
        var $this = $(element);
        var url = $this.attr('href');
        var dropdown = $this.parents('.normal_mode').find('.codelist_dropdown');
        var id = dropdown.val();
        if (id != '') {
            $.ajax({
                type: "POST", data: {'id': id}, dataType: "json", url: url,
                success: function (data) {
                    if (data) {
                        console.log(data);
                        if (data.status == "success") {
                            dropdown.val('', true);
                            dropdown.find('option[value="' + id + '"]').remove();
                        } else {
                            if (data.status == "failure") {
                                if (data.error) {
                                    var action_buttons = {
                                        "Ok": {
                                            click: function () {
                                                $(this).dialog("close");
                                            },
                                            class: 'ok_button'
                                        }
                                    }
                                    custom_alert('ERROR MESSAGE', data.error, action_buttons);
                                }
                            }
                        }
                    }
                },
                error: function () {
                    var action_buttons = {
                        "Ok": {
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: 'ok_button'
                        }
                    }
                    custom_alert('ERROR MESSAGE', 'The item couldn\'t be deleted because it is used in this module.', action_buttons);
                }
            });
        }
    }
</script>