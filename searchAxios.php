<?php

require('function.php');

if(empty($_POST)){
  debug('post送信がありません');
  exit();
}

$val = $_POST['data'];

$dbData = [
  'users' => getSearchUser($val),
  'poforis' => getSearchPofori($val),
];
echo json_encode($dbData);