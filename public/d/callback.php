<?php
	$appid = "wx2a2d24fbe2383604"; 
	$secret = "07b59b4bac2f17edc9e121e3dba6d0b3"; 
	$cmurl = "https://jy.chengwuwa.cn/d/index.html";//前端地址
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
	$nikname=isset($user_obj['nickname'])?$user_obj['nickname']:null;
	$headimgurl=isset($user_obj['headimgurl'])?$user_obj['headimgurl']:null;
	$urls=$cmurl.'?openid='.$openid.'&unionid='.$unionid.'&nikname='.$nikname.'&headimgurl='.$headimgurl;
	header('location:'.$urls);
?>