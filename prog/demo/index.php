<?php
include_once './../../init.php';

if ( DEBUG_VALUE == $_REQUEST[ DEBUG_KEY ] ) {
	Common_Tool::DebugModeOn();
}

define( 'SYS_ENTRANCE', 'demo' );
define( 'SYS_LEVEL', 2 );
define( 'PHP_NAME', 'index.php' );
define( 'TPL_PATH', 'prog/demo/' );

$model = Common_Tool::GetRequest();

$page_name = $model->act . '_' . $model->method;

//check required param
if ( is_object($model)  &&  !isset( $_REQUEST[ DEBUG_KEY ] ) ) {
	$model->checkGlobalParam();
}

$act = $model->act;

if ( $act ) {
	$datas = Prog_Demo_Core_Demo::Create( $model );
}
else {
	exit( 'act params error' );
}

Common_Tool::End();