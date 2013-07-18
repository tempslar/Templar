<?php
Class Common_Data {
	const MODEL_NAME = '';
	
	//static public $_counter  = NULL;
	
	static protected $_model = NULL;
	
	
	/**
	 * 获取optlog model
	 * 
	 * @param unknown_type $model
	 */
	static public function GetModel( $model ) {
		if ( $model ) {
			self::$_model = $model;
				
			return self::$_model;
		}
	
		return NULL;
	}
	
	
	/**
	 * 创建新model实例
	 * 
	 * @param string $modelName - model类名称
	 * @return object
	 */
	static protected function newModel() {
		//获取调用该方法的子类名称
		$callClass = get_called_class();
		
		$modelName = $callClass::MODEL_NAME;
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->log( $modelName, 'COMMON_DATA_MODEL_NAME' );
		
		if ( $modelName  &&  class_exists( $modelName, TRUE ) ) {
			self::$_model = new $modelName;
		}
		
		return self::$_model;
	}
}