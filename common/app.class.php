<?php
Class Common_App {
	
	/*
	static private $_model   = NULL;
	
	
	static private $_storage = NULL;
	
	
	static private $_view    = NULL;
	*/
	
	
	/*
	static public function Run( $model='', $storage='', $view='' ) {
		
	}
	*/
	
	
	/**
	 * 获取缓存key
	 * 
	 * @param object $model
	 * @param array $cacheKeyNames - 需要出现在KEY中的字段
	 * @return mixed string|null
	 */
	static public function GetCacheKey( $model='', $cacheKeyNames='', $needAct = TRUE ) {
		$key         = NULL;
		$cacheParams = NULL;
		
		//通过配置数组获取MC KEY
		if ( $model  &&  is_object( $model ) ) {
			
			if ( $needAct ) {
				$cacheParams[] = (string) $model->act;
				$cacheParams[] = (string) $model->method;
			}
			
			if ( $cacheKeyNames  &&  is_array( $cacheKeyNames ) ) {	//指定缓存key字段
					
				foreach ( $cacheKeyNames  AS  $name ) {
					$tempValue = $model->$name;
						
					if ( is_null( $tempValue ) ) {
						$tempValue = '';
					}
						
					$cacheParams[] = (string) $tempValue;
				}
			}
			else {	//没指定缓存key字段
				$tempCacheParams = $model->getSqlParamArr();
				$cacheParams     = array_merge( $cacheParams, $tempCacheParams );
			}
			
			//获取MC KEY
			if ( is_array( $cacheParams ) ) {
				$key = implode( MC_KEY_SPERATOR, $cacheParams );
			}
			
		}
		
		return $key;
	}
}