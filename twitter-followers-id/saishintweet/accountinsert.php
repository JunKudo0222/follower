<?php
//新しく追加した(テーブルを作成したアカウントをaccountテーブルにも入れていく手順)

require_once '../lib/twitteroauth.php';
 
define('CONSUMER_KEY', 'ZG2JQgtpUEJ4kcJ3oRES4jcgO');
define('CONSUMER_SECRET', 'Pe8vaSdNhs1DUIoVV8J7ctZ5UZ7fBI7i1uGEdSNVLt4IdIHLtM');
define('ACCESS_TOKEN', '1509193639523926016-zN6BdwFeeXHJsfZGecEfBVsotqialh');
define('ACCESS_TOKEN_SECRET', 'lcmnABRo8NoCM36DsQC3ck40FHBgTn5ViLgfnlEG7Svxz');


try {
	// $db = new PDO('mysql:dbname=twitter;host=localhost;charset=utf8','root','root');//テストDB
	$db = new PDO('mysql:dbname=azurefennec478_twitter;host=mysql634.db.sakura.ne.jp;charset=utf8','azurefennec478','Iojk3231');//本番DB
	echo "接続OK!!!！"."\n";	
} catch (PDOException $e) {
	echo 'DB接続エラー！: ' . $e->getMessage();
	exit;
}


$users=[
	// 'nanalog7',
	// 'kanae_udemy',
	// 'recoai_101',
	// 'Daibutsu_t_net',
	// 'saving_advisor',
	// 'forozou_2022',
	// '2tointoin',
	// 'vk25f',
	// '7miVzkFVofEKIAK',
	// 'kemablog3',
	// 'ottan_wotablog',
	// 'shapeman3127',
	// 'ichi3_startupW',
	// 'goki_consultant',
	// 'webukatu',
	// 'pokostyle1',
	// 'uta_honwakalife',
	// 'kaineko272',
	// 'kazukichi3110',
	// 'nachi_nachi8',
	// 'sakuraya0709',
	// 'hassy01210',
	// 'aBugr6ve91BO7J5',
	// 'fukugyo_sora',
	// 'yu_0503_2005',
	// 'fumiya238388',
	// 'iTakuX',
	// 'murapapafire',
	// 'v0250',
	// 'rito_zero',
	// 'bboy_fulufulu',
	// 'ryo_428_',
	// '7ka_326',
	// 'mtutbo7',
	// 'Umaki11',
	// 'eiever__',
	// 'yesmanblog',
	// 'kaznize',
	// 'akg_tky',
	// 'zer0_ykisaragi',
	// 'kenta_freelance',
	// 'furusatojuku',
	// 'tokyoselfstudy',
	// 'goworkgirls',
	// 'Bs_Hachi',
	// 'storywriter',
	// 'da_i_chi_dev',
	// 'Tommy_irugi',
	// 'kazu_cat_study',
	// 'kamizato_design',
	// 'Rose02_design',
	// 'yushinfx',
	// 'pitopitomm',
	// 'ojibu2020',
	// 'AlchemistGlobal',
	// 'ki_tora1122',
	// 'oshiromandayo',
	// 'mi_wa_sensei',
	// 'guchi13646123',
	// 'techan48',
	// 'Inv_Engineer',
	// 'kabutarou2021',
	// 'chiiusagi2',
	// 'hayapu',
	// 'mamannu50',
	// 'achamo___n',
	// 'ItoAkira123',
	//10月4日追加
// 	'Shoyan_mental',
//   'mikki007jp',
//   'yuukun2nd',
//   'Kazufree_blog',
//   'masaru_taisyoku',
//   'haruharuhi77',
//   'Mame_Love_GTR',
//   'hitsujiooya',
//   'maccchan',
//   'keizo_business',
//   'moka_066',
//   'kurenoaki888',
//   'hit_hit_hit_100',
//   'non_webcircle',
//   'jidou89',
//   'blog_hunt',
//   'ramuneblog',
//   'toto_azusa',
//   'hopeguitar',
//   'yuruihancho',
//   'tokuzon1',
//   'yukiyuki3yuki',
//   'oda_vn',
//   'fezNote18',
  //10月5日追加
  'marunomayuge113',
  'wwwbitter_root',
  'taichi_LexusLS',
  'ryosuke_blog_',
  '_kasiman',
  'hideki_climax',
  'goheycode',
  'php_mania_com',
  'kei_dokusyo',
  'MIISHO_AKA',
  'nao1026_3',
  'rulers_coins',
  'kou_restart',
  'yuki_choki_exp',
  'cryptopapa1651',
  'yusuke_sns1201',
  'junpei_sugiyama',
  'hide_creator',
  'Astromanjp',
  'kazuki69778809',
  'ab_tpx',
  'sedori_ojisun',
  'mama_1999',
  'momofukusandao',
  'kenmo425',
  'T_KYOKOS',
  'jiu_de_migaru',
  'peace_lifework',
  'matsu_f16',
  'nemublogcom',
  'mavshine1',
  'DemoOkan',
  'seonabe',
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