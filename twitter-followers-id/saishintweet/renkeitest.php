
<?php

// TwitterOAuthを利用するためComposerのautoload.phpを読み込み
require __DIR__ . '/vendor/autoload.php';
// TwitterOAuthクラスをインポート
use Abraham\TwitterOAuth\TwitterOAuth;

// Twitter APIを利用するための認証情報。xxxxxxxxの箇所にそれぞれの情報をセット
const TW_CK = 'ZG2JQgtpUEJ4kcJ3oRES4jcgO'; // Consumer Keyをセット
const TW_CS = 'Pe8vaSdNhs1DUIoVV8J7ctZ5UZ7fBI7i1uGEdSNVLt4IdIHLtM'; // Consumer Secretをセット
const TW_AT = '1509193639523926016-zN6BdwFeeXHJsfZGecEfBVsotqialh'; // Access Tokenをセット
const TW_ATS = 'lcmnABRo8NoCM36DsQC3ck40FHBgTn5ViLgfnlEG7Svxz'; // Access Token Secretをセット

// TwitterOAuthクラスのインスタンスを作成
$connect = new TwitterOAuth( TW_CK, TW_CS, TW_AT, TW_ATS );
// Twitter API v2. を利用する場合
// $connect->setApiVersion('2');


    

// $statuses = $connect->get(
// 	'statuses/user_timeline',
//     // 取得するツイートの条件を配列で指定
//     array(
//     	// ユーザー名（@は不要）
//         'user_id'       => $dat,//followdb.phpから取り込んでいる
//     	// ユーザーオブジェクト除外
//         // 'trim_user'       => 'true',
//         'trim_user'       => 'false',
//         // ツイート件数
//         // 'count'             => '3',
//         // リプライを除外するかを、true（除外する）、false（除外しない）で指定
//         'exclude_replies'   => 'true',
//         // リツイートを含めるかを、true（含める）、false（含めない）で指定
//         'include_rts'       => 'false'
//     )
// );//一旦リプライ除外して取得し、いいね0が存在するか確認。
