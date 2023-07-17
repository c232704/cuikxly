<?php
	$appid = "wxf0c41cb7eaf468e1"; 
	$secret = "d5d61c6cf4693f4a5947b28e2d4b8753"; 
	$code = $_GET["code"]; 
	$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$get_token_url); 
	curl_setopt($ch,CURLOPT_HEADER,0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
	$res = curl_exec($ch); 
	curl_close($ch); 
	$json_obj = json_decode($res,true); 
	//根据openid和access_token查询用户信息 
	$access_token = $json_obj['access_token']; 
	$openid = $json_obj['openid']; 
	$get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN'; 
	 
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,$get_user_info_url); 
	curl_setopt($ch,CURLOPT_HEADER,0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
	$res = curl_exec($ch); 
	curl_close($ch); 
	 
	//解析json 
	$user_obj = json_decode($res,true);
	//var_dump($user_obj);
	$openid=isset($user_obj['openid'])?$user_obj['openid']:null;
	$unionid=isset($user_obj['unionid'])?$user_obj['unionid']:null;
	$urls='https://enber.63tv.cn/h5/#/pages/login/logingzh?openid='.$openid.'&unionid='.$unionid;
	header('location:'.$urls);
?>