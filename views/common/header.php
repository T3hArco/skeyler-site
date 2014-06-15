<!doctype html>
<?php global $Config, $User, $isLoggedIn; ?>
<html>
<head>
  <title><?php echo($local['title'] ? substr($local['title'], 0, 50) . (strlen($local['title']) > 50 ? '...' : '') . ' - ' : ''); ?>Skeyler</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="shortcut icon" href="<?php echo $Config['mediaServer']; ?>favicon.ico"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/fonts.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/bootstrap-theme.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/main.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/header.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/forums.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/threads.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/posts.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/games.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/blog.css?xw=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/servers.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/staff.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/user.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/sprites.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/footer.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/userlist.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/stats.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/_EDIT_ME_IF_YOU_WANT_TO_ADD_STYLES.css?w=<?php echo $Config['staticFileCacheId']; ?>"/>
<!--  <link rel="stylesheet" type="text/css" media="handheld" href="--><?php //echo $Config['mediaServer']; ?><!--css/mobile.css?x=--><?php //echo $Config['staticFileCacheId']; ?><!--"/>-->

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/underscore-min.js"></script>

  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/jquery.caret.1.02.js"></script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/bbcode.js?w=<?php echo $Config['staticFileCacheId']; ?>"></script>
  <script>
    var isChatboxEnabled = <?php echo ($Config['chatboxEnabled'] ? 'true' : 'false'); ?>;
  </script>
  <script type="text/javascript" src="<?php echo $Config['mediaServer']; ?>js/main.js?w=<?php echo $Config['staticFileCacheId']; ?>"></script>

</head>
<body>
<div id="container">
  <div class="header">
    <h1><a href="/"><strong>Skeyler</strong> <span>Gmod Community</span></a></h1>
    <a class="topNavExpander" href="#">Navigation</a>
    <ul class="topNav"><!--
      --><li <?php echo ($local['navigationItem'] == 'news' ? 'class = "selected"' : ''); ?>><a href="/blog.php">News</a></li><!--
      --><li <?php echo ($local['navigationItem'] == 'forums' ? 'class = "selected"' : ''); ?>><a href="/forums/">Forums</a></li><!--
      --><li <?php echo ($local['navigationItem'] == 'servers' ? 'class = "selected"' : ''); ?>><a href="/servers.php">Servers</a></li><!--
      --><li <?php echo ($local['navigationItem'] == 'stats' ? 'class = "selected"' : ''); ?>><a href="/stats.php?gamemode=bhop">Stats</a></li><!--
    --></ul>
  </div>

  <?php Notice::writeNotices(); ?>
  <div class="content<?php echo ($local['pageClasses'] ? implode(' ', presuf($local['pageClasses'], ' ')) : ''); ?>">
    <ul class="welcome">
      <?php if($isLoggedIn) : ?>
        <li>Welcome back,
          <a href="/user.php?userId=<?php echo $User['id']; ?>"><img src="<?php echo User::writeAvatar($User['avatarUrl']); ?>" class="avatar small"/><?php echo ent($User['name']); ?>
          </a></li>
        <li><a href="/user.php?userId=<?php echo $User['id']; ?>">Profile</a></li>
        <li><a href="/logout.php">Logout</a></li>
      <?php else : ?>
        <form action="/login.php?login" method="post">
          <button class="login">Login through Steam</button>
        </form>
      <?php endif; ?>
    </ul>

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