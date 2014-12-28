<?php

// 4枚までの画像をtweet_add_imgテーブルに格納

require_once("twitteroauth/twitteroauth.php");

$consumerKey = "MYCONSUMERKEY";
$consumerSecret = "MYCONSUMERSECRET";
$accessToken = "MYACCESSTOKEN";
$accessTokenSecret = "MYACCESSTOKENSECRET";

$twObj = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);

$request = $twObj->OAuthRequest('https://api.twitter.com/1.1/statuses/user_timeline.json','GET',
    array(
        'count'=>'100',
        'screen_name' => 'echizenya_yota',
        ));
$results = json_decode($request);

if(isset($results) && empty($results->errors)){
    foreach($results as $tweet){
		// データベースの接続
		try {
		 	$dbh = new PDO('mysql:host=localhost;dbname=tweet1;charset=utf8', 'myusername','mypassword');
		} catch(PDOException $e) {
		 	var_dump($e->getMessage());
		 	exit;
		}
		// 処理(画像用URLの挿入)
		$stmt = $dbh->prepare("insert into tweet (tw_img0, tw_img1, tw_img2, tw_img3) values (:tw_img0, :tw_img1, :tw_img2, :tw_img3)");

		// バインドする
		$stmt->bindParam(":tw_img0", $tw_img[0]);
		$stmt->bindParam(":tw_img1", $tw_img[1]);
		$stmt->bindParam(":tw_img2", $tw_img[2]);
		$stmt->bindParam(":tw_img3", $tw_img[3]);

		if (is_array($tweet->extended_entities->media)) {
			foreach($tweet->extended_entities->media as $key => $media) {
				if (isset($tweet->extended_entities->media[$key])) {
					$tw_img[key] = $tweet->extended_entities->media[$key]->media_url;
				}
				// カラムの列番号が固定的な割に、繰り返表示される画像と対応させる方法がわからない
			}
		}

		$stmt->execute();

		// 切断
		$dbh = null;

  }else{
	echo "関連したつぶやきがありません。";
 }


 ?>



