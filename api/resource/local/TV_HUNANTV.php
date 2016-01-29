<?php

 /*==================================================================================
 *	                       湖南本地解析插件组合开发平台
 *	
 * http://www.hunantv.com/v/3/109079/f/1125464.html
 *	
 ==================================================================================*/
	
	function GetVideo_FLASH($key, $hdstyle){
		$banben= date('Y-m-d');

	//插件名字
	$video['name'] = "☆解析插件-hunantv系统☆-".$banben;
	//高清格式信息
	$hdstr = array(0 => "0",1 => "1",2 => "2");
	
	$video['Nowhds']  = $hds = $hdstyle >= 0 && $hdstyle < 3 ? $hdstyle : 2;
		
	$video['mixhds'] = 3;

	for($i=0; $i<3; $i++){
			
			$info = getfile('http://v.api.hunantv.com/player/video?video_id='.$key, 'http://www.hunantv.com' , null);
			
			if(!empty($info))break;
			
		}

   $json = json_decode($info);
   $data = $json->data;
		if(!empty($data->info->title))$video['subject'] = $data->info->title;
		$url = $data->stream[0]->url;
		$url = getfile($url, 'http://www.hunantv.com' , null);
		$src = json_decode($url);
    if(!empty($data->info->duration))$video['data'][0]['duration'] = $data->info->duration;	
    if(!empty($src->info))$video['data'][0]['src'] = $src->info;
		if(empty($video['data'][0]['src']))return false;
		
		return $video;
	}
	
 	function GetVideo_HTML5($key, $hdstyle){

	for($i=0; $i<3; $i++){
			
			$info = getfile('http://m.api.hunantv.com/video/relatedVideos/?_1&videoId='.$key, 'http://m.hunantv.com' , null);
			
			if(!empty($info))break;
			
		}

    $json = json_decode($info);
    $data = $json->data;
		$url = $data[0]->downloadUrl;
		$url = getfile($url, 'http://m.hunantv.com' , null);
		$data = json_decode($url);
		$src = $data->info;
    if(!empty($src))$video['data'][0]['src'] = $src;
		if(empty($video['data'][0]['src']))return false;
		
		return $video;
		
	}
?>