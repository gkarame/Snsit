<body bgcolor="#00ff00" ><?php $info = Yii::app()->db->createCommand("select u.firstname , u.lastname , upd.extension , upd.mobile  from users u ,user_personal_details upd  where u.id =upd.id_user and u.active='1' and upd.branch='31' order by firstname asc")->queryAll(); ?>
<div id="customers-grid" class="gridcustom-view"><table class="items"><thead><tr>
<th id="customers-grid_c0"><a class="sort-link">NAME</a></th><th id="customers-grid_c0"><a class="sort-link">EXTENSION</a></th> <th id="customers-grid_c0"><a class="sort-link">MOBILE</a></th></tr>
<tbody><?php 	foreach ($info as $key => $inf){ ?> 
<tr style="height:35px;"><td style="height:35px;"><?php echo $inf['firstname']." ". $inf['lastname']; ?></td> <td style="height:35px;"><?php echo $inf['extension'];?></td> <td style="height:35px;"><?php echo $inf ['mobile']; ?> </td></tr>
<?php }?> </tbody></table>
<script>
function checkStatus(){}
</script></div>		<br clear="all" />	</div>	</div>	</div>	<div class="popup_list" style="display:none"></div>
	<div style="display:none" id="confirm_dialog" class="confirm dialog"><h1 class="confirm_title"></h1><div class="confirm_content"></div></div>
<script type="text/javascript" src="/snsme/assets/7690d8a7/gridview/jquery.yiigridview.js"></script>
<script type="text/javascript">
jQuery(function($) {
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('customers-grid', {
		data: $(this).serialize()
	});
	return false;
});
jQuery('#Customers_contact_name').autocomplete({'minLength':'0','showAnim':'fold','source':[{'label':'test ing','id':'1'}]});
jQuery(document).on('click','#customers-grid a.delete',function() {
	var th = this,
	afterDelete = function(){};
	$("#confirm_dialog .confirm_title").html("DELETE MESSAGE");
	$("#confirm_dialog .confirm_content").html("Are you sure you want to delete this item?");
	$("#confirm_dialog").dialog({
        resizable: false,
        draggable: true,
        closeOnEscape: true,
        width: 'auto',
        buttons: {
	        "Yes": {
	        	class: "yes_button",
		        click: function() 
		        {
		            $( this ).dialog( "close" );
					jQuery('#customers-grid').yiiGridView('update', {
						type: 'POST',
						url: jQuery(th).attr('href'),
						success: function(data) {
							jQuery('#customers-grid').yiiGridView('update');
							afterDelete(th, true, data);
						},
						error: function(XHR) {
							return afterDelete(th, false, XHR);
						}
					});
					return false;
				}
	        },
	        "No": {
				class: "no_button",
				click:function() 
			        {
			            $( this ).dialog( "close" );
			            return false;
					}
	        },
		},
        modal: true,
        width: 379,
        height: 235,
        dialogClass: 'confirm_dialog'
    });
    return false;
});
jQuery('#customers-grid').yiiGridView({'ajaxUpdate':['customers-grid'],'ajaxVar':'ajax','pagerClass':'pager','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items','selectableRows':1,'enableHistory':false,'updateSelector':'{page}, {sort}','filterSelector':'{filter}','pageVar':'Customers_page','selectionChanged':function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "/snsme/customers/view/"+idg;}});
});
</script>
</body>
