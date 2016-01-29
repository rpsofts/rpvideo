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
<div id="a1"> </div> 
<script type="text/javascript" src="2/ckplayer.js" charset="utf-8"></script>
<script type="text/javascript">
    var flashvars={
        f:'api/index.php?url=<?php echo $url; ?>',
        a:88,
        s:2,
        c:0
    };
    var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
    var video=['api/index.php?url=<?php echo $url; ?>&mobile'];
    CKobject.embed('/player/player.swf','a1','ckplayer_a1','100%','100%',false,flashvars,video,params);
</script>  
    <script type="text/javascript" src="http://tajs.qq.com/stats?sId=51750708" charset="UTF-8"></script>

</div>