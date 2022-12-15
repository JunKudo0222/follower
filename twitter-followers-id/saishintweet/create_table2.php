<?php
ini_set('display_errors','On');
try {
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}

$stmt = $db->prepare("SELECT accountid FROM accounts");
$res = $stmt->execute();
if( $res ) {
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}
$accountids=[];
foreach($accounts as $account){
    $accountids[]=$account["accountid"];
    }
require_once '../lib/twitteroauth.php';
 
define('CONSUMER_KEY', 'ZG2JQgtpUEJ4kcJ3oRES4jcgO');
define('CONSUMER_SECRET', 'Pe8vaSdNhs1DUIoVV8J7ctZ5UZ7fBI7i1uGEdSNVLt4IdIHLtM');
define('ACCESS_TOKEN', '1509193639523926016-zN6BdwFeeXHJsfZGecEfBVsotqialh');
define('ACCESS_TOKEN_SECRET', 'lcmnABRo8NoCM36DsQC3ck40FHBgTn5ViLgfnlEG7Svxz');


$input=$_POST['username'];

//小文字に変更
$tablename=strtolower($input);


$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
//get the number of a users followers
$user_details = $toa->get('users/show', array('screen_name'=>$tablename));
$user_data = json_decode($user_details);
if($user_data->id_str){
if(in_array($user_data->id_str, $accountids)){
    echo "このアカウントは既に登録されています";
}else{
echo "入力情報:".$input."<br>";
echo "アカウントID:".$user_data->id_str."<br>";
echo "アカウント名:".$user_data->name."<br>";
echo "ユーザID:".$user_data->screen_name."<br>";
echo "フォロー数:".$user_data->friends_count."<br>";
echo "フォロワー数:".$user_data->followers_count."<br>";
echo "いいね数:".$user_data->favourites_count."<br>";
}}
else{
    echo "アカウントが存在しません";
}
?>
<br>
<?php if(!in_array($user_data->id_str, $accountids)){ ?>
この内容でテーブル作成しアカウントテーブルにも追記してよろしいですか？
<form method="post" action="create_table3.php">

<p><input type="hidden" name="input" size="30" value=<?= $input ?>></p>
<p><input type="hidden" name="accountid" size="30" value=<?= $user_data->id_str ?>></p>

<p>

<button type="submit"><big>追加する</big></button>
<?php } ?>
<button type="button" onclick="history.back()">戻る</button>
</p>

</form>
