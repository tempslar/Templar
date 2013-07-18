<?php
/**
 * DB封装类
 * 
 * 通过静态方法调用，实现了主从库分离，从全局变量中获取数据库配置信息
 * 
 * @author Ma Jingbin
 */
Class Common_DB {
	/**
	 * 数据库服务器地址
	 * 
	 * @var string
	 */
	static protected $_host   = NULL;
	
	
	/**
	 * 数据库服务器端口
	 * 
	 * @var int
	 */
	static protected $_port   = NULL;

	
	/**
	 * 数据库服务器用户名
	 * 
	 * @var string
	 */
	static protected $_user   = NULL;

	
	/**
	 * 服务器密码
	 * @var string
	 */
	static protected $_pwd    = '';

	
	/**
	 * DB 链接静态资源
	 * 
	 * @var array
	 */
	static protected $_db = array(
									'master' => NULL,
									'slave'  => NULL,
						 		);
	
	
	/**
	 * DB库名
	 * 
	 * @var string
	 */
	static protected $_dbName  = NULL;

	
	/**
	 * 默认编码
	 * 
	 * @var string
	 */
	static protected $_encode   = 'utf8';

	
	/**
	 * 日志记录
	 * 
	 * @var string
	 */
	static protected $_log = NULL;

	
	/**
	 * 单例构造方法 - 私有方法
	 */
	private function __construct() {}


	/**
	 * 单例析构方法 - 私有方法
	 */
	private function __destruct() {}

	
	/**
	 * 单例克隆方法  - 私有方法
	 */
	private function __clone() {}

	
	/**
	 * 单例入口
	 * 
	 * 可获取主库和从库SQL链接
	 * 
	 * @param string $dbType - 主从库属性，默认使用主库
	 * @param string $dbName - 数据库名
	 * @param string $encode - 编码，默认使用UTF8
	 * @return resource - 数据库链接实例
	 */
	static public function GetDB( $dbType='master', $dbName=DB_PRIMARY, $encode='utf8' ) {
		//获取DB配置
		if ( isset( $GLOBALS['g_conf']['db'][ PROJECT_NAME ][ $dbType ] ) ) {
			$dbConfigs = $GLOBALS['g_conf']['db'][ PROJECT_NAME ][ $dbType ];
		}
		else {
			$dbConfigs = $GLOBALS['g_conf']['db']['default'][ $dbType ];
		}

		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-5-1  CONNECT TO DB' );
		
		//链接数据库
		if ( NULL == self::$_db[ $dbType ] ) {
			self::$_db[ $dbType ] = self::connect( $dbConfigs['host'],
											$dbConfigs['port'],	$dbConfigs['user'],
											$dbConfigs['pwd'] );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->showTimeLog( '4-5-2-1 CONNECT DB' );
		}
		else {	//如果链接存在,则验证链接
			self::$_db[ $dbType ] = self::ping( self::$_db[ $dbType ] );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->showTimeLog( '4-5-2-2 USE STATIC HANDLE' );
		}

		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-5-2 CONNECT OK' );
		
		if ( $dbName ) {	//切换数据库
			$res = mysql_select_db( $dbName, self::$_db[ $dbType ] );
		}
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-5-3 SELECT DB ' . $dbName . ' ->' . (bool) $res );
		Common_Utility_Debug::getInstance()->info( self::$_db, 'DB TYPE' );
	
		return self::$_db[ $dbType ];
	}
	
	
	/**
	 * 设置数据库配置信息
	 * 
	 * @param array $configs
	 */
	static public function setDbConfig( $configs ) {
		self::$_host = $configs['host'];
		self::$_port = $configs['port'];
		self::$_user = $configs['user'];
		self::$_pwd  = $configs['pwd'];
		self::$_db   = $configs['db'];
	}
	

	/**
	 * 数据库链接方法
	 * 
	 * @param string $host
	 * @param int $port
	 * @param string $user
	 * @param string $pwd
	 * @return resource/NULL - 数据库链接
	 */
	static protected function connect( $host='', $port='', $user='', $pwd='' ) {
		if ( !$host  ||  !$port  ||  !$user ) {
			$host = self::$_host;
			$port = self::$_port;
			$user = self::$_user;
			$pwd  = self::$_pwd;
		}
		
		$db = mysql_connect( $host . ':' . $port, $user, $pwd )
				or self::SaveLog( "CONNECT MYSQL ERROR\n" );
			
		mysql_query( 'SET NAMES "' . self::$_encode . '";' ,  $db );

		return $db;
	}


	/**
	 * Ping方法
	 * 
	 * 验证数据库链接是否通畅,如果链接断开则重连一次
	 * 
	 * @param resource $db
	 * @return resource
	 */
	static protected function ping( $db ) {
		$pingRes = mysql_ping( $db );
		
		if ( !$pingRes ) {
			$db = self::connect();
		}

		return $db;
	}


	/**
	 * SELECT数据
	 * 
	 * 自动使用从库进行查询
	 * 
	 * @param string $table - 表名[internal]
	 * @param array $params - 等于条件的参数[external]
	 * @param mixed $limit - 可传int型(5),也可传string( 5,5 ) [external]
	 * @param string $order - 排序模式,ASC/DESC
	 * @param array $columns - 要查询的列，如与COUNT,MAX,MIN必须大写
	 * @return mixed - array/NULL
	 */
	static public function Select( $table, $params=array(), $limit=0, $order='', $columns=array( '*' ) ) {
		$sqlWhere = '';
		$sqlOrder = $order;
		$sqlLimit = mysql_escape_string( $limit );

		$sqlColumn = self::getColumn( $columns );
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-2' );
		
		$params    = self::mysqlString( $params );
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-3' );
		
		//获取sql where 部分
		$sqlWhere = self::getSqlWhere( $params );
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-4' );
		
		//生成SQL语句
		$sql = 'SELECT ' . $sqlColumn . ' FROM `' . $table . '`';

		if ( !empty( $sqlWhere ) ) {
			$sql .= ' WHERE ' . $sqlWhere;
		}

		if ( !empty( $sqlOrder ) ) {
			$sql .= ' ORDER BY ' . $sqlOrder;
		}

		if ( !empty( $sqlLimit ) ) {
			$sql .= ' LIMIT ' . $sqlLimit;
		}

		//优先使用从库
		$db = self::GetDB( 'slave' );
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->showTimeLog( '4-5' );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
		//记录SQL日志
		Common_Utility_Log::MysqlLog( $sql );
		
		$sqlRes = mysql_query( $sql, $db );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->log( $sqlRes, 'Sql Res' );
		
		if ( false == $sqlRes ) {
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->log( mysql_error(), 'Sql Error' );
		}
		
		$sqlDatas = self::outputSqlData( $sqlRes );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->log( $sqlDatas, 'Sql Data' );
		
		return $sqlDatas;
	}


	/**
	 * 获取数据COUNT值
	 * 
	 * @param string $table
	 * @param array $params
	 * @return mixed - int/NULL
	 */
	static public function Count( $table, $params ) {
		$count = self::Select( $table, $params, '', '', array( 'COUNT(*)' ) );
		
		return $count;
	}
	
	
	/**
	 * insert数据
	 * 
	 * @param string $table
	 * @param array $params
	 */
	static public function Insert( $table, $params ) {
		
		if ( $table ) {
			$sqlColumn = '';
			$sqlValue  = '';
			$params    = self::mysqlString( $params );
				
			$columns   = array_keys( $params );
			$values    = array_values( $params );
				
			$sqlColumn = '`' . implode( '`,`', $columns ) . '`';
			$sqlValue  = "'" . implode( "','" , $values ) . "'";
				
			$sql = "INSERT INTO `{$table}`( {$sqlColumn} ) VALUES( $sqlValue );";
				
			$db  = self::GetDB();

			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
			
			$sqlRes = mysql_query( $sql, $db );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $sqlRes, 'Sql Res' );
			
			if ( !$sqlRes ) {
				self::SaveLog( 'SQL->'.$sql."\nERROR->".mysql_error() );
				
				Common_Utility_Debug::getInstance()->info( mysql_error(), 'Sql Error' );
				
				return FALSE;
			}
				
			return $sqlRes;
		}

		return FALSE;
	}


	/**
	 * Update数据
	 *  
	 * @param string $table
	 * @param array $params
	 * @param array $wheres
	 * @param int $limit
	 */
	static public function Update( $table, $params, $wheres, $limit=1 ) {
		
		if ( $table  &&  $params ) {
			$sqlWhere = '';
			$sqlSet   = '';
				
			$params = self::mysqlString( $params );
			$wheres = self::mysqlString( $wheres );
				
			$sqlSet   = self::getUpdateSet( $params );
			$sqlWhere = self::getSqlWhere( $wheres );
				
			$sql = 'UPDATE `' . $table . '` SET ' . $sqlSet;
				
			if ( $sqlWhere ) {
				$sql .= ' WHERE ' . $sqlWhere;
			}
			else {	//不允许没有条件的更新
				return FALSE;
			}
				
			$sql .= ' LIMIT ' . mysql_escape_string( $limit );
				
			$db = self::GetDB();
				
			$sqlRes = mysql_query( $sql, $db );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
			Common_Utility_Debug::getInstance()->info( $sqlRes, 'Sql Res' );
			Common_Utility_Debug::getInstance()->info( mysql_error(), 'Sql Error' );
				
			return $sqlRes;
		}

		return FALSE;
	}


	/**
	 * REPLACE数据
	 *
	 * @param string $table
	 * @param array $params
	 */
	static public function Replace( $table, $params ) {
		
		if ( $table ) {
			$sqlColumn = '';
			$sqlValue  = '';
			$params    = self::mysqlString( $params );
	
			$columns   = array_keys( $params );
			$values    = array_values( $params );
	
			$sqlColumn = '`' . implode( '`,`', $columns ) . '`';
			$sqlValue  = "'" . implode( "','" , $values ) . "'";
	
			$sql = "REPLACE INTO `{$table}`( {$sqlColumn} ) VALUES( $sqlValue );";
	
			$db = self::GetDB();

			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
			
			$sqlRes = mysql_query( $sql, $db );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $sqlRes, 'Sql Res' );
			
			if ( !$sqlRes ) {
				self::SaveLog( 'SQL->'.$sql."\nERROR->".mysql_error() );
				
				//输出FIREPHP信息
				Common_Utility_Debug::getInstance()->info( mysql_error(), 'Sql Error' );
				
				return FALSE;
			}
	
			return $sqlRes;
		}
	
		return FALSE;
	}
	
	
	/**
	 * DELETE操作
	 * 
	 * 不能清空整个表,必须提供$params参数
	 * 
	 * @param string $table
	 * @param array $params
	 * @param string $order
	 * @param int $limit
	 * @return boolean
	 */
	static public function Delete( $table, $params, $order='', $limit=1 ) {
		$sqlWhere = '';
		$sqlOrder = $order;
		$sqlLimit = mysql_escape_string( $limit );
		
		//sql过滤
		$params    = self::mysqlString( $params );
		
		//获取sql where 部分
		$sqlWhere = self::getSqlWhere( $params );
		
		//生成SQL语句
		$sql = 'DELETE FROM `' . $table . '`';
		
		if ( !empty( $sqlWhere ) ) {
			$sql .= ' WHERE ' . $sqlWhere;
		}
		else {	//不允许没有条件的删除
			return FALSE;
		}
		
		if ( !empty( $sqlOrder ) ) {
			$sql .= ' ORDER BY ' . $sqlOrder;
		}
		
		if ( empty( $sqlLimit ) ) {
			$sqlLimit = 1;
		}
		
		$sql .= ' LIMIT ' . $sqlLimit;
		
		$db = self::GetDB();
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
		
		$sqlRes = mysql_query( $sql, $db );
		
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->log( strval( $sqlRes ), 'Sql Res' );
		
		return $sqlRes;
	}
	
	
	/**
	 * 直接执行SQL QUERY
	 * 
	 * 注意做mysql注入过滤
	 * 
	 * @param string $sql
	 * @return resource|NULL
	 */
	static public function Query( $sql ) {
		
		if ( $sql ) {
			$output = NULL;
			
			//分库模块
			if ( false !== strpos( $sql, 'SELECT' ) ) {
				$dbType = 'slave';
			}
			else {
				$dbType = 'master';
			}
			
			$db = self::GetDB( $dbType );
				
			$sqlRes = mysql_query( $sql );
			
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
			Common_Utility_Debug::getInstance()->log( strval( $sqlRes ), 'Sql Res' );
			
			//记录SQL日志
			Common_Utility_Log::MysqlLog( $sql );
				
			if ( false !== strpos( $sql, 'SELECT' ) ) {
				$output = self::outputSqlData( $sqlRes );
			}
			else {
				$output = $sqlRes;
			}
				
			//输出FIREPHP信息
			Common_Utility_Debug::getInstance()->log( $output, 'Sql Data' );
			
			return $output;
		}

		return NULL;
	}

	
	/**
	 * Get the ID generated in the last query
	 * 
	 */
	static public function MysqlInsertId() {
		$output = NULL;
		
		//获取数据库链接
		$db     = self::GetDB();
		
		$id     = mysql_insert_id();
		//输出FIREPHP信息
		Common_Utility_Debug::getInstance()->info( $id, 'SQL INSERT ID' );
		
		if ( $id ) {
			$output = $id;
		}
		
		return $output;
	}


	/**
	 * 生成SELECT中选取的字段串
	 * 
	 * @param array $columns
	 * @return string
	 */
	static protected function getColumn( $columns ) {
		
		if ( $columns  &&  is_array( $columns ) ) {
			$column = '';
				
			foreach ( $columns  AS  &$value ) {
				
				if ( '*' != $value
						&&  false === strpos( $value, '(')
						&&  false === strpos( $value, ')' ) ) {
				//特殊查询列不加`号		
				
					$value = '`' . $value . '`';
						
				}
				
			}
				
			$column = implode( ',', $columns );
		}
		else {
			$column = '*';
		}

		return $column;
	}


	/**
	 * 获取sql中where子句
	 * 
	 * 包含mysql注入过滤
	 * 
	 * @param array $params
	 * @return string
	 */
	static public function GetSqlWhere( $params ) {
		
		if ( $params  &&  is_array( $params ) ) {
			$sqlWhere = '';
				
			foreach ( $params  AS  $key => $value ) {
				if ( !empty( $sqlWhere ) ) {
					$sqlWhere .= ' AND ';
				}
				$sqlWhere .= "`$key`='$value'";
			}
				
			return $sqlWhere;
		}

		return NULL;
	}


	/**
	 * 获取Update操作中SET子句
	 * 
	 * @param array $dataArr
	 * @return string|NULL
	 */
	static public function getUpdateSet( $dataArr ) {
		
		if ( $dataArr ) {
			$setStr = '';
			
			foreach ( $dataArr  AS  $column => $value ) {
				
				if ( is_null( $value ) ) {	//如果值为null,则跳过
					continue;
				}
				
				if ( $setStr ) {	//补充SET 字段分隔符
					$setStr .= ',';
				}

				$setStr .= "`$column`=";

				if ( false !== strpos( $value, '+' ) ) {
				//处理字段运算,etc.`rank`+1
					$setStr .= $value;
				}
				else {
					$setStr .= "'$value'";
				}
				
			}
				
			return $setStr;
		}

		return NULL;
	}


	/**
	 * Mysql输入过滤
	 * 
	 * 循环执行mysql_escape_string()
	 * mysql_escape_string()效率慢
	 * 
	 * @param array $dataArr
	 * @return array
	 */
	static protected function mysqlString( $dataArr ) {
		
		if ( $dataArr  &&  is_array( $dataArr ) ) {
			
			foreach ( $dataArr  AS  $key => &$value ) {
				mysql_escape_string( $value );
			}
			
		}

		return $dataArr;
	}

	
	/**
	 * 格式化SELECT输出为数组
	 * 
	 * @param resource $sqlRes
	 * @return Ambigous <NULL, array>|NULL
	 */
	static protected function outputSqlData( $sqlRes=null ) {
		
		if ( $sqlRes ) {
			$sqlDatas = array();
				
			while ( $datas = mysql_fetch_array( $sqlRes, MYSQL_ASSOC) ) {
				$sqlDatas[] = $datas;
			}
		
			return self::outputDataArray( $sqlDatas );
		}
		else {
			return NULL;
		}
	}


	/**
	 * 格式化SQL查询结果为数组
	 * 
	 * @param array $dataArr
	 * @return array|null
	 */
	static public function outputDataArray( $dataArr ) {
		
		if ( $dataArr  &&  is_array( $dataArr ) ) {
			
			if ( !isset( $dataArr[ 0 ]['COUNT(*)'] )  ||  count( $dataArr[ 0 ] ) > 1 ) {
				return $dataArr;
			}
			else {
				return $dataArr[ 0 ]['COUNT(*)'];
			}
			
		}
		else {
			return NULL;
		}
	}

	
	/**
	 * 获取ORDER BY字段
	 * 
	 * @param mixed $col    - string/array 需要做排序的字段
	 * @param string $order - 排序规则 ASC/DESC,默认为DESC
	 * @return mixed - string/NULL
	 */
	static public function GetOrderStr( $col, $order='' ) {
		$col   = mysql_escape_string( $col );
		$order = mysql_escape_string( $order );
		
		$orderStr  = NULL;
		$orderArr  = array();
		$tmpOrders = NULL;
		
		if ( empty( $order ) ) {	//获取默认排序方式
			$order = Common_Config::$_defaultSqlOrder;
		}
		else {
			$order = strtoupper( $order );
			
			if ( false !== strpos( $order, PARAM_SPERATOR ) ) {
			//处理多排序方式
				$tmpOrders = explode( PARAM_SPERATOR, $order );
			}
			
		}
		
		$i = 0;
		//组合ORDER BY字段
		if ( !empty( $col ) ) {
			//分解$col
			$columnArr = explode( PARAM_SPERATOR, $col );
			$i = 0;
			
			//获取各项字段值
			foreach ( $columnArr AS $column ) {
				
				if ( !$column ) {
					continue;
				}
				
				if ( is_array( $tmpOrders )  &&  isset( $tmpOrders[ $i ] ) ) {
				//每个字段使用不同排序方式
					$orderArr[] = '`' . $column . '` ' . $tmpOrders[ $i ];
				}
				else {	//每个字段使用同一种排序方式
					$orderArr[] = '`' . $column . '` ' . $order;
				}
				
				$i++;
			}
			
			//组合多项字段名
			$orderStr = implode( ',', $orderArr );
			
			if ( strlen( $orderStr ) > 1 ) {	//字段过短则丢弃
				return $orderStr;
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * 启动事务处理
	 *
	 */
	static public function StartTransaction() {
		$db = self::GetDB();
	
		$sqlResult = mysql_query( 'START TRANSACTION' );
	
		return $sqlResult;
	}
	
	
	/**
	 * 提交事务处理
	 */
	static public function Commit() {
		$db = self::GetDB();
	
		$sqlResult = mysql_query( 'COMMIT' );
	
		return $sqlResult;
	}
	
	
	/**
	 * 事务回滚数据库
	 */
	static public function Rollback() {
		$db = self::GetDB();
	
		$sqlResult = mysql_query( 'ROLLBACK' );
	
		return $sqlResult;
	}
	
	
	/**
	 * 事务终止
	 *
	 * @return resource
	 */
	static public function End() {
		$db = self::GetDB();
	
		$sqlResult = mysql_query( 'END' );
	
		return $sqlResult;
	}
	
	
	/**
	 * 记录Sql Log
	 * 
	 * @param string $message
	 */
	static public function SaveLog( $message='' ) {
		self::$_log = '#' . date('Y-m-d H:i:s') . '#' . $message . "\n";
	}

	
	/**
	 * 获取log
	 * 
	 * @return string
	 */
	static public function GetLog() {
		return self::$_log;

	}
}
