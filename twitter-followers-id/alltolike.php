<?php
try {
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}

//現在の日付を出力。
$unitime = time();
now($unitime, 'Asia/Tokyo');
function now($unix_timestamp, $tz){
    date_default_timezone_set($tz);
    $script_tz = date_default_timezone_get();
    $time=date('Y-m-d H:i:s', $unix_timestamp);
	
}

$now=date('Y-m-d H:i:s', time());
$now=explode(' ',$now)[0];
$now=strtotime($now);

// exit;

//needlikeテーブル全ユーザーIDを一旦取得
// $stmt = $db->prepare("SELECT tweetid FROM need_to_like_users");
$stmt = $db->prepare("SELECT tweetid,registry_datetime FROM need_to_like_users");
$res = $stmt->execute();
if( $res ) {
    $needs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}
$remains=[];
foreach($needs as $need){
    $event_date=explode(" ",$need['registry_datetime'])[0];
    $event_date = str_replace('日', '', $event_date);  // "日"を空文字に置換する
    $event_date = str_replace('年', '-', $event_date); // "年"を"-"に置換する
    $event_date = str_replace('月', '-', $event_date); // "月"を"-"に置換する
    $event_date = strtotime($event_date);
    if($event_date>=$now-172800){//2日間(172800秒)以内に取得したものは表示し続ける
    // if($event_date>$now-518400){
        $remains[]=$need;

    }



}
// $i==0;
// foreach($remains as $remain){
foreach($needs as $need){//全て出力
    echo 'https://twitter.com/TwitterJP/status/'.$need['tweetid']."<br>";
// $i++;
// if($i>=950){
// exit;
// }
}
