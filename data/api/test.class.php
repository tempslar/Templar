<?php
Class Data_Api_Test {
	
	/**
	 * 获取单个课程信息
	 * 
	 * @param int $id - 课程id
	 * @return mixed array|null - 单门课程数据信息
	 */
	static public function GetClassInfo( $id ) {
		
		if ( !$id ) {
			return NULL;	
		}
		
		$cacheKey = 'class_info_' . $id;
		
		$datas = Common_MC::GetCache( $cacheKey );
		
		if ( is_null( $datas ) ) {
			
			$model     = new Model_Api;
			$model->id = $id;
			
			$model = Storage_Api_Class::GetInst( $model )->getData( array(), false );
			
			if ( isset( $model->_dataArr[ 0 ] ) ) {
				
				//SET CACHE
				if ( $cacheKey ) {
					Common_MC::SetCacheTime( 86400 );
					Common_MC::SetCache( $cacheKey, $model->_dataArr[ 0 ] );
				}
				
				return $model->_dataArr[ 0 ];
			}
			
		}
		else {
			return $datas;
		}
		
		return NULL;
	}
	
}