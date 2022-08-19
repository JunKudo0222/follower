<?php
try {
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
	echo "接続OK!!!！"."\n";	
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}

//$users[]にDB accountsからとったスクリーンネームを保存していく

$stmt = $db->prepare("SELECT twitterid FROM accounts WHERE registry_datetime IS NUll");
		$res = $stmt->execute();
		if( $res ) {
			$ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		}
		
		$users=[];
		foreach($ids as $id){
		// $users[]=$id['twitterid'];
		$users[]=strtolower($id['twitterid']);//さくらサーバ以降時に、大文字小文字を判別するようになってしまったので全て小文字に変換。
	    }
       

if(count($users)==0){//registry_datetimeがnullのものがなくなったら、再取得開始
//DB
$stmt = $db -> prepare("UPDATE accounts SET registry_datetime = null ");//登録準備
$stmt -> execute();//データベースの登録を実行
//再度全取得
$stmt = $db->prepare("SELECT twitterid FROM accounts WHERE registry_datetime IS NUll");
		$res = $stmt->execute();
		if( $res ) {
			$ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		}
		
		$users=[];
		foreach($ids as $id){
		$users[]=$id['twitterid'];
	    }
//ここまで
}




require_once 'lib/twitteroauth.php';

define('CONSUMER_KEY', 'Xos2GlnvnVSUwRPTQ93Nxg40P');
define('CONSUMER_SECRET', 'JzG5rjNAKcwKLak1E2T4pVIDq98pqGIBBjeg5s0R7YNOPpLwiC');
define('ACCESS_TOKEN', '1509193639523926016-CYS2Ajkvo9zag6nJxe0EwF2vAnHKZd');
define('ACCESS_TOKEN_SECRET', 'aOUjOVtmyLVtcq6ZlREm4Rk3H9ggRjGRUIrDVAhY3z9zh');

$num=count($users);//DBから取ったユーザ数
$count=0;
$reqtime=0;
$requsers=[];//リクエストするユーザを保存していくカラム
while($num>$count){//DBから取った数が14以下だった場合に備える
    $toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
    //get the number of a users followers
    $user_details = $toa->get('users/show', array('screen_name'=>$users[$count]));
    $user_data = json_decode($user_details);
    $id=$user_data->id_str;
    
    $followers=$user_data->followers_count;
    $reqtime+=ceil($followers/5000);//リクエスト必要な回数

    echo $followers.'■'.ceil($followers/5000).'■'.$users[$count]."\n";
    echo $reqtime."\n";
    $requsers[]=$users[$count];
    
    if($reqtime>=15){//リクエスト必要回数が15に達したら終了
    // if($count>=134){//リクエスト必要回数が15に達したら終了
        break;
    }
    $count++;
}

if($reqtime>15){//15回目にフォロワー多いやつが来てリクエスト数が15を超えた場合の対処(最後のやつを削る)
    echo $requsers[$count].'←こいつを削除します'."\n";
    $trash=array_splice($requsers, $count, 1);
}



//最後に終了時刻を出力。
$unitime = time();
saigo($unitime, 'Asia/Tokyo');
function saigo($unix_timestamp, $tz){
    date_default_timezone_set($tz);
    $script_tz = date_default_timezone_get();
    $time=date('Y年m月d日 H:i:s', $unix_timestamp);
	
}

$saigo=date('Y年m月d日 H:i:s', time());


foreach($requsers as $requser){

    //DB
    $stmt = $db -> prepare("UPDATE accounts SET registry_datetime = :saigo WHERE twitterid = :requser");//登録準備
    $stmt -> bindValue(':saigo', $saigo, PDO::PARAM_STR);//登録する文字の型を固定
    $stmt -> bindValue(':requser', $requser, PDO::PARAM_STR);//登録する文字の型を固定
    $stmt -> execute();//データベースの登録を実行
    //ここまで
}
$users=[];//followdb.phpに渡すために名称変更
$users=$requsers;
