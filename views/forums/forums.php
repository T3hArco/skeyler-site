<?php global $now, $Config, $pageId; ?>
  <h2 data-forum-id="<?php echo $local['forumId']; ?>" data-forum-description="<?php echo ent($local['forums'][$local['forumId']]['description']); ?>"><?php echo ent($local['forums'][$local['forumId']]['name']); ?></h2>
  <?php echo User::writeModOptions('forum'); ?>
  <br class="clr" />
<?php if ($local['forumId']) : ?>
  <span class="forumInfo"><?php echo $local['forums'][$local['forumId']]['threadCount']; ?>
    topic<?php echo singPlur($local['forums'][$local['forumId']]['threadCount']); ?>, <?php echo $local['forums'][$local['forumId']]['postCount']; ?>
    post<?php echo singPlur($local['forums'][$local['forumId']]['postCount']); ?></span>
<?php endif; ?>

<?php if (count($local['forums']) > 1) : ?>
  <div class="table subforums">
    <div class="row head">
      <div class="cell">Forum</div>
      <div class="cell">Topics</div>
    </div>
    <?php foreach ($local['forums'] as $forum) : ?>
      <?php if ($forum['id'] != $local['forumId']) : ?>
        <div class="row forum<?php echo(!isset($local['lastReadForumTimestamps'][$forum['id']]) || $local['lastReadForumTimestamps'][$forum['id']]['timestamp'] <= $forum['lastPostTimestamp'] ? 'New' : 'Seen'); ?><?php echo !(isset($local['subforums'][$forum['id']]) && count($local['subforums'][$forum['id']]) != 0) ? ' noSubforums' : '' ?>"><!--
          --><div class="cell forumInfo<?php echo !(isset($local['subforums'][$forum['id']]) && count($local['subforums'][$forum['id']]) != 0) ? ' noSubforums' : '' ?>">
            <a href="/forums/?forumId=<?php echo $forum['id']; ?>" class="forumName"><?php echo $forum['name']; ?></a>
            <span class="description"><?php echo $forum['description']; ?></span>
            <?php if (isset($local['subforums'][$forum['id']]) && count($local['subforums'][$forum['id']]) != 0) : ?>
              <ul class="subforums">
                <?php foreach ($local['subforums'][$forum['id']] as $sub) : ?>
                  <li><a href="/forums/?forumId=<?php echo $sub['id']; ?>"><?php echo ent($sub['name']); ?></a></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div><!--
                    --><div class="cell postCount">
            <?php echo $forum['threadCount']; ?>
            topic<?php echo singPlur($forum['threadCount']); ?>, <?php echo $forum['postCount']; ?>
            post<?php echo singPlur($forum['postCount']); ?>
          </div><!--
                    --><div class="cell lastPostInfo">
            <?php if ($forum['lastPostUserId']) : ?>
              <img src="<?php echo User::writeAvatar($local['users'][$forum['lastPostUserId']]['avatarUrl'], 'medium'); ?>" class="avatar"/>

              <div class="subPostInfo">
                <span class="timestamp"><?php echo writeTimeLength($now - $forum['lastPostTimestamp']); ?> ago</span>
                <a href="/user.php?userId=<?php echo $local['users'][$forum['lastPostUserId']]['id']; ?>" class="userLink"><?php echo ent($local['users'][$forum['lastPostUserId']]['name']); ?></a>
                <a href="/forums/thread.php?threadId=<?php echo $forum['lastPostThreadId']; ?>&amp;lastPost" class="viewLink">(view)</a>
              </div>
            <?php else : ?>
              <div class="missing52 avatar"></div>

              <div class="subPostInfo">
                <span class="timestamp">Never</span>
                <a class="userLink">Nobody</a>
                <a class="viewLink"></a>
              </div>
            <?php endif; ?>
          </div><!--

                  --></div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if (count($local['threads']) > 0) : ?>
  <?php if (User::can($local['forums'][$local['forumId']]['createThreadRank'])) : ?>
    <a href="/forums/newThread.php?forumId=<?php echo $local['forumId']; ?>" class="newThreadLink">New Thread</a>
  <?php endif; ?>
  <table class="threads">
    <thead>
    <tr>
      <th>Topic</th>
      <th>Author</th>
      <th>Posts</th>
      <th>Views</th>
      <th>Last Post</th>
      <th class="pageNav"><?php echo writePageNav($pageId, floor(($local['forums'][$local['forumId']]['threadCount'] - 1) / $Config['threadsPerPage']) + 1); ?></th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($local['threads'] as $thread) : ?>
      <tr class="thread<?php echo(!isset($local['lastReadThreadPostCounts'][$thread['id']]) || $local['lastReadThreadPostCounts'][$thread['id']]['postsSeen'] < $thread['postCount'] ? 'New' : 'Seen'); ?><?php echo($thread['isSticky'] ? ' sticky' : ''); ?><?php echo($thread['isClosed'] ? ' closed' : ''); ?>">
        <td class="title">
          <span><a href="/forums/thread.php?threadId=<?php echo $thread['id']; ?>"><?php echo ent($thread['title']); ?></a></span>
          <?php if (isset($local['lastReadThreadPostCounts'][$thread['id']]) && $local['lastReadThreadPostCounts'][$thread['id']]['postsSeen'] < $thread['postCount']) : ?>
            <a href="/forums/thread.php?threadId=<?php echo $thread['id']; ?>&amp;page=<?php echo floor($local['lastReadThreadPostCounts'][$thread['id']]['postsSeen'] / $Config['postsPerPage']) + 1; ?>#p_<?php echo($local['lastReadThreadPostCounts'][$thread['id']]['postsSeen'] % $Config['postsPerPage'] + 1); ?>" class="newPosts">(+<?php echo($thread['postCount'] - $local['lastReadThreadPostCounts'][$thread['id']]['postsSeen']); ?>)</a>
          <?php endif; ?>
        </td>
        <td>
          <a href="/users.php?userId=<?php echo $local['users'][$thread['userId']]['id']; ?>"><?php echo $local['users'][$thread['userId']]['name']; ?></a>
        </td>
        <td><?php echo $thread['postCount']; ?></td>
        <td><?php echo $thread['views']; ?></td>
        <td colspan="2">
          <?php if ($thread['lastPostUserId']) : ?>
          <?php echo writeDate($thread['lastPostTimestamp']); ?> by
          <a href="/users.php?userId=<?php echo $local['users'][$thread['lastPostUserId']]['id']; ?>" class="userLink"><?php echo $local['users'][$thread['lastPostUserId']]['name']; ?></a>
          <a href="/forums/thread.php?threadId=<?php echo $thread['id']; ?>&amp;lastPost">(view)</a></td>
        <?php else : ?>
          Never by nobody
        <?php
        endif;
        ?>
      </tr>

    <?php endforeach; ?>
    </tbody>
  </table>
<?php elseif ($local['forumId']) : ?>
  <p>There are no threads on this forum.</p>
<?php endif; ?>