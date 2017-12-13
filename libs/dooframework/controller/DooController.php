<?php
/**
 * DooController class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * Base class of all controller
 *
 * <p>Provides a few shorthand methods to access commonly used component during development. e.g. DooLoader, DooLog, DooSqlMagic.</p>
 *
 * <p>Parameter lists and extension type defined in routes configuration can be accessed through <b>$this->params</b> and <b>$this->extension</b></p>
 *
 * <p>If a client sends PUT request to your controller, you can retrieve the values sent through <b>$this->puts</b></p>
 *
 * <p>GET and POST variables can still be accessed via php $_GET and $_POST. They are not handled/process by Doo framework.</p>
 *
 * <p>Auto routing can be denied from a Controller by setting <b>$autoroute = false</b></p>
 *
 * Therefore, the following class properties & methods is reserved and should not be used in your Controller class.
 * <code>
 * $params
 * $puts
 * $extension
 * $autoroute
 * $vdata
 * $renderMethod
 * init_put_vars()
 * load()
 * language()
 * accept_type()
 * render()
 * renderc()
 * setContentType()
 * is_SSL()
 * view()
 * db()
 * cache()
 * acl()
 * beforeRun()
 * isAjax()
 * renderLayout()
 * clientIP()
 * saveRendered()
 * saveRenderedC()
 * toXML()
 * toJSON()
 * viewRenderAutomation()
 * getKeyParam()
 * afterRun()
 * </code>
 *
 * You still have a lot of freedom to name your methods and properties other than names mentioned.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooController.php 1000 2009-07-7 18:27:22
 * @package doo.controller
 * @since 1.0
 */
class DooController {
	/**
	 * Associative array of the parameter list found matched in a URI route.
	 * @var array
	 */
	public $params;

	/**
	 * Associative array of the PUT values sent by client.
	 * @var array
	 */
	public $puts;

	/**
	 * Extension name (.html, .json, .xml ,...) found in the URI. Routes can be specified with a string or an array as matching extensions
	 * @var string
	 */
	public $extension;

	/**
	 * Deny or allow auto routing access to a Controller. By default auto routes are allowed in a controller.
	 * @var bool
	 */
	public $autoroute = TRUE;

	/**
	 * Data to be pass from controller to view to be rendered
	 * @var mixed
	 */
	public $vdata;

	/**
	 * Enable auto render of view at the end of a controller -> method request
	 * @var bool
	 */
	public $autorender = FALSE;

	/**
	 * Render method for auto render. You can use 'renderc' & 'render' or your own method in the controller.
	 * @var string Default is renderc
	 */
	public $renderMethod = 'renderc';

	/**
	 * 默认皮肤路径
	 * @var string Default is ''
	 */
	public $templateSkin = 'default/';

	/**
	 * 页面访问类型,直接访问、ajax等
	 * @var int Default is 0
	 */
	public $pageType = EnumClass::PAGE_RET_MSG_TYPE_AJAX;

	/**
	 * 表单界面类型： 添加页、编辑页、详情页、列表页等
	 * @var int Default is FORM_PAGE_TYPE_NONE
	 */
	public $pageFormType = EnumClass::FORM_PAGE_TYPE_NONE;

	/**
	 * 模块ID
	 * @var int Default is 0
	 */
	public $moduleid = 0;

	/**
	 * 页面ID
	 * @var int Default is 0
	 */
	public $pageFormid = 0;

	/**
	 * 默认模板目录
	 * @var string Default is 'web'
	 */
	public $folder = 'web/';

	protected $_load;
	protected $_view;

