<?php

require('function.php');

debug('「「「「「「「「「email重複チェック」」」」」」」」」」');

$email = $_POST['email'];

//emailー情報取得
if(!empty($email)){
    debug('email情報を取得します。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT * FROM users  WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $check = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($check)){
          debug('すでに使用されているemailアドレスです。');
          echo 'check';
        }
      }else{
        return false;
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
}
