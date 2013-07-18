<?php

/*
 * Wap公用的HTTP接口类
 *
 * example:
 * $http = new WapHttp();
 * $http->openUrl('http://localhost');
 * $result = $http->getResult();
 */

class WapHttp
{
	var $url = '';
	var $baseurl = '';
	var $urlpath = '';
	var $urlquery = '';
	var $scheme = 'http';
	var $host = '';
	var $port = '80';
	var $auth = array();
	var $error = '';
	var $httphead = '';
	var $html = '';
	var $puthead = '';
	var $reTry = 0;
	var $jumpCount = 0;
	var $maxRetry = 1;
	var $maxJumpCount = 1;
	var $postdata = array();
	var $request_start_time;
	var $request_end_time;

	var $fp;
	var $connectTimeout = 5;
	var $readTimeout = 10;
    //是否使用压缩
	var $compressed = false;

	var $log;

	function WapHttp($project_name='')
	{
		$proj = 'xxx';//WapCommon::getProjectName($project_name);
		//$proj_log_dir = WapCommon::getProjectLogDir();
		//$this->log = $proj_log_dir . '/http_' . date("Ymd") . '.log';
	}

	//初始化系统
	function init($url)
	{
		if($url=='') {
			return ;
		}
		$urls = '';
		$urls = @parse_url($url);
		$this->url = $url;
		if(is_array($urls))
		{
			$this->host = $urls["host"];
			if(!empty($urls["scheme"]))
			{
				$this->scheme = $urls["scheme"];
			}
			if(!empty($urls["user"]))
			{
				$this->auth['user'] = $urls["user"];
			}
			if(!empty($urls["pass"]))
			{
				$this->auth['pass'] = $urls["pass"];
			}
			if(!empty($urls["port"]))
			{
				$this->port = $urls["port"];
			}
			if(!empty($urls["path"]))
			{
				$this->urlpath = $urls["path"];
			}
			if(!empty($urls["query"]))
			{
				$this->urlquery = $urls["query"];
			}
			if ($this->urlpath == '')
			{
				$this->urlpath = '/';
			}
			if ($this->urlquery)
			{
				$this->urlpath .= '?' . $this->urlquery;
			}
			$this->baseurl = $this->host . $this->urlpath;
		}
		else
		{
			$this->error .= 'parse url失败';
			return false;
		}
	}

	function reset()
	{
		//重设各参数
		$this->url		= "";
		$this->baseurl	= "";
		$this->urlpath	= "";
		$this->urlquery = "";
		$this->scheme	= "http";
		$this->host		= "";
		$this->port		= "80";
		$this->auth		= array();
		$this->puthead  = array() ;
		$this->httphead = array() ;
		$this->html		= '';
		$this->reTry	= 0;
		$this->close();
		$this->fp		= NULL;
		$this->compressed = false;
	}

	//打开指定网址
	function openUrl($url, $request_type="GET", $compressed = false)
	{
		$this->request_start_time = time();
		$this->jumpCount = 0;
		$this->error = '';

		//初始化系统
		$this->init($url);
		$this->compressed = $compressed;
		$this->sendRequest($request_type);
	}

	//转到302重定向网址
	function jumpOpenUrl($url)
	{
		$compressed = $this->compressed;
		$this->error .= '目标地址跳转到url:' . $url;
		$this->reset();
		$this->jumpCount++;

		//初始化系统
		$this->init($url);
		$this->compressed = $compressed;
		$this->sendRequest('GET');
	}

	//获取返回的网页的类型
	function getContentType()
	{
		if(!strncmp("2",$this->getHead("http-state"), 1))
		{
			return $this->getHead("content-type");
		}
		else
		{
			return false;
		}
	}

	/**
	 * 返回传输格式
	 *
	*/
	function getContentEnconding() {
		if(!strncmp("2",$this->getHead("http-state"), 1))
		{
			return $this->getHead("content-encoding");
		}
		else
		{
			return false;
		}
	}

