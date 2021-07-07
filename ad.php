<?php
//videoフォルダの中からmp4動画ファイルの相対パスを全て取得
$videos = glob("video/*.mp4");
//要素数格納
$count = count($videos);

//０から要素数までの乱数取得
$number = mt_rand(0,$count-1);
//リストから乱数番目の相対パスを格納
$video = $videos[$number];
//video
switch($video){
    case "video/movie1.mp4":
        $header = "当店イチ押しの露天風呂！";
        $text = "当館には、川沿いに設置された露天風呂がございます。<br>
        渓流を眺めながら、川のせせらぎや季節に応じた色豊かな大自然と一緒に温泉を楽しめる露天風呂となっておりますので気になる方はぜひご来店ください！";
        break;
    case "video/video2.mp4":
        $header = "四季折々の景色！";
        $text = "春夏秋冬によって見られる景色も様々！<br>
        ぜひみなさんの目でこの景色を味わいませんか？";
        break;
    case "video/video3.mp4":
        $header = "お食事も華やかに<br>～ビュッフェレストラン～<br>はいかがですか？";
        $text = "コックさんたちが目の前で自慢の腕をふるいます。<br>
        出来立ての贅沢をお召し上がりください。";
        break;
    case "video/video4.mp4":
        $header = "今年リニューアルオープン！「足湯庭園」";
        $text = "四季折々の風情を感じる風景盆栽や情緒あふれる水のせせらぎのなか<br>
        ぬくもりを感じながら庭園散策はいかがでしょうか？";
        break;
    case "video/video5.mp4":
        $header = "館内のお部屋をご紹介！";
        $text = "動画を見てお楽しみください。<br>
        和室から洋室まで様々なお部屋をご用意しておりますのでご予約はお早めに！";
        break;
}
//echo($video);
include(dirname(__FILE__) . '/screenSettings.php');
?>

    <title>Advertising</title>
    
</head>
    <p id="title">
        <img src="img/title" width="100%">
    </p>
<body id="ad">
    <h1>当旅館のおすすめ</h1>
    <div id="movie">
        <p>※音が出ます</p>
        <!--ランダムに取得した動画の相対パス$videoをセット-->
        <video id="video" poster="img/movietop.jpg"
        src=<?php echo $video?> controls muted autoplay playsinline></video>
    </div>
    <div>
        <p class="h2">☆☆☆今回のオススメ☆☆☆</p>
        <p><?php echo $header?></p>
        <p><?php echo $text?></p>
    </div>


    
    <script>
        var movie = document.getElementById('video');
       
        movie.addEventListener('ended',function(){
           window.location.href = "https://web-network.sakura.ne.jp/games2020/scenario5/Game/index.html"; 
        });
    </script>
    

</body>
</html>