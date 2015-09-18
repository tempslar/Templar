<?php
define( 'PROJECT_NAME', 'Templar' );

define( 'LOG_PATH', STORE_PATH . 'logs/' );
define( 'WEB_INDEX', 'www.templar.com' );	//Web site Top domain
define( 'RES_DOMAIN', 'http://res.templar.com' );	//Resource file domain

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

define( 'DEF_SQL_LIMIT', 50 );	//Default SQL limit value
define( 'MAX_SQL_LIMIT', 500 );	//Default maximum SQL limit value

define( 'DB_PRIMARY', 'templar' );	//Default project database name

//default master DB config
$g_conf['db']['default']['master'] = [
										'host'  => 'localhost'
										,'port' => '3306'
										,'user' => 'templar_rw'
										,'pwd'  => ''
										,'db'   => ''
										];

//default slave DB config
$g_conf['db']['default']['slave'] = [
										'host'  => 'localhost'
										,'port' => '3306'
										,'user' => 'templar_r'
										,'pwd'  => ''
										,'db'   => ''
										];

$g_conf['db']['templar'] = $g_conf['db']['default'];

//memcache config
$g_conf['mc']['default'][ 0 ] = array( 'host' => '127.0.0.1', 'port' => 11211 );