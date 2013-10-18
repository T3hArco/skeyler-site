<?php
require '../../_.php';

$bbcode = getPost('bbcode');


$parsedCode = BBCode::parse($bbcode);


$data['bbcode'] = $bbcode;
$data['parsedCode'] = $parsedCode;

view($data);
