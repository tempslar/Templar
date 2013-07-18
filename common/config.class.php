<?php
Class Common_Config {
	/**
	 * user token 加密值位置
	 *
	 * @var array
	 */
	static public $_tokenKeys = array( 1, 4, 8, 13, 14, 17, 22, 26, 29, 31 );
	
	static public $_defaultSqlOrder = 'DESC';
	
	static public $_useCache = TRUE;
	
	
	/**
	 * 识别是否需要翻页栏的标志
	 * 
	 * @var string
	 */
	const PAGE_BAR_MARK = 'list';


	/**
	 * 默认返回地址
	 * 
	 * @var string
	 */
	const DEFAULT_BACK_URL = './';
}