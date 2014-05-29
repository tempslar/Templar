<?php
/**
 * 通用存储类
 * 
 * 封装基本存储操作
 * 
 * @author jingbin
 */
Class Common_Storage {
	
	/**
	 * 数据库名
	 * 
	 * @var string
	 */
	protected $_dbName = NULL;
	
	
	/**
	 * 数据表名
	 * 
	 * @var string
	 */
	protected $_table  = NULL;
	
	
	/**
	 * model对象
	 * 
	 * @var object
	 */
	protected $_model  = NULL;
	
	
	/**
	 * 计数值
	 * 
	 * @var int
	 */
	static private $_totalCount = NULL;
	
	
	/**
	 * 构造函数
	 * 
	 * @param object $model
	 * @return $this
	 */
	public function __construct( $model=NULL ) {
		
		//如果$model存在,则赋值给$this->_model
		if ( !is_null( $model )  &&  is_object( $model ) ) {
			$this->setModel( $model );
		}
		
		return $this;
	}
	
	
	/**
	 * 静态调用方法
	 * 
	 * @param object $model - model对象
	 * @return object - Common_Storage
	 */
	static public function GetInst( $model=NULL ) {
		return new self( $model );
	}
	
	
	/**
	 * 统一参数入口
	 * 
	 * @param object $model
	 */
	public function setModel( $model ) {
		if ( !is_null( $model )  &&  is_object( $model ) ) {
			$this->_model = $model;
		}
		
		return $this;
	}
	
	
	/**
	 * 获取数据表方法
	 * 
	 * 可扩展为分表逻辑方法
	 * 
	 * @param string $table
	 */
	protected function getTable( $table='' ) {
		return $this->_table;
	}
	
	
	/**
	 * 外部获取数据表名方法
	 */
	public function outGetTable() {
		$this->_table = $this->_table;
		
		return $this;
	}
	
	
	/**
	 * 查询数据方法
	 * 
	 * @param object $model
	 * @param array $filterLists - 需要特别过滤的参数
	 * @param boolean $withCount - 是否同时执行COUNT语句
	 * @param boolean $countWithFilterLists - COUNT语句是否同时使用参数过滤列表
	 * @return object - $model
	 */
	public function getData( $filterLists=array(), $withCount=TRUE, $countWithFilterLists=TRUE ) {
		$table = $this->getTable();
		
		if ( $this->_model->pg > 1 ) {
			$limit = $this->_model->getPageSkiper()->sp . ',' . $this->_model->pn;
		}
		else {
			$limit = mysql_escape_string( $this->_model->pn );
		}
		
		//处理特殊排序
		$col   = $this->_model->col;
		$order = '';
		
		if ( !empty( $col ) ) {
			$order = Common_DB::GetOrderStr( $this->_model->col, $this->_model->order );
		}
		/*
		elseif ( isset( $this->_defaultColumn )  &&  !empty( self::SQL_DEFAULT_ORDER ) ) {
			$order = Common_DB::GetOrderStr( $this->_defaultColumn, self::SQL_DEFAULT_ORDER );
		}
		*/
		
		$params = $this->_model->getSqlParamArr();
		
		//过滤相应参数
		if ( $params  &&  $filterLists ) {
			$params = Common_Utility_Storage::FilteParam( $params, $filterLists );
		}
		
		Common_Utility_Debug::getInstance()->showTimeLog( '4-1' );
		
		$data = Common_Db::Select( $table, $params, $limit, $order );
		
		$this->_model->_dataArr = $data;
		
		//获取数据总条数
		if ( TRUE == $withCount ) {
			
			if ( TRUE == $countWithFilterLists ) {
				$this->_model->_dataCounter = $this->getDataCount( $filterLists );
			}
			else {
				$this->_model->_dataCounter = $this->getDataCount( $filterLists );
			}
		}
		
		return $this->_model;		
	}
	
	
	/**
	 * 搜索数据方法
	 */
	public function searchData( $searchColumn='' ) {
		$table = $this->getTable();
		
		if ( $this->_model->pg > 1 ) {
			$limit = $this->_model->getPageSkiper()->sp . ',' . $this->_model->pn;
		}
		else {
			$limit = $this->_model->pn;
		}
		
		$keyword = $this->_model->keyword;
		
		Common_Utility_Debug::getInstance()->showTimeLog( '4-1' );

		if ( $searchColumn  &&  $keyword ) {
			$sql = 'SELECT * FROM `' . $table . '`'
						. ' WHERE `' . $searchColumn . '` LIKE "%' . $keyword . '%"'
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
	
	
	/**
	 * 统计数据方法
	 */
	public function getDataCount( $filterLists=array() ) {
		$table  = $this->getTable();
		
		$params = $this->_model->getSqlParamArr();
		
		//过滤相应参数
		if ( $params  &&  $filterLists ) {
			$params = Common_Utility_Storage::FilteParam( $params, $filterLists );
		}
		
		$count = Common_Db::Count( $table, $params );
		
		if ( NULL != $count ) {
			self::$_totalCount = $count;
		}
		else {
			self::$_totalCount = '';
		}
		
		return self::$_totalCount;
	}
	
	
	/**
	 * 获取最大数据方法
	 */
	public function getMaxData() {
		
	}
	
	
	/**
	 * 获取最小数据方法
	 */
	public function getMinData() {
		
	}
	
	
	/**
	 * 插入数据方法
	 * 
	 * @param array $filterLists - 手动需要过滤的参数
	 */
	public function addData( $filterLists=array() ) {
		$table = $this->getTable();
		
		$params = $this->_model->getSqlParamArr();
		
		//过滤相应参数
		if ( $params  &&  $filterLists ) {
			$params = Common_Utility_Storage::FilteParam( $params, $filterLists );
		}
		
		$this->_model->_dataArr = Common_Db::Insert( $table, $params );
		
		return $this->_model;
	}

	
	/**
	 * 更新数据方法
	 * 
	 * @param array $filterLists - 需要过滤的参数
	 * @param array $whereCols   - 作为查询条件的字段
	 * @return boolean
	 */
	public function updateData( $filterLists=array(), $whereCols=array() ) {
		$table = $this->getTable();
		
		$tempParams = $this->_model->getSqlParamArr();
		$params = array();
		$wheres = array();
		
		//分割
		if ( is_array( $whereCols )  &&  is_array( $tempParams ) ) {
			
			foreach ( $tempParams  AS  $key => $value ) {
				//过滤相应参数
				if ( in_array( $key, $filterLists ) ) {
					continue;
				}
				
				//保存至修改参数和条件参数
				if ( in_array( $key, $whereCols ) ) {
					$wheres[ $key ] = $value;
				}
				else {
					$params[ $key ] = $value;
				}
				
			}
			
		}
		
		if ( is_array( $params )  &&  count( $params ) > 0 ) {
			$this->_model->_dataArr = Common_Db::Update( $table, $params, $wheres );
		}
		
		return $this->_model;
	}
	
	
	/**
	 * 删除数据方法
	 */
	public function delData( $filterLists=array() ) {
		$table  = $this->getTable();
	
		$params = $this->_model->getSqlParamArr();
	
		//过滤相应参数
		if ( $params  &&  $filterLists ) {
			$params = Common_Utility_Storage::FilteParam( $params, $filterLists );
		}
		
		//获取删除条数
		$limit = mysql_escape_string( $this->_model->pn );
		
		//处理特殊排序
		$col   = $this->_model->col;
		$order = '';
		
		if ( !empty( $col ) ) {
			$order = Common_DB::GetOrderStr( $this->_model->col, $this->_model->order );
		}
	
		$this->_model->_dataArr = Common_Db::Delete( $table, $params, $order, $limit );
	
		return $this->_model;
	}
	
	
	/**
	 * 覆盖数据方法
	 */
	public function replaceData(  $filterLists=array()  ) {
		$table = $this->getTable();
	
		$params = $this->_model->getSqlParamArr();
		
		//过滤相应参数
		if ( $params  &&  $filterLists ) {
			$params = Common_Utility_Storage::FilteParam( $params, $filterLists );
		}
	
		$this->_model->_dataArr = Common_Db::Replace( $table, $params );
	
		return $this->_model;
	}
	
	
	/**
	 * 获取最近一条插入数据的id
	 * 
	 * @return mixed int|null
	 */
	public function mysqlInsertId() {
		$this->_model->_data = Common_DB::MysqlInsertId();
		
		return $this->_model;
	}
}
