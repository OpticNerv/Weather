<?php $this->load->view("Header"); ?>
<div class="container">

<div class="col-sm-6 col-sm-offset-3">

	<h1><span class="fa fa-sign-in"></span><?php echo $this->lang->line("loginForm_title");?></h1>

	<!-- show any messages that come back with authentication -->
	<?php if(isset($errorMessage) && strlen($errorMessage)>0) { ?>
		<div class="alert alert-danger"><?php echo $errorMessage;?></div>
	<?php } ?>

	<?php if(!isset($errorMessage)) { ?>
	<!-- REGISTRATION FORM -->
	<form action="<?php echo $this->config->base_url();?>register_user" method="post">
		<div class="form-group">
		<input type="checkbox" value=1 name="consent" /> Agree to our super terms!
		<input type="submit" value="We are great!"/>
		</div>
	</form>
	<?php } ?>
	<hr>
</div>
<?php $this->load->view("Footer"); ?>