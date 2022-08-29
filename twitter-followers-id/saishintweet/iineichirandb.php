<?php
//エンドポイントを指定
$base_url = 'https://api.twitter.com/2/users/1509193639523926016/liked_tweets';//シマウマ
// $base_url = 'https://api.twitter.com/2/users/1534759605187645440/liked_tweets';//テスト用


//ヘッダ生成
$token = 'AAAAAAAAAAAAAAAAAAAAAFpKbAEAAAAAqWjd4v5wzeu19sAK%2BtV9bRc1xQI%3Dwb1vk4j5yI02l626Ix4vMf6qUAK46KOO7Y1u48ivUli3BFy4ae';  //Bearer Token
$header = [
  'Authorization: Bearer ' . $token,
  'Content-Type: application/json',
];




try {
    $db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
    echo "接続OK!!!！";
} catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
    exit;
  }

//20220720開発用
// 日本時間を取得
$unitime = time();
dipslay_datetime($unitime, 'Asia/Tokyo');
function dipslay_datetime($unix_timestamp, $tz){
    date_default_timezone_set($tz);
    $script_tz = date_default_timezone_get();
    $time=date('Y年m月d日 H:i:s', $unix_timestamp);
	
}

$hiduke=date('Y年m月d日 H:i:s', time());

//ここまで


//いいね済ユーザを全取得し配列に保存。
$stmt = $db->prepare("SELECT twitterid FROM liked_users");
		$res = $stmt->execute();
		if( $res ) {
			$likeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		}
		
		$ids=[];
		foreach($likeds as $liked){
		$ids[]=$liked['twitterid'];
	    }
//ここまで

// var_dump(count($ids));


// exit;




$users=[];//ID一覧を保存する空の配列をセット

// while(true)//元は無限ループ(無限ループの時はスリープを解除せよ!!)久々にやるときのみ
// while(count($users)<1200){//取得数が7500に到達するまで(rate limit に引っかかるまで)継続
while(count($users)<100){//テスト用にミニマルで取得//@20220830 tweet取得数の月次キャップに引っかかったため&毎15分に処理走らせているためミニマルで大丈夫なので100に変更
    //ネクストトークンがあればそこから取得
    if(isset($next_token)){

        $query = [
            'pagination_token'=>$next_token,
            'expansions' => 'author_id',
            'user.fields' => 'name,username'
        ];
    }
    else{
        $query = [
            'expansions' => 'author_id',
            'user.fields' => 'name,username'
        ];

    }
    $url = $base_url . '?' . http_build_query($query);


//cURLで問い合わせ
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);


$result = json_decode($response, true);

curl_close($curl);

if(isset($result['data'])){
    
    $next_token=$result['meta']['next_token'];
    $data=$result['data'];
    
    foreach($data as $dat){
        $users[]=$dat['author_id'];
    }
}
else{
    break;
}

echo(count($users).'いいね取得完了');
// sleep(12);//7500しか取らないのでsleep挟む必要なし

}
$users=array_unique($users);
echo('トータルいいねユーザ数'.count($users)."\n");

//こっから、既存DBのいいね済みユーザーと比較し差分だけ洗い出し


$users=array_diff($users,$ids);//差分のみ残す
var_dump(count($users));
echo "\n".count($users).'件のユーザをいいね済みに保存していきます'."\n";

//こっから、差分のみをいいね済みに保存していく
foreach($users as $user){
  
    $stmt = $db -> prepare("INSERT INTO liked_users (twitterid,registry_datetime) VALUES(:user,:hiduke)");//登録準備
    $stmt -> bindValue(':user', $user, PDO::PARAM_STR);//登録する文字の型を固定
    $stmt -> bindValue(':hiduke', $hiduke, PDO::PARAM_STR);
    $stmt -> execute();//データベースの登録を実行
}









//こっから、一旦need_like_usersから全データを抜き出し、レコード全削除し、差分を再保存。

//need_to_like_usersユーザを全取得し配列に保存。
    $stmt = $db->prepare("SELECT twitterid FROM need_to_like_users");
            $res = $stmt->execute();
            if( $res ) {
                $needs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            }
            
            $needids=[];
            foreach($needs as $need){
            $needids[]=$need['twitterid'];
            }
//ここまで
echo 'ニードテーブル合計'.count($needids).'件'."\n";

//likedユーザを全取得し配列に保存。
    $stmt = $db->prepare("SELECT twitterid FROM liked_users");
            $res = $stmt->execute();
            if( $res ) {
                $likeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            }
            
            $likedids=[];
            foreach($likeds as $liked){
            $likedids[]=$liked['twitterid'];
            }
//ここまで

$doubleids=array_intersect($needids,$likedids);
echo 'ニードといいね済みで被った件数合計'.count($doubleids).'件'."\n";

foreach($doubleids as $doubleid){

        $stmt = $db -> prepare("DELETE FROM need_to_like_users WHERE twitterid = :doubleid");//登録準備
        $stmt -> bindValue(':doubleid', $doubleid, PDO::PARAM_STR);//登録する文字の型を固定
        $stmt -> execute();//データベースの登録を実行
}
echo count($doubleids).'件削除'."\n";


// //差分をneed_to_like_usersテーブルに保存
// foreach($needids as $needid){
  
//     $stmt = $db -> prepare("INSERT INTO need_to_like_users (twitterid) VALUES(:needid)");//登録準備
//     $stmt -> bindValue(':needid', $needid, PDO::PARAM_STR);//登録する文字の型を固定
//     $stmt -> execute();//データベースの登録を実行
// }

$db = NULL;//データベース接続を解除






