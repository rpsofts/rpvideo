<?php

 /*==================================================================================
 *	                       bilibili本地解析插件组合开发平台
 *	
 * http://www.bilibili.com/video/av2215039/
 *	
 ==================================================================================*/
 
	function GetVideo_FLASH($key, $hdstyle){
	$banben= date('Y-m-d');

//	插件名字
	$video['name'] = "☆解析插件-bilibili系统☆-".$banben;
	

//高清格式信息
	$hdstr = array(0 => "720P",1 => "350",2 => "1080P",3 => "1000");
	
	$video['Nowhds']  = $hds = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 2;
		
	$video['mixhds'] = 4;

	//加载信息
	
  $info = getfile('http://www.bilibili.com/m/html5?cid='.$key,'http://www.bilibili.com' , null);
  
  preg_match('#src\"\:\"(.*?)\"\}#i', $info, $vurl);
  
  if(empty($vurl[1]))$info = getfile('http://www.bilibili.com/m/html5?aid='.$key,'http://www.bilibili.com' , null);
  
  preg_match('#src\"\:\"(.*?)\"\}#i', $info, $vurl);

  $i=0; 
  
	if(!empty($vurl[1]))$video['data'][$i]['src'] = str_replace('&amp;', '&', $vurl[1]);	


	if(empty($video['data'][0]['src']))return false;	

		return $video;
	}
	
 	function GetVideo_HTML5($key, $hdstyle, $userkey){
 		
 	if(hostmd5key()!=$userkey){
		$video['data'][0]['src']='对不起您的授权码错误，暂不能提供解析！';return $video;exit();
		} else {
				for($i=0; $i<3; $i++){	        
	        $token=getfile('http://api.lyhaoyu.cn/Index.php/Index/index/License/token/'.$userkey, '' , null);	       			
	        if(!empty($token))break;			
		     }
	       $token = json_decode($token);
	       $keytime=$token[0]->keytime;
	       	if ($keytime <= (date('Y-m-d'))){$video['data'][0]['src']='对不起您的许可码已过期，暂不能提供解析！';return $video;exit();}
	       $whtime=$token[0]->whtime;
	       $banben="2015-04-24";
	        if ($whtime <= $banben){$video['data'][0]['src']='对不起您的维护期已过期，暂不能使用此版本！';return $video;exit();}
	  } 		
	  
//	插件名字
	$video['name'] = "☆宁哥解析插件-bilibili系统☆";
	

//高清格式信息
	$hdstr = array(0 => "720P",1 => "350",2 => "1080P",3 => "1000");
	
	$video['Nowhds']  = $hds = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 2;
		
	$video['mixhds'] = 4;

	//加载信息
	
  $info = getfile('http://www.bilibili.com/m/html5?cid='.$key,'http://www.bilibili.com' , null);
  
  preg_match('#src\"\:\"(.*?)\"\}#i', $info, $vurl);
  
  if(empty($vurl[1]))$info = getfile('http://www.bilibili.com/m/html5?aid='.$key,'http://www.bilibili.com' , null);
  
  preg_match('#src\"\:\"(.*?)\"\}#i', $info, $vurl);

  $i=0; 
  
	if(!empty($vurl[1]))$video['data'][$i]['src'] = str_replace('&amp;', '&', $vurl[1]);	


	if(empty($video['data'][0]['src']))return false;	

		return $video;
	}
	

?>