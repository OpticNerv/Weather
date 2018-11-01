<div class="container">
<div class="col-sm-6" id="userDetails" style="overflow-y:scroll;height:800px;display:none;border:1px solid #f5f5f5; border-radius:5px;"></div>

<div class="col-sm-6">
<div id="statsContainer"  style="float:left;width:100%;height:30px;">
	<select id="citySelector" class="form-control"></select>
	<input class="form-control"  type="text" id="startDate" /><input class="form-control" type="text" id="endDate" />
	<label for="forecast"><input class="form-control" id="forecast" type="checkbox" /><?php echo $this->lang->line("show_forecast");?></label><input class="form-control" type="button" value="Show meh!" onclick="showWeatherStats($('#citySelector').val(),$('#startDate').val(), $('#endDate').val(), $('#forecast').is(':checked'));" />
</div>	

<div id="weatherResultContainer" style="float:left;width:100%;height:300px;">
	<canvas id="canvas"></canvas>
</div>
</div>
</div>

<script type="text/javascript">
$( document ).ready(function() 
{
	$("#startDate").datepicker();
	$("#endDate").datepicker();
	$("#startDate").datepicker("option", "dateFormat", "dd-mm-yy");
	$("#endDate").datepicker("option", "dateFormat", "dd-mm-yy");
	
	$("#startDate").datepicker('setDate', '<?php echo date("d-m-Y",time());?>');
	$("#endDate").datepicker('setDate', '<?php echo date("d-m-Y",time());?>');	
	
	getUserProfileData();
});

/**
* JS Function getUserProfileData, retrieves users profile data
* and selected cities via AJAX request
* @name: getUserProfileData
**/
function getUserProfileData()
{
	$("#canvas").html("");
	$('#citySelector').find('option').remove();
	
	$.ajax(
	{	
		url: "<?php echo $this->config->base_url();?>profile/getUserProfileData/", 
		type: "GET",
		dataType :"JSON",
		success: function(result)
		{
			try
			{
				if(result.success)
				{
					var user_view = "<h2>"+result.userData.name+"</h2>";
					user_view += '\r\n<p id="noCities" style="float:left;width:100%;" class="alert alert-danger"><?php echo $this->lang->line("no_cities_selected");?></p>';
					
					if(typeof result.allCities!="undefined" && result.allCities!=null && result.allCities.length>0)
					{
						user_view += '\r\n<h3><?php echo $this->lang->line('selected_cities');?></h3>';
						user_view += '\r\n<input type="text" id="searchText" placeholder="<?php echo $this->lang->line('search_cities');?>">  <a href="#/" onclick="searchCities();">🔎</a>';
						user_view += '<form class="well" id="updateUserForm" enctype="multipart/form-data" style="max-height: 300px;overflow: auto;">\r\n';
						user_view += '\r\n<input type="hidden" name="userId" value="'+result.userData.id+'" />';
						user_view += '<ul class="list-group checked-list-box">';
						$.each(result.allCities, function(key,value) {
							user_view += '\r\n<li id="city-'+value.id+'" class="list-group-item">\r\n<div class="checkbox">\r\n<label><input id="cityCheckbox-'+value.id+'" type="checkbox" name="selectedCities[]" value="'+value.id+'">'+value.city_name+'</label>\r\n</div>\r\n</li>';
						});
						user_view +='</ul>\r\n</form>';
						user_view += '\r\n<input class="btn btn-primary btn-lg" style="margin-top:10px;margin-right:10px;" type="button" onclick="$(\'#updateUserForm\').submit();" value="<?php echo $this->lang->line('save_changes');?>" />';
						user_view +='<input class="btn btn-secondary btn-lg" style="margin-top:10px;" type="button" value="<?php echo $this->lang->line('cancel_changes');?>" onclick="clearUserDetails();" />';
					}
					else
						user_view = '<div class="alert alert-danger"><?php $this->lang->line('no_cities_error');?></div>';
					
					
					$("#userDetails").html(user_view);
					
					if(typeof result.userCities!="undefined" && result.userCities!=null && result.userCities.length>0)
					{
						$.each(result.userCities, function(key,value) {
							$("#cityCheckbox-"+value.id).prop("checked",true);
							
							$('#citySelector').append($('<option>', {
								value: value.id,
								text: value.city_name
							}));
						});
					}
					
					if ($('#citySelector option').length == 0) 
					{
						$('#statsContainer').hide();
						$('#noCities').show();
					}
					else
					{
						$("#statsContainer").show();
						$('#noCities').hide();
					}
					
					$("#userDetails").show();
				}
				else
					$(".result").text("No match found!");					
			}
			catch(JSONError) { console.log(JSONError); }
		}
	});
}

/**
* We intercept the form submit and submit it 
* async using AJAX
**/
$(document).on('submit', '#updateUserForm', function(e)
{
	e.preventDefault();
	
	$.ajax(
	{	
		url: "<?php echo $this->config->base_url();?>profile/updateUserProfile/", 
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		type: "POST",
		dataType:"JSON",
		success: function(result)
		{
			try
			{
				if(result.success)
				{
					getUserProfileData();
					alert("<?php echo $this->lang->line('userprofile_updated');?>");
				}
				else
					alert("<?php echo $this->lang->line('userprofile_errorupdating');?>");
			}
			catch(JSONError) { console.log(JSONError); }
		}
	});
});

/**
* JS Function searchCities, search for a cities by name
* converts the input string to lower case, then capitalizes the first letter
* @name: searchCities
**/
function searchCities(searchText)
{
	searchText = $("#searchText").val().toLowerCase();
	
	if(searchText!=null && searchText.length>0)
	{
		searchText = searchText.substring(0,1).toUpperCase()+searchText.substring(1);
		
		if($("label:contains('"+searchText+"')").length)
		{
			$("#updateUserForm").animate({
				scrollTop: $("label:contains('"+searchText+"')").offset().top-240
			}, 500);
		}
	}
}
</script>

