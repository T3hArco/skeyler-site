<h2>Web Games</h2>
<p>Looking for some fun? Want some extra dough? This is the place to be!</p>
<ul class="sideNav">
  <li class="selected"><a href="#">Magic Boxes</a></li>
  <li><a href="#">Raffle</a></li>
  <li><a href="#">Game!!</a></li>
</ul>
<div class="webGame magicBoxes">
  <h2>Magic Boxes</h2>

  <h3>Instructions</h3>

  <p>Click on a box to make a guess. You are only allowed one guess per day and each guess costs 150 dough. You can win
    between 0 and 5000 dough.</p>

  <p>If you are VIP you can win a 50k dough jackpot, if you are not VIP then you can win a 35k dough jackpot.</p>

  <form method="post">
    <?php for ($i = 0; $i < 10; $i++) : ?>
      <div class="magicBoxRow">
        <?php for ($j = 1; $j <= 10; $j++) : ?>
          <?php if ($local['boxes'][$i * 10 + $j] == 0) : ?>
            <?php if ($local['canUseBox']) : ?>
              <input type="submit" class="magicBox titletip" value="<?php echo($i * 10 + $j); ?>" title="What could be inside?!"/>
            <?php else : ?>
              <span class="magicBox titletip" title="You must wait 24 hours before you can open another magic box."><?php echo($i * 10 + $j); ?></span>
            <?php endif; ?>
          <?php else : ?>
            <a href="/user.php?userId=<?php echo $local['boxes'][$i * 10 + $j]; ?>" class="magicBox userLink empty"><?php echo ent($local['users'][$local['boxes'][$i * 10 + $j]]['name']); ?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endfor; ?></form>
</div>