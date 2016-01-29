<?php

 /*==================================================================================
 *	                       YOUKU本地解析插件组合开发平台
 *	
 *
 *	
 ==================================================================================*/
	
	function GetVideo_FLASH($key, $hdstyle){
	
	$banben= date('Y-m-d');

//	插件名字
		
		$video['name'] = "☆解析插件-优酷系统☆-".$banben;
		
		$proxy = array('Address' => '', 'PORT' => '', 'USER' => '', 'PWD' => '');
				
		if(Region == 1 && empty($proxy['Address']))return false;
		
		$hdstr = array(0 => "flv",1 => "mp4",2 => "hd2",3 => "hd3");
		
		$video['Nowhds'] = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 3;
		
		for($i=0; $i<3; $i++){
			
			$info = getfile('http://v.youku.com/player/getPlayList/VideoIDS/'.$key.'/Pf/4/ctype/12/ev/1', 'http://v.youku.com', $proxy);
			
			if(!empty($info))break;
			
		}
		
		$json = json_decode($info);
		
		$data = $json->data[0];
		
		for($i=0; $i<3; $i++){
	
			$info2 = getfile('http://v.youku.com/player/getPlayList/VideoIDS/'.$key.'/timezone/+08/version/5/source/video?ev=1&n=3&ctype=10&ran=985', 'http://v.youku.com', $proxy);
			
			if(!empty($info2))break;
			
		}
	
		$json2 = json_decode($info2);
		
		$data2 = $json2->data[0];
		
		if(!empty($data->title))$video['subject'] = $data->title;
		
		$mhds = 0;
		
		foreach($data2->streamtypes as $val){
			
			for($i=0; $i<count($hdstr); $i++){
				
				if($val == $hdstr[$i])$mhds++;
				
				if($mhds>4)	break;	
			}
			
		}
		
		$video['mixhds'] = isset($mhds) ? $mhds : null;
		
		$video['Nowhds'] = min($video['mixhds']-1, $video['Nowhds']);
		
		$ep = $data->ep;
			
		list($user_sid, $user_token) = explode('_',Encode("becaf9be", confuse($ep)));
	
		if($video['Nowhds'] == 3){
						
			@$fid = cg_huns($data2->seed,$data2->streamfileids->$hdstr[$video['Nowhds']]);
			
		}else{
			
			@$fid = cg_huns($data->seed,$data->streamfileids->$hdstr[$video['Nowhds']]);
			
		}
	
		$type = $hdstr[$video['Nowhds']];
		
		$video['bytes'] = $data2->streamsizes->$hdstr[$video['Nowhds']];
		
		$video['duration'] = $data2->seconds;
		
		$seg = $data2->segs->$hdstr[$video['Nowhds']];
	
		if(!empty($seg)){
		
			$i = 0;
			
			foreach($seg as $value){
					
				$value->no = dechex($value->no);
					
				if(1 == strlen($value->no))$value->no = '0'.$value->no;
					
				$fileId = getFileId($fid, $value->no);
		
				$video['data'][$i]['src'] = getVideoSrc($value,$data,$type,$fileId,$user_sid,$user_token);
							
				if(!empty($value->seconds))$video['data'][$i]['duration'] = $value->seconds;
						
				if(!empty($value->size))$video['data'][$i]['bytes'] = $value->size;
						
				$i++;	
						
			}
			
		}
		
		if(empty($video['data'][0]['src']))return false;
		
		return $video;
	}
	
	
			//手机版采集
 	function GetVideo_HTML5($key, $hdstyle){


		$hdstr = array(0 => "flv",1 => "mp4");
		
		$video['Nowhds'] = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 3;

		
		for($i=0; $i<3; $i++){
			
			$info = getfile('http://v.youku.com/player/getPlayList/VideoIDS/'.$key.'/Pf/4/ctype/12/ev/1', 'http://v.youku.com');
			
			if(!empty($info))break;
			
		}

		$json = json_decode($info);
		
		$data = $json->data[0];
		
		$ep = $data->ep;
			
		list($user_sid, $user_token) = explode('_',Encode("becaf9be", confuse($ep)));

    $hd = $hdstr[$video['Nowhds']];
    
    $value= getVideoSrcm3u8($key,$hd,$data,$user_token, $user_sid);
  
    $video['data'][0]['src'] = $value;

  return $video;

	}

  function getVideoSrcm3u8($key, $type, $data,$user_token, $user_sid) { 
		
   //$sid = time().(rand(0,9000)+10000);   
   $c = 'http://pl.youku.com/playlist/m3u8?vid='.$key.'&type='.$type.'&ts='.time().'&keyframe=0&ep=';
	 $c .= urlencode(Decode(Encode('bf7e5f01', $user_sid . '_' . $key . '_' . $user_token)));
   $c .= '&sid='.$user_sid.'&token='.$user_token;
   $c .= '&ctype=12&ev=1&oip='.$data->ip;  
   
   	return $c;
  } 
	
	
	function getVideoSrc($obj, $data, $type, $fileId, $user_sid, $user_token){
		
		$hd = array('flv'=>'0','flvhd'=>'0','mp4'=>'1','hd2'=>'2','3gphd'=>'1','3gp'=>'0','hd3'=>'3');
		$hd = $hd[$type];
		$stremtype = array('flv'=>'flv','mp4'=>'mp4','hd2'=>'flv','3gphd'=>'mp4','3gp'=>'flv','hd3'=>'flv');
		$stremtype = $stremtype[$type];
				 
		$show='';
		
		if(@$data->show)$show = $data->show->show_paid?"&amp;ypremium=1":"&amp;ymovie=1";
			 
		$c = "http://k.youku.com/player/getFlvPath/sid/".$user_sid."_".$obj->no."/st/".$stremtype."/fileid/".$fileId."?K=".$obj->k."&amp;hd=".$hd."&amp;myp=0&amp;ts=".$obj->seconds."&amp;ypp=0".$show."&amp;ep=";
		$c .= urlencode(Decode(Encode('bf7e5f01',$user_sid. "_" .$fileId . "_" . $user_token)));
		$c .= "&amp;ctype=12&amp;ev=1&amp;token=".$user_token;
		$c .= "&amp;oip=".$data->ip;
			 
		return $c;
	}

	function getFileId($fid, $no){
		
		$prefix = substr($fid, 0, 8);
		$no = strtoupper($no);
		$suffix = substr($fid, 10);
		
		return $prefix.$no.$suffix;
	}
	
	function cg_huns($randomSeed, $str){
		
		$key = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/\\:._-1234567890";
		
		for($i = 0,$value= ""; $i <68; $i++){
			$randomSeed=(211 * $randomSeed + 30031) % 65536;
			$ran=$randomSeed / 65536;
			$on =  intval($ran* strlen($key));
			$value .= $key[$on];
			$key=str_replace($key[$on],'',$key);
		}
		
		$arr = explode('*',$str);
		
		for($return = "", $i = 0; $i < count($arr)- 1; $i++){ $return .= $value[$arr[$i]];}
		
		return $return;
	}
	
	function confuse($a){
		
		$h = array(- 1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
		$i = strlen($a);
		$f = 0;
		
		for($e = ""; $f < $i;){
			
			do{$c = $h[ord($a[$f++]) & 255];}while ($f < $i && -1 == $c);
			if( - 1 == $c) break;
			
			do{$b = $h[ord($a[$f++])& 255];} while ($f < $i && -1 == $b);
			if( - 1 == $b) break;
			
			$e .= chr($c << 2 | ($b & 48) >> 4);
			
			do{
				$c = ord($a[$f++]) & 255;
				if(61 == $c) return $e;
				$c = $h[$c];
			}while($f < $i && - 1 == $c);
			if( - 1 == $c) break;
			
			$e .= chr(($b & 15) << 4 | ($c & 60) >> 2);
			
			do{
				$b = ord($a[$f++]) & 255;
				if (61 == $b) return $e;
				$b = $h[$b];
			}while($f < $i && - 1 == $b);
			if( - 1 == $b) break;
			
			$e .= chr(($c & 3) << 6 | $b);
			
		}
		
		return $e;
	}
	
	function Decode($a){
		
		$n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		
		if(!$a) return "";
		
		for($c = "",$b = 0,$f = strlen($a); $b < $f;){
			
			$e = ord($a[$b++]) & 255;
			if($b == $f) {$c .= $n[$e>>2].$n[($e & 3) << 4]."==";break; }
			
			$g = ord($a[$b++]);
			if($b == $f) {$c .= $n[$e >> 2].$n[($e & 3) << 4 | ($g & 240) >> 4].$n[($g & 15) << 2]."="; break;}
			
			$h = ord($a[$b++]);
			
			$c .= $n[$e >> 2]. $n[($e & 3) << 4 | ($g & 240) >> 4].$n[($g & 15) << 2 | ($h & 192) >> 6].$n[$h & 63];
			
		}
		
		return $c;
	}
	
	function Encode($a, $c){
		
		$b = array();
		
		for($f = 0,$h = 0; 256 > $h; $h++){
			$b[$h] = $h;
		}
		for($h = 0; 256 > $h; $h++){ 
			$f = ($f + $b[$h] + ord($a[$h % strlen($a)])) % 256;
			$i = $b[$h];$b[$h] = $b[$f];$b[$f] = $i;
		}
		for ($q = $f = $h = 0,$e = ""; $q < strlen($c); $q++) {
			$h = ($h + 1) % 256;$f = ($f + $b[$h]) % 256;
			$i = $b[$h];$b[$h] = $b[$f];
			$b[$f] = $i;$d=$b[($b[$h] + $b[$f]) % 256];
			$e .= chr(ord($c[$q]) ^ $d);
		}
		
		return $e;
	}
	
?>