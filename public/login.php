<?php
$appid = 'wxf0c41cb7eaf468e1';
#你的公众号appid
$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=https%3a%2f%2fenber.63tv.cn%2fcallback.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
//$url='/login.html';
#redirect_uri改为你的网页授权域名和刚刚跳转到的显示页面，比如我的是getinfoDetail.php
header('location:'.$url);
?>