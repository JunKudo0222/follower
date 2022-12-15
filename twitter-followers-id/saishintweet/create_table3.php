<?php
ini_set('display_errors','On');
//create_table2から受け取ったデータを保存
$user=$_POST["input"];//大文字含む
$tablename=strtolower($_POST["input"]);//小文字に変更
$id_str=$_POST["accountid"];//アカウントID

try {
$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
echo "接続OK!!!！";






// SQL作成
// $sql = 'ALTER TABLE '.$tablename.' MODIFY registry_datetime VARCHAR(20)'; //registry_datetimeカラムをdatetime型からvarcharに変更したときのsql
$sql = 'CREATE TABLE '.$tablename.' (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  twitterid VARCHAR(20),
  registry_datetime VARCHAR(20)
) engine=innodb default charset=utf8';

// SQL実行
$res = $db->query($sql);








$stmt = $db->prepare("INSERT INTO accounts (
    twitterid, accountid
) VALUES (
    :twitterid, :accountid
)");

$stmt->bindParam( ':twitterid', $user, PDO::PARAM_STR);
$stmt->bindParam( ':accountid', $id_str, PDO::PARAM_STR);

$res = $stmt->execute();


} catch (PDOException $e) {
  echo 'DB接続エラー！: ' . $e->getMessage();
  exit;
}

$db = NULL;//データベース接続を解除

echo '<br>';
echo '追加が完了しました。';
?>
<br>
<a href="../tsuika.php">入力画面へ戻る</a>
