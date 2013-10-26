<h2>User Search</h2>
<form method="post">
  <label>
    Search:
    <input type="text" name="search" value="<?php echo ent($local['search']); ?>"/>
  </label>
  <input type="submit" value="Search!"/>
</form>

<?php if (count($local['users']) > 0) : ?>
  <table>
    <thead>
    <td>rank</td>
    <td>name</td>
    <td>steamID</td>
    <td>money</td>
    <td>playtime</td>
    <td>last login</td>
    <td>posts</td>
    </thead>

    <?php foreach ($local['users'] as $user) : ?>
      <tr>
        <td><?php echo User::writeRankTag($user); ?></td>
        <td><?php echo User::writeUserLink($user, array('hideTag' => true)); ?></td>
        <td>
          <a href="http://steamcommunity.com/profiles/<?php echo $user['steamId64']; ?>/" target="_blank"><?php echo $user['steamId']; ?></a>
        </td>
        <td><?php echo $user['money']; ?></td>
        <td><?php echo $user['playtime']; ?></td>
        <td><?php echo writeDateEng($user['lastLoginTimestamp']); ?></td>
        <td><?php echo $user['postCount']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>