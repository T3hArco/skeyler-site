<?php
$forum = $local['forum'];
?>
<h2><?php echo($local['isCreate'] ? 'Create New Forum!' : 'Edit Forum ' . $forum['name']); ?></h2>
<form method="post" class="admin-form form-labels">
  <div class="labels">
    <input type="text" name="name" value="<?php echo ent($forum['name']); ?>" id="form-name" placeholder="Name" required/>
    <label for="form-name">Name</label>
  </div>

  <div class="labels">
    <input type="text" name="description" value="<?php echo ent($forum['description']); ?>" placeholder="Description" required/>
    <label for="form-description">Description</label>
  </div>

  <label>Visible Rank</label>
  <input type="text" name="visibleRank" value="<?php echo ent($forum['visibleRank']); ?>"/>

  <label>Create Post Rank</label>
  <input type="text" name="createPostRank" value="<?php echo ent($forum['createPostRank']); ?>"/>

  <label>Create Thread Rank</label>
  <input type="text" name="createThreadRank" value="<?php echo ent($forum['createThreadRank']); ?>"/>

  <?php if($local['isCreate']) : ?>
    <input type="hidden" name="parentId" value="<?php echo ent($local['parentId']); ?>"/>
  <?php endif; ?>

  <input type="submit" name="submit" value="Do it!"/>
</form>