<?php
session_start();
require_once('serverlogin.php');

$pickup = 50;//景品対象人数

if(isset($_POST['rank_up'])){
    try {
        //DBへ接続
        $pdo = new PDO($dsn, $db_user, $db_password);
        
        //testテーブルの全データを取得
        $sql_0 = 'SELECT * FROM `2020_4193321_ranking`;';
        $data_0 = $pdo->query($sql_0);

        if (!empty($data_0)) {
            foreach ($data_0 as $value_0) {
                // 1レコードずつ配列へ格納
                $result_0[] = array(
                    "user_id" => $value_0["user_id"],
                    "highscore" => $value_0["highscore"],
                    "ranking" => $value_0["ranking"],
                    "rank" => $value_0["rank"],
                    "discount" => $value_0["discount"]
                );
            }
        }
        //要素数取得
        $tablesNum = count($result_0);

        //testテーブルの要素分降順で取得
        $sql = 'SELECT * FROM `2020_4193321_ranking` ORDER BY highscore DESC LIMIT :num;';
        $data = $pdo->prepare($sql);
        $data->bindValue(':num',$tablesNum,PDO::PARAM_INT);
        $data->execute();
        
        if (!empty($data)) {
            foreach ($data as $value) {
                // 1レコードずつ配列へ格納
                $result[] = array(
                    "user_id" => $value["user_id"],
                    "highscore" => $value["highscore"],
                    "ranking" => $value["ranking"],
                    "rank" => $value["rank"],
                    "discount" => $value["discount"]
                );
            }
        }

         //ランキング更新処理
        $old_score = 0;
        $rank = 0;
        foreach ($result as $value) {
            // 点数が同じなら同じ順位とする
            if ($old_score != $value["highscore"]) {
                $old_score = $value["highscore"];
                $rank++;
            }
            $sql_addranking = "UPDATE `2020_4193321_ranking` SET ranking = :newranking WHERE user_id = :id";
            $stmt_0 = $pdo->prepare($sql_addranking);
            $stmt_0->bindValue(':newranking',$rank,PDO::PARAM_INT);
            $stmt_0->bindValue(':id',$value["user_id"],PDO::PARAM_INT);
            $stmt_0->execute();
        }
    
        //rank抽選処理
        $rankingNum = 1;//現在の終了数
        $rankingNumber = 1;//現在のランキング番号
        
        while($rankingNum <= $tablesNum){
            $sql_1 = 'SELECT * FROM `2020_4193321_ranking` WHERE ranking = :rankingNumber;';
            $data_1 = $pdo->prepare($sql_1);
            $data_1->bindValue(':rankingNumber',$rankingNumber,PDO::PARAM_INT);
            $data_1->execute();

            if (!empty($data_1)) {
                $result_1 = array();
                foreach ($data_1 as $value_1) {
                    // 1レコードずつ配列へ格納
                    $result_1[] = array(
                        "user_id" => $value_1["user_id"],
                        "highscore" => $value_1["highscore"],
                        "ranking" => $value_1["ranking"],
                        "rank" => $value_1["rank"],
                        "discount" => $value_1["discount"]
                    );
                }
                $dataNum = count($result_1);//現在のランキング番号がいくつあるか
            
                //同率順位がある場合ランダムに並び替え
                if($dataNum > 1){
                    shuffle($result_1);
                }
                foreach ($result_1 as $value_1) {
                   
                    //rank更新処理
                    $sql_rank = "UPDATE `2020_4193321_ranking` SET rank = :rankingNumber WHERE user_id = :id";
                    $stmt1 = $pdo->prepare($sql_rank);
                    $stmt1->bindValue(':rankingNumber',$rankingNum,PDO::PARAM_INT);
                    $stmt1->bindValue(':id',$value_1["user_id"],PDO::PARAM_INT);
                    $stmt1->execute();
                    $rankingNum++;
                }
                $rankingNumber++;
            }
        }
    } catch (PDOException $e) {
        echo $e->getMessage(); // エラー出力
        die(); // 接続終了
    }    
    // 接続を閉じる
    $pdo = null;
}

