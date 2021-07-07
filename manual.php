<?php

session_start();


include(dirname(__FILE__) . '/screenSettings.php');
?>
<title>ゲーム説明</title>
</head>
<body>
    <!--ヘッダー-->
    <header>
        <h1>
            <img src="img/headerIcon.png" width=50% height=10% alt="headerIcon">
        </h1>
        <div id="header_hr">
            <hr size="2" width="98%" color="#426579">
            <hr size="1" width="95%" color="#426579">
        </div>  
    </header>
    <div id="manual">
        <h1>遊び方説明</h1>
        <div class="carousel-wrapper">
            <span id="item-1"></span>
            <span id="item-2"></span>
            <span id="item-3"></span>
            <div class="carousel-item item-1">
                <img src="img/STEP1" width="100%" height="360px">
                <a class="arrow arrow-prev" href="#item-3"></a>
                <a class="arrow arrow-next" href="#item-2"></a>
            </div>
            <div class="carousel-item item-2">
                <img src="img/STEP2" width="100%">
                <a class="arrow arrow-prev" href="#item-1"></a>
                <a class="arrow arrow-next" href="#item-3"></a>
            </div>
            <div class="carousel-item item-3">
                <img src="img/STEP3" width="100%">
                <a class="arrow arrow-prev" href="#item-2"></a>
                <a class="arrow arrow-next" href="#item-1"></a>
            </div>
        </div>
        <div id="PlayB" align="center">
            <a href="Game/index.html">
                <img src="img/bottun3.png" width="80%">
            </a>
        </div>
    </div>
</body>
</html>