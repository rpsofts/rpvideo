<?php

 /*==================================================================================
 *	                       土豆本地解析插件组合开发平台
 *	
 *
 *	
 ==================================================================================*/
	
 
	function GetVideo_FLASH($key, $hdstyle){
	
		$banben= date('Y-m-d');

//	插件名字
		
		$video['name'] = "☆解析插件-土豆系统☆-".$banben;
		
      if(preg_match('/^[0-9]*$/',$key)){
	       $url = tudourul($key); 
	       $tudoukey = tudouvid($url);
	       if(!empty($tudoukey))$key=$tudoukey;
	       }
     
      if(preg_match('/^[0-9]*$/',$key)){
	   	  if(!is_numeric($key)){
			for($i=0; $i<3; $i++){
				$c = getfile('http://www.tudou.com/programs/view/'.$key.'/', 'http://www.tudou.com/programs/view/'.$key.'/', null);
				preg_match("/,iid:[\s]+([0-9]+)/i", $c, $ketStr);
				if(!empty($ketStr[1]))break;
			}
			if(!empty($ketStr[1])){
				$key = $ketStr[1];
			}else{
				return false;
			}
		}
		$video['DragMethod'] = '1';
		
		$video['VideoDragStr'] = 'tflvbegin';
		
		$hdstr = array(0 => "2",1 => "3",2 => "5",3 => "99");
		
		$video['Nowhds'] = $hdstyle >= 0 && $hdstyle < 4 ? $hdstyle : 3;
		
		for($i=0; $i<3; $i++){
			
			$c = getfile('http://www.tudou.com/outplay/goto/getItemSegs.action?iid='.$key.'&r='.time(),  'http://www.tudou.com', null);
			
			if(strpos($c, 'baseUrl') !== false)break;
			
		}
		
		$json = json_decode($c, false);
		
		for($s=0, $mhds=0;$s<count($hdstr);$s++){
			
			if(!empty($json->$hdstr[$s]))$mhds++;
			
			if($mhds > 3 || $s > 3)break;
				
		}
		
		$video['mixhds'] = isset($mhds) ? $mhds : null;
		
		$video['Nowhds'] = min($video['mixhds']-1, $video['Nowhds']);
		
		@$info = !empty($json->$hdstr[$video['Nowhds']]) ? $json->$hdstr[$video['Nowhds']] : $hdstr[$video['Nowhds']-1];
		
		if(empty($info))@$info = $json->$hdstr[$video['Nowhds']+1];
		
		if(!empty($info)){
			
			$i = 0;
							
			foreach((array)@$info as $value){
				
				$k_Str = $value->k;
				
				for($s=0; $s<=3; $s++){
			
			    //$video_info = getfile("http://v2.tudou.com/f?sj=1&sid=10000&hd=3&id=".$key, 'http://www.tudou.com', null);
					$video_info = getfile('http://v2.tudou.com/f?sj=1&sid=10000&hd=3&id='.$k_Str.'&rand='.time(), 'http://www.tudou.com', null);
					//http://v2.tudou.com/f?id=298572851&sid=11000&hd=3&sj=1&areaCode=211000
					
					preg_match('#<f[^>]*>(.*?)<\/f>#is',$video_info, $video_src);
					
					if(!empty($video_src[1]))break;
			
				}
	
				$video['data'][$i]['src'] = str_replace('&amp;', '&', $video_src[1]);
						
				$video['data'][$i]['duration'] = $value->seconds / 1000;
					
				$video['data'][$i]['bytes'] = $value->size;
					
				$i++;	
					
			}
			
			
		}
		
		if(empty($video['data'][0]['src']))return false;
		
		return $video;
	 }
	   else{
		
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
 }
 
 //手机解析
 
 	function GetVideo_HTML5($key, $hdstyle){
 		
 		
      if(preg_match('/^[0-9]*$/',$key)){
	       $url = tudourul($key); 
	       $tudoukey = tudouvid($url);
	       if(!empty($tudoukey))$key=$tudoukey;
	       }
     
      if(preg_match('/^[0-9]*$/',$key)){

 	    $um3u8 = 'http://vr.tudou.com/v2proxy/v2.m3u8?it=';
	    $um3u8 .= $key;
	    $um3u8 .= '&st=2';
	
      $video['data'][0]['src'] = $um3u8;
  
      if(empty($video['data'][0]['src']))return false;
		
		return $video;
 
   }
 

    else{	
    	
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

function tudouvid($url){	

		for($i=0; $i<3; $i++){		
			$c = getfile($url, $url, null);		
			if($c)break;		
		}	
		preg_match("/,vcode:[\s]+\'([X]{1}.*?)\'/i", $c, $ketStr);
		
		if(empty($ketStr))preg_match("/,iid:[\s]+([0-9]+)/i", $c, $ketStr);
		
	  if(!empty($ketStr[1]))$key = $ketStr[1];
	
	   if(empty($key))return false;
	   
	   return $key;
	   
}

 function tudourul($value)
    {
   $all['url'] = 'parse';
   $all['tudou'] = 'tudou';
   $type = 'tudou';
   $class_video = new class_video;
   $tudouarr = call_user_func_array( array( $class_video, $all[$type] ), array( $value ) );    
   $tudouurl=$tudouarr[url];
   $isfile = get_headers($tudouurl);
   $result = str_replace("Location: ","",$isfile[10]);
    return $result;
 }

class class_video{
	
	// 超时时间
	var $timeout = 5;
	
	/**
	*	解析视频
	*
	*	1 参数 url 地址
	*
	*	返回值 数组 or false
	**/
	function parse( $url ) {
		$arr = parse_url( $url );
		if ( empty( $arr['host'] ) ) {
			return false;
		}
		$host = strtolower( preg_replace( '/.*(?:$|\.)(\w+(?:\.(?:com|net|org|co|info)){0,1}\.[a-z]+)$/iU', '$1', $arr['host'] ) );

		if ( $host == 'tudou.com' ) {
			return $this->tudou( $url );
		}
		
		return false;
	}
	
	/**
	*	土豆的
	*
	*	1 参数 vid or url
	*
	*	返回值 false array
	**/
	function tudou( $vid ) {
		if ( !$vid ) {
			return false;
		}
		if ( !preg_match( '/^[0-9a-z_-]+$/i', $vid ) ) {
			if ( !preg_match( '/^http\:\/\/www\.tudou\.com\/programs\/view\/([0-9a-z_-]+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/www\.tudou\.com\/v\/([0-9a-z_-]+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/www\.tudou\.com\/(?:listplay|albumplay)\/[0-9a-z_-]+\/([0-9a-z_-]+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/www\.tudou\.com\/(?:a|l)\/[0-9a-z_-]+\/.+iid\=(\d+)/i', $vid, $match ) ) {
				return false;
			}
			$vid = $match[1];
		}
		
		
		$url = 'http://www.tudou.com/v/'. $vid .'/v.swf';
		$this->url( $url, $header );
		if( empty( $header['Location'] ) ) {
			return false;
		}
		$parse = parse_url( $header['Location'] );
		if ( empty( $parse['query'] ) ) {
			return false;
		}
		$this->parse_str( $parse['query'], $arr );
		if ( empty( $arr['snap_pic'] ) ) {
			return false;
		}
		$r['vid'] = $arr['code'];
		$r['url'] = 'http://www.tudou.com/programs/view/'. $arr['code'];
		$r['swf'] = 'http://www.tudou.com/v/'. $arr['code'] .'/lianyue.swf';
		$r['title'] = $arr['title'];
		$r['img']['large'] = $arr['snap_pic'];
		$r['img']['small'] = str_replace( array( '/w.jpg', 'ykimg.com/11' ), array( '/p.jpg', 'ykimg.com/01' ), $arr['snap_pic'] );
		$r['time'] = $arr['totalTime'] / 1000;
    
		return $r;
	}
	
	/**
	*	打开 url
	*
	*	1 参数 url 地址
	*	2 参数 header 引用
	*
	*	返回值 字符串
	**/
	function url( $url = '',  &$header = array() ) {
		$timeout = $this->timeout;
		$accept = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1478.0 Safari/537.36';
		
		$content = '';
		
		
		if ( function_exists( 'curl_init' ) ) {
			// curl 的
			$curl = curl_init( $url );
			curl_setopt( $curl, CURLOPT_DNS_CACHE_TIMEOUT, 86400 ) ;	
			curl_setopt( $curl, CURLOPT_DNS_USE_GLOBAL_CACHE, true ) ;	
			curl_setopt( $curl, CURLOPT_BINARYTRANSFER, true );		
			curl_setopt( $curl, CURLOPT_ENCODING, 'gzip,deflate' );
			curl_setopt( $curl, CURLOPT_HEADER, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_USERAGENT, $accept );
			curl_setopt( $curl, CURLOPT_TIMEOUT, $timeout );
			$content = curl_exec ( $curl );
			curl_close( $curl );
		
		} elseif ( function_exists( 'file_get_contents' ) ) {
			
			// file_get_contents
			$head[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
			$head[] = "User-Agent: $accept";
			$head[] = "Accept-Language: zh-CN,zh;q=0.5";
			$head = implode( "\r\n", $head ). "\r\n\r\n";
			
			$context['http'] = array ( 
				'method' => "GET" ,  
				'header' => $head,
				'timeout' => $timeout,
			);   
			
			$content = @file_get_contents( $url, false , stream_context_create( $context ) );
			if ( $gzip = $this->gzip( $content ) ) {
				$content = $gzip;
			}
			$content = implode( "\r\n", $http_response_header ). "\r\n\r\n" . $content;
			
		} elseif ( function_exists('fsockopen') || function_exists('pfsockopen') ) {
			// fsockopen or pfsockopen
			$url = parse_url( $url );
			if ( empty( $url['host'] ) ) {
				return false;
			}
			$url['port'] = empty( $url['port'] ) ? 80 : $url['port'];
			
			$host = $url['host'];
			$host .= $url['port'] == 80 ? '' : ':'. $port;
			
			$get = '';
			$get .= empty( $url['path'] ) ? '/' : $url['path'];
			$get .= empty( $url['query'] ) ? '' : '?'. $url['query'];
			
			
			$head[] = "GET $get HTTP/1.1";
			$head[] = "Host: $host";
			$head[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
			$head[] = "User-Agent: $accept";
			$head[] = "Accept-Language: zh-CN,zh;q=0.5";
			$head[] = "Connection: Close";
			$head = implode( "\r\n", $head ). "\r\n\r\n";
 			
			$function = function_exists('fsockopen') ? 'fsockopen' : 'pfsockopen';
			if ( !$fp = @$function( $url['host'], $url['port'], $errno, $errstr, $timeout ) ) {
				return false;
			}
			
			if( !fputs( $fp, $head ) ) {
				return false;
			}
			
			while ( !feof( $fp ) ) {
				$content .= fgets( $fp, 1024 );
			}
			fclose( $fp );
			
			if ( $gzip = $this->gzip( $content ) ) {
				$content = $gzip;
			}
			
			$content = str_replace( "\r\n", "\n", $content );
			$content = explode( "\n\n", $content, 2 );
			
			if ( !empty( $content[1] ) && !strpos( $content[0], "\nContent-Length:" ) ) {
				$content[1] = preg_replace( '/^[0-9a-z\r\n]*(.+?)[0-9\r\n]*$/i', '$1', $content[1] );
			}
			$content = implode( "\n\n", $content );
		}
		
		// 分割 header  body
		$content = str_replace( "\r\n", "\n", $content );
		$content = explode( "\n\n", $content, 2 );	
		
		// 解析 header
		$header = array();
		foreach ( explode( "\n", $content[0] ) as $k => $v ) {
			if ( $v ) {
				$v = explode( ':', $v, 2 );
				if( isset( $v[1] ) ) {					
					if ( substr( $v[1],0 , 1 ) == ' ' ) {
						$v[1] = substr( $v[1], 1 );
					}
					$header[trim($v[0])] = $v[1];
				} elseif ( empty( $r['status'] ) && preg_match( '/^(HTTP|GET|POST)/', $v[0] ) ) {
					$header['status'] = $v[0];
				} else {
					$header[] = $v[0];
				}
			}
		}
		
		
		$body = empty( $content[1] ) ? '' : $content[1];
		return $body;
	}
	
	
	/**
	*	gzip 解压缩
	*
	*	1 参数 data
	*
	*	返回值 false or string
	**/
	function gzip( $data ) {
        $len = strlen ( $data );
        if ($len < 18 || strcmp ( substr ( $data, 0, 2 ), "\x1f\x8b" )) {
            return null; // Not GZIP format (See RFC 1952) 
        }
        $method = ord ( substr ( $data, 2, 1 ) ); // Compression method 
        $flags = ord ( substr ( $data, 3, 1 ) ); // Flags 
        if ($flags & 31 != $flags) {
            // Reserved bits are set -- NOT ALLOWED by RFC 1952 
            return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations) 
        $mtime = unpack ( "V", substr ( $data, 4, 4 ) );
        $mtime = $mtime [1];
        $xfl = substr ( $data, 8, 1 );
        $os = substr ( $data, 8, 1 );
        $headerlen = 10;
        $extralen = 0;
        $extra = "";
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header 
            if ($len - $headerlen - 2 < 8) {
                return false; // Invalid format 
            }
            $extralen = unpack ( "v", substr ( $data, 8, 2 ) );
            $extralen = $extralen [1];
            if ($len - $headerlen - 2 - $extralen < 8) {
                return false; // Invalid format 
            }
            $extra = substr ( $data, 10, $extralen );
            $headerlen += 2 + $extralen;
        }
     
        $filenamelen = 0;
        $filename = "";
        if ($flags & 8) {
            // C-style string file NAME data in header 
            if ($len - $headerlen - 1 < 8) {
                return false; // Invalid format 
            }
            $filenamelen = strpos ( substr ( $data, 8 + $extralen ), chr ( 0 ) );
            if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
                return false; // Invalid format 
            }
            $filename = substr ( $data, $headerlen, $filenamelen );
            $headerlen += $filenamelen + 1;
        }
     
        $commentlen = 0;
        $comment = "";
        if ($flags & 16) {
            // C-style string COMMENT data in header 
            if ($len - $headerlen - 1 < 8) {
                return false; // Invalid format 
            }
            $commentlen = strpos ( substr ( $data, 8 + $extralen + $filenamelen ), chr ( 0 ) );
            if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
                return false; // Invalid header format 
            }
            $comment = substr ( $data, $headerlen, $commentlen );
            $headerlen += $commentlen + 1;
        }
     
        $headercrc = "";
        if ($flags & 1) {
            // 2-bytes (lowest order) of CRC32 on header present 
            if ($len - $headerlen - 2 < 8) {
                return false; // Invalid format 
            }
            $calccrc = crc32 ( substr ( $data, 0, $headerlen ) ) & 0xffff;
            $headercrc = unpack ( "v", substr ( $data, $headerlen, 2 ) );
            $headercrc = $headercrc [1];
            if ($headercrc != $calccrc) {
                return false; // Bad header CRC 
            }
            $headerlen += 2;
        }
     
        // GZIP FOOTER - These be negative due to PHP's limitations 
        $datacrc = unpack ( "V", substr ( $data, - 8, 4 ) );
        $datacrc = $datacrc [1];
        $isize = unpack ( "V", substr ( $data, - 4 ) );
        $isize = $isize [1];
     
        // Perform the decompression: 
        $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // This should never happen - IMPLEMENTATION BUG! 
            return null;
        }
        $body = substr ( $data, $headerlen, $bodylen );
        $data = "";
        if ($bodylen > 0) {
            switch ($method) {
                case 8 :
                    // Currently the only supported compression method: 
                    $data = gzinflate ( $body );
                    break;
                default :
                    // Unknown compression method 
                    return false;
            }
        } else {
            //...
        }
     
        if ($isize != strlen ( $data ) || crc32 ( $data ) != $datacrc) {
            // Bad format!  Length or CRC doesn't match! 
            return false;
        }
        return $data;
    }
	
	
	/**
	*	解析数组
	*
	*	1 参数 str
	*	2 参数 arr 引用
	*
	*	返回值 无
	**/
	function parse_xml( $xml ) {
		if ( preg_match_all("/\<(?<tag>[a-z]+)\>\s*\<\!\[CDATA\s*\[(.*)\]\]\>\s*\<\/\k<tag>\>/iU", $xml, $matches ) ) {
			$find = $replace = array();
			foreach ( $matches[0] as $k => $v ) {
				$find[] = $v;
				$replace[] = '<'. $matches['tag'][$k]  .'>' .htmlspecialchars( $matches[2][$k] , ENT_QUOTES ). '</' . $matches['tag'][$k].'>';
			}
			 
			$xml = str_replace( $find, $replace, $xml );
		}
		if( !$xml = @simplexml_load_string( $xml ) ) {
			return false;
		}
		return $this->turn_array( $xml );
	}
	
	/**
	*	解析数组
	*
	*	1 参数 str
	*	2 参数 arr 引用
	*
	*	返回值 无
	**/
	function parse_str( $str, &$arr ) {
		parse_str( $str, $arr );
		if ( get_magic_quotes_gpc() ) {
			$arr = $this->stripslashes_array( $arr );
		}
	}
	
	/**
	*	stripslashes 取消转义 数组
	*
	*	1 参数 输入数组
	*
	*	返回值 处理后的数组
	**/
	function stripslashes_array( $value ) {
		if ( is_array( $value ) ) {
			$value = array_map( array( $this, __FUNCTION__ ), $value );
		} elseif ( is_object( $value ) ) {
			$vars = get_object_vars( $value );
			foreach ( $vars as $key => $data ) {
				$value->{$key} = stripslashes_array( $data );
			}
		} else {
			$value = stripslashes( $value );
		}
		return $value;
	}

	/**
	*	转换成 数组
	*
	*	1 参数 需要进行处理的 类 或者 数组 支持多维数组
	*
	*	返回值 处理后的数组
	**/
	function turn_array( $arr = array() ) {
		$arr = (array) $arr;
		$r = array();
			foreach ( $arr as $k => $v ) {
				if( is_object( $v ) || is_array( $v ) ) {
					$r[$k] = $this->turn_array( $v );
				} else {
					$r[$k] = $v;
				}
			}
		return $r;
	}
	
		
	/**
	*	删除 数组中 的空值
	*
	*	1 参数 数组
	*	2 参数 是否回调删除多维数组
	*
	*	返回值 数组
	**/
	function array_unempty( $a = array(), $call = false ) {

		foreach ( $a as $k => $v ) {
			if ( $call && is_array( $a ) && $a ) {
				 $a[$k] = $this->array_unempty( $a, $call );
			}
			if ( empty( $v ) ) {
				unset( $a[$k] );
			}
		}
		return $a;
	}
}	


?>