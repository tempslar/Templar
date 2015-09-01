<?php
Class Prog_Demo_Utility_Demo {

        /**
         * 返回api接口权限类型
         */
        public static function GetApiPrivType( $apiName='' ) {

                if ( $apiName ) {
                        //任何人都可访问
                        if ( isset( Prog_Demo_Config_Api::$_everyoneApis )  &&  is_array( Prog_Demo_Config_Api::$_everyoneApis )  &&  in_array( $apiName, Prog_Demo_Config_Api::$_everyoneApis ) ) {

                                return 1;
                        }

                        //内网访问
                        if ( isset( Prog_Demo_Config_Api::$_interApis )  &&  is_array( Prog_Demo_Config_Api::$_interApis )  &&  in_array( $apiName, Prog_Demo_Config_Api::$_interApis ) ) {

                                return 2;
                        }
                        else {  //token访问
                                return 3;
                        }

                }

                return 0;

        }


        /**
         * 检查电话号码格式
         * 
         * 电话号码格式为: 010-87654321
         * 
         * @param string $telNum
         * @return boolean
         */
        public static function CheckTelNum( $telNum ) {
        	$output = FALSE;
        	
        	if ( $telNum ) {
        		$pattern = '/^[\d]{3}-[\d]{8}$/';
        		
        		if ( preg_match( $pattern, $telNum ) ) {
        			$output = TRUE;
        		}
        		
        	}
        	
        	return $output;
        }
}

