<?php
Class Common_View {
	
	protected $_tplDir  = NULL;
	
	
	protected $_tplName = NULL;
	
	
	protected $_params  = NULL;
	
	protected $_model   = NULL;
	
	
	protected $_tpl = NULL;
	
	protected $_tplSuffix = '.tpl';
	
	protected $_compileDir = 'tmp/template_c/';
	
	protected $_configDir = 'tmp/configs/';
	
	protected $_cacheDir = 'tmp/cache/';
	
	
	const DEFAULT_MSG_TPL = 'msg.tpl';
	
	
	static public $_userAgents = NULL;

	
	public function __construct( $model = NULL, $openSmarty = TRUE ) {
		//实例化Smarty类
		if ( TRUE === $openSmarty ) {
			$this->_tpl = Common_View_Smarty::GetSmarty($model);
		}
		
		//给模板参数赋值
		if ( $model ) {
			$this->setParam( $model );
		}
	}
	
	
	static public function GetInst( $model=NULL, $openSmarty=TRUE ) {
		return new self( $model, $openSmarty );
	}
	
	
	/**
	 * 设定$this->Params
	 * 
	 * @param object $model
	 * @return object - $this
	 */
	public function setParam( $model ) {
		
		if ( $model ) {
			
			$this->_model  = $model;
			
			$this->_params = array();
			
			$this->_params = $model->_outputArr;
			
			$this->_tplName = $this->getTpl();
		}
		
		return $this;
	}
	
	
	protected function getTpl() {
		//获取模板文件夹
		return $this->_model->act . '/' . $this->_model->method . $this->_tplSuffix;
	}
	
	
	/**
	 * 页面显示方法
	 * 
	 * @param unknown_type $needPagebar
	 */
	public function show( $needPagebar=FALSE ) {
		$params = $this->_params;
		
		//给模板参数赋值
		$this->assignToTpl( $params );
		
		//计算分页栏
		$this->getPagebar( $needPagebar );
		
		$this->_tpl->display( $this->_tplName );
		
		Common_Tool::End();
	}

	
	/**
	 * 给模板参数赋值
	 * 
	 * @param array $params
	 */
	public function assignToTpl( $params ) {
		
		if ( is_array( $params ) ) {
				
			foreach ( $params  AS  $key => $subDatas ) {
				$this->_tpl->assign( $key, $subDatas );
			}
				
		}
		
		return;
	}

	
	/**
	 * Pagebar calculation
	 * 
	 * @param boolean $needPagebar - 是否强制计算分页栏
	 */
	public function getPagebar( $needPagebar=FALSE ) {
		
		if ( $needPagebar
				||  TRUE === Common_Utility_Pagebar::NeedPagebar( $this->_model->act . REQUEST_SEPARATOR . $this->_model->method ) ) {
				
			//计算最大页数
			$maxPage = ceil( $this->_params['total'] / $this->_model->pn );
				
			$this->_tpl->assign( 'max_page', $maxPage );
			$this->_tpl->assign( 'pg', $this->_model->pg );
				
		}
		
		return;
	}
	
	
	/**
	 * Notification Method
	 * 
	 * @param array $params - 必有参数 $cn_msg, $msg, $back_url
	 * @param string $tplName
	 * 
	 * @return
	 */
	static public function MessageExit( $params=array(), $tplName=self::DEFAULT_MSG_TPL ) {
		
		$smarty = Common_View_Smarty::GetSmarty();
		
		if ( $params  &&  is_array( $params ) ) {

			foreach ( $params  AS  $key => $value ) {
				$smarty->assign( $key, $value );
			}

			if ( !isset( $params['back_url'] )  ||  !$params['back_url'] ) {
				$smarty->assign( 'back_url', Common_Config::DEFAULT_BACK_URL );
			}

		}

		if ( self::DEFAULT_MSG_TPL == $tplName ) {
			$tplName = SYS_ENTRANCE . '/' . self::DEFAULT_MSG_TPL;
		}
		
		if ( file_exists( $smarty->template_dir . $tplName ) ) {
			$smarty->display( $tplName );exit;
		}
		else {
			exit( '失败' );
		}
	}
	
	
	/**
	 * Get users' UA
	 * 
	 * @return Ambigous <array, unknown>
	 */
	static public function GetUserAgent() {

		if ( is_null( self::$_userAgents ) ) {	//重新获取UA
			$userAgent = isset( $_SERVER['HTTP_USER_AGENT'] )  ?  $_SERVER['HTTP_USER_AGENT'] : '';
	
			if ( false !== strpos( $userAgent, 'Mobile' ) ) {
				self::$_userAgents['device'] = 'mobile';
			}
			else {
				self::$_userAgents['device'] = 'pc';
			}
			
			self::$_userAgents['ua'] = $userAgent;
		}
		else {
			$userAgents = self::$_userAgents;
		}
		
		return self::$_userAgents;
	}
	
	
	/**
	 * Get users' browser information
	 * 
	 * @return mixed string|null
	 */
	static public function GetBrowser() {
		$browser    = NULL;
		$userAgents = self::GetUserAgent();
		
		if ( isset( $userAgents['ua'] )  &&  !empty( $userAgents['ua'] ) ) {
			$pattern = '/(' . Common_Config::$_browser . ')/i';
			
			if ( preg_match( $pattern, $userAgents['ua'], $matches ) ) {
				
				$matchStr = strtolower( $matches[ 0 ] );
				
				if ( 'crios' == $matchStr ) {
					$browser = 'chrome';
				}
				else {
					$browser = $matchStr;
				}
				
			}
			
		}
		
		return $browser;
	}
}
