<?php
/**
 * 数据定义
 */
class RetClass {
	//命名规定 : 模块名+操作名+动作状态等，范围大的在前面，由大到小依次排列
	//		     1. 模块名
	//  		 2. 操作名
	// 			 3. 状态
	// 			 4. 定义值分段，模块返回值为8位整型，高4位模块id，低4位为模块内定义，所有数值定义全站不可重复、不可修改
	//					低4位1000的开始编号，000-2999为返回值，3000以上为状态值、类型等，编号要分区间，
	//					如：活动群发方式为20193000-20193049，活动参与方式为20193050-20193099
	//					(*前4位是模块编号，来自EnumClass的模块定义；后4为为返回值编号，从1000开始)
	//

	//////////////////////////////////////// 这部分暂时为目前手机返回值专用 ////////////////////////////////////////
	//---------------------------------------- 公共 ----------------------------------------
	/** 成功 */
	const SUCCESS					= 1000;	//成功	SUCCESS_CODE
	/** 失败 */
	const ERROR						= 1001;	//失败	ERROR_CODE
	/** 无 */
	const NONE						= 800;//其他

	const COM_ARG_ERROR				= 1002;	//参数错误	ARG_ERROR_CODE
	const COM_NO_PERMISSION			= 1003;	//您没有权限进行此操作	NO_PERMISSION_CODE
	const COM_NO_CONFIG				= 1004;	//无配置信息 NO_CONFIG_CODE	电话会议无配置信息
	const COM_NO_NETWORK			= 1005;	//网络不通	NO_NETWORK_CODE
	const COM_CONNECT_SUCCESS		= 1006;	//网络连接成功	CONNECT_SUCCESS_CODE
	const COM_NO_RECODE				= 1007;	//该记录不存在或已删除	NO_RECODE_CODE

	const COM_CREATE_FAILURE		= 1008;	//创建失败	CREATE_FAIL	文件柜 创建文件夹失败
	const COM_NOT_EMPTY				= 1009;	//不能为空	EMPTY_DIR	文件柜 文件夹名字不能为空
	const COM_NAME_SYSTEM			= 1010;	//与系统名相同	SYS_RENAME	文件柜 该名称与系统文件夹重名，请改用其它名称
	const COM_NAME_EXISTED			= 1013;	//名字已存在 EXISTED	文件柜 该文件夹名字已存在
	const COM_READ_ONLY				= 1011;	//只读	READ_ONLY	文件柜 无法[新建]，当前目录已设置成只读
	const COM_REQUEST_TIMEOUT		= 1012;	 //请求超时
	const COM_RECODE_EXISTED		= 1033; //记录已存在
	const COM_RECODE_NOT_EXISTED	= 1034; //没有相关记录
	const COM_OPERATION_FAILED		= 1035; //操作不成功
	const COM_RECORD_EXPIRED_CANNOT_EDIT	= 1036; //记录过期，不能修改
	const COM_STARTTIME_MUST_GREATER_CURRENT= 1037; //开始时间必须大于当前时间
	const COM_ENDTIME_MUST_GREATER_CURRENT	= 1038; //结束时间必须大于当前时间
	const COM_ENDTIME_MUST_GREATER_START	= 1039; //结束时间必须大于开始时间
	const COM_NO_MODIFIED			= 1040; //没有修改
	const COM_RECORD_EXPIRED_CANNOT_DEL		= 1041; //记录已过期，不能删除
	const COM_USER_AUTH_FAILED		= 1042; //用户登录令牌失效，鉴权失败

	//======================================== 公共 ========================================

