<?php
session_start();
require_once('serverlogin.php');

if(isset($_GET["url_token"])){
    $url_token = ($_GET["url_token"]);
    
    try{
        $pdo = new PDO($dsn, $db_user, $db_password);

        //url_tokenが合致AND regist_compが0が0の未登録者AND仮登録日から24時間以内
        $sql = "SELECT id FROM `2020_4193321_user` WHERE url_token=(:url_token) AND status=0 AND token_createtime > now() - interval 24 hour";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':url_token', $url_token, PDO::PARAM_STR);
        $stmt->execute();
       
        //レコード件数取得
        $row_count = $stmt->rowCount();
        //var_dump($row_count);
        //24時間以内に仮登録され、本登録されていないトークンの場合は本登録完了とする
        if ($row_count == 1) {
            
            $result = $stmt->fetch();
            $id = $result["id"];

            // 本登録完了
            $sql2 = "UPDATE `2020_4193321_user` SET status=1 WHERE id=:id";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindValue(':id', $id, PDO::PARAM_STR);
            $stmt2->execute();

            //ランキングDBにid登録
            $sql_rank = "INSERT INTO `2020_4193321_ranking` (user_id,highscore) VALUES (:id, 0)";
            $stmt3 = $pdo->prepare($sql_rank);
            $stmt3->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt3->execute();

           $test_alert = "<script type='text/javascript'>alert('本登録が完了しました');</script>";
           echo $test_alert;
            
            //チュートリアルに移動
            $pdo = null;
            exit;
        }else{
            echo "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおして下さい。";
        }
    }catch(PDOException $e){
        echo $e;
    }
    $pdo = null;

}else if(isset($_POST['submit'])){
    //ログインここから

    //
    $email = filter_var($_POST['email'],FILTER_VALIDATE_EMAIL);
    $password_1 = filter_var($_POST['password_1'],FILTER_SANITIZE_STRING);

    //
    $errors = array();
    if(!isset($email)){
        $errors['email'] = "正しいメールアドレスを入力してください";
    }
    if($password_1 == false || mb_strlen($password_1) < 4){
        $errors['password1_null'] = "パスワードが入力されていないか4文字以下です";
    }
    if(!preg_match('/^[\w]+$/',$password_1)){
        $errors['password_1'] = "パスワードは半角英数字で入力してください";
    }else if(preg_match('/^[a-zA-Z]+$/',$password_1)){
        $errors['password_1'] = "パスワードに数字が含まれていません";
    }else if(preg_match('/^[\d]+$/',$password_1)){
        $errors['password_1'] = "パスワードに英字が含まれていません";
    }

    if(0 < count($errors)){
        //
        $alertMsg = "";
        foreach ($errors as $key => $value) {
            $alertMsg = $alertMsg . $value . '\n';
        }
        echo "<script type='text/javascript'>alert('" . $alertMsg . "');</script>";
    }else{
        //
        $password_sha256 = hash('sha256', $password_1);

        try{
            $pdo = new PDO($dsn, $db_user, $db_password);

            //
            $sql = "SELECT id FROM `2020_4193321_user` WHERE email = :email AND pass = :pass AND status = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email',$email,PDO::PARAM_STR);
            $stmt->bindValue(':pass',$password_sha256,PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            if($row_count == 1){
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                session_regenerate_id(TRUE);
                $_SESSION["login"] = $user_data['id'];
                //var_dump("ログイン成功");
                //var_dump($_SESSION["login"]);
                header('Location: manual.php');
                exit();
            }else{
                $test_alert = "<script type='text/javascript'>alert('このメールアドレスは本登録できていません');</script>";
                echo $test_alert;
            }
        }catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        // 接続を閉じる
        $pdo = null;
    }
}
include(dirname(__FILE__) . '/screenSettings.php');
?>

    <title>Memberlogin</title>
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
    <div id="login">
        <h2>ログイン</h2>
        <form class="form" action="userLogin.php" method="POST">
            <p>登録したメールアドレスとパスワードを入力してください</p>
            <div id="inputform">
                <p>メールアドレス<br>
                <input type="text" name="email" value="<?php echo $email?>"
                size="30" maxlength="50"
                oncopy="return false" onpaste="return false" oncontextmenu="return false">
                </p>
            </div>
            <div id="inputform">
                <p>パスワード<br>
                半角英数字4文字以上で入力してください<br>
                <input type="text" name="password_1" value="<?php echo $password_1?>"
                size="30" maxlength="50"
                oncopy="return false" onpaste="return false" oncontextmenu="return false">
                </p>
                <p>
                <a href="psChange.php">パスワードを忘れた場合</a>
                </p>
                
            </div>
        
            <div align="center">
                <p class="loginbutton">
                <input type="submit" name="submit" value="ログインして遊び方説明へ" 
                style="width:70%; height:50px; margin-top:7px; border:none; background-color: transparent; font-size: 15px;"></p>
            </div>
            <!--<p><input type="submit" name="submit" value="ログインして遊び方説明へ"></p>-->
        </form>
    </div>  
</body>
</html>



