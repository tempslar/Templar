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

	
	public function __construct( $model=NULL, $openSmarty=TRUE ) {
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
			
			//$this->_params = $model->getOutPutArr();
			$this->_params = $model->_outputArr;
			
			$this->_tplName = $this->getTpl();
		}
		
		return $this;
	}
	
	
	protected function getTpl() {
		//var_dump( $this->_tpl );exit;
		
		//获取模板文件夹
		return SYS_ENTRANCE . '/' . $this->_model->act . '/' . $this->_model->method . $this->_tplSuffix;
	}
	
	
	public function show( $needPagebar=FALSE ) {
		$params = $this->_params;
		//var_dump( $params );exit;
		
		if ( is_array( $params ) ) {
			
			foreach ( $params  AS  $key => $subDatas ) {
				$this->_tpl->assign( $key, $subDatas );
			}
			
		}
		
		if ( $needPagebar  ||  TRUE === Common_Utility_Pagebar::NeedPagebar( $this->_model->act . '_' . $this->_model->method ) ) {
			$maxPage = ceil( $params['total'] / $this->_model->pn );
			
			$this->_tpl->assign( 'max_page', $maxPage );
			$this->_tpl->assign( 'pg', $this->_model->pg );
		}
		
		$this->_tpl->display( $this->_tplName );
		
		Common_Tool::End();
	}


	/**
	 * 通用中转提示页面
	 * 
	 * @param array $params - 必有参数 $cn_msg, $msg, $back_url
	 * @param string $tplName
	 * 
	 * @todo 通用通知页面
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
}