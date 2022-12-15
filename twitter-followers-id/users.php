<a href="index.php"><戻る></a>
<br>
<?php
try {
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}
$stmt = $db->prepare("SELECT twitterid FROM accounts");
$res = $stmt->execute();
if( $res ) {
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}

foreach($accounts as $account){
echo $account['twitterid'];
echo "<br>";
}