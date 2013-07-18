<?php
define( 'PROJECT_NAME', 'Templar' );
define( 'STORE_PATH', '/mnt/data0/' );
define( 'ROOT_PATH',  dirname( __FILE__ ) . '/' );
define( 'LOG_PATH', STORE_PATH . 'logs/api/' );

define( 'USE_FIREPHP', TRUE );	//firebug On/Off

define( 'REQUEST_SEPARATER', '_' );	//REQUEST parameter seperater

define( 'APP_ROOT_NAME', 'app' );	//执行类存放主路径

define( 'DEFAULT_CLASS_METHOD', 'Run' );	//默认执行类的入口方法

if ( '127.0.0.1' == $_SERVER['SERVER_ADDR'] ) {
	define( 'DEBUG_MODE', TRUE );
}

//Check script is / not is on Test Server
if ( false !== strpos( $_SERVER['SERVER_ADDR'], '172.16.' )
		||  '127.0.0.1' == $_SERVER['SERVER_ADDR'] ) {
	define( 'TEST_SERVER', TRUE );
}
else {
	define( 'TEST_SERVER', FALSE );
}

define( 'WRITE_MODE', 'ab' );	//文件写入类型
define( 'SQL_LOG_RECORD', FALSE ); //是否记录MYSQL日志

define( 'DEFAULT_SPERATOR', '_' );	//框架默认分隔符
define( 'LOG_SPERATOR', '`' );	//日志数据分割符
define( 'SQL_LOG_SPERATOR', '#' ); //SQL日志数据分割符
define( 'MC_KEY_SPERATOR', '_' );	//MC key分割符
define( 'PARAM_SPERATOR', ',' );	//HTTP参数默认分隔符

define( 'DEF_SQL_LIMIT', 50 );	//默认最大查询条数
define( 'MAX_SQL_LIMIT', 500 );	//大查询某认最大查询条数

define( 'DB_PRIMARY', 'templar' );	//默认链接的数据库名

Common_Tool::DebugModeOff();	// Debug Mode On/Off, On:Common_Tool::DebugModeOn();

//Global script begin timestamp
$g_vars['starttime'] = microtime( TRUE );

//default master DB config
$g_conf['db']['default']['master'] = array(
												'host'  => 'localhost'
												,'port' => '3306'
												,'user' => 'templar_rw'
												,'pwd'  => ''
												,'db'   => ''
											);

//default slave DB config
$g_conf['db']['default']['slave'] = array(
												'host'  => 'localhost'
												,'port' => '3306'
												,'user' => 'templar_r'
												,'pwd'  => ''
												,'db'   => ''
										);

$g_conf['db']['api']   = $g_conf['db']['default'];

//memcache config
$g_conf['mc']['default'][ 0 ] = array( 'host' => '127.0.0.1', 'port' => 11211 );


/***** Common Function *****/
/**
 * __autoload() Function
 *
 * Override __autoload function, load Class file automaticlly
 *
 * @param string $className - Class name in script, which may need transform.
 *								As Weibo_Follow_Core, the real file name starts as Core,
 *								and is core.class.php
 * @return void
 */
function __autoload( $className ) {

	if ( $className ) {
		$className = strtolower( $className );
	
		if ( false !== strpos( $className, '_' ) ) {	//Get filename from $className
			$fileName = str_replace( '_', '/' , $className );
		}
		else {
			$fileName = $className;
		}
	
		$fileName = ROOT_PATH . $fileName . '.class.php';
		
		if ( file_exists( $fileName ) ) {
			include_once $fileName;
		}
	}

}