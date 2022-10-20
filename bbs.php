<?php

require_once 'function.php';

$errors = [];
$lines = [];

define('FILE_PATH', './bbs.txt');

$name = '';
$comment = '';
$date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $date = $_POST['date'];
        // バリデーションチェック
        // 入力チェック
        if (empty($name) || empty($comment)) {
            $errors[] = 'どちらか未入力です';
        }
        
        // 文字数チェック
        if (mb_strlen($name, 'UTF-8') > 20 ) {
    		$errors[] = '名前は20文字以内で入力してください。';
    	} elseif (mb_strlen($comment, 'UTF-8') > 100 ) {
    	    $errors[] = '一言は100文字以内で入力してください。';
    	}
    	
    	if (empty($errors)) {
        
            $fp = fopen(FILE_PATH, 'a');
            if ($fp !== FALSE) {
                $log = $name . ':' . $comment . " " . $date . "\n";
                $result = fwrite($fp, $log);
                if ($result === FALSE) {
                    $errors[] = '書き込み失敗、もう一度お試しください:  ' . $filename;
                }
                fclose($fp);
            }
    	}
    }
}

$lines = [];

if (is_readable(FILE_PATH) === TRUE) {

    $fp = fopen(FILE_PATH, 'r');
    if ($fp !== FALSE) {
        $text = fgets($fp);

        while ($text !== FALSE) {
            $lines[] = $text;
            $text = fgets($fp);
        }
        fclose($fp);
        $lines = array_reverse($lines);
    }
} else {
    $errors[] = 'ファイルがありません';
    return;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡易掲示板</title>
    
    <script>
    function twoDigit(num) {
      let ret;
      if( num < 10 ) 
        ret = "0" + num; 
      else 
        ret = num; 
      return ret;
    }
    function showClock() {
      let nowTime = new Date();
      let nowHour = twoDigit( nowTime.getHours() );
      let nowMin  = twoDigit( nowTime.getMinutes() );
      let nowSec  = twoDigit( nowTime.getSeconds() );
      let msg = "現在時刻：" + nowHour + ":" + nowMin + ":" + nowSec;
      document.getElementById("realtime").innerHTML = msg;
    }
    setInterval('showClock()',1000);
  </script>
</head>
<body>
    <h1>簡易掲示板</h1>
    <p id="realtime"></p>
    <p>一言どうぞ</p>
    <form action="" method="post">
        <div>
            <label for="name">お名前</label>
            <input type="text" name="name" value="<?php if(!empty($_POST['name'])) { echo h($_POST['name']); } ?>" />
        </div>
        <div>
            <label for="comment">一言</label>
            <input type="text" name="comment" value="<?php if(!empty($_POST['comment'])) { echo h($_POST['comment']); } ?>" />
        </div>
        <input type="hidden" name="date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
        <input type="submit" name="submit" value="書き込み" />
    </form>

    <?php if (isset($errors)) : ?>
        <?php foreach ($errors as $error) : ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <p>書き込み一覧</p>
    <?php foreach ($lines as $line): ?>
        <p><?php echo h($line); ?></p>
    <?php endforeach; ?>
</body>
</html>