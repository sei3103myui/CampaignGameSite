<?php
session_start();
require_once('serverlogin.php');
if(isset($_POST['score'])){
    $score_1 = $_POST['score'];
}
if(!isset($_SESSION["login"])){


}else{
    

    $id = $_SESSION["login"];
    try{
        $pdo = new PDO($dsn, $db_user, $db_password);

        $sql = "SELECT name FROM `2020_4193321_user` WHERE id = :id AND status = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $name = $result['name'];
        
        $sql_rank = "SELECT highscore FROM `2020_4193321_ranking` WHERE user_id = :id";
        $stmt2 = $pdo->prepare($sql_rank);
        $stmt2->bindValue(':id',$id,PDO::PARAM_INT);
        $stmt2->execute();
       
        $rankingData = $stmt2->fetch();
        $highscore = $rankingData["highscore"];

        //ハイスコアよりスコアが高ければ
        if($score_1 > $highscore){
            $sql_addScore = "UPDATE `2020_4193321_ranking` SET highscore = :newScore WHERE user_id = :id";
            $stmt3 = $pdo->prepare($sql_addScore);
            $stmt3->bindValue(':newScore',$score_1,PDO::PARAM_INT);
            $stmt3->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt3->execute();
        }

    }catch(PDOException $e){
        echo $e;
    }
    $pdo = null;
}

include(dirname(__FILE__) . '/screenSettings.php');
?>
<title>リザルト</title>
</head>
<body id="result">
    <p id="title">
        <img src="img/title" width="100%">
    </p>
    <p><img src="img/resu.png" width="100%"></p>
    <div>
        <p class="scoretext">今回のスコア</p>
        <p class="score"><?php echo $score_1; ?>連勝</p>
        <p align="center"><img src="img/Tired.png" width="60%" height="25%"></p>
    </div>
    <div>
        <p class="scoretext"><?php echo $name; ?>様のハイスコア</p>
        <p class="highscore"><?php echo $highscore; ?>連勝</p>
    </div>
    
    <div align="center">
            <p>もう一度ゲームを遊びたい方は以下をクリック↓</p>
            <p class="entrybutton">
                <a href="Game/index.html">再挑戦</a>
            </p>
    </div>
    <div align="center">
            <p>ゲームをやめる方は以下をクリック↓</p>
            <p class="entrybutton">
                <a href="logout.php">ログアウト</a>
            </p>
    </div>
    <!--<p><a href="Game/index.html"><div align="center">
    <IMG SRC="img/buttun1.png" width="50%"></div></a></p>
    
    <div align="center">
    <a href="logout.php">
    <IMG SRC="img/buttun2.png" width="50%"></a></div></p>-->
    
</body>
</html>