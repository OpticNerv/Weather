<?php $this->load->view("Header"); ?>
<div class="container">

<div class="col-sm-6 col-sm-offset-3">

	<h1><span class="fa fa-sign-in"></span><?php echo $this->lang->line("loginForm_title");?></h1>

	<!-- show any messages that come back with authentication -->
	<?php if(isset($errorMessage) && strlen($errorMessage)>0) { ?>
		<div class="alert alert-danger"><?php echo $errorMessage;?></div>
	<?php } ?>

	<!-- REGISTRATION FORM -->
	<form action="<?php echo $this->config->base_url();?>register_user" method="post">
		<div class="form-group">
		<p>By registering at our website, you agree and allow of your personal data (name, surname and profile picture) to be stored
		for purpose of distinguishing users at our website.</p>
		<input type="checkbox" value=1 name="consent" /> I agree and allow usage of my personal data<br /><br />
		<input type="submit" value="Register me!"/>
		</div>
	</form>
	<hr>
</div>
<?php $this->load->view("Footer"); ?>