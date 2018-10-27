<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php if(isset($seoTitle)) echo $seoTitle;?></title>
  <meta name="description" content="<?php if(isset($seoDescription)) echo $seoDescription; else echo 'Weather retrieving app';?>">
  <meta name="keywords" content="<?php if(isset($seoKeywords)) echo $seoKeywords; else echo 'Codeigniter, weather, CRON';?>">
  <meta name="author" content="Jure Škorc">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="<?php echo $this->config->base_url();?>js/jquery-3.3.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <?php if(isset($extraScripts) && $extraScripts) { foreach($extraScripts as $script) { echo $script."\r\n"; }}?>
</head>

<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="<?php echo $this->config->base_url();?>"><?php echo $this->lang->line("menu_websitename");?></a>
    </div>
   <?php if($this->session->userdata('logged_in')) { ?>
    <li class="dropdown" style="float:right;">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php if(filter_var($this->session->userdata('profile_pic'), FILTER_VALIDATE_URL)) echo '<img src="'.$this->session->userdata('profile_pic').'" style="height:40px;width:40px;border-radius:20px;" />'; else echo $this->lang->line("menu_profile");?>
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
			<li><a href="<?php echo $this->config->base_url(); if($this->session->userdata("is_superuser")) echo "admin"; else echo "profile";?>"><?php echo $this->lang->line("menu_profile");?></a></li>
          <li><a href="<?php echo $this->config->base_url();?>logout"><?php echo $this->lang->line("menu_logout");?></a></li>
        </ul>
    </li>
	<?php } else { ?>
	<ul class="nav navbar-nav" style="float:right;">
      <li class="active"><a href="<?php echo $this->config->base_url();?>google_login"><?php echo $this->lang->line("menu_login");?></a></li>
    </ul>
	<?php } ?>
  </div>
</nav>
