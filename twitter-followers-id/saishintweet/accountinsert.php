<?php
//新しく追加した(テーブルを作成したアカウントをaccountテーブルにも入れていく手順)

require_once '../lib/twitteroauth.php';
 
define('CONSUMER_KEY', 'Xos2GlnvnVSUwRPTQ93Nxg40P');
define('CONSUMER_SECRET', 'JzG5rjNAKcwKLak1E2T4pVIDq98pqGIBBjeg5s0R7YNOPpLwiC');
define('ACCESS_TOKEN', '1509193639523926016-CYS2Ajkvo9zag6nJxe0EwF2vAnHKZd');
define('ACCESS_TOKEN_SECRET', 'aOUjOVtmyLVtcq6ZlREm4Rk3H9ggRjGRUIrDVAhY3z9zh');


try {
	// $db = new PDO('mysql:dbname=twitter;host=localhost;charset=utf8','root','root');//テストDB
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');//本番DB
	echo "接続OK!!!！"."\n";	
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}


$users=[
	'nanalog7',
	'kanae_udemy',
	'recoai_101',
	'Daibutsu_t_net',
	'saving_advisor',
	'forozou_2022',
	'2tointoin',
	'vk25f',
	'7miVzkFVofEKIAK',
	'kemablog3',
	'ottan_wotablog',
	'shapeman3127',
	'ichi3_startupW',
	'goki_consultant',
	'webukatu',
	'pokostyle1',
	'uta_honwakalife',
	'kaineko272',
	'kazukichi3110',
	'nachi_nachi8',
	'sakuraya0709',
	'hassy01210',
	'aBugr6ve91BO7J5',
	'fukugyo_sora',
	'yu_0503_2005',
	'fumiya238388',
	'iTakuX',
	'murapapafire',
	'v0250',
	'rito_zero',
	'bboy_fulufulu',
	'ryo_428_',
	'7ka_326',
	'mtutbo7',
	'Umaki11',
	'eiever__',
	'yesmanblog',
	'kaznize',
	'akg_tky',
	'zer0_ykisaragi',
	'kenta_freelance',
	'furusatojuku',
	'tokyoselfstudy',
	'goworkgirls',
	'Bs_Hachi',
	'storywriter',
	'da_i_chi_dev',
	'Tommy_irugi',
	'kazu_cat_study',
	'kamizato_design',
	'Rose02_design',
	'yushinfx',
	'pitopitomm',
	'ojibu2020',
	'AlchemistGlobal',
	'ki_tora1122',
	'oshiromandayo',
	'mi_wa_sensei',
	'guchi13646123',
	'techan48',
	'Inv_Engineer',
	'kabutarou2021',
	'chiiusagi2',
	'hayapu',
	'mamannu50',
	'achamo___n',
	'ItoAkira123',
];
foreach($users as $user){

	$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	//get the number of a users followers
	$user_details = $toa->get('users/show', array('screen_name'=>$user));
	$user_data = json_decode($user_details);
	

		echo 'twitterid,'.$user.'accountid,'.$user_data->id_str."\n";


	$stmt = $db->prepare("INSERT INTO accounts (
		twitterid, accountid
	) VALUES (
		:twitterid, :accountid
	)");

$stmt->bindParam( ':twitterid', $user, PDO::PARAM_STR);
$stmt->bindParam( ':accountid', $user_data->id_str, PDO::PARAM_STR);

$res = $stmt->execute();
}
$db = null;