<?php
include_once './../../init.php';
include_once './init.php';

//Control debug mode
if ( DEBUG_VALUE == $_REQUEST[ DEBUG_KEY ] ) {
	Common_Tool::DebugModeOn();
}

define( 'SYS_ENTRANCE', 'demo' );	//Should be same as your project name
define( 'SYS_LEVEL', 2 );	//Class inherit depth you like. Editing is not recommend
define( 'PHP_NAME', 'index.php' );	//http execute index.php
define( 'TPL_PATH', 'prog/demo/' );	//Template path

//extract http parameters, after SQL injection check and XSS check
$model = Common_Tool::GetRequest();

$page_name = $model->act . '_' . $model->method;

//check required param
if ( is_object($model)  &&  !isset( $_REQUEST[ DEBUG_KEY ] ) ) {
	$model->checkGlobalParam();
}

$act = $model->act;

if ( $act ) {
	$core = 'Prog' . CLASS_SEPARATOR . SYS_ENTRANCE . CLASS_SEPARATOR
				. 'Core' . CLASS_SEPARATOR
				. 'Demo';
	$datas = $core::Create( $model );
}
else {
	exit( 'act params error' );
}

Common_Tool::End();