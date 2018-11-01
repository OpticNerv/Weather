<style>
.well:hover { cursor:pointer; }
</style>

<script type="text/javascript">
$( document ).ready(function() 
{
	$("#startDate").datepicker();
	$("#endDate").datepicker();
	$("#startDate").datepicker("option", "dateFormat", "dd-mm-yy");
	$("#endDate").datepicker("option", "dateFormat", "dd-mm-yy");
	
	$("#startDate").datepicker('setDate', '<?php echo date("d-m-Y",time());?>');
	$("#endDate").datepicker('setDate', '<?php echo date("d-m-Y",time());?>');	
});

/**
* JS Function getUserProfileData, retrieves the users profile info
* retrieves the users selected cities (if he has any) and marks them with checkboxes
* @name: getUserProfileData
**/
function getUserProfileData(userId)
{
	if(typeof userId!="undefined" && userId!=null && userId>0)
	{
		clearUserDetails()
		
		$.ajax(
		{	
			url: "<?php echo $this->config->base_url();?>profile/getUserProfileData/", 
			data: { "userId":userId },
			type: "GET",
			dataType :"JSON",
			success: function(result)
			{
				try
				{
					if(result.success)
					{
						var user_view = "<h2>"+result.userData.name+"</h2>";
						
						if(typeof result.allCities!="undefined" && result.allCities!=null && result.allCities.length>0)
						{
							user_view += '<h3><?php echo $this->lang->line('selected_cities');?></h3>';
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
							});
						}
						
						
						$("#userCard-"+userId).css("border","2px solid red");
						$("#userDetails").show();
						
					}
					else
						$(".result").text("No match found!");					
				}
				catch(JSONError) { console.log(JSONError); }
			}
		});
	}
}

/**
* JS Function clearUserDetails, helper function 
* for hiding users profile container and removing border
* when the user is no longer selected
* @name: clearUserDetails
**/
function clearUserDetails()
{
	$('[id*="userCard-"]').css("border","initial");
	$("#userDetails").hide();
	$("#userDetails").html('');
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
					alert("<?php echo $this->lang->line('userprofile_updated');?>");
				else
					alert("<?php echo $this->lang->line('userprofile_errorupdating');?>");
			}
			catch(JSONError) { console.log(JSONError); }
		}
	});
});
</script>

<div class="container">

<div class="col-sm-3" >
<?php if($users && is_array($users) && count($users)>0){ foreach($users as $user) {?>
	<div class="well" id="userCard-<?php echo $user->id;?>" onclick="getUserProfileData(<?php echo $user->id;?>);">
		<?php if(isset($user->user_image) && filter_var($user->user_image, FILTER_VALIDATE_URL)) { ?><img style="height:100px;" src="<?php echo $user->user_image;?>" /><?php } ?>
		<h3><span class="fa fa-user" style="margin-right:10px;"></span><?php echo $user->name;?><?php if(!(bool)$user->is_active) {?><span style="color:red;margin-left:10px;" class="glyphicon glyphicon-fire" title="<?php echo $this->lang->line('deactivated_user');?>"></span><?php } ?></h3>
			<p><?php echo date("d.m.Y",$user->reg_time);?></p>
	</div>
	
<?php }}?>

</div>


<div class="col-sm-3" id="userDetails" style="overflow-y:scroll;height:800px;display:none;border:1px solid #f5f5f5; border-radius:5px;">

</div>


<div class="col-sm-6">
	
	<?php if($allCities) { ?> 
	<div id="statsContainer"  style="float:left;width:100%;height:30px;">
		<select id="citySelector" class="form-control">
		<?php foreach($allCities as $city) { ?>
		<option value="<?php echo $city->id;?>"><?php echo $city->city_name;?></option>
		<?php } ?>
		</select>
		<input class="form-control"  type="text" id="startDate" /><input class="form-control" type="text" id="endDate" />
		<label for="forecast"><input class="form-control" id="forecast" type="checkbox" /><?php echo $this->lang->line("show_forecast");?></label><input class="form-control" type="button" value="Show meh!" onclick="showWeatherStats($('#citySelector').val(),$('#startDate').val(), $('#endDate').val(), $('#forecast').is(':checked'));" />
	</div>	

	<div id="weatherResultContainer" style="float:left;width:100%;height:300px;">
		<canvas id="canvas"></canvas>
	</div>
	
	<?php } ?>
	</div>
</div>
</div>
	
	

<?php $this->load->view("Footer"); ?>

