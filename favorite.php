<?php

require('function.php');
require('viewsfunction.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('お気に入り登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$u_id = $_SESSION['user_id'];
$pofori_id = intval($_POST['pofori_id']);

$pofori = getOnePofori($pofori_id);

//poforiがない場合
if(empty($pofori)){
    debug($pofori_id.'のpoforiは存在しません。');
    echo 'error';
    exit();
}

$dbFavoPofori = getFavoPofori($u_id, $pofori_id);

if(empty($dbFavoPofori) && isLogin()){
    //例外処理
    try{
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        debug('お気に入り登録');
        $sql = 'INSERT INTO favorite (user_id, pofori_id, created)
        values(:u_id, :pofori_id, :created)';
        $data = array(':u_id' => $u_id, ':pofori_id' => $pofori_id, ':created' => date('Y-m-d H:i:s'));

        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        if($stmt){
            debug('お気に入り登録完了');

            echo 'countUp';
        }

    }catch(Exception $e){
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

if(!empty($dbFavoPofori) && isLogin()){
    try{

        $dbh = dbConnect();

        $sql = 'DELETE FROM favorite WHERE user_id = :u_id AND pofori_id = :pofori_id';

        $data = array(':u_id' => $u_id, ':pofori_id' => $pofori_id);

        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            debug('お気に入り削除完了');

            echo 'countdown';
        }else{
            debug('クエリが失敗しました。');
        }
    }catch(Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}