	//开始HTTP会话
	function sendRequest($request_type="GET")
	{
		if(!$this->openHost())
		{
			$this->error .= "打开远程主机出错!";
			return false;
		}
		$this->reTry++;
		if($this->getHead("http-version")=="HTTP/1.0")
		{
			$httpv = "HTTP/1.0";
		}
		else
		{
			$httpv = "HTTP/1.1";
		}

		$headString = '';

		//发送固定的起始请求头GET、Host信息
		if($request_type == "GET")
		{
			$headString .= "GET ".$this->urlpath." $httpv\r\n";
		}
        elseif ($request_type == 'DELETE')
        {
            $headString .= "DELETE ".$this->urlpath." $httpv\r\n";
        }
		else
		{
			$headString .= "POST ".$this->urlpath." $httpv\r\n";
		}
		if (!isset($this->puthead["Host"]))
		{
			$this->puthead["Host"] = $this->host;
		}


		//发送用户自定义的请求头
		if(!isset($this->puthead["Accept"]))
		{
			$this->puthead["Accept"] = "*/*";
		}
		if(!isset($this->puthead["User-Agent"]))
		{
			$this->puthead["User-Agent"] = "Opera/9.60";
		}
		if(!isset($this->puthead["Refer"]))
		{
			$this->puthead["Refer"] = "http://".$this->puthead["Host"];
		}

		//发送压缩头
		if ($this->compressed) {
		    $this->puthead['Accept-Encoding'] = 'gzip,deflate';
		}

		foreach($this->puthead as $k=>$v)
		{
			$k = trim($k);
			$v = trim($v);
			if($k != "" && $v != "")
			{
				$headString .= "$k: $v\r\n";
			}
		}
		fputs($this->fp, $headString);
		if($request_type == "POST")
		{
			$postdata = "";
			if ($this->postdata)
			{
				foreach ($this->postdata as $k=>$v)
				{
					$postdata .= "&" . $k . '=' . urlencode($v);
				}
			}
			if ($postdata == '')
			{
				$postdata = "OK";
			}
			$plen = strlen($postdata);
			fputs($this->fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($this->fp, "Content-Length: $plen\r\n");
		}

		//发送固定的结束请求头
		//HTTP1.1协议必须指定文档结束后关闭链接,否则读取文档时无法使用feof判断结束
		if($httpv == "HTTP/1.1")
		{
			fputs($this->fp, "Connection: Close\r\n\r\n");
		}
		else
		{
			fputs($this->fp,"\r\n");
		}
		if($request_type == "POST")
		{
			fputs($this->fp, $postdata);
		}

		//获取应答头状态信息
		if (!$line = fgets($this->fp,256))
		{
			$this->error .= '读取应答头状态信息失败 ';
			return false;
		}
		$httpstas = explode(" ", $line);
		$this->httphead["http-version"] = trim($httpstas[0]);
		$this->httphead["http-state"] = trim($httpstas[1]);
		$this->httphead["http-describe"] = "";
		for($i=2;$i<count($httpstas);$i++)
		{
			$this->httphead["http-describe"] .= " ".trim($httpstas[$i]);
		}

		//获取详细应答头
		while(!feof($this->fp))
		{
            $line = trim(fgets($this->fp,256));
            if ($line == '')
            {
                break;
            }
			$hkey = "";
			$hvalue = "";
			$v = 0;
			for($i=0; $i<strlen($line); $i++)
			{
				if($v==1)
				{
					$hvalue .= $line[$i];
				}
				if($line[$i]==":")
				{
					$v = 1;
				}
				if($v==0)
				{
					$hkey .= $line[$i];
				}
			}
			$hkey = trim($hkey);
			if($hkey!="")
			{
				$this->httphead[strtolower($hkey)] = trim($hvalue);
			}
		}

		/*如果连接被不正常关闭，重试——当url返回内容为空时有bug
		if(feof($this->fp))
		{
			if($this->reTry > $this->maxRetry)
			{
				$this->error .= '已达到最大尝试次数(' . $this->maxRetry . ')';
				return false;
			}
			$this->sendRequest($request_type);
			return;
		}
		*/

		//判断是否是3xx开头的应答
		if(!strncmp("3", $this->httphead["http-state"], 1))
		{
			if($this->jumpCount > $this->maxJumpCount)
			{
				$this->error .= '已达到最大跳转次数(' . $this->maxJumpCount . ')';
				return;
			}
			if(isset($this->httphead["location"]))
			{
				$newurl = $this->httphead["location"];
				if(!strncmp("http", strtolower($newurl), 4))
				{
					$this->jumpOpenUrl($newurl);
				}
				else
				{
					$newurl = $this->FillUrl($newurl);
					$this->jumpOpenUrl($newurl);
				}
			}
			else
			{
				$this->error .= "无法识别的答复！";
			}
		}
	}

	//判断http返回是否为200
	function isSuccess()
	{
		if( !strncmp("2",$this->getHead("http-state"), 1) )
		{
			return true;
		}
		else
		{
			$this->request_end_time = time();
			$this->error .= '请求总耗时:' . ($this->request_end_time - $this->request_start_time) . ' ';
			if (!$this->getHead("http-state"))
			{
				$this->error .= '读取超时,读取超时设置为' . $this->readTimeout;
			}
			else
			{
				$this->error .= 'StatusCode:' . $this->getHead("http-state")." - Describe:".$this->getHead("http-describe")."<br/>";
			}
			$this->logError($this->error);
			return false;
		}
	}

	//获取内容
	function getResult()
	{
		$result = '';
		if (!$this->isSuccess())
		{
			return false;
		}
		if(!is_resource($this->fp))
		{
			$this->request_end_time = time();
			$this->error .= '请求总耗时:' . ($this->request_end_time - $this->request_start_time) . ' ';
			$this->error .= "连接已经关闭！";
			$this->logError($this->error);
			return false;
		}
		if ($this->getHead("transfer-encoding") == "chunked")
		{//chunk encoding
			while (!feof($this->fp))
			{
				//get chunk length
				$line = fgets($this->fp, 1024);
	            if (preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
	                $content_chunked_length = hexdec($matches[1]);
	                while ($content_chunked_length > 0)
	                {//get content
	                	$read_content = fread($this->fp, $content_chunked_length);
	                	$content_chunked_length -= strlen($read_content);
	                	$result .= $read_content;
	                }
	            }
			}
		}
		else
		{
            if ($len = $this->getHead("content-length"))
            {//当能够获取到content-length时，只读取content-length字节内容。股票行情接口需要这样处理。
                while(!feof($this->fp) && $len > 0)
                {
                    $tmp = fread($this->fp, $len);
                    $len -= strlen($tmp);
                    $result .= $tmp;
                }
            }
            else
            {
	            while(!feof($this->fp))
	            {
	                $result .= fread($this->fp,1024);
	            }
            }
		}
		fclose($this->fp);
		$this->request_end_time = time();
		$request_time = $this->request_end_time - $this->request_start_time;
		if ($request_time >= 5)
		{
			$this->error .= '请求耗时较长:' . $request_time;
			$this->logError($this->error);
		}
		return ('gzip' == $this->getContentEnconding()) ? gzinflate(substr($result,10)) : $result;
	}

	//保存到文件
	function saveToFile($filename)
	{
		if (!$this->isSuccess())
		{
			return false;
		}
		if(!is_resource($this->fp) || @feof($this->fp))
		{
			$this->error .= "连接已经关闭！";
			return false;
		}
		if (!WapTool::makeDir(dirname($filename)))
		{
			$this->error .= "文件目录无法创建";
			return false;
		}
		if (!$fp = fopen($filename, "wb"))
		{
			$this->error .= "文件无法打开";
			return false;
		}
		while(is_resource($this->fp) && !feof($this->fp))
		{
			fwrite($fp, fread($this->fp,1024));
		}
		fclose($this->fp);
		fclose($fp);
		return true;
	}

	//获取错误信息
	function getError()
	{
		return $this->error;
	}

	//获得一个Http头的值
	function getHead($headname)
	{
		$headname = strtolower($headname);
		return isset($this->httphead[$headname]) ? $this->httphead[$headname] : '';
	}

	//设置Http头的值
	function setHead($skey, $svalue)
	{
		$this->puthead[$skey] = $svalue;
	}

	//设置auth
	function setAuth($user, $pass)
	{
		$this->puthead['Authorization'] = 'Basic ' . base64_encode($user . ':' . $pass);
	}

	//设置连接和读取超时
	function setTimeout($con_timeout=0, $read_timeout=0)
	{
		$this->setConnectTimeout($con_timeout);
		$this->setReadTimeout($read_timeout);
	}

	//设置连接超时
	function setConnectTimeout($timeout)
	{
		if ($timeout > 0)
		{
			$this->connectTimeout = $timeout;
		}
	}

	//设置读取超时
	function setReadTimeout($timeout)
	{
		if ($timeout > 0)
		{
			$this->readTimeout = $timeout;
		}
	}

	function setPostData($data)
	{
		$this->postdata = $data;
	}

	function addPostData($data)
	{
		$this->postdata = array_merge($this->postdata, $data);
	}

	//打开连接
	function openHost()
	{
		if($this->host=="")
		{
			return false;
		}
		$host_ip = $this->host;
		if (!preg_match("#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#", $this->host))
		{
			$s_time = time();
			$host_ip = gethostbyname($this->host);
			$e_time = time();
			$dns_cost_time = $e_time - $s_time;
			if ($dns_cost_time > 1)
			{
				$this->log("host({$this->host}) dns cost too much time: " . $dns_cost_time);
			}
		}
		$errno = "";
		$errstr = "";
		$this->fp = @fsockopen($host_ip, $this->port, $errno, $errstr, $this->connectTimeout);
		if(!$this->fp)
		{
			$this->error .= '连接失败或超时(' . $errstr . ' 连接超时设置:' . $this->connectTimeout . '秒)';
			return false;
		}
		else
		{
			stream_set_timeout($this->fp, $this->readTimeout);
			return true;
		}
	}

	//记录log
	function log($message)
	{
		/*
		$str = "url:" . $this->url . "\n";
		$str .= $message;
		WapLog::finalLog($str, $this->log);
		*/
	}

	//记录error log
	function logError($error)
	{
		$error = "ERROR HAPPENED:" . $error;
		$this->log($error);
	}

	//关闭连接
	function close()
	{
		@fclose($this->fp);
	}

	//补全相对网址
	function FillUrl($surl)
	{
		$i = 0;
		$dstr = "";
		$pstr = "";
		$okurl = "";
		$pathStep = 0;
		$surl = trim($surl);
		if($surl=="")
		{
			return "";
		}
		$pos = strpos($surl,"#");
		if($pos>0)
		{
			$surl = substr($surl,0,$pos);
		}
		if($surl[0]=="/")
		{
			$okurl = "http://".$this->host.$surl;
		}
		else if($surl[0]==".")
		{
			if(strlen($surl)<=1)
			{
				return "";
			}
			else if($surl[1]=="/")
			{
				$okurl = "http://".$this->baseurl."/".substr($surl,2,strlen($surl)-2);
			}
			else
			{
				$urls = explode("/",$surl);
				foreach($urls as $u)
				{
					if($u=="..")
					{
						$pathStep++;
					}
					else if($i<count($urls)-1)
					{
						$dstr .= $urls[$i]."/";
					}
					else
					{
						$dstr .= $urls[$i];
					}
					$i++;
				}
				$urls = explode("/",$this->baseurl);
				if(count($urls) <= $pathStep)
				{
					return "";
				}
				else
				{
					$pstr = "http://";
					for($i=0;$i<count($urls)-$pathStep;$i++)
					{
						$pstr .= $urls[$i]."/";
					}
					$okurl = $pstr.$dstr;
				}
			}
		}
		else
		{
			if(strlen($surl)<7)
			{
				$okurl = "http://".$this->baseurl."/".$surl;
			}
			else if(strtolower(substr($surl,0,7))=="http://")
			{
				$okurl = $surl;
			}
			else
			{
				$okurl = "http://".$this->baseurl."/".$surl;
			}
		}
		$okurl = preg_replace("#^(http://)#i","",$okurl);
		$okurl = preg_replace("#/{1,}#","/",$okurl);
		return "http://".$okurl;
	}
}

?>