	/**
	 * 显示错误信息 General.20141027
	 * @param type $right 权限值 true/false
	 * @param type $msg 显示信息
	 * @param type $page_type 错误类型 page,ajax,mobile
	 * @param string $url 返回地址
	 * @param type $type 类型
	 * @return type
	 */
	public function show_err($right = true, $msg = '',$page_type = null, $url = '',$type = '') {
		if($right){
			return;
		}
		//$res['success'] = false;
		//$res['message'] = $msg;
		$msgstr = $msg;
		if(strpos($msg,'_') !== false){
			$msgstr = CommonClass::txt($msg);
		}
		$res['ret'] = RetClass::ERROR;
		if (empty($url)) {//默认返回
			// $url = '/web/desktop/page/index';
			$url = '/';  //update by ljh 20160506
		}
		//General.20160129 获取当前url判断路由对应不同的pagetype
		if ($page_type == EnumClass::PAGE_RET_MSG_TYPE_NONE) {
			$url = $_SERVER['REQUEST_URI'];
			if(strpos($url,'/page/') !== false){
				$page_type = EnumClass::PAGE_RET_MSG_TYPE_PAGE;
			}else if(strpos($url,'/api/') !== false){
				$page_type = EnumClass::PAGE_RET_MSG_TYPE_MOBILE;
			}else if(strpos($url,'/ajax/') !== false){
				$page_type = EnumClass::PAGE_RET_MSG_TYPE_AJAX;
			}
		}
		if (is_null($page_type) || empty($page_type)) {
			$page_type = $this->pageType;
		}
		$setdata['url'] = $url;
		$return = BaseClass::ret(RetClass::ERROR,'',EnumClass::RET_TYPE_NOBTN,'',$msgstr, $setdata);//General.20150529 无按钮提示
		switch ($page_type) {
			case EnumClass::PAGE_RET_MSG_TYPE_AJAX:
				unset($return['attr']['setdata']);
				//echo CommonClass::json_encode($res,JSON_UNESCAPED_UNICODE);
				//echo CommonClass::encode($return, $page_type);
				echo CommonClass::json_encode($return);
				exit;
				break;
			case EnumClass::PAGE_RET_MSG_TYPE_MOBILE: //General.20141222 手机接口错误返回
				//$res['ret'] = RetClass::ERROR;
				//echo json_encode($res,JSON_UNESCAPED_UNICODE);
				echo CommonClass::encode($return, $page_type);
				exit;
				break;
			/* case EnumClass::PAGE_RET_MSG_TYPE_HTML:
				$res = CommonClass::show_msg($return, $type);
				if (substr($res[0], 0, 1) == '/') {
					$res = $this->prm_base->url.ltrim($res[0], '/');
				}
				$rets = BaseClass::ret(RetClass::ERROR,'',EnumClass::RET_TYPE_URL,'','','','','',$res);
				echo CommonClass::json_encode($rets);
				exit;
				break; */
			case EnumClass::PAGE_RET_MSG_TYPE_HTML:
			case EnumClass::PAGE_RET_MSG_TYPE_PAGE:
			default:
				$res = CommonClass::show_msg($return, $type);
				// ljh 2017-7-10
				$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
				$res[0] = !empty($_SERVER['HTTP_HOST']) ? $http_type.$_SERVER['HTTP_HOST'].$res[0] : $res[0];
				// if (substr($res[0], 0, 1) == '/') {
				// 	$res[0] = Doo::conf()->SAAS_URL.ltrim($res[0], '/'); //update by ljh 20160506
				// }
				//echo '<meta http-equiv="refresh" content="1;url='.$res[0].'"> ';
				echo '<script language="javascript" type="text/javascript">window.location.href="'.$res[0].'";</script>';
				exit;
				break;
		}
	}

