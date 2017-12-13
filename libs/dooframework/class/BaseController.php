<?php

/**
 * BaseController class file.
 * controller的业务基类
 */

/**
 * OA 无Session 整站的Controller基类
 */
class OABaseController extends DooController {

	public $data = null;
	public $from; //数据来源 0=网页,1=安卓客户端,2=ios,3=ipad
	public $time; //时间
	public $formsetid; //formsetid
	public $request;
	public $debug = false;

	/**
	 * 公共参数
	 * @var array
	 */
	public $prm_base;

	public function __construct($params = null) {
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';

		$this->time = time(); //当前时间戳
		$this->params = $params;
		header("Content-type: text/html; charset=utf-8");
		//基本配置
		$this->data['baseurl'] = Doo::conf()->APP_URL;
		$this->data['fileurl'] = Doo::conf()->FILE_URL . $this->templateSkin;
		$this->data['sitename'] = Doo::conf()->SITE_NAME;
		$this->data['docsurl'] = Doo::conf()->DOCS_URL;
		$this->data['wechaturl'] = !empty(Doo::conf()->WECHAT_OA_CN_URL) ? Doo::conf()->WECHAT_OA_CN_URL : 'http://wechat.oa.cn/';
		//系统版本信息
		// $this->data['version'] = '1.2.4';
		$this->data['version'] = CommonClass::txt('TXT_VERSION');
		//渠道地址
		$this->data['channel'] = CommonClass::get_channel();
		//模块ID
		$this->data['moduleid'] = 0;
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'];
	}

	//LJX.20160128 获取系统升级状态(无缓存，待加)
	public function getSysUpgrade() {
		$ret = ApiClass::enterprise_setting_get('3.0.0.0', $this->prm_base, EnumClass::SYS_OPTYPE_SYSUPGRADING);
		return isset($ret['ret']) && $ret['ret'] == RetClass::SUCCESS ? true : false;
	}

	//General.20151231 输出封装
	public function afterRunOutput($routeResult, $data, $template = 'index_base', $version = '3.0.0.0', $unified_login = '') {
		if ($this->autorender === true && ($routeResult === null || ($routeResult >= 200 && $routeResult < 300 && $routeResult != 204))) {
			$this->viewRenderAutomation();
		}

		if (!empty($template) && !empty($data)) {
			//General.20160412 判断pagetype来决定使用什么基础模板
			if (isset($this->pageType) && !empty($this->pageType)) {
				switch ($this->pageType) {
					case EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE:
						$template = 'index_base_noinc'; //无头尾的全页面
						break;
					case EnumClass::PAGE_RET_MSG_TYPE_HTML:
						if (empty($template)) {
							$template = 'index_base_html';
						}
						break;
					case EnumClass::PAGE_RET_MSG_TYPE_PAGE:
					default:
						// $template = 'index_base';  //已经传参数过来，不写死 suson.20160516
						break;
				}
			}
			$this->data['module_content'] = $data;
			$this->data['unified_login'] = $unified_login; // 初始化避免报错,统一登录
			if (isset(Doo::conf()->PROTECTED_FOLDER_ORI) === true) {
				$this->view()->rootViewPath = Doo::conf()->ROOT_VIEW_PATH . Doo::conf()->PROTECTED_FOLDER_ORI;
				//Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
			}
			if (empty($version)) {
				$version = '3.0.0.0';
			}
			$newtemplate = $this->templateSkin . $version . '/' . $template;
			return $this->view()->render($newtemplate, $this->data, null, false, $this->templateSkin, $this->folder, true, 1);
		} else {
			echo $data;
		}
	}

	/**
	 * 统一返回格式(如第一个参数已为包含ret和data的数组，则无需拆分传入，但若包含以外的键和值将会去掉)
	 * General.20150206
	 * $ret 返回类型
	 * $data 返回数据
	 * $type 返回类型
	 * $title 提示标题
	 * $content 提示内容
	 * $setdata 提示设置
	 * $icon 提示图标
	 * $pagetype 返回类型 page/ajax
	 */
	public function ret($ret = RetClass::ERROR, $data = '', $type = EnumClass::RET_TYPE_DATA, $title = '', $content = '', $setdata = '', $icon = 0, $code = 0, $pagetype = null) {
		$return = BaseClass::ret($ret, $data, $type, $title, $content, $setdata, $icon, $code);
		if (is_null($pagetype)) {
			$pagetype = $this->pageType;
		}
		if (empty($type)) {
			$type = EnumClass::RET_TYPE_DATA;
		}
		switch ($pagetype) {
			case EnumClass::PAGE_RET_MSG_TYPE_AJAX:
				return CommonClass::json_encode($return);
				break;
			case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
				//$return['systime'] = time();
				//return json_encode($return);
				// 在返回前清理 代码执行产生的其他信息, 仅输出加密信息 方便调试接口
				$message = ob_get_clean();
				if (trim($message, ' ') && $this->debug) { //客户端接口错误文案放这里， 故去掉空格的字符，防止没返回错误文案  add by suson 20150908
					// 加上debug条件判断，否则客户端都没法接收到正确的content  suson.20161128
					$return['attr']['content'] = $message;
				}
				//return $return;
				return CommonClass::encode($return, $pagetype);
				break;
			case EnumClass::PAGE_RET_MSG_TYPE_CONSOLE:
				//return json_encode($return);
				return CommonClass::encode($return, $pagetype);
				break;
			default:
				return $return;
				break;
		}
	}

	/**
	 * 显示统一返回格式(如第一个参数已为包含ret和data的数组，则无需拆分传入，但若包含以外的键和值将会去掉)
	 * dxf 2015-05-13 14:58:07
	 * modify by ljh 2015-07-07 (add param : $title,$content,$setdata,$icon,$code,$pagetype)
	 * @param  [type] $ret		返回类型
	 * @param  string $data		返回数据
	 * @param  [type] $type		返回类型 page/ajax
	 * @param  string $title	提示标题
	 * @param  string $content	提示内容
	 * @param  string $setdata	提示控件属性attr
	 * @param  [type] $icon		提示控件图标
	 * @param  string $code
	 * @param  [type] $pagetype	返回类型 page/ajax
	 */
	public function echo_ret($ret = RetClass::ERROR, $data = '', $type = EnumClass::RET_TYPE_DATA, $title = '', $content = '', $setdata = '', $icon = 0, $code = 0, $pagetype = null) {
		//General.20150819 数据格式统一转换
		$data = self::format_data($data, $pagetype);
		$return = $this->ret($ret, $data, $type, $title, $content, $setdata, $icon, $code, $pagetype);
		//General.20150806 调试信息
		$this->debug_info($this->request, $return, $pagetype);
		//qiuanxiao 20151219 header调用前有可能有输出
		//header('content-type:text/json; charset=utf-8');
		echo $return;
		return $return;
	}

	/**
	 * 数据格式统一转换
	 * General.20150819
	 */
	public static function format_data($data = array(), $pagetype = null) {
		if (empty($data)) {
			return $data;
		}
		switch ($pagetype) {
			case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
				if (!empty($data)) {
					if (CommonClass::is_json($data)) {
						$data = CommonClass::json_decode($data);
					}
					if (is_array($data)) {
						foreach ($data as $k => $v) {
							if (isset($v['crtime']) && !empty($v['crtime']) && !is_numeric($v['crtime'])) {
								$data[$k]['crtime'] = CommonClass::get_timestamp($v['crtime']);
							}
							if (isset($v['uptime']) && !empty($v['uptime']) && !is_numeric($v['uptime'])) {
								$data[$k]['uptime'] = CommonClass::get_timestamp($v['uptime']);
							}
						}
					}
				}
				break;
			default:
				break;
		}
		return $data;
	}

	/**
	 * 调试信息
	 * General.20150806
	 * $ret 返回类型
	 * $data 返回数据
	 */
	public function debug_info($request = '', $return = '', $pagetype = null) {
		if (is_null($pagetype)) {
			$pagetype = $this->pageType;
		}
		if ($this->debug) {
			switch ($pagetype) {
				case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
				case EnumClass::PAGE_RET_MSG_TYPE_CONSOLE:
					header('content-type:text/html; charset=utf-8');
					echo '<html><body><link rel="stylesheet" href="/t/res/default/script/highlight/monokai.css">
							<script src="/t/res/default/script/highlight/highlight.pack.js"></script>
							<script src="/t/res/default/script/highlight/json2.js"></script><style>body{font-size:14px}</style>';
					echo '<b>请求参数 ==></b>';
					echo '<pre>';
					print_r($request);
					echo '<hr size="1">';
					echo '<b>响应内容 ==></b>';
					echo '<br/>加密串：<br/>';
					echo $return;
					echo '<pre>';
					print_r(CommonClass::decode($return, $pagetype));
					echo '</body></html>';
					exit;
					break;
			}
		}
	}

	/**
	 * 判断模块是否已安装
	 * @author ljh 2015.10.28
	 * @param  string $moduleid	 模块id
	 * @param  string $isret	 是否返回数据
	 * @param  string $errtype	 错误类型 page,ajax,mobile
	 */
	public function check_module_install($moduleid, $isret, $errtype = EnumClass::PAGE_RET_MSG_TYPE_PAGE) {
		if (empty($errtype)) {
			$errtype = $this->pageType;
		}
		$mod_install = ApiClass::enterprise_handle_module_cache('3.0.0.0', $this->prm_base, $moduleid);
		if ($mod_install['ret'] == RetClass::ERROR) {
			if ($isret) {
				return false;
			} else {
				$burl = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : Doo::conf()->APP_URL . 'web/desktop/page/index';
				$this->show_err(false, 'TIPS_MODULE_UNSTALL', $errtype, $burl);
				exit;
			}
		}
		return true;
	}

	/**
	 * [新]权限验证
	 * @author ljh 2015.7.13
	 * @param  string $moduleid		模块id
	 * @param  string $chkobjid		报表id[formitemid]/表单id[formid]，以及EnumClass::PERMIT_ACTIONTYPE_APPADMIN
	 * 		1) 验证新建权限时，传入的是表单id，否则传入报表id;
	 * 		2) 验证新建权限时，当该模块中只有一张表单时【如：申请审批就有多张表单】，此参数须为空;
	 * 		3) 验证"管理员面板"选项卡权限时，可以传入EnumClass::PERMIT_ACTIONTYPE_APPADMIN;
	 * @param  string $actionid		行为、操作id	EnumClass::PERMIT_ACTIONTYPE_ADD等的值（EnumClass::PERMIT_ACTIONTYPE_APPADMIN除外）
	 * @param  string $validatetype	类型
	 * 		const PERMIT_VALIDATION_TYPE_LIST		= 'list';	//列表页权限验证
	 * 		const PERMIT_VALIDATION_TYPE_ADD		= 'add';	//新建权限验证
	 * 		const PERMIT_VALIDATION_TYPE_ACTION		= 'action';	//操作权限验证
	 * @param  string $msg			提示内容
	 * @param  string $defurl	 默认跳转地址
	 * @param  string $checktype 权限验证类型 PERMIT_CHECKTYPE_MODULE[模块]/PERMIT_CHECKTYPE_ACTION[功能]
	 * @param  string $errtype	 错误类型 page,ajax,mobile
	 * @param  string $isret	 是否返回 1/0
	 * @param  string $data		 参数，如果$data为空或者$data[‘actiontype’]不存在，则不进行check_logic()验证，数组格式，如：array('actiontype'=>PERMIT_ACTIONTYPE_VIEW,'objid'=>$objid)
	 * @param  string $permitret	引用，原先返回bool值，不清楚具体的提示而返回false，固增加此变量 suson.20170115
	 */
	public function check_permit($moduleid, $chkobjid = '', $actionid = '', $validatetype = '', $msg = '', $defurl = '', $checktype = '', $errtype = '', $isret = 0, $data = array(), &$permitret = array()) {
		// 默认验证类型[模块验证/操作验证]
		if (empty($checktype)) {
			$checktype = EnumClass::PERMIT_CHECKTYPE_ACTION;
		}
		// 默认错误类型[page/ajax]
		if (empty($errtype)) {
			$errtype = $this->pageType;
			// $errtype = EnumClass::PAGE_RET_MSG_TYPE_PAGE;
		}
		// 默认提示信息
		if (empty($msg)) {
			$msg = 'TIPS_ACCESS_ERROR';
		}
		$isret = (bool) $isret;
		// 默认跳转地址
		// $mod = ApiClass::enterprise_get_module_path('3.0.0.0', $this->prm_base, $moduleid);
		if (!$isret && empty($defurl)) {
			if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
				$defurl = $_SERVER['HTTP_REFERER'];
			}
			// else if ($mod['ret'] == RetClass::SUCCESS) {
			// 	$defurl = $this->prm_base->url.'web/'.$mod['data']['name'].'/page/index';
			// }
			else {
				$defurl = Doo::conf()->APP_URL . 'web/desktop/page/index';
			}
		}

