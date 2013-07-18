<?php
Class View_Admin {
	
	private $_tplPath = '';
	
	
	public function __construct() {
		
	}
	
	
	public function show( $datas='' ) {
		if ( empty( $datas ) ) {
			$datas = $this->_params;
		}
		
		if ( empty( $datas ) ) {
			Common_Tool::messageExit( FALSE );
		}
		
		//调用json处理方法
		Common_Utility_Json::jsonOut( $datas );
	}
	
}