if(isset($_POST['discount'])){
    //ランキングデータベースから全参加者取得
    try {
        //DBへ接続
        $pdo = new PDO($dsn, $db_user, $db_password);
        
        //テーブルの全データを取得
        $sql_0 = 'SELECT * FROM `2020_4193321_ranking`;';
        $data_0 = $pdo->query($sql_0);
        
        if (!empty($data_0)) {
            foreach ($data_0 as $value_0) {
                // 1レコードずつ配列へ格納
                $result_0[] = array(
                    "user_id" => $value_0["user_id"],
                    "highscore" => $value_0["highscore"],
                    "ranking" => $value_0["ranking"],
                    "rank" => $value_0["rank"],
                    "discount" => $value_0["discount"]
                );
            }
            foreach($result_0 as $value_0){
                //割引率初期化
                $sql_initialize = "UPDATE `2020_4193321_ranking` SET discount = 0 WHERE user_id = :id";
                $stmt1 = $pdo->prepare($sql_initialize);
                $stmt1->bindValue(':id',$value_0["user_id"],PDO::PARAM_INT);
                $stmt1->execute();
            }
        }
        //要素数取得
        $tablesNum = count($result_0);
        
        $all = 0;
        var_dump($pickup);
        //参加者が50人いない場合
        if($tablesNum < $pickup){
           $all =  $tablesNum;
        }else{//参加者50人以上いる場合
           $all = $pickup;
        }
        //抽選済みランキングから50人取り出し
        $sql_1 = 'SELECT * FROM `2020_4193321_ranking` ORDER BY rank ASC LIMIT :all;';
        $data_1 = $pdo->prepare($sql_1);
        $data_1->bindValue(':all',$all,PDO::PARAM_INT);
        $data_1->execute();
         
        if (!empty($data_1)) {
            foreach ($data_1 as $value_1) {
                // 1レコードずつ配列へ格納
                $result_1[] = array(
                    "user_id" => $value_1["user_id"],
                    "highscore" => $value_1["highscore"],
                    "ranking" => $value_1["ranking"],
                    "rank" => $value_1["rank"],
                    "discount" => $value_1["discount"]
                );
            }
        }
        //割引率付与
        foreach($result_1 as $value){
            $thisRank = $value["rank"];//自分のランキング番号を取得
            if($all != $pickup){//参加者が景品対象人数より少ない場合
                $percent = floor($thisRank / $all * 100);//自分が全体の何パーセントか
                if($percent > 0 && $percent <= 20){
                    $discount = 30;
                }else if($percent > 20 && $percent <= 50){
                    $discount = 20;
                }else if($percent > 50 && $percent <= 100){
                    $discount = 10;
                }
            }else{
                if($thisRank > 0 && $thisRank <= 10){
                    $discount = 30;
                }else if($thisRank > 10 && $thisRank <= 25){
                    $discount = 20;
                }else if($thisRank > 25 && $thisRank <= 50){
                    $discount = 10;
                }
            }

            $sql_discount = "UPDATE `2020_4193321_ranking` SET discount = :discount WHERE user_id = :id";
            $stmt2 = $pdo->prepare($sql_discount);
            $stmt2->bindValue(':discount',$discount,PDO::PARAM_INT);
            $stmt2->bindValue(':id',$value["user_id"],PDO::PARAM_INT);
            $stmt2->execute();
        }   
    } catch (PDOException $e) {
        echo $e->getMessage(); // エラー出力
        die(); // 接続終了
    }
     // 接続を閉じる
     $pdo = null;  
}
//メール送信処理ここから
if(isset($_POST['email'])){
    try{
        $pdo = new PDO($dsn, $db_user, $db_password);
        
        //抽選済みランキングから50人取り出し
        $sql_0 = 'SELECT * FROM `2020_4193321_ranking` ORDER BY rank ASC LIMIT :pickup;';
        $data_0 = $pdo->prepare($sql_0);
        $data_0->bindValue(':pickup',$pickup,PDO::PARAM_INT);
        $data_0->execute();
        
        if (!empty($data_0)) {
            foreach ($data_0 as $value_0) {
                // 1レコードずつ配列へ格納
                $result_0[] = array(
                    "user_id" => $value_0["user_id"],
                    "highscore" => $value_0["highscore"],
                    "ranking" => $value_0["ranking"],
                    "rank" => $value_0["rank"],
                    "discount" => $value_0["discount"]
                );
            }
        }

        foreach($result_0 as $value_1){
            
            $sql_1 = 'SELECT * FROM `2020_4193321_user` WHERE id = :id and status = 1;';
            $data_1 = $pdo->prepare($sql_1);
            $data_1->bindValue(':id',$value_1["user_id"],PDO::PARAM_INT);
            $data_1->execute();
            if(!empty($data_1)){
                if($value_1["discount"] == 30){
                    $path = "https://web-network.sakura.ne.jp/games2020/scenario5/QR/qr_3.php";
                }else if($value_1["discount"] == 20){
                    $path = "https://web-network.sakura.ne.jp/games2020/scenario5/QR/qr_2.php";
                }else if($value_1["discount"] == 10){
                    $path = "https://web-network.sakura.ne.jp/games2020/scenario5/QR/qr_1.php";
                }
                $qr_code = "https://api.qrserver.com/v1/create-qr-code/?data=" . $path ."&size=100x100";
                foreach($data_1 as $value_2){
                    $result[] = array(
                        "id" => $value_2["id"],
                        "name" => $value_2["name"],
                        "email" => $value_2["email"],
                        "pass" => $value_2["pass"],
                        "age" => $value_2["age"],
                        "status" => $value_2["status"],
                        "url_token" => $value_2["url_token"],
                        "token_createtime" => $value_2["token_createtime"],
                        "updatetime" => $value_2["updatetime"],
                        "createtime" => $value_2["createtime"],
                        "ranking" => $value_2["ranking"]
                    );
                    // $to = $value_2["email"];
                    // $subject = "ご当選おめでとうございます。";
                    // $headers = "From: from@example.com";
                    // $body = "この度は温泉街D旅館の「フクローと湯めぐりじゃんけんゲーム」に\nご参加いただきありがとうございました。\n";
                    // $body .= "ランキングの集計をいたしましたところ\n";
                    // $body .= $value_2["name"] . "様が上位" . $pickup . "位以内にランクイン致しました！\n";
                    // $body .= "景品は当旅館で使用できるサービス券です。\n";
                    // $body .= $value_2["name"] . "様には" . $value_1["discount"] . "%のサービス券をご用意しております。\n";
                    // $body .= "★チケットQRコード\n";
                    // $body .= "<p><img src='".$qr_code."' alt='QRコード' /></p>";
                    // $body .= "\n上記チケットQRコードを画像保存していただくと便利です。\nお取り扱いには十分ご注意ください。";
                    // mail($to,$subject,$body,$headers);

                    mb_language("Japanese");
                    mb_internal_encoding("UTF-8");
                    //メール文
                    $to = $value_2["email"];
                    $subject = "ご当選おめでとうございます。";
                    
                    $text = "この度は温泉街D旅館の「フクローと湯めぐりじゃんけんゲーム」に\nご参加いただきありがとうございました。\n";
                    $text .= "ランキングの集計をいたしましたところ\n";
                    $text .= $value_2["name"] . "様が上位" . $pickup . "位以内にランクイン致しました！\n";
                    $text .= "景品は当旅館で使用できるサービス券です。\n";
                    $text .= $value_2["name"] . "様には" . $value_1["discount"] . "%のサービス券をご用意しております。\n";
                    $text .= "★チケットQRコード\n";
                    $text .= "<p><img src='".$qr_code."' alt='QRコード' /></p>";
                    $text .= "\n上記チケットQRコードを画像保存していただくと便利です。\nお取り扱いには十分ご注意ください。";
                    
                    $from = "温泉街D旅館キャンペーン事務局";
                    $from_mail = "onsen@web-network.sakura.ne.jp";
                    $from_name = "フクローと湯めぐりじゃんけんゲームキャンペーン事務局";
                    $header = '';
                    $header .= "Content-Type: text/plain \r\n";
                    $header .= "Return-Path: " . $from_mail . " \r\n";
                    $header .= "From: " . mb_encode_mimeheader($from) ." \r\n";
                    $header .= "Sender: " . mb_encode_mimeheader($from) ." \r\n";
                    $header .= "Reply-To: " . $from_mail . " \r\n";
                    $header .= "Organization: " . $from_name . " \r\n";
                    $header .= "X-Sender: " . $from_mail . " \r\n";
                    $header .= "X-Priority: 3 \r\n";

                    //メール送信
                    mb_send_mail( $to, $subject, $text, $header);
                }
                
            }else{
                
            }
        }
    }catch(PDOException $e){
        //エラー処理
        echo $e->getMessage();
        die();
    }
    $pdo = null;
}
include(dirname(__FILE__) . '/screenSettings.php');
?>
<title>ランキング</title>
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
        <img src="img/title.png" width="100%">
    </p>
    <form class="ranking" action="ranking.php" method="POST">
        <div>
            <h1>ランキング排出</h1>
            <p><input type="submit" name="rank_up" value="ランキング決定"></p>
            <h2>割引率振り分け</h2>
            <p><input type="submit" name="discount" value="割引率"></p>
            <h3>メール送信</h3>
            <p><input type="submit" name="email" value="メール送信"></p>
        </div>
    </form>
    
</body>
</html>
