<?php
define( 'ROOT_PATH',  dirname( __FILE__ ) . '/' );	//Templar Framework file root path
define( 'STORE_PATH', '/mnt/data0/' );	//Default path to store log or other files
define( 'PROG_DIR', 'prog' );	//Default project root dir

define( 'APP_ROOT_NAME', 'app' );	//Application classes storage directory
define( 'DEFAULT_CLASS_METHOD', 'Run' );	//Default Application Class excute method name

define( 'WRITE_MODE', 'ab' );	//file write mode
define( 'SQL_LOG_RECORD', FALSE ); //whether auto record MySQL log

define( 'DEFAULT_SEPARATOR', '_' );	//Framework default separator
define( 'CLASS_SEPARATOR', '_' );	//Framework class name separator
define( 'REQUEST_SEPARATOR', '_' );	//REQUEST parameter separator
define( 'LOG_SEPARATOR', '`' );	//Default separator for log data
define( 'SQL_LOG_SEPARATOR', '#' );	//Default SQL log data separator
define( 'MC_KEY_SEPARATOR', '_' );	//Default cache key separator
define( 'PARAMDEFAULT_SEPARATOR', ',' );	//Default HTTP parameter separator

define( 'DEF_SQL_LIMIT', 50 );	//Default SQL limit value
define( 'MAX_SQL_LIMIT', 500 );	//Default maximum SQL limit value

define( 'NO_CACHE_PARAM', 'refresh' );	//Disable cache param name
define( 'DEBUG_KEY', 'db' );	//Debug mode switch param name
define( 'DEBUG_VALUE', '1' );	//Turn on debug mode param value

define( 'USE_FIREPHP', TRUE );	//Firebug On/Off

//Global script begin timestamp
$g_vars['starttime'] = microtime( TRUE );

//memcache config
$g_conf['mc']['default'][ 0 ] = array( 'host' => '127.0.0.1', 'port' => 11211 );

//If it is in your own computer, debug mode will be enable
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

/***** Common Function *****/

//regist function templar_autoload to __autoload stack
spl_autoload_register( 'templar_autoload' );

Common_Tool::DebugModeOff();	// Debug Mode On/Off, On:Common_Tool::DebugModeOn();

/**
 * __autoload() function
 * 
 * Override __autoload function, include class file automaticly
 * 
 * @param string $className - Class Name used in script, name like  'App_Example_Test'
 *                              will be converted into file name ./app/example/test.class.php
 *    
 * @return void
 */
function templar_autoload( $className ) {

	if ( $className ) {
		//convert class name into lower-letter for all file name is lower-letter
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
