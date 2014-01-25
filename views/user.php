<?php
$user = $local['user'];

?>
<div class="profile">
  <div class="quickstats">
    <div class="username"><?php echo User::writeRankTag($user); ?> <?php echo ent($user['name']); ?></div>
    <img src="<?php echo User::writeAvatar($user['avatarUrl'], 'full'); ?>" alt="<?php echo ent($user['name']); ?>\'s avatar!!!!"/>
    <br/>
    <?php echo ent($user['steamId']); ?>
    <div class="gold">WoW Gold: <?php echo $user['money']; ?></div>

    <div class="serverStats">
      <h2>Server Stats</h2>
      <dl>
        <dt>Playtime</dt>
        <dd><?php echo writeTimeLength($user['playtime'], 'short'); ?></dd>

      </dl>


    </div>

  </div>


</div>