<?php global $User; ?>
<?php $post = $local['post']; ?>
<h2>Edit a Post From:
  <a href="/forums/thread.php?threadId=<?php echo $local['thread']['id']; ?>"><?php echo ent($local['thread']['title']); ?></a>
</h2>

<br class="clr" />
<form method="post" class="editPost">
  <label for="postContent">Content</label>
  <div class="postFlexWrapper"><!--
  --><textarea id="postContent" name="content" rows="10" cols="100" placeholder="Enter your post content here."><?php echo ent($local['content']); ?></textarea><!--
  --><div class="bbcode">
      <h3>Post Formatting</h3>
      <ul class="bbc">
        <li><a href="#" class="sprite bbc-bold" data-bbc="[b]%s[/b]"></a></li>
        <li><a href="#" class="sprite bbc-italic" data-bbc="[i]%s[/i]"></a></li>
        <li><a href="#" class="sprite bbc-underline" data-bbc="[u]%s[/u]"></a></li>
        <li><a href="#" class="sprite bbc-list" data-bbc="[list]^[*]%s^[/list]"></a></li>
        <li><a href="#" class="sprite bbc-image" data-bbc="[img]%s[/img]"></a></li>
        <li><a href="#" class="sprite bbc-heading" data-bbc="[h1]%s[/h1]"></a></li>
        <li><a href="#" class="sprite bbc-link" data-bbc="[url=&quot;&quot;]%s[/url]"></a></li>
      </ul>
      <input type="submit" name="submit" value="Save this edit!"/>
    </div>
  </div>
  <div class="draftWrapper">
    <span class="saveDraft"></span>
    <a class="loadDraft" href="#"></a>
  </div>
</form>

<h3>Edited Post Preview:</h3>

<div class="posts">
  <div class="post">
    <div class="userInfo">
      <span class="postTime"><?php echo randArr(array('In the near future', 'The Year 2000', 'Jan 1st, 2070 BC', 'Dec 21, 2012', 'Tomorrow')); ?></span>
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

<h3>Original Post:</h3>
<div class="post post-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>" data-user-id="<?php echo $post['userId']; ?>">
  <div class="userInfo">
    <span class="postTime"><?php echo writeDateEng($post['timestamp']); ?></span>
    <img src="<?php echo User::writeAvatar($User['avatarUrl'], 'full'); ?>" class="avatar"/>
    <?php echo User::writeUserLink($User, array('hideTag' => true)); ?>
    <span class="postCount"><?php echo($User['postCount']); ?> posts</span>
    <?php echo User::writeRankTag($User); ?>
    <?php if (User::can(User::RANK_MOD)) : ?>
      <span class="steamId">STEAM_<?php echo $User['steamId']; ?></span>
      <?php if (User::can(User::RANK_ADMIN)) : ?>
        <span class="ip"><?php echo long2ip($post['ip']); ?></span>
      <?php endif; ?>
    <?php endif; ?>
  </div><!--
--><div class="postContent">
    <?php echo $post['contentParsed']; ?>
  </div>
</div>