		// 检查模块是否已安装
		$mod_install = $this->check_module_install($moduleid, $isret, $errtype);
		if ($isret && false == $mod_install) {
			return $mod_install;
		}

		$permit = false;
		// 判断权限验证类型
		switch ($checktype) {
			// 验证模块权限
			case EnumClass::PERMIT_CHECKTYPE_MODULE:
				if (!empty($moduleid)) {
					$ret = ApiClass::permit_check('3.0.0.0', $this->prm_base, $moduleid, $checktype);
					if ($ret['ret'] == RetClass::SUCCESS) {
						$permit = true;
					}
				}
				break;
			// 验证功能权限
			case EnumClass::PERMIT_CHECKTYPE_ACTION:
			default:
				extract($data);
				if (!isset($objid)) {
					$objid = '';
				}
				//业务状态的处理 如详情页，创建人直接进编辑页，其它人则提示TIPS_HANDLE_RECORD_DRAFT记录已撤回！ suson.20170109
				$api_params['objid'] = $objid;
				$api_params['chkobjid'] = $chkobjid;
				$api_params['actionid'] = $actionid;
				$api_params['validatetype'] = $validatetype;
				$api_params['data'] = $data;
				$function = 'permit_check_base_logic';
				$base_logic_ret = BaseClass::do_module_api('3.0.0.0', $this->prm_base, $moduleid, $function, $api_params);

				if ($base_logic_ret['ret'] != RetClass::SUCCESS) {
					$permit = false;
					$permitret = $base_logic_ret;
					if ($isret) {
						return $permit;
					} else {
						$msg = !empty($base_logic_ret['attr']['content']) ? $base_logic_ret['attr']['content'] : $msg;
						$this->show_err($permit, $msg, $errtype, $defurl);
					}
				}

				if (!empty($moduleid) && (!empty($actionid) || !empty($chkobjid) || ($validatetype == EnumClass::PERMIT_VALIDATION_TYPE_ACTION && !empty($objid)))) {
					if (!empty($chkobjid)) {
						$chkobjid_arr = explode(',', $chkobjid);
						$chkobjid = $chkobjid_arr[0];
						$fields[] = array('key'=>'moduleid','value'=>$moduleid);
						$fields[] = array('key'=>'formitemid','value'=>$chkobjid);
						$fromitem = FormClass::get_formitem_by_fields('3.0.0.0', $this->prm_base, $fields, 'code', 'get');
						if (!empty($formitem['code'])) {
							$chkobjid = $formitem['code'];
						}
					}
					$ret = ApiClass::permit_check('3.0.0.0', $this->prm_base, $moduleid, $checktype, $validatetype, $chkobjid, $actionid, $objid);
					if ($ret['ret'] == RetClass::SUCCESS) {
						$permit = true;
					}
				}
				// 验证基本逻辑权限
				if (!is_array($data)) {
					$data = (array) $data;
				}
				if (false == $permit && function_exists('api_check_logic')) {
					if (!empty($data['objid']) && !empty($data['actiontype'])) {
						$api_params['actiontype'] = $data['actiontype'];
						$ret = BaseClass::do_module_api('3.0.0.0', $this->prm_base, $moduleid, 'check_logic', $api_params);
						if ($res['ret'] == RetClass::SUCCESS) {
							$permit = true;
						}
					}
				}
				break;
		}
		if ($isret) {
			return $permit;
		} else {
			$this->show_err($permit, $msg, $errtype, $defurl);
		}
	}

	//操作处理
	public function handle_action($moduleid, $chkobjid = '', $actiontype = '', $validatetype = '', $defurl = '', $data = array()) {
		//执行某个操作

		$errtype = $this->pageType;


		if (empty($defurl)) {
			if (!empty($_SERVER['HTTP_REFERER'])) {
				$defurl = $_SERVER['HTTP_REFERER'];
			} else {
				$defurl = Doo::conf()->APP_URL . 'web/desktop/page/index';
			}
		}
		//执行操作
		$ret = ActionClass::handle_action('3.0.0.0', $this->prm_base, $moduleid, $chkobjid, $actiontype, $validatetype, $data);
		if ($ret['ret'] == RetClass::ERROR) {
			$this->show_err(false, $ret['attr']['content'], $errtype, $defurl);
		}

		return $ret;
	}

	/**
	 * 显示提示页面
	 * ljh 2016-01-11
	 */
	public function showMsg() {
		if (!isset($this->reqdata) || empty($this->reqdata)) {
			CommonClass::redirect(Doo::conf()->OA_CN_URL); //跳转OA首页
		}
		$data = $this->reqdata;
		$content = '';
		if (!isset($data['attr']['content'])) {
			$content = CommonClass::txt('TIPS_COM_404'); //页面不存在提示
		} else {
			$content = $data['attr']['content'];
		}
		$url = '';
		if (isset($data['attr']['setdata']['url'])) {
			$url = $data['attr']['setdata']['url'];
		} else {
			$url = Doo::conf()->OA_CN_URL;
		}

		$this->data['gourl'] = $url;
		$this->data['title'] = $data['attr']['title'];
		$this->data['content'] = $content;
		$this->data['code'] = $data['code'];
		/* $this->data['showbtn'] = true;
		  if ($this->prm_base->terminal != EnumClass::TERMINAL_TYPE_WEB) {//无效
		  $this->data['showbtn'] = false;
		  } */
		//var_dump($data);exit;
		$this->render('3.0.0.0', 'error', $this->data);
	}

	/**
	 * 设置配置文件 需要子类实现
	 * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
	 */
	protected function getConfigFileName() {
		return '';
	}

	/**
	 * 获取代码配置文件 需要子类实现
	 * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
	 */
	protected function getCodeFileName(){
		return '';
	}

	/**
	 * 获取controller 对象
	 * @return $this
	 */
	protected function getController(){
		return $this;
	}

	/**
	 * 这里增集中页面page请求处理(new)
	 * @author james.ou 2017-8-10
	 */
	public function page() {
		$this->process('pages');
	}

	/**
	 * 这里增集中页面ajax处理(new)
	 * @author james.ou 2017-8-10
	 */
	public function ajax() {
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_AJAX;
		$this->process('ajaxs');
	}

	/**
	 * 这里增集中页面api处理(new)
	 * api接口 by james.ou 2017-8-17
	 */
	public function api() {
		$this->process('apis');
	}

	/**
	 * 执行处理
	 * @author james.ou 2017-8-17
	 * @param str $nodeName
	 */
	private function process($nodeName){
		$start_time = microtime(true);
		$action = isset($this->params['action']) ? $this->params['action'] : '';
		$config = BaseClass::readConfig($this->getConfigFileName());
		$config_code = BaseClass::readConfig($this->getCodeFileName());
		$module = $config == false ? array($nodeName => array()) : $config['module'];
		$pages = is_array($module[$nodeName]) ? $module[$nodeName] : array($module[$nodeName]);

		OAConfigEngine::validate($config);
		$page = OAConfigEngine::find_in_array($pages, $action);
		$configEngine = new OAConfigEngine($this->getController());
		$configEngine->run($config_code, $module, $page, $action);

		LogClass::log_info0('controller process time:',  microtime(true) - $start_time, __FILE__, __LINE__);
	}

}

/**
 * OA 控制台API接口 Controller基类
 * General.20150924
 */
class OAConsoleAPIController extends OABaseController {

	public $request;

	public function __construct($params = null) {
		parent::__construct($params);
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_CONSOLE;
		$this->params = $params;
		$this->init_reqdata();
		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setTerminal(isset($this->request['terminal']) ? $this->request['terminal'] : EnumClass::TERMINAL_TYPE_CONSOLE); //终端类型
		$this->prm_base->setUserid(isset($this->request['userid']) ? $this->request['userid'] : 0);
		$this->prm_base->setEntid(isset($this->request['entid']) ? $this->request['entid'] : 0);
	}

	/**
	 * 初始化请求数据
	 */
	public function init_reqdata() {
		$data = '';

		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
		} else if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
		} else if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
		}
		//General.20150806 调试模式
		if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
			$this->debug = true;
		}
		$data = str_replace(' ', '+', $data); //浏览器测试api需要 add by suson 20150518
		if ($this->debug && strpos(urlencode($data), '%') == false) {
			$data = str_replace('2F', '%2F', $data);
			$data = str_replace('0A', '%0A', $data);
			$data = str_replace('3D', '%3D', $data);
			$data = str_replace('2B', '%2B', $data);
			$data = urldecode($data);
		}
		if (!empty($data)) {
			$data = CommonClass::decode($data, $this->pageType); //解密数据
		}
		$this->request = $data;
		return;
	}

}

/**
 * OA 整站的API接口 Controller基类
 */
class OAAPIController extends OABaseController {

	public $request;

