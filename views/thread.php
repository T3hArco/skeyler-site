<?php global $Config, $pageId; ?>
<h2><?php echo ent($local['thread']['title']); ?></h2>
<?php echo writePageNav($pageId, floor(($local['thread']['postCount'] - 1) / $Config['postsPerPage']) + 1, 'top right'); ?>
  <br class="clr"/>
  <div class="posts">
    <?php $i = 0; ?>
    <?php foreach ($local['posts'] as $post) : ?>
      <?php $i++; ?>
      <div class="post post-<?php echo $post['id']; ?>" id="p_<?php echo $i; ?>" data-post-id="<?php echo $post['id']; ?>" data-bbcode="<?php echo ent($post['content']); ?>">
        <div class="userInfo">
          <span class="postTime"><?php echo writeDateEng($post['timestamp']); ?></span>
          <img src="<?php echo User::writeAvatar($local['users'][$post['userId']]['avatarUrl'], 'full'); ?>" class="avatar"/>
          <?php echo User::writeUserLink($local['users'][$post['userId']], array('hideTag' => true)); ?>
          <span class="postCount"><?php echo($local['users'][$post['userId']]['postCount']); ?> posts</span>
          <?php echo User::writeRankTag($local['users'][$post['userId']]); ?>
          <?php if (User::can(User::RANK_MOD)) : ?>
            <span class="steamId">STEAM_<?php echo $local['users'][$post['userId']]['steamId']; ?></span>
            <?php if (User::can(User::RANK_ADMIN)) : ?>
              <span class="ip"><?php echo long2ip($post['ip']); ?></span>
            <?php endif; ?>
          <?php endif; ?>
        </div><!--
     --><div class="postContent">
          <?php echo BBCode::parse($post['content']); ?>
        </div>
        <div class="postOptions">
          <a href="#" class="editLink"></a>
          <a href="#" class="quoteLink"></a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php echo writePageNav($pageId, floor(($local['thread']['postCount'] - 1) / $Config['postsPerPage']) + 1, 'bottom right'); ?>
<form method="post" class="createPost" action="/newPost.php?threadId=<?php echo $local['thread']['id']; ?>">
  <label>
    <strong>Content:</strong><br/>
    <textarea id="postContent" name="content" rows="10" cols="100"></textarea>
  </label>
  <br/>
  <input type="submit" name="submit"/>
</form>