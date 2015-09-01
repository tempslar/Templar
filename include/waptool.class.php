<?php
class WapTool {
	
	function urlGetContents($url, $con_timeout=1, $read_timeout=3 ,$method="GET", $postdata=[], $compressed = false, $user = '', $pass = '', $extraHeaders = FALSE)
	{
		static $http;
		if (!is_object($http))
		{
			$http = new WapHttp();
		}
		$http->reset();
		$http->setTimeout($con_timeout, $read_timeout);
		if ($method == "POST" && $postdata)
		{
			$http->setPostData([]);
			$http->addPostData($postdata);
		}
		//增加验证功能
		if ($user) {
		    $http->setAuth($user, $pass);
		}
		//设置额外的HTTP头
		if (is_array($extraHeaders) && !empty($extraHeaders)) {
		    foreach ($extraHeaders as $key => $value) {
		        $http->setHead($key, $value);
		    }
		}
		$http->openUrl($url, $method, $compressed);
		$result = $http->getResult();
		$http->close();
		return $result;
	}
	
	/**
	 * 解析xml，只支持UTF-8
	 */
	function xmlDecode($xml, $element_name='', $start=false, $limit=false)
	{
		$xml_parser = new XMLDecode();
		$xml_parser->parse($xml);
		$result = [];
		if ($element_name)
		{
			if ($start !== false && $start >= 0)
			{
				if ($limit !== false && $limit > 0)
				{
					$result = $xml_parser->getResult($element_name, $start, $limit);
				}
				else
				{
					$result = $xml_parser->getResult($element_name, $start);
				}
			}
			else
			{
				$result = $xml_parser->getResult($element_name);
			}
		}
		else
		{
			$result = $xml_parser->getResult();
		}

		return $result;
	}
}