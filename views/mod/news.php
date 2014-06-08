<h2>Here is the news!</h2>
<form method="post">
  <textarea name="news" cols="100" rows="10"><?php echo ent($local['news']); ?>


</textarea>
  <br/>
  <input type="submit" value="Save News!" name="submit" />
</form>