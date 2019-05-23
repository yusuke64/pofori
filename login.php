<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//===============================
//ログイン画面処理
//===============================
//post送信されていた場合
if(!empty($_POST)){

//変数にユーザー情報を代入
$email = $_POST['email'];
$pass = $_POST['pass'];
$pass_save = (!empty($_POST['pass_save'])) ? true : false;

//emailの形式チェック
validEmail($email, 'email');
//emailの最大文字数チェック
validMaxLen($email, 'email');

//パスワードの半角英数字チェック
validHalf($pass, 'pass');
//パスワードの最大文字数チェック
validMaxLen($pass, 'pass');
//パスワードの最小文字数チェック
validMinLen($pass, 'pass');

//未入力チェック
validRequired($email, 'email');
validRequired($pass, 'pass');

if(empty($err_msg)){
    debug('バリデーションokです。');

    //例外処理
    try{
    //dbへ接続
    $dbh = dbConnect();
    //sql文作成
    $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    debug('クエリ結果の中身：'.print_r($result,true));

    //パスワード照合
    if(!empty($result) && password_verify($pass,array_shift($result))){
        debug('パスワードがマッチしました。');

        //ログイン有効期限
        $sesLimit = 60*60;
        //最終ログイン日時を現在日時に
        $_SESSION['login_date'] = time();

        //ログイン保持にチェックがある場合
        if($pass_save){
        debug('ログイン保持にチェックがあります。');
        //ログイン有効期限を30日にしてセット
        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
        debug('ログイン保持にチェックがありません。');
        //次回からログイン保持しないので、ログイン有効期限を1時間後にセット
        $_SESSION['login_limit'] = $sesLimit;
        }
        //ユーザーIDを格納
        $_SESSION['user_id'] = $result['id'];

        debug('セッションの中身：'.print_r($_SESSION,true));
        debug('マイページへ遷移します。');
        header("Location:mypage.php");//マイページへ
    }else{
        debug('パスワードがアンマッチです。');
        $err_msg['common'] = MSG09;
    }
    }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
    }
}

}

?>
<?php require('header.php'); ?>

<section class="main js-toggle-opacity">
<form class="form login" action="" method="post">
    <h2 class="title">ログイン</h2>
        <div class="area-msg">
        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
        </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
        <div class="area-msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></div>
        <input placeholder="Email" class="input" type="email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
        <span class="focus-animation"></span>
        </label>
    </div>

    <div class="form-zone">
        <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
        <div class="area-msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></div>
        <input placeholder="パスワード" class="input" type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
        <span class="focus-animation"></span>
        </label>
    </div>

    <label for="check" class="pass-check">
        <i class="check-icon fas fa-check-square"></i>
        <input id="check" class="js-check" type="checkbox" name="pass_save" style="display: none;">ログイン保持
    </label>

    <button class="input-btn" type="submit" name="submit">ログイン</button>
</form>

<div class="guest-login">
    <form action="guestLogin.php">
        <button class="input-btn guest" type="submit" name="submit">ゲストとしてログインする</button>
    </form>
</div>
</section>

<?php require('footer.php'); ?>