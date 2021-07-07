<?php
session_start();

$_SESSION = array();
if(ini_get("session.use_cookies")){
    setcookie(session_name(),'',time() - 3600,'/');
}

session_destroy();

include(dirname(__FILE__) . '/screenSettings.php');
?>
<title>ログアウトページ</title>
</head>

<body>
<header>
        <h1><img src="img/headerIcon.png" width=50% height=10% alt="headerIcon"></h1>
        <div id="menu">
            <div class="menuIcon">
                <a href="#menu" class="menuopen"><img src="img/menuIcon.png" width="90%" height="90%"></a>
                <a href="#close" class="menuclose">CLOSE</a>  
            </div>
            <ul>
                <li><a href="index.php">キャンペーントップ</a></li>
                <li><a href="newMenberEntry.php">ユーザー登録</a></li>
                <li><a href="userLogin.php">ログイン</a></li>
            </ul>
        </div>
        
        <div id="header_hr">
            <hr size="2" width="98%" color="#426579">
            <hr size="1" width="95%" color="#426579">
        </div>  
    </header>

    <!--キャンペーンロゴタイトル-->
    <p id="title">
        <img src="img/title" width="100%">
    </p>
    
    <div id="logout">
        <h1>ログアウト完了</h1>
        <p>ログアウトしました<br>
            ご参加ありがとうございました。
        </p>
        <img src="img/loguout_fukro.png" width="50%">
    </div>
    
</body>
</html>

