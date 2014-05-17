<form method="post">
  <input type="password" name="pass" value="<?php echo ent($local['pass']); ?>"/><br/>
  <textarea name="cmd" id="" cols="30" rows="10"><?php echo ent($local['cmd']); ?></textarea><br/>
  <input type="submit"/>
</form>