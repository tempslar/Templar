<?php
Class Utility_Interface_User extends Utility_Interface {
	
	CONST API_URL = '';
	
	/**
	 * 获取用户信息
	 * @param string $uidStr - 用户uid列表,支持多id,用半角逗号分割
	 * @param boolean $withCache - 是否启用缓存
	 * @return mixed array|null
	 */
	static public function GetUserInfo( $uidStr='', $withCache=TRUE ) {
		
		if ( $withCache ) {
			$cacheKey = 'userinfo_' . $uidStr;
			$apiData  = Common_MC::GetCache( $cacheKey );
		}
		
		if ( !$apiData ) {	//no cache, 则计算
			$url = 'http://shequ.example.com/index.php?app=api&mod=shequ&act=userInfo&oauth_token=test&oauth_token_secret=test';
			
			if ( !empty( $uidStr ) ) {
				$url .= '&user_id=' . $uidStr;
			}
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->showTimeLog( 'READY TO API user_info' );
			
			$apiData = Common_Utility_Http::HttpGetContent( $url );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $url, 'API URL user_info' );
			Common_Utility_Debug::getInstance()->info( $apiData, 'API RETURN user_info' );
			
			//SET CACHE
			if ( $apiData  &&  $withCache  &&  $cacheKey ) {
				Common_MC::SetCacheTime( 86400 );
				Common_MC::SetCache( $cacheKey, $apiData );
			}
			
		}
		
		if ( $apiData ) {	//如果成功获取数据则进行解析
			$apiDatas = json_decode( $apiData, true );
			
			if ( is_array( $apiDatas ) ) {
				$outArr = $apiDatas;
			}
			
		}
		
		return $outArr;
	}
	
}