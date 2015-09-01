<?php
Class Common_Api {
	public $_apiUrl = NULL;
	
	
	public $_domain = NULL;
	
	
	public $_apiName = NULL;
	
	
	public $_dnsTimeout = 1;
	
	
	public $_timeout = 5;
	
	
	public $_params = NULL;
	
	
	public $_commonParams = NULL;
	
	
	public $_data = NULL;
	
	
	public $_error = NULL;
	
	
	
	public function __construct( $params='' ) {
		if ( $params  &&  is_array( $params ) ) {
			$this->_params = $params;
		}
	}
	
	
	
	public static function GetInst( $params='' ) {
		return new self( $params );
	}
	
	
	/**
	 * POST方式请求接口
	 * 
	 * @return Common_Api
	 */
	public function post() {
		$url    = $this->_apiUrl;
		
		//拼接通用参数和用户参数
		$params = $this->getParamArr();
		
		//请求接口,获取返回数据
		$data = Common_Utility_Http::HttpGetContent( $url, 'post', $params );
		
		if ( $data ) {
			$this->_data = $data;
		}
		
		return $this;
	}
	
	
	/**
	 * GET方式请求接口
	 * 
	 * @return Common_Api
	 */
	public function get() {
		//拼接通用参数和用户参数
		$params = $this->getParamArr();

		//将参数拼接至URL地址中
		$url    = self::GetRequestUrl( $this->_apiUrl, $params );
		
		//请求接口,获取返回数据
		$data = Common_Utility_Http::HttpGetContent( $url, 'get' );
		
		if ( $data ) {
			$this->_data = $data;
		}
		
		return $this;
	}
	
	
	/**
	 * 合并通用参数和用户提交参数
	 * 
	 * @param array $this->_commonParams
	 * @param array $this->_params
	 * @return array
	 */
	public function getParamArr() {
		$params = NULL;
		
		if ( is_array( $this->_commonParams )  &&  !empty( $this->_commonParams )
				&&  is_array( $this->_params )  &&  !empty( $this->_params ) ) {
			
			$params = array_merge( $this->_commonParams, $this->_params );
			
		}
		else {
			$params = $this->_params;
		}

		return $params;
	}
	
	
	/**
	 * 生成get请求方式url
	 * 
	 * @param string $url
	 * @param array $params
	 * @return mixed string | null
	 */
	public static function GetRequestUrl( $url, $params ) {
		$getUrl = NULL;
				
		if ( $url ) {
		
			$getParamStr = http_build_query( $params );
			
			if ( false !== strpos( '?', $url ) ) {
				$getUrl .= '&' . $getParamStr;
			}
			else {
				$gerUrl .= $getParamStr;
			}
		}
		
		return $getUrl;
	}
}