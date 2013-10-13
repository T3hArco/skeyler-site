<br class="clr"/><?php global $Config; ?>
    <!-- <?php echo $local['totalMs']; ?>ms -->
    <div id="chatbox">
      <h4>Chatbox</h4>
      <div class="chats">
      </div>
      <form method="post" id="chatboxPostForm">
        <input type="textbox" name="chatboxPost" id="chatboxPost" maxlength="120"/>
      </form>
      <audio src="<?php echo $Config['mediaServer']; ?>audio/ding.mp3" id="chatboxAudio">
    </div>
  </div>
  <p id="footer">footer</p>
  </body>
</html>