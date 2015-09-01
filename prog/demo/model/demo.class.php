<?php
Class Prog_Demo_Model_Demo extends Common_Model {
	
	/**
	 * Get request param
	 *
	 * @param string $act - 
	 * @param string $params - 
	 */
	protected $_params = array(
								'act'       => NULL
								,'method'   => NULL
			
								,'pn'       => 1
								,'pg'       => 1
								,'id'       => NULL
								
								,'sql_res'  => NULL
								,'data_arr' => NULL
								);
	
	
	/**
	 * 获取sql参数时要过滤的参数
	 * 
	 * 在getSqlParamArr()方法中使用
	 * 
	 * @var array
	 */
	protected $_sqlFilterKeys = [ 'act', 'method', 'pg', 'pn', ];
	
	
	/**
	 * 全局必须参数
	 * 
	 * @var array
	 */
	protected $_globalParams = [ 'act', ];
	
	
	/**
	 * 静态获取实例方法
	 */
	public static function GetInst() {
		return new self();
	}
}