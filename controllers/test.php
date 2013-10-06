<?php
require '../_.php';
User::login('76561198010087850');
var_dump(User::loadIds(1));
echo '<img src="' . User::writeAvatar($User['avatarUrl'], 'medium') . '" />';
echo '<img src="' . User::writeAvatar($User['avatarUrl'], 'full') . '" />';
echo '<img src="' . User::writeAvatar($User['avatarUrl']) . '" />';

?>