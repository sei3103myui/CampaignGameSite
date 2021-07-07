<?php

//登録できたかどうか
$isEntry = false;

if(isset($_POST['back'])){
    header('Location: index.php');
}
//登録ボタンが押されたかチェック
if(isset($_POST['submit'])){
    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $age = filter_var($_POST['age'],FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'],FILTER_VALIDATE_EMAIL);
    $password_1 = filter_var($_POST['password_1'],FILTER_SANITIZE_STRING);
    $password_2 = filter_var($_POST['password_2'],FILTER_SANITIZE_STRING);

    //エラーチェック
    $errors = array();
    
    if($email == false){
        $errors['email'] = "正しいメールアドレスを入力してください";
    }
    if($password_1 == false || mb_strlen($password_1) < 4){
        $errors['password1_null'] = "一つ目のパスワードが入力されていないか4文字以下です";
    }
    if($password_2 == false || mb_strlen($password_2) < 4){
        $errors['password2_null'] = "二つ目のパスワードが入力されていないか4文字以下です";
    }

    if(!preg_match('/^[\w]+$/',$password_1)){
        $errors['password_1'] = "一つ目のパスワードは半角英数字で入力してください";
    }else if(preg_match('/^[a-zA-Z]+$/',$password_1)){
        $errors['password_1'] = "一つ目のパスワードに数字が含まれていません";
    }else if(preg_match('/^[\d]+$/',$password_1)){
        $errors['password_1'] = "一つ目のパスワードに英字が含まれていません";
    }
   
    if(!preg_match('/^[\w]+$/',$password_2)){
        $errors['password_2'] = "二つ目のパスワードは半角英数字で入力してください";
    }else if(preg_match('/^[a-zA-Z]+$/',$password_2)){
        $errors['password_2'] = "二つ目のパスワードに数字が含まれていません";
    }else if(preg_match('/^[\d]+$/',$password_2)){
        $errors['password_2'] = "二つ目のパスワードに英字が含まれていません";
    }
    if(strcmp($password_1,$password_2) != 0){
        $errors['password_not_equal'] = "入力された二つのパスワードが違います";
    }
    if($name == false){
        $errors['name'] = "名前が入力されていません";
    }
    if($age == false){
        $errors['name'] = "年代が入力されていません";
    }

    // エラーチェック
    if (0 < count($errors)) {
        // エラーメッセージを連結してアラート表示
        $alertMsg = "";
        foreach ($errors as $key => $value) {
            $alertMsg = $alertMsg . $value . '\n';
        }
            echo "<script type='text/javascript'>alert('".$alertMsg."');</script>";

    }else{
        //パスワードを複合不可な256bitのハッシュに変換
        $password_sha256 = hash('sha256',$password_1);

        require_once('serverlogin.php');
        //送信処理
        try{
            //DB接続
            $pdo = new PDO($dsn, $db_user, $db_password);

            //メールアドレスが既に登録されているか否か
            $sql1 = "SELECT id FROM `2020_4193321_user` WHERE email = :email AND status != 0";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->bindValue(':email',$email, PDO::PARAM_STR);
            $stmt1->execute();
            $row_count = $stmt1->rowCount();

            if($row_count == 0){
                // 現在時刻に基づいたユニークなIDを生成し256bitにハッシュ化する
                $url_token = hash('sha256', uniqid(rand(), 1));
                    
                //データの追加                
                $stmt2 = $pdo ->prepare("INSERT INTO `2020_4193321_user` (name,email,pass,age,status,url_token,token_createtime) VALUES (:name, :email, :pass, :age, 0, :url_token, now())");
                $stmt2->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt2->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt2->bindValue(':pass', $password_sha256, PDO::PARAM_STR);
                $stmt2->bindValue(':age', $age, PDO::PARAM_STR);
                $stmt2->bindValue(':url_token', $url_token, PDO::PARAM_STR);
                $stmt2->execute();
                
                mb_language("Japanese");
                mb_internal_encoding("UTF-8");
                //メール文
                $to = $email;

                $subject = "メールアドレス認証のお知らせ";

                $text = $name . "様\nこの度はフクローと湯めぐりじゃんけんゲームキャンペーンにご参加いただきありがとうございます\n";
                $text .= "会員登録を完了させるため、以下のURLをクリックしてメール認証を完了させてください\n";
                $text .= "★メール認証URL\n";
                $text .= "https://web-network.sakura.ne.jp/games2020/scenario5/userLogin.php?url_token=" . $url_token . "\n";
                $text .= "認証完了後キャンペーンサイトからログインをしてゲームにご参加いただけます\n";
                $text .= "★キャンペーントップURL\n";
                $text .= "https://web-network.sakura.ne.jp/games2020/scenario5/index.php\n";

                $from = "温泉街D旅館キャンペーン事務局";
                $from_mail = "@www243.sakura.ne.jp";
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

                $isEntry = true;
                
                echo "<script type='text/javascript'>alert('仮登録が完了しました。入力したメールアドレスから認証をお願い致します。');</script>";
                //var_dump("登録しました");
            }else{
                //var_dump("このメールアドレスは既に登録されています");
                echo "<script type='text/javascript'>alert('このメールアドレスは既に登録されています');</script>";
            }
                
        }catch(PDOException $e){
            var_dump($e->getMessage());
            die();
        }
        $pdo = null;
    }   
}

include(dirname(__FILE__) . '/screenSettings.php');
?>

    <title>newMember</title>
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
  
    <div id="newMenberEntry">
        <h2>ユーザー登録</h2>

        <form class="newMenberEntry" action="newMenberEntry.php" method="POST">       
            <p>登録者の情報を入力してください<br>
            ■ユーザー登録の流れ<br>
            １．ご登録にはメールアドレスとパスワードが必要です。<br><br>
            ２．その他必要事項を入力して登録後、入力したメールアドレスにメールが送信されますのでそちらに添付されるURLから認証をお願い致します。<br><br>
            ３．認証が終了次第キャンペーントップからログイン画面に入りメールアドレスとパスワードを入力してログインしてください<br><br>
            <font color="red">※メールが受信されない場合</font><br>
            <font color="red">迷惑メールに受信されている場合がありますので確認をお願い致します。</font><br>
            </p>
            <div id="inputform">
                <p>■登録者名<br>
                    <input type="text" name="name" value=<?php echo $name?>>
                </p>
            </div>
           
            <div id="inputform">
                <p>■年代を選択してください<br>
                <select name="age">
                <option value="10代"<?php echo $age?>>10代</option>
                <option value="20代"<?php echo $age?>>20代</option>
                <option value="30代"<?php echo $age?>>30代</option>
                <option value="40代"<?php echo $age?>>40代</option>
                <option value="50代"<?php echo $age?>>50代</option>
                <option value="60代"<?php echo $age?>>60代</option>
                <option value="70代以上"<?php echo $age?>>70代以上</option>
                </select>
                </p>
            </div>
            
            <div id="inputform">
                <p>■メールアドレス<br>
                <input type="text" name="email" value="<?php echo $email?>"
                size="30" maxlength="50" value=<?php echo $email ?> 
                oncopy="return false" onpaste="return false" oncontextmenu="return false">
                </p>
            </div>
            <div id="inputform">
                <p>■パスワード</p>
                <p>半角英数字4文字以上で入力してください<br>
                    <input type="text" name="password_1" value="<?php echo $password_1?>"
                    size="30" maxlength="50" value=<?php echo $password_1?>>
                    <br>
                    ※同じパスワードをもう一度入力してください<br>
                    <input type="text" name="password_2" value="<?php echo $password_2?>"
                    size="30" maxlength="50"
                    oncopy="return false" onpaste="return false" oncontextmenu="return false">
                </p>
                
            </div>
            <div align="center">
                <p class="loginbutton">
                <input type="submit" name="submit" value="登録" 
                style="width:70%; height:50px; margin-top:7px; border:none; background-color: transparent; font-size: 20px;"></p>
            </div>
            <!--<p><input type="submit" name="submit" value="登録" ><br></p>-->

            <div align="center">
                <p class="loginbutton">
                <input type="submit" name="back" value="戻る" 
                style="width:70%; height:50px; margin-top:7px; border:none; background-color: transparent; font-size: 20px;"></p>
            </div>
            <!--<p><input type="submit" name="back" value="戻る"></p>-->
        </form>
    </div>
    
    
    

</body>

</html>