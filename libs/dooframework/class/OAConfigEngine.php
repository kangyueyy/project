<?php

/**
 * page\ajax\api of OAConfigEngine
 *
 * @author james.ou 2017-8-29
 */
class OAConfigEngine {

	/**
	 * BaseController object
	 * @var obj
	 */
	private $controller;

	/**
	 * 当前人身份对象
	 * @var obj
	 */
	private $prm_base;

	/**
	 * 当前数据对象
	 * @var array
	 */
	private $data;

	/**
	 * 当前参数对象
	 * @var array
	 */
	private $params;

	/**
	 * 当前参数对象 针对 reqdata 里面的参数
	 * @var array
	 */
	private $request;

	/**
	 * 当前模块对象
	 * @var array
	 */
	private $module;

	/**
	 * 详情页右侧列表 缓存key
	 * @var string
	 */
	private $relakey;

	/**
	 * 页面版本 默认3.0.0.0
	 * @var str
	 */
	private $version;

	/**
	 * 模块id
	 * @var str
	 */
	private $moduleid;

	/**
	 * 表单id
	 * @var str
	 */
	private $formid;

	/**
	 * 主键 如id,objid,meetid,meetingid之类 每个地方传的不一样，可以在配置文件里面指定，默认id
	 * @var pk
	 */
	private $pk;

	/**
	 * 模块url
	 * @var str
	 */
	private $appurl;

	/**
	 * 主表数据
	 * @var array
	 */
	private $tbdata;

	/**
	 * 当前页面对象
	 * @var array
	 */
	private $page;

	/**
	 * 自定义配置代码
	 * @var array
	 */
	private $config_code;

	/**
	 * 返回值
	 * @var [type]
	 */
	private $ret;

	/**
	 * 静态实例
	 * @var obj
	 */
	private static $_instance;

	/**
	 * 构造
	 * @param obj $controller
	 */
	public function __construct($controller = null)
    {
		$this->init($controller);
	}

	/**
	 * 初始化
	 * @param obj $controller
	 */
	public function init($controller)
    {
		$this->controller = $controller;
		$this->prm_base   = $controller->prm_base;
		$this->data       = isset($controller->data) ? $controller->data : array();
		$this->params     = isset($controller->params) ? $controller->params : array();
		$this->request    = isset($controller->request) ? $controller->request : array();
	}

	/**
	 * 获取静态实例
	 * @param obj $controller
	 */
	public static function getInstance($controller)
    {
		if (empty(self::$_instance))
        {
			self::$_instance = new OAConfigEngine($controller);
		}
        else
        {
			self::$_instance->init($controller);
		}

		return self::$_instance;
	}

	/**
	 * 获取请求对应的参数 by james.ou 2017-8-17
	 * @param string $version
	 * @param string $moduleid
	 * @param string $arg
	 * @return string
	 */
	protected function _get_params($version, $moduleid, $arg)
    {
		if (is_array($arg))                        return $this->_get_params($version, $moduleid, $arg);
		if ($arg == 'tbdata')                      return !empty($this->tbdata) ? $this->tbdata : array();
		if ($arg == 'customapp')                   return !empty($this->customapp) ? $this->customapp : array();
		if ($arg == 'version')                     return $version;
		if ($arg == 'prm_base')                    return $this->prm_base;
		if ($arg == 'moduleid')                    return isset($this->params[$arg]) ? $this->params[$arg] : $moduleid;
		if ($arg == 'baseurl')                     return $this->prm_base->url;
		if (strpos($arg, "EnumClass::") !== false)
		{
			@eval("\$enum = $arg;");
			return $enum;
		}
		if ($arg == 'thisdata')                    return $this->data;
		if ($arg == 'thisparams')                  return $this->params;
		if (isset($this->params[$arg]))            return $this->params[$arg];

		return $arg;
	}

	/**
	 * 调用用户函数 by james.ou 2017-8-18
	 * @param string $class
	 * @param string $function
	 * @param array $params
	 * @param boolean $static
	 * @return ret
	 */
	protected function call_function($class, $function, $params, $static = true)
    {
		if ($static)
        {
			$ret = call_user_func_array(__NAMESPACE__ . "{$class}::{$function}", $params);
		}
        else
        {
			$obj = new $class['class']();
			$ret = call_user_func_array(array(__NAMESPACE__ . "{$class}", $obj->$function), $params);
		}

		return $ret;
	}

