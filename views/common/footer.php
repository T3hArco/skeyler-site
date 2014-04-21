<?php
global $isLoggedIn, $User;

?>
<br class="clr"/><?php global $Config; ?>
    <!-- <?php echo $local['totalMs']; ?>ms -->
<?php if($isLoggedIn && User::can(User::RANK_REGULAR)) : ?>
    <div id="chatbox">
      <h4>Chatbox</h4>
      <div class="chats">
      </div>
      <form method="post" id="chatboxPostForm">
        <input type="textbox" name="chatboxPost" id="chatboxPost" maxlength="120" />
      </form>
      <audio src="<?php echo $Config['mediaServer']; ?>audio/ding.mp3" id="chatboxAudio">
    </div>
<?php endif; ?>
  </div>
</div>
  <footer>
    <ul class="bottomNav"><!--
      --><li <?php echo ($local['navigationItem'] == 'news' ? 'class = "selected"' : ''); ?>><a href="/blog.php">News</a></li><!--
      --><li <?php echo ($local['navigationItem'] == 'forums' ? 'class = "selected"' : ''); ?>><a href="/forums/">Forums</a></li><!--
      --><!--<li <?php echo ($local['navigationItem'] == 'servers' ? 'class = "selected"' : ''); ?>><a href="/servers.php">Servers</a></li>--><!--
      --><!--<li <?php echo ($local['navigationItem'] == 'stats' ? 'class = "selected"' : ''); ?>><a href="/stats.php">Stats</a></li>--><!--
    --></ul>
  </footer>
  </body>
</html>