<?php
Class App_Api_Example_List {
	static public $_model   = NULL;
	
	static public $_view    = NULL;
	
	static public $_storage = NULL;
	
	
	static protected $_cacheKeyParams = array( 'uid', 'cid', 'col', 'order', 'pg', 'pn', 'grouping_id' );
	
	
	/**
	 * 缓存时间
	 * 
	 * @var int
	 */
	CONST CACHE_TIME = 60;
	
	
	/**
	 * example_list interface
	 * 
	 * @param int $uid
	 * @param int $cid - 课程id
	 * @param string $col - 排序字段
	 * @param string $order - 排序方式
	 * @param int $pg - 页码
	 * @param int $pn - 每页条数
	 * @param int $grouping_id - 课程分组id
	 */
	static public function Run( $model='', $storage='', $view='' ) {
		//Get from cache
		if ( $model->pn > 1 ) {
			$cacheKey          = Common_App::GetCacheKey( $model, self::$_cacheKeyParams );
			$model->_outputArr = Common_MC::GetCache( $cacheKey );
		}
		
		//DB Procedure, when no cache
		if ( !isset( $model->_outputArr['list'] ) ) {
			$model  = $storage->getData();
			$outArr = array();
			
			if ( isset( $model->_dataArr[ 0 ] ) ) {
				$classLists = array();
				$total      = $model->_dataCounter;
			
				foreach ( $model->_dataArr AS $key => $subDatas ) {
					$tmpClassInfos = self::createClassInfo( $subDatas );
		
					if ( $tmpClassInfos ) {
						$classLists[] = $tmpClassInfos;
					}
			
				}
			
				$outArr = array(
								'list'   => $classLists
								,'total' => $total
								);
			}
			
			$model->_outputArr = $outArr;
			
			if ( is_array( $model->_outputArr )  &&  !empty( $model->_outputArr ) ) {
			//SET TO CACHE
				Common_MC::SetCacheTime( self::CACHE_TIME );
				Common_MC::SetCache( $cacheKey, $model->_outputArr );
			}
		
		}
	
		if ( empty( $model->_outputArr ) ) {
			Common_Tool::messageExit( '0', 'no data', '无数据' );
		}
		
		return $view->show( $model->_outputArr );
	}
	
	
	/**
	 * 处理单一课程信息
	 * 
	 * 用kj_class表数据补全课程信息
	 * 
	 * @param array $datas
	 * @return mixed array|null
	 */
	static public function createTestInfo( $datas ) {
		$classInfos = NULL;
		
		if ( isset( $datas['cid'] ) ) {
			
			$articleInfos = Data_Api_Test::GetInfo( $datas['cid'] );
			
			if ( !is_null( $articleInfos ) ) {
			
				//获取个尺寸图片
				$tempImgs = Common_Utility_Img::GetClassImgArrary( $articleInfos['thumb'] );
			
				if ( !is_null( $tempImgs ) ) {
					$articleInfos = array_merge( $articleInfos, $tempImgs );
				}
				else {
					$articleInfos['thumb_big']   = '';
					$articleInfos['thumb_small'] = '';
				}
			
				//排除多余字段
				unset( $articleInfos['class_id'] );
				unset( $articleInfos['test_num'] );
				unset( $articleInfos['test_score'] );
				unset( $datas['cid'] );
			
				$classInfos = array_merge( $datas, $articleInfos);
			}
			
		}
		
		return $classInfos;
	}
	
}