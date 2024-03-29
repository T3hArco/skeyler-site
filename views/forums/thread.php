<?php global $Config, $pageId, $User; ?>
<?php

$classes = array('threadTitle');
if ($local['thread']['isClosed']) {
  $classes[] = 'closed';
}
if ($local['thread']['isSticky']) {
  $classes[] = 'sticky';
}

?>
<?php echo User::writeModOptions('thread'); ?>
<h2 class="<?php echo implode(' ', $classes); ?>"><?php echo ent($local['thread']['title']); ?></h2>
<?php echo writePageNav($pageId, floor(($local['thread']['postCount'] - 1) / $Config['postsPerPage']) + 1, null, null, 'top right'); ?>
<br class="clr"/>
<div class="posts" data-thread-id="<?php echo $local['thread']['id']; ?>" data-forum-id="<?php echo $local['thread']['forumId']; ?>">
  <?php $i = 0; ?>
  <?php foreach ($local['posts'] as $post) : ?>
    <?php $i++; ?>
    <div class="post post-<?php echo $post['id']; ?><?php echo ($post['userId'] == $User['id'] ? ' myPost' : '') . ($i <= $local['lastSeen'] ? ' seenPost':' newPost') ?>" id="p_<?php echo $i; ?>" data-post-id="<?php echo $post['id']; ?>" data-bbcode="<?php echo ent($post['content']); ?>" data-user-id="<?php echo $post['userId']; ?>">
      <div class="userInfo">
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
   --><div class="postContent"><?php echo $post['contentParsed']; ?>
        <?php if ($post['lastEditTimestamp'] > $post['timestamp'] + 2 * 60) : ?>
          <?php if($post['userId'] == $post['lastEditUserId'] || User::can(User::RANK_MOD)) : ?>
            <span class="editInfo">[ Edited <?php echo writeDateEng($post['lastEditTimestamp']); ?> by <?php echo User::writeUserLink($local['users'][$post['lastEditUserId']], array('hideTag' => true)); ?> ]</span>
          <?php else : ?>
            <span class="editInfo">[ Edited <?php echo writeDateEng($post['lastEditTimestamp']); ?> by Skeyler Staff ]</span>
          <?php endif; ?>
        <?php endif; ?>
        <a href="/forums/thread.php?postId=<?php echo $post['id']; ?>" class="postTime" title="<?php echo date('r', $post['timestamp']); ?>"><?php echo writeDateEng($post['timestamp']); ?></a>
      </div>
      <div class="postOptions">
        <a href="#" class="sprite quote"></a>
        <a href="/user.php?userId=<?php echo $post['userId']; ?>" class="sprite profile" target="_blank"></a>
        <?php if ($post['userId'] == $User['id']) : ?>
          <a href="/forums/editPost.php?postId=<?php echo $post['id']; ?>" class="sprite edit"></a>
        <?php endif; ?>
        <?php echo User::writeModOptions('post'); ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php if($local['canPost']) : ?>
  <form method="post" class="createPost" action="/forums/newPost.php?threadId=<?php echo $local['thread']['id']; ?>"><!--
  --><div class="postFlexWrapper"><!--
    --><textarea id="postContent" name="content" rows="10" cols="100" placeholder="Enter your post content here."></textarea><!--
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
        <input type="submit" value="Preview"/>
        <input type="submit" name="submit" value="Post!"/>
      </div>
    </div>
    <div class="draftWrapper">
      <span class="saveDraft"></span>
      <a class="loadDraft" href="#"></a>
    </div>
  </form>
<?php endif; ?>
<?php echo writePageNav($pageId, floor(($local['thread']['postCount'] - 1) / $Config['postsPerPage']) + 1, null, null, 'bottom right'); ?>