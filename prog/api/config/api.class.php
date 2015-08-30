<?php
Class Prog_Api_Config_Api {
	
	/**
	 * 
	 * @var unknown_type
	 */
	static public $_countTypes = array(
										'question' => '1'
										,'answer' => '2'
										,'bookmark' => '3'
										,'note' => '4'
										,'class' => '5'
										,'test' => '3'
										);
	
	/**
	 * 默认sql排序方式
	 * 
	 * @var unknown_type
	 */
	const SQL_DEAFULT_ORDER = 'DESC';
	
	
	/**
	 * 是否开启评星调整方法
	 * 
	 * @var boolean
	 */
	const OPEN_AVG_STAR = FALSE;


	/**
	 * everyone can access api names
	 */
	static public $_everyoneApis = array(
											'feedback_add'
											,'feedback_list'
											);

	/**
	 * only through interal domain access api names
	 */
	static public $_interApis = array(
										'user_token'
										,'user_checktoken'
										,'paylog_add'
										);
	
	
	/**
	 * 需要进行课程测验限制的机构列表
	 * 
	 * @var array
	 */
	static public $_testCheckAgencies = array( 3, );
	
	
	/**
	 * 
	 * @var unknown_type
	 */
	static public $_actions = array(
			'user_token'
			,'log_update'
			,'question_add'
			,'question_updown'
			,'answer_updown'
	/*
			,'class_list'
			,'class_catalog'
			,'class_log'
			,'class_check'
			,'class_info'
			,'class_search'
			,'article_info'
			,'article_topid'
			,'note_add'
			,'note_list'
			,'note_info'
			,'note_listother'
			,'bookmark_add'
			,'bookmark_del'
			,'bookmark_list'
			,'bookmark_check'
			,'bookmark_count'
			,'test_show'
			,'testlog_update'
			,'testlog_show'
			,'statistics_test'
			,'progress_count'
			,'progress_list'
			,'log_update'
			,'star_add'
			,'star_list'
			,'star_star'
			,'comment_list'
			,'comment_add'
			,'comment_new'
			,'question_count'
			,'question_list'
			,'question_new'
			,'answer_add'
			,'answer_list'
			,'answer_count'
			,'userclass_user'
			,'userclass_opt'
			,'userclass_check'
			,'userclass_list'
			,'userclass_complete'
			,'user_info'
			,'user_token'
			,'cart_add'
			,'cart_del'
			,'cart_list'
			,'order_add'
			,'order_update'
			,'order_list'
			,'pay_log'
			,'category_list'
		*/	
			);
}
