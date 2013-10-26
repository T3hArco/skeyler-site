<h2>Staff Roster</h2>
<div class="staffRoster">
  <?php foreach ($local['users'] as $user) : ?>
    <div class="user">
      <a class="steamLink" href="http://steamcommunity.com/profiles/<?php echo $user['steamId64']; ?>/" target="_blank">
        <img src="<?php echo ent(User::writeAvatar($user['avatarUrl'], 'full')); ?>" class="avatar"/>
      </a>
      <a class="viewProfile" href="/user.php?id=<?php echo $user['id']; ?>">View Profile</a>

      <div class="info">
        <h3>
          <?php echo User::writeUserLink($user, array('hideTag' => true)); ?>
          <span class="job"><?php echo(isset($local['staffInfo'][$user['id']]) ? $local['staffInfo'][$user['id']]['header'] : ''); ?></span>
        </h3>

        <p><?php echo(isset($local['staffInfo'][$user['id']]) ? $local['staffInfo'][$user['id']]['description'] : ''); ?></p>
        <br class="clr"/>
      </div>
    </div>
  <?php endforeach; ?>
</div>