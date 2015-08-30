<?php
Class Prog_Api_Utility_Api {

        /**
         * 返回api接口权限类型
         */
        static public function GetApiPrivType( $apiName='' ) {

                if ( $apiName ) {
                        //任何人都可访问
                        if ( isset( Prog_Api_Config_Api::$_everyoneApis )  &&  is_array( Prog_Api_Config_Api::$_everyoneApis )  &&  in_array( $apiName, Prog_Api_Config_Api::$_everyoneApis ) ) {

                                return 1;
                        }

                        //内网访问
                        if ( isset( Prog_Api_Config_Api::$_interApis )  &&  is_array( Prog_Api_Config_Api::$_interApis )  &&  in_array( $apiName, Prog_Api_Config_Api::$_interApis ) ) {

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
        static public function CheckTelNum( $telNum ) {
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

