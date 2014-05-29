<?php
Class Common_Utility_Debug {
	
	static protected $_firePhpObj = NULL;
	
	
	static protected $_fireStatus = NULL;
	
	
	static protected $_fireOptions = array(
											'maxObjectDepth'       => 5
											,'maxArrayDepth'       => 5
											,'maxDepth'            => 10
											,'useNativeJsonEncode' => TRUE
											,'includeLineNumbers'  => TRUE
											);
	
	
	public function __construct() {
		
		if ( TRUE == USE_FIREPHP  &&  isset( $_REQUEST['debug'] )  &&  'do' == $_REQUEST['debug'] ) {
			$firePhpPath = ROOT_PATH . 'include/firephp/FirePHP.class.php';
			
			if ( file_exists( $firePhpPath ) ) {
				include_once $firePhpPath;
				
				ob_start();
				
				self::$_firePhpObj = FirePhp::getInstance( TRUE );
				
				if ( self::$_firePhpObj  instanceof FirePhp ) {
					self::$_fireStatus = TRUE;
					
					//self::$_firePhpObj->setOptions( self::$_fireOptions );
				}
				//var_dump( __LINE__, self::$_firePhpObj );exit;
			}
			
		}
		
	}
	
	
	/**
	 * 获取firephp实例
	 * 
	 * @return object - FirePhp instance
	 */
	static public function getInstance() {
		return new self();
	}
	

	/**
	 * 处理firephp方法
	 * 
	 * @param string $methodName - Firephp 方法,目前支持log/warn/error/info
	 * @param array $params - 要输出的参数
	 * @return NULL
	 */
	public function __call( $methodName, $params=array() ) {
		if ( !is_null( self::$_firePhpObj ) ) {
			
			if ( isset( $GLOBALS['g_vars']['starttime'] ) ) {
				$runtime = microtime( TRUE ) - $GLOBALS['g_vars']['starttime'];
				
				$params[ 1 ] = 'Cost Time:' . $runtime . ' => ' . $params[ 1 ];
			}
			
			self::$_firePhpObj->$methodName( $params[ 0 ], $params[ 1 ] );
		}
		
		return $this;
	}
	
	
	/**
	 * 显示执行时间
	 */
	public function showTimeLog( $num='' ){
		if ( isset( $GLOBALS['g_vars']['starttime'] ) ) {
			$mark = !empty( $num )  ?  "Time-$num:" : 'Time:';
			
			$this->log( microtime( TRUE ), $mark );
		}
	}
	
}