<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//SESSION_IDを格納
$u_id = $_SESSION['user_id'];

//userデータを取得
$dbUserData = getUser($u_id);

$image = $dbUserData['prof_image'];

//post送信されていた場合
if(!empty($_POST)){

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
      if(move_uploaded_file($old_name, './profImage/'.$new_name)){
          $image = $new_name;
      }else{
          degug('アップロードに失敗しました');
  }
  }

  $about = '';

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $img = $_FILES['image']['name'];
  $about = $_POST['about'];

  //未入力チェック
  validRequired($name, 'name');

  if(empty($err_msg)){
    //最大文字数チェック
    validMaxLen($about, 'about');
    validMaxLenName($name, 'name');
  }

  if(empty($err_msg)){
      //例外処理
      try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users SET prof_image = :prof, name = :name, about = :about WHERE id = :id';
      $data = array(':prof' => $image, ':name' => $name, ':about' => $about, ':id' => $u_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
          debug('プロフィール編集完了');
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

<form action="" method="post" class="form" enctype="multipart/form-data">
  <h2 class="title">プロフィール</h2>
  <div class="area-msg">
    <?php
    if(!empty($err_msg['common'])) echo $err_msg['common'];
    ?>
  </div>

  <div class="photo-area-signup">
  <label class="<?php if(!empty($err_msg['img'])) echo 'err'; ?>">
          <div class="area-msg">
          <?php if(!empty($err_msg['img'])) echo $err_msg['img']; ?>
          </div>
          <input class="photo-input" type="file" name="image">
          <img src="
          <?php
            if(empty($_FILES['image']['name'])){

              if(!empty($dbUserData['prof_image'])){
                echo './profImage/'.sanitize($dbUserData['prof_image']);
              }else{
                echo './profImage/default.png';
              }
            }
          ?>
          " alt="" class="prev-img signup">
  </label>
  </div>

  <div class="form-zone">
  <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
    <div class="area-msg">
        <?php
        if(!empty($err_msg['name'])) echo $err_msg['name'];
        ?>
      </div>
    <p class="form-vali js-max-name">20文字以内で入力してください。</p>
    <input placeholder="Name*" class="input js-input-name" type="text" name="name" autocomplete="off" value="<?php if(!empty($_POST['name'])){ echo $_POST['name']; }else{ echo sanitize($dbUserData['name']); } ?>">
    <span class="focus-animation"></span>
  </label>
  </div>

  <div class="form-zone">
    <label class="<?php if(!empty($err_msg['about'])) echo 'err'; ?>">
        <div class="area-msg">
        <?php if(!empty($err_msg['about'])) echo $err_msg['about']; ?>
        </div>
    <textarea placeholder="About" class="input input-textarea" name="about" id="" cols="30" rows="5"><?php if(!empty($_POST['about'])){ echo $_POST['about']; }else{ echo sanitize($dbUserData['about']); } ?></textarea>
    </label>
  </div>

  <div class="btn-container">
    <button type="submit" class="input-btn">更新</button>
    <button href="mypage.php" class="input-btn cancel">戻る</button>
  </div>
</form>

</section>

</div>

<!-- footer -->
<?php
require('footer.php');
?>