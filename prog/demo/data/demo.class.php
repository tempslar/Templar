<?php
/**
 * 
 * @author mjb
 *
 */
Class Prog_Demo_Data_Index extends Common_Data {
	protected static $_model = NULL;
	
	public static $_results = NULL;
	
	
	/**
	 * 检查用户是否购买了某课
	 *  
	 * @param int $uid
	 * @param int $cid
	 * @return boolean
	 */
	public static function UserClassCheck( $uid, $cid ) {
		if ( $uid  &&  $cid ) {
			$model      = new Model_Api;
			$model->uid = $uid;
			$model->cid = $cid;
			
			$dataCounter = Storage_Api_Userclass::GetInst()->setModel( $model )->getDataCount();
			
			if ( is_null( $dataCounter )  ||  0 == $dataCounter ) {
				$dataCounter = FALSE;
			}
			else {
				$dataCounter = TRUE;
			}
			
			return $dataCounter;
		}
		
		return FALSE;
	}
	
	
	/**
	 * 检查用户是否完成某课，如果完成则更新课程完成状态
	 * 
	 * @param int $uid
	 * @param int $cid
	 * @param int $agency
	 * @return boolean
	 */
	public static function UserClassComplete( $uid, $cid, $agency=1 ) {
		if ( $uid  &&  $cid ) {
			$ucModel = new Model_Api();
			$ucModel->uid = $uid;
			$ucModel->cid = $cid;
			
			$ucModel = Storage_Api_Userclass::GetInst( $ucModel )->getData( [], FALSE );
			
			if ( !isset( $ucModel->_dataArr[ 0 ]['complete'] ) ) {
				//检查课程当前进度数据
				$classProgs = App_Api_Progress_Count::GetSingleProgress( $tmpModel->uid, $tmpModel->cid, $agency );
				
				//如果完成则写入完成状态
				if ( isset( $classProgs['complete'] )  &&  100 == $classProgs['complete'] ) {
					$userclassModel->complete      = 1;
					$userclassModel->complete_date = Common_Tool::NowDate();
					$ucModel = Storage_Api_Userclass::GetInst(  $userclassModel )->updateClassStatus();
				}
				
				return $ucModel->_dataArr;
				
			}
			
		}
		
		return FALSE;
	}
	
	
	/**
	 * 获取用户课程信息
	 * 
	 * @param int $uid
	 * @param int $cid
	 * @return mixed array|null
	 */
	public static function GetData( $uid, $cid ) {
		$outArr = NULL;
		
		if ( $uid  &&  $cid ) {
			$model = new Model_Api;
			$model->uid = $uid;
			$model->cid = $cid;
			
			$outArr = Storage_Api_Userclass::GetInst( $model )
												->getData( [] )
												->_dataArr;
			
			if ( !isset( $outArr[ 0 ]['cid'] ) ) {
				$outArr = NULL;
			}
		}
		
		return $outArr;
	}
	
	
	/**
	 * 给用户添加新课程
	 * 
	 * @param int $uid
	 * @param int $cid - 课程id
	 * @param string $oid - 订单id
	 * @return boolean - 添加成功/失败
	 */
	public static function AddData( $uid, $cid, $datas = '' ) {
		$output = FALSE;
		
		if ( $uid  &&  $cid ) {
			//生成model
			$model = new Model_Api;
			$model->uid    = $uid;
			$model->cid    = $cid;
			
			//检查用户是否已购买该课程
			$userclassExist = self::UserClassCheck( $uid, $cid );
			
			if ( !$userclassExist ) {
			
				//获取原课程信息
				$classModel = Storage_Api_Class::GetClassInfoById( $model->cid );
				
				if ( !is_null( $classModel->_dataArr ) ) {
					$model->grouping_id =  $classModel->_dataArr[ 0 ]['grouping_id'];
				}
				
				//order item id处理
				if ( isset( $datas['o_i_id'] )  &&  !empty( $datas['o_i_id'] ) ) {
					$model->o_i_id = $datas['o_i_id'];
				}
				
				//写入课程信息
				$output = Storage_Api_Userclass::GetInst( $model )->addData()->_dataArr;
			}
		}
		
		return $output;		
	}
	
	
	/**
	 * 批量添加用户课程
	 * 
	 * @param int $uid - 用户id
	 * @param string $cid - 课程id,多id用逗号','分割
	 * @param string $oid - 订单id 
	 * @return boolean - 添加全部成功/失败
	 */
	public static function MultiAdd( $uid, $cidStr, $datas = '' ) {
		$output = FALSE;
		$cids   = [];
		
		if ( !$uid  ||  !$cidStr ) {
			return $output;
		}
		
		//拆分多cid字符串
		if ( false !== strpos( $cidStr, PARAMDEFAULT_SEPARATOR ) ) {
			$cids = explode( PARAMDEFAULT_SEPARATOR, $cidStr );
		}
		else {
			$cids = array( $cidStr );
		}
		
		if ( is_array( $cids ) ) {
			
			//遍历cid列表进行插入
			foreach (  $cids  AS  $cid ) {
				
				//获取order item数据
				if ( isset( $datas['oid'] )  &&  $datas['oid'] ) {
					$orderItemParams = array(
												'oid' => $datas['oid']
												,'uid' =>  $uid
												,'cid' =>  $cid
												);
					
					$orderItemInfos = Data_Api_Cfaorderitem::GetOrderItemData( $orderItemParams, FALSE );
					
					if ( isset( $orderItemInfos[ 0 ] ) ) {
						$datas['o_i_id'] = $orderItemInfos[ 0 ]['id'];
					}
					
				}
				
				self::$_results[] = self::AddData( $uid, $cid, $datas );
			}
			
		}
		
		if ( !in_array( false, self::$_results ) ) {
			$output = TRUE;
		}
		
		return $output;
	}
	
	
	/**
	 * 记录课程起始时间
	 * 
	 * @param int $uid
	 * @param int $cid
	 */
	public static function StartClass( $uid, $cid ) {
		
		if ( !$uid  ||  !$cid ) {
			return FALSE;
		}
		
		$dataArr = self::GetData( $uid, $cid );
		
		if ( isset( $dataArr[ 0 ] ) ) {
			//$dataArr = $dataArr[ 0 ];
			
			if ( '0000-00-00 00:00:00' == $dataArr[ 0 ]['startdate'] ) {
			//提交第一次学习时间
			
				$model = new Model_Api;
				$model->uid = $uid;
				$model->cid = $cid;
				$model->startdate = Common_Tool::NowDate();
				
				$model = Storage_Api_Userclass::GetInst( $model )->updateData( [], array( 'uid', 'cid', ) );
				
				return $model->_dataArr;
			}
			
		}
		
		return FALSE;
	}
	
	
	/**
	 * 重置课程状态
	 * 
	 * @todo 增加o_i_id
	 * 
	 * @param int $uid
	 * @param int $cid
	 * @param string $oid - 订单id 
	 * @return boolean
	 */
	public static function UnlockClass( $uid, $cid, $datas = '' ) {
		$output = FALSE;
		
		if ( $uid  &&  $cid ) {
			$model             = new Model_Api;
			$model->uid        = $uid;
			$model->cid        = $cid;
			$model->complete   = '0';
			$model->test_num   = '0';
			//$model->createdate = Common_Tool::NowDate();
			$model->o_i_id     = $datas['o_i_id'];
			//var_dump( $model->o_i_id );die;
			
			$model = Storage_Api_Userclass::GetInst( $model )
						->updateData( [], array( 'uid', 'cid' ) );
			
			$output = $model->_dataArr;
		}
		
		return $output;
	}
	
	
	/**
	 * 锁定用户课程
	 * 
	 * @param int $uid
	 * @param int $cid
	 * @return boolean
	 */
	public static function LockClass( $uid, $cid ) {
		$output = FALSE;
		
		if ( $uid && $cid ) {
			$model = new Model_Api;
			$model->uid = $uid;
			$model->cid = $cid;
			$model->complete = 2;
			
			$output = Storage_Api_Userclass::GetInst( $model )
						->updateData( [], array( 'uid', 'cid' ) )->_dataArr;
		}
		
		return $output;
	}
}