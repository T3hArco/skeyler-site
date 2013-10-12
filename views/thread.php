<?php global $Config, $pageId; ?>
<?php var_dump($local); ?>


<div class="posts">
  <?php $i = 0; ?>
  <?php foreach ($local['posts'] as $post) : ?>
    <?php $i++; ?>
    <div class="post post-<?php echo $post['id']; ?>" id="p_<?php echo ($pageId - 1) * $Config['postsPerPage'] + $i; ?>">
      <img src="<?php echo User::writeAvatar($local['users'][$post['userId']]['avatarUrl'], 'full'); ?>"/>
      <?php echo User::writeUserLink($local['users'][$post['userId']]); ?>
      <?php if (User::can(User::RANK_MOD)) : ?>
        STEAM_<?php echo $local['users'][$post['userId']]['steamId']; ?>
        <?php if (User::can(User::RANK_ADMIN)) : ?>
          <?php echo long2ip($post['ip']); ?>
        <?php endif; ?>
      <?php endif; ?>
      <?php echo Posts::bbcodeToHtml($post['content']); ?>
    </div>
  <?php endforeach; ?>

</div>