<?php
Class Prog_Api_Core_Api {
	/**
	 * static model object
	 * 
	 * @var object
	 */
	static protected $_model   = NULL;
	
	
	/**
	 * static storage object
	 *
	 * @var object
	 */
	static protected $_storage = NULL;
	
	
	/**
	 * static view object
	 *
	 * @var object
	 */
	static protected $_view    = NULL;


	/**
	 * Act List of acts not need instance
	 * 
	 * @var array
	 */
	static protected $_spActs = array( 'log', );
	
	
	/**
	 * Initialize static properties
	 * 
	 * return void
	 */
	static protected function initProperty() {
		self::$_model   = NULL;
		
		self::$_storage = NULL;
		
		self::$_view    = NULL;
	}
	
	
	/**
	 * Factory Method
	 * 
	 * Get instance of Model/Storage/View, and execute right method
	 * 
	 * @param object $model
	 */
	static public function Create( $model ) {
		//initialize properties
		self::initProperty();
		
		$act            = $model->act;
		$method         = $model->method;
		
		$methodName     = $act . $method;
		
		//get model object
		self::$_model   = $model;
		
		//get Storage instance  & View instance, except class in $_spActs
		if ( !in_array( $act, self::$_spActs ) ) {
			$classTail = SYS_ENTRANCE . '_' . $act;
			
			//get Storage class instance
			$storageClass   = 'Storage_' . $classTail;
			
			self::$_storage = new $storageClass;
			self::$_storage->setModel( $model );
			
			//get View class instance
			$viewClass = 'View_' . $classTail;
			
			if ( class_exists( $viewClass, true ) ) {
				self::$_view = new $viewClass;
			}
			else {
				self::$_view = new View_Api;
			}
			
		}
		
		//execute method, and return with self::$_model->_outputArr,or directly exit
		self::$methodName();
		
		return self::$_view->show( self::$_model->_outputArr );
	}
	
	
	/**
	 * Call static method automatically
	 *
	 * @param string $methodName
	 * @param string $message - output content
	 */
	static public function __callStatic( $methodName, $params ) {
		
		$className = Common_Tool::GetAppClassName( trim( $_REQUEST['act'] ) );
		
		if ( class_exists( $className, TRUE ) ) {	//auto execute api method
			self::$_model = $className::Run(  self::$_model, self::$_storage, self::$_view );
			
			return self::$_view->show( self::$_model->_outputArr );
		}
		
		//if method is not exist, then return Error Message
		$status    = '0';
		$message   = $methodName . ' method not exists';
		$cnMessage = '不存在该方法';
		
		Common_Tool::messageExit( $status, $message, '不存在该方法' );
	}
}