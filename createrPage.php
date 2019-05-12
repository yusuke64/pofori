<?php

require('function.php');
require('viewsfunction.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('クリエイターページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//poforiId取得
$u_id = $_POST['id'];

if($u_id === $_SESSION['user_id']){
    header("Location: mypage.php");
}

//ユーザーデータ
$dbUserData = getUser($u_id);

//userのpoforiデータを取得
$dbPoforiData = getPofori($u_id);

?>
<?php require('header.php'); ?>
<section class="main js-toggle-opacity">
    <section class="mypage createrspage">
        <button class="creater"><img src="./profImage/<?php if(!empty($dbUserData['prof_image'])){ echo sanitize($dbUserData['prof_image']); }else{ echo 'default.png'; } ?>" alt="" class="creater-image"><p class="creater-name"><?php echo sanitize($dbUserData['name']); ?></p></button>
        <p class="prof-about"><?php echo sanitize($dbUserData['about']); ?></p>
    </section>
    <div class="select border">
    <p class="term" style="color: #2a5772;" href="">Poforis</p>
    </div>

    <?php
    if(!empty($dbPoforiData)){
        viewPoforis($dbPoforiData);
    }
    ?>
</section>


<?php require('footer.php'); ?>