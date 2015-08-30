<?php
/**
 * Userclass 通用相关方法
 * 
 * @author Jingbin Ma
 */
Class Prog_Api_Utility_Api_Example {
	
	/**
	 * 设置课程开始学习时间
	 * 
	 * @param int $uid
	 * @param int $cid
	 * @param int $oiid
	 * @param string $date
	 * @return boolean
	 */
	static public function SetStartDate( $uid, $cid, $date = '', $oiid = '' ) {
		$output    = FALSE;
		$emptyDate = '0000-00-00 00:00:00';
		
		
		if ( !$uid  ||  !$cid ) {
			return $output;
		}

		//获取用户课程信息
		$userClassInfos = Data_Api_Userclass::GetData($uid, $cid);
		
		if ( !$userClassInfos ) {
			return $output;
		}

		if ( $userClassInfos['o_i_id'] ) {	//可以获取o_i_id,则设置时间
			
			$output = Data_Api_UserclassDate::SmartSetDate( $userClassInfos['o_i_id'], 'startdate', $date );
			
		}
			
		return $output;
	}
	
}