<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
$debug_flg = true;
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime ', 60*60*24*30);
session_start();
if(time() > (filemtime("/var/tmp/") + 60*5)){
    session_regenerate_id(true);
}

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','250文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10','12文字以内で入力してください');
define('MSG11', 'その名前は既に登録されています');

//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();

//================================
// バリデーション関数
//================================

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
    if($str === ''){
      global $err_msg;
      $err_msg[$key] = MSG01;
    }
}
//バリデーション関数（namel重複チェック）
function validNameDup($name){
    global $err_msg;
        //例外処理
        try {
          // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT * FROM users  WHERE name = :name AND delete_flg = 0';
      $data = array(':name' => $name);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      // クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!empty($result)){
      $err_msg['name'] = MSG11;
      }
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
}
//バリデーション関数（Email形式チェック）
function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG02;
    }
  }
//バリデーション関数（Email重複チェック）
function validEmailDup($email){
global $err_msg;
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))){
        $err_msg['email'] = MSG08;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 250){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//バリデーション関数（nameの最大文字数チェック）
function validMaxLenName($str, $key, $max = 20){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
}
//バリデーション関数（半角チェック）
function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//パスワードチェック
function validPass($str, $key){
    //半角英数字チェック
    validHalf($str, $key);
    //最大文字数チェック
    validMaxLen($str, $key);
    //最小文字数チェック
    validMinLen($str, $key);
}
//エラーメッセージ表示
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//================================
// ログイン認証
//================================
function isLogin(){
    // ログインしている場合
    if( !empty($_SESSION['login_date']) ){
      debug('ログイン済みユーザーです。');

      // 現在日時が最終ログイン日時＋有効期限を超えていた場合
      if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('ログイン有効期限オーバーです。');

        // セッションを削除（ログアウトする）
        session_destroy();
        return false;
      }else{
        debug('ログイン有効期限以内です。');
        return true;
      }

    }else{
      debug('未ログインユーザーです。');
      return false;
    }
}

//=============================
//データベース接続
//=============================
function dbConnect(){
    //DBへの接続準備
    $dsn = 'mysql:dbname=pofori;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}
function queryPost($dbh, $sql, $data){
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
      debug('クエリに失敗しました。');
      debug('失敗したSQL：'.print_r($stmt,true));
      $err_msg['common'] = MSG07;
      return 0;
    }
    debug('クエリ成功。');
    return $stmt;
}

//poforiデータ取得
function getPofori($u_id){
debug('pofori情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM poforis WHERE user_id = :u_id ORDER BY id DESC';
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//poforiデータ取得
function getOnePofori($id){
debug('ひとつのpofori情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM poforis WHERE id = :id';
        $data = array(':id' => $id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//ユーザー情報取得
function getUser($u_id){
    debug('ユーザー情報を取得します。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT * FROM users  WHERE id = :u_id AND delete_flg = 0';
      $data = array(':u_id' => $u_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ結果のデータを１レコード返却
      if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
}

//すべてのpoforiデータ取得
function getAllPofori($currentPageNum, $span = 20){
debug('すべてのpofori情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM poforis ORDER BY id DESC';
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentPageNum;
        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

// 検索用ユーザーデータ
function getSearchUser($val) {
    debug('検索用のユーザー情報取得');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = "SELECT id,prof_image,name FROM users WHERE name LIKE '%$val%' LIMIT 8";
        // クエリ実行
        $stmt = $dbh->prepare($sql);
        if(!$stmt->execute()){
        debug('クエリに失敗しました。');
        debug('失敗したSQL：'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        }
        debug('クエリ成功。');

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

// 検索用poforiデータ
function getSearchPofori($val) {
    $split_val = str_split($val);
    $search_val = implode('%', $split_val);
    debug('検索用のpoforiデータ取得');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = "SELECT DISTINCT lang FROM poforis WHERE lang LIKE '%$search_val%' ORDER BY LENGTH(lang) ASC LIMIT 3";
        // クエリ実行
        $stmt = $dbh->prepare($sql);
        if(!$stmt->execute()){
        debug('クエリに失敗しました。');
        debug('失敗したSQL：'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        }
        debug('クエリ成功。');

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}
//userのお気に入りpoforiデータを取得
function getUsersFavo($u_id){
debug('userのお気に入り情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT pofori_id FROM favorite WHERE user_id = :u_id ORDER BY id DESC';
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//お気に入り情報取得
function getFavoPofori($u_id, $pofori_id){
debug('お気に入り情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM favorite WHERE user_id = :u_id AND pofori_id = :pofori_id';
        $data = array(':u_id' => $u_id, ':pofori_id' => $pofori_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//お気に入り情報取得
function getFavoCount($pofori_id){
debug('pofori_id指定のお気に入り情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM favorite WHERE pofori_id = :pofori_id';
        $data = array(':pofori_id' => $pofori_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
        return $stmt->fetchall();
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//===================================
//その他
//===================================
// サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}