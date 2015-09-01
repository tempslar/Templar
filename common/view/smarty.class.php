<?php
Class Common_View_Smarty {
	static public $_smarty  = NULL;
	
	protected $_tplRoot     = NULL;
	
	protected $_tplDir      = 'templates/';
	
	protected $_compileDir  = 'tmp/template_c/';
	
	protected $_configDir   = 'tmp/configs/';
	
	protected $_cacheDir    = 'tmp/cache/';
	
	protected $_lDelimiter  = '<{';
	
	protected $_rDelimiter  = '}>';
	
	protected $_model = NULL;
	
	
	public function __construct( $model = null ) {
		
		self::$_smarty = Plugins_Smarty_Smarty::GetInst();

		if ( is_object( self::$_smarty ) ) {	//设置SMARTY参数
			//setting templates dir
			$this->_tplRoot = defined('TPL_PATH') ? ROOT_PATH . TPL_PATH : ROOT_PATH;
			
			self::$_smarty->template_dir    = $this->_tplRoot . $this->_tplDir;
			self::$_smarty->compile_dir     = $this->_tplRoot . $this->_compileDir;
			self::$_smarty->config_dir      = $this->_tplRoot . $this->_configDir;
			self::$_smarty->cache_dir       = $this->_tplRoot . $this->_cacheDir;
			self::$_smarty->left_delimiter  = $this->_lDelimiter;
			self::$_smarty->right_delimiter = $this->_rDelimiter;
			
			if ( isset( $_REQUEST[ DEBUG_KEY ] )
					&&  DEBUG_KEY == $_REQUEST[ DEBUG_KEY ] ) {
				self::$_smarty->debugging = TRUE;
			}
		
			if ( !is_null( $model ) ) {
				$this->setModel( $model );
			}
		
			return self::$_smarty;
		}
		
		return NULL;
	}
	
	
	static public function GetInst( $model=NULL ) {
		
		if ( !is_null( $model ) ) {
			
			
			
		}
		
		
	}
	
	
	public function setModel( $model=NULL ) {
		
		if ( $model ) {
			$this->_model = $model;
		}
		
		return $this;
	}
	
	
	static public function GetSmarty( $model=NULL ) {
		$view = new self();
		$smarty = self::$_smarty;
		
		return $smarty;
	}
	
}
