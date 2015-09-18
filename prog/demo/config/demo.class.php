<?php
/**
 * 
 * @author tempslar
 *
 */
Class Prog_Demo_Config_Demo {
	
	/**
	 * default SQL order
	 * 
	 * @var unknown_type
	 */
	const SQL_DEAFULT_ORDER = 'DESC';


	/**
	 * act param value for index
	 *
	 * @var string
	 */
	const DEF_INDEX_ACT = 'index_index';
	
	
	/**
	 * everyone can access api names
	 */
	public static $_everyoneApis = array(
											//api name
											);

	/**
	 * only through interal domain access api names
	 */
	public static $_interApis = array(
										//api name
										);
	
	
	/**
	 * Valid act param value list
	 * 
	 * @var array
	 */
	public static $_actions = array(
										'index_index'
										);
}
