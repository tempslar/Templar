<?php
include_once 'init.php';

if ( 'do' == $_REQUEST['debug'] ) {
	//Common_Tool::DebugModeOn();
}

Common_Utility_Debug::getInstance()->showTimeLog( 1 );

define( 'SYS_ENTRANCE', 'api' );
define( 'SYS_LEVEL', 2 );

Common_Utility_Debug::getInstance()->showTimeLog( 2 );

$model = Common_Tool::GetRequest();
Common_Utility_Debug::getInstance()->showTimeLog( 3 );

$api_name = $model->act . '_' . $model->method;

//check apiname privilage type
$api_priv_type = Utility_Api::GetApiPrivType( $api_name );

//判断访问权限
switch ( $api_priv_type ) {
	case 2:
		if ( 'internal.api.example.com' != $_SERVER['HTTP_HOST']
			&&  '127.0.0.1' != $_SERVER['REMOTE_ADDR'] ){

			Common_Tool::messageExit( '0', 'api error', '不能访问' );
		}
	break;

	case 3:
		if ( 'internal.api.example.com' != $_SERVER['HTTP_HOST'] ){
                //||  '127.0.0.1' != $_SERVER['REMOTE_ADDR'] ) {
		//非内网访问，需要验证用户uid和token
			$uid   = $model->uid;
			$token = $model->u_token;

			if ( !$uid  ||  !$token  ||  FALSE === Common_Tool::CheckToken( $model->uid, $model->u_token ) ) {

                		Common_Tool::messageExit( '0', 'token error', '未转有效token' );
        		
			}
		}
	break;
}

//检查所有必传参数
if ( is_object($model)  &&  !isset( $_REQUEST['debug'] ) ) {
	$model->checkGlobalParam();
}

//记录日志
if ( $model->act == 'mail'  ||  $model->act == 'order' ) {
	Common_Utility_Log::RecordLog( $model );
}

$act = $model->act;

//DEBUG
$uid = $model->uid;

if ( !$uid ) {
	$model->uid = 1000;
}
//DEBUG /END

if ( $act ) {
	$datas = Core_Api::Create( $model );
}
else {
	exit( 'act params error' );
}

Common_Tool::End();