	/**
	 * 处理web page请求(index,add/edit,detail)等
	 * @param  [type] $config_code 嵌入代码块
	 * @param  [type] $config      配置
	 * @param  [type] $action      执行的动作名称
	 * @author james.ou 2017-8-8
	 */
	public function run($config_code, $_module, $_page, $action)
    {
		$start_time = microtime(true);
		if (empty($_module) || empty($_page))
        {
			LogClass::log_err("OAConfigEngine_run page or module 异常::module:" . json_encode($_module) . " page:" . json_encode($_module) . " action:" . $action, __FILE__, __LINE__);
			//$this->controller->show_err(false, "TIPS_COM_404");
			return;
		}

		$this->module               = $_module = empty($_module) ? array('pages' => array()) : $_module;
		$this->config_code          = $config_code == false ? array() : $config_code['code'];
		$this->controller->moduleid = $this->moduleid = $_module['moduleid']; //模块ID
		$this->formid               = !empty($_page['formid']) ? $_page['formid'] : $_module['formid']; //表单ID
		$this->appurl               = $this->prm_base->url . $_module['appurl'];
		$this->pk                   = !empty($_module['pk']) ? $_module['pk'] : 'id'; //主键字段
		$this->version              = $_version = !empty($_page['version']) ? $_page['version'] : '3.0.0.0';
		$params                     = isset($_page['params']) ? $_page['params'] : array();

		//执行自定义代码
		$this->exec_my_code();
        //参数预处理
		if (!empty($_page['checkparambefore']))
        {
			$fn_before = $_page['checkparambefore'];
			$before_params = $this->exec_my_function($fn_before);
			if ($before_params['ret'] == RetClass::SUCCESS && !empty($before_params['data']) && is_array($before_params['data'])) {
				$_REQUEST = array_merge($_REQUEST, $before_params['data']);
				$this->controller->request = array_merge($this->controller->request, $before_params['data']);
			}
			unset($before_params);
		}

		//处理参数
		if (!empty($_page['checkparam'])) $this->check_params($_page['checkparam']);

		$this->data['moduleid'] = $params['moduleid'] = $_moduleid = $this->moduleid;
		$this->data['formid']   = $params['formid']   = $formid    = $this->formid;
		$this->data['appurl']   = $this->appurl;
		$this->data['addurl']   = !empty($_page['addurl']) ? $this->prm_base->url . $_page['addurl'] : '';

		if (empty($_page['defurl'])) $_page['defurl'] = '';
		if (!empty($_page['title'])) $this->data['title'] = $_page['title'];

        $this->data['defurl'] = $_page['defurl'] = $this->prm_base->url . $_page['defurl'];

		extract($this->params);

		$this->pk     = !empty($_page['pk']) ? $_page['pk'] : $this->pk;
		$id           = !empty($this->params[$this->pk]) ? $this->params[$this->pk] : '';
		$path         = !empty($path) ? $path : '';
		$page         = !empty($page) ? $page : '';
		$design       = !empty($design) ? $design : '';
		$dataformid   = !empty($dataformid) ? $dataformid : '';
		$pagetype     = !empty($pagetype) ? $pagetype : '';

		if (!empty($objid)) $id = $objid;

		//检查是否需要显示自定义菜单（选项卡之类）
		if (!empty($_page['customapp']))
        {
			$defurl    = $_page['defurl'];
			$customapp = $_page['customapp'];

			if (!empty($customapp['moduleid']) && !empty($this->params['moduleid']))
            {
				$this->moduleid = $customapp['moduleid'] == 'auto' ? $this->params['moduleid'] : $customapp['moduleid'];
				$mod            = ApiClass::enterprise_get_module_path($_version, $this->prm_base, $moduleid);
				$this->controller->show_err($mod['ret'] == RetClass::SUCCESS && !empty($mod['data']['name']));

				$defurl         = "{$this->prm_base->url}web/{$mod['data']['name']}/page/index";
			}

			$this->get_custom_app($this->moduleid, $defurl, $path, $page, $_page['level'], $_page['otherparam'], false, $design);
		}

		$params['table_path'] = !empty($this->params['table_path']) ? $this->params['table_path'] : $path;

		//是否需要检查权限(列表/选项卡) //path 为空是首页（列表页面）
		if (!empty($_page['checkpermit'])) $this->controller->check_permit($_moduleid, $params['table_path'], '', EnumClass::PERMIT_VALIDATION_TYPE_LIST);
		//是否需要检查权限(新建/修改)
		if (!empty($_page['checkpermitadd']))
        {
			//非列表页面权限
			$moduleid = !empty($_page['checkpermitadd']['moduleid']) ? $_page['checkpermitadd']['moduleid'] : $_moduleid;
			if (empty($id))
            {
				$this->controller->check_permit($moduleid, '', EnumClass::PERMIT_ACTIONTYPE_ADD, EnumClass::PERMIT_VALIDATION_TYPE_ADD, '', $_page['defurl']);
			}
            else
            {
				$this->controller->check_permit($moduleid, $path, EnumClass::PERMIT_ACTIONTYPE_EDIT, EnumClass::PERMIT_VALIDATION_TYPE_ACTION, '', $_page['defurl'], '', '', 0, array('actiontype' => EnumClass::PERMIT_ACTIONTYPE_EDIT, 'objid' => $id));
			}
		}

		//是否需要检查权限(查看详情页)
		if (!empty($_page['checkpermitview'])) $this->controller->check_permit($_moduleid, $path, EnumClass::PERMIT_ACTIONTYPE_VIEW, EnumClass::PERMIT_VALIDATION_TYPE_ACTION, '', $_page['defurl'], '', '', 0, array('actiontype' => EnumClass::PERMIT_ACTIONTYPE_VIEW, 'objid' => $id));
		//是否需要检查权限(管理员权限)
		if (!empty($_page['checkpermitadmin'])) $this->controller->check_permit($_moduleid, EnumClass::PERMIT_ACTIONTYPE_APPADMIN, '', EnumClass::PERMIT_VALIDATION_TYPE_LIST);

		//获取表单详情数据
		if (!empty($_page['getmaintbdatabyid']))
        {
			//为特定模块兼容处理，如：超级推送（96757164491813719）
			if (!empty($_page['maintbname']) && strpos($path, $_page['maintbname']['path']) !== false)
            {
				$formdata_ret = BaseClass::get_main_tbdata_by_objid($_version, $this->prm_base, $_moduleid, $id, $_page['maintbname']['tbname']);
			}
            else
            {
				$formdata_ret = FormClass::get_main_tbdata_by_objid($_version, $this->prm_base, $_moduleid, $id);
			}
            // 表单不存在或者记录不存在
			if (!empty($id)) $this->controller->show_err($formdata_ret['ret'] == RetClass::SUCCESS, 'TIPS_HANDLE_RECORD_NOT_EXISTS', '', $_page['defurl']);

			$this->tbdata = $tbdata = !empty($formdata_ret['data']) ? $formdata_ret['data'] : array();
			$formid       = !empty($tbdata['formid']) ? $tbdata['formid'] : 0;

			//获取设置控件
			$this->data['data']['comset'] = BaseClass::get_comset($_version, $this->prm_base, $tbdata);
		}

		//获取表单信息
		if (!empty($_page['getforminfo']))
        {
			if (empty($formid))
            {
				$dataform_ret = FormClass::get_subforms_by_moduleid($_version, $this->prm_base, $_moduleid, EnumClass::STATE_TYPE_NORMAL, $datatype = EnumClass::FORM_DATA_TYPE_FORM, 'get');
				$formid       = isset($dataform_ret['data']['formid']) ? $dataform_ret['data']['formid'] : 0;
			}

			$this->params['formid'] = $formid;
			LogClass::log_trace1('news_formid::', $formid, __FILE__, __LINE__);
			$forminfo               = FormClass::get_forminfo($this->prm_base, $formid);
			LogClass::log_trace1('news_forminfo::', $forminfo, __FILE__, __LINE__);
			$this->data['form']     = $forminfo;
		}

		//获取表单项信息
		if (!empty($_page['getformitems']['class']))
        {
			//是否配置了自定义，获取表单项
			$getformitems_ret = $this->exec_my_function($_page['getformitems']);
			if (!empty($getformitems_ret['data']) && !empty($getformitems_ret['data']['item_com_data'])) $this->data = array_merge($this->data, $getformitems_ret['data']['item_com_data']);
		}
        else
        {
			//兼容之前配置{"getformitems":true}
			if (!empty($formid))
            {
				$_type     = 'view';
				$_pageType = EnumClass::FORM_PAGE_TYPE_VIEW;
				if ($action == 'pageadd')
                {
					$_type     = 'add';
					$_pageType = EnumClass::FORM_PAGE_TYPE_ADD;
				}

				//为特定模块兼容处理，如：超级推送（96757164491813719）
				if ($_moduleid == '96757164491813719' && strpos($path, '96757164491813808') !== false) $formid = '96757164491813852';  //推送明细的表单id

				$res = ApiClass::iweform_get_formitems_all($_version, $this->prm_base, $formid, $id, null, $withsubmit = false, $_type, $_pageType, EnumClass::TERMINAL_TYPE_WEB);
				if (!empty($res['data']) && !empty($res['data']['item_com_data'])) $this->data = array_merge($this->data, $res['data']['item_com_data']);
			}
		}

		// 是否需要获取列表控件
		if (!empty($_page['ngtable']))
        {
			//path 为空是首页（列表页面）
			$ajaxurl            = !empty($params['ajaxurl']) ? $params['ajaxurl'] : 'web/listdata/ajax/getreportlist';
			$params['ajaxurl']  = $this->prm_base->url . $ajaxurl;
			$params['page']     = !empty($_REQUEST['p']) ? $_REQUEST['p'] : 1; //分页页码
			$params['keywords'] = !empty($_REQUEST['k']) ? $_REQUEST['k'] : ''; //搜索关键字
			$params['rule']     = !empty($_REQUEST['r']) ? $_REQUEST['r'] : ''; //高级搜索规则
			$params['viewid']   = !empty($_REQUEST['v']) ? $_REQUEST['v'] : 0; //浏览过的id记录
			//$params['usehistory'] = 1; //使用把分页、搜索等信息保存在url里
			//$params['watermark'] = '公告标题、创建时间、发布人';
			if (!empty($_page['ng_params']))
            {
				//如果开启列表控件参数，则执行用户自定义函数获取参数
				$ng_ret = $this->exec_my_function($_page['ng_params']);
				$ngdata = $ng_ret['ret'] == RetClass::SUCCESS && !empty($ng_ret['data']) ? $ng_ret['data'] : array();
				$params = array_merge($params, $ngdata);
			}

			$this->data['ngtable'] = Doo::app()->getModule('unit/ngtable', 'NgtableController', 'getNgtable', $_version, $this->prm_base, $params);
		}

		//检测引导页
		if (!empty($_page['checkguide']))
        {
			$guide_ret = ApiClass::guide_check_setting($_version, $this->prm_base, $_moduleid, EnumClass::PUBLIC_TYPE_GUIDE_SETTING, $path, 0);
			if ($guide_ret['ret'] == RetClass::SUCCESS)
            {
				$this->data['guide'] = $guide_ret['data']['guide'];  //弹窗
				//$this->data['bubble'] = $guide_ret['data']['bubble']; //气泡
			}
		}

		//是否获取h5参数 -般是h5首页用到
		if (!empty($_page['geth5params']))
        {
			$this->prm_base->terminal = EnumClass::TERMINAL_TYPE_MOBILE;

			$h5_params  = FormClass::get_h5_params($_version, $this->prm_base, $_moduleid, $pagetype = EnumClass::FORM_PAGE_TYPE_LIST, $dataformid, $id, $_module['appurl']);
			$this->data = array_merge($this->data, $h5_params);
		}

		//进详情页需要处理的
		if (!empty($_page['handlerdetail']) && $id > 0)
        {
			//设置已阅
			$_otherparams = array('itemname' => !empty($_page['handlerdetail']['itemname']) ? $_page['handlerdetail']['itemname'] : array());
			FormClass::handel_after_detail($_version, $this->prm_base, $_moduleid, $id, $_otherparams);
		}

		//右侧列表
		if (!empty($_page['rightlist']))
        {
			$pk                  = !empty($_page['rightlist']['pk']) ? $_page['rightlist']['pk'] : 'id';
			$right_list_url      = !empty($_page['rightlist']['url']) ? $_page['rightlist']['url'] : $_module['appurl'] . 'page/pagedetail';
			$params['keyword']   = !empty($_REQUEST['k']) ? $_REQUEST['k'] : ''; //搜索关键字
			$params['condition'] = !empty($_REQUEST['r']) ? urldecode($_REQUEST['r']) : ''; //高级搜索规则
			$page                = !empty($_REQUEST['p']) ? $_REQUEST['p'] : 1; //页码
			$_REQUEST['v']       = $id;
			$transform           = ApiClass::datasource_get_right_list($_version, $this->prm_base, $_moduleid, $path, $page, $id, $pk, $right_list_url, 0, array(), 100, 0, $this->relakey, true, $params);

            $this->controller->show_err($transform['ret'] == RetClass::SUCCESS, 'TIPS_LIST_TITLE_ERROR', '', $_page['defurl']);
			$this->data['rightlist'] = $transform['data'];
		}

		//返回栏
		if (!empty($_page['returnlist']))
        {
			$returnlist = BaseClass::api_get_returnlist($_version, $this->prm_base, $_moduleid, $_REQUEST);

            $this->data['data']['returnlist'] = $returnlist['ret'] == RetClass::SUCCESS ? $returnlist['data'] : '\'\'';
		}

		//标题栏
		if (!empty($_page['titlebar']) && !empty($tbdata))
        {
			$this->data['data']['titlebar'] = call_user_func_array(__NAMESPACE__ . "{$_page['titlebar']['class']}::{$_page['titlebar']['function']}", array($_version, $this->prm_base, $tbdata, array("moduleid" => $_moduleid)));
		}

		//讨论区
		if (!empty($_page['commemtdata']) && !empty($_page['commemtdata']['class']) && class_exists($_page['commemtdata']['class']))
        {
			$commentdata = $_page['commemtdata'];
			$class       = $commentdata['class'];
			$comfunc     = isset($commentdata['function']) ? $commentdata['function'] : '';
			$comsett     = isset($commentdata['comsett']) ? $commentdata['comsett'] : array();
			$tbdata      = isset($tbdata) ? $tbdata : array();
			$otherparams = array('appurl' => $_module['appurl'], 'comsett' => $comsett);

			if(!isset($commentdata['otherparams'])) $commentdata['otherparams'] = array();

			$commentdata['otherparams'] = is_array($commentdata['otherparams']) ? $commentdata['otherparams'] : array();
			foreach ($commentdata['otherparams'] as $v)
            {
				$otherparams[$v] = $this->_get_params($_version, $_moduleid, $v);
				if (strpos($otherparams[$v], '$') !== false)
                {
					@eval("\$str = \"$otherparams[$v]\";");
					$otherparams[$v] = $str;
				}
			}

			$this->data['commemtdata'] = call_user_func_array(__NAMESPACE__ . "{$class}::{$comfunc}", array($_version, $this->prm_base, $_moduleid, $tbdata, $otherparams));
		}

		if (!empty($tbdata) && !empty($_page['comset']))
        {
			//待整理，针对配置函数参数
			//$this->data['data']['comset'] = $_page['comset']['class']::{$_page['comset']['function']}($_version,$this->prm_base,$tbdata);
			$this->data['data']['comset'] = call_user_func_array(__NAMESPACE__ . "{$_page['comset']['class']}::{$_page['comset']['function']}", array($_version, $this->prm_base, $tbdata));

            //特殊设置,只有公告才有的
			$canseeall  = $this->data['data']['comset']['commentattr'] == '1001' ? 1 : 0;
			$commentRet = ApiClass::comment_get_config($_version, $this->prm_base, $_moduleid, $id, 10, 'ASC', $canseeall);

            $this->data['commentConfig'] = '{}';
			if ($commentRet['ret'] == RetClass::SUCCESS) $this->data['commentConfig'] = $commentRet['data'];
		}

		//详情处理表单内容
		if (!empty($_page['handlercontent']))
        {
			$contents = CommonClass::json_decode($this->data['item']['content']);

            $this->data['item']['content'] = !empty($contents['content']) ? $contents['content'] : '';
		}

		//阅读情况
		if (!empty($_page['readlist']) && !empty($tbdata))
        {
			$readret = FormClass::read_list($_version, $this->prm_base, $id, $_moduleid, !empty($_page['readlist']['itemname']) ? $_page['readlist']['itemname'] : '', 'all', 1, 30);
			//未公布的公告没有“再次提醒未阅人员”功能
            if (!empty($readret['data']['remind']) && $tbdata['opstate'] != EnumClass::STATE_TYPE_NORMAL) $readret['data']['remind'] = '';

			$this->data['readlist'] = !empty($readret['data']) ? CommonClass::json_encode($readret['data']) : '';
		}

		//标题
		if (!empty($_page['formtitle']) && !empty($tbdata))
        {
			$formtitle = !empty($tbdata['formtitle']) ? $tbdata['formtitle'] : '';

			$this->data['data']['formtitle']    = CommonClass::json_encode(array('title' => $formtitle));
			$this->data['item']['content_name'] = '';
		}

		//需要传递的模版参数
		if (!empty($_page['t_params']))
        {
			foreach ($_page['t_params'] as $key => $value)
            {
				//如果未赋值，则直接用相同的key的值赋予
				if (empty($value)) $value = !empty($key) ? $key : '';
				if (strpos($value, '$') !== false)
                {
					@eval("\$str = \"$value\";");
					$this->data[$key] = $str;
				}
                else
                {
					$this->data[$key] = $value;
				}
			}
		}

		$this->data['id']             = $id;
		$this->data['objid']          = $id;
		$this->data['path']           = $path;
		$this->data['item']['formid'] = $formid;
		$this->data['nowurl']         = '';
		$this->data['reqdata']        = CommonClass::encode_format($this->prm_base, array('userid' => $this->prm_base->userid, 'entid' => $this->prm_base->entid));

		//如果会话里面有链接，则传值给页面 如:超级推送值导入数据时有用到
		if (isset($_SESSION[$this->prm_base->userid . '_nowurl'])) $this->data['nowurl'] = $_SESSION[$this->prm_base->userid . '_nowurl'];

		//获取另外模块的内容及是否配置了输出，还是传到模版上的标签 by james.ou 2017-8-17
		if (!empty($_page['getmodule']))
        {
			$_content = Doo::app()->getModule($_page['getmodule']['modulename'], $_page['getmodule']['controller'], $_page['getmodule']['action'], $_version, $this->prm_base, $this->data);
			if ($_page['getmodule']['tag'] == 'echo')
            {
				echo $_content;
				return;
			}
            else
            {
				$this->data[$_page['getmodule']['tag']] = $_content;
			}
		}

		//由执行函数改变或指定输出模版 by james.ou 2017-8-18
		if (!empty($this->data['tmp']['template'])) $_page['template'] = $this->data['tmp']['template'];
		//自定义函数
		if (!empty($_page['myfunction']))
        {
			//调用函数
			$fn_ret = $this->exec_my_function($_page['myfunction']);

			if (isset($fn_ret['ret']))
            {
				$this->controller->show_err($fn_ret['ret'] == RetClass::SUCCESS, $fn_ret['attr']['content'], '', $_page['defurl']);
				//保存返回结果
				$this->ret = $fn_ret;

				//有返回数据，则合并输出到$this->data
				if (isset($fn_ret['data']) && !empty($fn_ret['data']) && is_array($fn_ret['data']))
                {
					if(isset($_page['myfunction']['merged']) && $_page['myfunction']['merged'] == false)
                    {
						$this->data = $fn_ret['data'];
					}
                    else
                    {
						$this->data = array_merge($this->data, $fn_ret['data']);
					}
				}
			}
            else
            {
				//否则直接合并
				$this->data = array_merge($this->data, $fn_ret);
			}
		}

		// 是否工厂模式
		if (!empty($_page['checkfactory']))
        {
			$mcache = ApiClass::buildapp_handle_factory_model($_version, $this->prm_base, $_moduleid, $modeval = 1, $this->prm_base->userid);
			if ($mcache['ret'] == RetClass::SUCCESS)
            {
				$this->controller->pageType = EnumClass::PAGE_RET_MSG_TYPE_AJAX;
				if (empty($design))
                {
					$params['moduleid'] = $_moduleid;
					if ($action == 'index')
                    {
						$params['ngtable']    = $this->data['ngtable'];
						$params['desgin_url'] = $_page['defurl'];
					}
                    else
                    {
						$pageaction           = $action == 'pageadd' ? $action : 'pagedetail';
						$params['desgin_url'] = $this->appurl . "page/{$pageaction}?id={$id}&formid={$formid}&path={$path}&design=1";
					}

					$params['desgin_url']     = CommonClass::url_encrypt($params['desgin_url'], array('moduleid'=>$_moduleid, 'path'=>$path, 'design'=>1), EnumClass::PAGE_RET_MSG_TYPE_PAGE);
					echo Doo::app()->getModule('engine/buildapp', 'WebBuildappController', 'main', $_version, $this->prm_base, $params);
					exit;
				}
			}
		}

		LogClass::log_info0('OAConfigEngine_run time:',  microtime(true) - $start_time, __FILE__, __LINE__);
		//输出结果
		$this->send($_page, $formid, $action);

		//如果class有输出退出指令，则退出 by jame.ou 2017-8-28
		if (!empty($this->data['tmp']['exit'])) exit;
	}

