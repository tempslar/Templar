<?php
/**
 * ClassModel类
 * 
 * @author Jingbin　Ma
 */
Class Model_Api_Test_Userclass extends Common_Model {
	
	
	/**
	 * 获取参数
	 *
	 * @param string $act - 操作
	 * @param string $params - 参数
	 */
	protected $_params = array(
								'act'       => NULL
								,'method'   => NULL
								,'device_type' => NULL
								,'device_code' => NULL
								,'device_id' => NULL
								,'version' => NULL
								,'agency' => NULL
								,'language' => NULL
								,'u_token' => NULL
								,'uid' => NULL
			
								,'pn' => 1
								,'pg' => 1
								,'id'   => NULL
								,'cid' => NULL
								,'artid' => NULL
								,'col' => NULL
								,'order' => NULL
								,'fid' => NULL
								,'title' => NULL
								,'content' => NULL
								,'type' => NULL
								,'summery' => NULL
			
								//userclass
								,'startdate' => NULL
			
								//bookmark
								,'pos' => NULL
			
								//class_list
								,'grouping_id' => NULL
			
								//note
								,'author_id' => NULL
								,'source_id' => NULL
								,'is_public' => NULL
								,'top_artid' => NULL
			
								//testlog
								,'test_id' => NULL
								,'q_id' => NULL
								,'right' => NULL
								,'cost' => NULL
								,'answer' => NULL
								,'qtype' => NULL
			
								//test_statistics
								,'total' => NULL
								,'finishdate' => NULL
								,'pass' => NULL
								
								//star
								,'star' => NULL
								
								//progress
								,'log_list' => NULL
								,'status' => NULL
								,'createdate' => NULL
								,'art_title' => NULL
			
								//question&answer
								//,'q_id' => NULL
								
								//user_info
								,'target_uid' => NULL
								
								//search
								,'keyword' => NULL
								,'q' => NULL
								
								//order
								,'price_list' => NULL
								,'price' => NULL
								,'cut_list' => NULL
								
								//opt_log
								,'action' => NULL
								,'sp_id_name' => NULL
								,'sp_id' => NULL
								,'value' => NULL
								
								//userclass
								,'last_learn_artid' => NULL
								,'complete' => NULL
								,'complete_date' => NULL
								,'test_num' => NULL
								
								//feedback
								,'contact' => NULL
								
								//agency
								,'data'  => NULL
								,'ename' => NULL
								
								,'status' => NULL
								
								,'pre_id' => NULL
								,'next_id' => NULL
			
								//paylog
								,'ips_id' => NULL
								,'ips_oid' => NULL
								,'currency' => NULL
								,'o_date' => NULL
								,'oid' => NULL
								
								//mail
								,'email' => NULL
								,'name'  => NULL
								
								//invoice
								,'uname' => NULL
								,'agency_name' => NULL
								,'address' => NULL
								,'post_code' => NULL
								,'tel' => NULL
								,'deliver_type' => NULL
								,'updatedate' => NULL
								
								,'status' => NULL
								,'price' => NULL
			
								);
	
	
	/**
	 * 获取sql参数时要过滤的参数
	 * 
	 * 在getSqlParamArr()方法中使用
	 * 
	 * @var array
	 */
	protected $_sqlFilterKeys = array( 'act', 'method', 'device_type',
										'device_code', 'device_id', 'version',
										'language', 'u_token',
										'pg', 'pn', 'sp', 'col', 'order',
										'pre_id', 'next_id', );
	
	
	/**
	 * 全局必须参数
	 * 
	 * @var array
	 */
	protected $_globalParams = array( 'act', 'device_type',
										'device_code', 'device_id', 'version',
										'agency', 'language',
										);
	
	
	/**
	 * 静态获取实例方法
	 */
	static public function GetInst() {
		return new self();
	}
	
}