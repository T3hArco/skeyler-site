<?php
$posts = $local['posts'];

?>
<div class="search">
  <div class="posts">
    <?php foreach ($posts as $post) : ?>
      <?php if (!User::can($post['visibleRank'])) : ?>
        <div class="post">A post matched here, but you don't have permission to view it. ~({ O W N A G E })~</div>
      <?php else : ?>
        <div class="post post-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>">
          <a href="/thread.php?threadId=<?php echo $post['threadId']; ?>"><?php echo ent($post['title']); ?></a>
          -
          <a href="/forum.php?forumId=<?php echo $post['forumId']; ?>"><?php echo $post['forumName']; ?></a>
          -
          <?php echo writeDateEng($post['timestamp']); ?>

          <div class="postContent">
            <?php echo substr($post['content'], 0, 256); ?>

          </div>

        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>