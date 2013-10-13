<h2>Create Post</h2>

<form method="post" class="createPost">
  <label>
    <strong>Content:</strong><br/>
    <textarea id="postContent" name="content" rows="10" cols="100"><?php echo ent($local['content']); ?></textarea>
  </label>
  <br/>
  <input type="submit" name="submit"/>
</form>
<h3>Preview:</h3>
<div id="parsedContent" class="postContent"></div>