<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*
 * @操作代码
 */

class ActionCode {
    /*
     * @创建操作
     */

    public static $Create = "Create";
    /*
     * @管理操作
     */
    public static $Admin = "Admin";
    /*
     * @查看操作
     */
    public static $View = "View";
    /*
     * @上传操作
     */
    public static $Upload = "Upload";
    /*
     * @下载操作
     */
    public static $Download = "Download";

    /**
     * 发布
     * @var <type>
     */
    public static $Publish = "Publish";

    /**
     * 会议室管理
     * @var <type>
     */
    public static $AdminRoom = "AdminRoom";

    /**
     * 类型管理
     * @var <type>
     */
    public static $AdminType = "AdminType";

    /**
     * 找人汇报，计划
     * @var <type>
     */
    public static $ToMe = "ToMe";

    /**
     * 邀请注册
     * @var <type>
     */
    public static $InviteReg = "InviteReg";

    /**
     * 验证注册
     * @var <type>
     */
    public static $CheckReg = "CheckReg";

    /**
     * 员工账号管理
     * @var <type>
     */
    public static $AdminAccount = "AdminAccount";

    /**
     * 员工档案管理
     * @var <type>
     */
    public static $AdminEmployee = "AdminEmployee";

    /**
     * 权限组管理
     * @var <type>
     */
    public static $PerGroup = "PerGroup";

    /**
     * 部门管理
     * @var <type>
     */
    public static $AdminDepartment = "AdminDepartment";

    /**
     * 职务管理
     * @var <type>
     */
    public static $AdminPositon = "AdminPositon";

    /**
     * 单位信息维护
     * @var <type>
     */
    public static $EnterpriseInfo = "EnterpriseInfo";

    /**
     * 指派应用管理员
     * @var <type>
     */
    public static $SysAdmin = "SysAdmin";

    /**
     * 指派基本权限
     * @var <type>
     */
    public static $BasePerAdmin = "BasePerAdmin";
    /*
     * 安装app
     * jeffli 2012-02-06
     */
    public static $Install = "Install";
    /*
     * 卸载app
     * jeffli 2012-02-06
     */
    public static $Uninstall = "Uninstall";

    /**
     * 审批验收
     */
    public static $Approval = 'Approval';

    /**
     * 审批验收

      public static $AdminMeal = 'AdminMeal';
     */
    /*
     * 高级业务申请
     */
    public static $HighCreate = 'HighCreate';
    /*
     * 发起以单位名义的活动
     */
    public static $PublishCompany = 'PublishCompany';
    /*
     * 发起个人名义的活动
     */
    //public static $PublishPersonal = 'PublishPersonal';
    /*
     * 物品管理
     */
    public static $GoodsAdmin = 'GoodsAdmin';

    /*
     * 缴费确认
     */
    public static $Confirm = 'Confirm';

    /*
     * 管理套餐
     */
    public static $AdminMeal = 'AdminMeal';

    /*
     * 管理渠道
     */
    public static $AdminChannel = 'AdminChannel';

    /*
     * 受理业务
     */
    public static $AcceptBusiness = 'AcceptBusiness';

    /*
     * 修改客户经理
     */
    public static $AdminManager = 'AdminManager';
    
    /*
     * 管理认证受理
     */
    public static $Approve = 'Approve';
    
    /*
     * 使用停用套餐
     */
    public static $UseAllMeal = 'UseAllMeal';

    /*
     * 查看全部申请
     */
    public static $ViewAllApply = 'ViewAllApply';

    /*
     * 查看全部客户
     */
    public static $ViewAllCustomer = 'ViewAllCustomer';
    /*
     * 管理短信
     */
    public static $smsAdmin = 'smsAdmin';
    /*
     * 查看全部短信
     */
    public static $allView = 'allView';
    /*
     * 查看所有表单报表
     */
    public static $ViewAllSummary = 'ViewAllSummary';
    /*
     * 客户档案导出
     */
    public static $Export = 'Export';
    /*
     * 客户档案导入
     */
    public static $Import = 'Import';
    /*
     * 内部邮件的收邮件
     */
    public static $receivemail = 'receivemail';
    /*
     * 内部邮件的发邮件
     */
    public static $sendmail = 'sendmail';
    /*
     * 内部邮件的群发邮件
     */
    public static $bulkmail = 'bulkmail';
    /*     * ***************烟草 Start**************** */
    /* 直属分局查看权限 */
    public static $Catdirectly = 'is_directly';
    /*
     * 市局查看零售户
     */
    public static $CatPuc = 'CatPuc';
    /*
     * 县局查看
     */
    public static $CatCounty = 'CatCounty';
    /*
     * 稽查员查看
     */
    public static $CatInspector = 'CatInspector';
    /*
     * 中队长
     */
    public static $CatCaptain = 'CatCaptain';