	//---------------------------------------- 文件柜 ----------------------------------------
	const FILE_CREATE_ERR			= 1008;	//创建失败	CREATE_FAIL	文件柜 创建文件夹失败
	const FILE_NOT_EMPTY			= 1009;	//不能为空	EMPTY_DIR	文件柜 文件夹名字不能为空
	const FILE_NAME_SYSTEM			= 1010;	//重名	SYS_RENAME	文件柜 该名称与系统文件夹重名，请改用其它名称
	const FILE_NAME_EXISTED			= 1013;	//名字已存在 EXISTED	文件柜 该文件夹名字已存在
	const FILE_READ_ONLY			= 1011;	//只读	READ_ONLY	文件柜 无法[新建]，当前目录已设置成只读
	const FILE_NO_PERMISSION		= 1012;	//文件柜 你无权新建文件夹	NO_LIMIT
	const FILE_NOT_SHARE			= 1014;	//文件柜 该文件没有共享	NOT_SHARE
	//======================================== 文件柜 ========================================

	//---------------------------------------- 手机注册 ----------------------------------------
	const REG_MOBI_APPLY_CANCLE		= 1015;	//手机注册 对方已撤销申请	CANCLE_APPLY_CODE
	const REG_MOBI_PROCESSED		= 1016;	//手机注册 该消息已处理！	PROCESSED_CODE
	const REG_MOBI_JOINED			= 1017;	//手机注册 该员工已加入贵单位，无需再次操作	JOINED_CODE
	const REG_MOBI_USER_ADD			= 1018;	//手机注册 创建员工失败！	FAIL_ADDUSER_CODE
	const REG_MOBI_USER_UPDATE		= 1019;	//手机注册 更新员工失败!	FAIL_UPDATEUSER_CODE
	const REG_MOBI_LICENSE_ADD		= 1020;	//手机注册 添加授权码失败！	LICENSE_ERROR_CODE
	const REG_MOBI_APPLY_REFUSE		= 1021;	//手机注册 拒绝申请失败！	REFUSE_ERROR_CODE
	//======================================== 手机注册 ========================================

	const FAX_NO_FAXNUMBER			= 1022;	//网络传真 你没有传真账号	NO_FAXNUMBER_CODE
	const MEETING_ROOM_ISUSED		= 1023;	//会议地点已经被占用	MEETING_ROOM_ISUSE_CODE

	//---------------------------------------- 内部邮件 ----------------------------------------
	const PMS_ISREADS_NOT_CHANGE_DRAFT		= 1024;	//邮件已经被对方阅读，无法撤回	PMS_ISREADS_NOT_CHANGE_DRAFT_CODE
	const PMS_NOTCREATE_NOT_CHANGE_DRAFT	= 1025;	//邮件已经被对方阅读，无法撤回	PMS_NOTCREATE_NOT_CHANGE_DRAFT_CODE
	//======================================== 内部邮件 ========================================

	const SYS_SERVER_RESTART		= 1026;	//服务器重启中	SERVER_RESTART

	//---------------------------------------- 同事 ----------------------------------------
	const COLLEAGUE_NO_ATTENTION	= 1027;	//没有关注同事......	NO_ATTENTION_USER
	//======================================== 同事 ========================================

	//---------------------------------------- 考勤 ----------------------------------------
	const ATTEND_NO_ATTENTION_SCHEDULE			= 1028;	//没有关注班别......	NO_ATTENTION_SCHEDULE
	const ATTEND_NO_ATTENTION_SCHEDULE_AND_USER	= 1029;	//没有关注班别跟同事......	NO_ATTENTION_SCHEDULE_AND_USER
	const ATTEND_NO_CATEGORY					= 1030;	//没有考勤方式......	NO_ATTEND_CATEGORY
	const ATTEND_NO_SCHEDULE_RULE				= 1031;	//没有班次......	NO_ATTEND_SCHEDULE_RULE
	//======================================== 考勤 ========================================

	//---------------------------------------- 表单 ----------------------------------------
	const FORM_NO_USED			= 1032;	//没有使用过表单	NO_USED_CASES
	//======================================== 表单 ========================================

	//////////////////////////////////////// 以上暂时为目前手机返回值专用 ////////////////////////////////////////



	//////////////////////////////////////// 下面是OA3.0定义 返回值 ////////////////////////////////////////

