<?php
/**
 * DB Class
 *
 * Singleton DB Class, support multiple database and multiple DB groups
 *
 * @author Tempslar
 */
Class Common_DB {
	/**
	 * db server host name
	 *
	 * @var string
	 */
	protected static $_host   = NULL;


	/**
	 * db server port number
	 *
	 * @var int
	 */
	protected static $_port   = NULL;


	/**
	 * db user
	 *
	 * @var string
	 */
	protected static $_user   = NULL;


	/**
	 * db password
	 *
	 * @var string
	 */
	protected static $_pwd    = '';


	/**
	 * DB name
	 *
	 * @var string
	 */
	protected static $_dbName  = NULL;


	/**
	 * default encode
	 *
	 * @var string
	 */
	protected static $_encode   = 'utf8';


	/**
	 * Default DB group name
	 *
	 * @var string
	 */
	protected static $_dbGroup = 'default';


	/**
	 * DB static resource
	 *
	 * @var array
	 */
	protected static $_db = array(
									'master' => NULL,
									'slave'  => NULL,
						 		);


	/**
	 * log content
	 *
	 * @var string
	 */
	protected static $_log = NULL;


	/**
	 * private __construct for Singleton
	 */
	private function __construct() {}


	/**
	 * private __destruct for Singleton
	 */
	private function __destruct() {}


	/**
	 * private __clone for Singleton
	 */
	private function __clone() {}


	/**
	 * Singleton get DB instance method
	 *
	 * get DB connection
	 *
	 * @param string $dbType - Connect to Master Server/Slave Server
	 * @return resource - mysql db connection resource
	 */
	public static function GetDB( $dbType='master' ) {
		//Get DB Configure Array from init.php
		if ( isset( $GLOBALS['g_conf']['db'][ self::$_dbGroup ][ $dbType ] ) ) {
			$dbConfigs = $GLOBALS['g_conf']['db'][ self::$_dbGroup ][ $dbType ];
		}
		else {
			$dbConfigs = $GLOBALS['g_conf']['db']['default'][ $dbType ];
		}

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->showTimeLog( 'GetDB() CONNECT TO DB' );

		//Connect to DB
		if ( NULL == self::$_db[ $dbType ] ) {
			self::$_db[ $dbType ] = self::connect( $dbConfigs['host'],
											$dbConfigs['port'],	$dbConfigs['user'],
											$dbConfigs['pwd'] );

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->showTimeLog( 'GetDB() CONNECT DB' );
		}
		else {	//ping static DB connection, and reconnect to DB, if conncect is closed
			self::$_db[ $dbType ] = self::ping( self::$_db[ $dbType ] );

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->showTimeLog( 'GetDB() USE STATIC HANDLE' );
		}

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->showTimeLog( 'GetDB() CONNECT OK' );

		$dbName =  !empty( self::$_dbName )  ?  self::$_dbName : $dbConfigs['db'];

		if ( $dbName ) {	//switch Database
			$res = mysql_select_db( $dbName, self::$_db[ $dbType ] );
		}

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->showTimeLog( 'GetDB() SELECT DB ' . $dbName . ' ->' . (bool) $res );
		Common_Utility_Debug::getInstance()->info( self::$_db, 'DB TYPE' );

		return self::$_db[ $dbType ];
	}


	/**
	 * Set a group of DB config to static properties
	 *
	 * @param array $configs
	 */
	public static function setDbConfig( $configs ) {
		self::$_host   = isset( $configs['host'] )   ?  $configs['host'] : NULL;
		self::$_port   = isset( $configs['port'] )   ?  $configs['port'] : NULL;
		self::$_user   = isset( $configs['user'] )   ?  $configs['user'] : NULL;
		self::$_pwd    = isset( $configs['pwd'] )    ?  $configs['pwd'] : '';
		self::$_dbName = isset( $configs['db']  )    ?  $configs['db'] : NULL;
		self::$_encode = isset( $configs['encode'] ) ?  $configs['encode'] : 'utf8';
	}


	/**
	 * DB Connect Method
	 *
	 * @param string $host
	 * @param int $port
	 * @param string $user
	 * @param string $pwd
	 * @return resource/NULL - db connection resource
	 */
	protected static function connect( $host='', $port='', $user='', $pwd='' ) {
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
	 * Ping Method
	 *
	 * 验证数据库链接是否通畅,如果链接断开则重连一次
	 *
	 * @param resource $db
	 * @return resource
	 */
	protected static function ping( $db ) {
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
	public static function Select( $table, $params=[], $limit=0, $order='', $columns=array( '*' ) ) {
		$sqlWhere = '';
		$sqlOrder = $order;
		$sqlLimit = mysql_escape_string( $limit );

		$sqlColumn = self::getColumn( $columns );
		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->showTimeLog( '4-2' );

		$params    = self::mysqlString( $params );
		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->showTimeLog( '4-3' );

		//获取sql where 部分
		$sqlWhere = self::getSqlWhere( $params );
		//FIREPHP OUTPUT
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

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
		//记录SQL日志
		Common_Utility_Log::MysqlLog( $sql );

		$sqlRes = mysql_query( $sql, $db );

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->log( $sqlRes, 'Sql Res' );

		if ( false == $sqlRes ) {
			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->log( mysql_error(), 'Sql Error' );
		}

		$sqlDatas = self::outputSqlData( $sqlRes );

		//FIREPHP OUTPUT
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
	public static function Count( $table, $params ) {
		$count = self::Select( $table, $params, '', '', array( 'COUNT(*)' ) );

		return $count;
	}


	/**
	 * insert数据
	 *
	 * @param string $table
	 * @param array $params
	 */
	public static function Insert( $table, $params ) {

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

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );

			$sqlRes = mysql_query( $sql, $db );

			//FIREPHP OUTPUT
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
	 * Update Method
	 *
	 * @param string $table
	 * @param array $params
	 * @param array $wheres
	 * @param int $limit
	 */
	public static function Update( $table, $params, $wheres, $limit=1 ) {

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

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );
			Common_Utility_Debug::getInstance()->info( $sqlRes, 'Sql Res' );
			Common_Utility_Debug::getInstance()->info( mysql_error(), 'Sql Error' );

			return $sqlRes;
		}

		return FALSE;
	}


	/**
	 * REPLACE Method
	 *
	 * @param string $table
	 * @param array $params
	 */
	public static function Replace( $table, $params ) {

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

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );

			$sqlRes = mysql_query( $sql, $db );

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->info( $sqlRes, 'Sql Res' );

			if ( !$sqlRes ) {
				self::SaveLog( 'SQL->'.$sql."\nERROR->".mysql_error() );

				//FIREPHP OUTPUT
				Common_Utility_Debug::getInstance()->info( mysql_error(), 'Sql Error' );

				return FALSE;
			}

			return $sqlRes;
		}

		return FALSE;
	}


	/**
	 * DELETE Method
	 *
	 * 不能清空整个表,必须提供$params参数
	 *
	 * @param string $table
	 * @param array $params
	 * @param string $order
	 * @param int $limit
	 * @return boolean
	 */
	public static function Delete( $table, $params, $order='', $limit=1 ) {
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

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->info( $sql, 'Sql' );

		$sqlRes = mysql_query( $sql, $db );

		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->log( strval( $sqlRes ), 'Sql Res' );

		return $sqlRes;
	}


	/**
	 * SQL QUERY sent to DB directly
	 *
	 * Attention MySQL Injection!
	 *
	 * @param string $sql
	 * @return resource|NULL
	 */
	public static function Query( $sql ) {

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

			//FIREPHP OUTPUT
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

			//FIREPHP OUTPUT
			Common_Utility_Debug::getInstance()->log( $output, 'Sql Data' );

			return $output;
		}

		return NULL;
	}


	/**
	 * Get the ID generated in the last query
	 *
	 */
	public static function MysqlInsertId() {
		$output = NULL;

		//get db instance
		$db     = self::GetDB();

		$id     = mysql_insert_id();
		//FIREPHP OUTPUT
		Common_Utility_Debug::getInstance()->info( $id, 'SQL INSERT ID' );

		if ( $id ) {
			$output = $id;
		}

		return $output;
	}


	/**
	 * Generate Where String for SELECT
	 *
	 * @param array $columns
	 * @return string
	 */
	protected static function getColumn( $columns ) {

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
	public static function GetSqlWhere( $params ) {

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
	public static function getUpdateSet( $dataArr ) {

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
	protected static function mysqlString( $dataArr ) {

		if ( $dataArr  &&  is_array( $dataArr ) ) {

			foreach ( $dataArr  AS  $key => &$value ) {

				if ( null !== $value ) {
					mysql_escape_string( $value );
				}
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
	protected static function outputSqlData( $sqlRes=null ) {

		if ( $sqlRes ) {
			$sqlDatas = [];

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
	public static function outputDataArray( $dataArr ) {

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
	public static function GetOrderStr( $col, $order='' ) {
		$col   = mysql_escape_string( $col );
		$order = mysql_escape_string( $order );

		$orderStr  = NULL;
		$orderArr  = [];
		$tmpOrders = NULL;

		if ( empty( $order ) ) {	//获取默认排序方式
			$order = Common_Config::$_defaultSqlOrder;
		}
		else {
			$order = strtoupper( $order );

			if ( false !== strpos( $order, PARAMDEFAULT_SEPARATOR ) ) {
			//处理多排序方式
				$tmpOrders = explode( PARAMDEFAULT_SEPARATOR, $order );
			}

		}

		$i = 0;
		//组合ORDER BY字段
		if ( !empty( $col ) ) {
			//分解$col
			$columnArr = explode( PARAMDEFAULT_SEPARATOR, $col );
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
	 * Start Transaction
	 *
	 */
	public static function StartTransaction() {
		$db = self::GetDB();

		$sqlResult = mysql_query( 'START TRANSACTION' );

		return $sqlResult;
	}


	/**
	 * Transaction Commit
	 */
	public static function Commit() {
		$db = self::GetDB();

		$sqlResult = mysql_query( 'COMMIT' );

		return $sqlResult;
	}


	/**
	 * Transaction Rollback
	 */
	public static function Rollback() {
		$db = self::GetDB();

		$sqlResult = mysql_query( 'ROLLBACK' );

		return $sqlResult;
	}


	/**
	 * Transaction End
	 *
	 * @return resource
	 */
	public static function End() {
		$db = self::GetDB();

		$sqlResult = mysql_query( 'END' );

		return $sqlResult;
	}


	/**
	 * SQL Log set to static property
	 *
	 * @param string $message
	 */
	public static function SaveLog( $message='' ) {
		self::$_log = '#' . date('Y-m-d H:i:s') . '#' . $message . "\n";
	}


	/**
	 * Get Log Method
	 *
	 * @return string
	 */
	public static function GetLog() {
		return self::$_log;

	}
}
