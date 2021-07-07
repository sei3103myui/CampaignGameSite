<?php
include(dirname(__FILE__) . '/screenSettings.php');
?>
<title>タイトル画面</title>
</head>
<body>
    <!--ヘッダー-->
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
    <div id="in">
        <h2>イベント説明</h2>
        <img src="img/index_1.png" width="100%">
        <p><img src="img/index_2.png" width="100%"></p>
        <img src="img/index_3.png" width="100%">
        <!--<img src="img/gnb.png"width="55" height="55"align="right"><br>-->
        <p>※イベント参加にはユーザー登録が必要です<br>
            ■ユーザー登録の流れ<br>
            １．ご登録にはメールアドレスとパスワードが必要です。<br><br>
            ２．その他必要事項を入力して登録後、入力したメールアドレスにメールが送信されますのでそちらに添付されるURLから認証をお願い致します。<br><br>
            ３．認証が終了次第キャンペーントップからログイン画面に入りメールアドレスとパスワードを入力してログインしてください<br><br>
            <font color="red">※メールが受信されない場合</font><br>
            <font color="red">迷惑メールに受信されている場合がありますので確認をお願い致します。</font><br>
        </p>
        <div align="center">
            <p>ユーザ登録がまだの方は↓から登録</p>
            <p class="entrybutton">
                <a href="newMenberEntry.php">ユーザー登録</a>
            </p>
        </div>
    
        <div align="center">
            <p>登録済みの方は↓からログイン</p>
                <p class="loginbutton">
                <a href="userLogin.php">ログイン</a></p>
            </div>
            
        </p>
    </div>
</body>
</html>