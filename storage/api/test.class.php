<?php
Class Storage_Api_Class extends Common_Storage {
	
	protected $_table = 'kj_class';
	
	private $_tablePrefix = '';
	
	static private $_totalCount = NULL;
	
	
	/**
	 * 允许用户获取的课程状态
	 *
	 * @var int
	 */
	CONST STATUS = 5;
	
	
	/**
	 * 构造方法
	 *
	 * @param object $model
	 */
	public function __construct( $model=NULL ) {
		parent::__construct( $model );
	}
	
	
	/**
	 *
	 * @param object $model
	 */
	static public function GetInst( $model=NULL ) {
		return new self( $model );
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Common_Storage::getData()
	 */
	public function getData( $filterLists=array(), $withCount=TRUE, $countWithFilterList=TRUE ) {
		
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
		
		//过滤无效参数
		$params = Common_Utility_Storage::FilteParam( $params, array( 'uid' ) );
		
		//在WHERE子句左边添加status条件
		$params = array_merge( array( 'status' =>  self::STATUS ), $params );
		
		Common_Utility_Debug::getInstance()->showTimeLog( '4-1' );
		
		$data = Common_Db::Select( $table, $params, $limit, $order );
		
		$this->_model->_dataArr = $data;
		
		if ( TRUE == $withCount ) {
			$this->_model->_dataCounter = $this->getDataCount( $filterLists );
		}
		
		return $this->_model;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Common_Storage::getDataCount()
	 */
	public function getDataCount( $filterLists=array() ) {
		$table = $this->getTable();
		
		$params = $this->_model->getSqlParamArr();
		
		//在WHERE子句左边添加status条件
		$params = array_merge( array( 'status' =>  self::STATUS ), $params );
		
		//过滤无效参数
		$params = Common_Utility_Storage::FilteParam( $params, array( 'uid' ) );
		
		$count = Common_Db::Count($table, $params);
		
		if ( NULL != $count ) {
			self::$_totalCount = $count;
		}
		else {
			self::$_totalCount = '';
		}
		
		return self::$_totalCount;
	}
	
	
	/**
	 * 更新课程评星信息
	 * 
	 * @param int $cid
	 * @param int $star
	 */
	public function updateStar() {
		
		$table = $this->getTable();
		
		$wheres['id']   = $this->_model->id;
		$params['star'] = $this->_model->star;
		
		$this->_model->_dataArr = Common_DB::Update( $table, $params, $wheres );
		
		return $this->_model;
	}
	
	
	/**
	 * 通过ID获取课程信息方法
	 * 
	 * @param int $id
	 * @return mixed object|null
	 */
	static public function GetClassInfoById( $id='' ) {
		
		if ( $id > 0 ) {
			$classModel = New Model_Api;
			$classModel->id = $id;
			$classModel = self::GetInst()->setModel( $classModel )->getData();
			
			if ( !is_null( $classModel ) ) {
				return $classModel;
			}
			
		}
		
		return NULL;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Common_Storage::searchData()
	 */
	public function searchData( $searchColumn='' ) {
		$table = $this->getTable();
	
		if ( $this->_model->pg > 1 ) {
			$limit = $this->_model->getPageSkiper()->sp . ',' . $this->_model->pn;
		}
		else {
			$limit = $this->_model->pn;
		}
	
		$keyword = mysql_real_escape_string( $this->_model->q );
	
		Common_Utility_Debug::getInstance()->showTimeLog( '4-1' );
	
		if ( $searchColumn  &&  $keyword ) {
			$sql = 'SELECT * FROM `' . $table . '`'
			. ' WHERE `' . $searchColumn . '` LIKE "%' . $keyword . '%"'
			. ' AND `status`="' . self::STATUS . '"'
			. ' LIMIT ' . $limit;
				
			$sqlDatas = Common_DB::Query( $sql );
				
			if ( !is_null( $sqlDatas ) ) {
				$this->_model->_dataArr = $sqlDatas;
	
				$sqlCount = str_replace( '*', 'COUNT(*)', $sql );
	
				$sqlCountData = Common_DB::Query( $sqlCount );
	
				$this->_model->_dataCounter = $sqlCountData;
			}
				
		}
	
		return $this->_model;
	}
}