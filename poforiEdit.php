<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('pofori編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//poforiのid取得
$id = $_POST['id'];

//poforiデータを取得
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

if(!empty($_POST['edit_flg'])){
    //変数にpofori情報を代入
    $image = $dbPoforiData['image'];

    if(isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])){
        $old_name = $_FILES['image']['tmp_name'];
        $new_name = date("YmdHis");
        $new_name .= mt_rand();
        switch (exif_imagetype($_FILES['image']['tmp_name'])){
            case IMAGETYPE_JPEG:
            $new_name .= '.jpeg';
            break;
            case IMAGETYPE_GIF:
            $new_name .= '.gif';
            break;
            case IMAGETYPE_PNG:
            $new_name .= '.png';
            break;
            default:
            header('Location: login.php');
            exit();
        }
        if(move_uploaded_file($old_name, './image/'.$new_name)){
            $image = $new_name;
        }else{
            $image_err = "アップロードできませんでした。";
        }
    }
    $url = $_POST['url'];
    $lang = $_POST['lang'];
    $desc = $_POST['description'];


    //未入力チェック
    validRequired($lang, 'lang');
    validRequired($desc, 'desc');

    //最大文字数チェック
    validMaxLen($url, 'url');
    validMaxLen($lang, 'lang');
    validMaxLen($desc, 'desc');

    if(empty($err_msg) && empty($image_err)){
        //例外処理
        //例外処理
        try {
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'UPDATE poforis SET image = :image, url = :url, lang = :lang, description = :description WHERE id = :id';
            $data = array(':image' => $image, ':url' => $url, ':lang' => $lang, ':description' => $desc, ':id' => $id);
            // クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            // クエリ成功の場合
            if($stmt){
                debug('pofori編集完了');
                header("Location:mypage.php"); //マイページへ
            }

            } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
?>
<?php require('header.php'); ?>
<section class="main js-toggle-opacity">
    <form class="form" action="" method="POST" enctype="multipart/form-data">
    <h2 class="title">POFORI編集</h2>
    <div class="area-msg">
    <?php
    if(!empty($err_msg['common'])) echo $err_msg['common'];
    ?>
    </div>

    <div class="photo-area-pofori edit-pofori-image">
        <label class="<?php if(!empty($err_msg['img'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['img'])) echo $err_msg['img']; ?>
            </div>
            <input class="photo-input" type="file" name="image">
            <img src="<?php if(empty($_FILES['image']['name'])) echo './image/'.sanitize($dbPoforiData['image']); ?>" alt="" class="prev-img pofori edit-pofori-image">
        </label>
    </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['url'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['url'])) echo $err_msg['url']; ?>
            </div>
        <input placeholder="URL" class="input" value="<?php if(!empty($_POST['url'])){ echo $_POST['url']; }else{ echo sanitize($dbPoforiData['url']); } ?>" type="text" name="url">
        <span class="focus-animation"></span>
        </label>
    </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['lang'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['lang'])) echo $err_msg['lang']; ?>
            </div>
        <input placeholder="使った技術*" class="input" type="text" name="lang" value="<?php if(!empty($_POST['lang'])){ echo $_POST['lang']; }else{ echo sanitize($dbPoforiData['lang']); } ?>">
        <span class="focus-animation"></span>
        </label>
    </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['desc'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['desc'])) echo $err_msg['desc']; ?>
        </div>
        <textarea placeholder="説明*" class="input input-textarea" name="description" id="" cols="30" rows="4"><?php if(!empty($_POST['description'])){ echo $_POST['description']; }else{ echo sanitize($dbPoforiData['description']); } ?></textarea>
        </label>
    <span class="js-count count-area"></span>
    </div>

        <input type="hidden" name="id" value="<?php echo $dbPoforiData['id']; ?>">
        <input type="hidden" name="edit_flg" value="1">
        <button class="input-btn" type="submit">更新</button>
        <button href="mypage.php" class="input-btn cancel">戻る</button>
    </form>
</section>

<?php require('footer.php');