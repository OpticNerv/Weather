<div class="container">
<center><h1><?php echo $this->lang->line("startpage_title");?></h1></center>

<!-- show any messages that come back with authentication -->
<?php if(isset($errorMessage) && strlen($errorMessage)>0) { ?>
	<div class="alert alert-danger"><?php echo $errorMessage;?></div>
<?php } else if(!isset($errorMessage)) { ?>

<?php if($allCities) { ?> 
<div style="float:left;width:100%;">
	<div style="display:block;width:50%;margin:50px auto;">
	<select id="citySelector" class="form-control" onchange="showWeatherStats(this.value,false,false,true);">
	<?php foreach($allCities as $city) { ?>
		<option value="<?php echo $city->id;?>"><?php echo $city->city_name;?></option>
	<?php } ?>
	</select>
	</div>
</div>	

<div id="weatherResultContainer">
	<div>
		<canvas id="canvas"></canvas>
	</div>	
</div>
<?php } ?>


<?php } ?>
</div>

<script type="text/javascript">
$( document ).ready(function() {
    showWeatherStats($("#citySelector").val(),false,false,true);
});
</script>