	/**
	 * 检查验证参数，通过后将验证后结果赋值$this->params，否则按错误类型返回
	 * @param type $data 需要检查的参数数组
	 * @param type $rules 规则文件名
	 * @param type $page_type 错误类型
	 * @param string $url 错误返回路径
	 * @param type $type 错误页面返回类型
	 * @param type $exclude_rule_keys 不包括的规则字段
	 * @param type $necessary_rule_keys 必须的规则字段
	 * @return type
	 */
	public function check_params($version, $prm_base, $data=array(), $rules='', $page_type = null,
		$url = '',$type = '',$exclude_rule_keys = null, $necessary_rule_keys = null) {

		$default = array(
			'version'=>$version,
			'prm_base'=>$prm_base,
			'data'=>$data,
			'rules'=>$rules,
			'page_type'=>$page_type,
			'url'=>$url,
			'type'=>$type,
			'exclude_rule_keys'=>$exclude_rule_keys,
			'necessary_rule_keys'=>$necessary_rule_keys
		);
		/**
		 * 直接第二个是对象的时候
		 * 成立:直接用该参数
		 * 否则:所有参数向前走2位
		 */
		if(is_string($version) && is_object($prm_base)){
		}else{
			$data = $default['version'];
			$rules = $default['prm_base'];
			$page_type = $default['data'];
			$url = $default['rules'];
			$type = $default['page_type'];
			$exclude_rule_keys = $default['url'];
			$necessary_rule_keys = $default['type'];
		}

		//get defined rules and add show some error messages
		$validator = new ValidatorClass;
		$validator->checkMode = EnumClass::CHECK_PARAM_SKIP;

		if (is_null($page_type) || empty($page_type)) {
			$page_type = $this->pageType;
		}

		//General.20150310 增加$data与$this->params是否数组的判断
		if(is_array($data) && is_array($this->params)){
			$data = array_merge($data,$this->params);
		}else if(!is_array($data) && is_array($this->params)){
			$data = $this->params;
		}else if(!is_array($data) && !is_array($this->params)){
			$this->show_err(false,'TIPS_HANDLE_DATALOST',$page_type,$url,$type);
		}

		$oaret = array();
		$result = $validator->validate($data, $rules, $params, $oaret, $prm_base);

		$this->params = $params;
		// $this->params['eno'] = $this->prm_base->eno;
		// $this->params['uno'] = $this->prm_base->uno;
		// $this->params['eid'] = $this->prm_base->eid;

		if ($result){
			$this->show_err(false,$result,$page_type,$url,$type);
		}
		/*switch ($page_type) {
			case EnumClass::PAGE_RET_MSG_TYPE_AJAX:
				$res['success'] = false;
				$res['message'] = $result;
				echo json_encode($res,JSON_UNESCAPED_UNICODE);
				exit;
				break;
			default:
				if (empty($url)) {//默认返回首页
					$url = '/home/index';
				}
				//return UserCommonClass::showMsg((string)$result[0], '','');
				//return UserCommonClass::showMsg((string)'ddd', '','');
				echo '<meta http-equiv="content-type" content="text/html;charset=utf-8"/><hr>error return:<br>';
				echo json_encode($result,JSON_UNESCAPED_UNICODE);
				echo "<hr>";
				exit;
				return UserCommonClass::showMsg($error, $url,$type);
				/*$data['rootUrl'] = Doo::conf()->APP_URL;
				$data['title'] =  'Error Occured!';
				$data['content'] =  '<p style="color:#ff0000;">'.$error.'</p>';
				$data['content'] .=  '<p>Go <a href="javascript:history.back();">back</a> to edit.</p>';
				$this->render('news/error_', $data);*
				break;
		}*/
	}

    /**
     * Use initPutVars() instead
     * @deprecated deprecated since version 1.3
     */
    public function init_put_vars(){
        parse_str(file_get_contents('php://input'), $this->puts);
    }

    /**
     * Set PUT request variables in a controller. This method is to be used by the main web app class.
     */
    public function initPutVars(){
        parse_str(file_get_contents('php://input'), $this->puts);
    }

    /**
     * The loader singleton, auto create if the singleton has not been created yet.
     * @return DooLoader
     */
    public function load(){
        if($this->_load==NULL){
            Doo::loadCore('uri/DooLoader');
            $this->_load = new DooLoader;
        }

        return $this->_load;
    }

    /**
     * Returns the database singleton, shorthand to Doo::db()
     * @return DooSqlMagic
     */
    public function db(){
        return Doo::db();
    }

    /**
     * Returns the Acl singleton, shorthand to Doo::acl()
     * @return DooAcl
     */
    public function acl(){
        return Doo::acl();
    }

    /**
     * This will be called before the actual action is executed
     */
    public function beforeRun($resource, $action){}

