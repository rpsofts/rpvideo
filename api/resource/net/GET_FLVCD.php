<?php
function Get_FLVCD($url, $hdstyle){
				
	// HD Style Information
	$hdstr = array(0 => "normal",1 => "high",2 => "super");
	// --END-- HD Style Information
		
	$video['Nowhds'] = $hds = $hdstyle >= 0 && $hdstyle < 3 ? $hdstyle : 2;
		
	$video['mixhds'] = 3;
		
	for($i=0; $i<3; $i++){

		$data = getfile('http://www.flvcd.com/parse.php?kw='.$url.'&format='.$hdstr[$hds], 'http://www.flvcd.com/', null);
		
		if(!empty($data))break;
		
	}
		
	if(!empty($data)){
		
		$data = kms_iconv($data, 'GBK', 'UTF-8');
					
		preg_match("/document.title[\s]+=[\s]+\"(.*?)\"/i", $data, $datarow);
		if(empty($datarow[1]))preg_match("/hidden\"[\s]+name=\"name\"[\s]+value=\"(.*?)\"/i", $data, $datarow);
		if(empty($datarow[1]))preg_match("/hidden\"[\s]+name=\"filename\"[\s]+value=\"(.*?)\"/i", $data, $datarow);
			
		if(!empty($datarow[1]))$video['subject'] = $datarow[1];	

		preg_match("/<input[\s]+type=\"hidden\"[\s]+name=\"inf\"[\s]+value=\"(.*?)\"\/>/i", $data, $str);
		if(empty($str[1]))preg_match_all("/<BR><a[\s]+href=\"(.*?)\"/i", $data, $str2);
		if(empty($str2[1]) && empty($str[1]))preg_match_all("/<a[\s]+href=\"(.*?)\"[\s]+target=\"_blank\"[\s]+class=\"link/i", $data, $str2);
		
		if(!empty($str))$part =  array_filter(explode("|", $str[1]), "strlen");
		if(!empty($str2))$part =  array_filter($str2[1], "strlen");
		
		if(empty($part))return false;
			
		$i = 0;
		
		foreach($part as $value){
			
			$video['data'][$i]['src'] = $value;
		
			$i++;
				
		}
						
	}
	
	if(empty($video['data'][0]['src']))return false;
	
	return $video;
}
?>