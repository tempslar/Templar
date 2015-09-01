<?php
/**
 * 
 * @author tempslar
 */
Class Prog_Demo_App_About_Index {
	public static $_model   = NULL;
	
	public static $_view    = NULL;
	
	public static $_storage = NULL;
	
	
	protected static $_cacheKeyParams = array( 'uid', 'cid', 'col', 'order', 'pg', 'pn', 'grouping_id' );
	
	
	/**
	 * 缓存时间
	 * 
	 * @var int
	 */
	CONST CACHE_TIME = 60;
	
	
	/**
	 * About Page Method
	 * 
	 * @param int $uid
	 * @return object - Model
	 */
	public static function Run( $model = '', $storage = '', $view = '' ) {
		return $model;
	}

}