    /**
     * Returns the cache singleton, shorthand to Doo::cache()
     * @return DooFileCache|DooFrontCache|DooApcCache|DooMemCache|DooXCache|DooEAcceleratorCache
     */
    public function cache($cacheType='file'){
        return Doo::cache($cacheType);
    }

    /**
     * Writes the generated output produced by render() to file.
     * @param string $path Path to save the generated output.
     * @param string $templatefile Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @return string|false The file name of the rendered output saved (html).
     */
	// public function saveRendered($path, $templatefile, $data=NULL) {
	// 	return $this->view()->saveRendered($path, $templatefile, $data);
	// }
    /**
     * Short hand for $this->view()->render() Renders the view file.
     *
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function saveRendered($version, $templatefile, $tofile, $data=NULL, $isMakeTemplate=false, $process=NULL){
        $this->view()->saveRendered($this->templateSkin . $version .'/'. $this->folder . $tofile,
            $this->templateSkin . $version .'/'. $this->folder . $templatefile, $data, $isMakeTemplate, $process,
            $this->templateSkin, $this->folder, true, 3);
    }

    public function saveRendered_ent($version, $pageformid, $data=NULL, $tmplid=0,$isMakeTemplate=false, $process=NULL){
		// $tmplid = 1;
		$template = TemplateClass::get_template($version,$this->prm_base,$tmplid);

        if(!empty($template['content'])){
            //template表的content直接生成entfiles文件 suson.20160621
            return $this->view()->saveTmp_ent($this->prm_base,$this->prm_base->entid,$this->moduleid,$pageformid,$this->prm_base->terminal,
            $this->templateSkin . $version .'/'. $this->folder,'', $data, $isMakeTemplate, $process,
            $this->templateSkin, $this->folder, true, 3);
        }


        if(!empty($template['path'])){
            //指定template表path文件的情况
            $this->view()->saveRendered_ent($this->prm_base->entid,$this->moduleid,$pageformid,$this->prm_base->terminal,
            $this->templateSkin . $version .'/'. $this->folder,
            $template['path'], $data, $isMakeTemplate, $process,
            $this->templateSkin, $this->folder, true, 3);
        }
        
    }

    /**
     * Writes the generated output produced by renderc() to file.
     * @param string $path Path to save the generated output.
     * @param string $templatefile Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $enableControllerAccess Enable the view scripts to access the controller property and methods.
     * @param bool $includeTagClass If true, DooView will determine which Template tag class to include. Else, no files will be loaded
     * @return string|false The file name of the rendered output saved (html).
     */
    public function saveRenderedC($path, $templatefile, $data=NULL, $enableControllerAccess=False, $includeTagClass=True){
        if($enableControllerAccess===true){
            return $this->view()->saveRenderedC($file, $data, $this, $includeTagClass);
        }else{
            return $this->view()->saveRenderedC($file, $data, null, $includeTagClass);
        }
	}

    /**
     * The view singleton, auto create if the singleton has not been created yet.
     * @return DooView|DooViewBasic
     */
    public function view(){
        if($this->_view==NULL){
			$engine = Doo::conf()->TEMPLATE_ENGINE;
            Doo::loadCore('view/' . $engine);
            $this->_view = new $engine;
        }

        return $this->_view;
    }