	/**
	 * 在数组中查找查找
	 * @param array $arr
	 * @param string $action
	 * @return array
	 */
	public static function find_in_array($arr, $action)
    {
		$_page = false;
		foreach ($arr as $p)
        {
			if (strtolower($p['action']) == strtolower($action))
            {
				$_page = $p;
				break;
			}
		}

		return $_page;
	}

	/**
	 * 执行自定义代码
	 * @return boolean
	 */
	private function exec_my_code()
    {
		//---------------------------------------- dxf 变更部分 ----------------------------------------
		$codeall = '';
		if (!empty($this->page['codes']) && !empty($this->config_code))
        {
			foreach ($this->page['codes'] as $code)
            {
				if (empty($this->config_code[$code['id']]) || empty($this->config_code[$code['id']]['content'])) continue;

				$codeall .= $this->config_code[$code['id']]['content'] . "\r\n";
			}

			if (!empty($codeall)) eval($codeall);
		}
		//======================================== dxf 变更部分 ========================================
		return true;
	}

	/**
	 * 执行自定义函数
	 * @param array $fn
	 */
	private function exec_my_function($fn)
    {
		//自定义参数处理函数
		$args = $fn['args'];
		if (!is_array($args)) $args = array($args);

		$fn_params = array();
		foreach ($args as $arg)
        {
			$fn_params[] = $this->_get_params($this->version, $this->moduleid, $arg);
		}
		//调用函数
		return $this->call_function($fn['class'], $fn['function'], $fn_params, $fn['static']);
	}

