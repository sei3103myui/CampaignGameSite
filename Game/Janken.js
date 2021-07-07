enchant();

// ブラウザバック禁止
history.pushState(null, null, null);
window.addEventListener('popstate', function (e) {
    history.pushState(null, null, null);
    window.alert('前のページに戻ることは出来ません。');
    return;
});

window.addEventListener('orientationchange', () => {
    let angle = screen && screen.orientation && screen.orientation.angle;
    if (angle === undefined) {
        angle = window.orientation; // iOS用
    }
    if(angle != 0){
        
    }else{
        
    }
});

window.onload = function () {

    core = new Core(680, 1020);
    core.fps = 30;
    core.frame = 0;

    // 勝ち(0)、あいこ(1)、負け(2)を判断
    core.number = 0;

    // ブラウザを開いてるときは値が保存される
    getScore = sessionStorage.getItem('score');
    getPoster = sessionStorage.getItem('POSTER');
    getAiko = sessionStorage.getItem('AIKO');
    getUpdate = sessionStorage.getItem('UPDATE');
    getPush = sessionStorage.getItem('PUSH');
    getFlag = sessionStorage.getItem('FLAG');

    if (getScore == null) getScore = 0;
    if (getPoster == null) getPoster = 0;
    if (getUpdate == null) getUpdate = 0;
    if (getAiko == null) getAiko = 0;

    // 連勝記録
    core.score = getScore;

    // 広告を見たか(見たら1になる)
    core.poster = getPoster;

    // 負けた時に更新されたら(そのタイミングで更新されたら1になる)
    core.update = getUpdate;

    // どれを押したかを判断
    core.push = getPush;

    // 相手が何を出したかを判断
    core.flag = getFlag;

    // あいこになったか
    core.aiko = getAiko;

    core.preload('gamescene_backimage.png', 'hukuro1.png', 'hukuro2.png', 'グー_1.png', 'チョキ_1.png', 'パー_1.png',
        'グー_2.png', 'チョキ_2.png', 'パー_2.png', 'lost.png', 'draw.png', 'victory.png', 'retry.png', 'result.png',
        'talk1.png', 'talk2.png', 'talk3.png', 'talk4.png',
        'janken.mp3', 'pon.wav', 'aikode.wav', 'sho.wav', 'kati.mp3', 'aiko.mp3', 'make.mp3');
    
    // ふくろうのセリフ
    se1 = new Audio('janken.mp3');
    se2 = new Audio('pon.wav');
    se3 = new Audio('aikode.wav');
    se4 = new Audio('sho.wav');

    // じゃんけんの歓声
    fan1 = new Audio('kati.mp3');
    fan2 = new Audio('aiko.mp3');
    fan3 = new Audio('make.mp3');

    core.onload = function () {

        // じゃんけんボタンをタッチしたか
        touch = false;
        // 負けた時に出るボタンを押したか
        button = false;
        // 音声のフェードアウト管理
        fade = false;
        // 最初だけのタッチ判定
        begin = false;

        core.rootScene.addEventListener('enterframe', function (e) {
            info.text = core.score + "連勝";
        });

        Janken();
    }
    core.start();
}

