<?php

session_start();

function h($input) {
	return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

$msgs = array();
$text = '';

$token = $_POST['token'] ?? null;

try{
	$file = new SplFileObject('hogehoge.txt', 'cb+');

	if(isset($_POST['text']) && is_string($_POST['text'])) { //初回起動時に非表示
		$file->ftruncate(0);
		$file->fwrite($_POST['text']);
	} else {
        $already_file = file_exists('hogehoge.txt');
		$text = '';
		while (!$file->eof()) {
			$text .= $file->fgets();
		}
	}

} catch (Exception $e) {

	$m = 'エラー: ' . $e->getMessage();
	$msgs[] = '<div style="color:red;">' . h($m) .'</div>';

}

/*表示*/
if (isset($_SESSION['token']) ? $_SESSION['token'] === $token : false) {
    foreach ($file as $line) {
        if ($line == false) continue;
        echo "$line<br>", PHP_EOL;
    }
}


$msg = implode(PHP_EOL, $msgs) . PHP_EOL;
$_SESSION['token'] = bin2hex(random_bytes(32));
?>

/*------------------------------------------------------------------------*/
<!DOCTYPE html>
<head>
<meta charset="UTF-8" />
<title>サンプル</title>
</head>
<body>
<?php echo $msg; ?>
<form action="<?=basename($_SERVER['SCRIPT_NAME'])?>" method="post">


表示名
<div><input type="text" name="text" rows="10" cols="30"><br>
<?php echo h($text); ?>
</div>

一言メッセージ
<div><textarea id="t_message" name="text1" rows="10" cols="30">
<?php echo h($text); ?></textarea>
</div>

<div><input type="submit" value="送信" /></div>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']?>">
</form>
</body>
</html>
