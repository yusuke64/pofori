<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 ゲストログイン');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$email = 'guest@a.com';

try{
  //dbへ接続
  $dbh = dbConnect();
  //sql文作成
  $sql = 'SELECT id FROM users WHERE email = :email AND delete_flg = 0';
  $data = array(':email' => $email);
  //クエリ実行
  $stmt = queryPost($dbh, $sql, $data);
  //クエリ結果の値を取得
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  debug('クエリ結果の中身：'.print_r($result,true));

  //ログイン有効期限
  $sesLimit = 60*60;
  $_SESSION['login_limit'] = $sesLimit;
  //最終ログイン日時を現在日時に
  $_SESSION['login_date'] = time();
  //ユーザーIDを格納
  $_SESSION['user_id'] = $result['id'];

  debug('セッションの中身：'.print_r($_SESSION,true));
  debug('マイページへ遷移します。');
  header("Location:mypage.php");//マイページへ

}catch(Exception $e){
  error_log('エラー発生：'.$e->getMessage());
  $err_msg['common'] = MSG07;
}