    /**
     * Short hand for $this->view()->render() Renders the view file.
     *
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function renderLite($file, $data=NULL, $process=NULL, $forceCompile=false){
        $this->view()->render($file, $data, $process, $forceCompile);
    }

	/**
     * Short hand for $this->view()->render() Renders the view file.
     *
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function render($version, $file, $data=NULL, $process=NULL, $forceCompile=false){
        $this->view()->render($this->templateSkin . $version .'/'. $this->folder . $file, $data, $process, $forceCompile,
        	$this->templateSkin, $this->folder, true, 3);
    }

    public function render_ent($version, $pageformid, $data=NULL, $process=NULL, $forceCompile=false){
        // $tmplid = 1;
        // $template = TemplateClass::get_template($version,$this->prm_base,$tmplid);
        
        // $this->view()->render_ent($this->prm_base->entid,$this->moduleid,$pageformid,$this->prm_base->terminal,
        //     $this->templateSkin . $version .'/'. $this->folder,
        //     $template['path'], $data, $process, $forceCompile,
        //     $this->templateSkin, $this->folder, true, 3);
        //render时发现不需要获取template
        //suson.20160118
        $templatefile = '';
        $entid = $this->prm_base->entid;
        $moduleid = $this->moduleid;
        $terminal = $this->prm_base->terminal;
        $filepath = $this->templateSkin . $version .'/'. $this->folder;
        $vfilename = $this->view()->getViewFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath);
        if(!file_exists($vfilename)){
            FormClass::gen_module_formpage($version,$this->prm_base,$moduleid,$dataformid = 0,$terminal,$pageformid);
        }
        $this->view()->render_ent($entid,$moduleid,$pageformid,$terminal,
            $filepath,$templatefile, $data, $process, $forceCompile,
            $this->templateSkin, $this->folder, true, 3);
    }

    /**
     * Short hand for $this->view()->renderc() Renders the view file(php) located in viewc.
     *
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the php template.
     * @param bool $enableControllerAccess Enable the view scripts to access the controller property and methods.
     * @param bool $includeTagClass If true, DooView will determine which Template tag class to include. Else, no files will be loaded
     */
    public function renderc($file, $data=NULL, $enableControllerAccess=False, $includeTagClass=True){
        if($enableControllerAccess===true){
            $this->view()->renderc($this->templateSkin . $file, $data, $this, $includeTagClass);
        }else{
            $this->view()->renderc($this->templateSkin . $file, $data, null, $includeTagClass);
        }
    }

    /**
     * Get the client accept language from the header
     *
     * @param bool $countryCode to return the language code along with country code
     * @return string The language code. eg. <b>en</b> or <b>en-US</b>
     */
    public function language($countryCode=FALSE){
        $langcode = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $langcode = (!empty($langcode)) ? explode(';', $langcode) : $langcode;
        $langcode = (!empty($langcode[0])) ? explode(',', $langcode[0]) : $langcode;
        if(!$countryCode)
            $langcode = (!empty($langcode[0])) ? explode('-', $langcode[0]) : $langcode;
        return $langcode[0];
    }

    /**
     * Use acceptType() instead
     * @deprecated deprecated since version 1.3
     * @return string Client accept type
     */
    public function accept_type(){
        return $this->acceptType();
    }

    /**
     * Get the client specified accept type from the header sent
     *
     * <p>Instead of appending a extension name like '.json' to a URL,
     * clients can use 'Accept: application/json' for RESTful APIs.</p>
     * @return string Client accept type
     */
    public function acceptType(){
        $type = array(
            '*/*'=>'*',
            'html'=>'text/html,application/xhtml+xml',
            'xml'=>'application/xml,text/xml,application/x-xml',
            'json'=>'application/json,text/x-json,application/jsonrequest,text/json',
            'js'=>'text/javascript,application/javascript,application/x-javascript',
            'css'=>'text/css',
            'rss'=>'application/rss+xml',
            'yaml'=>'application/x-yaml,text/yaml',
            'atom'=>'application/atom+xml',
            'pdf'=>'application/pdf',
            'text'=>'text/plain',
            'png'=>'image/png',
            'jpg'=>'image/jpg,image/jpeg,image/pjpeg',
            'gif'=>'image/gif',
            'form'=>'multipart/form-data',
            'url-form'=>'application/x-www-form-urlencoded',
            'csv'=>'text/csv'
        );

        $matches = array();

        //search and match, add 1 priority to the key if found matched
        foreach($type as $k=>$v){
            if(strpos($v,',')!==FALSE){
                $tv = explode(',', $v);
                foreach($tv as $k2=>$v2){
                    if (stristr($_SERVER["HTTP_ACCEPT"], $v2)){
                        if(isset($matches[$k]))
                            $matches[$k] = $matches[$k]+1;
                        else
                            $matches[$k]=1;
                    }
                }
            }else{
                if (stristr($_SERVER["HTTP_ACCEPT"], $v)){
                    if(isset($matches[$k]))
                        $matches[$k] = $matches[$k]+1;
                    else
                        $matches[$k]=1;
                }
            }
        }

        if(sizeof($matches)<1)
            return NULL;

        //sort by the highest priority, keep the key, return the highest
        arsort($matches);

        foreach ($matches as $k=>$v){
            return ($k==='*/*')?'html':$k;
        }
    }

