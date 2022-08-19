<?php
/**
 * Gets the User IDs of of the followers of a Twitter user
 * @author aphoe <http://www.github.com/aphoe>
 * @date Oct 2015
 */

require_once 'saishintweet/iineichirandb.php';
echo 'いいね一覧合計'.count($likedids).'件'."\n";

try {
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');
	echo "接続OK!!!！";	
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}



require_once 'followernumber.php';






require_once 'lib/twitteroauth.php';
 
define('CONSUMER_KEY', 'Xos2GlnvnVSUwRPTQ93Nxg40P');
define('CONSUMER_SECRET', 'JzG5rjNAKcwKLak1E2T4pVIDq98pqGIBBjeg5s0R7YNOPpLwiC');
define('ACCESS_TOKEN', '1509193639523926016-CYS2Ajkvo9zag6nJxe0EwF2vAnHKZd');
define('ACCESS_TOKEN_SECRET', 'aOUjOVtmyLVtcq6ZlREm4Rk3H9ggRjGRUIrDVAhY3z9zh');

/*
Do not edit beyond this pointer
Unless you know what exactly you are doing
*/



$sleep_counter = 1; //skip one to be used to get no of user's followers//多数ユーザにリクエスト送るため、スリープカウンタのみwhileの外に出す
foreach($users as $user){
	
	//初回かどうかの判定トリガーを保存。
	$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); 
	$shokais = $db->query('SELECT COUNT(*) as cnt FROM '.$user.'');
	$shokai = $shokais->fetch();
	//ちゃんとテーブルを照合できているかのチェック
	// var_dump($shokai);
	// exit;
	//ここまで
	
	$cursor = -1; //Initialize
	
	
	$loop_counter = 0; //Total loops in the while block
	
	
	while($cursor != 0){	
		$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
		//get the number of a users followers
		$user_details = $toa->get('users/show', array('screen_name'=>$user));
		$user_data = json_decode($user_details);
		//Get followers
		$followers = $toa->get('followers/ids', array('cursor' => $cursor, 'screen_name'=>$user));
		//$friends = $toa->get('friends/ids', array('cursor' => -1));

		//Convert JSON returned to array
		$data = json_decode($followers);
		//Get next cursor pointer
		$cursor = $data->next_cursor_str;//カーソルを事前に定義
		echo $cursor."\n";
		
		
		$dataids=$data->ids;
		$shinki=[];
		foreach($dataids as $dataid){
			$shinki[]=(string)$dataid;//文字列に変換

		}
		
		echo '→'.count($shinki).'(APIで撮った件数の数)'."\n";
					
		$stmt = $db->prepare("SELECT twitterid FROM $user");
		$res = $stmt->execute();
		if( $res ) {
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		}
		echo 'データベースに保存してあった件数は'.count($data).'件'."\n";
		$ids=[];
		foreach($data as $dat){
		$ids[]=$dat['twitterid'];
		}
		$data=array_diff($shinki,$ids);
		$data=array_reverse($data);//フォローされた順にDBに保存するため
		


		//need-to-likeとユーザーのテーブルに保存。

		//まずはユーザテーブルに保存
		foreach($data as $dat){

			$stmt = $db -> prepare("INSERT INTO $user (twitterid,registry_datetime) VALUES(:dat,:hiduke)");//登録準備
			$stmt -> bindValue(':dat', $dat, PDO::PARAM_STR);//登録する文字の型を固定
			$stmt -> bindValue(':hiduke', $hiduke, PDO::PARAM_STR);//登録する文字の型を固定
			$stmt -> execute();//データベースの登録を実行


		}
		echo "\n".$user_data->name.'('.$user.')の新規フォロワ数は' . count($data) . '件'."\n";
		echo count($data) . '件追加します'."\n";

		if($shokai['cnt']!=0){//初回じゃない場合のみ
			if(count($data)>1000){//サーバで挙動がおかしいためデバッグ(初回じゃないのに1000超えてる時のみ。)
				echo "\n".'データ数が'.$shokai['cnt'].'件なので終了します';//デバッグ用の文言
				exit;
			}


		//needtolikeテーブルにアクセスし、diff部分のみ挿入していく
		//まずはneedを全取得

		//needlikeテーブル全ユーザーIDを一旦取得
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



		
		$data=array_diff($data,$needids);//ユーザのフォロワー差分をさらに既存ニード(最初に取得した件)との差分で削ぎ落としてから新しくニードに保存

		echo '既存ニードと被らなかった件数は' . count($data) . '件'."\n";
		
		//次にシマウマを全取得
		$stmt = $db->prepare("SELECT twitterid FROM shimaumaen");
		$res = $stmt->execute();
		if( $res ) {
			$shimaumas = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		}
		
		$ids=[];
		foreach($shimaumas as $shimauma){
			$ids[]=$shimauma['twitterid'];
	    }
		
		$data=array_diff($data,$ids);//ユーザのフォロワー差分をさらにシマウマとの差分で削ぎ落としてから新しくニードに保存
		echo '既存ニードとシマウマと被らなかった件数は' . count($data) . '件'."\n";
		$data=array_diff($data,$likedids);//ユーザのフォロワー差分をさらにいいね済との差分で削ぎ落としてから新しくニードに保存
		echo '既存ニードとシマウマといいね済みと被らなかった件数は' . count($data) . '件'."\n";


		


		//このneed_to_like_usersに保存する直前にツイート判定も行う！(元々iinesagyouでやっていたロジック)
							$userids=[];//need_to_like_usersのtwitteridカラム
							$tweetids=[];//need_to_like_usersのtweetidカラム
							require_once 'saishintweet/renkeitest.php';
							foreach($data as $dat){     
								//ここから開発テスト@20220715
								
								$statuses = $connect->get(
									'statuses/user_timeline',
									// 取得するツイートの条件を配列で指定
									array(
										// ユーザー名（@は不要）
										'user_id'       => $dat,//followdb.phpから取り込んでいる
										// ユーザーオブジェクト除外
										// 'trim_user'       => 'true',
										'trim_user'       => 'false',
										// ツイート件数
										// 'count'             => '3',
										// リプライを除外するかを、true（除外する）、false（除外しない）で指定
										'exclude_replies'   => 'true',
										// リツイートを含めるかを、true（含める）、false（含めない）で指定
										'include_rts'       => 'false'
									)
								);//一旦リプライ除外して取得し、いいね0が存在するか確認。
								
								//ここまで@20220715
								
								//ここから開発テスト行
								// var_dump($statuses);
								// exit;
								//ここまで開発テスト行
								
								$favoritecount=[];
								$count=-1;
								$all=1;//全取得ツイート巡回したかの確認
								
								if(!isset($statuses->error)&&!isset($statuses->errors)){//鍵垢でないかどうかのチェック。鍵垢はスルー
								foreach($statuses as $statuse){
									$count++;
									if($statuse->favorite_count===0){
										$all=0;
										break;
									}
								$favoritecount[]=$statuse->favorite_count;
								}
								}
								if($all===1&&count($favoritecount)!==0){//いいね数0がなかった場合
									
									$mins   = array_keys($favoritecount, min($favoritecount)); // 値が最小の要素を抜き出す
									$count = $mins[0]; // 最初に出現した最大値のキー名を返す
								}
								
								
								if($count!=-1){
								
									$userids[]=(string)$statuses[$count]->user->id;
									$tweetids[]=(string)$statuses[$count]->id;
									// print_r($statuses[$count]->id.",".$statuses[$count]->user->name.",".$statuses[$count]->user->screen_name.",follow,".$statuses[$count]->user->friends_count.",follower,".$statuses[$count]->user->followers_count.",tweets,".$statuses[$count]->user->statuses_count."\n");
									print_r("https://twitter.com/TwitterJP/status/".$statuses[$count]->id."\n");
								}
								}
		//ここまで


		//最後にneed to likeにデータ追加。(同時にテキストファイルにも追加)
		$count=0;
		$num=count($userids);//ユーザ数を保存
		$file = fopen('/home/azurefennec478/www/shimaumaen.com/follower/twitter-followers-id/likeusers.txt', 'a');//書き込みファイルを指定
		while($count<$num){//カウントがユーザ数を上回らない限り

			$userid=$userids[$count];
			$tweetid=$tweetids[$count];

			$stmt = $db -> prepare("INSERT INTO need_to_like_users (twitterid,tweetid,registry_datetime) VALUES(:userid,:tweetid,:hiduke)");//登録準備
			$stmt -> bindValue(':userid', $userid, PDO::PARAM_STR);//登録する文字の型を固定
			$stmt -> bindValue(':tweetid', $tweetid, PDO::PARAM_STR);//登録する文字の型を固定
			$stmt -> bindValue(':hiduke', $hiduke, PDO::PARAM_STR);//登録する文字の型を固定
			$stmt -> execute();//データベースの登録を実行
			fwrite($file, 'https://twitter.com/TwitterJP/status/'.$tweetid."\n"  );


			$count++;
		}
		fclose($file);//テキストファイルを閉じる
        


		echo '既存ニードともシマウマとも被らず、エラーとかツイート無しでもなかった件数は' . $num . '件'."\n";
		echo $num.'件追加します'."\n";

	}
		
		//User feedback
		echo 'Finished saving followers... ' . $cursor . '<br>';
		
		//Sleep counter
		//一旦、カウント関係なしに毎リクエストごとに60秒スリープする実装。

		// sleep(60);

		//↓なのでスリープカウント系の記述はコメントアウト。
		// if($sleep_counter >= 15){
		// 	sleep(15*60); //Pause execution for 16mins
		// 	$sleep_counter = 0; //Reset counter
		// }else{
		// 	$sleep_counter++;
		// }
		
		//loop counter
		$loop_counter++;
	}
	// fclose($file);

	//echo output
	$followers = $user_data->followers_count;
	$loop_multiplier = $loop_counter * 5000;

	//How many were really stored
	if($loop_multiplier > $followers){
		$dumped = $followers;
	}else{
		$dumped = $loop_multiplier;
	}
	

}
