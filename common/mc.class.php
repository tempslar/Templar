<?php
Class Common_MC {
	
	/**
	 * MC链接静态存储
	 * 
	 * @var object
	 */
	static protected $_mc = NULL;
	
	
	/**
	 * MC配置数组
	 * 
	 * @var array
	 */
	static protected $_mcConfigs = array();
	
	
	/**
	 * MC Key 前缀
	 * 
	 * @var string
	 */
	static protected $_keyPrefix = 'x_api_';
	
	
	/**
	 * MC Key版本
	 * 
	 * @var string
	 */
	static protected $_version   = 10;
	
	
	/**
	 * 默认缓存时间
	 * 
	 * @var int
	 */
	static protected $_cacheTime = 3600;
	
	
	/**
	 * MC再次查询等待时间
	 * 
	 * @var float
	 */
	static protected $_retryTime = 0.05;
	
	
	/**
	 * 私有构造方法
	 */
	private function __construct() {}
	
	
	/**
	 * 私有克隆方法
	 */
	private function __clone() {}
	
	
	/**
	 * 获取单例
	 */
	static public function GetInst() {
		
		//未获取到mc，则推出
		if ( TRUE != self::CheckMC() ) {
			return NULL;
		}
		
		if ( empty( self::$_mcConfigs ) ) {
			self::GetConf();
		}
		
		if ( is_null( self::$_mc ) ) {
			self::Connect();
		}
		
		return self::$_mc;
	}
	
	
	/**
	 * 获取mc配置
	 */
	static public function GetConf() {
		
		if ( isset( $GLOBALS['g_conf']['mc']['default']['0'] ) ) {
			self::$_mcConfigs = $GLOBALS['g_conf']['mc']['default']['0'];
		}
		
	}
	
	
	/**
	 * 检查是否开启Memcache
	 * 
	 * @return boolean
	 */
	static public function CheckMC() {
		/*
		if ( isset( $_REQUEST['refresh'] )  &&  1 == $_REQUEST['refresh']  ) {
			return FALSE;
		}
		*/
		
		if ( !isset( Common_Config::$_useCache )  ||  TRUE !== Common_Config::$_useCache ) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Memcache链接方法
	 */
	static public function Connect() {
		$mc = new Memcache;
		
		$mc->connect( self::$_mcConfigs['host'], self::$_mcConfigs['port'] );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $mc, 'MC CONNECT' );
		
		if ( is_object( $mc ) ) {
			self::$_mc = $mc;
			
			return $mc;
		}
		
		return FALSE;
	}
	
	
	/**
	 * 获取memcache缓存key
	 * 
	 * @param string $key
	 */
	static public function GetCacheKey( $key ) {
		$cacheKey = self::$_keyPrefix . self::$_version . '_' . $key;
		
		return $cacheKey;
	}
	
	
	/**
	 * 设置缓存生存周期
	 * @param int $time
	 */
	static public function SetCacheTime( $time ) {
	
		if ( $time >= 0 ) {
			self::$_cacheTime = $time;
		}
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $time, 'SET CACHE TIME' );
	
		return self::$_cacheTime;
	}
	
	
	/**
	 * 写入缓存
	 * 
	 * @param string $key
	 * @param mixed $datas
	 */
	static public function SetCache( $key, $datas='' ) {
		if ( $key ) {
			$cacheKey = self::GetCacheKey( $key );
			$mc       = self::GetInst();
			
			if ( is_object( $mc ) ) {
				$datas       = Common_Tool::SmartSerialize( $datas );
				
				$cacheResult = $mc->set( $cacheKey, $datas, 0, self::$_cacheTime );
				
				//输出FIREPHP信息
				Common_Utility_Debug::getInstance()->info( $cacheResult, 'MC SET CACHE RESULT' );
				Common_Utility_Debug::getInstance()->log( array( 'key' => $cacheKey, 'data'=>$datas, 'time'=>self::$_cacheTime, ),
															'MC SET CACHE DATA' );
					
				return $cacheResult;
			}
			
		}
	
		return FALSE;
	}
	
	
	/**
	 * 获取缓存数据
	 * 
	 * @param string $key
	 * @return mixed|NULL
	 */
	static public function GetCache( $key ) {
		if ( isset( $_REQUEST['refresh'] )  &&  1 == $_REQUEST['refresh']  ) {
			return NULL;
		}
		
		if ( $key ) {
			$cacheKey = self::GetCacheKey( $key );
			$mc       = self::GetInst();
			
			if ( !is_object( $mc ) ) {
				return NULL;
			}
			
			$cacheDatas = $mc->get( $cacheKey );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $cacheKey, 'MC KEY' );
			Common_Utility_Debug::getInstance()->log( $cacheDatas,	'MC GET CACHE DATA' );
			
			//如果第一次没有取到数据，停留$retryTime后重试一次
			if ( NULL == $cacheDatas  ||  !$cacheDatas ) {
				sleep( self::$_retryTime );
				
				$cacheDatas = $mc->get( $cacheKey );
			}
			
			if ( !is_null( $cacheDatas )  &&  !empty( $cacheDatas ) ) {
				$cacheDatas = Common_Tool::SmartUnserialize( $cacheDatas );
				
				return $cacheDatas;
			}
			
		}
		
		return NULL;
	}
	
	
	/**
	 * 删除缓存
	 *
	 * @param string $key
	 * @return boolean
	 */
	static public function DelCache( $key ) {
		if ( $key ) {
			$cacheKey = self::GetCacheKey( $key );
			$mc       = self::GetInst();
			
			if ( is_object( $mc ) ) {
				$cacheResult = $mc->delete( $cacheKey );
	
				return $cacheResult;
			}
		}
	
		return FALSE;
	}
}