    /**
     * Sent a content type header
     *
     * <p>This can be used with your REST api if you allow clients to retrieve result format
     * by sending a <b>Accept type header</b> in their requests. Alternatively, extension names can be
     * used at the end of an URI such as <b>.json</b> and <b>.xml</b></p>
     *
     * <p>NOTE: This method should be used before echoing out your results.
     * Use accept_type() or $extension to determined the desirable format the client wanted to accept.</p>
     *
     * @param string $type Content type of the result. eg. text, xml, json, rss, atom
     * @param string $charset Charset of the result content. Default utf-8.
     */
    public function setContentType($type, $charset='utf-8'){
        if(headers_sent())return;

        $extensions = array('html'=>'text/html',
                            'xml'=>'application/xml',
                            'json'=>'application/json',
                            'js'=>'application/javascript',
                            'css'=>'text/css',
                            'rss'=>'application/rss+xml',
                            'yaml'=>'text/yaml',
                            'atom'=>'application/atom+xml',
                            'pdf'=>'application/pdf',
                            'text'=>'text/plain',
                            'png'=>'image/png',
                            'jpg'=>'image/jpeg',
                            'gif'=>'image/gif',
                            'csv'=>'text/csv'
						);
        if(isset($extensions[$type]))
            header("Content-Type: {$extensions[$type]}; charset=$charset");
    }

