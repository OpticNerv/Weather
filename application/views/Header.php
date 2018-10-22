<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php if(isset($seoTitle)) echo $seoTitle;?></title>
  <meta name="description" content="<?php if(isset($seoDescription)) echo $seoDescription; else echo 'Weather retrieving app';?>">
  <meta name="keywords" content="<?php if(isset($seoKeywords)) echo $seoKeywords; else echo 'Codeigniter, weather, CRON';?>">
  <meta name="author" content="Jure Škorc">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="<?php echo $this->config->base_url();?>js/jquery-3.3.1.min.js"></script>
  <link rel="stylesheet" href="<?php echo $this->config->base_url();?>css/bootstrap/css/bootstrap.min.css"> <!-- load bootstrap -->
  <link rel="stylesheet" href="<?php echo $this->config->base_url();?>css/fontawesome/css/fontawesome.min.css"> <!-- load fontawesome -->
  <script src="<?php echo $this->config->base_url();?>css/bootstrap/js/bootstrap.min.js"></script>
 
  <?php if(isset($extraScripts)) echo $extraScripts;?>
</head>

<body>
