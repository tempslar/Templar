<?php
/**
 * 
 * @author tempslar
 *
 */
Class Prog_Demo_App_Index_Index {
	
	/**
	 * Model Object
	 *
	 * @var object
	 */
	public static $_model   = NULL;
	
	
	/**
	 * View Object
	 *
	 * @var object
	 */
	public static $_view    = NULL;
	
	
	/**
	 * Storage Object
	 *
	 * @var object
	 */
	public static $_storage = NULL;
	
	
	/**
	 * cache key param
	 *
	 * @var array
	 */
	protected static $_cacheKeyParams = [];
	
	
	/**
	 * cache live time
	 * 
	 * @var int
	 */
	CONST CACHE_TIME = 60;
	
	
	/**
	 * Index_index
	 * 
	 * @param int $model
	 * @param int $storage
	 * @param string $view
	 */
	public static function Run( $model = '', $storage = '', $view = '' ) {
		$model->_outputArr['msg'] = 'Hello, world!';
		
		return $model;
	}

}