	public function __construct($params = null) {
		parent::__construct($params);
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_MOBILE;
		// Doo::loadClass('UserCommonClass');
		// Doo::loadClass('OaExceptionClass');
		$this->params = $params;
		$this->reqdata = $this->init_reqdata();

		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setUserid(isset($this->request['userid']) ? $this->request['userid'] : 0);
		$this->prm_base->setEntid(isset($this->request['entid']) ? $this->request['entid'] : 0);

		$retData = ApiClass::datasource_mobile_get_info($version = '3.0.0.0', $this->prm_base);
		$this->prm_base->setProfid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['profid']) ? $retData['data']['profid'] : 0);
		$this->prm_base->setUsername($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['profname']) ? $retData['data']['profname'] : '');
		$this->prm_base->setUserDeptid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['deptid']) ? $retData['data']['deptid'] : 0);
		$this->prm_base->setUserDeptname($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['deptname']) ? $retData['data']['deptname'] : '');
		$this->prm_base->setUserPosnid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['posnid']) ? $retData['data']['posnid'] : 0);
		$this->prm_base->setUserPosnname($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['posnname']) ? $retData['data']['posnname'] : '');
		$this->prm_base->setEntno($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['entno']) ? $retData['data']['entno'] : 0);
		$this->prm_base->setEntname($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['entname']) ? $retData['data']['entname'] : '');
		$this->prm_base->setOAno($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['oano']) ? $retData['data']['oano'] : 0);

		$this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
		$this->prm_base->setFileUrl(isset($this->data['fileurl']) ? $this->data['fileurl'] : '/');
		$this->prm_base->setChannel(isset($this->data['channel']) ? $this->data['channel'] : '/'); //渠道
		$this->prm_base->setVersion(isset($this->data['version']) ? $this->data['version'] : '/'); //系统版本
		$this->prm_base->setTerminal(isset($this->request['terminal']) ? $this->request['terminal'] : EnumClass::TERMINAL_TYPE_MOBILE); //终端类型
		$this->prm_base->setPuserid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['puserid']) ? $retData['data']['puserid'] : 0);
		$this->prm_base->setOacnurl(Doo::conf()->OA_CN_URL);

		$this->check_hashkey();
	}

	/**
	 * 获取request里请求数据
	 * @param string $key
	 * @param string|int|float $defValue
	 * @return obj
	 */
	public function get($key, $defValue = '') {
		return isset($this->request[$key]) ? $this->request[$key] : $defValue;
	}

	/**
	 * @name 检查用户
	 * @date 2014-5-7
	 * @author james.ou
	 */
	public function check_user() {
		if (empty($this->request['userid']) || empty($this->request['entid'])) {
			//echo self::ret(RetClass::COM_NO_PERMISSION);
			$this->echo_ret(RetClass::COM_NO_PERMISSION);
			exit;
		}
	}

	/**
	 * 初始化请求数据
	 */
	public function init_reqdata() {
		//$data = $this->getgpc('input', 'R');
		$data = '';

		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
		} else if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
		} else if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
		}
		//General.20150806 调试模式
		if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
			$this->debug = true;
		}
		$data = str_replace(' ', '+', $data); //浏览器测试api需要 add by suson 20150518
		if ($this->debug && strpos(urlencode($data), '%') == false) {
			$data = str_replace('2F', '%2F', $data);
			$data = str_replace('0A', '%0A', $data);
			$data = str_replace('3D', '%3D', $data);
			$data = str_replace('2B', '%2B', $data);
			$data = urldecode($data);
		}
		//LogClass::log_trace1('手机请求解密前',$data,__FILE__,__LINE__);
		if (!empty($data)) {
			$decrypttype = isset($this->decrypttype) ? $this->decrypttype : ''; //这里报错了，做个容错处理 HCW 2017.02.07
			//$data = CommonClass::decode($data, $this->pageType, $this->decrypttype);//解密数据
			$data = CommonClass::decode($data, $this->pageType, $decrypttype); //解密数据
		}
		// 判断prm_base变量是否是对象类型 ljh 2016-04-11
		if (isset($data['prm_base']) && !is_object($data['prm_base'])) {
			$data['prm_base'] = (object) $data['prm_base'];
		}
		//LogClass::log_trace1('手机请求解密后',$data,__FILE__,__LINE__);
		$this->request = $data;
		return $data;
	}

	public function check_hashkey() {

		$cid = isset($this->request['cid']) ? $this->request['cid'] : '';
		if (!empty($cid)) {
			$ret = ApiClass::iweform_check_hashkey($version = '3.0.0.0', $this->prm_base, $cid);
			if ($ret['ret'] != RetClass::SUCCESS) {
				$this->echo_ret(RetClass::SUCCESS);
			}
		}
	}

	/**
	 * 设置配置文件 需要子类实现
	 * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
	 */
	protected function getConfigFileName() {
		return 'mod.api';
	}

	/**
	 * 获取代码配置文件 需要子类实现
	 * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
	 */
	protected function getCodeFileName(){
		return 'code.api';
	}

	/**
	 * 获取controller 对象
	 * @return $this
	 */
	protected function getController(){
		return $this;
	}
}

/**
 * OAuth2.0 api接口 controller基类
 */
class OAuthAPIcontroller extends OABaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		$this->params = $params;
		$this->reqdata = $this->init_reqdata();
		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setUserid(isset($this->request['userid']) ? $this->request['userid'] : 0);
		$this->prm_base->setEntid(isset($this->request['entid']) ? $this->request['entid'] : 0);
		$this->prm_base->scope = isset($this->request['scope']) ? $this->request['scope'] : '';
	}

	public function init_reqdata() {
		//$data = $this->getgpc('input', 'R');
		$access_token = '';

		if (isset($_GET['access_token'])) {
			$access_token = $_GET['access_token'];
		} else if (isset($_POST['access_token'])) {
			$access_token = $_POST['access_token'];
		} else if (isset($_REQUEST['access_token'])) {
			$access_token = $_REQUEST['access_token'];
		}

		if (empty($access_token)) {
			// return false;
			$this->simple_ret(RetClass::COM_NOT_EMPTY);
			die();
		}

		//验证access_token
		$postdata = array('access_token' => $access_token);
		$ret = CommonClass::get_remote_data(Doo::conf()->OA_CN_URL . 'yii/web/res', $postdata, $header = '', 0, 0, 1);

		$ret = is_null(CommonClass::json_decode($ret)) ? '' : CommonClass::json_decode($ret);
		if ($ret['error']) {
			$this->simple_ret(RetClass::ERROR, $ret);
			die();
		}

		$session_userid = isset($ret['userid']) ? $ret['userid'] : null;
		$session_entid = isset($ret['entid']) ? $ret['entid'] : null;

		//查oauth库
		Doo::db()->reconnect('oauth');
		$select = 'user_id,scope';
		$sql = "SELECT {$select} FROM oauth_access_tokens WHERE access_token = '{$access_token}'";
		$ret = Doo::db()->fetchRow($sql);
		if (!isset($ret['user_id']) || empty($ret['user_id'])) {
			return false;
		}
		$data = CommonClass::decode($ret['user_id']);
		$data['scope'] = $ret['scope'];
		//换回库
		Doo::db()->reconnect('db_oa');
		$this->request = $data;
		return $data;
	}

	/**
	 * 显示统一返回格式(如第一个参数已为包含ret和data的数组，则无需拆分传入，但若包含以外的键和值将会去掉)
	 * @param  [type] $ret		返回类型
	 * @param  string $data		返回数据
	 */
	public function simple_ret($ret = RetClass::ERROR, $data = '') {
		$return['ret'] = RetClass::ERROR;
		$return['data'] = '';
		if (empty($ret)) {
			return $return;
		}

		if (is_array($ret)) {
			if (isset($ret['ret']) && isset($ret['data'])) { //ret和data都存在
				$return['ret'] = $ret['ret'];
				$return['data'] = $ret['data'];
			} else if (isset($ret['ret']) && !isset($ret['data'])) {//ret存在且data不存在
				$return['ret'] = $ret['ret'];
			} else if ((!isset($ret['ret']) && !isset($ret['data'])) || (!isset($ret['ret']) && isset($ret['data']))) {//ret和data都不存在或data存在ret不存在，可能是空数组或是其他结构的数组
				$return['ret'] = RetClass::ERROR;
			}
		} else if (is_object($ret)) {//如果是对象
			$return['ret'] = RetClass::ERROR;
		} else {
			$return['ret'] = $ret;
			//只要存在不管为不为空都返回，控制器输出到手机时才可能要精简掉
			if (isset($data)) {
				$return['data'] = $data;
			}
		}
		$return = CommonClass::json_encode($return);
		echo $return;
		return $return;
	}

}

/**
 * 日程(小程序)重写OAAPIController基类
 */
class SmallProController extends OAAPIController {

	public function __construct($params = null) {
		parent::__construct($params);
		$data = $_REQUEST['reqdata'];
		$data = str_replace(' ', '+', $data);
		if ($this->debug && strpos(urlencode($data), '%') == false) {
			$data = str_replace('2F', '%2F', $data);
			$data = str_replace('0A', '%0A', $data);
			$data = str_replace('3D', '%3D', $data);
			$data = str_replace('2B', '%2B', $data);
			$data = urldecode($data);
		}
		if (!empty($data)) {
			$data = CommonClass::decode($data, $this->pageType); //解密数据
		}
		LogClass::log_trace1('myoa', $data['puserid'], __FILE__, __LINE__);
		if (empty($data['puserid'])) {
			return self::ret(RetClass::ERROR, '', '', '', '平台用户id不能为空');
		}
		$this->prm_base->userid = $data['puserid'];
	}

}

// 微信接口基类，不继承OABaseController lyj 2017-07-06
class WxBaseController extends DooController {

	// protected $params;
	// protected $pageType;
	public $request; //reqdata的数据
	public $prm_base; //当前操作者信息

	public function __construct($params = null) {
		$this->params = $params;
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_NONE;
		$this->prm_base = new PrmBaseClass();
	}

	// 输出数据方法
	public function echo_ret($ret = RetClass::ERROR, $data = '', $type = EnumClass::RET_TYPE_DATA, $title = '', $content = '', $setdata = '', $icon = 0, $code = 0, $pagetype = null) {
		if (empty($pagetype)) {
			$pagetype = $this->pageType;
		}
		$arr = BaseClass::ret($ret, $data, $type, $title, $content, $setdata, $icon, $code);
		$str = CommonClass::json_encode($arr); //把数组转JSON字符串
		switch ($pagetype) {
			case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
			case EnumClass::PAGE_RET_MSG_TYPE_NONE:
				$str = CommonClass::str_encode($str); //加密数据
				break;
		}
		ob_end_clean(); //清空缓冲区并关闭输出缓冲
		echo $str;
		LogClass::log_trace1('WxBaseController_echo_ret 输出的数据::', $arr, __FILE__, __LINE__);
		exit;
	}

}

// 手机端微信小程序接口基类（不含登录态验证） lyj 2017-06-23
class WxaappBaseController extends WxBaseController {

	// protected $params;
	// protected $pageType;
	// protected $request; //reqdata的数据
	// protected $prm_base; //当前操作者信息

	public function __construct($params = null) {
		parent::__construct($params);
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_MOBILE;
		$this->_init_reqdata();
	}

	// 请求数据处理
	private function _init_reqdata() {
		// 小程序前端提交的都是post请求、reqdata数据加密
		if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
			$str = CommonClass::str_decode($data); //解密数据
			$arr = CommonClass::json_decode($str); //把JSON字符串转为数组

			if (!isset($arr['userid'])) {
				$this->echo_ret(RetClass::ERROR, '', '', '', '非法请求1'); //请求参数中没有userid
			}
			$this->prm_base->userid = isset($arr['userid']) ? $arr['userid'] : ''; //额外附加的必备参数，当前操作者
			$this->prm_base->apptype = isset($arr['apptype']) ? $arr['apptype'] : 0; //额外附加的必备参数，当前小程序
			$this->prm_base->appver = isset($arr['appver']) ? $arr['appver'] : '1.0.0'; //额外附加的必备参数，当前小程序的代码版本
			Doo::conf()->PRM_BASE = $this->prm_base;
		} else {
			$this->echo_ret(RetClass::ERROR, '', '', '', '非法请求2');
		}

		$this->request = isset($arr['params']) ? $arr['params'] : array(); //实际提交的数据
		LogClass::log_trace1('WxaappBaseController_init_reqdata 接收的数据::', $this->request, __FILE__, __LINE__);
	}

}

// 手机端微信小程序接口基类（含登录态验证） lyj 2017-04-14
class WxaappController extends WxaappBaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		Doo::loadClass('wxaapp/WxCommonClass');
		Doo::loadClass('wxaapp/WxUserClass');
		Doo::loadModuleClass('weapp/wxlogin', 'WxLoginClass');
		Doo::loadModuleClass('weapp/wxgroups', 'WxGroupsClass');
		Doo::loadModuleClass('weapp/wxmsg', 'WxMsgClass');
		$this->_check_login();
	}

	// 检查登录态
	private function _check_login() {
		if (empty($this->prm_base->userid)) {
			$this->echo_ret(RetClass::ERROR, '', '', '', '非法用户1'); //请求参数userid为空
		}
		$params = array('version' => '3.0.0.0', 'prm_base' => $this->prm_base);
		$wxlogin = new WxLoginClass($params);
		$cacheVal = $wxlogin->cache_sessionid('check'); //通过获取sessionid检查登录态
		if (empty($cacheVal)) {
			$this->echo_ret(RetClass::COM_USER_AUTH_FAILED, '', '', '', '登录超时，请重新登录'); //sessionid缓存不存在
		} elseif ($this->prm_base->userid != $cacheVal['userid']) {
			$this->echo_ret(RetClass::ERROR, '', '', '', '非法用户2'); //请求参数的userid和缓存的不一致
		}
	}

}

// 服务端微信小程序接口基类（非手机端调用的接口） lyj 2017-07-06
class ServerWxaappController extends WxBaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_NONE;
		$this->_init_reqdata();
	}

	// 请求数据处理
	private function _init_reqdata() {
		if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
			$str = CommonClass::str_decode($data); //解密数据
			$arr = CommonClass::json_decode($str); //把JSON字符串转为数组
		} else {
			$arr = $_REQUEST;
		}

		$this->request = !empty($arr) ? $arr : array();
		LogClass::log_trace1('ServerWxaappController_init_reqdata 接收的数据::', $this->request, __FILE__, __LINE__);
	}

}