	//---------------------------------------- 参数验证 ----------------------------------------
	const CHECK_PARAM_SUCCESS		= 10001320;
	const CHECK_PARAM_ERR			= 10001321;
	const CHECK_PARAM_NOT_NULL		= 10001322;
	const CHECK_PARAM_NOT_EMPTY		= 10001323;
	const CHECK_PARAM_INVALID		= 10001324;
	//======================================== 参数验证 ========================================

	//---------------------------------------- 数据库 ----------------------------------------
	const DB_WHERE_NONE_ERR			= 10002401;
	//======================================== 数据库 ========================================

	//---------------------------------------- 活动 ----------------------------------------
	const ACTIVITY_CAN_JOIN              = 20191033;	//可以参加活动
	const ACTIVITY_CAN_NOT_JOIN          = 20191034;	//不可以参加活动
	const ACTIVITY_JOIN_SUCCESS          = 20191035;	//参加活动成功
	const ACTIVITY_JOIN_ERROR            = 20191036;	//参加活动失败
	const ACTIVITY_REFUSAL_SUCCESS       = 20191037;	//拒绝参加活动成功
	const ACTIVITY_REFUSAL_ERROR         = 20191038;	//拒绝参加活动失败
	const ACTIVITY_NOT_EXIST             = 20191039;	//活动不存在
	const ACTIVITY_NOT_PERMISSION_VIEW   = 20191040;	//您无权查看活动
	const ACTIVITY_NOT_PERMISSION_EDIT   = 20191041;	//您无权修改该活动
	const ACTIVITY_NOT_PERMISSION_DELETE = 20191042;    //你不能删除该活动
	const ACTIVITY_DEADLINE              = 20191043; //活动报名已截止
	const ACTIVITY_PEOPLE_FULL           = 20191044; //活动参加人数已满
	const ACTIVITY_HAS_JOIN              = 20191045; //您已参加了活动
	const ACTIVITY_HAS_REFUSAL           = 20191046; //您已拒绝参加活动
	const ACTIVITY_IS_AGREE_APPLY        = 20191047; //添加的人员中已有人申请参加活动，是否同意他们的申请？
	const ACTIVITY_HAS_APPLY             = 20191048; //您已申请参加该活动
	const ACTIVITY_DELETE_APPLY_ERROR    = 20191049;  //取消申请失败
	const ACTIVITY_ADD_APPLY_ERROR       = 20191050;  //提交申请失败
	//======================================== 活动 ========================================
	//
    //======================================== 开会 ========================================
	const MEETING_NO_CONFERENCE_CONFIG = 20171000;	//电话会议无配置信息MEETING_NO_NETWORK
	const MEETING_NO_NETWORK           = 20171001;	//网络不通
    //======================================== 开会 ========================================

	//---------------------------------------- 公告 ----------------------------------------
	const NEWS_ALL_READ								= 20011000;	//全部已读
	const NEWS_MSG_WAIT								= 20011001;	//
	const NEWS_MSG_SEND_AGAIN						= 20011002;//再次通知成功!
	const NEWS_RECORD_DELETED						= 20011003;//公告已被删除。
	const NEWS_RECORD_UNPUBLISH						= 20011004;//公告还没发布，无法阅读。
	const NEWS_RECORD_EDITING						= 20011005;//公告正在修改中，无法阅读。
	const NEWS_TIME_INCONFORMITY					= 20011006;//发布时间要大于当前时间
	const NEWS_BACK_TO_DRAFT						= 20011007;//已撤回至草稿箱！
	//======================================== 公告 ========================================

	//-----------------------------------------  网络传真 -------------------------------------------------
	const EFAX_NO_WRONG                             = 20221000; //没有错误信息返回
	const EFAX_HAS_WRONG                            = 20221001; //有错误信息返回
	//=========================================  网络传真 ==================================================

    //---------------------------------------- 行政审批 ----------------------------------------
    const APPROVE_SELECT_FROM                       = 20211000;	//请选择表单
    //======================================== 行政审批 ========================================

