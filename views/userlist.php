<h2>User Search</h2>
<form method="post">
  <label>
    Search:
    <input type="text" name="search" value="<?php echo ent($local['search']); ?>"/>
  </label>
  <input type="submit" value="Search!"/>
</form>

<?php if (count($local['users']) > 0) : ?>
  <table class="userlist">
    <thead>
    <td>Rank</td>
    <td>Playername</td>
    <td>Play Time</td>
    <td>Pixels</td>
    <td>Posts</td>
    <td>Status</td>
    </thead>

    <?php foreach ($local['users'] as $user) : ?>
      <tr>
        <td><?php echo User::writeRankTag($user); ?></td>
        <td><?php echo User::writeUserLink($user, array('hideTag' => true)); ?></td>
        <td><?php echo $user['playtime']; ?></td>
        <td><span class="money"><?php echo $user['money']; ?></span></td>
        <td><?php echo $user['postCount']; ?></td>
        <td><?php echo writeDateEng($user['lastLoginTimestamp']); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>