// 网页端微信小程序接口基类（不含登录态验证） lyj 2017-06-28
class WebWxaappBaseController extends OABaseController { //使用OABaseController里的方法，所以继承OABaseController
	// protected $params;
	// protected $pageType;
	// protected $request; //reqdata的数据
	// protected $prm_base; //当前操作者信息

	public function __construct($params = null) {
		parent::__construct($params);
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_PAGE;
		$this->prm_base = new PrmBaseClass();
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		$this->_init_reqdata();
	}

	// 请求数据处理
	private function _init_reqdata() {
		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
			$str = CommonClass::str_decode($data); //解密数据
			$arr = CommonClass::json_decode($str); //把JSON字符串转为数组
		} elseif (isset($_SERVER['CONTENT_TYPE']) && strtolower($_SERVER['CONTENT_TYPE']) == 'application/json;charset=utf-8') {
			$arr = json_decode(file_get_contents('php://input'), true);
		}

		$this->request = !empty($arr) ? $arr : array();
		// LogClass::log_trace1('WxWebController_init_reqdata 接收的数据::', $this->request, __FILE__, __LINE__);
	}

	// 重写输出数据方法
	public function echo_ret($ret = RetClass::ERROR, $data = '', $type = EnumClass::RET_TYPE_DATA, $title = '', $content = '', $setdata = '', $icon = 0, $code = 0, $pagetype = null) {
		if (empty($pagetype)) {
			$pagetype = $this->pageType;
		}
		$arr = BaseClass::ret($ret, $data, $type, $title, $content, $setdata, $icon, $code);
		$str = CommonClass::json_encode($arr); //把数组转JSON字符串
		switch ($pagetype) {
			case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
			case EnumClass::PAGE_RET_MSG_TYPE_NONE:
				$str = CommonClass::str_encode($str); //加密数据
				break;
		}
		ob_end_clean(); //清空缓冲区并关闭输出缓冲
		echo $str;
		// LogClass::log_trace1('WebWxaappBaseController_echo_ret 输出的数据::', $arr, __FILE__, __LINE__);
		exit;
	}

	// 重写输出模板方法
	public function afterRunEx($routeResult, $data) {
		$this->afterRunOutput($routeResult, $data, 'index_base_wxaapp', '3.0.0.0');
	}

}

// 网页端微信小程序接口基类（含登录态验证） lyj 2017-06-27
class WebWxaappController extends WebWxaappBaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		$this->_check_login(); //查询登录session状态，把用户信息写入prm_base
	}

	private function _check_login() {
		session_start();
		if (empty($_SESSION['wxauser']['userid'])) {
			CommonClass::redirect('/web/wxscan/page/index');
		} else {
			$this->prm_base->setUserid($_SESSION['wxauser']['userid']);
		}
	}

}

/**
 * OA 无Session Controller基类
 */
class OAWebUnitController extends OABaseController {

	public function __construct($params = null) {
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		Doo::loadClass('WebUnitClass');
		// $this->prm_base = new PrmBaseClass();
		// $this->prm_base->setUno(isset(UserCommonClass::session()->userid) ? UserCommonClass::session()->userid : 0);
		// $this->prm_base->setEno(isset(UserCommonClass::session()->entid) ? UserCommonClass::session()->entid : 0);
		// $this->prm_base->setUsername(isset($this->data["currentuser"]->username) ? $this->data["currentuser"]->username : 0);
		// $this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
		$this->params = $params;

		$this->data['component'] = $this->params['component'];
		$this->data['component_id'] = $this->params['component_id'];
		$this->data['component_name'] = $this->params['component_name'];
		$this->data['component_value'] = $this->params['component_value'];
		$this->data['component_width'] = $this->params['component_width'];
		$this->data['component_isrepeat'] = $this->params['component_isrepeat'];
		$this->data['component_validator'] = $this->params['component_validator'];
		$this->data['component_tpltype'] = $this->params['component_tpltype'];
		$this->data['component_params'] = $this->params['component_params'];
	}

}

/**
 * OA 无Session Controller基类 lyj 2016-06-20
 */
class OAHtmlUnitController extends OABaseController {

	public function __construct($params = null) {
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->folder = 'mobile/';
		Doo::loadClass('HtmlUnitClass');
		$this->params = $params;

		$this->data['component'] = $this->params['component'];
		$this->data['component_id'] = $this->params['component_id'];
		$this->data['component_name'] = $this->params['component_name'];
		$this->data['component_value'] = $this->params['component_value'];
		$this->data['component_width'] = $this->params['component_width'];
		$this->data['component_isrepeat'] = $this->params['component_isrepeat'];
		$this->data['component_validator'] = $this->params['component_validator'];
		$this->data['component_tpltype'] = $this->params['component_tpltype'];
		$this->data['component_params'] = $this->params['component_params'];
	}

}

/**
 * OA 企业端基类 Controller基类
 * General.20150323
 */
class OAEnterpriseBaseController extends OABaseController {

	protected $reqdata;
	protected $tkdata = '';  //临时身份数据token
	protected $tkcode = '';  //绑定资源类型     //update liaoan  20170330 添加

	public function __construct($params = null) {
		parent::__construct($params);
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_NONE;
		$this->folder = 'web/'; //General.20150205 默认模板类型目录
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		Doo::loadClass('CommonClass');
		$this->prm_base = new PrmBaseClass();
		$this->params = $params;
		$this->reqdata = $this->init_reqdata();
		$this->reqdata['relakey'] = !empty($this->reqdata['relakey']) ? $this->reqdata['relakey'] : ''; //关联缓存key 获取关联右侧列表 suson.20160309
		// $this->init_reqdata();
	}

	public function init_reqdata() {
		//$data = $this->getgpc('input', 'R');
		$data = '';

		// 解析angularjs POST过来的数据
		if (isset($_SERVER['CONTENT_TYPE']) &&
				strtolower($_SERVER['CONTENT_TYPE']) == 'application/json;charset=utf-8') {
			$params = json_decode(file_get_contents('php://input'), true);
			if ($params) {
				foreach ($params as $key => $value) {
					$_POST[$key] = $value;
					// 不要覆盖REQUEST里面的数据,在程序里有地方用到$_REQUEST
					if (!isset($_REQUEST[$key])) {
						$_REQUEST[$key] = $value;
					}
				}
			}
		}
		$debug = !empty($_REQUEST['debug']) ? $_REQUEST['debug'] : 0;
		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
			if (!empty($data)) {
				$data = $_REQUEST = $_GET = CommonClass::decode($_GET['reqdata'], EnumClass::PAGE_RET_MSG_TYPE_PAGE); //解密数据
			}
		} else if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
			if (!empty($data)) {
				$data = $_REQUEST = $_POST = CommonClass::decode($_POST['reqdata'], EnumClass::PAGE_RET_MSG_TYPE_PAGE); //解密数据
			}
		} else if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
			if (!empty($data)) {
				$data = $_REQUEST = CommonClass::decode($_REQUEST['reqdata'], EnumClass::PAGE_RET_MSG_TYPE_PAGE); //解密数据
			}
		}

		if (isset($_REQUEST['tkdata'])) {
			$tkdata = $_REQUEST['tkdata'];
			//update liaoan  20170330 用于国投跳转登录
			if (isset($_REQUEST['tkcode']) && !empty($_REQUEST['tkcode'])) {
				$tkcode = $_REQUEST['tkcode'];
			} else {
				$tkcode = '';
			}
			switch ($tkcode) {
				case EnumClass::TP_GT_BINDUSER:
					// if(!session_id()){
					// 	session_start();
					// }
					// var_dump($_SESSION);exit;
					// if(!empty($_SESSION['guotousaveuserid'])){
					// $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
					// $url=strtok($url,'?');
					// $backurl=$_SERVER['HTTP_REFERER'];
					// CommonClass::Redirect2($backurl);
					// }
					// $time=time();
					// $backurl='192.168.0.177:8408/check_oa.asp?action=ok';
					// /web/issue/page/index?tkdata=eyJ1c2VyaWQiOjEsInRpbWUiOjE0OTA3OTM2MDQsInRva2VuIjpkMTVkMzc4OWY3MzU2YzI0ZDgyMmFmMTY4MGEwZDcyOX0=&tkcode=10013005
					// $token='142'.$time.'IJKLMNOPQR0123456789ABCDEFGHSTUVWXYZabcdefghijklmnopqrstuvwxyz';
					// $token=base64_encode(md5($token));
					// $tkdata='{"userid":"142","time":"'.$time.'","token":"'.$token.'"}';
					// $ret = Doo::getModuleClass('tp/10013005','GuotouClass', 'login_check','3.0.0.0', $this->prm_base, array('tkdata'=>$tkdata);
					// //暂时先都跳
					// if($ret['ret']!=RetClass::SUCCESS){
					// 	// header('location:'.$ret['data']);
					// 	$this->show_err(false, '身份验证不通过',EnumClass::PAGE_RET_MSG_TYPE_NONE);
					// }else{
					// 	header('location:'.$ret['data']);
					// }
					break;
				default:
					if (!empty($tkdata)) {
						$this->tkdata = CommonClass::decode($tkdata, EnumClass::PAGE_RET_MSG_TYPE_PAGE); //解密数据
						//update liaoan  20170330 添加
						$this->tkcode = $tkcode;
					}
					break;
			}
		}

		/* if (isset($_GET['reqdata'])) {
		  $data = $_GET['reqdata'];
		  }else if(isset($_POST['reqdata'])) {
		  $data = $_POST['reqdata'];
		  }else if(isset($_REQUEST['reqdata'])){
		  $data = $_REQUEST['reqdata'];
		  }
		  if(!empty($data)){
		  $data = CommonClass::decode($data, EnumClass::PAGE_RET_MSG_TYPE_PAGE);//解密数据
		  } */
		if (isset($debug)) {
			$data['debug'] = $debug;
		}

		return $data;
	}

}

/**
 * OA 企业端 Controller基类
 */
class OAEnterpriseController extends OAEnterpriseBaseController {

