<?php
require '../_.php';
$Page = new Page('forums');

$userId = (int) getGet('userId');


$user = User::getId($userId);

if (!$user) {
  Notice::error('That user does not exist! They may be a ghost! oOOooO0Ooo0OOOoo!');
  exit;
}

$Page->header($user['name'] . '\'s profile');

$sassStats = array();

// get the wins and rank of the user
$query = '
  SELECT *
  FROM rts_leaderboards AS rl
  WHERE userId = :userId
  LIMIT 1;
';
$binds = array(
  'userId' => $user['id']
);
$row = DB::q($query, $binds)->fetch();
if (count($row) == 0) {
  $victories = 0;
  $rank = 0;
}
else {
  $victories = $row['wins'];
  // fetch rank
  $query = '
    SELECT COUNT(userId) + 1 AS rank
    FROM rts_leaderboards
    WHERE wins > :userWins
    LIMIT 1;
  ';
  $binds = array(
    'userWins' => $victories,
  );
  $row = DB::q($query, $binds)->fetch();
  $rank = $row['rank'];
}
$sassStats[] = array(
  'name' => 'Global Rank',
  'value' => $rank ? numericSuffix($rank) : '--',
);
$sassStats[] = array(
  'name' => 'Victories',
  'value' => $victories
);



// get their archers, etc, counts
$query = '
SELECT rc.rtsMatchPlayerId, rct.name, rct.description, SUM(rc.amountBuilt) AS amount
FROM rts_constructions AS rc
JOIN rts_construction_types AS rct
ON rc.constructionTypeId = rct.id
WHERE rc.rtsMatchPlayerId = :userId
GROUP BY rc.rtsMatchPlayerId, rc.constructionTypeId
;';
$binds = array(
  'userId' => $user['id']
);
$rows = DB::q($query, $binds)->fetchAll();

foreach ($rows as $row) {
  $sassStats[] = array(
    'name' => $row['name'],
    'value' => $row['amount'],
    'description' => $row['description'],
  );
}

$data = array(
  'user' => $user,
  'stats' => array(
    'sassilization' => $sassStats,
  )
);

view($data);

