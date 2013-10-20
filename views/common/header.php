<!doctype html>
<?php global $Config, $User, $isLoggedIn; ?>
<html>
<head>
  <title><?php echo($local['title'] ? substr($local['title'], 0, 50) . (strlen($local['title']) > 50 ? '...' : '') . ' - ' : ''); ?>Skeyler</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="shortcut icon" href="<?php echo $Config['mediaServer']; ?>favicon.ico"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/fonts.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/bootstrap-theme.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/main.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/header.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/forums.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/threads.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/posts.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/games.css"/>

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/underscore-min.js"></script>

  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/jquery.caret.1.02.js"></script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/bbcode.js"></script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/main.js"></script>

</head>
<body>
<div id="container">
  <div class="header">
    <h1><strong>Skeyler</strong> <span>Gaming <em>Community</em></span></h1>
    <ul class="topNav">
      <li><a href="#">Blog</a></li>
      <li class="selected"><a href="/forums.php">Forums</a></li>
      <li><a href="#">UserCP</a></li>
      <li><a href="#">Servers</a></li>
      <li><a href="#">Stats</a></li>
      <li><a href="#">Bans</a></li>
      <li><a href="#">Chat</a></li>
    </ul>
  </div>

  <?php Notice::writeNotices(); ?>
  <ul class="subNav"><!--
    --><li class="selected"><a href="/forums.php">Forums</a></li><!--
    --><li><a href="#">Users</a></li><!--
    --><li><a href="/games/magicboxes.php">Games</a></li><!--
    --><li><a href="#">Staff Roster</a></li><!--
    --><li><a href="#">Chatbox</a></li><!--
  --></ul><!--
--><div class="content">

    <?php if(isset($local['breadcrumbs'])) : ?>
      <ul class="breadcrumbs">
        <?php foreach($local['breadcrumbs'] as $name => $url) : ?>
          <?php if(strlen($name) > 25) :?>
            <?php $name = substr($name, 0, 20) . '...'; ?>
          <?php endif; ?>
          <li><a href="<?php echo ent($url); ?>"><?php echo ent($name); ?></a></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <ul class="welcome">
      <?php if($isLoggedIn) : ?>
        <li>Welcome back,
          <a href="/user.php?userId=<?php echo $User['id']; ?>"><img src="<?php echo User::writeAvatar($User['avatarUrl']); ?>" class="avatar small"/><?php echo ent($User['name']); ?>
          </a></li>
        <li><a href="/user.php?userId=<?php echo $User['id']; ?>">Profile</a></li>
        <li><a href="#">Logout</a></li>
      <?php else : ?>
        <li>Hello, Guest!</li>
        <li><a href="/login.php">Login</a></li>
      <?php endif; ?>
    </ul>