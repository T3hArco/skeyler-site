<?php global $now, $Config, $pageId; ?>
<h2>Hello</h2>
<div class="table subforums">
  <?php foreach ($local['stats'] as $shortName => $gamemodeStats) : ?>
      <div class="row forum"><!--
        --><div class="cell forumInfo">
          <a href="/stats.php?gamemode=<?php echo $shortName; ?>" class="forumName"><?php echo $gamemodeStats['gamemodeName']; ?></a>
          <span class="description">Stats regarding our <?php echo $gamemodeStats['gamemodeName'];?> Servers</span>
        </div><!--
                  --><div class="cell postCount"><?php echo $gamemodeStats['playerCount']; ?> Player<?php echo singPlur($gamemodeStats['playerCount'])?> Recorded</div><!--
                  --><div class="cell lastPostInfo">
            <img src="<?php echo User::writeAvatar($local['users'][$gamemodeStats['record']['userId']]['avatarUrl'], 'medium'); ?>" class="avatar"/>

            <div class="subPostInfo">
              <a href="/user.php?userId=<?php echo $gamemodeStats['record']['userId']; ?>" class="userLink"><?php echo ent($local['users'][$gamemodeStats['record']['userId']]['name']); ?></a>
              <span class="timestamp"><?php echo writeTimeLength($gamemodeStats['record']['timespan'], 'record'); ?> on <a href="/stats.php?gamemode=<?php echo $shortName; ?>"><?php echo $gamemodeStats['record']['mapName']; ?></a></span>
              <a href="/stats.php?gamemode=<?php echo $shortName; ?>&amp;lastPost" class="viewLink">(view)</a>
            </div>
        </div><!--

                --></div>
  <?php endforeach; ?>
</div>