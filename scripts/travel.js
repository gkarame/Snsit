/**
* on client select, activate and refresh projects list in the travel page
*/
function refreshProjectListsTravel()
{
	var customer = $('#Travel_id_customer').val();
	if(customer)
	{
		$('#Travel_id_project').removeAttr('disabled');
		$.ajax({
 			type: "GET",
 			data: {id : customer},					
 			url: getProjectsByClientUrl, 
 			dataType: "json",
 			success: function(data) {
			  	if (data) {
			  		
			  		var arr = [];

			  		for (var key in data) {
			  		    if (data.hasOwnProperty(key)) {
			  		        arr.push({'id': key, 'label': data[key]});
			  		    }
			  		}
			  		
			  		 var sorted = arr.sort(function (a, b) {
		    				if (a.label > b.label) {
		      					return 1;
		      				}
		    				if (a.label < b.label) {
		     					 return -1;
		     				}

		    				return 0;
					 });
			  		
			  		var selectOptions = '<option value="">'+''+'</option>';
			  		$.each(sorted,function(index, val){
				        selectOptions += '<option value="'+val.id+'">'+val.label+'</option>';
				    });
				    $('#Travel_id_project').html(selectOptions);
			  	}
	  		}
		});
	}
	else
	{
		$('#Travel_id_project').attr('disabled', 'disabled');
	}
}

/**
 * On change the project from the select
 * @param element
 */
function refreshBillableBasedOnProjectEA(element)
{
	$element = $(element);
	var id_project = $element.val();
	
	$.ajax({
		url: testProjectActualsExpensesUrl,
		data: {id_project: id_project},
		type: 'post',
		dataType: 'json',
		success: function(res)
		{
			if (res.code == 200)
			{
				$('select[name="Travel[billable]"] > option').removeAttr('selected');
				
				if (res.type == 'true')
					$('select[name="Travel[billable]"] > option[value="yes"]').attr('selected', 'selected');
				else
					$('select[name="Travel[billable]"] > option[value="no"]').attr('selected', 'selected');
			}
		}
	});
}