	public function __construct($params = null) {
		if (isset(Doo::conf()->LOGIN_CHECK)) {
			//指定额外登录验证方法
			$version = '3.0.0.0';
			$prm_base = null;
			$params = array();
			$ret = Doo::getModuleClass(Doo::conf()->LOGIN_CHECK[0], Doo::conf()->LOGIN_CHECK[1], Doo::conf()->LOGIN_CHECK[2], $version, $prm_base, $params);
			if ($ret['ret'] != RetClass::SUCCESS) {
				exit();
			}
		}
		parent::__construct($params);
        
		$upgrade = $this->getSysUpgrade(); //LJX 2016-01-19 获取系统升级状态
		$this->show_err(!$upgrade, '系统升级中', EnumClass::PAGE_RET_MSG_TYPE_NONE); //LJX 2016-01-19
		
        Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_PAGE;

		if (!empty($_POST['pageType'])) {
			$this->pageType = $_POST['pageType']; //lyj 2016-11-16 18:06:35 异步刷新参数
		}

		$this->folder = 'web/'; //General.20150205 默认模板类型目录
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		$this->prm_base = new PrmBaseClass();
        
		if (!empty($this->tkdata)) {
			//临时入口，直接指定身份
			$tkdata = CommonClass::cache_op('get', EnumClass::CACHE_KEY_PERSONAL, 0, 0, $this->tkdata['token']);
		
			if (is_array($tkdata)) {
				$this->prm_base = $tkdata['prm_base'];
				if ($this->tkdata['userid'] != $this->prm_base->userid || $this->tkdata['token_keep'] != $tkdata['token_keep'] || $this->tkdata['timetamp'] != $tkdata['timetamp']) {
					$this->show_err(false, 'tkdata验证不通过', EnumClass::PAGE_RET_MSG_TYPE_NONE);
				}
				if ($tkdata['check_time'] == true && (time() - $tkdata['timetamp'] > 60)) {
					//超过60秒，超时
					$this->show_err(false, 'tkdata已过期', EnumClass::PAGE_RET_MSG_TYPE_NONE);
				}
				// var_dump($this->prm_base);exit();
				if (!isset($tkdata['token_keep']) || $tkdata['token_keep'] != true) {
					//主动删除tkdata cache，避免多次请求
					//注意：大部分页面又多次请求（含getModule），如果第一次访问即删除，可能会导致整个页面无法访问
					CommonClass::cache_op('del', EnumClass::CACHE_KEY_PERSONAL, 0, 0, $this->tkdata['token']);
				}
				Doo::conf()->PRM_BASE = $this->prm_base;

				if ($tkdata['session_make'] == true) {
					//生成session，一般情况下，不生成session
					CommonClass::session()->puserid = $this->prm_base->puserid;
					CommonClass::session()->userid = $this->prm_base->userid;
					CommonClass::session()->profid = $this->prm_base->profid;
					CommonClass::session()->oano = $this->prm_base->oano;
					CommonClass::session()->entid = $this->prm_base->entid;
					CommonClass::session()->entno = $this->prm_base->entno;
					CommonClass::session()->entname = $this->prm_base->entname;
					CommonClass::session()->profname = $this->prm_base->username;
					CommonClass::session()->roles = $this->prm_base->roles;
					CommonClass::session()->deptid = $this->prm_base->deptid;
					CommonClass::session()->deptname = $this->prm_base->deptname;
					CommonClass::session()->posnid = $this->prm_base->posnid;
					CommonClass::session()->posnname = $this->prm_base->posnname;

					$_SESSION['ent_user']['profid'] = $this->prm_base->profid;
					$_SESSION['ent_user']['profname'] = $this->prm_base->username;
				}
			}
		} else {
			//权限验证
            Doo::loadClass('UserCommonClass');
			UserCommonClass::Common();
			//Doo::loadClass('PermissionClass');
			$this->prm_base->setUserid(isset(CommonClass::session()->userid) ? CommonClass::session()->userid : 0);
			$this->prm_base->setProfid(isset(CommonClass::session()->profid) ? CommonClass::session()->profid : 0);
			$this->prm_base->setOAno(isset(CommonClass::session()->oano) ? CommonClass::session()->oano : 0);
			$this->prm_base->setEntid(isset(CommonClass::session()->entid) ? CommonClass::session()->entid : 0);
			$this->prm_base->setEntno(isset(CommonClass::session()->entno) ? CommonClass::session()->entno : 0);
			$this->prm_base->setEntname(isset(CommonClass::session()->entname) ? CommonClass::session()->entname : '');
			$this->prm_base->setUsername(isset(CommonClass::session()->profname) ? CommonClass::session()->profname : '');
			$this->prm_base->setUserRoles(isset(CommonClass::session()->roles) ? CommonClass::session()->roles : '');
			$this->prm_base->setUserDeptid(isset(CommonClass::session()->deptid) ? CommonClass::session()->deptid : 0);
			$this->prm_base->setUserDeptname(isset(CommonClass::session()->deptname) ? CommonClass::session()->deptname : 0);
			$this->prm_base->setUserPosnid(isset(CommonClass::session()->posnid) ? CommonClass::session()->posnid : 0);
			$this->prm_base->setUserPosnname(isset(CommonClass::session()->posnname) ? CommonClass::session()->posnname : 0);
			$this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
			$this->prm_base->setFileUrl(isset($this->data['fileurl']) ? $this->data['fileurl'] : '/');
			$this->prm_base->setChannel(isset($this->data['channel']) ? $this->data['channel'] : '/'); //渠道
			$this->prm_base->setVersion(isset($this->data['version']) ? $this->data['version'] : '/'); //系统版本
			$this->prm_base->setTerminal(EnumClass::TERMINAL_TYPE_WEB); //终端类型
			$this->prm_base->setOacnurl(Doo::conf()->OA_CN_URL);
			$this->prm_base->setPuserid(isset(CommonClass::session()->puserid) ? CommonClass::session()->puserid : 0);
		}

        $this->params = $params;
		$this->data['theme_style'] = $this->_getTheme();
	}

	//General.20160106 获取当前模块url
	public function getCurrModuleUrl() {
		$this->data['current_url'] = '';
		//获取当前模块url
		$current_url = explode('/', $_SERVER['REQUEST_URI']);
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if ((isset($current_url[3]) && !empty($current_url[3])) || (isset($current_url[2]) && !empty($current_url[2]))) {
			if (strpos($agent, "MSIE 8.0") !== false) {//判断是否为IE8
				$this->data['current_url'] = $current_url[3];
			} else {
				$this->data['current_url'] = $current_url[2];
			}
		}
		//return $this->data;
	}

	//General.20160106 获取个人端验证信息(有跨站调用，耗时，待改进)
	private function _getPersonVerify() {
		//General.20150528 获取个人端验证信息
		$this->data['verify'] = array();
        //直接从当前session取公司信息 by james.ou 2017-11-7
        $userinfo = CommonClass::session()->puserinfo;
        
        if ($userinfo) {
			$puserid = $userinfo['puserid'];
			$opdata = $userinfo['opdata'];
			$ret = ApiClass::user_platform_state('3.0.0.0', $this->prm_base, $puserid, $opdata);
			if (isset($ret['ret']) && $ret['ret'] == RetClass::SUCCESS) {
				$this->data['verify'] = $ret['data'];
			}
		}
		//return $verify;
	}

	//General.20160106 获取个人头像(无缓存，待加)
	private function _getPersonLogo() {
		$this->data['person_logourl'] = array();
		$ret = ApiClass::file_get_person_logo('3.0.0.0', $this->prm_base, $this->prm_base->entid, array($this->prm_base->userid));
		if (isset($ret['ret']) && $ret['ret'] == RetClass::SUCCESS) {
			$this->data['person_logourl'] = isset($ret['data'][$this->prm_base->userid]) ? $ret['data'][$this->prm_base->userid] : $this->data['person_logourl'];
		}
		//return $person_logourl;
	}

	//General.20160106 界面风格(已加缓存)
	private function _getTheme() {
		$theme_style = '';
		$theme = ApiClass::person_get_theme('3.0.0.0', Doo::conf()->PRM_BASE);
		if (isset($theme['ret']) && $theme['ret'] == RetClass::SUCCESS) {
			if (isset($theme['data'])) {
				if (!empty($theme['data'])) {
					$theme_style = $theme['data'] == 'default' ? '' : '_' . $theme['data'];
				}
			}
		}
		return $theme_style;
	}

