<?php
/**
 * 
 * @author tempslar
 *
 */
Class Prog_Demo_Core_Demo {
	/**
	 * static model object
	 * 
	 * @var object
	 */
	protected static $_model   = NULL;
	
	
	/**
	 * static storage object
	 *
	 * @var object
	 */
	protected static $_storage = NULL;
	
	
	/**
	 * static view object
	 *
	 * @var object
	 */
	protected static $_view    = NULL;


	/**
	 * Act List of acts not need instance
	 * 
	 * @var array
	 */
	protected static $_spActs = array( 'log', );
	
	
	/**
	 * Initialize static properties
	 * 
	 * return void
	 */
	protected static function initProperty() {
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
	public static function Create( $model ) {
		//initialize properties
		self::initProperty();
		
		$act            = $model->act;
		$method         = $model->method;
		
		$methodName     = $act . $method;
		
		//get model object
		self::$_model   = $model;
		
		//get Storage instance  & View instance, except class in $_spActs
		if ( !in_array( $act, self::$_spActs ) ) {
			$classTail = $act;
			
			//get Storage class instance
			$storageClass   = PROG_DIR . '_' . SYS_ENTRANCE . '_Storage_' . $classTail;
			
			if ( class_exists( $storageClass, true) ) {
				self::$_storage = new $storageClass;
				self::$_storage->setModel( $model );
			}
			
			//get View class instance
			$viewClass = PROG_DIR . '_' . SYS_ENTRANCE . '_View_' . $classTail;
			
			if ( class_exists( $viewClass, true ) ) {
				self::$_view = new $viewClass;
			}
			else {	//no specific View Class for App, then use project view
				$progView  = PROG_DIR . '_' . SYS_ENTRANCE . '_View_Demo';
				
				if ( class_exists( $progView, true ) ) {
					self::$_view = new $progView;
				}
				else {	//use common_view
					self::$_view = new Common_View;
				}
				
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
	public static function __callStatic( $methodName, $params ) {
		
		$className = Common_Tool::GetAppClassName( trim( $_REQUEST['act'] ) );
		
		if ( class_exists( $className, TRUE ) ) {	//auto execute api method
			self::$_model = $className::Run(  self::$_model, self::$_storage, self::$_view );
			
			return self::$_view->setParam( self::$_model )->show();
		}
		
		//if method is not exist, then return Error Message
		$status    = '0';
		$message   = $methodName . ' method not exists';
		$cnMessage = '不存在该方法';
		
		Common_Tool::messageExit( $status, $message, '不存在该方法' );
	}
}