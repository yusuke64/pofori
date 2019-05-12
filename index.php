<?php
//共通変数・関数を読み込み
require('function.php');
require('viewsfunction.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('Home');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


$currentPageNum = (!empty($_GET['p'])) ? intval($_GET['p']) : 1;
debug('現在のページ: '.$currentPageNum);

$currentMinNum = (($currentPageNum-1)*20);
$dbPoforiData = getAllPofori($currentMinNum);
$poforiCount = count($dbPoforiData);
?>
<?php require('header.php'); ?>
<section class="main js-toggle-opacity">

    <?php
    if(!empty($dbPoforiData)){
        viewPoforis($dbPoforiData);
    }
    ?>

</section>

<?php if($poforiCount >= 20){ ?>

<form class="next-item" method="GET" action="">
    <input type="hidden" name="p" value="<?php echo $currentPageNum+1 ?>">
    <button class="next-item-btn" type="submit">もっと見る</button>
</form>

<?php } ?>

<?php require('footer.php'); ?>