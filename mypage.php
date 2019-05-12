<?php

require('function.php');
require('viewsfunction.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//SESSION_IDを格納
$u_id = $_SESSION['user_id'];

//userデータを取得
$dbUserData = getUser($u_id);

//userのpoforiデータを取得
$dbPoforiData = getPofori($u_id);

//favoriteデータを取得
$dbFavoData = getUsersFavo($u_id);

?>
<?php require('header.php'); ?>

<section class="main js-toggle-opacity">
    <section class="mypage">
        <button class="creater"><img src="./profImage/<?php if(!empty($dbUserData['prof_image'])){ echo sanitize($dbUserData['prof_image']); }else{ echo 'default.png'; } ?>" alt="" class="creater-image"><p class="creater-name"><?php echo sanitize($dbUserData['name']); ?></p></button>
        <p class="prof-about"><?php echo sanitize($dbUserData['about']); ?></p>
        <a class="edit-btn" href="profEdit.php"><i class="fas fa-pen"></i></a>
    </section>
    <div class="select">
    <a class="term active pofori-btn" href="">Pofori</a><a href="" class="term reactive favorite-btn">Favorite</a>
    </div>

    <?php
    if(!empty($dbPoforiData)){
        viewPoforis($dbPoforiData, 1);
    }
    ?>

    <?php
    if(!empty($dbFavoData)){
        viewFavoPoforis($dbFavoData);
    }
    ?>

    </article>
</section>

<?php require('footer.php'); ?>