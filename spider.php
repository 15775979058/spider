<?php

function getUrlContent($url)
{
    $handle = fopen($url, "r");
    if ($handle) {
        $content = stream_get_contents($handle, 1024 * 1024);
        return $content;
    } else {
        return false;
    }
}
//允许跨域
header("Access-Control-Allow-Origin: *");

//爬取搜狗微信搜索提供的，一个公众号的前十条推文的内容
$url = "https://mp.weixin.qq.com/profile?src=3&timestamp=1501246217&ver=1&signature=4jbwo-z*VvJ2ACvNnzfx-sbhlyz9KA0Mes4lwZa0bxo85JIcBCrU*WFBHzMgC8ft*pe8nzN2cZwkuo63q-*cFA==";

//获取网站内容
$webContent = getUrlContent($url);

if($webContent!=false){
    //正则匹配
    $regular = '/msgList.*?[\{]{0,1}.*?[\}]\;/';
    //匹配
    $result = preg_match_all($regular, $webContent, $matchResult);

    if($result){
        $json=$matchResult[0][0];
        $json=str_replace('msgList =', '', $json);
        $json=str_replace(';', '', $json);

        echo json_encode($json);
    }else{
        echo "404";
    }

}else{
    echo "error";
}



?>


