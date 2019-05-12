<?php

require('function.php');

debug('「「「「「「「「「name重複チェック」」」」」」」」」」');

$name = $_POST['name'];
debug($name);

//nameー情報取得
if(!empty($name)){
    debug('name情報を取得します。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT * FROM users  WHERE name = :name AND delete_flg = 0';
      $data = array(':name' => $name);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $check = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($check)){
          debug('すでに使用されているnameです。');
          echo 'check';
        }
      }else{
        return false;
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
}