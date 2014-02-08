<div class="blog">
  <ul class="categories">
    <li class="selected"><a href="#">Newest</a></li>
    <li><a href="#">Popular</a></li>
  </ul>

  <?php foreach ($local['threads'] as $thread) : ?>
    <div class="blogPost">
      <h3><a href="/thread.php?threadId=<?php echo $thread['id']; ?>"><?php echo ent($thread['title']); ?></a></h3>

      <p><?php echo $local['posts'][$thread['id']]['contentParsed']; ?></p>

      <div class="postDetails">
        <a href="/user.php?userId=<?php echo $local['posts'][$thread['id']]['userId']; ?>" class="user-link"><?php echo ent($local['users'][$local['posts'][$thread['id']]['userId']]['name']); ?></a>
        <span class="timestamp" title="<?php echo writeDateEng($local['posts'][$thread['id']]['timestamp']); ?>"><?php echo date('m.d.y', $local['posts'][$thread['id']]['timestamp']); ?></span>
        <a href="/thread.php?threadId=<?php echo $thread['id']; ?>" class="comments"><?php echo $thread['postCount'] - 1; ?> Comments</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>