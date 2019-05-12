<?php

require('function.php');
require('viewsfunction.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('検索ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST['search'])){
  debug('poforiを検索します');
  $search_input = $_POST['search'];
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = "SELECT * FROM poforis WHERE CONCAT(lang, description) LIKE '%$search_input%' ORDER BY id DESC";
    // クエリ実行
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute()){
      debug('クエリに失敗しました。');
      debug('失敗したSQL：'.print_r($stmt,true));
      $err_msg['common'] = MSG07;
    }
    debug('クエリ成功。');

    if($stmt){
      $dbPoforiData = $stmt->fetchall();
    }else{
      return false;
    }
  } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
  }

  debug('user検索します');
  $search_input = $_POST['search'];
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = "SELECT id,prof_image,name FROM users WHERE name LIKE '%$search_input%' ORDER BY id DESC";
    // クエリ実行
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute()){
      debug('クエリに失敗しました。');
      debug('失敗したSQL：'.print_r($stmt,true));
      $err_msg['common'] = MSG07;
    }
    debug('クエリ成功。');

    if($stmt){
      $dbResultUserData = $stmt->fetchall();
    }else{
      return false;
    }
  } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
  }

  if(empty($dbPoforiData) && empty($dbResultUserData)){
    $emptyMessage = '「'.$search_input.'」の検索結果はありませんでした。';
  }

}
?>
<?php require('header.php'); ?>
<style>
  body{
    background: #ebe9e920;
  }
</style>

<section class="main js-toggle-opacity">
  <form method="POST" action="" class="search-area">
    <i class ="fas fa-search"></i><input autocomplete="off" v-model="search" name="search" class="search-input" placeholder="キーワード検索" value="<?php if(!empty($_POST['search'])) echo sanitize($_POST['search']); ?>" type="search">
    <button class="search-btn" type="submit">検索</button>
  </form>

  <?php if(!empty($emptyMessage)){ ?>
  <p class="empty-msg-area"><?php echo $emptyMessage; ?></p>
  <?php } ?>

  <!-- 検索結果表示 -->
  <div class="search-result-poforis"></div>
  <div class="search-result-users">
  <?php
   if(!(empty($dbResultUserData))):
    foreach($dbResultUserData as $key => $val):
  ?>

  <form method="POST" action="createrPage.php">
    <input type="hidden" name="id" value="<?php echo $val['id']; ?>">
    <button type="submit" class="search-result-user">
      <span class="search-result-image"><img src="./profImage/<?php if(!empty($val['prof_image'])){ echo sanitize($val['prof_image']); }else{ echo 'default.png'; } ?>"></span>
      <span class="search-result-name"><?php echo sanitize($val['name']); ?></span>
    </button>
  </form>

  <?php
    endforeach;
    endif;
  ?>
  </div>


  <?php
  //pofori表示
   if(!empty($dbPoforiData)){
     viewPoforis($dbPoforiData);
   }
  ?>

</section>

<script src="./js/vue.js"></script>
<script src="./js/axios.js"></script>
<script src="./js/search.js"></script>

<?php require('footer.php'); ?>