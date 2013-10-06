<?php global $now; ?>
<h2><?php echo $local['forums'][$local['forumId']]['name']; ?></h2>
<span class="forumInfo"><?php echo $local['forums'][$local['forumId']]['threadCount']; ?>
  topics, <?php echo $local['forums'][$local['forumId']]['postCount']; ?> posts</span>


<?php if (count($local['forums']) > 1) : ?>
  <div class="table subforums">
    <div class="cell">
      <div class="cell">Forum</div>
      <div class="cell">Topics</div>
    </div>
    <?php foreach ($local['forums'] as $forum) : ?>
      <?php if ($forum['id'] != $local['forumId']) : ?>
        <div class="row forumNew">
          <div class="cell">
            <span class="forumName"><?php echo $forum['name']; ?></span>
            <span class="description"><?php echo $forum['description']; ?></span>
          </div>
          <div class="cell">
            <?php echo $local['forums'][$local['forumId']]['threadCount']; ?>
            topics, <?php echo $local['forums'][$local['forumId']]['postCount']; ?> posts
          </div>
          <div class="cell">
            <img src="<?php echo User::writeAvatar($local['users'][$forum['lastPostUserId']]['avatarUrl']); ?>" class="avatar"/>
            <span class="timestamp"><?php echo writeTimeLength($now - $forum['lastPostTimestamp']); ?> ago</span>
            <a href="/user.php?userId=<?php echo $local['users'][$forum['lastPostUserId']]['id']; ?>" class="userLink"><?php echo ent($local['users'][$forum['lastPostUserId']]['name']); ?></a>
            <a href="#" class="viewLink">(view)</a>
          </div>

        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
