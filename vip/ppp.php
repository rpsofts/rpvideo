<?php
$url=$_GET['url'];
$VideoIDS = VideoIDS($url);
function VideoIDS($url){
	preg_match("#id\_([\w=]+)#", $url, $matches); //id里可以有=号
	return $matches[1];
}
?>
  <div id="main">		
           <style> html,body{ margin:0px; height:100%; } </style>         
<div id="a1">
<script type="text/javascript" src="2/ckplayer.js" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var flashvars={f:'vip/youkuvip.php?VideoIDS=[$pat]',a:'cq_<?php echo $VideoIDS; ?>_youku',s:2};
  var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
    var video=['api/index.php?url=<?php echo $url; ?>&mobile'];
CKobject.embedSWF('player/player.swf','a1','ckplayer_a1','100%','100%',flashvars,params);
</script>
  <script type="text/javascript" src="http://tajs.qq.com/stats?sId=51750708" charset="UTF-8"></script>
</div>