<?php
    require_once "spider.php";

    //设置允许跨域
    header("Access-Control-Allow-Origin: *");

    //参数是微信公众号名字
    $data=weixinSpider('创新与创业实践基地');

    echo json_encode($data);
?>
