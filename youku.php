<?php
function cget($url) {
	if (!$url) return;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Appkey: TkpWeC1uNmFidA==",
	));
	curl_setopt($ch, CURLOPT_TIMEOUT, 6);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}
if(isset($_REQUEST['url'])){
	$url = trim($_REQUEST['url']);
}elseif(isset($_REQUEST['vid'])){
	$url = "http://v.youku.com/v_show/id_{$_REQUEST['vid']}.html";
}else{
	header("Content-Type: text/html; charset=UTF-8"); exit("参数错误。");
}
$hd = isset($_REQUEST['hd']) ? intval($_REQUEST['hd']) : 2;
if($hd > 3){ $hd = 3; }
if($hd < 1){ $hd = 1; }
$phone = false;
if(isset($_REQUEST['phone']) && $_REQUEST['phone']){
	$phone = true;
}
$api = json_decode(cget("http://videojj.com/api/videos/parse?url=" . base64_encode($url)), true);
$segs = $api['msg']['segs'];
if(!$segs){ header("Content-Type: text/html; charset=UTF-8"); exit("解析失败。"); }
$hdlist = array();
if(!$phone){
	$fmt = "";
	if(isset($segs['FLV-SuperHD'])){ $hdlist[] = 3; if($hd == 3){ $fmt = "FLV-SuperHD"; } }
	if($hd == 3 && !$fmt){ $hd = 2; }
	if(isset($segs['MP4'])){ $hdlist[] = 2; if($hd == 2){ $fmt = "MP4"; } }
	if(isset($segs['MP4-SD'])){ $hdlist[] = 2; if($hd == 2){ $fmt = "MP4-SD"; } }
	if(isset($segs['MP4-HD'])){ $hdlist[] = 2; if($hd == 2){ $fmt = "MP4-HD"; } }
	if($hd == 2 && !$fmt){ $hd = 1; }
	if(isset($segs['FLV'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "FLV"; } }
	if(isset($segs['FLV-SD'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "FLV-SD"; } }
	if(isset($segs['FLV-HD'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "FLV-HD"; } }
	if(!$fmt){ header("Content-Type: text/html; charset=UTF-8"); exit("资源解析失败。"); }
	$hdlist = array_unique($hdlist);
	sort($hdlist);
	$files = $segs[$fmt];
	$deft = $defa = array();
	$q = array(1 => "标清", 2 => "高清", 3 => "超清");
	$a = "url=" . urlencode($url) . "&hd=" . $hd;
	$http = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
	$def = "{$http}://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?["."$"."pat]";
	foreach($hdlist as $ahd){
		$defa[] = "url=" . urlencode($url) . "&hd=" . $ahd;
		$deft[] = $q[$ahd];
	}
	header("Content-Type: text/xml; charset=UTF-8");
	echo '<?xml version="1.0"?>';
	echo "\r\n";
	echo '<ckplayer>';
	echo "<flashvars><![CDATA[{h->3}{a->{$a}}{defa->" . implode('|', $defa) . "}{deft->" . implode('|', $deft) . "}{f->{$def}}]]></flashvars>";
	foreach($files as $file){
		echo "<video><file><![CDATA[{$file['url']}]]></file><size>{$file['size']}</size><seconds>{$file['seconds']}</seconds></video>";
	}
	echo '</ckplayer>';
}else{
	$fmt = "";
	if(isset($segs['Mobile-m3u8-FLV-SuperHD'])){ $hdlist[] = 3; if($hd == 3){ $fmt = "Mobile-m3u8-FLV-SuperHD"; } }
	if($hd == 3 && !$fmt){ $hd = 2; }
	if(isset($segs['Mobile-m3u8-MP4'])){ $hdlist[] = 2; if($hd == 2){ $fmt = "Mobile-m3u8-MP4"; } }
	if(isset($segs['Mobile-m3u8-MP4-SD'])){ $hdlist[] = 2; if($hd == 2){ $fmt = "Mobile-m3u8-MP4-SD"; } }
	if(isset($segs['Mobile-m3u8-MP4-HD'])){ $hdlist[] = 2; if($hd == 2){ $fmt = "Mobile-m3u8-MP4-HD"; } }
	if($hd == 2 && !$fmt){ $hd = 1; }
	if(isset($segs['Mobile-m3u8-FLV'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "Mobile-m3u8-FLV"; } }
	if(isset($segs['Mobile-m3u8-FLV-SD'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "Mobile-m3u8-FLV-SD"; } }
	if(isset($segs['Mobile-m3u8-FLV-HD'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "Mobile-m3u8-FLV-HD"; } }
	if(isset($segs['3GP-HD'])){ $hdlist[] = 1; if($hd == 1){ $fmt = "3GP-HD"; } }
	if(!$fmt){ header("Content-Type: text/html; charset=UTF-8"); exit("资源解析失败。"); }
	$file = $segs[$fmt][0]['url'];
	header("Location: {$file}");
}
?>
