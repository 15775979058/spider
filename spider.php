<?php

//通过curl获取页面内容
function getUrlContent($url)
{
    $ch = curl_init();

    //设置header，可选
    // $headers = array();
    // $headers[] = 'X-Apple-Tz: 0';
    // $headers[] = 'X-Apple-Store-Front: 143444,12';
    // $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    // $headers[] = 'Accept-Language: en-US,en;q=0.5';
    // $headers[] = 'Cache-Control: no-cache';
    // $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
    // $headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
    // $headers[] = 'X-MicrosoftAjax: Delta=true';

    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//匹配函数
function getResult($url,$regular)
{
    $webContent = getUrlContent($url);
    //echo $webContent;
    if($webContent!=false){
        //匹配
        $result = preg_match_all($regular, $webContent, $matchResult);

        if($result){
            return $matchResult;
        }else{
            return 404;
        }
    }else{
        return 500;
    }
}

//错误信息定义
function errorData($code){
    if($code==404){
        return ['code'=>404,'message'=>'没有匹配'];
    }else {
        return ['code'=>500,'message'=>'页面读取失败'];
    }
}

//爬取某个微信前十条信息
function weixinSpider($name){
    $url ="http://weixin.sogou.com/weixin?&query=".$name."&ie=utf8";
    $regular = "/<[a|A].*?href=[\'\"]{0,1}([^>\'\"\ ]*).*?>.*?<em><!--red_beg-->".$name."<!--red_end--><\/em>/";

    $result=getResult($url,$regular);
    if($result=="404"){
        $data=errorData(404);
        return json_encode($data);
    }else if($result=="500"){
        $data=errorData(500);
        return json_encode($data);
    }else{
        $weixinUrl=$result[1][0];
        $weixinUrl=str_replace('&amp;', '&', $weixinUrl);
        $weixinRegular='/msgList.*?[\{]{0,1}.*?[\}]\;/';

        $weixinResult=getResult($weixinUrl,$weixinRegular);
        if($weixinResult==404){
            $data=errorData(404);
            return json_encode($data);
        }else if($weixinResult==500){
            $data=errorData(500);
            return json_encode($data);
        }else{
            $json=$weixinResult[0][0];
            $json=str_replace('msgList =', '', $json);
            $json=str_replace(';', '', $json);
            $json=json_encode($json);

            $data['code']=200;
            $data['list']=$json;
            return json_encode($data);
        }
    }
}


//面向过程写法
/*
$url ="http://weixin.sogou.com/weixin?type=1&s_from=input&query=创新与创业实践基地&ie=utf8&_sug_=n&_sug_type_=";
$webContent = getUrlContent($url);

//echo $webContent;
if($webContent!=false){
	//正则匹配
    $regular = '/<[a|A].*?href=[\'\"]{0,1}([^>\'\"\ ]*).*?>.*?<em><!--red_beg-->创新与创业实践基地<!--red_end--><\/em>/';
    //匹配
    $result = preg_match_all($regular, $webContent, $matchResult);

    if($result){
        $json=$matchResult[1][0];
    }else{
        echo "404";
    }
}else{
    echo "error";
}

$json=str_replace('&amp;', '&', $json);

$url=$json;
//获取网站内容
$webContent = getUrlContent($url);
//echo $webContent;
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
}*/

?>


