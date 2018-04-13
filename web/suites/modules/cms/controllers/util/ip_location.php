<?php
/*
 * 根据腾讯IP分享计划的地址获取IP所在地，比较精确
 */
function getIPLoc_QQ($queryIP) {
	if ($queryIP == "::1") {
		return "本机访问";
	}
	$url = 'http://ip.qq.com/cgi-bin/searchip?searchip1=' . $queryIP;
	$ch = curl_init ( $url );
	curl_setopt ( $ch, CURLOPT_ENCODING, 'gb2312' );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); // 获取数据返回
	$result = curl_exec ( $ch );

	$result = mb_convert_encoding ( $result, "utf-8", "gb2312" ); // 编码转换，否则乱码
	curl_close ( $ch );
	//print_r($ipArray);
	preg_match ( "@您当前的IP为：<span class=\"red\">(.*)</span></p>@iU", $result, $ipArray );

	//print_r($result);
	//echo "dddd";
	//exit();
	//print_r($ipArray);
	if(!empty($ipArray))
	{
		$loc = $ipArray [1];
	}else
	{

		$loc = "";
	}
	return $loc;
}

/**
 * 获取当前IP
 *
 * @return Ambigous <string, unknown>
 */
function GetIP() {
	$IPaddress = '';
	
	if (isset ( $_SERVER )) {
		
		if (isset ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
			
			$IPaddress = $_SERVER ["HTTP_X_FORWARDED_FOR"];
		} else if (isset ( $_SERVER ["HTTP_CLIENT_IP"] )) {
			
			$IPaddress = $_SERVER ["HTTP_CLIENT_IP"];
		} else {
			
			$IPaddress = $_SERVER ["REMOTE_ADDR"];
		}
	} else {
		
		if (getenv ( "HTTP_X_FORWARDED_FOR" )) {
			
			$IPaddress = getenv ( "HTTP_X_FORWARDED_FOR" );
		} else if (getenv ( "HTTP_CLIENT_IP" )) {
			
			$IPaddress = getenv ( "HTTP_CLIENT_IP" );
		} else {
			
			$IPaddress = getenv ( "REMOTE_ADDR" );
		}
	}
	
	return $IPaddress;
}
?>