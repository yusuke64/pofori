<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('削除ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//poforiId取得
$id = intval($_POST['id']);

$dbPoforiData = getOnePofori($id);
$u_id = $dbPoforiData['user_id'];
$session_id = $_SESSION['user_id'];

//ユーザーsession確認
if($u_id != $session_id){
    //ユーザーid不合致
    debug('ユーザーid不合致');
    session_destroy();
    header('Location:login.php');
    exit();
}

$image = $dbPoforiData['image'];

if(!empty($id)){
    try{
        //画像ファイル削除
        $f = fopen('./image/'.$image, 'r');
        unlink('./image/'.$image);
        fclose($f);

        $dbh = dbConnect();

        $stmt = $dbh->prepare(
            'DELETE FROM poforis WHERE id = :id'
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt){
            debug('pofori削除完了');
            header('Location:mypage.php');
        }else{
            debug('クエリが失敗しました。');
        }
    }catch(Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        header('Location:mypage.php');
        $err_msg['common'] = MSG07;
    }

    try{
        //favoriteからの削除
        $dbh = dbConnect();

        $stmt = $dbh->prepare(
            'DELETE FROM favorite WHERE pofori_id = :id'
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt){
            debug('favoriteからの削除完了');
            header('Location:mypage.php');
        }else{
            debug('クエリが失敗しました。');
        }
    }catch(Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        header('Location:mypage.php');
        $err_msg['common'] = MSG07;
    }
}

header('Location:mypage.php');