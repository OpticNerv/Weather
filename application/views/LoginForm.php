<div class="container">

<div class="col-sm-6 col-sm-offset-3">

	<h1><span class="fa fa-sign-in"></span><?php echo $this->lang->line("loginForm_title");?></h1>

	<!-- show any messages that come back with authentication -->
	<?php if(isset($errorMessage) && strlen($errorMessage)>0) { ?>
		<div class="alert alert-danger"><?php echo $errorMessage;?></div>
	<?php } ?>

	<?php if(!isset($errorMessage)) { ?>
	<!-- LOGIN FORM -->
	<form action="/login" method="post">
		<div class="form-group">
		<p style="text-align:center;"><br /><a href="<?php echo $this->config->base_url();?>google_login" class="btn btn-danger"><span class="fa fa-google-plus"></span> Google</a></p>
		</div>
	</form>
	<?php } ?>
	
	<hr>
</div>