	/**
	 * 检查并处理url参数 合并到$this->params里面
	 * @param string $rules
	 */
	private function check_params($rules)
    {
		if (!empty($this->controller->request)) $this->controller->check_params($this->controller->request, $rules);
		if (!empty($_REQUEST)) $this->controller->check_params($_REQUEST, $rules);

		$this->params  = $this->controller->params;
		$this->request = $this->controller->request;
		LogClass::log_info("OAConfigEngine_check_params page 接收 params:" . json_encode($this->params), __FILE__, __LINE__);
		return true;
	}

	/**
	 * 获取自定义选项卡（$this->data['customapp']）
	 * @param string $defurl
	 * @param string $path
	 * @param int $page
	 * @param int $level
	 * @param array $otherparam
	 * @param bool $connect_personal
	 * @param bool $design 是否设计模式
	 */
	private function get_custom_app($moduleid, $defurl, $path, $page, $level, $otherparam = array(), $connect_personal = false, $design = false)
    {
		$customapp = ApiClass::customapp_get_menu($this->version, $this->prm_base, $moduleid, $defurl, $path, $page, $level, $otherparam, $connect_personal, $design);
		$this->controller->show_err($customapp['ret'] == RetClass::SUCCESS, 'TIPS_MENU_ERROR', '');

        $this->data['customapp']    = $customapp['data']['tab_template'];
		$this->params['table_path'] = !empty($customapp['data']['path']) ? $customapp['data']['path'] : $path;
		$this->customapp            = $customapp;

		return $customapp;
	}