	//General.20160106 判断手机客户端是否显示
	private function _chkClient() {
		$this->data['check_client'] = false;
		$ret = ApiClass::enterprise_sob_chk('3.0.0.0', $this->prm_base);
		if (isset($ret['ret']) && $ret['ret'] == RetClass::SUCCESS) {
			$this->data['check_client'] = true;
		}
	}

//	//General.20160106 获取模块列表(有缓存)
//	public function getModuleList() {
//		//获取模块列表
//		//$permit_module_list = array();
//		$this->data['module_list'] = array();
//        //1.good 该方法有缓存
//		$ret = ApiClass::enterprise_get_ent_modulelist('3.0.0.0', $this->prm_base);
//		LogClass::log_trace1('afterRunEx1_getModuleList_enterprise_get_ent_modulelist:', Doo::benchmark(), __FILE__, __LINE__);
//		if (isset($ret['ret']) && $ret['ret'] == RetClass::SUCCESS) {
//			$permit_module_list = $ret['data'];
//			//获取个人模块列表设置(有缓存)
//			$res = Apiclass::person_get_modulelist('3.0.0.0', $this->prm_base, $permit_module_list);
//			LogClass::log_trace1('afterRunEx1_getModuleList_person_get_modulelist:', Doo::benchmark(), __FILE__, __LINE__);
//			if ($res['ret'] == RetClass::SUCCESS) {
//				$module_list = $res['data']['module_list'];
//				//$this->data['current_url'] = $res['data']['current_url'];
//				//判断左侧模块列表的相关模块的权限(有缓存)
//				$this->chkLeftPermit($module_list);
//				LogClass::log_trace1('afterRunEx1_getModuleList_chkLeftPermit:', Doo::benchmark(), __FILE__, __LINE__);
//				$this->chkAdvancedAuth($this->data['module_list'], 'left'); //左侧模块中，检测是否为高级功能  ljh 2017-3-9
//			}
//			//权限检查(有缓存)
//			$this->chkModulePermit($permit_module_list);
//			LogClass::log_trace1('afterRunEx1_getModuleList_chkModulePermit:', Doo::benchmark(), __FILE__, __LINE__);
//			$this->chkAdvancedAuth($permit_module_list); //顶端模块中，检测是否为高级功能  ljh 2017-3-9
//		}
//	}
//
//	//检测是否为高级功能  ljh 2017-3-9
//	public function chkAdvancedAuth($modulelist, $showtype = '') {
//		switch ($showtype) {
//			case 'left':
//				$moduleid = EnumClass::MODULE_NAME_MOBILEDEVICE;
//				if (isset($modulelist[$moduleid])) {
//					//检测是否为高级功能  ljh 2017-3-9
//					$model = new BaseClass();
//					$mobidece_adv = $model->check_advanced_auth('3.0.0.0', $this->prm_base, $moduleid);
//					if (false == $mobidece_adv) {
//						unset($modulelist[$moduleid]);
//					}
//				}
//				$this->data['module_list'] = $modulelist;
//				break;
//			case 'top':
//			default:
//				$advanced_auth = CommonClass::cache_op('get', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, $this->prm_base->userid, 'ADVANCEDAUTH_TOP');
//				if (false == $advanced_auth) {
//					$advanced_auth = array();
//					$model = new BaseClass();
//					foreach ($modulelist as $v) {
//						switch ($v['moduleid']) {
//							case EnumClass::MODULE_NAME_MOBILEDEVICE:
//								$advanced_auth['mobiledevice'] = $model->check_advanced_auth('3.0.0.0', $this->prm_base, EnumClass::MODULE_NAME_MOBILEDEVICE);
//								break;
//							case EnumClass::MODULE_NAME_PERMIT:
//								$advanced_auth['staffpermit'] = $model->check_advanced_auth('3.0.0.0', $this->prm_base, EnumClass::MODULE_NAME_PERMIT);
//								break;
//							default:
//								break;
//						}
//					}
//					CommonClass::cache_op('set', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, $this->prm_base->userid, 'ADVANCEDAUTH_TOP', $advanced_auth);
//				}
//				$this->data['advauth_top'] = $advanced_auth;
//				break;
//		}
//	}
//
//	//General.20160106 判断左侧模块列表的相关模块的权限
//	public function chkLeftPermit($module_list) {
//		$cachekey = EnumClass::CACHE_KEY_PERSONAL.$this->prm_base->userid.'MODULE_LIST';
//		$module_list_ret = CommonClass::cache_op('get', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, EnumClass::CACHE_KEY_PERMIT, $cachekey);
//		if (!empty($module_list_ret)) {
//			$module_list = $module_list_ret;
//		} else {
//			// 判断左侧模块列表的相关模块的权限
//			foreach ($module_list as $k => $v) {
//				// 判断是否是左侧的模块列表，不是则unset掉
//				if (isset($v['leftmenu']) && $v['leftmenu'] != 1) {
//					unset($module_list[$k]);
//					continue;
//				}
//				// 判断左侧模块列表的相关模块是否有权限，没有权限，则unset掉
//				//General.20151210 排除分组的权限检查
//				if ($module_list[$k]['moduletype'] != EnumClass::APP_TYPE_GROUP && $module_list[$k]['moduletype'] != EnumClass::APP_TYPE_SYSADMIN) {
//					$res_permit = $this->check_permit($v['moduleid'], '', '', '', '', '', EnumClass::PERMIT_CHECKTYPE_MODULE, '', 1);
//					if (false == $res_permit) {
//						unset($module_list[$k]);
//						continue;
//					}
//				}
//				$module_list[$k]['url'] = CommonClass::url_encrypt($v['url'], array('moduleid'=>$v['moduleid']));
//			}
//			CommonClass::cache_op('set', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, EnumClass::CACHE_KEY_PERMIT, $cachekey, $module_list);
//		}
//		$module_list = BaseClass::remove_invalid_module_group('3.0.0.0', $this->prm_base, $module_list);
//		$this->data['module_list'] = $module_list;
//	}
//
//	//General.20160106 顶部权限检查
//	public function chkModulePermit($permit_module_list) {
//		$cachekey = EnumClass::CACHE_KEY_PERSONAL.$this->prm_base->userid.'MODULE_LIST_TOP';
//		$permit_top = CommonClass::cache_op('get', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, EnumClass::CACHE_KEY_PERMIT, $cachekey);
//		if (empty($permit_top)) {
//			$permit_top = array();
//			$permitroleRet = ApiClass::permitrole_check_user('3.0.0.0', $this->prm_base);
//			$permitrole = $permitroleRet['ret'] == RetClass::SUCCESS ? $permitroleRet['data'] : array();
//			$permit_top['weixin'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//			foreach ($permit_module_list as $k => $v) {
//				$res_permit = $this->check_permit($v['moduleid'], '', '', '', '', '', EnumClass::PERMIT_CHECKTYPE_MODULE, '', 1);
//				switch ($v['moduleid']) {
//					// 内部邮件
//					case EnumClass::MODULE_NAME_PMS:
//						$permit_top['pms'] = (!empty($permitrole) && ($permitrole['boss'] || $permitrole['admin'])) || $res_permit;
//						break;
//					// 同事录
//					case EnumClass::MODULE_NAME_COLLEAGUE:
//						$permit_top['colleague'] = true;
//						$permit_top['module_name'] = CommonClass::txt('TXT_COLLEAGUE');
//						//获取enterprise_module的名字作为模块名  hky20161221
//						$ret = ApiClass::enterprise_get_module_info('3.0.0.0', $this->prm_base, $v['moduleid'], 'mname', 'get', $this->prm_base->entid);
//						if ($ret['ret'] == RetClass::SUCCESS) {
//							$permit_top['module_name'] = $ret['data']['mname'];
//						}
//						break;
//					// 系统设置
//					case EnumClass::MODULE_NAME_SYSSET:
//						$permit_top['sysset'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						$permit_top['baseset'] = $permit_top['sysset'];
//						break;
//					// 系统角色
//					case EnumClass::MODULE_NAME_PERMITROLE:
//						$permit_top['permitrole'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					// 权限管理
//					case EnumClass::MODULE_NAME_PERMIT:
//						$permit_top['permit'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					// 账号管理
//					case EnumClass::MODULE_NAME_SYS:
//						$permit_top['sys'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						$permit_top['account'] = /* $permit_top['posn'] = */ $permit_top['sys']; //去掉员工职务入口   hky20170206
//						break;
//					// 移动设备授权管理
//					case EnumClass::MODULE_NAME_MOBILEDEVICE:
//						$permit_top['mobiledevice'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					case '96826120728477766'://系统日志
//						$permit_top['syslog'] = $res_permit && !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					//管理首页
//					case '96844998821019649':
//						$permit_top['sysindex'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					// appstore
//					case EnumClass::MODULE_NAME_APPSTORE:
//						$permit_top['appstore'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					case '97130657615970314':
//						$permit_top['security'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					case '97149456587161603':
//						$permit_top['security'] = !empty($permitrole) && ($permitrole['boss'] || $permitrole['admin']) ? true : false;
//						break;
//					default:
//						break;
//				}
//			}
//			// //如果权限管理为true 且 移动设备授权管理为true，则移动设备授权管理为true lyj 2016-04-21
//			// if (!empty($permit_top['permit']) && !empty($permit_top['mobiledevice'])) {
//			// 	$permit_top['mobiledevice'] = true;
//			// } else if (isset($permit_top['mobiledevice'])) {
//			// 	$permit_top['mobiledevice'] = false;
//			// }
//			CommonClass::cache_op('set', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, EnumClass::CACHE_KEY_PERMIT, $cachekey, $permit_top);
//		}
//		$this->data['permit_top'] = $permit_top;
//	}
//
//	//General.20160115 获取企业列表和当前企业(未有缓存，有问题，待改进)
//	public function getEntInfo() {
//
//		//每个用户的数据都存在一个缓存key中，修改公司的时候方便统一管理 suson.20190824
//		$ent_infos = CommonClass::cache_op('get', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, 0, 'ENT_INFO');
//		//获取企业列表
//		// $ent_info = CommonClass::cache_op('get',EnumClass::CACHE_KEY_PERSONAL,$this->prm_base->entid,$this->prm_base->userid,'ENT_INFO');
//		$key = 'u_' . $this->prm_base->userid;
//		$ent_info = !empty($ent_infos[$key]) ? $ent_infos[$key] : false;
//		if (false != $ent_info) {
//			$current_entinfo = $ent_info['current_entinfo'];
//			$ent_list = $ent_info['ent_list'];
//		} else {
//			$ent_list = array();
//			//$this->data['ent_list'] = array();
//            //先取消选择公司列表，前端可以用ajax获取 by james.ou 2017-11-7
////			$ret = ApiClass::enterprise_get_name_url('3.0.0.0', $this->prm_base);
//			if (isset($ret['ret']) && $ret['ret'] == RetClass::SUCCESS) {
//				//$this->data['ent_list'] = $ret['data'];
//				$ent_list = $ret['data'];
//			}
//			$current_entinfo = CommonClass::session()->entinfo;
//            
//			//foreach($this->data['ent_list'] as $v){
//			foreach ($ent_list as $v) {
//				if ($v['entid'] == $this->prm_base->entid) {
//					$current_entinfo = $v;
//					break;
//				}
//			}
//			// $this->data['current_entinfo'] = empty($current_entinfo) ? '' : CommonClass::json_encode($current_entinfo);
//			// $this->data['ent_list'] = !empty($this->data['ent_list']) ? CommonClass::json_encode($this->data['ent_list']) : CommonClass::json_encode(array());
//			$ent_info['current_entinfo'] = $current_entinfo;
//			$ent_info['ent_list'] = $ent_list;
//			if (!is_array($ent_infos)) {
//				$ent_infos = array();
//			}
//			$ent_infos[$key] = $ent_info;
//			// CommonClass::cache_op('set',EnumClass::CACHE_KEY_PERSONAL,$this->prm_base->entid,$this->prm_base->userid,'ENT_INFO',$ent_info,20);
//			CommonClass::cache_op('set', EnumClass::CACHE_KEY_ENTERPRISE, $this->prm_base->entid, 0, 'ENT_INFO', $ent_infos, 20);
//		}
//		//add by jimswoo 20170116 start 增加判断是否是 企业是否绑定微信企业号
//		// $is_weixin_ent_bind = Apiclass::weixin_get_bindcorp('3.0.0.0', $this->prm_base, $this->prm_base->entid, EnumClass::TP_BINDTYPE_WEIXIN);
//
//		
//	}

    /**
     * 获取当前公司信息 modify by james.ou 2017-11-9
     * 取消切换公司的获取
     */
    private function _getEntInfo(){
        $current_entinfo = CommonClass::session()->entinfo;
        // 检查是否关联企业微信
		$is_weixin_ent_bind = Apiclass::suite_check_is_wxcorp('3.0.0.0', $this->prm_base, $this->prm_base->entid);
		$this->data['is_weixin_bind'] = (isset($is_weixin_ent_bind['ret']) && $is_weixin_ent_bind['ret'] == RetClass::SUCCESS ) ? 1 : 0;

		// var_dump('weixin@:',$is_weixin_ent_bind);//exit;
		// add by jimswoo 20170116 end
		$this->data['current_entinfo'] = empty($current_entinfo) ? '' : CommonClass::json_encode($current_entinfo);
		$this->data['ent_list'] = array();
    }
	public function afterRunEx($routeResult, $data) {
		LogClass::log_trace1('afterRunEx1:', Doo::benchmark(), __FILE__, __LINE__);
		switch ($this->pageType) {
			case EnumClass::PAGE_RET_MSG_TYPE_PAGE://普通页面请求
				//General.20160106 获取当前模块url
				$this->getCurrModuleUrl();
				LogClass::log_trace1('afterRunEx1_getCurrModuleUrl:', Doo::benchmark(), __FILE__, __LINE__);
				//General.20160106 获取个人端验证信息 dxf 2016-11-16 14:16:43 不需要获取此数据
				$this->_getPersonVerify();
				// LogClass::log_trace1('afterRunEx1_getPersonVerify:', Doo::benchmark(),__FILE__,__LINE__);
				//General.20160106 获取个人头像
				$this->_getPersonLogo();
				LogClass::log_trace1('afterRunEx1_getPersonLogo:', Doo::benchmark(), __FILE__, __LINE__);
				//General.20160113 判断手机客户端是否显示
				$this->_chkClient();
				//LogClass::log_trace1('afterRunEx1_chkClient:', Doo::benchmark(),__FILE__,__LINE__);
				//General.20160115 获取企业列表和当前企业
				$this->_getEntInfo();
				LogClass::log_trace1('afterRunEx1_getEntInfo:', Doo::benchmark(), __FILE__, __LINE__);
				break;
			case EnumClass::PAGE_RET_MSG_TYPE_AJAX://页面Ajax请求
				break;
			default:
				break;
		}

		//General.20160113 界面风格
		/* 统一登录 更新到个人端有问题临时撤回 */
		$unified_login = '';
		$unified_login_ret = ApiClass::setting_get('3.0.0.0', $this->prm_base, EnumClass::TP_UNIFIE_LOGIN, '97133917429039105', 0, 0, '', $this->prm_base->entid, null, null);
		if ($unified_login_ret['ret'] == RetClass::SUCCESS) {
			$unified_login = Doo::app()->getModule('app/m97133917429039105', 'WebM97133917429039105Controller', 'get_unified_login', '3.0.0.0', $this->prm_base, $params = array());
		}
		// 判断该企业是否有统一登录
		LogClass::log_trace1('afterRunEx1:', Doo::benchmark(), __FILE__, __LINE__);
		$this->afterRunOutput($routeResult, $data, 'index_base'.$this->data['theme_style'], '3.0.0.0', $unified_login);
		LogClass::log_trace1('afterRunEx2:', Doo::benchmark(), __FILE__, __LINE__);
		//return $this->view()->render($template, $this->data, null, false,	$this->templateSkin, $this->folder, true, 1);
	}

    /**
	 * 设置配置文件
	 * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
	 */
	protected function getConfigFileName() {
		return 'mod';
	}
    /**
     * 获取代码配置文件名称
     * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
     */
    protected function getCodeFileName(){
        return 'code';
    }
    /**
     * 获取当前controller 对象
     * @return $this
     */
    protected function getController(){
        return $this;
    }

}

/**
 * OA 自定义APP Controller基类
 */
class OACustomappController extends OAEnterpriseController {

	public function __construct($params = null) {
		parent::__construct($params);
	}
}

/**
 * OA 个人端基类 Controller基类
 * General.20150323
 */
class OAPersonalBaseController extends OABaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_PAGE;
		$this->folder = 'web/'; //General.20150205 默认模板类型目录
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		Doo::loadClass('CommonClass');
		$this->params = $params;
		$this->init_reqdata();

		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setTerminal(!empty($_REQUEST['terminal']) ? $_REQUEST['terminal'] : EnumClass::TERMINAL_TYPE_WEB); //终端类型
		$upgrade = $this->getSysUpgrade(); //LJX 2016-01-19 获取系统升级状态
		$this->show_err(!$upgrade, '系统升级中', EnumClass::PAGE_RET_MSG_TYPE_NONE); //LJX 2016-01-19
	}

	public function init_reqdata() {
		// 解析angularjs POST过来的数据
		if (isset($_SERVER['CONTENT_TYPE']) &&
				strtolower($_SERVER['CONTENT_TYPE']) == 'application/json;charset=utf-8') {
			$params = json_decode(file_get_contents('php://input'), true);
			if ($params) {
				foreach ($params as $key => $value) {
					$_POST[$key] = $value;
					// 不要覆盖REQUEST里面的数据,在程序里有地方用到$_REQUEST
					if (!isset($_REQUEST[$key])) {
						$_REQUEST[$key] = $value;
					}
				}
			}
		}
	}

}