	//---------------------------------------- 工作流 ----------------------------------------
    const WORKFLOW_NAME_EMPTY						= 20131000;	//名称不能为空
	const WORKFLOW_STARTED_CANTMODIFIED				= 20131001; //当前流程已启动,不能修改！
	const WORKFLOW_ONLY_CREATER_REMIND				= 20131002; //只有提交人才能催办！
	const WORKFLOW_ONLY_PROCESSING_REMIND			= 20131003; //只有在流程审批进行中才能催办！
	const WORKFLOW_OBJECTWORKFLOW_NOTEXIST			= 20131004; //该表单不存在该流程！
	const WORKFLOW_USER_NO_PERMIT					= 20131005; //当前用户没有该流程修改权限，提交失败！
	const WORKFLOW_CASE_CANCELED					= 20131006; //申请单已撤销，无需重复操作！
	const WORKFLOW_PROCESS_END_NOT_INVITE			= 20131007; //该流程已结束，不能邀请！
	const WORKFLOW_CANNOT_TRANSMIT_MYSELF			= 20131008; //不能转发给自己！
	const WORKFLOW_NOT_TRANSMIT_NO_UNDO				= 20131009; //您还未提交转发，无需撤销！
	const WORKFLOW_SELECT_TRANSAUTH_USER			= 20131010; //请选择权限转移接收人
	const WORKFLOW_CANNOT_TRANSAUTH_MYSELF			= 20131011; //权限不能转给自己
	const WORKFLOW_SELECTED_TIME_HAS_TRANSAUTH		= 20131012; //您所选的时间段已经将权限转移
	const WORKFLOW_SELECTED_TRANSAUTH_USER_NOTEXIST	= 20131013; //您转移权限的人不存在或者被禁用
    //======================================== 工作流 ========================================


    //---------------------------------------- 附件上传 ----------------------------------------
	const UPLOAD_REFER_ERROR     = 20231000;	//来源验证失败
	const UPLOAD_TOKEN_ERROR     = 20231001;	//令牌验证失败
	const UPLOAD_TIMEOUT_ERROR   = 20231002;	//链接失效
	const UPLOAD_BLOCKNO_ERROR   = 20231003;	//所传块号不符合
	const UPLOAD_FILE_NOT_EXISTS = 20231004;	//文件不存在
	const UPLOAD_FILE_TYPE_ERROR = 20231005;	//无效文件格式
	const UPLOAD_FILE_TURN_ERROR = 20231006;	//文件转换失败
    //======================================== 附件上传 ========================================

    //---------------------------------------- 系统消息 ----------------------------------------
	const SYSMSG_SEND_SUCCESS			= 20241000;	//发送成功
	const SYSMSG_ADD_WAIT_ERROR			= 20241002;	//插入wait表失败
	const SYSMSG_ADD_SINGLE_ERROR		= 20241003;	//插入single表失败
	const SYSMSG_PUSH_SUCCESS			= 20241004;	//推送消息成功
	const SYSMSG_PUSH_ERROR				= 20241005;	//推送消息失败
	const SYSMSG_PUSH_GET_HOST_ERROR	= 20241006;	//获取推送host失败
	const SYSMSG_PUSH_CONNETION_ERROR	= 20241007;	//连接失败
	const SYSMSG_READ_SUCCESS			= 20241008;	//设置已读
	const SYSMSG_DEL_SUCCESS			= 20241009;	//删除消息
    //======================================== 系统消息 =======================================

	//---------------------------------------- 内部邮件 ---------------------------------------
    const PMS_GET_COUNTS				= 20281006;	 //获取列表总数成功
	const PMS_TITLE_NOT_NULL			= 20281001;  //标题不能为空
    const PMS_ATTN_NOT_NULL				= 20281002;	 //收件人不能为空
    const PMS_HAD_READ					= 20281004;	 //邮件已经被对方阅读，无法撤回
    const PMS_NOT_USER					= 20281005;  //您不是这封邮件发起者,不可撤销
	//======================================== 内部邮件 ========================================

	//---------------------------------------- 微信企业号 --------------------------------------
    const WEIXIN_MODULE_NOT_BIND		= 20291000;	 //未绑定模块
	//======================================== 微信企业号 ======================================

