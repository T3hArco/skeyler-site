<?php global $User; ?>
<h2><a href="/thread.php?threadId=<?php echo $local['thread']['id']; ?>"><?php echo ent($local['thread']['title']); ?></a></h2>
<h2>Reply to Thread</h2>
<form method="post" class="createPost">
  <label>
    <strong>Content:</strong><br/>
    <textarea id="postContent" name="content" rows="10" cols="100"><?php echo ent($local['content']); ?></textarea>
  </label>
  <br/>
  <!--<input type="submit" value="Preview"/>-->
  <input type="submit" name="submit" value="Post!"/>
</form>
<h3>Reply Preview:</h3>

<div class="posts">
  <div class="post">
    <div class="userInfo">
      <span class="postTime">In the near future</span>
      <img src="<?php echo User::writeAvatar($User['avatarUrl'], 'full'); ?>" class="avatar"/>
      <?php echo User::writeUserLink($User, array('hideTag' => true)); ?>
      <span class="postCount"><?php echo($User['postCount']); ?> posts</span>
      <?php echo User::writeRankTag($User); ?>
      <?php if (User::can(User::RANK_MOD)) : ?>
        <span class="steamId">STEAM_<?php echo $User['steamId']; ?></span>
        <?php if (User::can(User::RANK_ADMIN)) : ?>
          <span class="ip"><?php echo ent($_SERVER['REMOTE_ADDR']); ?></span>
        <?php endif; ?>
      <?php endif; ?>
    </div><!--
 --><div id="parsedContent" class="postContent"></div>
  </div>
</div>