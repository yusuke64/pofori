<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ユーザー登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)){

//変数にユーザー情報を代入
$name = $_POST['name'];
$email = $_POST['email'];
$pass = $_POST['pass'];
$pass_re = $_POST['pass_re'];

//未入力チェック
validRequired($name, 'name');
validRequired($email, 'email');
validRequired($pass, 'pass');
validRequired($pass_re, 'pass_re');

if(empty($err_msg)){
  //nameの最大文字数チェック
  validMaxLenName($name, 'name');
  //nameの重複チェック
  validNameDup($name);
  //emailの形式チェック
  validEmail($email, 'email');
  //emailの最大文字数チェック
  validMaxLen($email, 'email');
  //email重複チェック
  validEmailDup($email);

  //パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  //パスワードの最大文字数チェック
  validMaxLen($pass, 'pass');
  //パスワードの最小文字数チェック
  validMinLen($pass, 'pass');
}

if(empty($err_msg)){
    //パスワードとパスワード再入力が合っているかチェック
    validMatch($pass, $pass_re, 'pass_re');
}

if(empty($err_msg)){
    //例外処理
    try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'INSERT INTO users (name, about, email, password, created) VALUES(:name, :about, :email, :pass, :created)';
    $data = array(':name' => $name, ':about' => '', ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':created' => date('Y-m-d H:i:s'));
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if($stmt){
        debug('ユーザー登録しました。');
        //ログイン有効期限（デフォルトを１時間とする）
        $sesLimit = 60*60;
        // 最終ログイン日時を現在日時に
        $_SESSION['login_date'] = time();
        $_SESSION['login_limit'] = $sesLimit;
        // ユーザーIDを格納
        $_SESSION['user_id'] = $dbh->lastInsertId();

        debug('セッション変数の中身：'.print_r($_SESSION,true));

        header("Location:index.php"); //homeへ
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
  <h2 class="title">ユーザー登録</h2>
  <div class="area-msg">
    <?php
    if(!empty($err_msg['common'])) echo $err_msg['common'];
    ?>
  </div>

  <div class="form-zone">
  <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
    <div class="area-msg">
        <?php
        if(!empty($err_msg['name'])) echo $err_msg['name'];
        ?>
      </div>
    <p class="form-vali js-max-name">20文字以内で入力してください。</p>
    <p class="form-vali js-valid-name">その名前はすでに使用されています。</p>
    <input placeholder="名前*" class="input js-input-name" type="text" name="name" autocomplete="off" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>">
    <span class="focus-animation"></span>
  </label>
  </div>

  <div class="form-zone">
  <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
    <div class="area-msg">
        <?php
        if(!empty($err_msg['email'])) echo $err_msg['email'];
        ?>
      </div>
      <p class="form-vali js-valid-email">そのEmailアドレスはすでに使用されています。</p>
      <input placeholder="Email*" class="input js-input-email" type="email" autocomplete="off" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
    <span class="focus-animation"></span>
  </label>
  </div>

  <div class="form-zone">
  <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
    <div class="area-msg">
      <?php
      if(!empty($err_msg['pass'])) echo $err_msg['pass'];
      ?>
    </div>
    <input placeholder="パスワード(6文字以上)*" class="input" type="password" name="pass" class="pass" autocomplete="off" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
    <span class="focus-animation"></span>
  </label>
  </div>

  <div class="form-zone">
  <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
    <div class="area-msg">
      <?php
      if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
      ?>
    </div>
    <input placeholder="パスワード(再入力)*" class="input" type="password" name="pass_re" autocomplete="off" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
    <span class="focus-animation"></span>
  </label>
  </div>

  <div class="btn-container">
    <button type="submit" class="input-btn">登録</button>
  </div>
</form>

</section>

</div>

<!-- footer -->
<?php
require('footer.php');
?>
