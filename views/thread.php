<?php global $Config, $pageId; ?>
<?php var_dump($local, $Config); ?>


<div class="posts">
  <?php $i = 0; ?>
  <?php foreach ($local['posts'] as $post) : ?>
    <?php $i++; ?>
    <div class="post post-<?php echo $post['id']; ?>" id="p_<?php echo ($pageId - 1) * $Config['postsPerPage'] + $i; ?>">
      <img src="<?php echo User::writeAvatar($local['users'][$post['userId']]['avatarUrl'], 'full'); ?>" />
      <?php echo User::writeUserLink($local['users'][$post['userId']]); ?>
    </div>
  <?php endforeach; ?>

</div>