<?php global $now; ?>
<h2><?php echo $local['forums'][$local['forumId']]['name']; ?></h2>
<span class="forumInfo"><?php echo $local['forums'][$local['forumId']]['threadCount']; ?>
  topics, <?php echo $local['forums'][$local['forumId']]['postCount']; ?> posts</span>


<?php if (count($local['forums']) > 1) : ?>
  <div class="table subforums">
    <div class="row head">
      <div class="cell">Forum</div>
      <div class="cell">Topics</div>
    </div>
    <?php foreach ($local['forums'] as $forum) : ?>
      <?php if ($forum['id'] != $local['forumId']) : ?>
        <div class="row forumNew">
          <div class="cell forumInfo">
            <a href="/forums.php?forumId=<?php echo $forum['id']; ?>" class="forumName"><?php echo $forum['name']; ?></a>
            <span class="description"><?php echo $forum['description']; ?></span>
          </div>
          <div class="cell postCount">
            <?php echo $local['forums'][$local['forumId']]['threadCount']; ?>
            topics, <?php echo $local['forums'][$local['forumId']]['postCount']; ?> posts
          </div>
          <div class="cell lastPostInfo">
            <img src="<?php echo User::writeAvatar($local['users'][$forum['lastPostUserId']]['avatarUrl'], 'medium'); ?>" class="avatar"/>

            <div class="subPostInfo">
              <span class="timestamp"><?php echo writeTimeLength($now - $forum['lastPostTimestamp']); ?> ago</span>
              <a href="/user.php?userId=<?php echo $local['users'][$forum['lastPostUserId']]['id']; ?>" class="userLink"><?php echo ent($local['users'][$forum['lastPostUserId']]['name']); ?></a>
              <a href="/thread.php?threadId=<?php echo $forum['lastPostThreadId']; ?>" class="viewLink">(view)</a>
            </div>
          </div>

        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if (count($local['threads']) > 0) : ?>
<table class="threads">
  <thead>
  <tr class="row head">
    <th>Topic</th>
    <th>Author</th>
    <th>Posts</th>
    <th>Views</th>
    <th>Last Post</th>
  </tr>
  </thead>

  <?php foreach ($local['threads'] as $thread) : ?>
    <tr class="threadSeen">
      <td><?php echo ent($thread['title']); ?></td>
      <td><?php echo $thread['creatorUserId']; ?></td>
      <td><?php echo $thread['postCount']; ?></td>
      <td><?php echo $thread['views']; ?></td>
      <td><?php echo $thread['lastPostTimestamp']; ?></td>
    </tr>

  <?php endforeach; ?>

  <?php endif; ?>



