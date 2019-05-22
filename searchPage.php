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
<script src="./js/vue.js"></script>
<script src="./js/axios.js"></script>

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

  <!-- pofori言語の検索結果 -->
  <div class="search-result-poforis">
    <form method="POST" action="" v-for="pofori in searchResultPoforis">
      <input type="hidden" name="search" :value="pofori.lang">
      <button type="submit" class="search-result-pofori">
      <p class="search-result-pofori-lang">{{ pofori.lang }}</p><i class="fas fa-arrow-circle-right"></i>
      </button>
    </form>
  </div><!-- search-result-poforis -->

  <!-- userの検索結果 -->
  <div class="search-result-users">
    <form method="POST" action="createrPage.php" v-for="user in searchResultUsers">
      <input type="hidden" name="id" :value="user.id">
      <button type="submit" class="search-result-user">
      <span class="search-result-image"><img :src="user.prof_image | image"></span>
      <span class="search-result-name">{{ user.name }}</span>
      </button>
    </form>
  </div><!-- search-result-users -->


  <?php
  //pofori表示
   if(!empty($dbPoforiData)){
     viewPoforis($dbPoforiData);
   }
  ?>
</section>

<?php
if(!empty($_POST['search'])){
  $encodeUserdata = json_encode($dbResultUserData);
  ?>
  <script>
    const dbUsersData = '<?php echo $encodeUserdata; ?>';
  </script>
  <?php
}else{ ?>
  <script>
    const dbUsersData = null;
  </script>
<?php } ?>

<script src="./js/search.js"></script>

<?php require('footer.php'); ?>