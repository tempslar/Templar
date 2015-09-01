<?php
Class Common_Tool {
	
	public static $_model = NULL;
	
	
	/**
	 * 开启PHP调试信息
	 */
	public static function DebugModeOn( $level = E_ALL) {
		error_reporting( $level );
		ini_set( 'display_errors', 1 );
	}
	
	
	/**
	 * 关闭PHP调试信息
	 */
	public static function DebugModeOff() {
		error_reporting( E_ERROR );
		ini_set( 'display_errors', 0 );
	}

	
	/**
	 * 生成模型
	 * 
	 * @param string $name    - 对应act参数
	 * @param string $subName - 对应method参数
	 * @return resource - model 实例
	 */
	public static function GetModel( $name='', $subName='' ) {
		
		if ( defined('PROG_DIR') && defined( 'SYS_ENTRANCE' )  &&  defined( 'SYS_LEVEL' ) ) {
		//Model正常按层级处理流程
		
			//2015-02-25 modified by tempslar - change dir structure
			$modelName    = PROG_DIR . DEFAULT_SEPARATOR
					. SYS_ENTRANCE . DEFAULT_SEPARATOR
					. 'Model' . DEFAULT_SEPARATOR . SYS_ENTRANCE;

			$defaultModel = $modelName;
			
			if ( 2 == SYS_LEVEL ) {
				$modelName .= DEFAULT_SEPARATOR . $name;
			}
			
			if ( 3 == SYS_LEVEL ) {
				$modelName .= DEFAULT_SEPARATOR . $subName;
			}
			
			if ( class_exists( $modelName, true ) ) {	//生成制定model
				self::$_model = new $modelName;
			}
			else {	//生成默认model
				self::$_model = new $defaultModel;
			}
			
		}
		
		if ( !is_object( self::$_model ) ) {	//若无法实例化Model,则使用框架Model
			self::$_model = new Common_Model;
		}
		
		return self::$_model;
	}
	
	
	/**
	 * 获取请求参数生成model实例
	 * 
	 * Enter description here ...
	 * 
	 * @param array $_REQUEST - 全局参数
	 * @return resource       - model 实例
	 */
	public static function GetRequest() {
		
		$model = NULL;
		
		if ( isset( $_REQUEST )  &&  is_array( $_REQUEST ) ) {
			
			$method = '';
			
			//解析model名相关参数，拆分act为多部分
			if ( !isset( $_REQUEST['act'] ) ) {
				self::DefaultAct();
			}
				
			if ( isset( $_REQUEST['act'] ) ) {
				
				if ( strpos( $_REQUEST['act'], REQUEST_SEPARATOR ) ) {
					$apiNames = explode( REQUEST_SEPARATOR, $_REQUEST['act'] );
					$act      = $apiNames[ 0 ];
					$method   = $apiNames[ 1 ];
				}
				
			}
			//FIREPHP输出
			Common_Utility_Debug::getInstance()->log( $act . DEFAULT_SEPARATOR . $method, 'ACT_METHOD' );
			
			//获取MODEL
			$model = self::GetModel( $act, $method );
			
			Common_Utility_Debug::getInstance()->showTimeLog( '2-2' );
			
			foreach ( $_REQUEST  as  $key => $value ) {
				
				if ( 'act' != $key ) {	//赋值给model
					$model->$key = $value;
				}
				
			}
			
			//写入act和method参数
			$model->act    = $act;
			$model->method = $method;
		}
		//FIREPHP输出
		Common_Utility_Debug::getInstance()->log( self::$_model, 'INIT MODEL' );
		
		return $model;
	}
	
	
	/**
	 * 获取对外方法规则
	 * 
	 * @param string $act
	 */
	public static function GetAppClassName( $act ) {
		$className = '';
		$nameArr   = [];
	
		if ( defined( 'PROG_DIR' ) ) {
			$nameArr[] = PROG_DIR;
		}	

		if ( defined( 'SYS_ENTRANCE' ) ) {
			$nameArr[] = SYS_ENTRANCE;
		}

		if ( defined( 'APP_ROOT_NAME' ) ) {
			$nameArr[] = APP_ROOT_NAME;
		}
		
		if ( !empty( $act ) ) {
			$nameArr[] = $act;
		}
		
		if ( is_array( $nameArr )  &&  !empty( $nameArr ) ) {
			$className = implode( $nameArr, REQUEST_SEPARATOR );
		}
		
		return $className;
	}
	
	
	/**
	 * 获取用户token信息
	 * @param int $uid
	 * @return string/NULL - token值
	 */
	public static function GetToken( $uid ) {
		
		if ( $uid ) {
			$md5Uid = md5( $uid );
			
			$tokenKeys = Common_Config::$_tokenKeys;
			$userToken = '';
			
			if ( $tokenKeys ) {
				
				foreach ( $tokenKeys  AS  $key ) {
					$userToken .= $md5Uid[ $key ];
				}
				
				return $userToken;
			}
			
		}
		
		return NULL;
		
	}
	
	
	/**
	 * 
	 * @param unknown_type $uid
	 * @param unknown_type $skey
	 */
	public static function CheckToken( $uid, $uToken ) {
		if ( $uid  &&  $uToken ) {
			$rightToken = self::getToken($uid);
			
			if ( $rightToken  &&  $rightToken == $uToken ) {
				return TRUE;
			}
			
		}
		
		return FALSE;
		
	}
	
	
	/**
	 * 标准化输出方法
	 * 
	 * @param int $status - 状态码
	 * @param string $message - 英文信息
	 * @param string $cnMessage - 中文信息
	 */
	public static function messageExit( $status, $message='', $cnMessage='', $type='' ) {
		//$status值初始化
		if ( TRUE == $status ) {
			$status = '1';
		}
		else {
			$status = '0';
		}
		
		//生成默认english信息
		if ( empty($message ) ) {
			if ( 1 == $status ) {
				$message = 'ok';
			}
			else {
				$message = 'failed';
			}
		}
		
		//生成默认中文信息
		if ( empty( $cnMessage ) ) {
			if ( 1 == $status ) {
				$cnMessage = '成功';
			}
			else {
				$cnMessage = '失败';
			}
		}
		
		$datas = array(
						'status'      => $status
						,'message'    => $message
						,'cn_message' => $cnMessage
						,'type'       => $type
						);
		
		Common_Utility_Json::jsonOut($datas);
	}
	
	
	/**
	 * 获取当前日期
	 * 
	 * 返回不同格式
	 * 
	 * @param string $type
	 * @param int $time
	 */
	public static function NowDate( $type='all', $time='' ) {
		$format = 'Y-m-d H:i:s';
		
		if ( !$time ) {
			$time = time();
		}
		
		switch ( $type ) {
			case 'i':
				$format = 'Y-m-d H:i';
			break;
			
			case 'H':
				$format = 'Y-m-d H';
			break;
			
			case 'd':
				$format = 'Y-m-d';
			break;
			
			case 'm':
				$format = 'Y-m';
			break;
			
			case 'Y':
				$format = 'Y';
			break;
		}
		
		return date( $format, $time );
	}
	
	
	/**
	 * 全局输出方法
	 */
	public static function End() {
		//输出脚本执行时间
		if ( isset( $GLOBALS['g_vars']['starttime'] ) ) {
			$runtime = microtime( TRUE ) - $GLOBALS['g_vars']['starttime'];
			Common_Utility_Debug::getInstance()->info( $runtime, 'Run Time:' );
		}
		
		exit;
	}
	
	
	/**
	 * 获取智能序列化后数据
	 * 
	 * @param unknown_type $input
	 */
	public static function SmartSerialize( $input ) {
		$output = NULL;
		
		if ( is_array( $input )  /*||  is_object( $input )*/ ) {
			$output = serialize( $input );
		}
		else {
			$output = $input;
		}
		
		return $output;
	}
	
	
	/**
	 * 智能反序列化方法
	 * 
	 * @param string $input
	 */
	public static function SmartUnserialize( $input='' ) {
		$output = NULL;
		
		if ( !empty( $input ) ) {
			
			$pattern = '/^[a-z]:[0-9]+:/';
			
			if ( preg_match( $pattern, $input, $matches ) ) {
				$output = unserialize( $input );
			}
			else {
				$output = $input;
			}
		}
		
		return $output;
	}
	
	
	/**
	 * 智能urlencode方法
	 *
	 * @param string $input
	 */
	public static function SmartUrlEncode( $input='' ) {
		$output = NULL;
	
		if ( !empty( $input ) ) {
			$output = $input;
				
			if ( false === strpos( $input, '%' ) ) {
				$output = urlencode( $input );
			}
			
		}
	
		return $output;
	}
	
	
	/**
	 * 智能urldecode方法
	 *
	 * @param string $input
	 */
	public static function SmartUrlDecode( $input='' ) {
		$output = NULL;
	
		if ( !empty( $input ) ) {
			$output = $input;
	
			if ( false !== strpos( $input, '%' ) ) {
				$output = urldecode( $input );
			}
				
		}
	
		return $output;
	}
	
	
	/**
	 * 写入文件方法
	 *
	 * @param string $file
	 * @param string $content
	 * @param string $type
	 * @return boolean
	 */
	public static function WriteFile( $file='', $content='', $type=WRITE_MODE ) {
		
		if ( $file  &&  $content  &&  is_writable( $file ) ) {
			//写入日志文件
			$fp          = fopen( $file, $type );
			$writeResult = fwrite( $fp, $content . "\n" );
			fclose( $fp );
				
			return $writeResult;
		}
	
		return FALSE;
	}
}
