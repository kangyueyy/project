<?php
/* 生成于 2017-11-30 */
/**
 * 常量定义
 */
class EnumClass {

//------------------------- 模块新建按钮类型 --------------------------------

const MODULE_ADD_TYPE_JUMP			= 20001000;  //模块新建按钮类型，单表单跳转
const MODULE_ADD_TYPE_DROPDOWN			= 20001001;  //模块新建按钮类型，多表单下拉
const MODULE_ADD_TYPE_LISTPAGE			= 20001002;  //模块新建按钮类型，多表单列表页展示
//------------------------- 模块新建按钮类型 --------------------------------

//------------------------- 同事圈 --------------------------------

const BLOG_TYPE_BLOG			= 20741000;  //动态类型
const BLOG_TYPE_COMMENT			= 20741001;  //回复类型
const BLOG_RECORD_OPTYPE_PRAISE			= 20742000;  //赞赏
const BLOG_PERMIT_NONE			= 20743000;  //不设权限，查所有
const BLOG_PERMIT_ICANSEE			= 20743001;  //我有权看的所有
const BLOG_PERMIT_MYSHARE			= 20743002;  //我的分享
const BLOG_PERMIT_ATME			= 20743003;  //@我的
const BLOG_PERMIT_IPRAISED			= 20743004;  //我赞过的
const BLOG_PERMIT_PRAISEDME			= 20743005;  //赞过我的
const BLOG_PERMIT_MYDEPT			= 20743006;  //同部门的
const BLOG_PERMIT_OTHERSHARE			= 20743007;  //某人的分享
const BLOG_REPLYTYPE_USER			= 20744000;  //用户回复类型
const BLOG_REPLYTYPE_SYSLOG			= 20744001;  //系统日志类型
//------------------------- 同事圈 --------------------------------

//------------------------- 系统类型 --------------------------------

const TP_BINDTYPE_JXMOBILE			= 10013001;  //江西移动集客
const TP_BINDTYPE_WEIXIN			= 10013002;  //微信企业号
const TP_BIND_ENT			= 10013010;  //绑定企业
const TP_BIND_USER			= 10013011;  //绑定用户
const MODULE_CUSTOMFIELD_SET			= 10013012;  //模块自定义字段系统配置允许修改等
const MODULE_FORMITEM_PERMITSET			= 10013013;  //菜单权限控制
const TP_BINDTYPE_OA			= 10013003;  //从OA网页创建企业
const TP_GT_BINDUSER			= 10013005;  //国投用户绑定类型
const TB_UKEY_BINDUSER			= 12010003;  //Ukey绑定oa用户
const TP_UNIFIE_LOGIN			= 12010004;  //统一登录
//------------------------- 系统类型 --------------------------------

//------------------------- 验证操作权限类型 --------------------------------

//------------------------- 验证操作权限类型 --------------------------------

//------------------------- 变更历史 --------------------------------

const UP_HISTORY_TYPE_STAGE			= 10009000;  //阶段变更历史
const UP_PERSON_HISTORY_TYPE_STAGE			= 10009001;  //人员数据更新时间（俊才）//lwh 20170830
//------------------------- 变更历史 --------------------------------

//------------------------- 权限_模块 --------------------------------

const PERMIT_MODULE_ADMINSYSTEM			= 'AdminSystem';	//系统管理
const PERMIT_MODULE_ADMINHRM			= 'AdminHrm';	//人事管理
const PERMIT_MODULE_ADMINDOCS			= 'AdminDocs';	//知识管理
const PERMIT_MODULE_ADMINPROJECT			= 'AdminProject';	//项目管理
const PERMIT_MODULE_VIEWSTRUCTURE			= 'ViewStructure';	//单位信息
const PERMIT_MODULE_ADMINMEETING			= 'AdminMeeting';	//会议管理
const PERMIT_MODULE_TASK			= 'Task';	//任务管理
const PERMIT_MODULE_HR			= 'Hr';	//行政
const PERMIT_MODULE_PROCESS			= 'Process';	//申请审批
const PERMIT_MODULE_WORKFLOW			= 'Workflow';	//工作流
const PERMIT_MODULE_NEWS			= 'News';	//公告
const PERMIT_MODULE_REPORT			= 'Report';	//汇报
const PERMIT_MODULE_PLAN			= 'Plan';	//计划
const PERMIT_MODULE_ACTIVITY			= 'Activity';	//活动
const PERMIT_MODULE_EFAX			= 'efax';	//网络传真
const PERMIT_MODULE_SYSLOG			= 'Syslog';	//系统日志
const PERMIT_MODULE_OASTORE			= 'OaStore';	//oa应用商城 jeffli 2012-02-06
const PERMIT_MODULE_LICENSE			= 'License';	//授权码
const PERMIT_MODULE_OBMALL			= 'OBmall';	//OB商城
const PERMIT_MODULE_CUSTOMER			= 'Customer';	//客户
const PERMIT_MODULE_PAYMENT			= 'Payment';	//催缴费
const PERMIT_MODULE_VISIT			= 'Visit';	//服务拜访
const PERMIT_MODULE_MESSAGEADMIN			= 'MessageAdmin';	//留言后台
const PERMIT_MODULE_HELPADMIN			= 'HelpAdmin';	//客服后台
const PERMIT_MODULE_WINGSALEADMIN			= 'WingSaleAdmin';	//翼办公后台
const PERMIT_MODULE_WINGSALE			= 'WingSale';	//翼办公销售
const PERMIT_MODULE_SMS			= 'Sms';	//sms短信平台
const PERMIT_MODULE_PRODUCT			= 'Product';	//商品
const PERMIT_MODULE_PMS			= 'Pms';	//内部邮件
const PERMIT_MODULE_CHECK			= 'Check';	//盘点
const PERMIT_MODULE_WORKATTENDANCE			= 'WorkAttendance';	//考勤
const PERMIT_MODULE_DATAMANAGEMENT			= 'DataManagement';	//统计分析
const PERMIT_MODULE_PHONEAUTH			= 'PhoneAuth';	//手机认证	dxf 2014-11-21 15:35:47 本版本新增
const PERMIT_MODULE_WAGES			= 'Wages';	//工资 General.20141226
//------------------------- 权限_模块 --------------------------------

//------------------------- 权限_操作 --------------------------------

const PERMIT_ACTION_COUNT			= 'Count';	//统计汇总 General.20141226
const PERMIT_ACTION_CREATE			= 'Create';	//创建操作
const PERMIT_ACTION_ADMIN			= 'Admin';	//管理操作
const PERMIT_ACTION_VIEW			= 'View';	//查看操作
const PERMIT_ACTION_UPLOAD			= 'Upload';	//上传操作
const PERMIT_ACTION_DOWNLOAD			= 'Download';	//下载操作
const PERMIT_ACTION_PUBLISH			= 'Publish';	//发布
const PERMIT_ACTION_ADMINROOM			= 'AdminRoom';	//会议室管理
const PERMIT_ACTION_ADMINTYPE			= 'AdminType';	//类型管理
const PERMIT_ACTION_TOME			= 'ToMe';	//找人汇报，计划
const PERMIT_ACTION_INVITEREG			= 'InviteReg';	//邀请注册
const PERMIT_ACTION_CHECKREG			= 'CheckReg';	//验证注册
const PERMIT_ACTION_ADMINACCOUNT			= 'AdminAccount';	//员工账号管理
const PERMIT_ACTION_ADMINEMPLOYEE			= 'AdminEmployee';	//员工档案管理
const PERMIT_ACTION_PERGROUP			= 'PerGroup';	//权限组管理
const PERMIT_ACTION_ADMINDEPARTMENT			= 'AdminDepartment';	//部门管理
const PERMIT_ACTION_ADMINPOSITON			= 'AdminPositon';	//职务管理
const PERMIT_ACTION_ENTERPRISEINFO			= 'EnterpriseInfo';	//单位信息维护
const PERMIT_ACTION_SYSADMIN			= 'SysAdmin';	//指派应用管理员
const PERMIT_ACTION_BASEPERADMIN			= 'BasePerAdmin';	//指派基本权限
const PERMIT_ACTION_INSTALL			= 'Install';	//安装app jeffli 2012-02-06
const PERMIT_ACTION_UNINSTALL			= 'Uninstall';	//卸载app jeffli 2012-02-06
const PERMIT_ACTION_APPROVAL			= 'Approval';	//审批验收
const PERMIT_ACTION_HIGHCREATE			= 'HighCreate';	//高级业务申请
const PERMIT_ACTION_PUBLISHCOMPANY			= 'PublishCompany';	//发起以单位名义的活动
const PERMIT_ACTION_GOODSADMIN			= 'GoodsAdmin';	//物品管理
const PERMIT_ACTION_CONFIRM			= 'Confirm';	//缴费确认
const PERMIT_ACTION_ADMINMEAL			= 'AdminMeal';	//管理套餐
const PERMIT_ACTION_ADMINCHANNEL			= 'AdminChannel';	//管理渠道
const PERMIT_ACTION_ACCEPTBUSINESS			= 'AcceptBusiness';	//受理业务
const PERMIT_ACTION_ADMINMANAGER			= 'AdminManager';	//修改客户经理
const PERMIT_ACTION_APPROVE			= 'Approve';	//管理认证受理
const PERMIT_ACTION_USEALLMEAL			= 'UseAllMeal';	//使用停用套餐
const PERMIT_ACTION_VIEWALLAPPLY			= 'ViewAllApply';	//查看全部申请
const PERMIT_ACTION_VIEWALLCUSTOMER			= 'ViewAllCustomer';	//查看全部客户
const PERMIT_ACTION_SMSADMIN			= 'smsAdmin';	//管理短信
const PERMIT_ACTION_ALLVIEW			= 'allView';	//查看全部短信
const PERMIT_ACTION_VIEWALLSUMMARY			= 'ViewAllSummary';	//查看所有表单报表
const PERMIT_ACTION_EXPORT			= 'Export';	//客户档案导出
const PERMIT_ACTION_IMPORT			= 'Import';	//客户档案导入
const PERMIT_ACTION_RECEIVEMAIL			= 'receivemail';	//内部邮件的收邮件
const PERMIT_ACTION_SENDMAIL			= 'sendmail';	//内部邮件的发邮件
const PERMIT_ACTION_BULKMAIL			= 'bulkmail';	//内部邮件的群发邮件
const PERMIT_ACTION_CREATEMEETINGRULE			= 'Createmeetingrule';	//创建例会
const PERMIT_ACTION_MESSAGESETTING			= 'MessageSetting';	//系统管理的    消息设置的权限   add on 2014-3-27
const PERMIT_ACTION_ADMINOFFICEONLINE			= 'AdminOfficeOnline';	//管理Office控件
const PERMIT_ACTION_PHONEAUTH			= 'PhoneAuth';	//手机认证管理	//dxf 2014-11-21 15:36:46 旧版本存在，是否移到上一个类？
const PERMIT_ACTIONTYPE_ALL			= 20434311;  //全部操作
//------------------------- 权限_操作 --------------------------------

//------------------------- 评论类型 --------------------------------

const COMMENT_TYPE_ALLOW			= 20201000;  //允许评论
const COMMENT_TYPE_PUBLIC			= 20201001;  //公开评论
const COMMENT_TYPE_REPLY			= 20203000;  //回复类型
//------------------------- 评论类型 --------------------------------

//------------------------- 会议 --------------------------------

const MEETING_TYPE_GENERAL			= 0;  //普通会议
const MEETING_TYPE_VIDEO			= 1;  //视频会议
const MEETING_TYPE_PROJECT			= 2;  //项目会议
const MEETING_TYPE_TASK_CLOSE			= 20172000;  //未开通会议任务高级功能
const MEETING_TYPE_TASK_NONE			= 20172001;  //会议未安排任务
const MEETING_TYPE_TASK_HAS			= 20172002;  //会议已安排任务
//------------------------- 会议 --------------------------------

//------------------------- 系统日志操作类型 --------------------------------

const SYSLOG_CAT_MEETING_PLACE_ADD			= 1;  //添加了会议地点
const SYSLOG_CAT_MEETING_PLACE_EIDT			= 2;  //修改了会议地点
const SYSLOG_CAT_MEETING_PLACE_DEL			= 3;  //删除了会议地点：[***]
const SYSLOG_CAT_MEETING_ADD			= 4;  //新建会议
const SYSLOG_CAT_MEETING_EDIT			= 5;  //修改了会议
const SYSLOG_CAT_MEETING_DEL			= 6;  //删除会议
const SYSLOG_CAT_NEWS_TYPE_ADD			= 7;  //添加了公告类别：[***]
const SYSLOG_CAT_NEWS_TYPE_EDIT			= 8;  //修改了公告类别：[***]
const SYSLOG_CAT_NEWS_TYPE_DEL			= 9;  //修改了公告类别：[***]
const SYSLOG_CAT_NEWS_ADD			= 10;  //添加公告
const SYSLOG_CAT_NEWS_EDIT			= 11;  //修改公告
const SYSLOG_CAT_NEWS_DEL			= 12;  //删除公告
const SYSLOG_CAT_PLAN_ADD			= 13;  //添加计划
const SYSLOG_CAT_PLAN_EDIT			= 14;  //修改计划
const SYSLOG_CAT_PLAN_DEL			= 15;  //删除计划
const SYSLOG_CAT_PLAN_RULE_ADD			= 16;  //系统日志操作
const SYSLOG_CAT_PLAN_RULE_EDIT			= 17;  //系统日志操作
const SYSLOG_CAT_PLAN_RULE_DEL			= 18;  //系统日志操作
const SYSLOG_CAT_REPORT_ADD			= 19;  //添加汇报
const SYSLOG_CAT_REPORT_EDIT			= 20;  //修改汇报
const SYSLOG_CAT_REPORT_DEL			= 21;  //删除汇报
const SYSLOG_CAT_REPORT_RULE_ADD			= 22;  //系统日志操作
const SYSLOG_CAT_REPORT_RULE_EDIT			= 23;  //系统日志操作
const SYSLOG_CAT_REPORT_RULE_DEL			= 24;  //系统日志操作
const SYSLOG_CAT_SYSTEM_EMPLOYEE_APPLY			= 25;  //添加员工账号(已注册,未加入状态)
const SYSLOG_CAT_SYSTEM_EMPLOYEE_SUCESS			= 27;  //员工加入成功
const SYSLOG_CAT_SYSTEM_EMPLOYEE_REJECT			= 28;  //拒绝加入单位
const SYSLOG_CAT_SYSTEM_EMPLOYEE_ACTIVE			= 29;  //员工激活成功
const SYSLOG_CAT_SYSTEM_EMPLOYEE_EDIT			= 30;  //修改员工帐号
const SYSLOG_CAT_SYSTEM_POWER_ADD			= 36;  //添加了权限组
const SYSLOG_CAT_SYSTEM_POWER_EDIT_PEOPLE			= 37;  //修改了权限组[***]的成员
const SYSLOG_CAT_SYSTEM_POWER_EDIT_VALUE			= 38;  //修改了权限组[***]的权限值
const SYSLOG_CAT_SYSTEM_POWER_DEL			= 40;  //删除了权限组
const SYSLOG_CAT_POWER_BOSS			= 41;  //修改老板账号
const SYSLOG_CAT_POWER_ADMIN			= 42;  //修改了系统管理员账号
const SYSLOG_CAT_SYS_SAFE_EDIT			= 43;  //修改安全设置
const SYSLOG_CAT_SYS_GREET			= 44;  //修改新员工入职问候
const SYSLOG_CAT_LOGIN			= 45;  //系统日志操作
const SYSLOG_CAT_LOGINOUT			= 46;  //系统日志操作
const SYSLOG_CAT_ATTACHMENT_ADD			= 47;  //系统日志操作
const SYSLOG_CAT_ATTACHMENT_DEL			= 48;  //系统日志操作
const SYSLOG_CAT_TASK_ADD			= 49;  //系统日志操作
const SYSLOG_CAT_TASK_DEL			= 51;  //系统日志操作
const SYSLOG_CAT_PROCESS_FORMTYPE_EDIT			= 53;  //系统日志操作
const SYSLOG_CAT_PROCESS_FORMTYPE_DEL			= 54;  //系统日志操作
const SYSLOG_CAT_PROCESS_TYPE_ADD			= 55;  //系统日志
const SYSLOG_CAT_PROCESS_TYPE_EDIT			= 56;  //系统日志操作
const SYSLOG_CAT_PROCESS_TYPE_DEL			= 57;  //系统日志操作
const SYSLOG_CAT_PROCESS_FORM_ADD			= 58;  //系统日志操作
const SYSLOG_CAT_PROCESS_FORM_EDIT			= 59;  //系统日志操作
const SYSLOG_CAT_PROCESS_FORM_DEL			= 60;  //系统日志操作
const SYSLOG_CAT_PROCESS_ADD			= 61;  //系统日志操作
const SYSLOG_CAT_PROCESS_EDIT			= 62;  //系统日志操作
const SYSLOG_CAT_PROCESS_DEL			= 63;  //系统日志操作
const SYSLOG_CAT_PROCESS_APPLY			= 64;  //系统日志操作
const SYSLOG_CAT_PROCESS_HANDLE			= 65;  //系统日志操作
const SYSLOG_CAT_PROCESS_DEL_APPLYFORM			= 155;  //删除申请单 //GENERAL.20140613
const SYSLOG_CAT_PROCESS_CANCEL_APPLYFORM			= 156;  //撤销申请单 //GENERAL.20140613
const SYSLOG_CAT_PROCESS_INVITE			= 141;  //邀请审批
const SYSLOG_CAT_SYSTEM_DEPARTMENT_ADD			= 66;  //添加部门
const SYSLOG_CAT_SYSTEM_DEPARTMENT_EDIT			= 67;  //系统日志操作
const SYSLOG_CAT_SYSTEM_DEPARTMENT_DEL			= 68;  //系统日志操作
const SYSLOG_CAT_SYSTEM_POSITION_ADD			= 69;  //添加职位
const SYSLOG_CAT_SYSTEM_POSITION_EDIT			= 70;  //系统日志操作
const SYSLOG_CAT_SYSTEM_POSITION_DEL			= 71;  //系统日志操作
const SYSLOG_CAT_SYSTEM_EMPLOYEE_DEL			= 72;  //删除员工帐号
const SYSLOG_CAT_SYSTEM_EMPLOYEE_BAN			= 73;  //启用员工账号
const SYSLOG_CAT_SYSTEM_EMPLOYEE_SET			= 74;  //禁用员工账号
const SYSLOG_CAT_SYS_SAFE_TIME			= 75;  //系统日志操作
const SYSLOG_CAT_SYS_SAFE_JOIN			= 76;  //系统日志操作
const SYSLOG_CAT_ACTIVITY_ADD			= 77;  //系统日志操作
const SYSLOG_CAT_ACTIVITY_EDIT			= 78;  //系统日志操作
const SYSLOG_CAT_ACTIVITY_DEL			= 79;  //系统日志操作
const SYSLOG_CAT_TRANSFER			= 80;  //系统日志操作
const SYSLOG_CAT_COMPANY_EXIT			= 81;  //系统日志操作
const SYSLOG_CAT_DOC_DIRECTORY_VIEW			= 82;  //系统日志操作
const SYSLOG_CAT_DOC_DIRECTORY_DEL			= 83;  //系统日志操作
const SYSLOG_CAT_DOC_DIRECTORY_CREATE			= 84;  //系统日志操作
const SYSLOG_CAT_DOC_FILE_VIEW			= 85;  //系统日志操作
const SYSLOG_CAT_DOC_FILE_UPLOAD			= 86;  //系统日志操作
const SYSLOG_CAT_DOC_FILE_DOWNLOAD			= 87;  //系统日志操作
const SYSLOG_CAT_DOC_FILE_DEL			= 88;  //系统日志操作
const SYSLOG_CAT_DOC_DIRECTORY_RENAME			= 89;  //系统日志操作
const SYSLOG_CAT_DOC_FILE_RENAME			= 90;  //系统日志操作
const SYSLOG_CAT_DOC_DIRECTORY_SET_PERMISSIONS			= 91;  //系统日志操作
const SYSLOG_CAT_DOC_BASEINFO_EDIT_DIRECTORY			= 95;  //系统日志操作
const SYSLOG_CAT_DOC_BASEINFO_EDIT_FILE			= 96;  //系统日志操作
const SYSLOG_CAT_CUSTOMER_ADD			= 97;  //新建客户
const SYSLOG_CAT_CUSTOMER_DEL			= 98;  //删除客户
const SYSLOG_CAT_CUSTOMER_TYPE_ADD			= 99;  //添加客户字段
const SYSLOG_CAT_CUSTOMER_DEL_TYPE			= 100;  //删除客户字段
const SYSLOG_CAT_CUSTOMER_EDIT_TYPE			= 101;  //修改客户字段
const SYSLOG_CAT_VISIT_ADD			= 102;  //新建服务拜访
const SYSLOG_CAT_VISIT_DEL			= 103;  //删除服务拜访
const SYSLOG_CAT_VISIT_TYPE_ADD			= 104;  //添加拜访方式
const SYSLOG_CAT_VISIT_TYPE_DEL			= 105;  //删除拜访方式
const SYSLOG_CAT_VISIT_TYPE_EDIT			= 106;  //修改拜访方式
const SYSLOG_CAT_VISIT_SIGN			= 107;  //签到评估功能
const SYSLOG_CAT_PAYMENT_ADD			= 108;  //新建缴费通知单
const SYSLOG_CAT_PAYMENT_DEL			= 109;  //删除缴费通知单
const SYSLOG_CAT_PAYMENT_PAYSTATUS_SUCESS			= 110;  //标记为已缴费
const SYSLOG_CAT_PAYMENT_PAYSTATUS_FALSE			= 111;  //取消已缴费
const SYSLOG_CAT_PMS_SEND			= 112;  //发送内部邮件
const SYSLOG_CAT_PMS_DRAFT			= 113;  //保存草稿
const SYSLOG_CAT_PMS_SETREAD			= 114;  //全部设为已读
const SYSLOG_CAT_VOTE_ADD_INFORMED			= 115;  //知会
const SYSLOG_CAT_VOTE_ADD_INSTRUCTIONS			= 116;  //请示
const SYSLOG_CAT_VOTE_ADD_COUNTERSIGN			= 117;  //会签
const SYSLOG_CAT_VOTE_ADD_IDEA			= 118;  //收集意见
const SYSLOG_CAT_VOTE_ADD_VOTE			= 119;  //投票
const SYSLOG_CAT_VOTE_ADD_TOUSERS			= 120;  //添加收件人
const SYSLOG_CAT_VOTE_DEL_LMAIL			= 121;  //删除邮件
const SYSLOG_CAT_VOTE_DEL_DRAFT			= 122;  //删除草稿
const SYSLOG_CAT_SMS_SEND			= 123;  //发送短信
const SYSLOG_CAT_SMS_BUY			= 124;  //购买短信
const SYSLOG_CAT_SMS_SET			= 125;  //短信设置
const SYSLOG_CAT_EFAX_SEND			= 126;  //发传真
const SYSLOG_CAT_EFAX_REVICE			= 127;  //收传真
const SYSLOG_CAT_EFAX_DEL_REVICE			= 128;  //删除收到的传真
const SYSLOG_CAT_EFAX_DEL_SEND			= 129;  //删除发送的传真
const SYSLOG_CAT_EFAX_BIND_NEW			= 130;  //绑定新传真总机
const SYSLOG_CAT_EFAX_EDIT_TELEPHONE			= 131;  //修改传真总机
const SYSLOG_CAT_EFAX_BIND_CHILDREN			= 132;  //绑定新传真分机
const SYSLOG_CAT_EFAX_EDIT_BIND			= 133;  //新改传真分机
const SYSLOG_CAT_EFAX_EDIT_ALLOWSEND			= 134;  //修改发传真权限
const SYSLOG_CAT_DOC_CANCEL_SHARE_TO			= 135;  //文件柜取消对别人的共享
const SYSLOG_CAT_DOC_CANCEL_SHARE_FORME			= 136;  //文件柜取消对我的共享
const SYSLOG_CAT_DOC_SHARE_ADD			= 137;  //添加一条共享设置
const SYSLOG_CAT_DOC_SHARE_EDIT			= 138;  //修改一条共享设置
const SYSLOG_CAT_DOC_SHARE_DEL			= 139;  //取消某一共享设置
const SYSLOG_CAT_EXCEL_IMPORT			= 140;  //EXCEL导入
const SYSLOG_CAT_DOC_MOVE			= 142;  //文件移动
const SYSLOG_CAT_TEL_SEARCH			= 143;  //允许选取相册文件
const SYSLOG_CAT_DEL_TIME			= 144;  //设置允许删改时限
const SYSLOG_CAT_CUSTOMER_DEL_CUSTOMERS			= 145;  //批量删除客户
const SYSLOG_CAT_CUSTOMER_UP_MANAGER			= 146;  //转移负责人
const SYSLOG_CAT_CUSTOMER_UP_MANAGERS			= 147;  //批量转移负责人
const SYSLOG_CAT_CUSTOMER_EDIT_TYPES			= 148;  //批量修改客户字段
const SYSLOG_CAT_CUSTOMER_BUSINESSER			= 149;  //一个业务协助人
const SYSLOG_CAT_CUSTOMER_BUSINESSERS			= 150;  //多个业务协助人
const SYSLOG_CAT_CUSTOMER_ASSISTANT			= 151;  //一个业务助理
const SYSLOG_CAT_CUSTOMER_ASSISTANTS			= 152;  //多个业务助理
const SYSLOG_CAT_CUSTOMER_SHARE			= 153;  //一个共享人
const SYSLOG_CAT_CUSTOMER_SHARES			= 154;  //多个共享人
const SYSLOG_TYPE_UPDATEADMIN_UPDATE			= 20813001;  //更新部署后台更新操作
const SYSLOG_TYPE_USERDEVICE			= 20813002;  //用户设备历史
//------------------------- 系统日志操作类型 --------------------------------

//------------------------- 图表类型 --------------------------------

const CHART_TYPE_PIE			= 10008000;  //饼图
const CHART_TYPE_BAR			= 10008001;  //条形图
const CHART_COUNT_TIME_MONTH			= 'month';	//按月份统计
const CHART_COUNT_TIME_QUARTER			= 'quarter';	//按季度统计
const CHART_COUNT_TIME_YEAR			= 'year';	//按年统计
const CHART_COUNT_TIME_CTM_TIME			= 'day';	//按自定义时间段统计
const CHART_COUNT_OPP_COUNT			= 'opportunity_count';	//统计机会数量
const CHART_COUNT_CUS_COUNT			= 'customer_name';	//统计客户数量
const CHART_COUNT_GRADE			= 'grade';	//按客户级别分组统计
const CHART_COUNT_INDUSTRY			= 'industry';	//按客户行业分组统计
const CHART_COUNT_DEPT			= 'department';	//按部门统计
const CHART_COUNT_HEADUSER			= 'headuser';	//按负责人统计
const CHART_TYPE_HISTOGRAM			= 10008002;  //柱状图
const CHART_TYPE_FUNNEL			= 10008004;  //漏斗图
const CHART_TYPE_BUBBLE			= 10008005;  //气泡图
const CHART_TYPE_RADAR			= 10008006;  //雷达图
const CHART_TYPE_PROPEL			= 10008003;  //推进图
const CHART_SHOW_DATA			= 10008010;  //图表区域数据
//------------------------- 图表类型 --------------------------------

//------------------------- 系统日志 --------------------------------

const SYSLOG_MEETING_PLACE_ADD			= 1;  //添加了会议地点
const SYSLOG_MEETING_PLACE_EIDT			= 2;  //修改了会议地点
const SYSLOG_MEETING_PLACE_DEL			= 3;  //删除了会议地点：[***]
const SYSLOG_MEETING_ADD			= 4;  //新建会议
const SYSLOG_MEETING_EDIT			= 5;  //修改了会议
const SYSLOG_MEETING_DEL			= 6;  //删除会议
const SYSLOG_NEWS_TYPE_ADD			= 7;  //添加了公告类别：[***]
const SYSLOG_NEWS_TYPE_EDIT			= 8;  //修改了公告类别：[***]
const SYSLOG_NEWS_TYPE_DEL			= 9;  //修改了公告类别：[***]
const SYSLOG_NEWS_ADD			= 10;  //添加公告
const SYSLOG_NEWS_EDIT			= 11;  //修改公告
const SYSLOG_NEWS_DEL			= 12;  //删除公告
const SYSLOG_PLAN_ADD			= 13;  //添加计划
const SYSLOG_PLAN_EDIT			= 14;  //修改计划
const SYSLOG_PLAN_DEL			= 15;  //删除计划
const SYSLOG_PLAN_RULE_ADD			= 16;  //添加计划规则
const SYSLOG_PLAN_RULE_EDIT			= 17;  //编辑计划规则
const SYSLOG_PLAN_RULE_DEL			= 18;  //删除计划规则
const SYSLOG_REPORT_ADD			= 19;  //添加汇报
const SYSLOG_REPORT_EDIT			= 20;  //修改汇报
const SYSLOG_REPORT_DEL			= 21;  //删除汇报
const SYSLOG_REPORT_RULE_ADD			= 22;  //添加汇报规则
const SYSLOG_REPORT_RULE_EDIT			= 23;  //编辑汇报规则
const SYSLOG_REPORT_RULE_DEL			= 24;  //删除汇报规则
const SYSLOG_SYSTEM_EMPLOYEE_APPLY			= 25;  //添加员工账号(已注册,未加入状态)
const SYSLOG_SYSTEM_EMPLOYEE_HELP			= 26;  //添加员工账号(帮助注册,未激活状态)
const SYSLOG_SYSTEM_EMPLOYEE_SUCESS			= 27;  //员工加入成功
const SYSLOG_SYSTEM_EMPLOYEE_REJECT			= 28;  //拒绝加入单位
const SYSLOG_SYSTEM_EMPLOYEE_ACTIVE			= 29;  //员工激活成功
const SYSLOG_SYSTEM_EMPLOYEE_EDIT			= 30;  //修改员工帐号
const SYSLOG_SYSTEM_POWER_ADD			= 36;  //添加了权限组
const SYSLOG_SYSTEM_POWER_EDIT_PEOPLE			= 37;  //修改了权限组[***]的成员
const SYSLOG_SYSTEM_POWER_EDIT_VALUE			= 38;  //修改了权限组[***]的权限值
const SYSLOG_SYSTEM_POWER_DEL			= 40;  //删除了权限组
const SYSLOG_POWER_BOSS			= 41;  //修改老板账号
const SYSLOG_POWER_ADMIN			= 42;  //修改了系统管理员账号
const SYSLOG_SYS_SAFE_EDIT			= 43;  //修改安全设置
const SYSLOG_SYS_GREET			= 44;  //修改新员工入职问候
const SYSLOG_LOGIN			= 45;  //系统日志
const SYSLOG_LOGINOUT			= 46;  //系统日志
const SYSLOG_ATTACHMENT_ADD			= 47;  //系统日志
const SYSLOG_ATTACHMENT_DEL			= 48;  //系统日志
const SYSLOG_TASK_ADD			= 49;  //系统日志
const SYSLOG_TASK_EDIT			= 50;  //系统日志
const SYSLOG_TASK_DEL			= 51;  //系统日志
const SYSLOG_PROCESS_FORMTYPE_ADD			= 52;  //系统日志
const SYSLOG_PROCESS_FORMTYPE_EDIT			= 53;  //系统日志
const SYSLOG_PROCESS_FORMTYPE_DEL			= 54;  //系统日志
const SYSLOG_PROCESS_TYPE_ADD			= 55;  //系统日志
const SYSLOG_PROCESS_TYPE_EDIT			= 56;  //系统日志
const SYSLOG_PROCESS_TYPE_DEL			= 57;  //系统日志
const SYSLOG_PROCESS_FORM_ADD			= 58;  //系统日志
const SYSLOG_PROCESS_FORM_EDIT			= 59;  //系统日志
const SYSLOG_PROCESS_FORM_DEL			= 60;  //系统日志
const SYSLOG_PROCESS_ADD			= 61;  //系统日志
const SYSLOG_PROCESS_EDIT			= 62;  //系统日志
const SYSLOG_PROCESS_DEL			= 63;  //系统日志
const SYSLOG_PROCESS_APPLY			= 64;  //系统日志
const SYSLOG_PROCESS_HANDLE			= 65;  //系统日志
const SYSLOG_PROCESS_DEL_APPLYFORM			= 155;  //删除申请单 //GENERAL.20140613
const SYSLOG_PROCESS_CANCEL_APPLYFORM			= 156;  //撤销申请单 //GENERAL.20140613
const SYSLOG_PROCESS_INVITE			= 141;  //邀请审批
const SYSLOG_SYSTEM_DEPARTMENT_ADD			= 66;  //添加部门
const SYSLOG_SYSTEM_DEPARTMENT_EDIT			= 67;  //系统日志
const SYSLOG_SYSTEM_DEPARTMENT_DEL			= 68;  //系统日志
const SYSLOG_SYSTEM_POSITION_ADD			= 69;  //添加职位
const SYSLOG_SYSTEM_POSITION_EDIT			= 70;  //系统日志
const SYSLOG_SYSTEM_POSITION_DEL			= 71;  //系统日志
const SYSLOG_SYSTEM_EMPLOYEE_DEL			= 72;  //删除员工帐号
const SYSLOG_SYSTEM_EMPLOYEE_BAN			= 73;  //启用员工账号
const SYSLOG_SYSTEM_EMPLOYEE_SET			= 74;  //禁用员工账号
const SYSLOG_SYS_SAFE_TIME			= 75;  //系统日志
const SYSLOG_SYS_SAFE_JOIN			= 76;  //系统日志
const SYSLOG_ACTIVITY_ADD			= 77;  //系统日志
const SYSLOG_ACTIVITY_EDIT			= 78;  //系统日志
const SYSLOG_ACTIVITY_DEL			= 79;  //系统日志
const SYSLOG_TRANSFER			= 80;  //系统日志
const SYSLOG_COMPANY_EXIT			= 81;  //系统日志
const SYSLOG_DOC_DIRECTORY_VIEW			= 82;  //系统日志
const SYSLOG_DOC_DIRECTORY_DEL			= 83;  //系统日志
const SYSLOG_DOC_DIRECTORY_CREATE			= 84;  //系统日志
const SYSLOG_DOC_FILE_VIEW			= 85;  //系统日志
const SYSLOG_DOC_FILE_UPLOAD			= 86;  //系统日志
const SYSLOG_DOC_FILE_DOWNLOAD			= 87;  //系统日志
const SYSLOG_DOC_FILE_DEL			= 88;  //系统日志
const SYSLOG_DOC_DIRECTORY_RENAME			= 89;  //系统日志
const SYSLOG_DOC_FILE_RENAME			= 90;  //系统日志
const SYSLOG_DOC_DIRECTORY_SET_PERMISSIONS			= 91;  //系统日志
const SYSLOG_DOC_BASEINFO_EDIT_DIRECTORY			= 95;  //系统日志
const SYSLOG_DOC_BASEINFO_EDIT_FILE			= 96;  //系统日志
const SYSLOG_CUSTOMER_ADD			= 97;  //新建客户
const SYSLOG_CUSTOMER_DEL			= 98;  //删除客户
const SYSLOG_CUSTOMER_TYPE_ADD			= 99;  //添加客户字段
const SYSLOG_CUSTOMER_DEL_TYPE			= 100;  //删除客户字段
const SYSLOG_CUSTOMER_EDIT_TYPE			= 101;  //修改客户字段
const SYSLOG_VISIT_ADD			= 102;  //新建服务拜访
const SYSLOG_VISIT_DEL			= 103;  //删除服务拜访
const SYSLOG_VISIT_TYPE_ADD			= 104;  //添加拜访方式
const SYSLOG_VISIT_TYPE_DEL			= 105;  //删除拜访方式
const SYSLOG_VISIT_TYPE_EDIT			= 106;  //修改拜访方式
const SYSLOG_VISIT_SIGN			= 107;  //签到评估功能
const SYSLOG_PAYMENT_ADD			= 108;  //新建缴费通知单
const SYSLOG_PAYMENT_DEL			= 109;  //删除缴费通知单
const SYSLOG_PAYMENT_PAYSTATUS_SUCESS			= 110;  //标记为已缴费
const SYSLOG_PAYMENT_PAYSTATUS_FALSE			= 111;  //取消已缴费
const SYSLOG_PMS_SEND			= 112;  //发送内部邮件
const SYSLOG_PMS_DRAFT			= 113;  //保存草稿
const SYSLOG_PMS_SETREAD			= 114;  //全部设为已读
const SYSLOG_VOTE_ADD_INFORMED			= 115;  //知会
const SYSLOG_VOTE_ADD_INSTRUCTIONS			= 116;  //请示
const SYSLOG_VOTE_ADD_COUNTERSIGN			= 117;  //会签
const SYSLOG_VOTE_ADD_IDEA			= 118;  //收集意见
const SYSLOG_VOTE_ADD_VOTE			= 119;  //投票
const SYSLOG_VOTE_ADD_TOUSERS			= 120;  //添加收件人
const SYSLOG_VOTE_DEL_LMAIL			= 121;  //删除邮件
const SYSLOG_VOTE_DEL_DRAFT			= 122;  //删除草稿
const SYSLOG_SMS_SEND			= 123;  //发送短信
const SYSLOG_SMS_BUY			= 124;  //购买短信
const SYSLOG_SMS_SET			= 125;  //短信设置
const SYSLOG_EFAX_SEND			= 126;  //发传真
const SYSLOG_EFAX_REVICE			= 127;  //收传真
const SYSLOG_EFAX_DEL_REVICE			= 128;  //删除收到的传真
const SYSLOG_EFAX_DEL_SEND			= 129;  //删除发送的传真
const SYSLOG_EFAX_BIND_NEW			= 130;  //绑定新传真总机
const SYSLOG_EFAX_EDIT_TELEPHONE			= 131;  //修改传真总机
const SYSLOG_EFAX_BIND_CHILDREN			= 132;  //绑定新传真分机
const SYSLOG_EFAX_EDIT_BIND			= 133;  //新改传真分机
const SYSLOG_EFAX_EDIT_ALLOWSEND			= 134;  //修改发传真权限
const SYSLOG_DOC_CANCEL_SHARE_TO			= 135;  //文件柜取消对别人的共享
const SYSLOG_DOC_CANCEL_SHARE_FORME			= 136;  //文件柜取消对我的共享
const SYSLOG_DOC_SHARE_ADD			= 137;  //添加一条共享设置
const SYSLOG_DOC_SHARE_EDIT			= 138;  //修改一条共享设置
const SYSLOG_DOC_SHARE_DEL			= 139;  //取消某一共享设置
const SYSLOG_EXCEL_IMPORT			= 140;  //EXCEL导入
const SYSLOG_DOC_MOVE			= 142;  //文件移动
const SYSLOG_TEL_SEARCH			= 143;  //允许选取相册文件
const SYSLOG_DEL_TIME			= 144;  //设置允许删改时限
const SYSLOG_CUSTOMER_DEL_CUSTOMERS			= 145;  //批量删除客户
const SYSLOG_CUSTOMER_UP_MANAGER			= 146;  //转移负责人
const SYSLOG_CUSTOMER_UP_MANAGERS			= 147;  //批量转移负责人
const SYSLOG_CUSTOMER_EDIT_TYPES			= 148;  //批量修改客户字段
const SYSLOG_CUSTOMER_BUSINESSER			= 149;  //一个业务协助人
const SYSLOG_CUSTOMER_BUSINESSERS			= 150;  //多个业务协助人
const SYSLOG_CUSTOMER_ASSISTANT			= 151;  //一个业务助理
const SYSLOG_CUSTOMER_ASSISTANTS			= 152;  //多个业务助理
const SYSLOG_CUSTOMER_SHARE			= 153;  //一个共享人
const SYSLOG_CUSTOMER_SHARES			= 154;  //多个共享人
const SYSLOG_TYPE_TRANSFER			= 20813003;  //数据迁移
//------------------------- 系统日志 --------------------------------

//------------------------- 数据库类型 --------------------------------

const DB_NAME_PERSONAL			= 10001400;  //个人端数据库
const DB_NAME_ENTERPRISE			= 10001401;  //企业端数据库
const DB_NAME_UPDATE			= 10001402;  //配置数据库
//------------------------- 数据库类型 --------------------------------

//------------------------- 语言包 --------------------------------

const LANG_TYPE_ZHCN			= 20783000;  //简体中文
const LANG_TYPE_ZHTW			= 20783001;  //繁体中文(香港)
const LANG_TYPE_ZHHK			= 20783002;  //繁体中文(台湾)
const LANG_TYPE_ENG			= 20783003;  //英文
//------------------------- 语言包 --------------------------------

//------------------------- 系统消息 --------------------------------

const MSG_ACTION_ADD			= 20243000;  //添加
const MSG_ACTION_EDIT			= 20243001;  //修改
const MSG_ACTION_COMMON			= 20243002;  //其它操作
const MSG_ACTION_DELETE			= 20243003;  //删除
const MSG_ACTION_WARN			= 20243004;  //提醒
const MSG_ACTION_COMMENT			= 20243005;  //评论
const MSG_SETTYPE_PERSONAL			= 20243100;  //个人设置
const MSG_SETTYPE_ENTERPRISE			= 20243101;  //单位设置
const MSG_SETTYPE_SMS			= 20243102;  //消息设置
const MSG_PUSH_WEB			= 1;  //客户端位运算 1
const MSG_PUSH_MOBILE			= 2;  //客户端位运算 10
const MSG_PUSH_IM			= 4;  //客户端位运算 100
const MSG_PUSH_TYPE_LIST			= 20243200;  //推送列表
const MSG_PUSH_TYPE_COUNT			= 20243201;  //推送统计
const MSG_READ_MSG			= 50;  //标为已读
const MSG_DEL_MSG			= 100;  //删除消息
const MSG_SEND_MESSAGE			= 0;  //系统消息
const MSG_SEND_PUSH			= 10;  //推送
const MSG_SEND_ALL			= 20;  //推送+系统消息
const MSG_OPTYPE_APPLY2JOIN			= '202440000';	//申请加入
const MSG_SEND_SMS			= 30;  //短信
const MSG_PUSH_PLATFORM_IM			= 20241000;  //推送平台IM
const MSG_PUSH_PLATFORM_XG			= 20241001;  //推送平台信鸽
const MSG_SEND_WX			= 40;  //企业微信消息推送
//------------------------- 系统消息 --------------------------------

//------------------------- 消息队列 --------------------------------

const QUEUE_TASK_TYPE_LOOP			= 50;  //每天循环
const QUEUE_TASK_TYPE_ONCE			= 100;  //单次执行
//------------------------- 消息队列 --------------------------------

//------------------------- 操作类型 --------------------------------

const OP_TYPE_READ			= 10005000;  //已阅
const OP_TYPE_HANDLE			= 10005001;  //已办理
const OP_TYPE_BUBBLE			= 10005002;  //气泡数
const OP_TYPE_BUBBLE_RECORD			= 10005003;  //记录气泡数
const OP_TYPE_BUBBLE_MODULE			= 10005004;  //模块气泡数
const OP_TYPE_UNREAD			= 10005005;  //未阅
const OP_TYPE_SEARCH_SETTING			= 10005006;  //最近搜索的高级搜索信息
const OP_TYPE_LISTDATA_READ			= 10005007;  //最近浏览的报表
const ACTION_SHOW_LIST			= 10143001;  //操作列表显示
const ACTION_SHOW_NONE			= 10143000;  //无显示操作
const SYS_OPTYPE_SYSUPGRADING			= 20276005;  //系统更新中
const ACTION_SCENES_LIST			= 10144001;  //操作使用场景列表
const ACTION_SCENES_INFO			= 10144002;  //操作使用场景详情页
const ACTION_SCENES_CARD			= 10144003;  //操作使用场景卡片式
const ACTION_EVENTTYPE_TRANSFERUSER			= 10142108;  //操作事件类型：转移负责人
const ACTION_EVENTTYPE_TRANSFERSTATE			= 10142109;  //操作事件类型： 变更状态
const ACTION_EVENTTYPE_RELAADD			= 10142110;  //操作事件类型，关联引用新建
const ACTION_EVENTTYPE_SHARE			= 10142111;  //操作事件类型，共享
const ACTION_EVENTTYPE_VIEW_SHARE			= 10142112;  //操作事件类型，查看共享
const ACTION_EVENTTYPE_DISTRIBUTE			= 10142113;  //操作事件类型，分发
const ACTION_EVENTTYPE_VIEW_DISTRIBUTE			= 10142114;  //操作事件类型，查看分发
const ACTION_EVENTTYPE_WORKFOLW_RETROVERSION			= 10142115;  //操作事件类型，流程，强制退回
const ACTION_EVENTTYPE_WORKFOLW_SKIP			= 10142116;  //操作事件类型，流程，跳过步骤
const ACTION_EVENTTYPE_WORKFOLW_FINISH			= 10142117;  //操作事件类型，流程，结束流程
const ACTION_EVENTTYPE_WORKFOLW_REPLACE			= 10142118;  //操作事件类型，流程，修改步骤
const ACTION_EVENTTYPE_AUDIT			= 10142119;  //操作事件类型，审核
const ACTION_EVENTTYPE_PROGRESS			= 10142120;  //操作事件类型，进度汇报
const ACTION_EVENTTYPE_TOP			= 10142121;  //操作事件类型，置顶
const ACTION_EVENTTYPE_CANCEL_TOP			= 10142122;  //操作事件类型，取消置顶
const ACTION_EVENTTYPE_ARCHIVE			= 10142123;  //操作事件类型，归档
const ACTION_EVENTTYPE_ISSUE			= 10142124;  //操作事件类型，发布（定时发布）
const ACTION_EVENTTYPE_REPEAL			= 10142125;  //操作事件，撤回草稿
const ACTION_EVENTTYPE_CANCEL_ARCHIVE			= 10142126;  //操作事件类型，取消归档
const OP_TYPE_SETTING			= 10005008;  //设置项类型
const ACTION_NOT_SHOW_PERMIT			= 10143002;  //不在报表权限出现的操作
const ACTION_TYPE_REMIND_PRESET			= 20176002;  //消息提醒预设操作类型
const SYS_OPTYPE_SYSRECOVERING			= '97280301406885114';	//系统恢复中
//------------------------- 操作类型 --------------------------------

//------------------------- 分类类型 --------------------------------

const PARAM_TYPE_TASK			= 20153008;  //任务类型
const PARAM_TYPE_LEVEL			= 20153009;  //任务等级
const PARAM_TYPE_NOT_PERMISSION			= 20153010;  //子任务无权发布
const PARAM_TYPE_PERMISSION			= 20153011;  //子任务有权发布
const PARAM_TYPE_FILETYPE			= 20456000;  //文件类型
const PARAM_TYPE_FILETYPE_WORD			= 20456001;  //word类型
const PARAM_TYPE_FILETYPE_EXCEL			= 20456002;  //excel类型
const PARAM_TYPE_FILETYPE_PPT			= 20456003;  //幻灯片类型
const PARAM_TYPE_FILETYPE_PDF			= 20456004;  //PDF类型
const PARAM_TYPE_FILETYPE_MUSIC			= 20456005;  //音乐类型
const PARAM_TYPE_FILETYPE_PIC			= 20456006;  //图片类型
const PARAM_TYPE_FILETYPE_COMPRESS			= 20456007;  //压缩类型
const PARAM_TYPE_FILETYPE_VIDEO			= 20456008;  //视频类型
const PARAM_TYPE_FILETYPE_OTHER			= 20456009;  //其他类型
const PARAM_TYPE_CATG			= 10004000;  //分类类型，各模块配可用
const PARAM_TYPE_OTHER			= 10004001;  //其他类型，各模块配可用
const PARAM_TYPE_WORKFLOW			= 10004002;  //流程类型
const PARAM_TYPE_WORKFLOW_FORMTAG			= 10004003;  //表单标记
const PARAM_TYPE_DEPTTYPE			= 10004004;  //部门类型
const PARAM_TYPE_MEETROOM			= 10004005;  //会议地点
const PARAM_TYPE_CUSTYPE			= 10004006;  //客户类型
const PARAM_TYPE_SRC			= 10004007;  //客户来源
const PARAM_TYPE_GRADE			= 10004008;  //客户级别
const PARAM_TYPE_STAGE			= 10004009;  //合作阶段
const PARAM_TYPE_APICATG			= 10004020;  //接口类型
const PARAM_TYPE_ENUMCATG			= 10004021;  //常量类型
const PARAM_TYPE_INDUSTRY			= 10004010;  //所属行业
const PARAM_TYPE_FORM			= 10004011;  //表单类型
const PARAM_TYPE_AREA			= 10004014;  //地区
const PARAM_TYPE_DIMISSION			= 10004015;  //离职原因（系统管理模块）
const PARAM_TYPE_RETIRE			= 10004016;  //退休原因（系统管理模块）
const PARAM_TYPE_DEPTTYPE_DEPT			= 10004017;  //部门类型(机构属性)中的部门
const PARAM_TYPE_DEPTTYPE_NOTASSIGNED			= 10004022;  //未分配部门类型和ID
const PARAM_TYPE_PERFASSESS			= 10004023;  //绩效考核类型
const PARAM_TYPE_STAGE_TYPE_COURSE			= 10004018;  //合作阶段中过程分类
const PARAM_TYPE_STAGE_TYPE_RESULT			= 10004019;  //合作阶段中结果分类
const PARAM_TYPE_UPDATECATG			= 10004024;  //更新管理类型
const PARAM_TYPE_GENDER			= 10004025;  //性别类型
const PARAM_TYPE_GENDER_FEMALE			= 10004026;  //性别女
const PARAM_TYPE_GENDER_MALE			= 10004027;  //性别男
const PARAM_TYPE_GENDER_UNKNOW			= 10004028;  //性别未知
const PARAM_TYPE_CONTACTOR_TYPE			= 10004029;  //联系人类型
const PARAM_TYPE_CONTRACT_PAYWAY			= 20653000;  //合同回款方式
const PARAM_TYPE_CONTRACT_INVOICETYPE			= 20653001;  //合同票据类型
const PARAM_TYPE_TAG			= 10151000;  //标签类型
const PARAM_TYPE_PERFOCHECK			= 10004030;  //考核指标
const PARAM_TYPE_PERFOCHECK_TYPE_OPPORTUNITY_AMOUNT			= 10004031;  //考核指标，机会金额
const PARAM_TYPE_PERFOCHECK_TYPE_OPPORTUNITY_COUNT			= 10004032;  //考核指标，机会数量
const PARAM_TYPE_PERFOCHECK_TYPE_CUSTOMER_COUNT			= 10004033;  //考核指标，客户数量
const PARAM_TYPE_PERFOCHECK_TYPE_CONTRACT_AMOUNT			= 10004034;  //考核指标，合同总金额
const PARAM_TYPE_PERFOCHECK_TYPE_PAY_AMOUNT			= 10004035;  //考核指标，已回款金额
const PARAM_TYPE_ACTION_GROUP			= 10004036;  //操作组类型
const PARAM_TYPE_SAVEHANDLE_MSG			= 10004037;  //逻辑调用类型，发送消息或短信
const PARAM_TYPE_SAVEHANDLE_CALL_FUNC			= 10004038;  //逻辑调用类型，回调操作方法
const PARAM_TYPE_SAVEHANDLE_DATA_UPDATE			= 10004039;  //逻辑调用类型，数据回写
const PARAM_TYPE_SAVEHANDLE_RELAADD			= 10004040;  //逻辑调用类型，关联创建
const PARAM_TYPE_SAVEHANDLE_LOG			= 10004041;  //逻辑调用类型，系统日志
const PARAM_TYPE_APP_CODE			= 10004042;  //app代码类型
const PARAM_TYPE_APP_SQL			= 10004043;  //app数据库脚本类型
const PARAM_TYPE_APP_VER			= 10004044;  //app版本类型
const PARAM_TYPE_NO_AUDIT			= 10004045;  //审核未通过
const PARAM_TYPE_ALREADY_AUDIT			= 10004046;  //已审核通过
const PARAM_TYPE_ARCHIVE			= 10004047;  //归档状态分类
const PARAM_TYPE_TOP			= 10004048;  //置顶分类
const PARAM_TYPE_APP_FILELIST			= 10004049;  //app文件清单类型
const PARAM_TYPE_POSNTYPE_NOTASSIGNED			= 10004050;  //未分配职务类型和ID
const PARAM_TYPE_SYS_FORMTAG			= 10004051;  //系统级标签
const PARAM_TYPE_POSN_NOTCLASSIFIED			= 10004052;  //职务类别--未分类
const PARAM_TYPE_COMMON_SETTING_GROUP			= 10004053;  //高级设置分组类别
const PARAM_TYPE_COMMON_SETTING_ITEM			= 10004055;  //企业设置高级设置项
const PARAM_TYPE_COMMON_SETTING_SOBGROUP			= 10004054;  //企业设置套餐高级设置分组类别
const PARAM_TYPE_APP_CODEPACKAGE			= 10004056;  //代码包类型
const PARAM_TYPE_DEPTLEVEL			= 10004057;  //部门层级类型
const PARAM_TYPE_DEPTGROUP			= 10004058;  //部门分组类型
const PARAM_TYPE_TRANSFER_REPORT			= 10004059;  //计划汇报迁移类型
const PARAM_TYPE_READ_PERMISSION			= 20153013;  //任务有无权查看
const PARAM_TYPE_SUBORDINATE_PERMISSION			= 20153014;  //直接或间接下属有无权查看
const PARAM_TYPE_REMIND_PRESET			= 20170602;  //提醒消息预设类型
const PARAM_PASSWORD_MODE_NORMAL			= 10004060;  //普通密码模式
const PARAM_PASSWORD_MODE_COMPLEX			= 10004061;  //复杂密码模式
const OP_TYPE_NEWS_AUDIT_SETTING			= 10004062;  //公告审核配置
const OP_TYPE_NEWS_BRUN_SETTING			= 10004063;  //公告焚毁记录
const PARAM_TYPE_MODULE_ICON			= 21082000;  //模块图标
const PARAM_TYPE_MENU_ICON			= 21082001;  //菜单图标
//------------------------- 分类类型 --------------------------------

//------------------------- 日期类型格式 --------------------------------

const DATETIME_TYPE_NONE			= 10002001;  //日期
const DATETIME_TYPE_DATE			= 10002002;  //日期
const DATETIME_TYPE_TIMESTAMP			= 10002003;  //日期
const DATETIME_TYPE_DIFF_SECOND			= 10002004;  //日期
const DATETIME_TYPE_DIFF_MINUTE			= 10002005;  //日期
const DATETIME_TYPE_DIFF_HOUR			= 10002006;  //日期
const DATETIME_TYPE_DIFF_DAY			= 10002007;  //日期
const DATETIME_FORMAT_ZERO_NONE			= 10002010;  //获取当前日期和时分秒
const DATETIME_FORMAT_ZERO_SECOND			= 10002011;  //获取当前日期和时分且秒为零
const DATETIME_FORMAT_ZERO_MINUTE			= 10002012;  //获取当前日期和小时且分秒为零
const DATETIME_FORMAT_ZERO_HOUR			= 10002013;  //获取当前日期且时分秒为零
const DATETIME_FORMAT_FIRST_DAY			= 10002014;  //获取第一天且时分秒为零
const DATETIME_FORMAT_MONTH_DAY_1			= 10002015;  //获取当月第一天且时分秒为零，如2014-6-1 0:0:0
const DATETIME_FORMAT_DAY_FIRST			= 10002016;  //获取当天，时分秒为零 0:0:0
const DATETIME_FORMAT_DAY_END			= 10002017;  //获取当天，23:59:59
const DATETIME_FORMAT_DATE_ONLY			= 10002018;  //获取当天日期，如2015-3-11
const DATETIME_FORMAT_NO_SECOND			= 10002019;  //获取日期时分，不包括秒如2015-3-11 10:10
const DATETIME_FORMAT_YEAR_FIRST_DAY			= 10002020;  //获取当前年份的第一天
const DATETIME_FORMAT_YEAR_LAST_DAY			= 10002021;  //获取当前年份的最后一天
const DATETIME_FORMAT_YEAR_MONTH			= 10002022;  //获取当前年和月
//------------------------- 日期类型格式 --------------------------------

//------------------------- 返回类型值 --------------------------------

const PAGE_RET_MSG_TYPE_NONE			= 10001200;  //返回类型，无
const PAGE_RET_MSG_TYPE_PAGE			= 10001201;  //返回类型，普通页面请求
const PAGE_RET_MSG_TYPE_AJAX			= 10001202;  //返回类型，页面Ajax请求
const PAGE_RET_MSG_TYPE_MOBILE			= 10001203;  //返回类型，Mobile请求
const PAGE_RET_MSG_TYPE_STRING			= 10001204;  //字符串类型
const PAGE_RET_MSG_TYPE_CONSOLE			= 10001205;  //控制台类型
const PAGE_RET_MSG_TYPE_HTML			= 10001206;  //手机页面类型
const PAGE_RET_MSG_TYPE_WHOLEPAGE			= 10001207;  //返回类型，全页面请求
//------------------------- 返回类型值 --------------------------------

//------------------------- 通用的是否类型值 --------------------------------

const COM_TYPE_YES			= 1;  //是
const COM_TYPE_NO			= 0;  //否
const COM_DATATYPE_ALL			= 999;  //全部数据(适合各类型数据)
const COM_DATATYPE_NONE			= 111;  //无数据，类似null(适合各类型数据)
const MODULE_TYPE_NORMAL			= 10001001;  //模块类型： 普通模块
const MODULE_TYPE_PARAM			= 10001002;  //模块类型： 参数
const MODULE_BIND_TYPE_NONE			= 10001100;  //绑定类型： OA自有模块
const MODULE_BIND_TYPE_WEIXIN			= 10001103;  //绑定类型： 微信
const CHECK_PARAM_ALL			= 10001300;  //检查全部并返回每个检查字段的全部错误
const ACTION_TYPE_PERMIT			= 10142101;  //操作类型： 权限
const ACTION_TYPE_LOG			= 10142102;  //操作类型： 日志
const ACTION_TYPE_PERMIT_APP			= 10142103;  //操作类型： 模块权限定义
const ACTION_TYPE_PERMIT_CHAR			= 10142104;  //操作类型： 模块权限角色定义
const ACTION_EVENTTYPE_RET			= 10142105;  //操作事件类型： 跳转
const ACTION_EVENTTYPE_MSG			= 10142106;  //操作事件类型： 发系统消息
const ACTION_EVENTTYPE_CUSTOM			= 10142107;  //操作事件类型： 自定义方法
const SYS_LVL_BAS			= 10002210;  //系统级别：基础级
const SYS_LVL_SYS			= 10002220;  //系统级别：系统级
const SYS_LVL_APP			= 10002230;  //系统级别：APP级
const SYS_LVL_ENT			= 10002240;  //系统级别：企业级
const SYS_LVL_USR			= 10002250;  //系统级别：用户级
const GROUP_TYPE_NONE			= 0;  //组合类型，无
const GROUP_TYPE_ON			= 10002301;  //组合类型，组合控件、组合item等
const SQL_FLD_ADD_ENT_NONE			= 10002400;  //SQL添加字段：不额外添加entid
const SQL_FLD_ADD_ENT_NORMAL			= 10002401;  //SQL添加字段：添加当前企业ID和公共企业ID
const SQL_FLD_ADD_STATE_NONE			= 10002403;  //SQL添加字段：不额外添加state
const SQL_FLD_ADD_STATE_NORMAL			= 10002404;  //SQL添加字段：添加state=EnumClass::STATE_TYPE_NORMAL
const SQL_FLD_CHECK_DB_NONE			= 10002405;  //从数据库定义检查SQL表结构：不检查
const SQL_FLD_CHECK_DB_NORMAL			= 10002406;  //从数据库定义检查SQL表结构：检查
const SQL_FLD_PK_NONE			= 0;  //主键: 不是主键
const SQL_FLD_PK_NORMAL			= 10002421;  //主键: 是主键
const SQL_FLD_RET_NONE			= 0;  //新增后不返回该字段
const SQL_FLD_RET_NORMAL			= 10002425;  //新增后返回该字段
const SQL_RUN_SINGLE_NONE			= 10002407;  //运行单条SQL语句: 不运行
const SQL_RUN_SINGLE_NORMAL			= 10002408;  //运行单条SQL语句: 运行
const LOG_TYPE_NORMAL			= 10003001;  //日志
const LOG_TYPE_VAR_DUMP			= 10003002;  //日志
const SYS_SYNC_STATE_START			= 10006001;  //开始更新
const SYS_SYNC_STATE_UPDATING			= 10006002;  //更新中
const SYS_SYNC_STATE_DONE			= 10006003;  //更新完毕
const SYS_SYNC_TYPE_TABLE			= 10006201;  //更新表
const SYS_SYNC_TYPE_USER			= 10006202;  //更新人员
const SYS_SYNC_OPTYPE_TABLENAME			= 10006500;  //当前更新的表名记录
const SYS_SYNC_OPTYPE_STEP			= 10006501;  //当前更新的步骤记录
const SYS_SYNC_OPTYPE_LASTUPTIME			= 10006502;  //最后更新记录的时间
const SYS_SYNC_OPTYPE_LASTID			= 10006503;  //最后更新的记录id
const SYS_SYNC_OPTYPE_VERSION			= 10006504;  //更新对应的版本号
const SYS_SYNC_OPTYPE_STATE			= 10006505;  //当前更新操作的状态
const PLATFORM_TYPE_SYS			= 0;  //平台
const PLATFORM_TYPE_WEB			= 1;  //平台
const CHECK_PARAM_SKIP			= 10001301;  //检查到有一个错误就返回
const MODULE_TYPE_OA			= 10001003;  //模块类型：OA
const MODULE_TYPE_CRM			= 10001004;  //模块类型：CRM
const SYS_SYNC_STATE_WAIT			= 10006004;  //等待更新
const SYS_SYNC_STATE_GETCONFIG			= 10006005;  //只同步配置
const SYS_SYNC_STATE_GETCODE			= 10006006;  //只同步代码
const SYS_SYNC_STATE_GETSQL			= 10006007;  //只更新脚本
const PUBLIC_TYPE_GUIDE_SETTING			= 20001024;  //引导页个人设置
const SYS_FIELD_NOT_UPDATE			= 30001000;  //字段不更新
const PERSONAL_SIGNPWD_SWITCH			= 20451000;  //手势密码开关
//------------------------- 通用的是否类型值 --------------------------------

//------------------------- 终端类型 --------------------------------

const TERMINAL_TYPE_WEB			= 1000;  //网站来源
const TERMINAL_TYPE_MOBILE			= 1001;  //不清楚终端类型的手机来源
const TERMINAL_TYPE_PAD			= 1002;  //不清楚终端类型的平板来源
const TERMINAL_TYPE_MOBILE_ANDROID			= 1003;  //安卓手机来源
const TERMINAL_TYPE_MOBILE_IPHONE			= 1004;  //iPhone手机来源
const TERMINAL_TYPE_MOBILE_WINDOWS			= 1005;  //window手机来源
const TERMINAL_TYPE_PAD_ANDROID			= 1006;  //安卓平板来源
const TERMINAL_TYPE_PAD_IPAD			= 1007;  //iPad平板来源
const TERMINAL_TYPE_PAD_WINDOWS			= 1008;  //window平板来源
const TERMINAL_TYPE_TV_ANDROID			= 1009;  //安卓电视来源
const TERMINAL_TYPE_TV_IOS			= 1010;  //苹果电视来源
const TERMINAL_TYPE_IM			= 1011;  //不清楚终端类型的IM客户端
const TERMINAL_TYPE_CONSOLE			= 1012;  //控制台
const TERMINAL_TYPE_H5			= 1017;  //H5
//------------------------- 终端类型 --------------------------------

//------------------------- 状态 --------------------------------

const STATE_TYPE_INVALID			= 1000;  //无效状态
const STATE_TYPE_DELETE			= 1001;  //删除状态
const STATE_TYPE_LOCK			= 1011;  //锁定状态
const STATE_TYPE_HIDDEN			= 1015;  //不显示状态
const STATE_TYPE_HISTORY			= 1016;  //历史版本状态
const STATE_TYPE_CHECKED			= 1021;  //审核状态
const STATE_TYPE_DRAFT			= 1031;  //草稿
const STATE_TYPE_RECYCLING			= 1041;  //回收状态
const STATE_TYPE_NORMAL			= 1101;  //正常
const STATE_TYPE_RUNNING			= 1102;  //进行中
const STATE_TYPE_SUSPEND			= 1103;  //暂停
const STATE_TYPE_APPLYVERIFY			= 1104;  //申请审核
const STATE_TYPE_VERIFYING			= 1105;  //审核中
const STATE_TYPE_NOTPASS			= 1106;  //审核未通过
const STATE_TYPE_SCHEDULED			= 1107;  //定时执行
const STATE_TYPE_ARCHIVE			= 1108;  //归档
const STATE_TYPE_HALT			= 1109;  //停用
const STATE_TYPE_COMPLETE			= 1201;  //完成
const STATE_TYPE_ABSDELETE			= 1010;  //彻底删除状态
const STATE_TYPE_NOCHECK			= 1022;  //无需审核
//------------------------- 状态 --------------------------------

//------------------------- 投票 --------------------------------

const VOTE_SELECTTYPE_ANON			= 20411000;  //匿名投票
const VOTE_SELECTTYPE_OPEN_ALWAYS_RESULT			= 20411001;  //始终公开结果
const VOTE_SELECTTYPE_OPEN_AFTER_RESULT			= 20411002;  //投票结束后公开结果
const VOTE_SELECTTYPE_OPEN_ALWAYS_RECORD			= 20411003;  //始终公开参与人投票记录
const VOTE_SELECTTYPE_OPEN_AFTER_RECORD			= 20411004;  //投票结束后公开参与人投票记录
const VOTE_STATE_CAN			= 20412000;  //可投票状态
const VOTE_STATE_CANNOT			= 20412001;  //已投票状态
const VOTE_STATE_NOPERMIT			= 20412002;  //无权投票状态
const VOTE_STATE_NORMAL			= 20413000;  //正常投票
const VOTE_STATE_ENDING			= 20413001;  //结束投票
//------------------------- 投票 --------------------------------

//------------------------- 数据源类型 --------------------------------

const DATASOURCE_USERTYPE_USER			= 20381000;  //人员不带部门职位
const DATASOURCE_USERTYPE_DEPT			= 20381001;  //部门
const DATASOURCE_USERTYPE_POSN			= 20381002;  //职位
const DATASOURCE_USERTYPE_ROLE			= 20381003;  //角色
const DATASOURCE_USERTYPE_RULE			= 20381004;  //规则
const DATASOURCE_USERTYPE_UDP			= 20381005;  //人员带部门职位
const DATASOURCE_USERTYPE_UDPR			= 20381006;  //人员带部门职位角色
const DATASOURCE_RANGETYPE_USER_ALL			= 20381007;  //全部人
const DATASOURCE_RANGETYPE_USER_NOWALL			= 20381008;  //现有全部人
const DATASOURCE_RANGETYPE_USER_INDEPT			= 20381009;  //同部门人员
const DATASOURCE_RULETYPE_MYSELF			= 20381010;  //自己
const DATASOURCE_RULETYPE_DEPT_ALL			= 20381011;  //所有部门
const DATASOURCE_RULETYPE_DEPT_INCHARGE			= 20381012;  //所属主管部门
const DATASOURCE_RULETYPE_DEPT_DIRECTUNDER			= 20381013;  //所在直管组织机构(部门类型不限)
const DATASOURCE_RULETYPE_DEPT_DIRECTHEAD			= 20381014;  //直接上级组织机构(部门类型不限)
const DATASOURCE_RULETYPE_DEPT_DIRECTSUB			= 20381015;  //直接下级组织机构(部门类型不限)
const DATASOURCE_RULETYPE_POSN_DIRECTHEAD			= 20381016;  //直接上司
const DATASOURCE_RULETYPE_POSN_DIRECTSUB			= 20381017;  //直接下属
const DATASOURCE_RULETYPE_POSN_LEADER			= 20381018;  //部门领导(部门中职位最大)
const DATASOURCE_RULETYPE_POSN_DEPUTYLEADER			= 20381019;  //部门副领导
const DATASOURCE_RULETYPE_POSN_ALL			= 20381020;  //全部职位
const DATASOURCE_SELECTTYPE_USER			= 20381030;  //是否支持选人
const DATASOURCE_SELECTTYPE_DEPT			= 20381031;  //是否支持选部门
const DATASOURCE_SELECTTYPE_POSN			= 20381032;  //是否支持选职位
const DATASOURCE_SELECTTYPE_ROLE			= 20381033;  //是否支持选岗位
const DATASOURCE_SELECTTYPE_RULE			= 20381034;  //是否支持选规则
const DATASOURCE_SELECTTYPE_SEPARATE			= 20381035;  //不同岗位的同一个人员是否分开选择
const DATASOURCE_SELECTTYPE_NOACCOUNT			= 20381036;  //只显示无账号的人员
const DATASOURCE_SELECTTYPE_LEAVE			= 20381037;  //只显示离职的人员
const DATASOURCE_SELECTTYPE_REMOVEME			= 20381038;  //是否剔除自己
const DATASOURCE_SELECTTYPE_SHORTCUT			= 20381039;  //是否需要快捷选项
const DATASOURCE_SELECTTYPE_NOLIST			= 20381040;  //是否无需数据列表
const DATASOURCE_SELECTTYPE_APPLY			= 20381041;  //是否申请人
const DATASOURCE_SELECTTYPE_HASACCOUNT			= 20381042;  //只显示有账号的人员
const DATASOURCE_SELECTTYPE_ALLACCOUNT			= 20381043;  //有无账号的人员都显示
const DATASOURCE_SELECTTYPE_STAFF			= 20381044;  //只显示在职的人员
const DATASOURCE_SELECTTYPE_STAFFORNOT			= 20381045;  //在职离职的人员都显示
const DATASOURCE_SELECTTYPE_SUBLEVEL			= 20381046;  //树状部门时获取该部门下所有部门的人员
const DATASOURCE_RANGETYPE_USER_NO_CHG			= 20380000;  //保存时，人员未改变
const DATASOURCE_SELECTTYPE_STRUCT			= 20381047;  //组织架构
const DATASOURCE_OPTYPE_CUSTOMGROUP			= 20381048;  //选人自定义分组
const DATASOURCE_CTRLTYPE_STAFFPERMIT			= 20381049;  //是否受人员查看权限限制
const DATASOURCE_SELECTTYPE_HISTORYRECORD			= 20381050;  //选人控件历史记录
const DATASOURCE_OPTYPE_ATTENTION			= 20381051;  //默认关注的人
const DATASOURCE_POSNTYPE_CATE			= 20381052;  //职务分类类别
const DATASOURCE_POSNTYPE_POSITION			= 20381053;  //职务类型
const DATASOURCE_SELECTTYPE_SPEDATASR			= 20381054;  //选人指定数据源(选人范围)
const DATASOURCE_DATAVAL_EMPTY			= 20381055;  //数据值为空，给控件赋空值（使用此值时，“默认为最近选择”开关不起作用）
const DATASOURCE_USERTYPE_DEGP			= 20381056;  //部门分组
const DATASOURCE_SELECTTYPE_DEGP			= 20381057;  //是否支持选部门分组
//------------------------- 数据源类型 --------------------------------

//------------------------- 流程 --------------------------------

const WORKFLOW_TYPE_FREEFLOW			= 20133000;  //自由流程
const WORKFLOW_TYPE_CUSTOM			= 20133001;  //定制
const WORKFLOW_TYPE_FORM			= 20133002;  //表单
const WORKFLOW_TYPE_PRIVATE			= 20133003;  //私有
const WORKFLOW_CASE_STATE_DRAFT			= 0;  //草稿
const WORKFLOW_CASE_STATE_EDIT			= 1;  //编辑/办理中
const WORKFLOW_CASE_STATE_END			= 2;  //结束/已办结
const WORKFLOW_CASE_STATE_CLOSE			= 3;  //关闭/撤销
const WORKFLOW_CASE_STATE_NOFLOW			= 4;  //不绑流程提交
const WORKFLOW_RULE_TYPE_STEPMARK			= 20133101;  //规则类型：指定步骤标签
const WORKFLOW_SET_TYPE_TIMESORT			= 20133201;  //意见排序：按签批时间
const WORKFLOW_SET_TYPE_POSNSORT			= 20133202;  //意见排序：按职务大小
const WORKFLOW_SET_TYPE_SHOWNAME			= 20133301;  //显示内容：姓名
const WORKFLOW_SET_TYPE_SHOWDEPT			= 20133302;  //显示内容：部门
const WORKFLOW_SET_TYPE_SHOWPOSN			= 20133303;  //显示内容：职位
const WORKFLOW_SET_TYPE_SHOWVIEW			= 20133304;  //显示内容：办理意见
const WORKFLOW_SET_TYPE_SHOWSIGN			= 20133305;  //显示内容：手签图片
const WORKFLOW_SET_TYPE_SHOWATTCH			= 20133306;  //显示内容：相关附件
const WORKFLOW_SET_TYPE_SHOWTIME			= 20133307;  //显示内容：办理时间
//------------------------- 流程 --------------------------------

//------------------------- 审批 --------------------------------

const PROCESS_STATE_DOING			= 20021000;  //编辑/办理中
const PROCESS_STATE_END			= 20021001;  //结束/已办结
const PROCESS_STATE_CLOSE			= 20021002;  //关闭/撤销
const PROCESS_STATE_WAIT_TO_DO			= 20021003;  //待办理
const PROCESS_STATE_ALREADY_DONE			= 20021004;  //已办理
const PROCESS_STATE_NO_NEED			= 20021005;  //无需办理
//------------------------- 审批 --------------------------------

//------------------------- 员工 --------------------------------

const SYS_EMP_STATE_NORMAL			= 20273000;  //正常状态
const SYS_EMP_STATE_LEAVE			= 20273001;  //离职
const SYS_EMP_STATE_DELETE			= 20273002;  //删除
const SYS_EMP_STATE_REMOVE			= 20273003;  //彻底删除
const SYS_USER_FILTER_OFFLINE			= 20273100;  //离线
const SYS_USER_FILTER_ONLINE			= 20273101;  //在线
const SYS_ACCOUNT_STATE_INVITE			= 20273004;  //邀请加入单位
const SYS_ACCOUNT_STATE_BAN			= 20273005;  //账号不能登录
const SYS_ACCOUNT_STATE_DEL			= 20273006;  //只删除账号
const SYS_ACCOUNT_STATE_HAS			= 20273009;  //有账号
const SYS_ACCOUNT_STATE_NON			= 20273010;  //无账号
const SYS_ACCOUNT_STATE_REJECT			= 20273011;  //拒绝加入单位
const SYS_EMP_STATE_RETIRE			= 20273008;  //离休
const SYS_EMP_STATE_HONORABLE			= 20273007;  //退休
const SYS_JOB_STATE_DELETE			= 20273012;  //部门职位变动
const SYS_USER_AVATAR_UPLOADED			= 20273050;  //上传了个人头像 user_account.avatarid
const SYS_EMP_PHOTO_UPLOADED			= 20273051;  //上传了档案照片 profile.photoid
const SYS_UPTIME_TYPE_EMP			= 20273500;  //最后更新时间类型
const SYS_UPTIME_TYPE_DEPT			= 20273501;  //最后更新时间类型
const SYS_UPTIME_TYPE_POSN			= 20273502;  //最后更新时间类型
const SYS_UPTIME_TYPE_RELATION			= 20273503;  //最后更新时间类型
const SYS_USER_REG_TYPE_ACTIVE			= 20274000;  //主动注册
const SYS_USER_REG_TYPE_PASSIVE			= 20274001;  //被动注册
const SYS_USER_TYPE_ENTERPRISE			= 20274100;  //企业用户
const SYS_USER_TYPE_PERSONAL			= 20274101;  //个人用户
const SYS_USER_TARGET_TYPE_EMPLOYEE			= 20274200;  //员工
const SYS_USER_TARGET_TYPE_CUSTOMER			= 20274300;  //客户
const SYS_PERMIT_ACTIONTYPE_EDIT_USERPWD			= 20275000;  //修改账号密码
const SYS_PERMIT_ACTIONTYPE_ADMIN_ORG			= 20275001;  //管理组织架构
const SYS_PERMIT_ACTIONTYPE_ADMIN_EMP			= 20275002;  //管理员工档案
const SYS_OPTYPE_AUTONO			= 20276000;  //自动编号类型
const SYS_OPTYPE_PHONEUSE			= 20276001;  //电话使用权限
const SYS_OPTYPE_INITPWD			= 20276002;  //企业初始密码
const SYS_OPTYPE_GLOBALTHEME			= 20276003;  //整站界面风格
const SYS_OPTYPE_SIDEBARSORT			= 20276004;  //侧边栏排序方案
const SYS_ACCOUNT_STATE_NO_LOGIN			= 20273013;  //未登录过
const SYS_ACCOUNT_STATE_NO_DEPT			= 20273014;  //未设置部门
const SYS_ACCOUNT_STATE_NO_POSN			= 20273015;  //未设置职务
const SYS_ACCOUNT_STATE_NO_LEADER			= 20273016;  //未设置上司
const SYS_ACCOUNT_STATE_NO_HEAD			= 20273017;  //未设置部门负责人
const SYS_OPERATION_TYPE			= 20273018;  //操作类型
const SYS_EMP_STATE_WAIT			= 20273022;  //待入职状态
const SYS_WORT_TIME			= 20273019;  //上班时间设置
const SYS_APPLY_STATE_NOCHECK			= 20273023;  //帐号待审核
const SYS_APPLY_STATE_PASS			= 20273024;  //帐号已通过审核
const SYS_APPLY_STATE_REFUSE			= 20273025;  //帐号已拒绝
const SYS_ACCOUNT_STATE_NO_ATTENTION			= 20273026;  //未关注
const SYS_ACCOUNT_STATE_UNBIND			= 20273027;  //帐号已解绑
//------------------------- 员工 --------------------------------

//------------------------- 菜单类型 --------------------------------

const PREFERENCE_TYPE_NAVTAB			= 20351000;  //导航菜单项
const PREFERENCE_TYPE_ACTIONTAB			= 20351001;  //操作菜单项
const PREFERENCE_TYPE_DESKTOP			= 20351003;  //桌面菜单项
const PREFERENCE_TYPE_DATASOURCE			= 20351004;  //数据源控件用
const PREFERENCE_TYPE_RIGTH			= 20351005;  //详情页右侧列表
const PREFERENCE_TYPE_CUSTOMTAB			= 20351006;  //自定义菜单项
const PREFERENCE_TYPE_MAINLISTDATA			= 20351007;  //主报表
//------------------------- 菜单类型 --------------------------------

//------------------------- 登录类型 --------------------------------

const LOGIN_TYPE_USERNAME			= 20341000;  //用户名登录
const LOGIN_TYPE_MOBILE			= 20341001;  //手机登录
const LOGIN_TYPE_EMAIL			= 20341002;  //邮箱登录
const LOGIN_TYPE_ID			= 20341003;  //ID登录
const LOGIN_TYPE_OTHER			= 20341004;  //其他登录
const LOGIN_BIND_MOBILE			= 20341005;  //绑定手机
const LOGIN_BIND_EMAIL			= 20341006;  //绑定邮箱
const LOGIN_BIND_NOT			= 20341007;  //不做绑定
const LOGIN_VERIFY_FIRST			= 20342001;  //第一次登录
const LOGIN_VERIFY_EMAIL			= 20342002;  //有验证邮箱
const LOGIN_VERIFY_NOEMAIL			= 20342003;  //没有验证邮箱
const LOGIN_VERIFY_MUST_EMAIL			= 20342004;  //必须验证邮箱
const LOGIN_VERIFY_PHONE			= 20342005;  //有验证手机
const LOGIN_VERIFY_NOPHONE			= 20342006;  //没有验证手机
const LOGIN_VERIFY_MUST_PHONE			= 20342007;  //必须验证手机
const LOGIN_VERIFY_NOPWD			= 20342008;  //没有修改密码
const LOGIN_VERIFY_PWD			= 20342009;  //注册后修改过密码
const LOGIN_VERIFY_EDIT_INITPWD			= 20342010;  //修改初始密码
const LOGIN_TYPE_NO_REG			= 20341013;  //未注册账号
const LOGIN_TYPE_WECHAT_SUITE			= 20341011;  //微信套件登录
const LOGIN_TYPE_WECHAT_APP			= 20341012;  //微信定制应用登录
const LOGIN_VERIFY_MUST			= 12010001;  //需要Ukey验证
const LOGIN_VERIFY_NO_UKEY			= 12010002;  //不需要Ukey验证
const TP_UNIFIE_LOGIN_NOTPASSWORD			= 12010005;  //统一登录免密登录
const TP_UNIFIE_LOGIN_URLPARAM			= 12010006;  //统一登录url参数类型
const LOGIN_COMMON_MUST			= 12010010;  //允许普通登录
const LOGIN_COMMON_NO			= 12010007;  //不允许普通登录
const LOGIN_MESSAGE_MUST			= 12010008;  //允许短信登录
const LOGIN_MESSAGE_NO			= 12010009;  //不允许短信登录
const LOGIN_CERTIFICATE_MUST			= 12010011;  //允许手机证书登录
const LOGIN_CERTIFICATE_NO			= 12010012;  //不允许手机证书登录
//------------------------- 登录类型 --------------------------------

//------------------------- 公告操作类型 --------------------------------

const NEWS_OP_READ			= 20011000;  //阅读
const NEWS_OP_PUBLISH			= 20011001;  //发布
const NEWS_OP_EDIT			= 20011002;  //编辑
const NEWS_OP_DELETE			= 20011003;  //删除
const NEWS_OP_SETTOP			= 20011004;  //归档
const NEWS_OP_DRAFT			= 20011005;  //设为草稿
//------------------------- 公告操作类型 --------------------------------

//------------------------- 开会操作类型 --------------------------------

const MEETING_OP_READ			= 20171000;  //阅读
const MEETING_OP_PUBLISH			= 20171001;  //发布
const MEETING_OP_EDIT			= 20171002;  //编辑
const MEETING_OP_DELETE			= 20171003;  //删除
const MEETING_OP_SETTOP			= 20171004;  //归档
const MEETING_OP_DRAFT			= 20171005;  //设为草稿
const MEETING_TIMESTATE_YET_START			= 20171006;  //未开始
const MEETING_TIMESTATE_HAVING			= 20171007;  //正在开
const MEETING_TIMESTATE_END			= 20171008;  //已结束
//------------------------- 开会操作类型 --------------------------------

//------------------------- 电话会议 --------------------------------

const TELMEETING_STATE_NOTOPEN			= 20361000;  //未开通电话会议功能
const TELMEETING_STATE_OPEN			= 20361001;  //开通了电话会议功能
const TELMEETING_STATE_PERMIT			= 20361002;  //有权限发电话会议
const TELMEETING_STATE_NOTPERMIT			= 20361003;  //没有权限发电话会议
const TELMEETING_STATE_RUNNING			= 20361100;  //进行中
const TELMEETING_STATE_END			= 20361101;  //已结束
const TELMEETING_STATE_INIT			= 20361102;  //初始化
const TELMEETING_STATE_CONNECTED			= 20361103;  //连接中
const TELMEETING_STATE_RING			= 20361104;  //通话中
const TELMEETINGÎ_DISCONNECTED			= 20361105;  //已断开
const TELMEETING_USERSTATE_CONNECTED			= 20362000;  //已接通
const TELMEETING_USERSTATE_NOTCONNECT			= 20362001;  //未接通
const TELMEETING_USERTYPE_SILENCE			= 20363000;  //禁言
const TELMEETING_USERTYPE_RESOUND			= 20363001;  //恢复
const TELMEETING_USERTYPE_HANGUP			= 20363002;  //挂断
//------------------------- 电话会议 --------------------------------

//------------------------- 内部邮件操作类型 --------------------------------

const PMS_OP_READ			= 20283000;  //阅读
const PMS_OP_DELETE			= 20283001;  //删除
//------------------------- 内部邮件操作类型 --------------------------------

//------------------------- 今天 --------------------------------

const TODAY_TYPE_TODO			= 20373000;  //待办
const TODAY_TYPE_RUNNING			= 20373001;  //进行中
const TODAY_TYPE_DONE			= 20373002;  //完成
const TODAY_TYPE_AUTODONE			= 20373003;  //自动完成
const TODAY_OP_EXECUTE			= 20372000;  //执行
const TODAY_OP_VALIDATE			= 20372001;  //验收
const TODAY_TYPE_OVERDUE			= 20373004;  //逾期
//------------------------- 今天 --------------------------------

//------------------------- 数据表 --------------------------------

const TB_TYPE_ONEID			= 20403000;  //所有关联表相同ID
const TB_TYPE_WHERE_EQ			= 20403101;  //表关联的条件类型，相等
const TB_TYPE_WHERE_GT			= 20403102;  //表关联的条件类型，大于
const TB_TYPE_WHERE_GE			= 20403103;  //表关联的条件类型，大于等于
const TB_TYPE_WHERE_LT			= 20403104;  //表关联的条件类型，小于
const TB_TYPE_WHERE_LE			= 20403105;  //表关联的条件类型，小于等于
const TB_TYPE_VLAUE_NORMAL			= 20033201;  //表关联的值类型，普通，直接赋值
const TB_TYPE_VLAUE_TB			= 20033202;  //表关联的值类型，表的某个字段
//------------------------- 数据表 --------------------------------

//------------------------- 邮件 --------------------------------

const PMS_BOX_INBOX			= 20281000;  //收件箱
const PMS_BOX_OUTBOX			= 20281001;  //发件箱
const PMS_BOX_DRAFTS			= 20281002;  //草稿箱
const PMS_READ_YES			= 20282001;  //已阅
const PMS_READ_NO			= 20282002;  //未阅
const PMS_REPLY_ONLYME			= 20282003;  //回复仅我可见
//------------------------- 邮件 --------------------------------

//------------------------- 控件 --------------------------------

const COMPONENT_TYPE_NONE			= 20423000;  //控件类型
//------------------------- 控件 --------------------------------

//------------------------- 权限 --------------------------------

const PERMIT_GROUP_TYPE_BOSS			= 20433001;  //BOSS
const PERMIT_GROUP_TYPE_SUPER			= 20433002;  //超管
const PERMIT_GROUP_TYPE_ADMIN			= 20433003;  //管理员
const PERMIT_GROUP_TYPE_BASE			= 20433004;  //基本
const PERMIT_GROUP_TYPE_ALLUSER			= 20433005;  //全部人员
const PERMIT_GROUP_TYPE_CUSTOM			= 20433006;  //自定义
const PERMIT_TYPE_ACTION			= 20433100;  //功能操作权限
const PERMIT_TYPE_VISIBLE_DATA			= 20433101;  //人员查看权限
const PERMIT_ITEM_SETTYPE_DEFAULTON			= 20433200;  //默认选中
const PERMIT_ITEM_SETTYPE_HIGHLIGHT			= 20433201;  //是否注意
const PERMIT_ITEM_SETTYPE_DISABLE			= 20433202;  //是否禁用
const PERMIT_VIEWTYPE_ALLDEPT			= 20434000;  //所在部门人员
const PERMIT_VIEWTYPE_OFFICER			= 20434001;  //上级部门人员
const PERMIT_VIEWTYPE_DIRECT_OFFICER			= 20434002;  //仅直接上级
const PERMIT_VIEWTYPE_SUBDEPT			= 20434003;  //下级部门人员
const PERMIT_VIEWTYPE_DIRECT_SUBDEPT			= 20434004;  //仅直接下级
const PERMIT_VIEWTYPE_LEADER			= 20434005;  //上司
const PERMIT_VIEWTYPE_DIRECT_LEADER			= 20434006;  //仅直接上司
const PERMIT_VIEWTYPE_SUBORDINATE			= 20434007;  //下属
const PERMIT_VIEWTYPE_DIRECT_SUBUSER			= 20434008;  //仅直接下属
const PERMIT_VIEWTYPE_NODEPT			= 20434009;  //无部门人员
const PERMIT_VIEWTYPE_ALLUSER			= 20434010;  //全部人员
const PERMIT_ACTIONTYPE_ADD			= 20434300;  //添加
const PERMIT_ACTIONTYPE_VIEW			= 20434301;  //查看
const PERMIT_ACTIONTYPE_EDIT			= 20434302;  //编辑
const PERMIT_ACTIONTYPE_DEL			= 20434303;  //删除
const PERMIT_ACTIONTYPE_ADMIN			= 20434304;  //管理
const PERMIT_ACTIONTYPE_IMPORT			= 20434305;  //导入
const PERMIT_ACTIONTYPE_EXPORT			= 20434306;  //导出
const PERMIT_ACTIONTYPE_DRAFT			= 20434307;  //暂存草稿
const PERMIT_ACTIONTYPE_APPADMIN			= 20434308;  //应用管理员
const PERMIT_CHECKTYPE_MODULE			= 20434100;  //验证模块权限
const PERMIT_CHECKTYPE_ACTION			= 20434101;  //验证功能权限
const FORM_VER_TYPE_NORMAL			= 20031016;  //表单版本类型：创建新版本
const FORM_ITEM_TYPE_BUTTON			= 20033004;  //表单Item类型,按钮
const PERMIT_VALIDATION_TYPE_LIST			= 'list';	//列表页权限验证
const PERMIT_VALIDATION_TYPE_ADD			= 'add';	//新建权限验证
const PERMIT_VALIDATION_TYPE_ACTION			= 'action';	//操作权限验证
const PERMIT_ACTIONTYPE_REPCENTER			= 20434309;  //报表中心
const PERMIT_ACTIONTYPE_ABSDEL			= 20434310;  //彻底删除
const PERMIT_FORM_ALL			= -1;  //全部表单
const PERMIT_FORM_SETTING			= 20434312;  //表单设置
const PERMIT_WORKFLOW_SETTING			= 20434313;  //流程设置
const PERMIT_TAB_SETTING			= 20434314;  //选项卡设置
const PERMIT_FORM_DATA			= 20434315;  //表单数据管理
const PERMIT_LISTDATA_SETTING			= 20434316;  //报表设置
const PERMIT_PERMIT_SETTING			= 20434317;  //权限设置
const PERMIT_ACTIONTYPE_AUDIT			= 20434318;  //审核
const PERMIT_ACTIONTYPE_REPEAL			= 20434319;  //撤回
const PERMIT_ACTIONTYPE_PROGRESS			= 20434320;  //进度汇报
const PERMIT_ACTIONTYPE_TOP			= 20434321;  //置顶
const PERMIT_ACTIONTYPE_CANCEL_TOP			= 20434322;  //取消置顶
const PERMIT_ACTIONTYPE_ARCHIVE			= 20434323;  //归档
const PERMIT_ACTIONTYPE_ISSUE			= 20434324;  //发布（定时发布）
const PERMIT_ACTIONTYPE_CANCEL_ARCHIVE			= 20434325;  //取消归档
const PERMIT_TMPLET_SETTING			= 20434326;  //模板设置
const PERMIT_SIGNET_SETTING			= 20434327;  //印章设置
const PERMIT_GROUP_TYPE_WX			= 20433007;  //微信权限组
//------------------------- 权限 --------------------------------

//------------------------- 表单 --------------------------------

const FORM_TYPE_NONE			= 20031000;  //表单类型
const FORM_HIS_TYPE_NORMAL			= 20031011;  //表单历史版本类型：历史版本
const FORM_WF_TYPE_NORMAL			= 20031015;  //表单流程绑定类型：普通绑定模式
const FORM_TB_TYPE_SINGLE			= 20031021;  //表单数据库字段类型：单个数据库字段
const FORM_TB_TYPE_MULTI			= 20031022;  //表单数据库字段类型：多个数据库字段
const FORM_DATA_TYPE_NONE			= 20031100;  //数据类型，无
const FORM_DATA_TYPE_FORM			= 20031110;  //数据类型，表单
const FORM_DATA_TYPE_LIST			= 20031120;  //数据类型，列表
const FORM_DATA_TYPE_ALL			= 20031130;  //数据类型，综合
const FORM_PAGE_TYPE_NONE			= 20032000;  //表单界面类型
const FORM_PAGE_TYPE_SADD			= 20032070;  //表单界面类型，特殊新建如（转移负责人，追加人员）
const FORM_PAGE_TYPE_EDIT			= 20032020;  //表单界面类型，编辑页
const FORM_PAGE_TYPE_VIEW			= 20032030;  //表单界面类型，详情页
const FORM_PAGE_TYPE_LIST			= 20032040;  //表单界面类型，列表页
const FORM_PAGE_TYPE_DESKTOP			= 20032050;  //表单界面类型，桌面嵌入页
const FORM_PAGE_TYPE_TODAY			= 20032060;  //表单界面类型，今天嵌入页
const FORM_ITEM_TYPE_NONE			= 20033000;  //表单Item类型,不指定类型
const FORM_ITEM_TYPE_DISPLAY			= 20033001;  //表单Item类型,界面元素
const FORM_ITEM_TYPE_DATA			= 20033002;  //表单Item类型,数据，不显示
const FORM_ITEM_TYPE_COMPONENT			= 20033003;  //表单Item类型,控件
const FORM_ITEM_TYPE_BREADCRUMBS			= 20033005;  //表单Item类型,面包屑导航
const FORM_ITEM_TYPE_TAB			= 20033006;  //表单Item类型,Tab
const FORM_ITEM_TYPE_TAB_NAV			= 20033007;  //表单Item类型,导航菜单项
const FORM_ITEM_TYPE_TAB_OPERATE			= 20033008;  //表单Item类型,操作菜单项
const FORM_ITEM_TYPE_DESKTOP			= 20033009;  //表单Item类型,桌面菜单项
const FORM_ITEM_TYPE_DATASOURCE_NEWS_CATEG_PARAMS			= 20033041;  //表单Item类型,公告类型数据源
const FORM_ITEM_ATTRTYPE_SHOW			= 20033100;  //显示
const FORM_ITEM_ATTRTYPE_HIDE			= 20033101;  //隐藏
const FORM_ITEM_ATTRTYPE_READONLY			= 20033102;  //只读
const FORM_HIS_TYPE_NONE			= 0;  //表单历史版本类型：目前正在使用版本
const FORM_WF_TYPE_NONE			= 0;  //表单流程绑定类型：无绑定流程
const FORM_VER_TYPE_NONE			= 0;  //表单版本类型：不创建新版本
const FORM_TB_TYPE_NONE			= 0;  //表单数据库字段类型：无数据库字段
const FORM_DATA_TYPE_ACTION			= 20031140;  //数据类型，操作
const FORM_DATA_TYPE_PAGE			= 20031150;  //数据类型，页面
const FORM_PAGE_TYPE_CATEGLIST			= 20032080;  //表单界面类型，选类别
const FORM_PAGE_TYPE_ADD			= 20032010;  //表单界面类型，新建页
const FORM_TYPE_TABLE			= 20031001;  //表格类型
const DATA_RELA_PERMIT_NONE			= 20034010;  //表单设置数据引用参数，不支持
const DATA_RELA_PERMIT_ALL			= 20034011;  //表单设置数据引用参数，全部人可用
const DATA_RELA_PERMIT_SOME			= 20034012;  //表单设置数据引用参数，部分人可用
const TOOL_SET_NONE			= 20034020;  //表单设置小工具，不支持
const TOOL_SET_ALL			= 20034021;  //表单设置小工具，支持全部小工具
const TOOL_SET_SOME			= 20034022;  //表单设置小工具，支持部分小工具
const SAVE_PAGE_WAY_POP			= 20034030;  //新建页打开方式，弹窗
const SAVE_PAGE_WAY_PAGE			= 20034031;  //新建页打开方式，新页面
const REPLY_DECIDED_NONE			= 20034040;  //决定是否允许回复,不支持填单者决定
const REPLY_DECIDED_ABLE_AND_PUBLIC			= 20034041;  //决定是否允许回复,由填单者决定，并默认允许回复并公开回复
const REPLY_DECIDED_ABLE_NO_PUBLIC			= 20034042;  //决定是否允许回复,由填单者决定，默认允许回复但不公开回复
const REPLY_DECIDED_DISABLE			= 20034043;  //决定是否允许回复,由填单者决定，默认不允许回复
const ARCHIVE_NONE			= 20034050;  //自动归档，不支持
const ARCHIVE_30_DAYS			= 20034051;  //自动归档，默认30天后自动归档
const ARCHIVE_180_DAYS			= 20034052;  //自动归档，默认180天后自动归档
const ARCHIVE_DISABLE			= 20034053;  //自动归档，默认不自动归档
const SYSMSG_NONE			= 20034060;  //发送系统消息，不支持
const SYSMSG_DEF_SEND			= 20034061;  //发送系统消息，由填单者决定，默认发送消息
const SYSMSG_DEF_NOSEND			= 20034062;  //发送系统消息，由填单者决定，默认不发送消息
const MSG_SEND_ROLE_FORMITEM			= 20034070;  //系统消息通知角色，某表单角色
const MSG_SEND_ROLE_FORMITEM_DIRECTHEAD			= 20034071;  //系统消息通知角色，某表单角色直接上司
const MSG_SEND_ROLE_FORMITEM_INDIRECTHEAD			= 20034072;  //系统消息通知角色，某表单角色间接上司
const MSG_SEND_ROLE_FORMITEM_LEADER			= 20034073;  //系统消息通知角色，某表单角色部门领导
const MSG_SEND_RULE_NEW_USER			= 20034080;  //系统消息通知规则，新增加人员
const MSG_SEND_RULE_DEL_USER			= 20034081;  //系统消息通知规则，发给删除人员
const MSG_SEND_RULE_UNCHANGE_USER			= 20034082;  //系统消息通知规则，发给未变动人员
const MSG_SEND_RULE_UNREAD_USER			= 20034083;  //系统消息通知规则，发给未阅人员
const MSG_SEND_RULE_ALL_USER			= 20034084;  //系统消息通知规则，发给全部人员
const DATA_SOURCE_FORM_CURRENT			= 20034090;  //数据所属表单，当前数据
const DATA_SOURCE_FORM_CURRENT_PARENT			= 20034091;  //回写数据所属表单，当前数据所属数据（表单支持层级）
const DATA_SOURCE_FORM_CURRENT_FORM			= 20034092;  //回写数据所属表单，当前表单数据
const DATA_SOURCE_FORM_OTHER_FORM			= 20034093;  //回写数据所属表单，其它表单数据
const FORM_PAGE_TYPE_RELALIST			= 20032090;  //表单界面类型，客户端关联列表
const FORM_PAGE_TYPE_ISSUE_EDIT			= 20032070;  //表单界面类型，公文嵌入编辑页
const FORM_PAGE_TYPE_ISSUE_VIEW			= 20032071;  //表单界面类型，公文嵌入浏览页
const FORM_PAGE_TYPE_CUSTOM_SUBMIT			= 20032072;  //表单界面类型，特殊提交页
const FORM_PAGE_TYPE_PROCESS_H5_VIEW			= 20032073;  //申请审批H5详情页
const FORM_PAGE_TYPE_REPORT_H5_VIEW			= 20032074;  //新计划汇报H5详情页
const FORM_PAGE_TYPE_POP_RELA_SAVE			= 20032075;  //申请单弹窗关联其它模块的提交页面
const FORM_PAGE_TYPE_BUDGET_DEPT_VIEW			= 20032076;  //部门预算详情页
const FORM_PAGE_TYPE_BUDGET_FORM_VIEW			= 20032077;  //部门预算异步提交页
const FORM_PAGE_TYPE_BUDGET_FORM_EDIT			= 20032078;  //部门预算异步提交页
//------------------------- 表单 --------------------------------

//------------------------- 应用设置 --------------------------------

const APP_DISPLAY_WEB_LEFTMENU			= 20461000;  //显示于WEB左边菜单栏
const APP_DISPLAY_WEB_TOPMENU			= 20461001;  //显示于WEB顶部菜单栏
const APP_DISPLAY_WEB_PERMIT			= 20461002;  //显示于WEB权限设置
const APP_DISPLAY_MOBILE			= 20461003;  //显示于手机客户端
const APP_TYPE_STANDARD			= 20463000;  //标准应用
const APP_TYPE_TOOL			= 20463001;  //工具应用
const APP_TYPE_GROUP			= 20463002;  //应用分组
const APP_TYPE_ENTADMIN			= 20463003;  //企业管理应用
const APP_TYPE_SYSADMIN			= 20463004;  //系统管理应用
const APP_TYPE_CUSTOM			= 20463005;  //自定义APP应用
const APP_TYPE_BASE			= 20463006;  //基础应用
const APP_TYPE_PARTNER			= 20463007;  //伙伴应用
const APP_DISPLAY_MOBILE_NO_DESKTOP			= 20461004;  //不显示在手机客户端桌面
//------------------------- 应用设置 --------------------------------

//------------------------- 返回类型 --------------------------------

const RET_TYPE_DATA			= 3001;  //数据
const RET_TYPE_NOBTN			= 3002;  //无按钮弹窗
const RET_TYPE_WITHBTN			= 3003;  //有按钮弹窗
const RET_TYPE_URL			= 3004;  //跳转到url
const RET_TYPE_ACTION			= 3005;  //内置动作
const RET_TYPE_REFRESH			= 3006;  //页面列表刷新
const RET_TYPE_RELOAD			= 3007;  //当前页面刷新
const RET_TYPE_AJAX_PAGE			= 3008;  //弹窗加载页面
const RET_TYPE_RELOAD_NOAJAX			= 3009;  //当前页面刷新，非异步
const RET_TYPE_OPEN_URL			= 3010;  //新窗口打开url
const PAGE_RET_MSG_TYPE_PORTAL			= 10001208;  //门户类型页面
//------------------------- 返回类型 --------------------------------

//------------------------- 考勤 --------------------------------

const ATTEND_REST_TYPE_DOUBLE			= 20473000;  //双休
const ATTEND_REST_TYPE_SINGLE			= 20473001;  //单休
const ATTEND_REST_TYPE_ALTERNATE			= 20473002;  //长短周
const ATTEND_CLOCK_IN			= 20473050;  //上班打卡
const ATTEND_CLOCK_OUT			= 20473051;  //下班打卡
const ATTEND_LOTTERY_TYPE_DATA			= 20473100;  //流量
const ATTEND_LOTTERY_TYPE_MONEY			= 20473101;  //话费
const ATTEND_SETTYPE_SMS			= 20473200;  //短信内容
//------------------------- 考勤 --------------------------------

//------------------------- 文件柜 --------------------------------

const DOCUMENT_TYPE_FILE			= 20493000;  //文件
const DOCUMENT_TYPE_FOLDER			= 20493001;  //文件夹
const DOCUMENT_ATTRTYPE_ENTERPRISE			= 20493100;  //单位
const DOCUMENT_ATTRTYPE_PERSON			= 20493101;  //个人
const DOCUMENT_ATTRTYPE_MY_SHARE			= 20493102;  //我的共享
const DOCUMENT_ATTRTYPE_COL_SHARE			= 20493103;  //同事共享
const DOCUMENT_PERMITTYPE_BROWSE			= 20494000;  //在线浏览权限
const DOCUMENT_PERMITTYPE_WRITE			= 20494001;  //上传、写入权限
const DOCUMENT_PERMITTYPE_UPLOAD			= 20494002;  //上传文件、新建文件夹
const DOCUMENT_PERMITTYPE_RENAME			= 20494003;  //重命名
const DOCUMENT_PERMITTYPE_MOVE			= 20494004;  //移动文件或文件夹到当前目录
const DOCUMENT_PERMITTYPE_DOWNLOAD			= 20494005;  //下载权限
const DOCUMENT_PERMITTYPE_DEL			= 20494006;  //删除权限
const DOCUMENT_PERMITTYPE_ADMIN			= 20494007;  //管理权限
const DOCUMENT_PERMIT_ACTIONTYPE_PERSONAL			= 20495000;  //个人文件柜
//------------------------- 文件柜 --------------------------------

//------------------------- 桌面 --------------------------------

const DESKTOP_TYPE_LIST			= 20483000;  //列表
const DESKTOP_TYPE_STATISTIC			= 20483001;  //统计
const DESKTOP_TYPE_SHORTCUT			= 20483002;  //快捷方式
const DESKTOP_TYPE_CUSTOM			= 20483003;  //自定义
const DESKTOP_TYPE_CHART			= 20483004;  //图表类型
const DESKTOP_TYPE_NEWS_SPECIAL			= 20483005;  //桌面公告特定企业显示
const DESKTOP_TYPE_CONTAINER			= 20483006;  //容器小部件 lwh 20170802
const DESKTOP_DATATYPE_TAB			= 20483101;  //桌面tab记录 lwh 20170803
const DESKTOP_DATATYPE_SETTING			= 20483102;  //桌面tab设置 lwh 20170803
const DESKTOP_WIDGET_TYPE			= 20483201;  //小部件分类
//------------------------- 桌面 --------------------------------

//------------------------- 笔记 --------------------------------

const NOTES_SETTYPE_AUTOSAVE			= 20513000;  //自动保存
const NOTES_SETTYPE_PUBLIC			= 20513001;  //公开
const NOTES_SETTYPE_PRIVATE			= 20513002;  //私有
//------------------------- 笔记 --------------------------------

//------------------------- 提示 --------------------------------

const TIPS_THEME_DEFAULT			= 20501000;  //默认
const TIPS_THEME_WARN			= 20501001;  //警告
const TERMINAL_TYPE			= '77776666666';	//kkjjjjjjjjjj
//------------------------- 提示 --------------------------------

//------------------------- 通道 --------------------------------

const PASSAGE_TYPE_IOS			= 10133000;  //IOS
const PASSAGE_TYPE_WINPHONE			= 10133001;  //WIN手机
const PASSAGE_TYPE_WECHAT			= 10133002;  //微信
//------------------------- 通道 --------------------------------

//------------------------- 任务 --------------------------------

const TASK_STATE_DRAFT			= 20153000;  //未发布
const TASK_STATE_TODO			= 20153001;  //未执行
const TASK_STATE_DOING			= 20153002;  //执行中
const TASK_STATE_PENDING			= 20153003;  //待验收
const TASK_STATE_NOTPASS			= 20153004;  //验收不通过
const TASK_STATE_DONE			= 20153005;  //已完成
const TASK_STATE_PARENT_PENDING			= 20153006;  //父任务待验收
const TASK_STATE_PARENT_DONE			= 20153007;  //父任务已完成
const PARAM_TYPE_PERMISSION_PARENT			= 20153012;  //父任务有权发布
//------------------------- 任务 --------------------------------

//------------------------- 运营中心 --------------------------------

const OPERATION_SOBTYPE_OFFICIAL			= 20533000;  //正式账套
const OPERATION_SOBTYPE_TRIAL			= 20533001;  //试用账套
const OPERATION_VISITTYPE_MOBILE_ALLOW			= 20533100;  //手机均允许访问
const OPERATION_VISITTYPE_MOBILE_DISALLOW			= 20533101;  //手机均禁止访问
const OPERATION_VISITTYPE_WEB_ALLOW			= 20533102;  //web均允许访问
const OPERATION_VISITTYPE_WEB_DISALLOW			= 20533103;  //web均禁止访问
const OPERATION_ACCCTRLTYPE_ALTERNATIVE			= 20533200;  //绑定手机或邮件
const OPERATION_ACCCTRLTYPE_MOBILE			= 20533201;  //必须绑定手机
const OPERATION_ACCCTRLTYPE_EMAIL			= 20533202;  //必须绑定邮箱
const OPERATION_CELLCTRLDTYPE_TELECOM			= 20533400;  //仅电信
const OPERATION_CELLCTRLDTYPE_MOBILE			= 20533401;  //仅移动
const OPERATION_CELLCTRLDTYPE_UNICOM			= 20533402;  //仅联通
const OPERATION_CELLCTRLDTYPE_UNLIMITED			= 20533403;  //不限运营商
const OPERATION_MODULE_AVAILABLE			= 20531000;  //可用
const OPERATION_MODULE_PREINST			= 20531001;  //预装
const OPERATION_MODULE_NOTEDIT			= 20531002;  //不允许修改
const OPERATION_MODULE_INITIALIZED			= 20531003;  //模块已初始化
const OPERATION_MODULE_BASEPERMIT			= 20531004;  //默认基础权限
const OPERATION_MODETYPE_CLOUD			= 20533500;  //云端模式
const OPERATION_MODETYPE_LOCAL			= 20533501;  //本地模式
const OPERATION_APPTYPE_GROUP			= 'group';	//应用分组
const OPERATION_APPTYPE_APP			= 'app';	//应用标配app
const OPERATION_APPTYPE_SYSAPP			= 'sysapp';	//应用必配app
const OPERATION_PRODTYPE_PRODUCT			= 'products';	//产品
const OPERATION_PRODTYPE_SUBPRODUCT			= 'subproduct';	//子产品
const OPERATION_MODETYPE_STANDALONE			= 20533502;  //独立模式
//------------------------- 运营中心 --------------------------------

//------------------------- 缓存 --------------------------------

const CACHE_KEY_ENTERPRISE			= 'ENTERPRISE';	//企业
const CACHE_KEY_PERSONAL			= 'PERSONAL';	//个人
const CACHE_KEY_MODULE			= 'MODULE';	//模块
const CACHE_KEY_LOGINURL			= 'LOGINURL';	//登录时的URL
const CACHE_KEY_REDIRETURL			= 'REDIRETURL';	//登录前的URL(点击别人给的URL，登录后需要返回)
const CACHE_KEY_GLOBALTHEME			= 'GLOBALTHEME';	//整站界面风格
const CACHE_KEY_GLOBALTHEME_DEFAULT			= 'default';	//默认界面风格
const CACHE_KEY_FORM			= 'FORM';	//表单
const CACHE_KEY_TEMPLATE			= 'TEMPLATE';	//模版
const CACHE_KEY_TABLE			= 'TABLE';	//表
const CACHE_KEY_WECHAT			= 'WECHAT';	//微信企业号
const CACHE_KEY_PERMIT			= 'PERMIT';	//权限
const CACHE_KEY_PERMITROLE			= 'PERMITROLE';	//角色权限
const CACHE_KEY_SMSCODE			= 'SMSCODE';	//手机短信验证码
const CACHE_KEY_OAUTH			= 'OAUTH';	//oauth服务
const CACHE_KEY_MOBILE_CERT			= 'MOBILE_CERT';	//手机证书
//------------------------- 缓存 --------------------------------

//------------------------- 收集意见 --------------------------------

const OPINION_SELECTTYPE_ANON			= 20591000;  //匿名收集意见
const OPINION_SELECTTYPE_OPEN_ALWAYS_RESULT			= 20591001;  //始终公开结果
const OPINION_SELECTTYPE_OPEN_AFTER_RESULT			= 20591002;  //收集意见结束后公开结果
const OPINION_SELECTTYPE_OPEN_ALWAYS_RECORD			= 20591003;  //始终公开参与人意见记录
const OPINION_SELECTTYPE_OPEN_AFTER_RECORD			= 20591004;  //收集意见结束后公开参与人意见记录
const OPINION_STATE_CAN			= 20592000;  //可提交意见状态
const OPINION_STATE_CANNOT			= 20592001;  //已提交意见状态
const OPINION_STATE_NOPERMIT			= 20592002;  //无权提交意见状态
const OPINION_STATE_NORMAL			= 20593000;  //正常收集意见
const OPINION_STATE_ENDING			= 20593001;  //结束收集意见
//------------------------- 收集意见 --------------------------------

//------------------------- 会签 --------------------------------

const COUNTERSIGN_SELECTTYPE_ANON			= 20581000;  //匿名会签
const COUNTERSIGN_SELECTTYPE_OPEN_ALWAYS_RESULT			= 20581001;  //始终公开结果
const COUNTERSIGN_SELECTTYPE_OPEN_AFTER_RESULT			= 20581002;  //会签结束后公开结果
const COUNTERSIGN_SELECTTYPE_OPEN_ALWAYS_RECORD			= 20581003;  //始终公开参与人会签记录
const COUNTERSIGN_SELECTTYPE_OPEN_AFTER_RECORD			= 20581004;  //会签结束后公开参与人会签记录
const COUNTERSIGN_STATE_CAN			= 20582000;  //可会签状态
const COUNTERSIGN_STATE_CANNOT			= 20582001;  //已会签状态
const COUNTERSIGN_STATE_NOPERMIT			= 20582002;  //无权会签状态
const COUNTERSIGN_STATE_NORMAL			= 20583000;  //正常会签
const COUNTERSIGN_STATE_ENDING			= 20583001;  //结束会签
//------------------------- 会签 --------------------------------

//------------------------- 号码生成 --------------------------------

const NUMADMIN_OPSTATE_WAITING			= 20601000;  //候选号码
const NUMADMIN_OPSTATE_ENABLE			= 20601001;  //可用号码
const NUMADMIN_OPSTATE_USED			= 20601002;  //已用号码
const NUMADMIN_OPSTATE_REUSE			= 20601003;  //复用号码
const NUMADMIN_NUMTYPE_ORDINARY			= 20603000;  //普通号码
const NUMADMIN_NUMTYPE_SPECIAL			= 20603001;  //特殊号码
const NUMADMIN_NUMTYPE_RESERVE			= 20603002;  //保留号码
const NUMADMIN_PLATTYPE_ENTNO			= 20603100;  //企业号entno
const NUMADMIN_PLATTYPE_OANO			= 20603101;  //个人号oano
//------------------------- 号码生成 --------------------------------

//------------------------- 系统角色 --------------------------------

const PERMIT_ROLE_TYPE_BOSS			= 20611000;  //BOSS
const PERMIT_ROLE_TYPE_ADMIN			= 20611001;  //系统管理员
const PERMIT_ROLE_TYPE_APPADMIN			= 20611002;  //模块管理员
const PERMIT_ROLE_TYPE_WORKFLOW			= 20611003;  //流程管理员
//------------------------- 系统角色 --------------------------------

//------------------------- 标签替换格式 --------------------------------

const TAG_FORMAT_USERDATA_TO_NAME			= 'userdata_to_name';	//选人格式转人名
const TAG_FORMAT_USERID_TO_NAME			= 'userid_to_name';	//创建人id转人名
const TAG_FORMAT_PARAMS			= 'params';	//类别id转文本
const TAG_FORMAT_ENUM			= 'enum';	//特殊枚举值转文本
const TAG_FORMAT_DATETIME			= 'datetime';	//时间格式转换
const TAG_FORMAT_LINK			= 'link';	//url链接格式
const TAG_FORMAT_VENUE			= 'venue';	//会议室id转文本
const TAG_FORMAT_CUSTOM			= 'custom';	//自定义的转化
//------------------------- 标签替换格式 --------------------------------

//------------------------- 模块定义 --------------------------------

const MODULE_NAME_ALL			= 2000;  //全部模块
const MODULE_NAME_INDUSTRY			= 1004;  //行业
const MODULE_NAME_CHARACTER			= 1005;  //性格
const MODULE_NAME_INTEREST			= 1006;  //爱好
const MODULE_NAME_MEALS			= 1007;  //套餐
const MODULE_NAME_CUSTYPE			= 1008;  //客户类型
const MODULE_NAME_GRADE			= 1009;  //级别
const MODULE_NAME_STATE			= 1010;  //阶段
const MODULE_NAME_DISTRICT			= 1011;  //区域
const MODULE_NAME_SRC			= 1012;  //来源
const MODULE_NAME_PASSAGE			= 1013;  //通道(推送通道，IOS或微信)
const MODULE_NAME_ACTION			= 1014;  //操作事件
const MODULE_NAME_TAG			= 1015;  //标签
const MODULE_NAME_NEWS			= 2001;  //新闻公告
const MODULE_NAME_PROCESS			= 2002;  //申请审批
const MODULE_NAME_FORM			= 2003;  //表单
const MODULE_NAME_WORKFLOW			= 2013;  //工作流
const MODULE_NAME_APPFORM			= 2014;  //app表单
const MODULE_NAME_TASK			= 2015;  //任务
const MODULE_NAME_DEPARTMENT			= 2016;  //部门
const MODULE_NAME_MEETING			= 2017;  //开会
const MODULE_NAME_VISIT			= 2018;  //拜访
const MODULE_NAME_ACTIVITY			= 2019;  //活动
const MODULE_NAME_COMMENT			= 2020;  //评论
const MODULE_NAME_APPROVE			= 2021;  //行政审批
const MODULE_NAME_EFAX			= 2022;  //网络传真
const MODULE_NAME_UPLOAD			= 2023;  //文件上传
const MODULE_NAME_MSG			= 2024;  //系统消息
const MODULE_NAME_PLAN			= 2025;  //计划
const MODULE_NAME_REPORT			= 2026;  //汇报
const MODULE_NAME_SYS			= 2027;  //系统，包括人员 部门 职位
const MODULE_NAME_PMS			= 2028;  //内部邮件
const MODULE_NAME_WEIXIN			= 2029;  //微信企业号
const MODULE_NAME_WAGES			= 2030;  //工资
const MODULE_NAME_TRIAL			= 2031;  //试用
const MODULE_NAME_FAQ			= 2032;  //常见问题
const MODULE_NAME_ISSUE			= 2033;  //公文
const MODULE_NAME_LOGIN			= 2034;  //登录
const MODULE_NAME_PREFERENCE			= 2035;  //偏好
const MODULE_NAME_TELMEETING			= 2036;  //电话会议
const MODULE_NAME_TODAY			= 2037;  //今天
const MODULE_NAME_DATASOURCE			= 2038;  //数据源
const MODULE_NAME_COLLEAGUE			= 2039;  //同事录
const MODULE_NAME_TABLE			= 2040;  //数据表
const MODULE_NAME_VOTE			= 2041;  //投票
const MODULE_NAME_COMPONENT			= 2042;  //控件
const MODULE_NAME_PERMIT			= 2043;  //权限
const MODULE_NAME_CUSTOMER			= 2044;  //客户
const MODULE_NAME_PERSON			= 2045;  //个人设置
const MODULE_NAME_APP			= 2046;  //应用
const MODULE_NAME_ATTEND			= 2047;  //考勤
const MODULE_NAME_DESKTOP			= 2048;  //桌面
const MODULE_NAME_DOCUMENT			= 2049;  //文件柜
const MODULE_NAME_TIPS			= 2050;  //提示
const MODULE_NAME_NOTES			= 2051;  //笔记
const MODULE_NAME_SYSSET			= 2052;  //系统设置
const MODULE_NAME_OPERATION			= 2053;  //运营中心
const MODULE_NAME_CONTACT			= 2054;  //联系人
const MODULE_NAME_SIGNIN			= 2055;  //签到
const MODULE_NAME_PHOTO			= 2056;  //拍照
const MODULE_NAME_APIADMIN			= 2057;  //接口管理后台
const MODULE_NAME_COUNTERSIGN			= 2058;  //会签
const MODULE_NAME_OPINION			= 2059;  //收集意见
const MODULE_NAME_NUMADMIN			= 2060;  //号码生成管理后台
const MODULE_NAME_PERMITROLE			= 2061;  //系统角色
const MODULE_NAME_MARKETACTIVITY			= 2062;  //市场活动
const MODULE_NAME_SALESCLUES			= 2063;  //销售线索
const MODULE_NAME_OPPORTUNITY			= 2064;  //销售机会
const MODULE_NAME_CONTRACT			= 2065;  //合同
const MODULE_NAME_SALESTARGET			= 2066;  //销售目标
const MODULE_NAME_PRODUCT			= 2067;  //产品
const MODULE_NAME_COMPETITOR			= 2068;  //竞争对手
const MODULE_NAME_PLATFORMADMIN			= 2069;  //平台管理
const MODULE_NAME_PRODUCTADMIN			= 2070;  //产品管理
const MODULE_NAME_SUBPRODUCTADMIN			= 2071;  //子产品管理
const MODULE_NAME_VERSIONADMIN			= 2072;  //版本管理
const MODULE_NAME_PUBLISHADMIN			= 2073;  //发布管理
const MODULE_NAME_BLOG			= 2074;  //同事圈
const MODULE_NAME_ENUMADMIN			= 2075;  //枚举管理后台
const MODULE_NAME_PERFASSESS			= 2076;  //绩效考核
const MODULE_NAME_UPDATEADMIN			= 2077;  //更新管理后台
const MODULE_NAME_LANGADMIN			= 2078;  //语言包管理后台
const MODULE_NAME_PACKAGEADMIN			= 2079;  //更新包管理后台
const MODULE_NAME_SOBPACKAGE			= 2080;  //企业帐套套餐
const MODULE_NAME_SYSLOG			= 2081;  //系统日志
const MODULE_NAME_PAYROLL			= 2082;  //工资条
const MODULE_NAME_PM			= 2083;  //项目管理
const MODULE_NAME_MOBILEDEVICE			= 2085;  //移动设备授权管理
const MODULE_NAME_USERVIEW			= 2086;  //用户查看
const MODULE_NAME_OPENLIST			= 2087;  //开通表导入
const MODULE_NAME_CHANNEL			= 2088;  //渠道
const MODULE_NAME_CLOUDPLATFORM			= 2089;  //云平台
const MODULE_NAME_USERACCOUNT			= 2090;  //用户账号
const MODULE_NAME_ZHIXIN			= 2091;  //智信
const MODULE_NAME_AUDIT			= 2092;  //审核
const MODULE_NAME_PROGRESS			= 2093;  //进度汇报
const MODULE_NAME_EXTMAIL			= 2124;  //外部邮件
const MODULE_NAME_APPADMIN			= 2125;  //App后台
const MODULE_NAME_DISTRIBUTE			= 2094;  //分发
const MODULE_NAME_CUSTOMAPP			= 2104;  //自定义App
const MODULE_NAME_ENTERPRISE			= 2105;  //企业处理
const MODULE_NAME_FILE			= 2129;  //文件
const MODULE_NAME_HOME			= 2095;  //企业主页
const MODULE_NAME_LISTDATA			= 2096;  //列表数据(报表)
const MODULE_NAME_LOCATE			= 2097;  //定位
const MODULE_NAME_NEWCORP			= 2098;  //创建公司
const MODULE_NAME_PLATFORM			= 2099;  //平台
const MODULE_NAME_QUEUE			= 2100;  //队列
const MODULE_NAME_BIZFLOW			= 2084;  //业务流
const MODULE_NAME_TOOL			= 2101;  //工具
const MODULE_NAME_USER			= 2102;  //用户
const MODULE_NAME_VENUE			= 2103;  //地点
const MODULE_NAME_APPBASE			= 2106;  //App基础
const MODULE_NAME_ENTBASE			= 2107;  //企业基础
const MODULE_NAME_IWEFORM			= 2108;  //表单
const MODULE_NAME_PAGEBASE			= 2109;  //页面基础
const MODULE_NAME_PAGEMAKE			= 2110;  //页面生成
const MODULE_NAME_10013001			= 2122;  //10013001
const MODULE_NAME_10013002			= 2123;  //10013002
const MODULE_NAME_UNIT_ALARM			= 2111;  //提醒控件
const MODULE_NAME_UNIT_CHART			= 2112;  //图表控件
const MODULE_NAME_UNIT_COMMENT			= 2113;  //评论控件
const MODULE_NAME_UNIT_HTML5			= 2114;  //H5控件
const MODULE_NAME_UNIT_INC			= 2115;  //inc
const MODULE_NAME_UNIT_NGTABLE			= 2116;  //表格控件
const MODULE_NAME_UNIT_OFFICEONLINE			= 2117;  //office控件
const MODULE_NAME_UNIT_PARAMS			= 2118;  //参数控件
const MODULE_NAME_UNIT_TAG			= 2119;  //标签控件
const MODULE_NAME_UNIT_WEB			= 2120;  //web控件
const MODULE_NAME_UNIT_WORKFLOW			= 2121;  //流程控件
const MODULE_NAME_ACTIONADMIN			= 2126;  //操作后台
const MODULE_NAME_PAGECLIENTADMIN			= 2127;  //客户端页面后台
const MODULE_NAME_PAGEWEBADMIN			= 2128;  //Web端页面后台
const MODULE_NAME_PAGADMIN			= 2130;  //页面后台
const MODULE_NAME_ALBUM			= 2131;  //相册
const MODULE_NAME_SIGNUP			= 2132;  //报名
const MODULE_NAME_APPSTORE			= 2133;  //Appstore
const MODULE_NAME_PAGEADMIN			= 2134;  //页面管理后台
const MODULE_NAME_SETTINGADMIN			= 2135;  //设置管理后台
const MODULE_NAME_ENTADMIN			= 2136;  //企业管理后台
const CONVERSION_MODE_OPPENOFFICE			= 20434312;  //openoffcie
const CONVERSION_MODE_LIBREOFFICE			= 20434313;  //libreoffice
const CONVERSION_MODE_PREVIEW			= 2043414;  //预览模式
const CONVERSION_MODE_IM			= 2043415;  //服务[doc,tif]->pdf
const MODULE_NAME_REQUEST			= 2138;  //请示
const MODULE_NAME_RETURNRECEIPT			= 2139;  //回执
const MODULE_NAME_CUSTOM_PERSONAL			= 12010021;  //个人预设的审批意见
const MODULE_NAME_ADMIN			= 12010020;  //管理员预设的审批意见
const MODULE_NAME_DESIGN			= 12010022;  //流程步骤预设的审批意见
const CONVERSION_MODE_WINDOWS			= 2043416;  //Windows系统Word转换PDF服务（农信贷用）
const MODULE_NAME_SITEADMIN			= 2176;  //站点管理后台
const MODULE_NAME_DBCLEARADMIN			= 2137;  //清理数据后台
const MODULE_NAME_MAINTENANCE			= 2138;  //运维管理后台
//------------------------- 模块定义 --------------------------------

//------------------------- 搜索条件 --------------------------------

const FORM_CONDITION_TYPE_WHERE_EQUAL			= '=';	//等于
const FORM_CONDITION_TYPE_WHERE_NOTEQUAL			= '!=';	//不等于
const FORM_CONDITION_TYPE_WHERE_CONTAIN			= 'find_in_set';	//包含
const FORM_CONDITION_TYPE_WHERE_BETWEEN			= 'between';	//区间
const FORM_CONDITION_TYPE_WHERE_LESS			= '<';	//小于
const FORM_CONDITION_TYPE_WHERE_LESSEQUAL			= '<=';	//小于或等于
const FORM_CONDITION_TYPE_WHERE_GREATER			= '>';	//大于
const FORM_CONDITION_TYPE_WHERE_GREATEREQUAL			= '>=';	//大于或等于
const FORM_CONDITION_TYPE_WHERE_AND			= 'and';	//同时满足
const FORM_CONDITION_TYPE_WHERE_OR			= 'or';	//包括任意选中项
const FORM_CONDITION_TYPE_DATA_FIXED			= 'absolute';	//固定值
const FORM_CONDITION_TYPE_DATA_DYNAMIC			= 'relative';	//变量值
const FORM_CONDITION_TYPE_DATE_BEFOREYESTERDAY			= 'beforeyesterday';	//前天
const FORM_CONDITION_TYPE_DATE_YESTERDAY			= 'yesterday';	//昨天
const FORM_CONDITION_TYPE_DATE_TODAY			= 'today';	//今天
const FORM_CONDITION_TYPE_DATE_TOMORROW			= 'tomorrow';	//明天
const FORM_CONDITION_TYPE_DATE_AFTERTOMORROW			= 'aftertomorrow';	//后天
const FORM_CONDITION_TYPE_DATE_LASTWEEK			= 'lastweek';	//上周
const FORM_CONDITION_TYPE_DATE_THISWEEK			= 'thisweek';	//本周
const FORM_CONDITION_TYPE_DATE_NEXTWEEK			= 'nextweek';	//下周
const FORM_CONDITION_TYPE_DATE_LASTMONTH			= 'lastmonth';	//上月
const FORM_CONDITION_TYPE_DATE_THISMONTH			= 'thismonth';	//本月
const FORM_CONDITION_TYPE_DATE_NEXTMONTH			= 'nextmonth';	//下月
const FORM_CONDITION_TYPE_DATE_LASTQUARTER			= 'lastquarter';	//上季度
const FORM_CONDITION_TYPE_DATE_THISQUARTER			= 'thisquarter';	//本季度
const FORM_CONDITION_TYPE_DATE_NEXTQUARTER			= 'nextquarter';	//下季度
const FORM_CONDITION_TYPE_DATE_LASTYEAR			= 'lastyear';	//去年
const FORM_CONDITION_TYPE_DATE_THISYEAR			= 'thisyear';	//今年
const FORM_CONDITION_TYPE_DATE_NEXTYEAR			= 'nextyear';	//明年
const FORM_CONDITION_TYPE_SELECT_ISME			= 'isme';	//自己
const FORM_CONDITION_TYPE_SELECT_DIRSUB			= 'dirsub';	//我的直接下属
const FORM_CONDITION_TYPE_SELECT_DIRSUP			= 'dirsup';	//我的直接上司
const FORM_CONDITION_TYPE_SELECT_DEPTIN			= 'deptin';	//同部门人员
const FORM_CONDITION_TYPE_SELECT_THISDEP			= 'thisdep';	//本部门
const FORM_CONDITION_TYPE_SELECT_HIGHERDEP			= 'higherdep';	//上级部门
const FORM_CONDITION_TYPE_SELECT_LOWERDEP			= 'lowerdep';	//下级部门
const FORM_CONDITION_TYPE_SELECT_SIBLINGDEP			= 'siblingdep';	//同级部门
//------------------------- 搜索条件 --------------------------------

//------------------------- 报表类型 --------------------------------

const LISTDATA_TYPE_NORMAL			= 10007000;  //普通报表类型
//------------------------- 报表类型 --------------------------------

//------------------------- 统计方式 --------------------------------

const COUNT_TYPE_SUM			= 'sum';	//求和
const COUNT_TYPE_AVG			= 'avg';	//平均值
const COUNT_TYPE_MAX			= 'max';	//最大值
const COUNT_TYPE_MIN			= 'min';	//最小值
//------------------------- 统计方式 --------------------------------

//------------------------- 关联类型 --------------------------------

const RELA_TYPE_REFERENCES			= 20002000;  //关联类型，引用（无关联字段）
const RELA_TYPE_CORRELATE			= 20002001;  //关联类型，关联（有关联字段）
const RELA_TYPE_TOOL			= 20002002;  //关联类型，小工具
//------------------------- 关联类型 --------------------------------

//------------------------- 工资单 --------------------------------

const PAYROLL_OP_PRIVATE			= 20821000;  //不公开
const PAYROLL_OP_PUBLISH			= 20821001;  //公开
const SALARY_TYPE_CONFIRM_YES			= 27901000;  //新工资条确认状态
const SALARY_TYPE_CONFIRM_NO			= 27901001;  //新工资条未确认状态
const SALARY_TYPE_READED_YES			= 27901002;  //新工资条已阅状态
const SALARY_TYPE_READED_NO			= 27901003;  //新工资条未阅状态
const SALARY_TYPE_BURN_YES			= 27901004;  //新工资条已焚毁状态
const SALARY_TYPE_BURN_NO			= 27901005;  //新工资条未焚毁状态
const SALARY_TYPE_ADMIN			= 27901006;  //新工资条管理员面板设置
const SALARY_TYPE_PERSNOAL_SETTING			= 27901007;  //工资单个人设置
//------------------------- 工资单 --------------------------------

//------------------------- 公文 --------------------------------

const ISSUE_STATE_DOING			= 20331000;  //编辑/办理中
const ISSUE_STATE_END			= 20331001;  //结束/已办结
const ISSUE_STATE_CLOSE			= 20331002;  //关闭/撤销
const EXCHANGE_STATE_SENDED			= 21351001;  //交换中心，已发送未签收
const EXCHANGE_STATE_ACCEPTED			= 21351002;  //已签收
const EXCHANGE_STATE_REJECTED			= 21351003;  //已拒收
const EXCHANGE_STATE_ACCEPTED_ALL			= 21351004;  //已发件（所有人发件）
//------------------------- 公文 --------------------------------

//------------------------- 模板类型 --------------------------------

const COMMON_PAGE_WEB_SAVE_FORM			= 10001010;  //通用表单类型的新建页
const COMMON_PAGE_WEB_SAVE_TABLE			= 10001011;  //通用表格类型的新建页
const COMMON_PAGE_WEB_DETAIL_FORM			= 10001012;  //通用表单类型的详情页
const COMMON_PAGE_WEB_DETAIL_TABLE			= 10001013;  //通用表格类型的详情页
//------------------------- 模板类型 --------------------------------

//------------------------- 详情页tab --------------------------------

const DETAIL_NAVTAB_FORMINFO			= 20003001;  //详情页tab,表单详情
const DETAIL_NAVTAB_WFSITUATION			= 20003002;  //详情页tab,办理流程
const DETAIL_NAVTAB_COMMENT			= 20003003;  //详情页tab,讨论区
//------------------------- 详情页tab --------------------------------

//------------------------- H5入口来源 --------------------------------

const H5_SRC_MOBILE			= 10171001;  //客户端来源类型
const H5_SRC_WEIXIN			= 10171002;  //微信来源类型
//------------------------- H5入口来源 --------------------------------

//------------------------- app发布状态 --------------------------------

const VERSION_TYPE_NONE			= 21251000;  //none 空版本，用于修改，只能存在一次
const VERSION_TYPE_BASE			= 21251001;  //base  页面功能没有完成的最初级版本
const VERSION_TYPE_ALPHA			= 21251002;  //alpha 软件初级版本，实现功能为主
const VERSION_TYPE_BETA			= 21251003;  //Beta  完成度较高的版本，消除严重错误(测试版本)
const VERSION_TYPE_RC			= 21251004;  //RC     最接近标准版的版本
const VERSION_TYPE_RELEASE			= 21251005;  //release 正式版本
//------------------------- app发布状态 --------------------------------

//------------------------- 微信 --------------------------------

const MODULE_INSTALL_FROM_OA			= 20290001;  //从oa内页安装模块
const MODULE_INSTALL_FROM_WXQYH			= 20290002;  //从微信企业号或者微信app平台安装模块
//------------------------- 微信 --------------------------------

//------------------------- 设置参数类型 --------------------------------

const SETTING_TYPE_NORMAL			= 10001501;  //高级设置类型，普通类型
const SETTING_TYPE_ADVANNCE			= 10001502;  //高级设置类型，高级类型
const SETTING_TYPE_GOV			= 10001503;  //高级设置类型，政务类型
const SETTING_TYPE_VIP			= 10001504;  //高级设置类型，VIP类型
const SETTING_TYPE_WATERMARK			= 10001505;  //开启图片水印模式
const SETTING_CUSTOM_MENU_AMOUNT			= 10001506;  //自定义客户端菜单数量
//------------------------- 设置参数类型 --------------------------------

//------------------------- 标签 --------------------------------

const TAG_ACTION_ALTER			= 10152000;  //标签操作类型修改
const TAG_ACTION_CREATE			= 10152001;  //标签类型新建操作
const TAG_ACTION_DELETE			= 10152002;  //标签操作类型删除
//------------------------- 标签 --------------------------------

//------------------------- 控件分类（按数据格式一致性分） --------------------------------

const CMPT_TYPE_INPUT			= 10007001;  //单行文本类控件类型
const CMPT_TYPE_TEXT			= 10007002;  //多行文本控件类型
//------------------------- 控件分类（按数据格式一致性分） --------------------------------

//------------------------- 小程序日程 --------------------------------

const ALL_CALENDAR_TYPE			= 20221101;  //全天日程类型
const NO_CALENDAR_TYPE			= 20221100;  //非全天日程类型
const PRIVATE_TYPE			= 20221102;  //私人日程类型
const REPEAT_TYPE_TRUE			= 20221104;  //重复日程
const REPEAT_TYPE_FALSE			= 20221105;  //非重复日程
const REPEAT_FREQUENCY_DAY			= 20221107;  //每天重复
const REPEAT_FREQUENCY_WEEK			= 20221106;  //每周重复
const REPEAT_FREQUENCY_MONTH			= 20221108;  //每月重复
const REPEAT_FREQUENCY_YEAR			= 20221110;  //每年重复
const REMIND_TYPE_NO			= 20221114;  //无提醒
const REMIND_TYPE_MIN			= 20221111;  //提醒单位为分钟
const REMIND_TYPE_HOUR			= 20221112;  //提醒单位为小时
const REMIND_TYPE_DAY			= 20221113;  //提醒单位为天
const SHARE_TYPE_TRUE			= 20221115;  //分享
const SHARE_TYPE_FALSE			= 20221116;  //不分享
const REWARD_TYPE_TRUE			= 20221117;  //发布悬赏金
const REWARD_TYPE_FALSE			= 20221118;  //不发布悬赏金
//------------------------- 小程序日程 --------------------------------

//------------------------- 公司服务器id --------------------------------

const SERVER_INFO_ENT_OA_CN			= '96958028921710849';	//互联网注册服务器
//------------------------- 公司服务器id --------------------------------

//------------------------- 客户拜访 --------------------------------

const SCHEDULE_OVERDUE			= 20181001;  //逾期日程
const SCHEDULE_WAIT_TO_SIGNOUT			= 20181002;  //待签退
const SCHEDULE_WRITING_REPORT			= 20181003;  //正在写拜访总结
const SCHEDULE_FINISH			= 20181004;  //日程完成
const VISIT_UNDERLING_CHECK_PERMIT			= 20182001;  //查看下属权限设置
const VISIT_USER_CHECK_PERMIT			= 20182002;  //按人员授权
const VISIT_OTHER_CHECK_PERMIT			= 20182003;  //客户拜访其他设置
//------------------------- 客户拜访 --------------------------------

//------------------------- 培训管理 --------------------------------

const EXAM_TIME			= '2054818158';	//考试时间固定标识
//------------------------- 培训管理 --------------------------------

//------------------------- 小程序类型 --------------------------------

const WEAPP_TYPE_CARD			= 2001;  //名片本
const WEAPP_TYPE_SWL			= 2002;  //书为邻
const WEAPP_TYPE_COOP			= 2003;  //智协作
const WEAPP_TYPE_REPORT			= 2004;  //智汇报
const WEAPP_TYPE_NEWS			= 2005;  //智公告
const WEAPP_TYPE_GROUPSET			= 2006;  //群设置
const WEAPP_TYPE_WISDOM			= 2007;  //智慧的管理公众号
const WEAPP_TYPE_SWLPUBLIC			= 2008;  //书为邻公众号
//------------------------- 小程序类型 --------------------------------

//------------------------- 公共设置opstype --------------------------------

const OPTYPE_SIGNATURE_VARLE			= 10020001;  //签章接口类型
const OPTYPE_UKEY_VALUE			= 13800210;  //UKEY验证类型
const OPTYPE_CMDAPI_DOWN_URL			= 13800211;  //cmdapi 站点更新源下载地址
const OPTYPE_SIGNATURESAVEPDF_VARLE			= 10020002;  //在线office签章后保存方式，1 按文档类型保存， 2 保存为pdf(只做软航)
//------------------------- 公共设置opstype --------------------------------

//------------------------- 验证属性 --------------------------------

const UPDATE_SERVER_TOKEN			= '7a4168848caecdfc';	//cmdapi验证用token
//------------------------- 验证属性 --------------------------------

//------------------------- 请示 --------------------------------

const REQUEST_SELECTTYPE_ANON			= 21381000;  //匿名请示
const REQUEST_SELECTTYPE_OPEN_ALWAYS_RESULT			= 21381001;  //始终公开结果
const REQUEST_SELECTTYPE_OPEN_AFTER_RESULT			= 21381002;  //请示结束后公开结果
const REQUEST_SELECTTYPE_OPEN_ALWAYS_RECORD			= 21381003;  //始终公开参与人请示记录
const REQUEST_SELECTTYPE_OPEN_AFTER_RECORD			= 21381004;  //请示结束后公开参与人请示记录
const REQUEST_STATE_CAN			= 21382000;  //可请示状态
const REQUEST_STATE_CANNOT			= 21382001;  //已请示状态
const REQUEST_STATE_NOPERMIT			= 21382002;  //无权请示状态
const REQUEST_STATE_NORMAL			= 21383000;  //正常请示
const REQUEST_STATE_ENDING			= 21383001;  //结束请示
//------------------------- 请示 --------------------------------

//------------------------- 回执 --------------------------------

const RETURNRECEIPT_SELECTTYPE_ANON			= 21391000;  //匿名回执
const RETURNRECEIPT_SELECTTYPE_OPEN_ALWAYS_RESULT			= 21391001;  //始终公开结果
const RETURNRECEIPT_SELECTTYPE_OPEN_AFTER_RESULT			= 21391002;  //回执结束后公开结果
const RETURNRECEIPT_SELECTTYPE_OPEN_ALWAYS_RECORD			= 21391003;  //始终公开参与人回执记录
const RETURNRECEIPT_SELECTTYPE_OPEN_AFTER_RECORD			= 21391004;  //回执结束后公开参与人回执记录
const RETURNRECEIPT_STATE_CAN			= 21392000;  //可回执状态
const RETURNRECEIPT_STATE_CANNOT			= 21392001;  //已回执状态
const RETURNRECEIPT_STATE_NOPERMIT			= 21392002;  //无权回执状态
const RETURNRECEIPT_STATE_NORMAL			= 21393000;  //正常回执
const RETURNRECEIPT_STATE_ENDING			= 21393001;  //结束回执
//------------------------- 回执 --------------------------------

//------------------------- 小程序进入页面的链接来源 --------------------------------

const WEAPP_SOURCE_SHARE			= 3000;  //从分享链接进入
//------------------------- 小程序进入页面的链接来源 --------------------------------

//------------------------- 小程序用户操作类型 --------------------------------

const WEAPP_USER_OP_INIT			= 4000;  //项目初始化
//------------------------- 小程序用户操作类型 --------------------------------

//------------------------- 小程序消息分类类型 --------------------------------

const WEAPP_MSG_TYPE_SYS			= 5000;  //系统类型
const WEAPP_MSG_TYPE_USER			= 5001;  //用户类型
//------------------------- 小程序消息分类类型 --------------------------------

//------------------------- 小程序消息操作类型 --------------------------------

const WEAPP_MSG_OPTYPE_CARD_CHANGE			= 20011000;  //名片本请求交换名片
const WEAPP_MSG_OPTYPE_CARD_DELGROUP			= 20011001;  //名片本删除微信群
//------------------------- 小程序消息操作类型 --------------------------------

//------------------------- 小程序消息操作状态 --------------------------------

const WEAPP_MSG_OPSTATE_UNTREATED			= 0;  //未处理
const WEAPP_MSG_OPSTATE_AGREE			= 1;  //已同意
const WEAPP_MSG_OPSTATE_REJECT			= 2;  //已拒绝
const WEAPP_MSG_OPSTATE_NEGLECT			= 3;  //已忽略
//------------------------- 小程序消息操作状态 --------------------------------

//------------------------- 小程序消息阅读状态 --------------------------------

const WEAPP_MSG_READSTATE_UNREAD			= 0;  //未阅
const WEAPP_MSG_READSTATE_READ			= 1;  //已阅
//------------------------- 小程序消息阅读状态 --------------------------------

//------------------------- 小程序用户 --------------------------------

const WEAPP_USER_VISITOR			= -1;  //游客
//------------------------- 小程序用户 --------------------------------

//------------------------- 选项卡类型 --------------------------------

const TAB_TYPE_REFER_ME			= 22011000;  //@我的
const TAB_TYPE_MEETING_VENUE			= 22011001;  //会议地点
const TAB_TYPE_MEETING_TASK			= 22011002;  //议而有行
const TAB_TYPE_MEETING_NOTE			= 22011003;  //会议笔记
const TAB_TYPE_DRAFT			= 22011004;  //草稿
const TAB_TYPE_SHARE			= 22011005;  //转发件
//------------------------- 选项卡类型 --------------------------------

//------------------------- 证书类型 --------------------------------

const CERT_MOBILE_TYPE			= 12010055;  //手机证书
const CERT_HUACE_TYPE			= 12010014;  //华测证书
const CERT_HUACE_DEF_VAL			= 12010015;  //华测证书绑定默认值
//------------------------- 证书类型 --------------------------------

//------------------------- 角标 --------------------------------

const CORNER_METHOD_UNREAD			= 'unread';	//默认获取角标方式(未阅数量)
const CORNER_METHOD_SQL			= 'sql';	//拼接sql获取角标方式
const CORNER_METHOD_PATH			= 'path';	//通过path得到sql获取角标方式
const CORNER_METHOD_CUSTOM			= 'custom';	//通过回调方法获取角标方式
const CORNER_TYPE_RED			= 'red';	//红点角标类型
const CORNER_TYPE_BOMB			= 'bomb';	//炸弹角标类型
const CORNER_TYPE_BUBBLE			= 'bubble';	//气泡角标类型
//------------------------- 角标 --------------------------------

}
?>