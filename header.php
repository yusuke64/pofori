<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ポートフォリオ共有サービス 「POFORI」">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery.js"></script>
    <title>POFORI</title>
</head>
<body>
    <div class="header-wrap">
    <header class="header">
        <h1 class="site-title <?php if(!empty($_SESSION['user_id'])) echo 'login'; ?>"><a href="index.php">POFORI</a></h1>
        <div class="nav-lock">
            <?php if(empty($_SESSION['user_id'])){ ?>
            <a href="login.php"><i class="icon-lock fas fa-lock"></i></a>
            <?php }else{ ?>
            <a class="js-logout-modal" href="logout.php"><i class="icon-lock fas fa-lock-open"></i></a>
            <?php } ?>
        </div>
    <nav>
        <div class="top-nav">
            <?php if(empty($_SESSION['user_id'])){ ?>
            <div class="menu-trigger js-toggle-sp-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <?php } ?>
            <ul class="nav-menu">
                <li><a href="index.php">トップ</a></li>
                <?php if(empty($_SESSION['user_id'])){ ?>
                <li><a href="login.php">ログイン</i></a></li>
                <li><a href="signup.php">ユーザー登録</a></li>
                <?php }else{ ?>
                <li><a href="searchPage.php">探す</a></li>
                <li><a href="pofori.php">Pofori作成</a></li>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="logout.php">ログアウト</a></li>
                <?php } ?>
            </ul>
        </div>
        <?php if(empty($_SESSION['user_id'])){ ?>
            <ul class="nav-menu-sp js-toggle-sp-menu-target">
                <li><a href="index.php">トップ</a></li>
                <li><a href="login.php">ログイン</a></li>
                <li><a href="signup.php">ユーザー登録</a></li>
            </ul>
        <?php }else{ ?>
            <ul class="nav-login-menu">
                <li><a href="/pofori/index.php"><i class ="fas fa-home"></i></a></li>
                <li><a href="/pofori/searchPage.php"><i class ="fas fa-search"></i></a></li>
                <li><a href="/pofori/pofori.php"><i class ="far fa-plus-square"></i></a></li>
                <li><a href="/pofori/mypage.php"><i class ="far fa-user"></i></a></li>
            </ul>
        <?php } ?>
    </nav>
    </header>
    </div>
