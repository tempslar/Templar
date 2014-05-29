<?php
Class Core_Api {
	/**
	 * 静态model对象
	 * 
	 * @var object
	 */
	static protected $_model   = NULL;
	
	
	/**
	 * 静态storage对象
	 *
	 * @var object
	 */
	static protected $_storage = NULL;
	
	
	/**
	 * 静态view对象
	 *
	 * @var object
	 */
	static protected $_view    = NULL;
	
	
	/**
	 * 无需实例化的ACT操作列表
	 * 
	 * @var array
	 */
	static protected $_spActs = array( 'log', );
	
	
	/**
	 * 初始化静态属性
	 */
	static protected function initProperty() {
		self::$_model   = NULL;
		
		self::$_storage = NULL;
		
		self::$_view    = NULL;
	}
	
	
	/**
	 * 工厂方法
	 * 
	 * 做通用处理，并调用各接口逻辑方法
	 * 
	 * @param object $model
	 */
	static public function Create( $model ) {
		//初始化成员属性
		self::initProperty();
		
		$act            = $model->act;
		$method         = $model->method;
		
		$methodName     = $act . $method;
		
		//获取model
		self::$_model   = $model;
		
		//实例化Storage类  & View类, 配置的特殊类不实例化
		if ( !in_array( $act, self::$_spActs ) ) {
			$classTail = SYS_ENTRANCE . '_' . $act;
			
			//实例化Storage类 
			$storageClass   = 'Storage_' . $classTail;
			
			self::$_storage = new $storageClass;
			self::$_storage->setModel( $model );
			
			//实例化View类
			$viewClass = 'View_' . $classTail;
			
			if ( class_exists( $viewClass, true ) ) {
				self::$_view = new $viewClass;
			}
			else {
				self::$_view = new View_Api;
			}
			
		}
		
		//执行接口逻辑方法,执行结果将返回self::$_model->_outputArr,或直接退出
		self::$methodName();
		
		return self::$_view->show( self::$_model->_outputArr );
	}
	
	
	/**
	 * 业务逻辑静态调用方法
	 *
	 * @param string $methodName - 方法名
	 * @param string $message - 输出信息内容
	 */
	static public function __callStatic( $methodName, $params ) {
		
		$className = Common_Tool::GetAppClassName( trim( $_REQUEST['act'] ) );
		
		if ( class_exists( $className, TRUE ) ) {	//自动执行接口 方法
			self::$_model = $className::Run(  self::$_model, self::$_storage, self::$_view );
			
			return self::$_view->show( self::$_model->_outputArr );
		}
		
		//如果不存在相应接口方法，则返回通用错误信息
		$status    = '0';
		$message   = $methodName . ' method not exists';
		$cnMessage = '不存在该方法';
		
		Common_Tool::messageExit( $status, $message, '不存在该方法' );
	}
	
	
	/**
	 * class_list全部课件列表接口
	 * 
	 * 已独立为文件
	 * @filesource ./app/api/class/list.class.php
	 */
	/*
	static public function ClassList() {
		return TRUE;
	}
	*/
	
	
	/**
	 * class_info 获取课程详细信息
	 * 
	 * @filesource ./app/api/class/info.class.php
	 */
	/*
	static public function ClassInfo() {
		return TRUE;
	}
	*/
	
	
	/**
	 * class_catalog课件目录列表接口
	 * 
	 */
	//static public function ClassCatalog() {}
	
	
	/**
	 * userclass_check课件是否购买检查接口
	 * 
	 * @param int uid - 
	 * @param int cid - 
	 * 
	 */
	//static public function UserClassCheck() {}
	
	
	/**
	 * class_category课程分类列表
	 */
	static public function CategoryList() {

		
		//获取cache
		$cacheKey = self::$_model->act . '_' . self::$_model->method . '_' . self::$_model->agency . '_' . self::$_model->pn;
		self::$_model->_outputArr = Common_MC::GetCache( $cacheKey );
		
		if ( !isset( self::$_model->_outputArr['list'] ) ) {
		
			self::$_model = self::$_storage->getData( array('uid') );
			
			//NULL值转化为空字符串
			foreach ( self::$_model->_dataArr  AS  $key => $datas ) {
				
				foreach ( $datas AS $subKey => $value ) {
					
					if ( NULL == $value ) {
						self::$_model->_dataArr[ $key ][ $subKey ] = '';
					}
					
				}
				
			}
			
			self::$_model->_outputArr = array(
												'list'  => self::$_model->_dataArr,
												'total' => self::$_model->_dataCounter,
												);
			
			//写入cache
			if ( isset( self::$_model->_outputArr['list'] ) ) {
				Common_MC::SetCacheTime( 600 );
				Common_MC::SetCache( $cacheKey, self::$_model->_outputArr['list'] );
			}
		}
		
		return self::$_model;
	}

	
	/**
	 * class_recommand课件推荐列表接口
	 * 
	 * @todo 暂时不做
	 * 
	 * @filesource ./app/api/class/recommend.class.php
	 */
	//static public function ClassRecommend() {}
	
	
	/**
	 * article_info章节信息接口
	 * 
	 * @filesource ./app/api/article/info.class.php
	 */
	//static public function AritcleInfo() {}
	
	
	/**
	 * class_comment课件评论列表接口
	 * 
	 */
	static public function StarList() {
		self::$_model = self::$_storage->getData( array( 'uid', ) );
		
		self::$_model->_outputArr = array(
											'list'   => self::$_model->_dataArr
											,'total' => self::$_model->_dataCounter
											);
		
		return self::$_model;
	}


	/**
	 * star_add 课件评论列表接口
	 * 
	 * @todo 更新课程表
	 */
	//static public function StarAdd() {}
	
	
	/**
	 * class_star课件评分列表接口
	 * 
	 */
	//static public function StarStar() {}
	
	
	/**
	 * comment_list章节评论列表接口
	 * 
	 * 根据课程id+章节id，获取所有用户对于该章节的评论信息
	 * 支持翻页 
	 */
	static public function CommentList() {
		self::$_model = self::$_storage->getData( array( 'uid', ) );
		
		self::$_model->_outputArr = array(
											'list'   => self::$_model->_dataArr
											,'total' => self::$_model->_dataCounter
											);
			
		return self::$_model;
	}
	
	
	/**
	 * article_commentadd章节评论列表接口
	 * 
	 * 
	 */
	static public function CommentAdd() {
		
		//截字
		self::$_model->content = mb_substr( self::$_model->content, 0, 140, 'UTF-8' );

		self::$_model = self::$_storage->setModel( self::$_model )->addData();
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * class_progress课程进度接口
	 */
	//static public function ProgressList() {}
	
	
	/**
	 * progress_count学习进度百分比接口
	 * 
	 * @filesource ./app/api/progress/count.class.php
	 */
	//static public function ProgressCount() {}
	
	
	/**
	 * class_log学习进度上报接口
	 */
	//static public function LogUpdate() {}
	
	
	/**
	 * class_search课程搜索接口
	 * 
	 */
	//static public function ClassSearch() {}
	
	
	/**
	 * user_token密钥获取接口
	 * 
	 * 用户token获取接口，仅限于内网访问
	 * 
	 * @params int $uid - 用户id
	 * @return 
	 */
	static public function UserToken() {
		$uid = self::$_model->uid;
		
		if ( !$uid ) {	//检测uid参数
			Common_Tool::messageExit( '0', 'uid param error', '未传有效uid' );
		}
		
		$token = Common_Tool::getToken($uid);
		
		self::$_model->_outputArr = array(
									'uid'   => $uid
									,'token' => $token,
									);
				
		return self::$_model;
	}
	
	
	/**
	 * user_checktoken用户密钥验证接口
	 * 
	 * @params int $uid - 用户id
	 * @params string $u_token - 要验证的用户token
	 * @return boolean
	 */
	static public function UserChecktoken() {
		$checkResult = Common_Tool::CheckToken( self::$_model->uid, self::$_model->u_token );

		Common_Tool::messageExit( $checkResult );
	}
	
	
	/**
	 * user_info获取用户信息接口
	 * 
	 * @param string $target_uid - uid
	 */
	static public function UserInfo() {
		$uid = self::$_model->target_uid;
		
		if ( empty( $uid ) ) {
			$uid = self::$_model->uid;
		}
		
		$userInfos = Utility_Interface_User::GetUserInfo( $uid );
		
		if ( !is_null( $userInfos ) ) {
			self::$_model->_outputArr['list'] = $userInfos;
		}
		
		return self::$_model;
	}
	
	
	/**
	 * userclass_opt用户选课接口
	 * 
	 *
	 */
	static public function UserClassOpt() {
		$type = self::$_model->type;
		
		switch ( $type ) {
			case 1:	//添加课程
				
				self::$_model->_dataArr
					= Data_Api_Userclass::AddData( self::$_model->uid,
														self::$_model->cid );
					
			break;
			case 0:
				self::$_model = self::$_storage->delData();
			break;
		}
		
		$status = (string) self::$_model->_dataArr;
	
		Common_Tool::messageExit( $status );
	}
	
	
	/**
	 * userclass_list用户订阅课程接口
	 */
	//static public function UserclassList() {}
	
	
	/**
	 * user_inclass正在学某课程用户列表接口
	 */
	//static public function UserClassUser() {}
	
	
	/**
	 * test_show测验题显示接口
	 * 
	 */
	static public function TestShow() {
		self::$_model = self::$_storage->getData();
		
		if ( !is_null( self::$_model->_dataArr ) ) {
			$outputArr = array();
			
			$outputArr = self::$_model->_dataArr;
		
			/*
			foreach ( self::$_model->_dataArr  AS  $key => $subDatas ) {
				$outputArr[ $subDatas['id'] ] = $subDatas;
			}
			*/
			
		}
		
		self::$_model->_outputArr = array(
											'list' => $outputArr
											,'test_id' => Utility_Test::GetTestId( self::$_model->uid )
											,'total' => self::$_model->_dataCounter
											);
		
		return self::$_model;
	}
	
	
	/**
	 * testlog_update答题提交接口
	 * 
	 * 已独立为文件
	 * @filesource ./app/api/testlog/update.class.php
	 */
	/*
	static public function TestlogUpdate() {
	}
	*/
	
	
	/**
	 * testlog_show成绩统计接口
	 */
	//static public function TestLogShow() {}
	
	
	/**
	 * test_statistics_list成绩统计接口
	 * 
	 */
	static public function StatisticsTest() {
		self::$_model = self::$_storage->getData();
	
		if ( !is_null( self::$_model->_dataArr ) ) {
			self::$_model->_outputArr = array(
					'uid' => self::$_model->uid
					,'cid' => self::$_model->cid
					,'artid' => self::$_model->artid
					);
			
			$tmpDatas = array();
			
			foreach ( self::$_model->_dataArr  AS  $key => $testInfos ) {
	
				if ( isset( $testInfos['id'] ) ) {
					$tmpDatas[] = array(
							'test_id' => $testInfos['id']
							,'costtime' => $testInfos['costtime']
							,'right_count' => $testInfos['right_count']
							,'q_total' => $testInfos['q_total']
							,'finishdate' => $testInfos['finishdate']
							);
				}
				
			}
			
			self::$_model->_outputArr['list'] = $tmpDatas;
				
		}
	
		return self::$_model;
	}
	
	
	/**
	 * bookmark_add书签添加接口
	 * 
	 */
	static public function BookmarkAdd() {
		self::$_model = self::$_storage->addMark();
		
		Common_Tool::messageExit( (int) self::$_model->_dataArr );
	}
	
	
	/**
	 * bookmark_del书签添加接口
	 * 
	 */
	static public function BookmarkDel() {
		
		self::$_model = self::$_storage->delData();
		
		Common_Tool::messageExit( (int) self::$_model->_dataArr );
	}
	
	
	/**
	 * bookmark_list书签列表接口
	 * 
	 */
	static public function BookmarkList() {
		self::$_model = self::$_storage->getData();
		
		self::$_model->_outputArr = array(
											'list'   => self::$_model->_dataArr
											,'total' => self::$_model->_dataCounter
											);
		
		return self::$_model;
		
	}
	
	
	/**
	 * bookmark_check书签检查接口
	 * 
	 */
	static public function BookmarkCheck() {
		$counter = self::$_storage->getDataCount();
		
		if ( !is_null( $counter )  &&  $counter > 0 ) {
			self::$_model->_outputArr = TRUE;
		}
		else {
			self::$_model->_outputArr = FALSE;
		}
		
		Common_Tool::messageExit( self::$_model->_outputArr );
	}
	
	
	/**
	 * 书签计数接口
	 * 
	 * @todo 支持多id, 支持接收父id，返回所有子节点的BOOKMARK值
	 */
	//static public function bookmarkCount() {}
	
	
	/**
	 * note_list书签列表接口
	 * 
	 * @filesource ./app/api/note/list.class.php
	 */
	//static public function NoteList() {}
	
	
	/**
	 * Note_Listother他人公开的笔记列表接口
	 * 
	 * 
	 */
	static public function NoteListOther() {
		self::$_model->type = 10;
		
		//GET FROM CACHE
		$cacheTime                = 300;
		$cacheKey                 = Common_App::GetCacheKey( self::$_model, App_Api_Note_List::$_cacheKeyNames );
		self::$_model->_outputArr = Common_MC::GetCache( $cacheKey );
		
		//获取数据流程，NO CACHE
		if ( is_null( self::$_model->_outputArr ) ) {
			
			self::$_model = self::$_storage->getData();
		
			if ( !is_null( self::$_model->_dataArr ) ) {
				self::$_model->_outputArr = array(
													'list' => self::$_model->_dataArr
													,'total' => self::$_model->_dataCounter
													);
				
				//SET TO CACHE
				Common_MC::SetCacheTime( $cacheTime );
				Common_MC::SetCache( $cacheKey, self::$_model->_outputArr );
			}
		}
		
		return self::$_model;
	} 
	
	
	/**
	 * note_info书签列表接口
	 * 
	 * 
	 */
	static public function NoteInfo() {
		$uid = self::$_model->uid;
		
		self::$_model = self::$_storage->getData();
		
		if ( isset( self::$_model->_dataArr[ 0 ] ) ) {
			
			if ( 0 < self::$_model->_dataArr[ 0 ]['is_public']
					||  $uid == self::$_model->_dataArr[ 0 ]['uid'] ) {
				
				self::$_model->_outputArr = self::$_model->_dataArr[ 0 ];
				//设定前后数据id
				self::$_model->_outputArr['pre_id']  = self::$_model->pre_id;
				self::$_model->_outputArr['next_id'] = self::$_model->next_id;
				self::$_model->_outputArr['create_date'] = self::$_model->_outputArr['createdate'];
				unset( self::$_model->_outputArr['createdate'] );
				
				$userInfos = Utility_Interface_User::GetUserInfo( self::$_model->_outputArr['uid'] );
				
				if ( isset( $userInfos['id'] ) ) {
					self::$_model->_outputArr['user'] = $userInfos;
					
					unset( self::$_model->_outputArr['uid'] );
				}
				
				if ( NULL != self::$_model->_outputArr['author_id'] ) {
					$authorInfos = Utility_Interface_User::GetUserInfo( self::$_model->_outputArr['author_id'] );
					
					if ( isset( $authorInfos['id'] ) ) {
						self::$_model->_outputArr['author'] = $authorInfos;
							
						unset( self::$_model->_outputArr['author_id'] );
					}
				}
				else {
					unset( self::$_model->_outputArr['author_id'] );
				}
				
				if ( NULL == self::$_model->_outputArr['source_id'] ) {
					unset( self::$_model->_outputArr['source_id'] );
				}
				
				return self::$_model;
			}
			
		}
		
		Common_Tool::messageExit( '0' );
	}
	
	
	/**
	 * note_add笔记添加接口
	 * 
	 * 
	 */
	//static public function NoteAdd() {}
	
	
	/**
	 * note_del笔记删除接口
	 * 
	 * 
	 */
	static public function NoteDel() {
		self::$_model = self::$_storage->delData();
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * QuestionList书签列表接口
	 * 
	 */
	static public function QuestionList() {
		//设置默认排序方式
		$col = self::$_model->col;
		if ( empty( $col ) ) {
			self::$_model->col   = 'createdate';
			self::$_model->order = Config_Api::SQL_DEAFULT_ORDER;
		}
		
		self::$_model = self::$_storage->setModel( self::$_model )->getData( array( 'uid' ) );
		
		//添加用户信息
		if ( !is_null( self::$_model->_dataArr ) ) {
			
			foreach ( self::$_model->_dataArr  AS  $key => $subDatas ) {
				
				if ( isset( $subDatas['uid'] ) ) {
					
					$userInfos = Utility_Interface_User::GetUserInfo( $subDatas['uid'] );
					
					if ( $userInfos  &&  is_array( $userInfos ) ) {
						self::$_model->_dataArr[ $key ]['user'] = $userInfos;
						unset( self::$_model->_dataArr['uid'] );
					}
					
				}
				
				if ( is_null( $subDatas['answer_num'] ) ) {
					self::$_model->_dataArr[ $key ]['answer_num'] = '0';
				}
				
			}
			
		}
		
		//生成输入
		self::$_model->_outputArr = array(
											'list' => self::$_model->_dataArr
											,'total' => self::$_model->_dataCounter
											);
		
		return self::$_model;
	}
	
	
	/**
	 * QuestionAdd笔记添加接口
	 * 
	 * 添加章节问题
	 */
	static public function QuestionAdd() {
		self::$_model->content = mb_substr( self::$_model->content, 0, 300, 'utf-8' );
		self::$_model          = self::$_storage->setModel( self::$_model )->addData();
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * QuestionUpDown笔记删除接口
	 * 
	 */
	static public function QuestionUpDown() {
		//判断1天内是否记录过日志
		$userOptlogInfos = Data_Api_OptLog::GetData( self::$_model, 1 );
		
		//判断是否顶踩太频繁
		if ( isset( $userOptlogInfos['createdate'] )
				&&  strtotime( $userOptlogInfos['createdate'] ) + 86400 > time() ) {
			
			Common_Tool::messageExit( '0', 'updown question too much', '顶踩问题太频繁' );
			
		}
		
		self::$_model = self::$_storage->updownData();
		
		//记录日志
		Data_Api_OptLog::ReplaceData( self::$_model );
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * QuestionCount书签列表接口
	 * 
	 */
	//static public function QuestionCount() {}
	
	
	/**
	 * AnswerList书签列表接口
	 * 
	 */
	static public function AnswerList() {
		self::$_model->col   = 'createdate';
		self::$_model->order = Config_Api::SQL_DEAFULT_ORDER;
		
		self::$_model = self::$_storage->setModel( self::$_model )->getData( array('uid') );
		
		//添加用户信息
		if ( !is_null( self::$_model->_dataArr ) ) {
			
			foreach ( self::$_model->_dataArr  AS  $key => $subDatas ) {
				
				if ( isset( $subDatas['uid'] ) ) {
					
					$userInfos = Utility_Interface_User::GetUserInfo( $subDatas['uid'] );
					
					if ( $userInfos  &&  is_array( $userInfos ) ) {
						self::$_model->_dataArr[ $key ]['user'] = $userInfos;
						unset( self::$_model->_dataArr['uid'] );
					}
					
				}
				
			}
			
		}
		
		//生成输入
		self::$_model->_outputArr = array(
											'list' => self::$_model->_dataArr
											,'total' => self::$_model->_dataCounter
											);
		
		return self::$_model;
	}
	
	
	/**
	 * AnswerAdd笔记添加接口
	 * 
	 * @param int $qid - 
	 * @param string $title - 
	 * @param string $content - 
	 */
	static public function AnswerAdd() {
		self::$_model->content = mb_substr( self::$_model->content, 0, 300, 'utf-8' );
		
		self::$_model          = self::$_storage->setModel( self::$_model )->addData();
	
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * AnswerUpDown答题顶踩接口
	 * 
	 * 一个用户一天只能顶踩一次
	 * 
	 */
	static public function AnswerUpDown() {
		//判断1天内是否记录过日志
		$userOptlogInfos = Data_Api_OptLog::GetData( self::$_model, 1 );
		
		//判断是否顶踩太频繁
		if ( isset( $userOptlogInfos['createdate'] )
				&&  strtotime( $userOptlogInfos['createdate'] ) + 86400 > time() ) {
				
			Common_Tool::messageExit( '0', 'updown question too much', '顶踩问题太频繁' );
				
		}
		
		self::$_model = self::$_storage->updownData();
		
		//记录日志
		Data_Api_OptLog::ReplaceData( self::$_model );
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * answer_count答题计数接口
	 * 
	 */
	static public function AnswerCount() {
		$total = self::$_storage->getDataCount( array( 'uid' ) );
		
		self::$_model->_outputArr = array(
									'type'  => ANSWER_COUNT_TYPE,
									'count' => strval( $total ),
									);
	
		return self::$_model;
	}
	
	
	/**
	 * cart_add
	 */
	static public function CartAdd() {
		self::$_model = self::$_storage->addData();
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
	
	
	/**
	 * cart_del
	 */
	static public function CartDel() {
		self::$_model = self::$_storage->delData();
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
		
		
	/**
	 * cart_list
	 */
	static public function CartList() {
		
		self::$_model = self::$_storage->getData();
		
		$count = 0;
		
		if ( !is_null( self::$_model->_dataArr ) ) {
			self::$_model->_outputArr = array( 'list'=>array() );
			
			foreach ( self::$_model->_dataArr  AS  $key => $subDatas ) {
				//var_dump( $subDatas['cid'] );exit;
				
				if ( $subDatas['cid'] ) {
					$classModel     = new Model_Api;
					$classModel->id = $subDatas['cid'];
					$classModel     = Storage_Api_Class::GetInst()->setModel( $classModel )->getData();
					$classInfos     = $classModel->_dataArr[ 0 ];
					
					//格式化输出数据
					if ( !is_null( $classInfos ) ) {
						$classInfos['c_status'] = $classInfos['status'];
						
						unset( $classInfos['id'] );
						unset( $classInfos['status'] );
						unset( $classInfos['addtime'] );
						unset( $classInfos['updatetime'] );
						unset( $classInfos['del_time'] );
						
						$count    += $classInfos['price'];
						$subDatas = array_merge( $subDatas, $classInfos );
						self::$_model->_outputArr['list'][] = $subDatas;
					}
					
				}
				
			}
			
		}
		
		self::$_model->_outputArr['total'] = self::$_model->_dataCounter;
		self::$_model->_outputArr['count'] = $count;
		
		return self::$_model;
	}
	
	
	/**
	 * order_add
	 */
	//static  public function OrderAdd() {}
	
	
	/**
	 * order_update
	 */
	//static public function OrderUpdate() {}
	
	
	/**
	 * order_list
	 */
	static public function OrderList() {
		self::$_model = self::$_storage->getData();
		
		self::$_model->_outputArr = array( 
											'list' => self::$_model->_dataArr
											,'total' => self::$_model->_dataCounter
											);
		
		return self::$_model;
	}
	
	
	/**
	 * pay_log
	 */
	static public function PayLog() {
		self::$_model = self::$_storage->addData();
		
		Common_Tool::messageExit( self::$_model->_dataArr );
	}
}