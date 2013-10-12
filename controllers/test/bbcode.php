<?php
require '../../_.php';

$bbcode = isset($_POST['bbcode']) ? $_POST['bbcode'] : '';


$parsedCode = BBCode::parse($bbcode);


$data['bbcode'] = $bbcode;
$data['parsedCode'] = $parsedCode;

view($data);
