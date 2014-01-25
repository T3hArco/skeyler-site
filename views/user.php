<?php
$user = $local['user'];

?>
<div class="profile">
  <h2><?php echo $user['name']; ?>'s Profile</h2>

  <div class="quickstats">
    <div class="username"><?php echo User::writeRankTag($user); ?> <?php echo ent($user['name']); ?></div>
    <img src="<?php echo User::writeAvatar($user['avatarUrl'], 'full'); ?>" alt="<?php echo ent($user['name']); ?>\'s avatar!!!!"/>
    <br/>
    <?php echo ent($user['steamId']); ?>
    <div class="gold">WoW Gold: <?php echo $user['money']; ?></div>

    <div class="serverStats">
      <h3>Server Stats</h3>
      <dl>
        <dt>Playtime</dt><!--
                --><dd><?php echo writeTimeLength($user['playtime'], 'short'); ?></dd>

        <dt>Steam Profile</dt><!--
                --><dd><a href="http://steamcommunity.com/profiles/<?php echo $user['steamId64']; ?>" target="_blank">View Profile Page</a></dd>
      </dl>

      <h3>Forum Stats</h3>
      <dl>
        <dt>Post Count</dt><!--
                --><dd><?php echo $user['postCount']; ?></dd>
      </dl>
    </div>
  </div>

  <div class="statsDetailed">

    <ul class="tabs">
      <li class="selected" data-tab="sa"><a href="#">Sassilization</a></li>
      <li data-tab="bh"><a href="#">Bunny Hop</a></li>
      <li data-tab="cl"><a href="#">Climb</a></li>
      <li data-tab="dr"><a href="#">Deathrun</a></li>
      <li data-tab="ze"><a href="#">Zombie Escape</a></li>
      <li data-tab="misc"><a href="#">Misc</a></li>
    </ul>

    <div class="tabItem selected" data-tab="sa">
      <h2>Sassilization</h2>
      HERE ARE YOUR STATS AND ACHIEVEMENTSSS!!
      YOU DONT HAVE ANY
    </div>
    <div class="tabItem" data-tab="bh">
      <h2>Bunny Hop</h2>
      BHOOOOPPP
    </div>
    <div class="tabItem" data-tab="cl">
      <h2>CLp</h2>
      poop
    </div>
    <div class="tabItem" data-tab="dr">
      <h2>DR</h2>
      butts
    </div>
    <div class="tabItem" data-tab="ze">
      <h2>ZE</h2>
      fart
    </div>
    <div class="tabItem" data-tab="misc">
      <h2>MISC</h2>
      shit
    </div>

  </div>
</div>