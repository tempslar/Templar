<?php
Class Common_Utility_Storage {
	
	/**
	 * 删除需过滤的参数
	 * 
	 * @param array $params
	 * @param array $filterLists
	 */
	public static function FilteParam( $params, $filterLists = [] ) {
		
		if ( $filterLists  &&  is_array( $filterLists ) ) {
			
			//遍历要过滤的key
			foreach ( $filterLists  AS  $paramKey ) {
				
				if ( array_key_exists( $paramKey, $params ) ) {	//如果key存在则unset
					unset( $params[ $paramKey ] );
				}
				
			}
			
		}
		
		return $params;
	}
	
}