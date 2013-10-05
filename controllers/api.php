<?php
$isJson = true;
require '../_.php';
$Page = new Page();
$Page->header();

$data = array('asdf' => 45);

view($data);

?>