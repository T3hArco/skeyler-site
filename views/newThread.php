<h2>Create Thread</h2>

<form method="post" class="createThread">
  <label>
    <strong>Title:</strong><br/>
    <input type="text" id="postTitle" name="title" value="<?php echo ent($local['title']); ?>"/>
  </label>
  <label>
    <strong>Content:</strong><br/>
    <textarea id="postContent" name="content" rows="10" cols="100"><?php echo ent($local['content']); ?></textarea>
  </label>
  <br/>
  <input type="submit" name="submit"/>
</form>
<h3>Preview:</h3>
<div id="parsedContent"></div>