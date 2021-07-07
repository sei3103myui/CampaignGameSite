<?php

session_start();
require_once('serverlogin.php');

if(isset($_GET["pass"]) and isset($_GET["id"])){

    $pass = $_GET["pass"];
    $id = $_GET["id"];

    try{
        $pdo = new PDO($dsn, $db_user, $db_password);

        $sql_2 = "UPDATE `2020_4193321_user` SET pass=:pass WHERE  id=:id";
        $stmt2 = $pdo->prepare($sql_2);
        $stmt2->bindValue(':pass',$pass,PDO::PARAM_STR);
        $stmt2->bindValue(':id',$id,PDO::PARAM_STR);
        $stmt2->execute();
        $row_count_2 = $stmt2->rowCount();

        
        

        
    }catch (PDOException $e) {
        echo $e;
    }
    // 接続を閉じる
    $pdo = null;
    
}

if(isset($_POST['back'])){
    header('Location: userLogin.php');
}

if(isset($_POST['submit'])){
    
    //データ取得
    $email_2 = filter_var($_POST['email_2'],FILTER_VALIDATE_EMAIL);
    $password_1 = filter_var($_POST['password_1'],FILTER_SANITIZE_STRING);
    $password_2 = filter_var($_POST['password_2'],FILTER_SANITIZE_STRING);

    $errors = array();
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

    // エラーチェック
    if (0 < count($errors)) {
        // エラーメッセージを連結してアラート表示
        $alertMsg = "";
        foreach ($errors as $key => $value) {
            $alertMsg = $alertMsg . $value . '\n';
        }
        echo "<script type='text/javascript'>alert('" . $alertMsg . "');</script>";
    } else {
        // パスワードを復号不可な256bitのハッシュに変換
        $password_sha256 = hash('sha256', $password_1);

        try{
            //DB接続
            $pdo = new PDO($dsn, $db_user, $db_password);

            $sql_1 = "SELECT id,name FROM `2020_4193321_user` WHERE email = :email AND status=1";
            $stmt1 = $pdo->prepare($sql_1);
            $stmt1->bindValue(':email', $email_2, PDO::PARAM_STR);
            $stmt1->execute();
            $row_count = $stmt1->rowCount();
            $result = $stmt1->fetch();
            $id = $result['id'];
            $name = $result['name'];
            
            var_dump($name);
            if($row_count == 1){    

                //パスワード変更
                $to = $email_2;
                $subject = "パスワード変更をメール認証してください";
                $headers = $name . "様";
                $body = "以下のURLをクリックでパスワードの変更が完了します\n";
                $body .= "https://web-network.sakura.ne.jp/games2020/scenario5/psChange.php?pass=" . $password_sha256 . "&id=" . $id;
                mail($to, $subject, $body, $headers);
                var_dump("メールを送信しました");
            }else{
                var_dump("入力されたメールアドレスが間違っているか登録または本登録されていません");
            }
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        // 接続を閉じる
        $pdo = null;
    }
}

include(dirname(__FILE__) . '/screenSettings.php');
?>

    <title>PasswordChange</title>
</head>

<body >
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
    <div id="login">
    <form class="form" action="psChange.php" method="POST">
        <h1>パスワード変更</h1>
        <p>お客様のメールアドレスと新しいパスワードを入力してください</p>
        <div id="inputform">
            <p>メールアドレス<br>
            <input type="text" name="email_2" value="<?php echo $email_2?>"
            size="40" maxlength="50"
            oncopy="return false" onpaste="return false" oncontextmenu="return false">
            </p>
        </div>
        <div id="inputform">
            <p>新規パスワード<br>
                半角英数字4文字以上で入力してください<br>
                <input type="text" name="password_1" value="<?php echo $password_1?>"
                size="30" maxlength="50"
                oncopy="return false" onpaste="return false" oncontextmenu="return false">
                <br>
                もう一度入力してください<br>
                <input type="text" name="password_2" value="<?php echo $password_2?>"
                size="30" maxlength="50"
                oncopy="return false" onpaste="return false" oncontextmenu="return false">
            </p><br>
        </div>
        
        <div align="center">
                <p class="loginbutton">
                <input type="submit" name="submit" value="変更" 
                style="width:70%; height:50px; margin-top:7px; border:none; background-color: transparent; font-size: 20px;"></p>
        </div>
        <div align="center">
                <p class="loginbutton">
                <input type="submit" name="back" value="戻る" 
                style="width:70%; height:50px; margin-top:7px; border:none; background-color: transparent; font-size: 20px;"></p>
            </div>
        <!--<p><input type="submit" name="submit" value="変更"></p>
        <p><input type="submit" name="back" value="戻る"></p>-->
    </form>
    </div>
    
        
    
</body>

</html>