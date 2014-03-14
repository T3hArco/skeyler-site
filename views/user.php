<?php
$user = $local['user'];

?>
<div class="profile" data-user-id="<?php echo $user['id']; ?>" data-rank="<?php echo $user['rank']; ?>">

  <?php if(User::can(User::RANK_MOD)) : ?>
    <?php echo User::writeModOptions('user'); ?>
  <?php endif; ?>

  <h2><?php echo $user['name']; ?>'s Profile</h2>

  <div class="quickstats">
    <span class="membership">Member since <?php echo date('F, Y', $user['registerTimestamp']); ?></span>
    <!--    <div class="username">--><?php //echo User::writeRankTag($user); ?><!-- --><?php //echo ent($user['name']); ?><!--</div>-->
    <img src="<?php echo User::writeAvatar($user['avatarUrl'], 'full'); ?>" alt="<?php echo ent($user['name']); ?>\'s avatar!!!!" class="avatar"/>
    <br/>
    <?php if (User::can(User::RANK_MOD)) : ?>
      <span class="steamId">STEAM_<?php echo ent($user['steamId']); ?></span>
    <?php endif; ?>

    <div class="serverStats">
      <h3>Server Stats</h3>
      <dl>
        <dt>Playtime</dt><!--
             --><dd><?php echo writeTimeLength($user['playtime'], 'short'); ?></dd>
        <dt>Last Seen</dt><!--
             --><dd><?php echo date('n/j/y', $user['lastLoginTimestamp']); ?></dd>
        <dt>Last Scene</dt><!--
             --><dd>--</dd>
        <dt>Steam Profile</dt><!--
             --><dd><a href="http://steamcommunity.com/profiles/<?php echo $user['steamId64']; ?>" target="_blank">View Profile</a></dd>
      </dl>

      <h3>Forum Stats</h3>
      <dl>
        <dt>Post Count</dt><!--
             --><!--<dd><a href="/forums/search.php?userId=<?php echo $user['id']; ?>" title="Search this users super good awesome posts!"><?php echo $user['postCount']; ?></a></dd>
             --><dd><?php echo $user['postCount']; ?></dd>
        <dt>Forum Rank</dt><!--
             --><dd><?php echo User::writeRankTag($user); ?></dd>
        <dt>Doubloons Tossed</dt><!--
             --><dd>0</dd>
        <dt>Doubloons</dt><!--
             --><dd><?php echo $user['money']; ?></dd>
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
      <h3>Achievements</h3>
      <div class="stats">
      HERE ARE YOUR STATS AND ACHIEVEMENTSSS!!
      YOU DONT HAVE ANY
    </div>

    <h3>Statistics</h3>
    <dl><!--
      --><dt class="globalRank sprite">Global Rank</dt><!--
      --><dd>9999</dd><!--
      --><dt class="victories sprite">Victories</dt><!--
      --><dd>0</dd><!--
    --></dl>
    </div>
    <div class="tabItem" data-tab="bh">
      <h3>Bunny Hop</h3>
      BHOOOOPPP
    </div>
    <div class="tabItem" data-tab="cl">
      <h3>CLp</h3>
      poop
    </div>
    <div class="tabItem" data-tab="dr">
      <h3>DR</h3>
      butts
    </div>
    <div class="tabItem" data-tab="ze">
      <h3>ZE</h3>
      fart
    </div>
    <div class="tabItem" data-tab="misc">
      <h3>MISC</h3>
      shit
    </div>

  </div>
</div>