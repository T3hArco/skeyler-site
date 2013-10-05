<?php
require '../_.php';

$Page = new Page();

$Page->header('test');
?>
TESTING
<?php view(array('678'=>'asdf')); ?>