Janken = function () {

    // スタートの文字
    opening = new Label();
    opening.x = 30;
    opening.y = 470;
    opening.width = 800;
    opening.color = '#ffa500';
    opening.text = "TOUCH TO START!";
    opening.font = '70px sens-serif';

    // 背景の温泉
    background = new Sprite(680, 1020);
    background.image = core.assets['gamescene_backimage.png'];
    background.x = 0;
    background.y = 0;

    core.rootScene.addChild(background);
    core.rootScene.addChild(opening);

    // 文字列　連勝
    info = new Label();
    info.x = 260;
    info.y = 20;
    info.text = core.score + "連勝";
    info.font = '70px sens-serif';

    core.rootScene.addChild(info);

    // グー
    Rock = new Sprite(175, 169);
    Rock.image = core.assets['グー_2.png'];
    Rock.x = 50;
    Rock.y = 840;

    // チョキ
    Scissors = new Sprite(175, 164);
    Scissors.image = core.assets['チョキ_2.png'];
    Scissors.x = 250;
    Scissors.y = 840;

    // パー
    Paper = new Sprite(175, 175);
    Paper.image = core.assets['パー_2.png'];
    Paper.x = 460;
    Paper.y = 840;

    // じゃんけん出した後
    MoveOwl = new Sprite(580, 568);
    MoveOwl.image = core.assets['hukuro2.png'];
    MoveOwl.x = 40;
    MoveOwl.y = 95;

    // グーの旗
    RockFlag = new Sprite(160, 256);
    RockFlag.image = core.assets['グー_1.png'];
    RockFlag.x = 75;
    RockFlag.y = 140;
    RockFlag.rotation = 15;

    // チョキの旗
    ScissorsFlag = new Sprite(160, 246);
    ScissorsFlag.image = core.assets['チョキ_1.png'];
    ScissorsFlag.x = 75;
    ScissorsFlag.y = 140;
    ScissorsFlag.rotation = 15;

    // パーの旗
    PaperFlag = new Sprite(160, 265);
    PaperFlag.image = core.assets['パー_1.png'];
    PaperFlag.x = 75;
    PaperFlag.y = 140;
    PaperFlag.rotation = 15;

    // 負け
    lose = new Sprite(715, 1470);
    lose.image = core.assets['lost.png'];
    lose.x = 0;
    lose.y = 0;

    // リザルト
    result = new Sprite(465, 136);
    result.image = core.assets['result.png'];
    result.x = 110;
    result.y = 620;

    // リザルト(CMを観る前用)
    result2 = new Sprite(465, 136);
    result2.image = core.assets['result.png'];
    result2.x = 110;
    result2.y = 780;

    // リトライ
    retry = new Sprite(461, 135);
    retry.image = core.assets['retry.png'];
    retry.x = 110;
    retry.y = 620;

    core.rootScene.on('touchstart', function (e) {
        if (begin == false) {
            begin = true;
            se1.play();
            se1.pause();
            se3.play();
            se3.pause();
            Game();
            core.rootScene.removeChild(opening);
        }
    });

    Game = function(){

        if (core.update == 0) {

            judge = Math.ceil(Math.random() * 30);
            console.log(judge);

            // 立ち状態のフクロウ
            idleOwl = new Sprite(550, 438);
            idleOwl.image = core.assets['hukuro1.png'];
            idleOwl.x = 65;
            idleOwl.y = 220;

            core.rootScene.addChild(idleOwl);

            serif1 = function () {
                // じゃんけんセリフ
                talk1 = new Sprite(450, 90);
                talk1.image = core.assets['talk1.png'];
                talk1.x = 10;
                talk1.y = 160;
                talk1.rotation = -5;

                // あいこセリフ
                talk3 = new Sprite(450, 96);
                talk3.image = core.assets['talk3.png'];
                talk3.x = 10;
                talk3.y = 160;
                talk3.rotation = -5;

                if (core.aiko == 0) {
                    core.rootScene.addChild(talk1);

                    fan1.pause();
                    fan1.currentTime = 0;
                    fan2.pause();
                    fan2.currentTime = 0;

                    fade = false;

                    // フェードアウトした音声を戻す
                    fan2.volume = 1;
                    se1.play();
                }
                if (core.aiko == 1) {
                    core.rootScene.addChild(talk3);

                    fan1.pause();
                    fan1.currentTime = 0;
                    fan2.pause();
                    fan2.currentTime = 0;

                    fade = false;

                    // フェードアウトした音声を戻す
                    fan2.volume = 1;
                    se3.play();
                }
            }

            setTimeout(serif1, 1000);
          
            Panel1 = function () {

                Rock.addEventListener('touchstart', function () {

                    core.rootScene.removeChild(idleOwl);
                    core.push = 0;

                    if (judge < 11 && touch == false) {
                        setTimeout(Draw, 1000);
                        core.flag = 0;
                        touch = true;
                        serif3();
                        Left();
                    }
                    if (judge > 10 && judge < 21 && touch == false) {
                        setTimeout(Win, 1000);
                        core.flag = 1;
                        touch = true;
                        serif3();
                        Center();
                    }
                    if (judge > 20 && judge < 31 && touch == false) {
                        setTimeout(Lose, 1000);
                        core.update = 1;
                        sessionStorage.setItem('UPDATE', core.update);
                        core.flag = 2;
                        touch = true;
                        serif3();
                        Right();
                    }

                    sessionStorage.setItem('PUSH', core.push);
                    sessionStorage.setItem('FLAG', core.flag);

                    core.rootScene.removeChild(Scissors);
                    core.rootScene.removeChild(Paper);
                    core.rootScene.removeChild(cursor);
                    core.rootScene.removeChild(select);
                    core.rootScene.removeChild(talk1);
                    core.rootScene.removeChild(talk3);
                });

               core.rootScene.addChild(Rock);
            }

            Panel2 = function () {

                Scissors.addEventListener('touchstart', function () {

                    core.rootScene.removeChild(idleOwl);
                    core.push = 1;

                    if (judge < 11 && touch == false) {
                        setTimeout(Lose, 1000);
                        core.update = 1;
                        sessionStorage.setItem('UPDATE', core.update);
                        core.flag = 0;
                        touch = true;
                        serif3();
                        Left();
                    }
                    if (judge > 10 && judge < 21 && touch == false) {
                        setTimeout(Draw, 1000);
                        core.flag = 1;
                        touch = true;
                        serif3();
                        Center();
                    }
                    if (judge > 20 && judge < 31 && touch == false) {
                        setTimeout(Win, 1000);
                        core.flag = 2;
                        touch = true;
                        serif3();
                        Right();
                    }

                    sessionStorage.setItem('PUSH', core.push);
                    sessionStorage.setItem('FLAG', core.flag);

                    core.rootScene.removeChild(Rock);
                    core.rootScene.removeChild(Paper);
                    core.rootScene.removeChild(cursor);
                    core.rootScene.removeChild(select);
                    core.rootScene.removeChild(talk1);
                    core.rootScene.removeChild(talk3);
                });

                core.rootScene.addChild(Scissors);
            }

            Panel3 = function () {

                Paper.addEventListener('touchstart', function () {

                    core.rootScene.removeChild(idleOwl);
                    core.push = 2;

                    if (judge < 11 && touch == false) {
                        setTimeout(Win, 1000);
                        core.flag = 0;
                        touch = true;
                        serif3();
                        Left();
                    }
                    if (judge > 10 && judge < 21 && touch == false) {
                        setTimeout(Lose, 1000);
                        core.update = 1;
                        sessionStorage.setItem('UPDATE', core.update);
                        core.flag = 1;
                        touch = true;
                        serif3();
                        Center();
                    }
                    if (judge > 20 && judge < 31 && touch == false) {
                        setTimeout(Draw, 1000);
                        core.flag = 2;
                        touch = true;
                        serif3();
                        Right();
                    }

                    sessionStorage.setItem('PUSH', core.push);
                    sessionStorage.setItem('FLAG', core.flag);

                    core.rootScene.removeChild(Rock);
                    core.rootScene.removeChild(Scissors);
                    core.rootScene.removeChild(cursor);
                    core.rootScene.removeChild(select);
                    core.rootScene.removeChild(talk1);
                    core.rootScene.removeChild(talk3);
                });

                core.rootScene.addChild(Paper);
            }

            setTimeout(Panel1, 2000);
            setTimeout(Panel2, 2000);
            setTimeout(Panel3, 2000);

            serif2 = function () {
                // 文字列　↓
                cursor = new Label();
                cursor.x = 315;
                cursor.y = 770;
                cursor.text = "↓";
                cursor.font = '60px sens-serif';

                // 文字列　一つ選んでタッチ
                select = new Label();
                select.x = 105;
                select.y = 700;
                select.width = 800;
                select.text = "一つ選んでタッチ";
                select.color = '#ffa500';
                select.font = '60px sens-serif';

                core.rootScene.addChild(cursor);
                core.rootScene.addChild(select);
            }

            serif3 = function () {

                // ぽんセリフ
                talk2 = new Sprite(300, 110);
                talk2.image = core.assets['talk2.png'];

                // しょっセリフ
                talk4 = new Sprite(300, 117);
                talk4.image = core.assets['talk4.png'];

                if (core.aiko == 0) {
                    talk2.x = 330;
                    talk2.y = 130;
                    talk2.rotation = 5;
                    core.rootScene.addChild(talk2);
                    se2.play();
                }
                if (core.aiko == 1) {
                    talk4.x = 330;
                    talk4.y = 130;
                    talk4.rotation = 5;
                    core.rootScene.addChild(talk4);
                    se4.play();
                }
                core.rootScene.addChild(MoveOwl);
            }

            // 一つ選んでタッチを遅らせて
            setTimeout(serif2, 2000);

            // グーを相手が出したとき
            Left = function () {
                core.rootScene.addChild(RockFlag);
            }

            // チョキを相手が出したとき
            Center = function () {
                core.rootScene.addChild(ScissorsFlag);
            }

            // パーを相手が出したとき
            Right = function () {
                core.rootScene.addChild(PaperFlag);
            }

            // 勝ったとき
            Win = function () {

                victory = new Sprite(678, 1468);
                victory.image = core.assets['victory.png'];
                victory.x = 0;
                victory.y = 0;

                core.aiko = 0;
                sessionStorage.setItem('AIKO', core.aiko);

                core.score++;
                core.number = 0;

                core.rootScene.removeChild(talk2);
                core.rootScene.removeChild(talk4);

                sessionStorage.setItem('score', core.score);

                core.rootScene.addChild(victory);
                fan1.play();

                setTimeout('Rush();', 2000);
            }

            // あいこのとき
            Draw = function () {

                draw = new Sprite(747, 1468);
                draw.image = core.assets['draw.png'];
                draw.x = 0;
                draw.y = 0;

                core.rootScene.removeChild(talk2);
                core.rootScene.removeChild(talk4);

                core.aiko = 1;
                sessionStorage.setItem('AIKO', core.aiko);

                core.number = 1;
                sessionStorage.setItem('score', core.score);

                core.rootScene.addChild(draw);
                fan2.play();

                setTimeout('Rush();', 2000);
            }

            // 負けたとき
            Lose = function () {

                core.aiko = 0;
                sessionStorage.setItem('AIKO', core.aiko);

                core.number = 2;
                sessionStorage.setItem('score', core.score);

                core.rootScene.addChild(lose);
                core.rootScene.removeChild(talk2);
                core.rootScene.removeChild(talk4);
                fan3.play();

                Commercial();
            }
        }

        // 負けをなしにする更新を防ぐ
        if (core.update == 1) {
            core.rootScene.addChild(MoveOwl);

            core.aiko = 0;
            core.number = 2;
            sessionStorage.setItem('score', core.score);

            // タッチ場所を保存
            if (core.push == 0) core.rootScene.addChild(Rock);
            if (core.push == 1) core.rootScene.addChild(Scissors);
            if (core.push == 2) core.rootScene.addChild(Paper);

            // 相手の旗を保存
            if (core.flag == 0) core.rootScene.addChild(RockFlag);
            if (core.flag == 1) core.rootScene.addChild(ScissorsFlag);
            if (core.flag == 2) core.rootScene.addChild(PaperFlag);

            core.rootScene.addChild(lose);
            Commercial();
        }
    }
}