	/**
	 * 输出结果
	 * @param array $_page
	 * @param string $action
	 * @return void
	 */
	private function send($_page, $formid, $action)
    {
		if (empty($_page['template']) && !empty($this->data['tmp']['template'])) $_page['template'] = $this->data['tmp']['template'];
		if (!empty($_page['template']))
        {
			//如果配置了模版，直接编译输出指定模版 by james.ou 2017-8-17
			$this->controller->render($this->version, $_page['template'], $this->data);
		}
        else
        {
			//函数返回输出方式 add by james.ou 2017-8-24
			if (!empty($this->data['tmp']['output']))
            {
				if ($this->data['tmp']['output'] == 'json')
                {
					echo CommonClass::json_encode($this->data);
				}
                else
                {
					echo $this->data;
				}
				exit;
			}
			//如果有配置输出 add by james.ou 2017-8-24
			if (!empty($_page['output']))
            {
				if ($_page['output'] == 'json')
                {
					echo CommonClass::json_encode($this->data);
				}
                else
                {
					echo $this->data;
				}
				exit;
			}

			if (($this->controller->pageType == EnumClass::PAGE_RET_MSG_TYPE_AJAX
                    || $this->controller->pageType == EnumClass::PAGE_RET_MSG_TYPE_MOBILE)
                    && empty($_page['gen_pageformid']))
            {
				if (empty($this->ret['ret'])) $this->ret['ret'] = RetClass::ERROR;
                //先注释掉，有种情况是 myfunction 返回空data, 不需要数据 by james.ou 2017-11-1
				//if (empty($this->ret['data'])) $this->ret['data'] = $this->data;	//$this->ret包含整个返回格式，将处理过的$this->data赋值给$this->ret  hky20171018
				return $this->controller->echo_ret($this->ret);
			}

			//如果配置了生成表单规则，直接用配置里面的，否则默认 by james.ou 2017-8-17
			$pagetype = $action == 'pageadd' ? 'add' : 'view';
			if (!empty($_page['gen_pageformid']))
            {
				$gen_pageformid = !empty($_page['gen_pageformid']) ? $_page['gen_pageformid'] : array();
				$pageformid     = !empty($gen_pageformid['pageformid']) ? $gen_pageformid['pageformid'] : 0;

				if (!empty($gen_pageformid['formid']) && strpos($gen_pageformid['formid'], '$') === false) $formid = $gen_pageformid['formid'];
				if (!empty($gen_pageformid['type'])) $pagetype = $gen_pageformid['type'];
			}

			//有可能其他地方需要改变显示的表单id 如:超级推送会根据不同的选项卡来显示不同的表单详情
			//by james.ou 2017-8-17
			if (!empty($this->data['tmp']['formid'])) $formid = $this->data['tmp']['formid'];
			if (empty($pageformid)) $pageformid = FormClass::gen_pageformid($this->version, $this->prm_base, $this->moduleid, $formid, $pagetype);

			$this->controller->render_ent($this->version, $pageformid, $this->data);
		}
	}

