<?php 
//下方设置网站域名带协议头，结尾不加“/”，留空则自动获取
$domain = "";
$domainh = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$protocol = 'http://';
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https://';
} elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
    $protocol = 'https://';
}
$wangzhi = $protocol.$domainh;
if($domain == "") {
	$domain = $wangzhi;
}
$dingyu = str_replace("www.","",$domainh);
//下方配置每个域名对应的网站名称（二级域名需单独配置，www除外），英文逗号分隔，未配置的使用默认网站名称
$dyttlist = array("111.com"=>"网站名称1","222.cn"=>"网站名称2");	
$dyttres = array_key_exists($dingyu, $dyttlist);
if($dyttres) {
	$urkeji_com_hz = $dyttlist[$dingyu];
} else {
    //默认网站名称
	$urkeji_com_hz = "多城市版AI站群";
}
//程序所在目录，开头和结尾都加“/”，如果在根目录，请直接写“/”
$url = "/";
//模拟IP前三段（以“.”结尾），抓取网络数据时使用
$cip = "175.23.36.";