Rush = function () {

    if (core.number == 0) {
        core.rootScene.removeChild(MoveOwl);
        core.rootScene.removeChild(victory);
        Banish();
    }

    if (core.number == 1) {
        core.rootScene.removeChild(MoveOwl);
        core.rootScene.removeChild(draw);
        Banish();
    }

    if (core.number == 2) {
        core.rootScene.removeChild(MoveOwl);
        core.rootScene.removeChild(lose);
        core.rootScene.removeChild(retry);
        core.rootScene.removeChild(result);
        Banish();
    }
}

Commercial = function () {

    if (core.poster == 0) {
        core.rootScene.addChild(retry);
        core.rootScene.addChild(result2);
        MoveOwl.y = 20;
        RockFlag.y = 65;
        ScissorsFlag.y = 65;
        PaperFlag.y = 65;

        retry.addEventListener('touchstart', function (e) {
            if (button == false) {
                window.location.href = 'https://web-network.sakura.ne.jp/games2020/scenario5/ad.php'; // CMへ遷移
            }
            button = true;
            core.poster = 1;
            core.update = 0;
            sessionStorage.setItem('POSTER', core.poster);
            sessionStorage.setItem('UPDATE', core.update);
        });

        result2.addEventListener('touchstart', function(){
            if (button == false) {
                var $f = $('<form method="POST" action="../result.php"></form>');
                $f.append('<input type="hidden" name="score" value=' + core.score + '>').appendTo($('body:first'));
                $f.submit();
            }
            button = true;
            core.poster = 0;
            core.update = 0;
            core.score = 0;

            sessionStorage.setItem('POSTER', core.poster);
            sessionStorage.setItem('UPDATE', core.update);
            sessionStorage.setItem('score', core.score);
        });
    }
    if (core.poster == 1) {
        core.rootScene.addChild(result);
        MoveOwl.y = 20;
        RockFlag.y = 65;
        ScissorsFlag.y = 65;
        PaperFlag.y = 65;

        result.addEventListener('touchstart', function (e) {
            if (button == false) {
                var $f = $('<form method="POST" action="../result.php"></form>');
                $f.append('<input type="hidden" name="score" value=' + core.score + '>').appendTo($('body:first'));
                $f.submit();
            }
            button = true;
            core.poster = 0;
            core.update = 0;
            core.score = 0;

            sessionStorage.setItem('POSTER', core.poster);
            sessionStorage.setItem('UPDATE', core.update);
            sessionStorage.setItem('score', core.score);
        });
    }
}

Banish = function () {
    // タッチ場所を消す
    if (core.push == 0) core.rootScene.removeChild(Rock);
    if (core.push == 1) core.rootScene.removeChild(Scissors);
    if (core.push == 2) core.rootScene.removeChild(Paper);

    // 相手の旗を消す
    if (core.flag == 0) core.rootScene.removeChild(RockFlag);
    if (core.flag == 1) core.rootScene.removeChild(ScissorsFlag);
    if (core.flag == 2) core.rootScene.removeChild(PaperFlag);

    fade = true;
    setInterval("FadeOut();", 100);

    Game();
    touch = false;
    button = false;
}

FadeOut = function () {
    // あいこの歓声の音量
    var vl = fan2.volume;
    if (vl > 0 && fade == true) {
        fan2.volume = Math.floor((vl - 0.1) * 10) / 10;
    }
}

// HTMLフォームの形式にデータを変換する
function EncodeHTMLForm(data) {
    var params = [];
    for (var name in data) {
        var value = data[name];
        var param = encodeURIComponent(name) + '=' + encodeURIComponent(value);
        params.push(param);
    }
    return params.join('&').replace(/%20/g, '+');
}