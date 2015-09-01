<?php
Class Common_Utility_Pagebar {
	
	/**
	 * 判断是否需要分页栏
	 * 
	 * 通过检查接口名是否有list字符来判断是否需要分页
	 * 
	 * @param string $apiName
	 */
	public static function NeedPagebar( $apiName ) {
		
		if ( $apiName  &&  false !== strpos( $apiName, Common_Config::PAGE_BAR_MARK ) ) {
			
			return TRUE;
			
		}
		else {
			return FALSE;
		}
		
	}
	
	
}