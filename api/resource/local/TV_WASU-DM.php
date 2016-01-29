<?php

 /*==================================================================================
 *	                       华数本地解析插件组合开发平台
 *	
 * http://www.wasu.cn/Play/show/id/5688430
 *	
 ==================================================================================*/
 
	function GetVideo_FLASH($key, $hdstyle){
	$banben= date('Y-m-d');

//	插件名字
	$video['name'] = "☆解析插件-wasu动漫系统☆-".$banben;
	
//高清格式信息
	$hdstr = array(0 => "720P",1 => "350",2 => "1080P",3 => "1000");
	
	$video['Nowhds']  = $hds = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 2;
		
	$video['mixhds'] = 1;

	//加载信息
	$info = getfile('http://www.wasu.cn/Api/getPlayInfoById/id/'.$key.'/datatype/xml','http://www.wasu.cn' , null);
  preg_match('#<video>(.*)</video>#i', $info, $vurl);
  preg_match('#<duration>(.*)</duration>#i', $info, $duration);
  //preg_match('#<duration>(.*)</duration>#i', $info, $duration);
  preg_match('#<title>(.*)</title> #i', $info, $subject);
  
  $getkey = getfile('http://www.wasu.cn/Play/show/id/'.$key,'http://www.wasu.cn' , null);
  preg_match("#playKey[\s]=[\s]'(.*)',_playTitle#i", $getkey, $urlkey);
  
  $url = 'http://www.wasu.cn/Api/getVideoUrl/id/'.$key.'/url/'.$vurl[1].'/key/'.$urlkey[1].'/';
  $content = getfile($url, 'http://s.wasu.cn/' , null);
  preg_match('#<video><!\[CDATA\[(.*)\]\]></video>#i', $content, $vurl);
  if(empty($vurl[1])) return;
  $i=0; //视频标题
  if(!empty($subject[0]))$video['subject'] = $subject;
  if(!empty($duration[1]))$video['data'][$i]['duration'] = $duration[1];
  if(!empty($vurl[1]))$video['data'][$i]['src'] = $vurl[1].'?version=SWFPlayer_V.3.7.24';//?version=SWFPlayer_V.3.7.24';
  
      if(empty($video['data'][0]['src']))return false;
		
		return $video;
	}

 	function GetVideo_HTML5($key, $hdstyle){
 		
	
	$info = getfile('http://www.wasu.cn/Api/getPlayInfoById/id/'.$key.'/datatype/xml','http://www.wasu.cn' , null);
  preg_match('#<video>(.*)</video>#i', $info, $vurl);
  $getkey = getfile('http://www.wasu.cn/Play/show/id/'.$key,'http://www.wasu.cn' , null);
  preg_match("#playKey[\s]=[\s]'(.*)',_playTitle#i", $getkey, $urlkey);
  $url = 'http://www.wasu.cn/Api/getVideoUrl/id/'.$key.'/url/'.$vurl[1].'/key/'.$urlkey[1].'/';
  $content = getfile($url, 'http://s.wasu.cn/' , null);
  preg_match('#<video><!\[CDATA\[(.*)\]\]></video>#i', $content, $vurl);
  if(empty($vurl[1])) return;
  $i=0; //视频标题
  $video['data'][$i]['src'] = $vurl[1].'?version=MIPlayer_V1.3.2';
  
      if(empty($video['data'][0]['src']))return false;
		
		return $video;
}

?>