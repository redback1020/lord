<?php
 
function fetch_page($url,$postArr=false)
{
	   // 初始化CURL
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	  
	   // 设置提交方式
	   curl_setopt($ch, CURLOPT_POST, count($postArr));
	   
	  // 传递信息
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $postArr);
	  // 头部信息不获取
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  // 返回原生的（Raw）输出
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	  // 执行并获取返回结果
	  $content = curl_exec($ch);
	  // 关闭CURL
	  curl_close($ch);
	   
	  return dealBom($content);
}

function dealBom($content){
  return json_decode(trim($content, chr(239).chr(187).chr(191)), true); // 0xEF 0xBB 0xBF
 }

 

?>