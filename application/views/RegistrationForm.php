<?php $this->load->view("Header"); ?>
<div class="container">

<div class="col-sm-6 col-sm-offset-3">

	<h1><span class="fa fa-sign-in"></span><?php echo $this->lang->line("loginForm_title");?></h1>

	<!-- show any messages that come back with authentication -->
	<?php if(isset($errorMessage) && strlen($errorMessage)>0) { ?>
		<div class="alert alert-danger"><?php echo $errorMessage;?></div>
	<?php } ?>

	<!-- REGISTRATION FORM -->
	<form action="<?php echo $this->config->base_url();?>registerUser" method="post">
		<div class="form-group">
		<p><?php echo $this->lang->line("consent_text");?></p>
		<input type="checkbox" value=1 name="consent" /> <?php echo $this->lang->line("consent_desc");?><br /><br />
		<input type="submit" value="<?php echo $this->lang->line("consent_btn_text");?>"/>
		</div>
	</form>
	<hr>
</div>
<?php $this->load->view("Footer"); ?>