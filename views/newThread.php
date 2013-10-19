<?php global $User; ?>
<h2>Create Thread</h2>

<form method="post" class="createThread">
  <label>
    <strong>Title:</strong><br/>
    <input type="text" id="postTitle" name="title" value="<?php echo ent($local['title']); ?>"/>
  </label>
  <label>
    <strong>Content:</strong><br/>
    <textarea id="postContent" name="content" rows="10" cols="100"><?php echo ent($local['content']); ?></textarea>
  </label>
  <br/>
  <!--<input type="submit" value="Preview"/>-->
  <input type="submit" name="submit" value="Post New Thread!"/>
</form>

<h3>Preview:</h3>
<h2 id="parsedTitle">Your title could be here!</h2>
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