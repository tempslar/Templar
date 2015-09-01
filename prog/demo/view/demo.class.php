<?php
/**
 * View class
 * 
 * @author tempslar
 *
 */
Class Prog_Demo_View_Demo extends Common_View {
	
	/**
	 * template file path
	 * 
	 * @var string
	 */
	private $_tplPath = '';
	
	
	/**
	 * __construct
	 */
	public function __construct( $model = NULL ) {
		parent::__construct( $model );
	}
	

	/**
	 * Output method
	 * 
	 * Load data into template
	 * 
	 * @param bool $needPagebar
	 * @return string - html
	 */
	public function show( $needPagebar = false ) {
		
		$params = $this->_params;
		
		//add constant
		$params['res_domain'] = RES_DOMAIN;

		//给模板参数赋值
		$this->assignToTpl( $params );

		$this->_tpl->display( $this->_tplName );

		Common_Tool::End();
	}

}