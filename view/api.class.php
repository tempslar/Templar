<?php
/**
 * API项目VIEW类
 * 
 * 将数据输出为JSON
 * 
 * @author Jingbin Ma <majingbin@ata.net.cn>
 *
 */
Class View_Api {
	/**
	 * 
	 * 
	 * @var unknown_type
	 */
	private $_tplPath = '';
	
	
	/**
	 * __construct
	 */
	public function __construct() {}
	

	/**
	 * 输出类
	 * 
	 * 将数据输出为JSON
	 * 
	 * @param array $datas
	 * @return string - json字符串
	 */
	public function show( $datas='' ) {
		
		if ( empty( $datas ) ) {
			$datas = $this->_params;
		}
		
		if ( empty( $datas ) ) {
			Common_Tool::messageExit( '0' );
		}
		
		//调用json处理方法
		Common_Utility_Json::jsonOut( $datas );
	}
	
}