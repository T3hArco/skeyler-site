<!doctype html>
<?php global $Config; ?>
<html>
<head>
  <title><?php echo($local['title'] ? $local['title'] . ' - ' : ''); ?>Skeyler</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="shortcut icon" href="<?php echo $Config['mediaServer']; ?>favicon.ico"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/bootstrap-theme.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/main.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/header.css"/>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/underscore-min.js"></script>
</head>
<body>
<div id="container">
  <div class="header">
    <h1><strong>Skeyler</strong> <span>Gaming Servers</span></h1>
    <ul class="topNav">
      <li><a href="#">Blog</a></li>
      <li class="selected"><a href="#">Forums</a></li>
      <li><a href="#">UserCP</a></li>
      <li><a href="#">Servers</a></li>
      <li><a href="#">Stats</a></li>
      <li><a href="#">Bans</a></li>
      <li><a href="#">Chat</a></li>
    </ul>
  </div>

  <?php Notice::writeNotices(); ?>
  <ul class="subNav"><!--
    --><li class="selected"><a href="#">Forums</a></li><!--
    --><li><a href="#">Users</a></li><!--
    --><li><a href="#">Games</a></li><!--
    --><li><a href="#">Staff Roster</a></li><!--
    --><li><a href="#">Chatbox</a></li><!--
  --></ul><!--
  --><div class="content">