    /**
     * Get client's IP
     * @return string
     */
    public function clientIP(){
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            return getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            return getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * This will be called if the action method returns null or success status(200 to 299 not including 204) after the actual action is executed
     * @param mixed $routeResult The result returned by an action
     */
	public function afterRun($routeResult) {
		if($this->autorender===true && ($routeResult===null || ($routeResult>=200 && $routeResult<300 && $routeResult!=204))){
            $this->viewRenderAutomation();
		}
	}

    /**
     * This will be called if the action method returns null or success status(200 to 299 not including 204) after the actual action is executed
     * @param mixed $routeResult The result returned by an action
     */
	public function afterRunEx($routeResult,$data) {
		if($this->autorender===true && ($routeResult===null || ($routeResult>=200 && $routeResult<300 && $routeResult!=204))){
            $this->viewRenderAutomation();
		}
		echo $data;
	}

    /**
     * Retrieve value of a key from URI accessed from an auto route.
     * Example with a controller named UserController and a method named listAll():
     * <code>
     * //URI is http://localhost/user/list-all/id/11
     * $this->getKeyParam('id');   //returns 11
     * </code>
     *
     * @param string $key
     * @return mixed
     */
    public function getKeyParam($key){
        if(!empty($this->params) && in_array($key, $this->params)){
            $valueIndex = array_search($key, $this->params) + 1;
            if($valueIndex<sizeof($this->params))
                return $this->params[$valueIndex];
        }
    }

    /**
     * Controls the automated view rendering process.
     */
	public function viewRenderAutomation(){
		if(is_string(Doo::conf()->AUTO_VIEW_RENDER_PATH)){
			$path = Doo::conf()->AUTO_VIEW_RENDER_PATH;
			$path = str_replace(':', '@', substr($path, 1));
			$this->{$this->renderMethod}($path, $this->vdata);
		}else{
            if(isset(Doo::conf()->AUTO_VIEW_RENDER_PATH))
                $this->{$this->renderMethod}(strtolower(Doo::conf()->AUTO_VIEW_RENDER_PATH[0]) .'/'. strtolower(Doo::conf()->AUTO_VIEW_RENDER_PATH[1]), $this->vdata);
            else
                $this->{$this->renderMethod}('index', $this->vdata);
		}
	}

    /**
     * Check if the request is an AJAX request usually sent with JS library such as JQuery/YUI/MooTools
     * @return bool
     */
    public function isAjax(){
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * Check if the connection is a SSL connection
     * @return bool determined if it is a SSL connection
     */
    public function isSSL(){
        if(!isset($_SERVER['HTTPS']))
            return FALSE;

        //Apache
        if($_SERVER['HTTPS'] === 1) {
            return TRUE;
        }
        //IIS
        elseif ($_SERVER['HTTPS'] === 'on') {
            return TRUE;
        }
        //other servers
        elseif ($_SERVER['SERVER_PORT'] == 443){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Use isSSL() instead
     * @deprecated deprecated since version 1.3
     */
    public function is_SSL(){
        return $this->isSSL();
    }

    /**
     * Convert DB result into XML string for RESTful api.
     * <code>
     * public function listUser(){
     *     $user = new User;
     *     $rs = $user->find();
     *     $this->toXML($rs, true);
     * }
     * </code>
     * @param mixed $result Result of a DB query. eg. $user->find();
     * @param bool $output Output the result automatically.
     * @param bool $setXMLContentType Set content type.
     * @param string $encoding Encoding of the result content. Default utf-8.
     * @return string XML string
     */
    public function toXML($result, $output=false, $setXMLContentType=false, $encoding='utf-8'){
        $str = '<?xml version="1.0" encoding="utf-8"?><result>';
        foreach($result as $kk=>$vv){
            $cls = get_class($vv);
            $str .= '<' . $cls . '>';
            foreach($vv as $k=>$v){
                if($k!='_table' && $k!='_fields' && $k!='_primarykey'){
                    if(is_array($v)){
                        //print_r($v);
                        //exit;
                        $str .= '<' . $k . '>';
                        foreach($v as $v0){
                            $str .= '<data>';
                            foreach($v0 as $k1=>$v1){
                                if($k1!='_table' && $k1!='_fields' && $k1!='_primarykey'){
                                    if(is_array($v1)){
                                        $str .= '<' . $k1 . '>';
                                        foreach($v1 as $v2){
                                            $str .= '<data>';
                                            foreach($v2 as $k3=>$v3){
                                                if($k3!='_table' && $k3!='_fields' && $k3!='_primarykey'){
                                                    $str .= '<'. $k3 . '><![CDATA[' . $v3 . ']]></'. $k3 . '>';
                                                }
                                            }
                                            $str .= '</data>';
                                        }
                                        $str .= '</' . $k1 . '>';
                                    }else{
                                        $str .= '<'. $k1 . '><![CDATA[' . $v1 . ']]></'. $k1 . '>';
                                    }
                                }
                            }
                            $str .= '</data>';
                        }
                        $str .= '</' . $k . '>';

                    }else{
                        $str .= '<'. $k . '>' . $v . '</'. $k . '>';
                    }
                }
            }
            $str .= '</' . $cls . '>';
        }
        $str .= '</result>';
        if($setXMLContentType===true)
            $this->setContentType('xml', $encoding);
        if($output===true)
            echo $str;
        return $str;
    }

    /**
     * Convert DB result into JSON string for RESTful api.
     * <code>
     * public function listUser(){
     *     $user = new User;
     *     $rs = $user->find();
     *     $this->toJSON($rs, true);
     * }
     * </code>
     * @param mixed $result Result of a DB query. eg. $user->find();
     * @param bool $output Output the result automatically.
     * @param bool $removeNullField Remove fields with null value from JSON string.
     * @param array $exceptField Remove fields that are null except the ones in this list.
     * @param array $mustRemoveFieldList Remove fields in this list.
     * @param bool $setJSONContentType Set content type.
     * @param string $encoding Encoding of the result content. Default utf-8.
     * @return string JSON string
     */
    public function toJSON($result, $output=false, $removeNullField=false, $exceptField=null, $mustRemoveFieldList=null, $setJSONContentType=true, $encoding='utf-8'){
        $rs = preg_replace(array('/\,\"\_table\"\:\".*\"/U', '/\,\"\_primarykey\"\:\".*\"/U', '/\,\"\_fields\"\:\[\".*\"\]/U'), '', json_encode($result));
        if($removeNullField){
            if($exceptField===null)
                $rs = preg_replace(array('/\,\"[^\"]+\"\:null/U', '/\{\"[^\"]+\"\:null\,/U'), array('','{'), $rs);
            else{
                $funca1 =  create_function('$matches',
                            'if(in_array($matches[1], array(\''. implode("','",$exceptField) .'\'))===false){
                                return "";
                            }
                            return $matches[0];');

                $funca2 =  create_function('$matches',
                            'if(in_array($matches[1], array(\''. implode("','",$exceptField) .'\'))===false){
                                return "{";
                            }
                            return $matches[0];');

                $rs = preg_replace_callback('/\,\"([^\"]+)\"\:null/U', $funca1, $rs);
                $rs = preg_replace_callback('/\{\"([^\"]+)\"\:null\,/U', $funca2, $rs);
            }
        }

        //remove fields in this array
        if($mustRemoveFieldList!==null){
            $funcb1 =  create_function('$matches',
                        'if(in_array($matches[1], array(\''. implode("','",$mustRemoveFieldList) .'\'))){
                            return "";
                        }
                        return $matches[0];');

            $funcb2 =  create_function('$matches',
                        'if(in_array($matches[1], array(\''. implode("','",$mustRemoveFieldList) .'\'))){
                            return "{";
                        }
                        return $matches[0];');

            $rs = preg_replace_callback(array('/\,\"([^\"]+)\"\:\".*\"/U', '/\,\"([^\"]+)\"\:\{.*\}/U', '/\,\"([^\"]+)\"\:\[.*\]/U', '/\,\"([^\"]+)\"\:([false|true|0-9|\.\-|null]+)/'), $funcb1, $rs);

            $rs = preg_replace_callback(array('/\{\"([^\"]+)\"\:\".*\"\,/U','/\{\"([^\"]+)\"\:\{.*\}\,/U'), $funcb2, $rs);

            preg_match('/(.*)(\[\{.*)\"('. implode('|',$mustRemoveFieldList) .')\"\:\[(.*)/', $rs, $m);

            if($m){
                if( $pos = strpos($m[4], '"}],"') ){
                    if($pos2 = strpos($m[4], '"}]},{')){
                        $d = substr($m[4], $pos2+5);
                        if(substr($m[2],-1)==','){
                            $m[2] = substr_replace($m[2], '},', -1);
                        }
                    }
                    else if(strpos($m[4], ']},{')!==false){
                        $d = substr($m[4], strpos($m[4], ']},{')+3);
                        if(substr($m[2],-1)==','){
                            $m[2] = substr_replace($m[2], '},', -1);
                        }
                    }
                    else if(strpos($m[4], '],"')===0){
                        $d = substr($m[4], strpos($m[4], '],"')+2);
                    }
                    else if(strpos($m[4], '}],"')!==false){
                        $d = substr($m[4], strpos($m[4], '],"')+2);
                    }
                    else{
                        $d = substr($m[4], $pos+4);
                    }
                }
                else{
                    $rs = preg_replace('/(\[\{.*)\"('. implode('|',$mustRemoveFieldList) .')\"\:\[.*\]\}(\,)?/U', '$1}', $rs);
                    $rs = preg_replace('/(\".*\"\:\".*\")\,\}(\,)?/U', '$1}$2', $rs);
                }

                if(isset($d)){
                    $rs = $m[1].$m[2].$d;
                }
            }
        }

        if($output===true){
			if($setJSONContentType===true)
				$this->setContentType('json', $encoding);
            echo $rs;
		}
        return $rs;
    }

	public function  __call($name,  $arguments) {
		if ($name == 'renderLayout') {
			throw new Exception('renderLayout is no longer supported by DooController. Please use $this->view()->renderLayout instead');
		}
	}

}
