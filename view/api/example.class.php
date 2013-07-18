<?php
Class View_Api_Userclass extends Common_View {

	private $_tplPath = '';


	public function __construct() {

	}


	public function show( $datas='' ) {
		if ( empty( $datas ) ) {
			$datas = $this->_params;
		}

		//调用json处理方法
		Common_Utility_Json::jsonOut($datas);
	}

}