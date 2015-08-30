<?php
/**
 * View class for api
 * 
 * convert data to JSON
 * 
 * @author tempslar
 *
 */
Class Prog_Api_View_Api {
	
	/**
	 * template file path
	 * 
	 * @var string
	 */
	private $_tplPath = '';
	
	
	/**
	 * __construct
	 */
	public function __construct() {}
	

	/**
	 * Output method
	 * 
	 * Convert data into JSON
	 * 
	 * @param array $datas
	 * @return string - json string
	 */
	public function show( $datas='' ) {
		
		if ( empty( $datas ) ) {
			$datas = $this->_params;
		}
		
		if ( empty( $datas ) ) {
			Common_Tool::messageExit( '0' );
		}
		
		//call json utlity method
		Common_Utility_Json::jsonOut( $datas );
	}

}