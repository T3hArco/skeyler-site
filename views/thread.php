<?php global $Config, $pageId; ?>

<div class="posts">
  <?php $i = 0; ?>
  <?php foreach ($local['posts'] as $post) : ?>
    <?php $i++; ?>
    <div class="post post-<?php echo $post['id']; ?>" id="p_<?php echo ($pageId - 1) * $Config['postsPerPage'] + $i; ?>">
      <div class="userInfo">
        <span class="postTime"><?php echo writeDateEng($post['timestamp']); ?></span>
        <img src="<?php echo User::writeAvatar($local['users'][$post['userId']]['avatarUrl'], 'full'); ?>" class="avatar" />
        <?php echo User::writeUserLink($local['users'][$post['userId']]); ?>
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
    </div>
  <?php endforeach; ?>

</div>