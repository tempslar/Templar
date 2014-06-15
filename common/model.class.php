<?php
/**
 * 数据模型类
 * 验证/约束数据的类型,负责在类之间传递数据
 * 
 * @return object $model
 * @author Jingbin
 */
Class Common_Model {
	
	/**
	 * 获取参数
	 * 
	 * @var array
	 * string $act - 操作
	 * string $params - 参数
	 */
	protected $_params = array(
							'act'       => NULL
							,'method'   => NULL
							,'params'   => NULL
							,'sql_res'  => NULL
							,'data_arr' => NULL
							);
	

	/**
	 * 获取sql参数时要过滤的参数
	 * 
	 * 在getSqlParamArr()方法中使用
	 * 
	 * @var array
	 */
	protected $_sqlFilterKeys = array( 'act', 'method', 'col', 'order' );
	
	
	/**
	 * 全局必须参数
	 * 
	 * @var array
	 */
	protected $_globalParams = array( 'act'	);
	
	
	/**
	 * 特殊参数类型配置
	 * 
	 * @var array
	 */
	private $_paramTypes = array(
		 'pg' => 'int'
		,'pn' => 'int'
		,'sp' => 'int'
		,'tp' => 'int'
	);
	
	
	/**
	 * 参数极值限制
	 * 
	 * @var array
	 */
	private $_paramMaxs = array(
		'pg'  => 30
		,'pn' => 500
		,'sp' => 900
	);
	
	
	/**
	 * 存放简单数据
	 * 
	 * @var mixed - int|string|boolean
	 */
	public $_data = NULL;
	
	
	/**
	 * 存放获取到的数据
	 */
	public $_dataArr = NULL;
	
	
	/**
	 * 存放获取到的数据条数
	 */
	public $_dataCounter = 0;
	
	
	/**
	 * 存放当前页数据条数
	 * 
	 * @var int
	 */
	public $_currentDataNum = 0;
	
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	public $_outputArr = NULL;
	
	
	/**
	 * 错误日志
	 * 
	 * @var string
	 */
	public $_errorLog = '';
	
	
	/**
	 * 需要直接使用mysql_escape_string进行过滤的参数
	 * 
	 * @var array
	 */
	protected $_sqlEscapeParams = array( 'pn', 'pg', 'col', 'order', 'q', );
	
	
	/**
	 * 静态方法
	 */
	static public function GetInst() {
		return new self();
	}
	
	
	/**
	 * 给参数赋值
	 * 
	 * @param string $key
	 * @param unknown_type $value
	 * @return bool FALSE - 参数采用默认值
	 * @return $this->_params[] - 参数值 
	 */
	public function __set( $key, $value='' ) {
		if ( !empty( $key )  &&  array_key_exists( $key, $this->_params )  
				&&  !is_null($value)
				&& ( '0' === $value  ||  0 === $value  ||  !empty( $value ) ) ) {

			$value = trim( $value, ' ,' );
	    	$value = htmlspecialchars( $value );
	    	    
	        //$value = str_replace( array("\r", "\n", "\r\n"), '', $value );
			
	    	//进行mysql注入过滤
	    	if ( in_array( $key, $this->_sqlEscapeParams ) ) {
	    		$value = mysql_escape_string( $value );
	    	}

			//中断
			if ( '0' !== $value  &&  empty( $value ) ) {
				return FALSE;
			}
			
			$this->_params[ $key ] = $value;
			
 			//验证参数类型,如果正确则赋值
 			if ( false !== array_key_exists( $key, $this->_paramTypes ) ) {
				switch ( $this->_paramTypes[ $key ] ) {
					//字符型变量处理
					case 'str':	
						if ( false !== is_string( $value ) ) {
							$this->_params[ $key ] = $value;
						}
						else {
							$this->_params[ $key ] = '';
						}
					break;
					
					//整型变量处理
					case 'int':	
						$value = intval( $value );
						if ( false !== is_numeric( $value ) ) {
							if ( $value > 0 ) {
								$this->_params[ $key ] 
										= $this->checkMax( $key, $value );
							}
							else {
								$this->_params[ $key ] = 1;
							}
						}
						else {
							$this->_params[ $key ] = 1;
						}
					break;
					
					//缺省
					default:
						$this->_params[ $key ] = $value;
					break;
				 } 
 			}
 			else {
 				$this->_params[ $key ] = $value;
 			}
 			
		}
		else {
			return FALSE;
		}
	}
	
	
	/**
	 * 取参数值
	 * 
	 * @param string $key - $_params数组的键值
	 * @return bool FALSE - 不存在该键值
	 * @return $this->_params[ $key ] - 参数值
	 */
	public function __get( $key ) {
		
		if ( !empty($key)  &&  isset( $this->_params[ $key ] ) ) {
			return $this->_params[ $key ];
		}
		else {
			return FALSE;
		}
		
	}
	
	
	/**
	 * 返回参数数组
	 * 返回整个参数数组
	 */
	public function getParamArr() {
		return $this->_params;
	}
	
	
	/**
	 * 返回数据库查询数组
	 * 
	 * 默认删除act,method
	 * 
	 * @param array $this->_sqlFilterKeys - 要过滤的参数
	 * @return array - 用于数据库查询的参数数组
	 */
	public function getSqlParamArr() {
		$outArr = $this->_params;
		
		if ( is_array( $this->_sqlFilterKeys )  &&  is_array( $outArr ) ) {
			
			foreach ( $outArr  AS  $key => $value ) {
				
				if ( ('0' !== $value  &&  empty( $value ) )  ||  NULL === $value || FALSE !== in_array( $key, $this->_sqlFilterKeys ) ) {
					//清除数据
					unset( $outArr[ $key ] );
				}
				
			} 
		}
		
		return $outArr;
	}

	
	/**
	 * 格式化输出数据
	 */
	public function getOutputArr() {
		$this->_outputArr = $this->_dataArr;
		
		return $this->_outputArr;
	}
	
	/**
	 * 从sa获取所有参数
	 * 
	 * @param string $this->_params['sa']
	 * @return array
	 */
	public function getParams() {
		if ( !is_null( $this->_params['sa'] ) ) {
			$datas = explode('_', $this->_params['sa']);
			$this->_params['id'] = $datas[ 0 ];
			$this->_params['back_sa'] = $datas[ 1 ];
			
			//获取sa中的 tid, did, vid
			$this->getSortId( array('t','d','v'), $datas[ 1 ] );
			
			$this->_params['domain'] = $datas[ 2 ];
			$this->_params['back_url'] = 'http://' . $this->_params['domain']
											. '.sina.cn/?sa='
											. $this->_params['back_sa']
											. '&amp;vt=' . $this->_params['vt'];
		}
		else {
			$this->_errorLog .= "getParams() => sa为空\n";
			return FALSE;
		}
	}
	
	
	/**
	 * 检查全局参数是否都合法
	 * 
	 * @param array $_globalParams - 需验证的全局参数数组
	 * @param array $_params - 接收到的参数列表
	 * @return mixed - object|null
	 */
	public function checkGlobalParam() {
		if ( isset( $this->_globalParams )  &&  is_array( $this->_globalParams ) ) {
			
			//遍历必须传递参数数组,验证参数是否传递
			foreach ( $this->_globalParams  AS  $keyName ) {
				
				if ( false == array_key_exists( $keyName, $this->_params )
						||  is_null( $this->_params[ $keyName ] ) ) {
					
					$status = '0';
					$msg    = $keyName . ' parameter error!';
					$cnMsg  = $keyName . ' 参数错误!';
					
					//报错输出
					Common_Tool::messageExit( $status, $msg, $cnMsg );
				}
				
			}
			
		}
		
		return $this;
	}
	
	
	/**
	 * 
	 */
	public function getSortId( array $keys, $data='' ) {
		if ( !empty($data) ) {
			foreach( $keys as $key ) {
				$pattern = "/" . $key . "([\\d]*)/";
				if ( preg_match( $pattern, $data, $matches ) ) {
					$this->_params[ $key . 'id' ] = $matches[ 1 ];
				}
			}
		}
		else {
			$this->_errorLog = "getSortId() => 没有输入值\n";
			return FALSE;
		}
		
		
	}
	
	
	/**
	 * 检测最大值
	 * 
	 * @param string $k   - 参数的key
	 * @param int $value  - 整形变量值
	 * @return int $value - 如果配置数组$__paramMaxs里有该键值,则返回验证后的值;
	 * 						如果没有,则直接返回$value
	 */
	public function checkMax( $key, $value ) {
		
		if ( false !== array_key_exists( $key, $this->_paramMaxs ) ) {
			
			if ( $value > $this->_paramMaxs[ $key ] ) {
				$value = $this->_paramMaxs[ $key ];
			}
			
		}
		
		return $value;
	}
	
	
	/**
	 * 获取跳过数据条数
	 * 
	 * @param int $this->_params['pg'] - 页码
	 * @param int $this->_params['pn'] - 单页最高数据数
	 */
	public function getPageSkiper() {
		if ( $this->_params['pg'] > 1  &&  $this->_params['pn'] > 0 ) {
			//得到第n页跳过的数据值
			$this->_params['sp'] = ($this->_params['pg'] - 1) * $this->_params['pn'];
		}
		else {
			$this->_params['sp'] = 0;
		}
		
		return $this;
	}
	
	/**
	 * 返回错误信息 
	 */
	public function showError() {
		return $this->_errorLog;
	}
}