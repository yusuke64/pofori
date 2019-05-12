<?php

//pofori表示
function viewPoforis($dbPoforiData, $my_page = null){
  foreach($dbPoforiData as $key => $val):
    $u_id = $val['user_id'];
    $dbUserData = getUser($u_id);

    if(!empty($_SESSION['user_id'])){
        $ses_u_id = $_SESSION['user_id'];
        $pofori_id = $val['id'];

        require('auth.php');

        $favoCount = count(getFavoCount($pofori_id));

        $dbFavoPofori = getFavoPofori($ses_u_id, $pofori_id);
    }
?>

  <section class="item <?php if(isset($my_page)){ echo 'my-item pofori'; } ?>">
    <form action="createrPage.php" class="creater-page" method="POST">
    <input type="hidden" name="id" value="<?php echo $dbUserData['id']; ?>">
    <button type="submit" class="creater"><img src="./profImage/<?php if(!empty($dbUserData['prof_image'])){ echo sanitize($dbUserData['prof_image']); }else{ echo 'default.png'; } ?>" alt="" class="creater-image"><p class="creater-name"><?php echo sanitize($dbUserData['name']); ?></p></button>
    </form>
    <?php if(isset($my_page)){ ?>
      <form action="deletePofori.php" class="deletePofori" method="POST">
          <input type="hidden" name="id" value="<?php echo $val['id']; ?>">
          <button class="aboutPofori-btn delete-pofori-btn" type="submit"><i class="fas fa-trash-alt"></i></button>
      </form>
      <form action="poforiEdit.php" class="editPofori" method="POST">
          <input type="hidden" name="id" value="<?php echo $val['id']; ?>">
          <button class="aboutPofori-btn edit-pofori-btn" type="submit"><i class="fas fa-pen"></i></span></button>
      </form>
    <?php } ?>
    <img src="./image/<?php echo sanitize($val['image']); ?>" alt="" class="item-img">
    <div class="item-about">
        <?php if(!empty($_SESSION['user_id'])){ ?>
        <input type="hidden" class="favo-data" data-pofori_id="<?php echo $val['id']; ?>">
        <button tyep="button" class="favo-btn item-like"><i class="fas fa-heart favo-icon <?php if(!empty($dbFavoPofori)) echo 'js-toggle-favo'; ?>"></i><span class="favo-num"><?php echo $favoCount; ?></span></button>
        <?php } ?>
        <p class="item-tech"><?php echo sanitize($val['lang']); ?></p>
        <p class="item-url"><a href="<?php echo sanitize($val['url']); ?>"><?php echo sanitize($val['url']); ?></a></p>
        <p class="item-description"><?php echo sanitize($val['description']); ?></p>
    </div>
  </section>

<?php endforeach;
}


//userのお気に入り表示
function viewFavoPoforis($dbFavoData){
    foreach($dbFavoData as $key => $val):
        $id = $val['pofori_id'];
        debug('お気に入り');
        $dbOneFavo = getOnePofori($id);
        $u_id = $dbOneFavo['user_id'];
        $dbUserData = getUser($u_id);

        $favoCount = count(getFavoCount($id));
    ?>

  <section class="my-item favo">
    <form action="createrPage.php" method="POST">
      <input type="hidden" name="id" value="<?php echo $dbUserData['id']; ?>">
      <button type="submit" class="creater"><img src="./profImage/<?php if(!empty($dbUserData['prof_image'])){ echo sanitize($dbUserData['prof_image']); }else{ echo 'default.png'; } ?>" alt="" class="creater-image"><p class="creater-name"><?php echo sanitize($dbUserData['name']); ?></p></button>
    </form>
    <img src="./image/<?php echo sanitize($dbOneFavo['image']); ?>" alt="" class="item-img">
    <div class="item-about">
      <input type="hidden" class="favo-data" data-pofori_id="<?php echo $dbOneFavo['id']; ?>">
      <button tyep="button" class="favo-btn item-like"><i class="fas fa-heart favo-icon js-toggle-favo"></i><span class="favo-num"><?php echo $favoCount; ?></span></button>
      <p class="item-tech"><?php echo sanitize($dbOneFavo['lang']); ?></p>
      <p class="item-url"><a href="<?php echo sanitize($dbOneFavo['url']); ?>"><?php echo sanitize($dbOneFavo['url']); ?></a></p>
      <p class="item-description"><?php echo sanitize($dbOneFavo['description']); ?></p>
    </div>
  </section>

<?php
endforeach;
}