<?php
Class Storage_Api_Userclass extends Common_Storage {
	
	protected $_table = 'user_class';
	
	private $_tablePrefix = '';
	
	static protected $_totalCount = NULL;
	
	
	/**
	 * 构造方法
	 *
	 * @param object $model
	 */
	public function __construct( $model=NULL ) {
		parent::__construct( $model );
	}
	
	
	/**
	 * 获取实例静态方法
	 * 
	 * @param object $model
	 */
	static public function GetInst( $model=NULL ) {
		return new self( $model );
	}
	
	
	/**
	 * 获取用户id列表
	 */
	public function getUidList( $withCount=TRUE ) {
		$table = $this->getTable();
		
		$limit = 1;
		$order = '';
		
		if ( $this->_model->pg > 1 ) {
			$limit = $this->_model->getPageSkiper()->sp . ',' . $this->_model->pn;
		}
		else {
			$limit = $this->_model->pn;
		}
		
		$col = $this->_model->col;
		
		//处理特殊排序
		if ( !empty( $col ) ) {
			$order = Common_DB::GetOrderStr( $this->_model->col, $this->_model->order );
		}
		
		$params = $this->_model->getSqlParamArr();
		
		unset( $params['uid'] );
		
		if ( isset( $params['col'] ) ) {
			$order = $params['col'];
		}
		
		if ( isset( $params['order'] ) ) {
			$order .= ' ' . strtoupper( $params['order'] );
		}
		
		Common_Utility_Debug::getInstance()->showTimeLog( '4-1' );
		
		$data = Common_Db::Select( $table, $params, $limit, $order, array( 'uid' ) );
		
		$this->_model->_dataArr = $data;
		
		//获取数据总条数
		if ( TRUE == $withCount) {
			$this->_model->_dataCounter = $this->getDataCount();
		}
		
		return $this->_model;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Common_Storage::addData()
	 */
	public function addData( $filterLists=array() ) {
		$table = $this->getTable();
		
		$params = $this->_model->getSqlParamArr();
		
		//数据处理
		$params['createdate'] = Common_Tool::NowDate();
		
		if ( isset( $params['type'] ) ) {
			unset( $params['type'] );
		}
		
		$this->_model->_dataArr = Common_DB::Insert( $table, $params );
		
		return $this->_model;
		
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Common_Storage::delData()
	 */
	public function delData() {
		$table = $this->getTable();
		
		$params = array(
							'cid' => $this->_model->cid
							,'uid' => $this->_model->uid
						);
		
		$this->_model->_dataArr = Common_DB::Delete( $table, $params, '' );
		
		return $this->_model;
	}
	
	
	/**
	 * 获取用户所选课程的学时
	 * 
	 * 可获取总学时和已完成的学时
	 * 支持按年/月/日筛选
	 * 
	 * @param boolean $complete -  是否获取完成的课程
	 * @param string $completedate - 完成年份
	 */
	public function getClassHour( $complete=FALSE, $completedate='', $createdate='' ) {
		$classTable = 'kj_class';
		$table      = $this->getTable();
	
		$params    = $this->_model->getSqlParamArr();
		$sumColumn = 'SUM(`class_hour`)';
	
		$sql = 'SELECT ' . $sumColumn . ' FROM `' . $classTable . '`'
		. 'WHERE `id` in ( '
		. 'SELECT `cid` FROM `' . $table . '`'
		. ' WHERE `uid`="' . $params['uid'] . '" ';
		
		if ( TRUE == $complete ) {
			$sql .= ' AND `complete`="1"';
			
			if ( $completedate ) {
				$sql .= ' AND `complete_date` LIKE "' . mysql_escape_string( $completedate ) . '%"';
			}
			
		}
		
		if ( $createdate ) {
			$sql .= ' AND `createdate`="' . mysql_escape_string( $createdate ) . '"';
		}
		
		$sql .= ' );';
	
		Common_Utility_Debug::getInstance()->showTimeLog( '4-1' );
	
		$datas = Common_Db::Query( $sql );
	
		$this->_model->_dataCounter = $datas[ 0 ][ $sumColumn ];
	
		return $this->_model;
	}
	
	
	/**
	 * 更新用户课程数据
	 * 
	 * @params int $uid
	 * @params int $cid
	 * @params int $complete
	 * @params int $last_learn_artid
	 * @return mixed object
	 */
	public function updateClassStatus() {
		$table = $this->getTable();
		
		$wheres = array();
		$params = array();
		
		$tempParams  = $this->_model->getSqlParamArr();
		
		$wheres = array(
						'uid'  => $tempParams['uid']
						,'cid' => $tempParams['cid']
						);
		
		//@todo 默认值为0是否有BUG
		$params['complete']         = !is_null( $tempParams['complete'] )  ?  $tempParams['complete'] : NULL;
		$params['complete_date']    = !is_null( $tempParams['complete_date'] )  ?  $tempParams['complete_date'] : NULL;
		$params['last_learn_artid'] = !is_null( $tempParams['last_learn_artid'] )  ?  $tempParams['last_learn_artid'] : NULL;
		$params['test_num']         = !is_null( $tempParams['test_num'] )  ?  $tempParams['test_num'] : NULL;
		
		$this->_model->_dataArr = Common_DB::Update( $table, $params, $wheres );
		
		return $this->_model;
	}
	
}