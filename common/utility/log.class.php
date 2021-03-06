<?php
Class Common_Utility_Log {
	
	CONST WRITE_MODE = 'ab';
	
	public static $_FileName = NULL;
	
	
	/**
	 * 日志记录方法
	 * 
	 * @todo
	 * 
	 * @param object $model
	 */
	public static function RecordLog( $model ) {
		//var_dump( LOG_PATH, $model );exit;
		
		$file = self::GetLogFileFullName();
		//var_dump( $file );exit;
		
		$logData = self::GetLogDataStr( $model );
		
		//var_dump( $logData );exit;
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $file, 'Log' );
		
		$fp        = fopen( $file, WRITE_MODE );
		$logResult = fwrite( $fp, $logData );
		fclose( $fp );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->log( $logData  , 'LOG DATA' );
		Common_Utility_Debug::getInstance()->log( $logResult, 'LOG RESULT' );
	}
	
	
	/**
	 * 记录SQL日志
	 * 
	 * @param string $log - 
	 */
	public static function MysqlLog( $log='' ) {
		//判断是否需要记录SQL日志
		if ( !defined( 'SQL_LOG_RECORD' )  ||  TRUE != SQL_LOG_RECORD ) {
			return FALSE;
		}
		
		self::$_FileName = 'mysql_' . date( 'Ymd' ) . '.log';
		$file            = LOG_PATH . self::$_FileName;
		
		$log    = str_replace( "\n", ' ', $log );
		$logStr = date('YmdHis') . SQL_LOG_SEPARATOR . $log;
		
		return Common_Tool::WriteFile( $file, $logStr );
	}
	
	
	/**
	 * 独立日志记录方法
	 * 
	 * @param string $prefix - 日志文件头
	 * @param array $datas - 日志数据
	 * @return void
	 */
	public static function SpLog( $prefix, $datas ) {
		$logData = '';
		
		//获取日志文件名
		$file = self::GetLogFileFullName( $prefix );
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $file, 'LOG FILE' );
		
		if ( $datas  &&  is_array( $datas ) ) {
			
			//拼接日志数据
			foreach ( $datas  AS  $key => $value ) {
				
				$logData .= LOG_SEPARATOR . $key . LOG_SEPARATOR . $value;
				
			}
			
		}
		
		//添加前缀
		if ( $logData ) {
			$logData = date('YmdHis') . LOG_SEPARATOR . $logData;
		}
		
		//写入日志文件
		$fp        = fopen( $file, WRITE_MODE );
		$logResult = fwrite( $fp, $logData );
		fclose( $fp );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->log( $logData  , 'LOG DATA' );
		Common_Utility_Debug::getInstance()->log( $logResult, 'LOG RESULT' );
	}
	
	
	/**
	 * 日志文件名生成规则
	 * 
	 * @param string $prefix - 日志文件前缀
	 * 
	 */
	public static function GetLogFileName( $prefix = '' ) {
		
		self::$_FileName = date( 'YmdH' ) . '.log';
		
		if ( $prefix ) {
			self::$_FileName = $prefix . DEFAULT_SEPARATOR . self::$_FileName;
		}
		
		return self::$_FileName;
	}
	
	
	/**
	 * 日志文件名完整生成规则
	 * 
	 * 包含日志存储路径
	 * 
	 * @param stirng $prefix - 日志文件前缀
	 */
	public static function GetLogFileFullName( $prefix = '' ) {
		self::GetLogFileName( $prefix );
		
		return LOG_PATH . self::$_FileName;
	}
	
	
	/**
	 * 日志数据生成方法
	 * 
	 * 生成日志数据文本串
	 * 
	 * @param object $model
	 * @return string
	 */
	public static function GetLogDataStr( $model ) {
		$logData = '';
		
		if ( is_object( $model ) ) {
			$tempLogDatas = $model->getParamArr();
			//$tempLogDatas['datetime'] = Common_Tool::NowDate();
			$datetime = Common_Tool::NowDate();
			
			if ( is_array( $tempLogDatas ) ) {
				$logParts = [];
				
				foreach ( $tempLogDatas  AS  $key => $value ) {
					$logParts[] = $key . LOG_SEPARATOR . $value;
				}
				
				$logData = implode( LOG_SEPARATOR, $logParts );
				$logData = 'datetime' . LOG_SEPARATOR . $datetime . LOG_SEPARATOR . $logData;
			}
			
			$logData .= "\n";
		}
		
		return $logData;
	}
}