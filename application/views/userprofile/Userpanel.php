<h1>Welcome <?php echo $this->session->userdata("name")."!";?></h1>

<?php if($userCities) { ?> 
<div style="float:left;width:100%;height:30px;">
	<div style="display:block;float:left;">
	<select id="citySelector" class="form-control">
	<?php foreach($userCities as $city) { ?>
		<option value="<?php echo $city->id;?>"><?php echo $city->city_name;?></option>
	<?php } ?>
	</select>
	</div>
	<input style="float:left;height:30px;margin-left:5px;margin-right:5px;" type="text" id="startDate" /><input style="float:left;height:30px;margin-left:5px;margin-right:5px;" type="text" id="endDate" />
	<input style="float:left;height:30px;" type="checkbox" /><input style="float:left;height:30px;" type="button" value="Show meh!" onclick="showWeatherStats($('#citySelector').val(),$('#startDate').val(), $('#endDate').val(), $('input[type=checkbox]').is(':checked'));" />
</div>	

<div id="weatherResultContainer" style="float:left;width:100%;background-color:pink;height:300px;">

</div>
<?php } ?>


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
</script>

