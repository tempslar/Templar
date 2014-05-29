<?php
Class Common_Utility_Http {
	/**
	 * 尝试链接时间(s)
	 * 
	 * @var int
	 */
	CONST CONNECT_TRY_TIME = 1;
	
	
	/**
	 * 保持链接时间(s)
	 * 
	 * @var int
	 */
	CONST CONNECT_ALIVE_TIME = 3;
	
	
	/**
	 * HTTP链接操作
	 * @param unknown_type $url
	 * @param unknown_type $method
	 * @param unknown_type $connectTime
	 * @param unknown_type $timeout
	 * @param unknown_type $params
	 */
	static public function HttpGetContent( $url, $method='get', $connectTime=self::CONNECT_TRY_TIME, $timeout=CONNECT_ALIVE_TIME, $params=array() ) {
		/*
		$data = file_get_contents( $url );
		
		if ( !is_null( $data )  &&  !empty( $data ) ) {
			return $data;
		}

		return NULL;
		*/
		
		return self::Curl( $url );
	}
	
	
	/**
	 * Curl 函数封装
	 * 
	 * @param unknown_type $api
	 * @param unknown_type $method
	 * @param unknown_type $params
	 */
	static public function Curl( $url, $method='get', $postParams=array() ) {
		$curl = curl_init(  );
		curl_setopt( $curl, CURLOPT_URL, $url );//请求地址
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_ENCODING, 'gzip' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );	//将返回值传递给变量
		
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 1 );//最大链接时间
		curl_setopt( $curl, CURLOPT_TIMEOUT, 5 );//最大保持链接时间

		$output = curl_exec( $curl );
		
		$httpCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		
		if ( 200 == $httpCode ) {
			
			curl_close( $curl );
			
			//var_dump( __METHOD__,  json_decode( $output, true ) );
			return $output;
		}
		
		return NULL;
	}
	
	
	
	static public function Socket() {
		
	}
	
	
}