	/**
	 * 验证配置数据 2017-8-31
	 * @param array $config
	 * @param array $rules
	 */
	public static function validate($config, $rules = array())
    {
		//todo
		if (!is_array($config) || !array_key_exists('module', $config) || !is_array($config['module'])) LogClass::log_info('module error', __FILE__, __LINE__);

		//判断必选字段
		$necessary_params = array('moduleid', 'formid', 'appurl', 'name', 'description', 'icon', 'class', 'pk', 'pages', 'ajaxs');
		if (!empty($rules)) $necessary_params = array_merge($necessary_params, $rules);

		$necessary_error_params = "";
		foreach ($necessary_params as $value)    if (!array_key_exists($value, $config['module'])) $necessary_error_params.=$value . "|";
		if (strlen($necessary_error_params) > 0) LogClass::log_info("necessary_params error={$necessary_error_params}", __FILE__, __LINE__);

		//判断可选字段
		if (is_array($config['module']['pages']))
        {
			$optional_error_params = "";
			$optional_params       = array('pageid', 'action', 'formid', 'version', 'defurl', 'id', 'level', 'otherparam', 'template', 'customapp', 'checkparam', 'checkpermit',
				'checkpermitview', 'checkpermitadd', 'checkfactory', 'checkguide', 'codes', 'params', 'getforminfo', 'getformitems', 'getmaintbdatabyid', 'handlerdetail',
				'rightlist', 'returnlist', 'titlebar', 'commemtdata', 'readlist', 'formtitle', 'myfunction');

            foreach ($config['module']['pages'] as $value) if (!in_array($value, $optional_params)) $optional_error_params.= json_encode($value) . "|";
			if (strlen($optional_error_params) > 0)        LogClass::log_info("OAConfigEngine_validate[pages] necessary_params error={$optional_error_params}", __FILE__, __LINE__);
		}

		if (is_array($config['module']['ajaxs']))
        {
			$optional_error_params = "";
			$optional_params       = array('action', 'myfunction', 'version', 'checkparam', 'checkpermit');

            foreach ($config['module']['ajaxs'] as $value) if (!in_array($value, $optional_params)) $optional_error_params.= json_encode($value) . "|";
			if (strlen($optional_error_params) > 0)        LogClass::log_info("OAConfigEngine_validate[ajaxs] necessary_params error={$optional_error_params}", __FILE__, __LINE__);

		}
	}

}

?>
