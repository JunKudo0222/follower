
<?php

// TwitterOAuthを利用するためComposerのautoload.phpを読み込み
require __DIR__ . '/vendor/autoload.php';
// TwitterOAuthクラスをインポート
use Abraham\TwitterOAuth\TwitterOAuth;

// Twitter APIを利用するための認証情報。xxxxxxxxの箇所にそれぞれの情報をセット
const TW_CK = 'Xos2GlnvnVSUwRPTQ93Nxg40P'; // Consumer Keyをセット
const TW_CS = 'JzG5rjNAKcwKLak1E2T4pVIDq98pqGIBBjeg5s0R7YNOPpLwiC'; // Consumer Secretをセット
const TW_AT = '1509193639523926016-CYS2Ajkvo9zag6nJxe0EwF2vAnHKZd'; // Access Tokenをセット
const TW_ATS = 'aOUjOVtmyLVtcq6ZlREm4Rk3H9ggRjGRUIrDVAhY3z9zh'; // Access Token Secretをセット

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