	//---------------------------------------- 工资 --------------------------------------
    const WAGES_NOT_MODIFY				= 20301000;	 //完全没有修改过数据
    const EXCEL_HEADER_FORMAT_WRONG		= 20301001;	 //excel表表头格式不正确
    const WAGES_IMPORT_RECORD_OVERMUCH	= 20301002;	 //导入的记录过多
    const WAGES_FORMAT_ONLY_BE_NUMBER	= 20301003;	 //格式只能为数字
    const EXCEL_FUND_ONLY_ONEROW		= 20301004;	 //excel表中只有工资款项一行
    const OA_PASSWORD_ERROR				= 20301005;	 //密码错误
    const WAGES_PASSWORD_FORMAT_ERROR	= 20301006;	 //工资查看密码格式有误
    const IMPORT_NUMBER_REPEAT			= 20301007;	 //导入数据时，参数（工号）重复
    const WAGES_IMPORT_COL_OVERMUCH		= 20301008;	 //导入的列数超过限制的值
    const WAGES_FILE_READ_ERROR			= 20301009;	 //文件无法读取
	//======================================== 工资 ======================================

	//======================================== 登录 ======================================
    const LOGIN_EMPLOYEE_NOT_EXIST		= 20341000;	 //该员工不存在
	const LOGIN_PWD_NOT_MATCH			= 20341001;  //用户名称与密码不相符
    const LOGIN_USERDATA_FAILED_LOAD	= 20341002;	 //加载用户数据失败,请检查您的OA服务器的网络是否畅通!
    const LOGIN_NON_ENTERPRISE_USERS	= 20341004;	 //该用户没有企业
    const LOGIN_USER_NOT_EXIST			= 20341005;	 //用户不存在
    const LOGIN_USER_DISABLED			= 20341006;	 //用户已被禁用
    const LOGIN_CODE_ERR				= 20341007;	 //验证码错误
    const LOGIN_MOBILE_NOT_VERIFIED		= 20341008;	 //手机未验证
    const LOGIN_USER_PWD_NOT_MATCH		= 20341009;	 //用户名和密码不符合
    const LOGIN_TIMEOUT					= 20341010;	 //登录超时
    const LOGIN_VERIFY_FAIL				= 20341011;	 //登录验证失败
    //新增用户登录判断
    const LOGIN_FIRST_ONE				= 20341012;	 //第一次登录
    const LOGIN_NO_EMAIL				= 20341013;	 //没有验证邮箱
    const LOGIN_NO_PHONE				= 20341014;	 //没有验证手机
    const LOGIN_NO_CHANGE_PWD			= 20341015;	 //修改密码
    const LOGIN_SELECT_ENTERPRISE		= 20341016;	 //选择企业
    const LOGIN_USER_HALT				= 20341017;	 //账号停用
    const LOGIN_USER_OVERDUE			= 20341018;	 //账号过期
    const LOGIN_ENTERPRISE_HALT			= 20341019;	 //企业停用
    const LOGIN_ENTERPRISE_OVERDUE		= 20341020;	 //企业过期
    const LOGIN_SYS_MSG					= 20341021;  //个人消息
    //用户类型
	const SYS_USER_TYPE_ENTERPRISE		= 20274100; //企业用户
	const SYS_USER_TYPE_PERSONAL		= 20274101; //个人用户
	//======================================== 登录 ======================================

	//======================================== 系统管理 ======================================
	const SYS_EMP_STATE_NORMAL			= 20273000; //正常状态
	const SYS_EMP_STATE_LEAVE			= 20273001; //离职
	const SYS_EMP_STATE_DELETE			= 20273002; //删除
	const SYS_EMP_STATE_REMOVE			= 20273003; //彻底删除

	const SYS_ACCOUNT_STATE_INVITE		= 20273004; //邀请加入单位
	const SYS_ACCOUNT_STATE_BAN			= 20273005; //账号不能登录
	const SYS_ACCOUNT_STATE_DEL			= 20273006; //只删除账号
	//======================================== 系统管理 ======================================
}
?>