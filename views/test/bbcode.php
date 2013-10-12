<div style="border:1px solid #ccc;padding:0.5em;display:inline-block;">
<?php echo $local['parsedCode']; ?>
</div>
<form method="post">
  <textarea name="bbcode" rows="20" cols="100" style="max-width:100%;"><?php echo ent($local['bbcode']); ?></textarea>
  <input type="submit"/>
</form>