/**
 * OA 个人端手机基类 Controller基类
 * General.20150323
 */
class OAPersonalHtmlController extends OABaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_HTML;
		$this->folder = 'mobile/'; //General.20151231 默认模板类型目录
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		Doo::loadClass('CommonClass');
		$this->params = $params;
		$this->init_reqdata();

		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setPuserid(isset(CommonClass::session()->puserid) ? CommonClass::session()->puserid : 0);
		$this->prm_base->setUserid(isset($this->request['userid']) ? $this->request['userid'] : 0);
		$this->prm_base->setTerminal(isset($this->request['terminal']) && !empty($this->request['terminal']) ? $this->request['terminal'] : EnumClass::TERMINAL_TYPE_MOBILE); //终端类型
		//$this->prm_base->setUsername(isset(CommonClass::session()->username) ? CommonClass::session()->username : '');
		$this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
	}

	/**
	 * 初始化请求数据
	 */
	public function init_reqdata() {
		$data = '';

		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
		} else if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
		} else if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
		} else {
			$data = $_REQUEST;
		}
		//General.20150806 调试模式
		if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
			$this->debug = true;
		}
		$data = str_replace(' ', '+', $data); //浏览器测试api需要 add by suson 20150518
		if ($this->debug && strpos(urlencode($data), '%') == false) {
			$data = str_replace('2F', '%2F', $data);
			$data = str_replace('0A', '%0A', $data);
			$data = str_replace('3D', '%3D', $data);
			$data = str_replace('2B', '%2B', $data);
			$data = urldecode($data);
		}
		if (!empty($data)) {
			$data = CommonClass::decode($data, $this->pageType); //解密数据
		}
		$this->request = $data;
		return;
	}

	public function afterRunEx($routeResult, $data) {
		$this->afterRunOutput($routeResult, $data, 'index_base_html', '3.0.0.0');
	}

}

/**
 * OA H5 Controller基类
 * dxf 2017-01-13 13:46:57
 */
class OAHtmlController extends OABaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_HTML;
		$this->folder = 'mobile/'; //General.20151231 默认模板类型目录
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		// Doo::loadClass('CommonClass');
		$this->params = $params;
		$this->init_reqdata();
		$this->prm_base = new PrmBaseClass();
	}

	public function afterRunEx($routeResult, $data) {
		$this->afterRunOutput($routeResult, $data, 'index_base_html', '3.0.0.0');
	}
	/**
	 * 初始化请求数据
	 */
	public function init_reqdata() {
		$data = '';

		// 解析angularjs POST过来的数据
		if (isset($_SERVER['CONTENT_TYPE']) &&
				strtolower($_SERVER['CONTENT_TYPE']) == 'application/json;charset=utf-8') {
			$params = json_decode(file_get_contents('php://input'), true);
			if ($params) {
				foreach ($params as $key => $value) {
					$_POST[$key] = $value;
					// 不要覆盖REQUEST里面的数据,在程序里有地方用到$_REQUEST
					if (!isset($_REQUEST[$key])) {
						$_REQUEST[$key] = $value;
					}
				}
			}
		}

		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
		} else if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
		} else if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
		} else {
			$data = $_REQUEST;
		}
		//General.20150806 调试模式
		if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
			$this->debug = true;
		}
		$data = str_replace(' ', '+', $data); //浏览器测试api需要 add by suson 20150518
		if ($this->debug && strpos(urlencode($data), '%') == false) {
			$data = str_replace('2F', '%2F', $data);
			$data = str_replace('0A', '%0A', $data);
			$data = str_replace('3D', '%3D', $data);
			$data = str_replace('2B', '%2B', $data);
			$data = urldecode($data);
		}
		if (!empty($data)) {
			$data = CommonClass::decode($data, $this->pageType); //解密数据
		}
		$basedata = array();
		if (isset($_REQUEST['h5src'])) {
			$basedata['h5src'] = $_REQUEST['h5src'];
		}
		if (isset($_REQUEST['wx_auth'])) {
			$basedata['wx_auth'] = $_REQUEST['wx_auth']; /* 微信autocode */
		}

		$this->request = $basedata;
		if (!empty($data) && is_array($data)) {
			$this->request = array_merge($this->request, $data);
		}

		return;
	}

}

/**
 * OA 企业端手机基类 Controller基类
 * General.20150323
 */
class OAEnterpriseHtmlController extends OABaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		Doo::conf()->ROOT_VIEW_PATH = Doo::conf()->ROOT_VIEW_PATH_V3;
		$this->templateSkin = 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_HTML;
		$this->folder = 'mobile/'; //General.20151231 默认模板类型目录
		//模块基地址
		$this->data['appbaseurl'] = $this->data['baseurl'] . $this->folder;
		Doo::loadClass('CommonClass');
		$this->params = $params;
		$this->init_reqdata();
		$this->prm_base = new PrmBaseClass();
		$h5src = !empty($this->request['h5src']) ? $this->request['h5src'] : '';

		$check_cookie = false;
		switch ($h5src) {
			case EnumClass::H5_SRC_WEIXIN:
				//访问地址为 html/m96679686402257752/page/index?reqdata=adsfadsf&h5src=10171002&wx_auth=aaaaaaaaaaaaa
				//设置session
				$authcode = !empty($this->request['wx_auth']) ? $this->request['wx_auth'] : '';
				$ret = Apiclass::weixin_check_login('3.0.0.0', $this->prm_base, $authcode, EnumClass::H5_SRC_WEIXIN);
				//var_dump($ret);exit;
				if ($ret['ret'] == RetClass::SUCCESS) {
					$userid = $ret['data']['userid'];
					$entid = $ret['data']['entid'];

					LogClass::log_trace1('微信来访用户', array('userid' => $userid, 'entid' => $entid), __FILE__, __LINE__);
				}
				//测试数据
				// $userid = '96904508076537401';
				// $entid = '96904508076537382';
				$this->set_session('3.0.0.0', $this->prm_base, $entid, $userid, $h5src);
				break;
			case EnumClass::H5_SRC_MOBILE:

				//访问地址为 html/m96679686402257752/page/index?reqdata=adsfadsf&h5src=10171001
				//设置session
				$userid = !empty($this->request['userid']) ? $this->request['userid'] : 0;
				$entid = !empty($this->request['entid']) ? $this->request['entid'] : 0;

				$this->set_session('3.0.0.0', $this->prm_base, $entid, $userid, $h5src);
				break;
			case '10171003'://这个case仅为方便管理后台调试使用，正式上线时应注释 HCW 2017.10.10
				$userid = !empty($_REQUEST['userid']) ? $_REQUEST['userid'] : 0;
				$entid = !empty($_REQUEST['entid']) ? $_REQUEST['entid'] : 0;
				$this->set_session('3.0.0.0', $this->prm_base, $entid, $userid, '10171003');
				$sessionid = sha1(uniqid($userid, true)); //设置sessionid
				/*$set = $wxworksession->cache_sessionid('set', $sessionid, array('oa_userid'=>$userid,'oa_entid'=>$entid,'oa_puserid'=>''));
				if (empty($set)) {
					die('设置缓存失败');
				};*/
				break;
			default:
				$check_cookie = true;
				break;
		}

		/*$logs = array();
		$logs['H5_request'] = $this->request;
		$logs['H5_session_entid'] = CommonClass::session()->entid;
		$logs['H5_session_userid'] = CommonClass::session()->userid;
		LogClass::log_trace1('H5_basecontroller', $logs, __FILE__, __LINE__);*/

		LogClass::log_trace1('OAEnterpriseHtmlController 读取request',$this->request,__FILE__,__LINE__);
		// if ($check_cookie) {
		// 	// 企业微信移动端登录态检查 lyj 2017-10-19
		// 	!class_exists('WxWorkSessionClass') && Doo::loadModuleClass('wechat/wxwork', 'WxWorkSessionClass');
		// 	$params = array('version' => '3.0.0.0', 'prm_base' => $this->prm_base);
		// 	$wxworksession = new WxWorkSessionClass($params);

		// 	// $cacheVal = $wxworksession->cache_sessionid('check'); //通过获取sessionid检查登录态
		// 	$cacheVal = $wxworksession->get_session();
		// 	LogClass::log_trace1('OAEnterpriseHtmlController 读取session缓存',$cacheVal,__FILE__,__LINE__);
		// 	// if (empty($cacheVal)) {
		// 	// 	$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_AJAX;
		// 	// 	$this->echo_ret(RetClass::ERROR, '', '', '', '登录态失效'); //请求参数的userid和缓存的不一致
		// 	// 	exit;
		// 	// };
		// 	$this->prm_base->setPuserid(isset($cacheVal['oa_puserid']) ? $cacheVal['oa_puserid'] : 0);
		// 	$this->prm_base->setUserid(isset($cacheVal['oa_userid']) ? $cacheVal['oa_userid'] : 0);
		// 	$this->prm_base->setEntid(isset($cacheVal['oa_entid']) ? $cacheVal['oa_entid'] : 0);
		// 	LogClass::log_trace1('OAEnterpriseHtmlController 读取prm_base',$this->prm_base,__FILE__,__LINE__);
		// } else {
			if (isset($this->request['userid'])) {
				$this->prm_base->setUserid(isset($this->request['userid']) ? $this->request['userid'] : 0);
				$this->prm_base->setEntid(isset($this->request['entid']) ? $this->request['entid'] : 0);
			} else {
				$this->prm_base->setUserid(isset(CommonClass::session()->userid) ? CommonClass::session()->userid : 0);
				$this->prm_base->setEntid(isset(CommonClass::session()->entid) ? CommonClass::session()->entid : 0);
			}
			//验证用户
			$this->check_user();
		// };

		$retData = ApiClass::datasource_mobile_get_info($version = '3.0.0.0', $this->prm_base);
		LogClass::log_trace1('OAEnterpriseHtmlController 读取retData',$retData,__FILE__,__LINE__);
		$this->prm_base->setProfid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['profid']) ? $retData['data']['profid'] : 0);
		$this->prm_base->setUsername($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['profname']) ? $retData['data']['profname'] : '');
		$this->prm_base->setUserDeptid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['deptid']) ? $retData['data']['deptid'] : 0);
		$this->prm_base->setUserDeptname($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['deptname']) ? $retData['data']['deptname'] : '');
		$this->prm_base->setUserPosnid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['posnid']) ? $retData['data']['posnid'] : 0);
		$this->prm_base->setUserPosnname($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['posnname']) ? $retData['data']['posnname'] : '');
		$this->prm_base->setEntno($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['entno']) ? $retData['data']['entno'] : 0);
		$this->prm_base->setEntname($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['entname']) ? $retData['data']['entname'] : '');
		$this->prm_base->setOAno($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['oano']) ? $retData['data']['oano'] : 0);

		$this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
		$this->prm_base->setFileUrl(isset($this->data['fileurl']) ? $this->data['fileurl'] : '/');
		$this->prm_base->setChannel(isset($this->data['channel']) ? $this->data['channel'] : '/'); //渠道
		$this->prm_base->setVersion(isset($this->data['version']) ? $this->data['version'] : '/'); //系统版本
		$this->prm_base->setTerminal(isset($this->request['terminal']) && !empty($this->request['terminal']) ? $this->request['terminal'] : EnumClass::TERMINAL_TYPE_H5); //终端类型
		$this->prm_base->setPuserid($retData['ret'] == RetClass::SUCCESS && isset($retData['data']['puserid']) ? $retData['data']['puserid'] : 0);
		$this->prm_base->setOacnurl(Doo::conf()->OA_CN_URL);
	}

	/**
	 * 初始化请求数据
	 */
	public function init_reqdata() {
		$data = '';

		// 解析angularjs POST过来的数据
		if (isset($_SERVER['CONTENT_TYPE']) &&
				strtolower($_SERVER['CONTENT_TYPE']) == 'application/json;charset=utf-8') {
			$params = json_decode(file_get_contents('php://input'), true);
			if ($params) {
				foreach ($params as $key => $value) {
					$_POST[$key] = $value;
					// 不要覆盖REQUEST里面的数据,在程序里有地方用到$_REQUEST
					if (!isset($_REQUEST[$key])) {
						$_REQUEST[$key] = $value;
					}
				}
			}
		}

		if (isset($_GET['reqdata'])) {
			$data = $_GET['reqdata'];
		} else if (isset($_POST['reqdata'])) {
			$data = $_POST['reqdata'];
		} else if (isset($_REQUEST['reqdata'])) {
			$data = $_REQUEST['reqdata'];
		} else {
			$data = $_REQUEST;
		}
		//General.20150806 调试模式
		if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
			$this->debug = true;
		}
		$data = str_replace(' ', '+', $data); //浏览器测试api需要 add by suson 20150518
		if ($this->debug && strpos(urlencode($data), '%') == false) {
			$data = str_replace('2F', '%2F', $data);
			$data = str_replace('0A', '%0A', $data);
			$data = str_replace('3D', '%3D', $data);
			$data = str_replace('2B', '%2B', $data);
			$data = urldecode($data);
		}
		if (!empty($data)) {
			$data = CommonClass::decode($data, $this->pageType); //解密数据
		}
		$basedata = array();
		if (isset($_REQUEST['h5src'])) {
			$basedata['h5src'] = $_REQUEST['h5src'];
			if($_REQUEST['h5src'] == '10171003'){
				//接口管理后台暂时处理，方便测试 HCW 2017.10.30
				$basedata = $_REQUEST;
			}
		}
		if (isset($_REQUEST['wx_auth'])) {
			$basedata['wx_auth'] = $_REQUEST['wx_auth']; /* 微信autocode */
		}

		$this->request = $basedata;
		if (!empty($data) && is_array($data)) {
			$this->request = array_merge($this->request, $data);
		}

		return;
	}

	public function afterRunEx($routeResult, $data) {
		$this->data['incheader'] = !empty($this->incheader) ? $this->incheader : ''; //引入不同的incheader,scalable表示可缩放头 suson.20170123
		$this->afterRunOutput($routeResult, $data, 'index_base_html', '3.0.0.0');
	}

	public function set_session($version, $prm_base, $entid, $userid, $h5src) {
		CommonClass::session()->namespaceUnset();
		CommonClass::session()->h5src = $h5src;
		if (!empty($entid) && !empty($userid)) {
			CommonClass::session()->userid = $userid;
			CommonClass::session()->entid = $entid;
		}
	}

	/**
	 * [check_user 检查用户 2016-07-13 lyj]
	 * @return [type] [description]
	 */
	public function check_user() {
		Doo::loadClass('UserCommonClass');
		UserCommonClass::Common();
		if (empty($this->prm_base->userid) || empty($this->prm_base->entid)) {
			// echo self::ret(RetClass::COM_NO_PERMISSION);
			// $this->echo_ret(RetClass::COM_NO_PERMISSION);
			echo '无权访问！';
			exit;
		}
	}

    /**
	 * 设置配置文件
	 * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
	 */
	protected function getConfigFileName() {
		return 'mod.h5';
	}
    /**
     * 获取代码配置文件
     * @return $fileName 配置文件名称(不含扩展名.json) 如 mod.h5
	 * @author james.ou 2017-8-2
     */
    protected function getCodeFileName(){
        return 'code.h5';
    }
    /**
     * 获取controller 对象
     * @return $this
     */
    protected function getController(){
        return $this;
    }
}

