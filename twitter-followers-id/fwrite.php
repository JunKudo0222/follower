<?php

try {
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
	echo "接続OK!!!！";	
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}


//needlikeテーブル全ユーザーIDを一旦取得
$stmt = $db->prepare("SELECT tweetid FROM need_to_like_users WHERE tweetid IS NOT NUll");//tweetidが入っている行のみ取得
$res = $stmt->execute();
if( $res ) {
	$needs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
}

$needids=[];
foreach($needs as $need){
$needids[]='https://twitter.com/TwitterJP/status/'.$need['tweetid'];
}
//ここまで




			$fp = fopen('likeusers.txt', 'r');
			
			// whileで行末までループ処理
			$txt=[];
			while (!feof($fp)) {

				// fgetsにてファイルポインタの位置から1行分を変数に格納
				$txt[] =str_replace(array("\r\n", "\r", "\n"), "",fgets($fp));

				// ファイルを読み込んだ変数を出力
			}

			// fcloseでファイルを閉じる
			fclose($fp);
			


	//Open file
	$file = fopen('likeusers.txt', 'a');

		
		
		
		//write to file
			$data=array_diff($needids,$txt);
            

			foreach($data as $dat){
				fwrite($file, $dat."\n"  );//fileはforeachでユーザごとに変わる
			}
	    
		
	
	fclose($file);