    /*
     * 查看零售户   dosgo
     */
    public static $CatRetailcustomers = 'CatRetailcustomers';

    /* 接收级别变动消息 */
    public static $LevelChangeMsg = 'LevelChangeMsg';
    /*
     * 导入导出零售户   dosgo
     */
    public static $ImportRetailcustomers = 'ImportRetailcustomers';

    /*
     * 管理零售户   dosgo
     */
    public static $AdminRetailcustomers = 'AdminRetailcustomers';
    /*
     * 查看区域   dosgo
     */
    public static $CatArea = 'CatArea';
    /*
     * 添加市场类型   dosgo
     */
    public static $AddMarkettype = 'AddMarkettype';
    /*
     * 导出零售户相关
     */
    public static $Exrb = 'Exrb';
    /*
     * 零售户市场检查信息管理
     */
    public static $Checkrbadmin = 'Checkrbadmin';
    /*
     * 专卖类别R值管理
     */
    public static $Radmin = 'Radmin';
    /*
     * 零售户短信服务
     */
    public static $Rbsms = 'Rbsms';
    /*
     * 变动提示短信管理
     */
    public static $Editsmsadmin = 'Editsmsadmin';

    /*
     * 线路管理
     */
    public static $Roadadmin = 'Roadadmin';

    /*
     * 审核流程管理
     */
    public static $Processadmin = 'Processadmin';


    /*
     * 管理市场类型 
     */
    public static $AdminMarkettype = 'AdminMarkettype';

    /*
     * 添加导入区域
     */
    public static $AddArea = 'AddArea';
    /*
     * 管理区域
     */
    public static $AdminArea = 'AdminArea';

    /*
     * 数据汇总 (信息统计)
     */
    public static $InformationStatistics = 'InformationStatistics';
    /*
     * 数据分析
     */
    public static $DataAnalysis = 'DataAnalysis';

    /**
     * 创建和管理计划权限
     */
    public static $AdminPlans = 'AdminPlans';

    /**
     * 创建和管理总结权限
     */
    public static $AdminReports = 'AdminReports';

    /*
     * 查看下属的计划
     */
    public static $CatLowPlan = 'CatLowPlan';
    /*
     * 查看下属的总结
     */
    public static $CatLowReport = 'CatLowReport';
    //零售户测评
    public static $RetailerAlert = 'RetailerAlert';
    //市场测评
    public static $MarketAreaAlert = 'MarketAreaAlert';

    /**
     * 上报数据管理
     */
    public static $Adminreporting = 'Adminreporting';

    /**
     * 上报数据创建
     */
    public static $Createreporting = 'Createreporting';
    
    /**
     * 处理管理
     */
    public static $Admindeal = 'Admindeal';

    /**
     * 处理浏览
     */
    public static $Viewdeal = 'Viewdeal';
    

    public static $AdminAttend = 'AdminAttend';

    public static $AdminPanel = 'AdminPanel';
	
	/**
     * 错误记录
     */
    public static $MobileError = 'MobileError';
    
    /**
     * 电话会议管理
     */
    public static $EcpliveConfig = 'EcpliveConfig';
    

    /*     * ***************烟草 End**************** */

    /**
     * 创建例会
     */
    public static $Createmeetingrule = 'Createmeetingrule';
    
    /*
     * 系统管理的    消息设置的权限   add on 2014-3-27
     */
    public static $MessageSetting = 'MessageSetting'; 

    /*
     * 管理Office控件
     */
    public static $AdminOfficeOnline = 'AdminOfficeOnline'; 
    /*
     * 手机认证管理	//dxf 2014-11-21 15:36:46 旧版本存在，是否移到上一个类？
     */
    public static $PhoneAuth = 'PhoneAuth'; 
    
    function __construct() {
        
    } 
}