/**
 * OA 个人端 Controller基类
 * General.20150105
 */
class OAPersonalController extends OAPersonalBaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setUserid(isset(CommonClass::session()->puserid) ? CommonClass::session()->puserid : 0);
		$this->prm_base->setUsername(isset(CommonClass::session()->username) ? CommonClass::session()->username : '');
		$this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
		$this->prm_base->setFileUrl(isset($this->data['fileurl']) ? $this->data['fileurl'] : '/');
		$this->prm_base->setChannel(isset($this->data['channel']) ? $this->data['channel'] : '/'); //渠道
		$this->prm_base->setVersion(isset($this->data['version']) ? $this->data['version'] : '/'); //系统版本
		$this->prm_base->setTerminal(!empty($_REQUEST['terminal']) ? $_REQUEST['terminal'] : EnumClass::TERMINAL_TYPE_WEB); //终端类型
		$this->prm_base->setOacnurl(Doo::conf()->OA_CN_URL);
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_PAGE;

		Doo::loadClass('UserCommonClass');
		UserCommonClass::Common();
		//$this->data['main'] = Doo::app()->getModule('base/main','BaseMainController', 'main');
		$this->params = $params;
		//header("Content-type: text/html; charset=utf-8");
	}

	public function afterRunEx($routeResult, $data) {
		$template = 'index_base';
		if ($this->prm_base->entid == '96197057806605466') {
			$template = 'index_base_classic';
		}
		$this->afterRunOutput($routeResult, $data, $template, '3.0.0.0');
	}

}

/**
 * OA 企业端服务端API接口 Controller基类
 * General.20150418
 */
class OAEnterpriseServerController extends OAAPIController {

	public function __construct($params = null) {
		parent::__construct($params);
	}

}

/**
 * OA 个人端服务端API接口 Controller基类
 * General.20150418
 */
class OAPersonalServerController extends OAAPIController {

	public function __construct($params = null) {
		parent::__construct($params);
	}

}

/**
 * OA 企业端API接口 Controller基类
 * General.20150107
 */
class OAEnterpriseAPIController extends OAAPIController {

	public function __construct($params = null) {
		parent::__construct($params);

		//add lys 20160613
		Doo::loadClass('CommonClass');
		Doo::loadClass('PermissionClass');
		// $this->prm_base = new PrmBaseClass();
		$this->chkUserAuth();
	}

	//General.20160519 客户端接口鉴权
	public function chkUserAuth() {

		/*		 * *********add lys 20160613 start************* */
		/* LogClass::log_trace1('OAEnterpriseAPIController_chkUserAuth(1\)::',$this->request,__FILE__,__LINE__);
		  extract($this->request);//接收到的参数

		  if(!isset($this->prm_base->terminal) || empty($this->prm_base->terminal)){
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'data' => '$this->prm_base->terminal终端来源不能为空'));
		  }
		  if(!isset($this->prm_base->userid) || empty($this->prm_base->userid)){
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'data' => '$this->prm_base->userid不能为空'));
		  }
		  if(!isset($authcode)){ //请求令牌
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'data' => '$authcode请求令牌不存在'));
		  }

		  $terminal = $this->prm_base->terminal; //终端类型
		  $userid = $this->prm_base->userid; //用户Id

		  $get_cache_authcode = CommonClass::cache_op('get','USER','AUTHCODE',$userid); //获取令牌缓存
		  if($get_cache_authcode == false){
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'code' => RetClass::COM_USER_AUTH_FAILED));
		  }

		  $cache_authcodes = CommonClass::json_decode($get_cache_authcode);
		  if(!isset($cache_authcodes[$terminal])){
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'code' => RetClass::COM_USER_AUTH_FAILED));
		  }
		  $cache_authcode = $cache_authcodes[$terminal];
		  $addtime = $get_cache_authcode[$terminal]['time']; //同一终端类型的缓存请求时间
		  $time = time(); //当前时间
		  $differ_time = ($time - $addtime) / 60; //超时多少分钟

		  if($authcode_terminal != $authcode){
		  LogClass::log_trace1('client_authcode_dismatch:', '请求令牌：'.$authcode.'不等于登录令牌：'.$cache_authcode.'且/或登录令牌时间：'.$addtime.'与服务器当前时间：'.$time.'超过'.($differ_time/60).'小时，登录失效',__FILE__,__LINE__);
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'code' => RetClass::COM_USER_AUTH_FAILED));
		  }

		  $timeout = isset($timeout) ? $timeout : 30;
		  if($differ_time > $timeout){
		  LogClass::log_trace1('client_request_timeout:', '请求时间：'.$addtime.'大于服务器当前时间：'.$time,__FILE__,__LINE__);
		  $this->echo_ret(array('ret' => RetClass::ERROR, 'code' => RetClass::COM_REQUEST_TIMEOUT));
		  }

		  $this->echo_ret(RetClass::SUCCESS,array('authcode'=>$authcode,'addtime'=>$addtime)); */
		/*		 * *********add lys 20160613 end************* */
	}

}

/**
 * OA 个人端API接口 Controller基类
 * General.20150104
 */
class OAPersonalAPIController extends OAAPIController {

	public function __construct($params = null) {
		parent::__construct($params);
	}

}

/**
 * 新建公司网页端基类 20160612 add by hcf
 */
class OANewcorpController extends OAEnterpriseBaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		// Doo::conf()->ROOT_VIEW_PATH	= Doo::conf()->ROOT_VIEW_PATH_V3;
		// $this->templateSkin			= 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_PAGE;
		$this->prm_base = new PrmBaseClass();
		$this->prm_base->setUrl(isset($this->data['baseurl']) ? $this->data['baseurl'] : '/');
		$this->prm_base->setFileUrl(isset($this->data['fileurl']) ? $this->data['fileurl'] : '/');
		$this->prm_base->setChannel(isset($this->data['channel']) ? $this->data['channel'] : '/'); //渠道
		$this->prm_base->setVersion(isset($this->data['version']) ? $this->data['version'] : '/'); //系统版本
		$this->prm_base->setTerminal(!empty($_REQUEST['terminal']) ? $_REQUEST['terminal'] : EnumClass::TERMINAL_TYPE_WEB); //终端类型
		$this->prm_base->setOacnurl(Doo::conf()->OA_CN_URL);
		if (!empty($_POST['pageType'])) {
			$this->pageType = $_POST['pageType'];
		}
		// $this->folder = 'web/';//General.20151231 默认模板类型目录
		// //模块基地址
		// $this->data['appbaseurl'] =  $this->data['baseurl'].$this->folder;
		//  Doo::loadClass('CommonClass');
		//  $this->params = $params;
		//  $this->init_reqdata();
	}

	public function afterRunEx($routeResult, $data) {

		//$this->afterRunOutput($routeResult, $data, 'index_base_newcorp', '3.0.0.0');
		switch ($this->pageType) {
			case EnumClass::PAGE_RET_MSG_TYPE_PAGE://普通页面请求
				$this->afterRunOutput($routeResult, $data, 'index_base_newcorp', '3.0.0.0');
				break;
			case EnumClass::PAGE_RET_MSG_TYPE_AJAX://页面Ajax请求
				break;
			default:
				break;
		}
	}

}

/**
 * 门户网页端基类 20170103 add by hcf
 */
class OAWebsiteController extends OAEnterpriseBaseController {

	public function __construct($params = null) {
		parent::__construct($params);
		// Doo::conf()->ROOT_VIEW_PATH	= Doo::conf()->ROOT_VIEW_PATH_V3;
		// $this->templateSkin			= 'default/';
		$this->pageType = EnumClass::PAGE_RET_MSG_TYPE_PAGE;
		$this->prm_base = new PrmBaseClass();
		if (!empty($_POST['pageType'])) {
			$this->pageType = $_POST['pageType'];
		}
		// $this->folder = 'web/';//General.20151231 默认模板类型目录
		// //模块基地址
		// $this->data['appbaseurl'] =  $this->data['baseurl'].$this->folder;
		//  Doo::loadClass('CommonClass');
		//  $this->params = $params;
		//  $this->init_reqdata();
	}

	public function afterRunEx($routeResult, $data) {

		//$this->afterRunOutput($routeResult, $data, 'index_base_website', '3.0.0.0');
		switch ($this->pageType) {
			case EnumClass::PAGE_RET_MSG_TYPE_PAGE://普通页面请求
				$this->afterRunOutput($routeResult, $data, 'index_base_website', '3.0.0.0');
				break;
			case EnumClass::PAGE_RET_MSG_TYPE_AJAX://页面Ajax请求
				break;
			default:
				break;
		}
	}

}
?>
