<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * @模块代码
 */
class ModuleCode {
    /*
     * @系统管理
     */

    public static $AdminSystem = "AdminSystem";
    /*
     * @人事管理
     */
    public static $AdminHrm = "AdminHrm";
    /*
     * @知识管理
     */
    public static $AdminDocs = "AdminDocs";
    /*
     * @项目管理
     */
    public static $AdminProject = "AdminProject";
    /*
     * @单位信息
     */
    public static $ViewStructure = "ViewStructure";
    /*
     * @会议管理
     */
    public static $AdminMeeting = "AdminMeeting";
    /*
     * @任务管理
     */
    public static $Task = "Task";

    /**
     * @行政
     * @var <type>
     */
    public static $HR = "Hr";

    /**
     * @申请审批
     * @var <type>
     */
    public static $Process = "Process";
	
	/**
     * @行政审批	//dxf 2014-11-21 15:35:13 旧版本存在此变量
     * @var <type>
     */
    public static $Approve = "Approve";
     /**
     * @工作流
     * @var <type>
     */
    public static $Workflow = "Workflow";   
    /*
     * @公告
     */
    public static $News = 'News';

    /**
     * 汇报
     */
    public static $Report = 'Report';

    /**
     * 计划
     */
    public static $Plan = 'Plan';
    /*
     * 活动
     */
    public static $Activity = 'Activity';
    /*
     * 网络传真
     */
    public static $Efax = 'efax';

    /**
     * 系统日志
     */
    public static $Syslog = 'Syslog';
    /*
     * oa应用商城
     * jeffli 2012-02-06
     */
    public static $OaStore = 'OaStore';
    /*
     * 授权码
     */
    public static $License = 'License';
    /*
     * OB商城
     */
    public static $OBmall = 'OBmall';
    /*
     * @客户
     */
    public static $Customer = 'Customer';
    /*
     * @催缴费
     */
    public static $Payment = 'Payment';

    /*
     * 服务拜访
     */
    public static $Visit = 'Visit';
    /*
     * 留言后台
     */
    public static $MessageAdmin = 'MessageAdmin';
    /*
     * 客服后台
     */
    public static $HelpAdmin = 'HelpAdmin';
    /*
     * 翼办公后台
     */
    public static $WingSaleAdmin = 'WingSaleAdmin';
    /*
     * 翼办公销售
     */
    public static $WingSale = 'WingSale';
    /*
     * sms短信平台
     */
    public static $Sms = 'Sms';
    /*
     * 商品
     */
    public static $Product = 'Product';
    /*
     * 内部邮件
     */
    public static $Pms = 'Pms';
    /*     * ***************烟草 Start**************** */
    /*
     * 零售户
     */
    public static $Retailbusinesses = 'Retailer';
    /*
     * 智能分析
     */
    public static $IntelligentAnalysis = 'IntelligentAnalysis';

    /*
     * 计划
     */
    public static $Plans = 'Plans';

    /**
     * 总结
     */
    public static $Reports = 'Reports';

    /**
     * 上报数据
     */
    public static $Reporting = 'Reporting';
    
    /**
     * 处理
     */
    public static $Deal = 'Deal';
    /*     * ***************烟草 End**************** */
    /*
     * @盘点
     */
    public static $Check = 'Check';
    
    /*
     * 考勤
     */
    public static $WorkAttendance = 'WorkAttendance';
	
	/*
     * 统计分析
     */
    public static $DataManagement = 'DataManagement';

    /*
     * 手机认证	dxf 2014-11-21 15:35:47 本版本新增
     */
    public static $PhoneAuth = 'PhoneAuth';
    /*
     * 人员轨迹 fyl 2015-01-21 
     */
    public static $Locate = 'Locate';
    /*
     * 人员轨迹 zjl 2015-02-6 
     */
    public static $Weixin = 'Weixin';
    
    function __construct() {
        
    } 
}