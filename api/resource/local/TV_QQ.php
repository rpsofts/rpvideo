<?php

 /*==================================================================================
 *	                       QQ本地解析插件组合开发平台
 *	
 * http://www.56.com/u85/v_MTM2MTYyOTE0.html
 *	
 ==================================================================================*/

	function GetVideo_FLASH($key, $hdstyle){
	
	$banben= date('Y-m-d');

	//插件名字
	$video['name'] = "☆解析插件-qq系统☆-".$banben;
	//高清格式信息
	$hdstr = array(0 => "sd",1 => "shd",2 => "hd",3 => "fhd");
	
	$video['Nowhds']  = $hds = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 3;
		
	$video['mixhds'] = 4;

	// base url http://vv.video.qq.com/getinfo  http://vv.video.qq.com/geturl?vid=
	$info_url = 'http://vv.video.qq.com/getinfo?defaultfmt='.$hdstr[$hds].'&otype=json&platform=11&vids='.$key;
	
	// get the remote page contents
	$content1 =  getfile($info_url,'http://v.qq.com');

	// get the json strings
	preg_match('~QZOutputJson\s*=\s*(.*);~iUs',$content1,$info);
	
	// decode json data
	$json1 = json_decode($info[1]);
	
	// get the vi object which contains the video information
	$vi = $json1->vl->vi;
	
	// the total segmentations of the video files
	$fc = $vi[0]->cl->fc;
	
	// if the remote page returned ZERO segmentation of the video, we change it to this one
	// maybe we can get some in it
	
		// get ui object, which contains the uri and host information
		$ui = $vi[0]->ul->ui;
		
		// vt value, that is required to the file server
		$vt = $ui[1]->vt;
		
		// base video url, it is the uri of the video resource
		$vurl = $ui[0]->url;
		
		// file name, it is required when we request for the key
		$fn = explode('.',$vi[0]->fn);
		
		// ci object, which contains the video segmentations information
		$ci = $vi[0]->cl->ci;
		
		// the keyid of the video segmentations, we will use it to get the key
		$keyid = explode('.',$ci[0]->keyid);
		
		$key_base_url = 'http://vv.video.qq.com/getkey?platform=11&otype=json&vid='.$key.'&vt='.$vt;
		
		for($i = 0; $i < $fc; $i++){
			// the size and duration of the video
			$cd[$i] = $ci[$i]->cd;
			$cs[$i] = $ci[$i]->cs;
			
			// video file name
			$vname = $fn[0].'.'.$fn[1].'.'.($i+1).'.'.$fn[2];
			
			// key url
			$key_url = $key_base_url.'&format='.$keyid[1].'&filename='.$vname;
			
			// get key
			$content2 = getfile($key_url,'http://v.qq.com');
			
			// get the json strings
			preg_match('~QZOutputJson\s*=\s*(.*);~iUs',$content2,$get);
			
						
			// decode json data
			$json2 = json_decode($get[1]);
			
			// the key
			$kkkey = $json2->key;


				$video['data'][$i]['src'] = $vurl.$vname.'?platform=0&fmt='.$hdstr[$hds].'&level=0&vkey='.$kkkey;
				$video['data'][$i]['bytes'] = $cs[$i];
				$video['data'][$i]['duration'] = $cd[$i];
		}
			return $video;
	}

 	function GetVideo_HTML5($key, $hdstyle){
 		

//	插件名字
	//http://vv.video.qq.com/geturl?vid=
	$content = getfile('http://vv.video.qq.com/geturl?vid='.$key.'&otype=json','http://v.qq.com');
	preg_match('~QZOutputJson\s*=\s*(.*);~iUs',$content,$info);
  $json = json_decode($info[1]);
  $vd=$json->vd->vi[0];
  $url=$vd->url;
  $i=0;
  if(!empty($url))$video['data'][$i]['src'] = $url;
  
      if(empty($video['data'][0]['src']))return false;
		
		return $video;
}


?>