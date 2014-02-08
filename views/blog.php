<div class="blog">
  <ul class="categories">
    <li class="selected"><a href="#">Newest</a></li>
    <li><a href="#">Popular</a></li>
  </ul>

  <?php foreach ($local['threads'] as $thread) : ?>
    <div class="blogPost">
      <h3><a href="/thread.php?threadId=<?php echo $thread['id']; ?>"><?php echo ent($thread['title']); ?></a></h3>

      <span class="postDetails">posted by <a href="/user.php?userId=<?php echo $local['posts'][$thread['id']]['userId']; ?>"><?php echo ent($local['users'][$local['posts'][$thread['id']]['userId']]['name']); ?></a> on <?php echo date('F jS, Y', $local['posts'][$thread['id']]['timestamp']); ?></span>

      <p><?php echo $local['posts'][$thread['id']]['contentParsed']; ?></p>
      <a href="/thread.php?threadId=<?php echo $thread['id']; ?>" class="comments"><?php echo $thread['postCount'] - 1; ?>
        Comments</a>
    </div>
  <?php endforeach; ?>
</div>