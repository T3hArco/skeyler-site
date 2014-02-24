<?php
$user = $local['user'];

?>
<div class="profile">

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
             --><dd><?php echo writeTimeLength($user['playtime'], 'short'); ?></dd>
        <dt>Last Scene</dt><!--
             --><dd><?php echo writeTimeLength($user['playtime'], 'short'); ?></dd>
        <dt>Steam Profile</dt><!--
             --><dd><a href="http://steamcommunity.com/profiles/<?php echo $user['steamId64']; ?>" target="_blank">View Profile</a></dd>
      </dl>

      <h3>Forum Stats</h3>
      <dl>
        <dt>Post Count</dt><!--
             --><dd><a href="/search.php?userId=<?php echo $user['id']; ?>" title="Search this users super good awesome posts!"><?php echo $user['postCount']; ?></a></dd>
        <dt>Forum Rank</dt><!--
             --><dd><?php echo User::writeRankTag($user); ?></dd>
        <dt>Doubloons Tossed</dt><!--
             --><dd>99999</dd>
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
      <h2>Achievements</h2>
      <div class="stats">
      HERE ARE YOUR STATS AND ACHIEVEMENTSSS!!
      YOU DONT HAVE ANY
    </div>

    <h2>Statistics</h2>
    <dl><!--
      --><dt class="globalRank">Global Rank</dt><!--
      --><dd>10</dd><!--
      --><dt class="victories">Victories</dt><!--
      --><dd>884</dd><!--
    --></dl>
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