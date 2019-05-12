<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('pofori投稿ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

if(!empty($_POST)){
    //変数にpofori情報を代入
    $image = '';
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
    $img = $_FILES['image']['name'];
    $url = $_POST['url'];
    $lang = $_POST['lang'];
    $desc = $_POST['description'];


    //未入力チェック
    validRequired($img, 'img');
    validRequired($lang, 'lang');
    validRequired($desc, 'desc');

    //最大文字数チェック
    validMaxLen($url, 'url');
    validMaxLen($lang, 'lang');
    validMaxLen($desc, 'desc');

    if(empty($err_msg) && empty($image_err)){
        //例外処理
        try{
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            debug('pofori作成');
            $sql = 'INSERT INTO poforis (user_id, image, url, lang, description, created)
            values(:u_id, :image, :url, :lang, :description, :created)';
            $data = array(':u_id' => $_SESSION['user_id'], ':image' => $image, ':url' => $url, ':lang' => $lang, ':description' => $desc, ':created' => date('Y-m-d H:i:s'));

            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            //クエリ成功の場合
            if($stmt){
                debug('homeへ遷移します');
                header("Location:mypage.php");
            }

        }catch(Exception $e){
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
?>
<?php require('header.php'); ?>
<section class="main js-toggle-opacity">
    <form class="form" action="" method="POST" enctype="multipart/form-data">
    <h2 class="title">POFORI登録</h2>
    <div class="area-msg">
    <?php
    if(!empty($err_msg['common'])) echo $err_msg['common'];
    ?>
    </div>

    <div class="photo-area photo-area-pofori">
        <label class="<?php if(!empty($err_msg['img'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['img'])) echo $err_msg['img']; ?>
            </div>
            <span class="choise-msg">写真を選択</span>
            <input class="photo-input" type="file" name="image">
            <img src="" alt="" class="prev-img pofori">
        </label>
    </div>

    <div class="form-zone">
    <label class="<?php if(!empty($err_msg['url'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['url'])) echo $err_msg['url']; ?>
            </div>
        <input placeholder="URL" class="input" value="<?php if(!empty($_POST['url'])) echo $_POST['url'] ?>" type="text" name="url">
        <span class="focus-animation"></span>
        </label>
    </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['lang'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['lang'])) echo $err_msg['lang']; ?>
            </div>
        <input placeholder="使った技術*" class="input" type="text" name="lang" value="<?php if(!empty($_POST['lang'])) echo $_POST['lang'] ?>">
        <span class="focus-animation"></span>
        </label>
    </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['desc'])) echo 'err'; ?>">
            <div class="area-msg">
            <?php if(!empty($err_msg['desc'])) echo $err_msg['desc']; ?>
            </div>
        <textarea placeholder="説明*" class="input input-textarea" name="description" id="" cols="30" rows="4"><?php if(!empty($_POST['description'])) echo $_POST['description'] ?></textarea>
        </label>
    </div>

        <button class="input-btn pofori" type="submit">登録</button>
    </form>
</section>

<?php require('footer.php');